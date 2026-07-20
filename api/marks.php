<?php
/**
 * Marks API — Enter, update, publish
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
header('Content-Type: application/json');

$user = currentUser();
$method = requestMethod();
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if ($action === 'students_for_activity') {
            // Get students enrolled in the activity's subject with their marks
            $activityId = (int)($_GET['activity_id'] ?? 0);
            if (!$activityId) jsonResponse(['success' => false, 'message' => 'Activity ID required.'], 400);
            
            $activity = dbFetchOne("SELECT * FROM activities WHERE id = ?", 'i', [$activityId]);
            if (!$activity) jsonResponse(['success' => false, 'message' => 'Activity not found.'], 404);
            
            $students = dbFetchAll(
                "SELECT s.id as student_id, u.name as student_name, s.usn, s.section,
                        m.id as mark_id, m.marks_obtained, m.remarks, m.is_published
                 FROM students s
                 JOIN users u ON s.user_id = u.id
                 JOIN subject_students ss ON ss.student_id = s.id
                 LEFT JOIN marks m ON m.student_id = s.id AND m.activity_id = ?
                 WHERE ss.subject_id = ?
                 ORDER BY s.usn",
                'ii', [$activityId, $activity['subject_id']]
            );
            
            jsonResponse(['success' => true, 'activity' => $activity, 'students' => $students]);
        }
        
        if ($action === 'by_student') {
            // Get marks for a student
            $studentId = (int)($_GET['student_id'] ?? 0);
            if ($user['role'] === 'student') {
                $stu = dbFetchOne("SELECT id FROM students WHERE user_id = ?", 'i', [$user['id']]);
                $studentId = $stu ? $stu['id'] : 0;
            }
            
            $marks = dbFetchAll(
                "SELECT m.*, a.name as activity_name, a.type as activity_type, a.max_marks,
                        s.name as subject_name, s.code as subject_code
                 FROM marks m 
                 JOIN activities a ON m.activity_id = a.id 
                 JOIN subjects s ON a.subject_id = s.id 
                 WHERE m.student_id = ? AND m.is_published = 1 
                 ORDER BY s.code, a.activity_date",
                'i', [$studentId]
            );
            
            jsonResponse(['success' => true, 'marks' => $marks]);
        }
        
        if ($action === 'by_activity') {
            $activityId = (int)($_GET['activity_id'] ?? 0);
            $marks = dbFetchAll(
                "SELECT m.*, u.name as student_name, s.usn
                 FROM marks m 
                 JOIN students s ON m.student_id = s.id 
                 JOIN users u ON s.user_id = u.id 
                 WHERE m.activity_id = ? ORDER BY s.usn",
                'i', [$activityId]
            );
            jsonResponse(['success' => true, 'marks' => $marks]);
        }
        
        jsonResponse(['success' => false, 'message' => 'Invalid action.'], 400);
        break;

    case 'POST':
        requireRole(['admin', 'hod', 'faculty', 'coordinator']);
        
        if ($action === 'save') {
            // Save marks for multiple students
            $data = getJsonBody();
            $activityId = (int)($data['activity_id'] ?? 0);
            $marksData  = $data['marks'] ?? [];
            
            if (!$activityId || empty($marksData)) {
                jsonResponse(['success' => false, 'message' => 'Activity ID and marks data required.'], 400);
            }
            
            $activity = dbFetchOne("SELECT max_marks FROM activities WHERE id = ?", 'i', [$activityId]);
            if (!$activity) jsonResponse(['success' => false, 'message' => 'Activity not found.'], 404);
            
            $maxMarks = (float)$activity['max_marks'];
            $saved = 0;
            $errors = [];
            
            foreach ($marksData as $entry) {
                $studentId = (int)($entry['student_id'] ?? 0);
                $marks     = $entry['marks'] !== '' && $entry['marks'] !== null ? (float)$entry['marks'] : null;
                $remarks   = trim($entry['remarks'] ?? '');
                
                if (!$studentId) continue;
                
                // Validate max marks
                if ($marks !== null && $marks > $maxMarks) {
                    $errors[] = "Student $studentId: marks exceed maximum ($maxMarks).";
                    continue;
                }
                
                if ($marks !== null && $marks < 0) {
                    $errors[] = "Student $studentId: marks cannot be negative.";
                    continue;
                }
                
                // Check if already exists
                $existing = dbFetchOne(
                    "SELECT id FROM marks WHERE activity_id = ? AND student_id = ?",
                    'ii', [$activityId, $studentId]
                );
                
                if ($existing) {
                    // Update
                    dbExecute(
                        "UPDATE marks SET marks_obtained = ?, remarks = ?, entered_by = ? WHERE id = ?",
                        'dsii', [$marks, $remarks, $user['id'], $existing['id']]
                    );
                } else {
                    // Insert
                    dbInsert(
                        "INSERT INTO marks (activity_id, student_id, marks_obtained, remarks, entered_by) VALUES (?, ?, ?, ?, ?)",
                        'iidsi', [$activityId, $studentId, $marks, $remarks, $user['id']]
                    );
                }
                $saved++;
            }
            
            $msg = "Saved marks for $saved student(s).";
            if (!empty($errors)) $msg .= ' Errors: ' . implode('; ', $errors);
            
            jsonResponse(['success' => true, 'message' => $msg, 'saved' => $saved, 'errors' => $errors]);
        }
        
        if ($action === 'publish') {
            $data = getJsonBody();
            $activityId = (int)($data['activity_id'] ?? 0);
            if (!$activityId) jsonResponse(['success' => false, 'message' => 'Activity ID required.'], 400);
            
            // Publish all marks for this activity
            dbExecute(
                "UPDATE marks SET is_published = 1, published_at = NOW() WHERE activity_id = ? AND is_published = 0",
                'i', [$activityId]
            );
            
            // Update activity status
            dbExecute("UPDATE activities SET status = 'completed' WHERE id = ?", 'i', [$activityId]);
            
            // Notify students
            $activity = dbFetchOne(
                "SELECT a.name, s.name as subject_name FROM activities a JOIN subjects s ON a.subject_id = s.id WHERE a.id = ?",
                'i', [$activityId]
            );
            
            $students = dbFetchAll(
                "SELECT s.user_id FROM marks m JOIN students s ON m.student_id = s.id WHERE m.activity_id = ?",
                'i', [$activityId]
            );
            
            foreach ($students as $stu) {
                createNotification(
                    $stu['user_id'],
                    'Marks Published',
                    "Marks for \"{$activity['name']}\" ({$activity['subject_name']}) have been published.",
                    'success'
                );
            }
            
            jsonResponse(['success' => true, 'message' => 'Marks published and students notified.']);
        }
        
        jsonResponse(['success' => false, 'message' => 'Invalid action.'], 400);
        break;

    default:
        jsonResponse(['success' => false], 405);
}
