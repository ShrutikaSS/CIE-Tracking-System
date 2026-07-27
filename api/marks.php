<?php
/**
 * Marks API — Enter, update, publish
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/cie_marks.php';

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
                        m.id as mark_id, m.marks_obtained, m.remarks, m.is_published,
                        sub.id as submission_id, sub.file_path, sub.submission_text, sub.submitted_at
                 FROM students s
                 JOIN users u ON s.user_id = u.id
                 JOIN subject_students ss ON ss.student_id = s.id
                 LEFT JOIN marks m ON m.student_id = s.id AND m.activity_id = ?
                 LEFT JOIN submissions sub ON sub.student_id = s.id AND sub.activity_id = ?
                 WHERE ss.subject_id = ?
                 ORDER BY s.usn",
                'iii', [$activityId, $activityId, $activity['subject_id']]
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
                "SELECT m.id, m.activity_id, m.student_id, m.remarks, m.is_published, m.published_at,
                        COALESCE(m.marks_obtained, sub.marks_awarded) as marks_obtained,
                        a.name as activity_name, a.type as activity_type, a.max_marks,
                        s.name as subject_name, s.code as subject_code
                 FROM marks m 
                 JOIN activities a ON m.activity_id = a.id 
                 JOIN subjects s ON a.subject_id = s.id 
                 LEFT JOIN submissions sub ON sub.student_id = m.student_id AND sub.activity_id = m.activity_id
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
        
        if ($action === 'cie_summary') {
            $subjectId = (int)($_GET['subject_id'] ?? 0);
            if (!$subjectId) jsonResponse(['success' => false, 'message' => 'Subject ID required.'], 400);
            
            // Get all students enrolled in subject
            $students = dbFetchAll(
                "SELECT s.id as student_id, u.name as student_name, s.usn
                 FROM students s
                 JOIN users u ON s.user_id = u.id
                 JOIN subject_students ss ON ss.student_id = s.id
                 WHERE ss.subject_id = ?
                 ORDER BY s.usn",
                'i', [$subjectId]
            );
            
            $summary = [];
            foreach ($students as $stu) {
                $totalMarks = getTotalActivityMarks($stu['student_id'], $subjectId);
                $cieMarks = convertToCIE($totalMarks);
                
                $summary[] = [
                    'student_id' => $stu['student_id'],
                    'student_name' => $stu['student_name'],
                    'usn' => $stu['usn'],
                    'total_out_of_60' => $totalMarks,
                    'cie_out_of_20' => $cieMarks
                ];
            }
            jsonResponse(['success' => true, 'summary' => $summary]);
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
                    // Check if existing mark was already published
                    $wasPublished = dbFetchOne("SELECT is_published, marks_obtained FROM marks WHERE id = ?", 'i', [$existing['id']]);
                    
                    // Update
                    dbExecute(
                        "UPDATE marks SET marks_obtained = ?, remarks = ?, entered_by = ? WHERE id = ?",
                        'dsii', [$marks, $remarks, $user['id'], $existing['id']]
                    );

                    if ($wasPublished && $wasPublished['is_published'] == 1 && $wasPublished['marks_obtained'] != $marks) {
                        $stuUser = dbFetchOne("SELECT user_id FROM students WHERE id = ?", 'i', [$studentId]);
                        $actInfo = dbFetchOne("SELECT a.name, s.name as subject_name FROM activities a JOIN subjects s ON a.subject_id = s.id WHERE a.id = ?", 'i', [$activityId]);
                        if ($stuUser && $actInfo) {
                            createNotification(
                                $stuUser['user_id'],
                                'Marks Updated',
                                "Your marks for \"{$actInfo['name']}\" ({$actInfo['subject_name']}) have been updated to {$marks}.",
                                'info',
                                '/student/activities.php',
                                'marks_updated',
                                'portal'
                            );
                        }
                    }
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
            
            // Fetch activity metadata and max marks
            $activity = dbFetchOne(
                "SELECT a.name, a.max_marks, s.name as subject_name, s.department_id FROM activities a JOIN subjects s ON a.subject_id = s.id WHERE a.id = ?",
                'i', [$activityId]
            );
            
            $maxMarks = (float)($activity['max_marks'] ?? 10);
            $lowThreshold = 0.40 * $maxMarks; // 40% threshold for Low Performance Alert

            $marksRecords = dbFetchAll(
                "SELECT m.*, s.user_id as student_user_id, s.department_id, u.name as student_name, s.usn
                 FROM marks m 
                 JOIN students s ON m.student_id = s.id 
                 JOIN users u ON s.user_id = u.id 
                 WHERE m.activity_id = ?",
                'i', [$activityId]
            );

            // Fetch HOD user ID for department
            $hodUser = dbFetchOne("SELECT hod_id FROM departments WHERE id = ?", 'i', [$activity['department_id']]);
            $hodUserId = $hodUser ? $hodUser['hod_id'] : null;

            // Fetch Coordinators for department
            $coordinators = dbFetchAll("SELECT id FROM users WHERE role = 'coordinator' AND department_id = ?", 'i', [$activity['department_id']]);

            foreach ($marksRecords as $rec) {
                // 1. Send Marks Published Notification (Green)
                createNotification(
                    $rec['student_user_id'],
                    'Marks Published',
                    "Marks for \"{$activity['name']}\" ({$activity['subject_name']}) have been published.",
                    'success',
                    '/student/activities.php',
                    'marks_published',
                    'portal'
                );

                // 2. Check for Low Performance Alert (Red < 40%)
                if ($rec['marks_obtained'] !== null && $rec['marks_obtained'] < $lowThreshold) {
                    $pct = number_format(($rec['marks_obtained'] / $maxMarks) * 100, 1);
                    
                    // Alert Student
                    createNotification(
                        $rec['student_user_id'],
                        'Low Performance Alert',
                        "Alert: Your mark in \"{$activity['name']}\" is {$rec['marks_obtained']}/{$maxMarks} ({$pct}%). Please review your performance.",
                        'danger',
                        '/student/activities.php',
                        'low_performance',
                        'all'
                    );

                    // Alert HOD
                    if ($hodUserId) {
                        createNotification(
                            $hodUserId,
                            'Low Performance Alert',
                            "Low Performance Alert: Student {$rec['student_name']} ({$rec['usn']}) scored {$rec['marks_obtained']}/{$maxMarks} in {$activity['name']}.",
                            'danger',
                            '/hod/student_performance.php',
                            'low_performance',
                            'portal'
                        );
                    }

                    // Alert Coordinators
                    foreach ($coordinators as $coord) {
                        createNotification(
                            $coord['id'],
                            'Low Performance Alert',
                            "Low Performance Alert: Student {$rec['student_name']} ({$rec['usn']}) scored {$rec['marks_obtained']}/{$maxMarks} in {$activity['name']}.",
                            'danger',
                            '/coordinator/class_performance.php',
                            'low_performance',
                            'portal'
                        );
                    }
                }
            }
            
            jsonResponse(['success' => true, 'message' => 'Marks published and notifications dispatched.']);
        }
        
        if ($action === 'auto_fill') {
            $data = getJsonBody();
            $activityId = (int)($data['activity_id'] ?? 0);
            if (!$activityId) jsonResponse(['success' => false, 'message' => 'Activity ID required.'], 400);
            
            $activity = dbFetchOne("SELECT max_marks FROM activities WHERE id = ?", 'i', [$activityId]);
            if (!$activity) jsonResponse(['success' => false, 'message' => 'Activity not found.'], 404);
            
            // Fetch all submissions for this activity
            $submissions = dbFetchAll("SELECT student_id, marks_awarded FROM submissions WHERE activity_id = ?", 'i', [$activityId]);
            
            $filled = 0;
            foreach ($submissions as $sub) {
                if ($sub['marks_awarded'] !== null) {
                    $existing = dbFetchOne("SELECT id FROM marks WHERE activity_id = ? AND student_id = ?", 'ii', [$activityId, $sub['student_id']]);
                    if ($existing) {
                        dbExecute(
                            "UPDATE marks SET marks_obtained = ?, entered_by = ? WHERE id = ?",
                            'dii', [$sub['marks_awarded'], $user['id'], $existing['id']]
                        );
                    } else {
                        dbInsert(
                            "INSERT INTO marks (activity_id, student_id, marks_obtained, entered_by) VALUES (?, ?, ?, ?)",
                            'iidi', [$activityId, $sub['student_id'], $sub['marks_awarded'], $user['id']]
                        );
                    }
                    $filled++;
                }
            }
            
            jsonResponse(['success' => true, 'message' => "Auto-filled marks for $filled student(s).", 'filled' => $filled]);
        }
        
        jsonResponse(['success' => false, 'message' => 'Invalid action.'], 400);
        break;

    default:
        jsonResponse(['success' => false], 405);
}
