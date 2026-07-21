<?php
/**
 * Dashboard API Endpoint
 * Returns stats and chart data based on user role
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
header('Content-Type: application/json');

$user = currentUser();
$action = $_GET['action'] ?? 'stats';

switch ($action) {
    case 'stats':
        switch ($user['role']) {
            case 'admin':
                $stats = [
                    'students'   => dbFetchOne("SELECT COUNT(*) as cnt FROM students")['cnt'],
                    'faculty'    => dbFetchOne("SELECT COUNT(*) as cnt FROM faculty")['cnt'],
                    'subjects'   => dbFetchOne("SELECT COUNT(*) as cnt FROM subjects WHERE is_active = 1")['cnt'],
                    'activities' => dbFetchOne("SELECT COUNT(*) as cnt FROM activities")['cnt'],
                    'departments'=> dbFetchOne("SELECT COUNT(*) as cnt FROM departments")['cnt']
                ];
                jsonResponse(['success' => true, 'stats' => $stats]);
                break;

            case 'hod':
                $deptId = $user['department_id'];
                $stats = [
                    'students'   => dbFetchOne("SELECT COUNT(*) as cnt FROM students WHERE department_id = ?", 'i', [$deptId])['cnt'],
                    'faculty'    => dbFetchOne("SELECT COUNT(*) as cnt FROM faculty WHERE department_id = ?", 'i', [$deptId])['cnt'],
                    'subjects'   => dbFetchOne("SELECT COUNT(*) as cnt FROM subjects WHERE department_id = ? AND is_active = 1", 'i', [$deptId])['cnt'],
                    'activities' => dbFetchOne("SELECT COUNT(*) as cnt FROM activities a JOIN subjects s ON a.subject_id = s.id WHERE s.department_id = ?", 'i', [$deptId])['cnt']
                ];
                jsonResponse(['success' => true, 'stats' => $stats]);
                break;

            case 'faculty':
            case 'coordinator':
                // Get faculty record
                $fac = dbFetchOne("SELECT id FROM faculty WHERE user_id = ?", 'i', [$user['id']]);
                $facId = $fac ? $fac['id'] : 0;
                
                $stats = [
                    'subjects'         => dbFetchOne("SELECT COUNT(*) as cnt FROM subjects WHERE faculty_id = ? AND is_active = 1", 'i', [$facId])['cnt'],
                    'active_activities'=> dbFetchOne("SELECT COUNT(*) as cnt FROM activities a JOIN subjects s ON a.subject_id = s.id WHERE s.faculty_id = ? AND a.status = 'active'", 'i', [$facId])['cnt'],
                    'total_activities' => dbFetchOne("SELECT COUNT(*) as cnt FROM activities a JOIN subjects s ON a.subject_id = s.id WHERE s.faculty_id = ?", 'i', [$facId])['cnt'],
                    'pending_marks'    => dbFetchOne(
                        "SELECT COUNT(DISTINCT a.id) as cnt FROM activities a 
                         JOIN subjects s ON a.subject_id = s.id 
                         LEFT JOIN marks m ON m.activity_id = a.id 
                         WHERE s.faculty_id = ? AND a.status IN ('active','completed') AND m.id IS NULL", 'i', [$facId]
                    )['cnt']
                ];
                jsonResponse(['success' => true, 'stats' => $stats]);
                break;

            case 'student':
                $stu = dbFetchOne("SELECT id FROM students WHERE user_id = ?", 'i', [$user['id']]);
                $stuId = $stu ? $stu['id'] : 0;
                
                $stats = [
                    'total_activities' => dbFetchOne(
                        "SELECT COUNT(*) as cnt FROM activities a 
                         JOIN subject_students ss ON ss.subject_id = a.subject_id 
                         WHERE ss.student_id = ? AND a.status != 'cancelled'", 'i', [$stuId]
                    )['cnt'],
                    'completed'        => dbFetchOne(
                        "SELECT COUNT(*) as cnt FROM marks WHERE student_id = ? AND is_published = 1", 'i', [$stuId]
                    )['cnt'],
                    'subjects'         => dbFetchOne(
                        "SELECT COUNT(DISTINCT subject_id) as cnt FROM subject_students WHERE student_id = ?", 'i', [$stuId]
                    )['cnt'],
                    'avg_percentage'   => dbFetchOne(
                        "SELECT ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as avg_pct 
                         FROM marks m JOIN activities a ON m.activity_id = a.id 
                         WHERE m.student_id = ? AND m.is_published = 1", 'i', [$stuId]
                    )['avg_pct'] ?? 0
                ];
                jsonResponse(['success' => true, 'stats' => $stats]);
                break;
        }
        break;

    case 'chart':
        $chartType = $_GET['type'] ?? '';
        
        if ($chartType === 'activities_by_dept') {
            $data = dbFetchAll(
                "SELECT d.code as label, COUNT(a.id) as value 
                 FROM departments d 
                 LEFT JOIN subjects s ON s.department_id = d.id 
                 LEFT JOIN activities a ON a.subject_id = s.id 
                 GROUP BY d.id, d.code ORDER BY d.code"
            );
            jsonResponse(['success' => true, 'chart' => $data]);
        }
        
        if ($chartType === 'marks_trend') {
            $userId = $user['id'];
            if ($user['role'] === 'student') {
                $stu = dbFetchOne("SELECT id FROM students WHERE user_id = ?", 'i', [$userId]);
                $stuId = $stu ? $stu['id'] : 0;
                $data = dbFetchAll(
                    "SELECT a.name as label, ROUND(m.marks_obtained / a.max_marks * 100, 1) as value 
                     FROM marks m JOIN activities a ON m.activity_id = a.id 
                     WHERE m.student_id = ? AND m.is_published = 1 
                     ORDER BY m.created_at LIMIT 10", 'i', [$stuId]
                );
            } else {
                $data = dbFetchAll(
                    "SELECT DATE_FORMAT(a.activity_date, '%b %d') as label, 
                            ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as value 
                     FROM marks m JOIN activities a ON m.activity_id = a.id 
                     WHERE m.is_published = 1 
                     GROUP BY a.activity_date 
                     ORDER BY a.activity_date DESC LIMIT 10"
                );
                $data = array_reverse($data);
            }
            jsonResponse(['success' => true, 'chart' => $data]);
        }

        if ($chartType === 'subject_performance') {
            $userId = $user['id'];
            $stu = dbFetchOne("SELECT id FROM students WHERE user_id = ?", 'i', [$userId]);
            $stuId = $stu ? $stu['id'] : 0;
            $data = dbFetchAll(
                "SELECT s.code as label, 
                        ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as value 
                 FROM marks m 
                 JOIN activities a ON m.activity_id = a.id 
                 JOIN subjects s ON a.subject_id = s.id 
                 WHERE m.student_id = ? AND m.is_published = 1 
                 GROUP BY s.id, s.code", 'i', [$stuId]
            );
            jsonResponse(['success' => true, 'chart' => $data]);
        }

        if ($chartType === 'activity_type_dist') {
            $data = dbFetchAll(
                "SELECT type as label, COUNT(*) as value FROM activities GROUP BY type"
            );
            jsonResponse(['success' => true, 'chart' => $data]);
        }
        
        jsonResponse(['success' => false, 'message' => 'Invalid chart type.'], 400);
        break;
    
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action.'], 400);
}
