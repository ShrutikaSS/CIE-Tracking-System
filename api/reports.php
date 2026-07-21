<?php
/**
 * Reports API — Student-wise and Subject-wise data
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
header('Content-Type: application/json');

$user = currentUser();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'student':
        $studentId = (int)($_GET['student_id'] ?? 0);
        if (!$studentId) jsonResponse(['success' => false, 'message' => 'Student ID required.'], 400);
        
        // Student info
        $student = dbFetchOne(
            "SELECT s.*, u.name, u.email, d.name as dept_name, d.code as dept_code
             FROM students s JOIN users u ON s.user_id = u.id JOIN departments d ON s.department_id = d.id
             WHERE s.id = ?", 'i', [$studentId]
        );
        
        if (!$student) jsonResponse(['success' => false, 'message' => 'Student not found.'], 404);
        
        // All marks grouped by subject
        $marks = dbFetchAll(
            "SELECT m.marks_obtained, a.name as activity_name, a.type, a.max_marks,
                    s.name as subject_name, s.code as subject_code, s.id as subject_id
             FROM marks m 
             JOIN activities a ON m.activity_id = a.id 
             JOIN subjects s ON a.subject_id = s.id 
             WHERE m.student_id = ? AND m.is_published = 1 
             ORDER BY s.code, a.activity_date",
            'i', [$studentId]
        );
        
        // Group by subject
        $subjects = [];
        foreach ($marks as $m) {
            $key = $m['subject_code'];
            if (!isset($subjects[$key])) {
                $subjects[$key] = [
                    'subject_name' => $m['subject_name'],
                    'subject_code' => $m['subject_code'],
                    'activities'   => [],
                    'total_obtained' => 0,
                    'total_max' => 0
                ];
            }
            $subjects[$key]['activities'][] = $m;
            $subjects[$key]['total_obtained'] += (float)$m['marks_obtained'];
            $subjects[$key]['total_max'] += (float)$m['max_marks'];
        }
        
        // Calculate percentages
        foreach ($subjects as &$sub) {
            $sub['percentage'] = $sub['total_max'] > 0 
                ? round($sub['total_obtained'] / $sub['total_max'] * 100, 1) 
                : 0;
        }
        
        jsonResponse(['success' => true, 'student' => $student, 'subjects' => array_values($subjects)]);
        break;

    case 'subject':
        $subjectId = (int)($_GET['subject_id'] ?? 0);
        if (!$subjectId) jsonResponse(['success' => false, 'message' => 'Subject ID required.'], 400);
        
        $subject = dbFetchOne(
            "SELECT s.*, d.name as dept_name, u.name as faculty_name
             FROM subjects s JOIN departments d ON s.department_id = d.id 
             LEFT JOIN faculty f ON s.faculty_id = f.id 
             LEFT JOIN users u ON f.user_id = u.id 
             WHERE s.id = ?", 'i', [$subjectId]
        );
        
        if (!$subject) jsonResponse(['success' => false, 'message' => 'Subject not found.'], 404);
        
        // All students with their marks
        $students = dbFetchAll(
            "SELECT s.id as student_id, u.name, s.usn, s.section,
                    ROUND(SUM(m.marks_obtained), 2) as total_obtained,
                    ROUND(SUM(a.max_marks), 2) as total_max,
                    COUNT(m.id) as activities_completed
             FROM students s
             JOIN users u ON s.user_id = u.id
             JOIN subject_students ss ON ss.student_id = s.id
             LEFT JOIN marks m ON m.student_id = s.id AND m.is_published = 1
             LEFT JOIN activities a ON m.activity_id = a.id AND a.subject_id = ?
             WHERE ss.subject_id = ?
             GROUP BY s.id, u.name, s.usn, s.section
             ORDER BY s.usn",
            'ii', [$subjectId, $subjectId]
        );
        
        foreach ($students as &$stu) {
            $stu['percentage'] = ($stu['total_max'] > 0) 
                ? round($stu['total_obtained'] / $stu['total_max'] * 100, 1) 
                : 0;
        }
        
        // Aggregate stats
        $percentages = array_column($students, 'percentage');
        $stats = [
            'total_students' => count($students),
            'average' => !empty($percentages) ? round(array_sum($percentages) / count($percentages), 1) : 0,
            'highest' => !empty($percentages) ? max($percentages) : 0,
            'lowest'  => !empty($percentages) ? min($percentages) : 0,
            'pass_rate' => !empty($percentages) 
                ? round(count(array_filter($percentages, fn($p) => $p >= 40)) / count($percentages) * 100, 1) 
                : 0
        ];
        
        // Activities
        $activities = dbFetchAll(
            "SELECT * FROM activities WHERE subject_id = ? ORDER BY activity_date",
            'i', [$subjectId]
        );
        
        jsonResponse([
            'success' => true, 
            'subject' => $subject, 
            'students' => $students,
            'stats' => $stats,
            'activities' => $activities
        ]);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action.'], 400);
}
