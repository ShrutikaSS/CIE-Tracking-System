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
                
                $total_activities = dbFetchOne(
                    "SELECT COUNT(*) as cnt FROM activities a 
                     JOIN subject_students ss ON ss.subject_id = a.subject_id 
                     WHERE ss.student_id = ? AND a.status IN ('active', 'completed')", 'i', [$stuId]
                )['cnt'];
                
                $completed = dbFetchOne(
                    "SELECT COUNT(*) as cnt FROM marks m
                     JOIN activities a ON m.activity_id = a.id
                     WHERE m.student_id = ? AND m.is_published = 1 AND a.status IN ('active', 'completed')", 'i', [$stuId]
                )['cnt'];
                
                $pending = max(0, $total_activities - $completed);
                
                $avg_pct = dbFetchOne(
                    "SELECT ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as avg_pct 
                     FROM marks m JOIN activities a ON m.activity_id = a.id 
                     WHERE m.student_id = ? AND m.is_published = 1 AND a.status IN ('active', 'completed')", 'i', [$stuId]
                )['avg_pct'] ?? 0;
                
                // Rank calculation
                $deptId = $user['department_id'] ?: 1;
                $allRankings = dbFetchAll(
                    "SELECT s.id as student_id, ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as avg_pct
                     FROM students s
                     JOIN marks m ON m.student_id = s.id
                     JOIN activities a ON m.activity_id = a.id
                     WHERE m.is_published = 1 AND s.department_id = ?
                     GROUP BY s.id
                     ORDER BY avg_pct DESC", 'i', [$deptId]
                );
                $rank = 1;
                $total_students = max(1, count($allRankings));
                foreach ($allRankings as $index => $row) {
                    if ($row['student_id'] == $stuId) {
                        $rank = $index + 1;
                        break;
                    }
                }
                
                $stats = [
                    'total_activities' => $total_activities,
                    'completed'        => $completed,
                    'pending'          => $pending,
                    'avg_percentage'   => $avg_pct,
                    'rank'             => $rank,
                    'total_students'   => $total_students
                ];
                
                // Fetch recent activities
                $recent_activities = dbFetchAll(
                    "SELECT a.*, s.name as subject_name, s.code as subject_code
                     FROM activities a 
                     JOIN subject_students ss ON a.subject_id = ss.subject_id
                     JOIN subjects s ON a.subject_id = s.id
                     WHERE ss.student_id = ? AND a.status IN ('active', 'completed')
                     ORDER BY a.created_at DESC LIMIT 5", 'i', [$stuId]
                );
                
                // Fetch recent notifications
                $recent_notifications = dbFetchAll(
                    "SELECT * FROM notifications 
                     WHERE user_id = ? 
                     ORDER BY created_at DESC LIMIT 5", 'i', [$user['id']]
                );
                foreach ($recent_notifications as &$n) {
                    $n['time_ago'] = timeAgo($n['created_at']);
                }
                
                // Attendance details
                $attendance_subjects = [
                    ['code' => 'CS501', 'name' => 'Software Engineering', 'attended' => 32, 'missed' => 4],
                    ['code' => 'CS502', 'name' => 'Machine Learning', 'attended' => 28, 'missed' => 6],
                    ['code' => 'CS301', 'name' => 'Data Structures', 'attended' => 30, 'missed' => 2],
                    ['code' => 'CS302', 'name' => 'Database Systems', 'attended' => 29, 'missed' => 3]
                ];
                $total_attended = 0;
                $total_missed = 0;
                foreach ($attendance_subjects as $as) {
                    $total_attended += $as['attended'];
                    $total_missed += $as['missed'];
                }
                $total_classes = $total_attended + $total_missed;
                $attendance_pct = $total_classes > 0 ? round(($total_attended / $total_classes) * 100, 1) : 0;
                $attendance = [
                    'attended' => $total_attended,
                    'missed' => $total_missed,
                    'percentage' => $attendance_pct,
                    'subjects' => $attendance_subjects,
                    'status_message' => 'Good standing. You are 12 classes above the 75% attendance threshold.'
                ];
                
                // Streak details (Duolingo theme)
                $streak = [
                    'count' => 6,
                    'last_active_days' => [
                        ['day' => 'M', 'active' => true],
                        ['day' => 'T', 'active' => true],
                        ['day' => 'W', 'active' => true],
                        ['day' => 'T', 'active' => true],
                        ['day' => 'F', 'active' => true],
                        ['day' => 'S', 'active' => true],
                        ['day' => 'S', 'active' => false]
                    ]
                ];
                
                // Heatmap data (last 90 days for calendar representation)
                $heatmap_data = [];
                $today = new DateTime();
                for ($i = 90; $i >= 0; $i--) {
                    $date = clone $today;
                    $date->modify("-$i days");
                    $dateStr = $date->format('Y-m-d');
                    
                    // seed some random values
                    $val = (crc32($dateStr . $stuId) % 10);
                    $count = $val == 0 ? 3 : ($val == 1 ? 2 : ($val == 2 ? 1 : 0));
                    
                    $heatmap_data[$dateStr] = $count;
                }
                $real_marks = dbFetchAll(
                    "SELECT DATE(created_at) as date, COUNT(*) as cnt 
                     FROM marks WHERE student_id = ? 
                     GROUP BY DATE(created_at)", 'i', [$stuId]
                );
                foreach ($real_marks as $rm) {
                    $heatmap_data[$rm['date']] = min(4, ($heatmap_data[$rm['date']] ?? 0) + $rm['cnt']);
                }
                
                jsonResponse([
                    'success' => true, 
                    'stats' => $stats,
                    'recent_activities' => $recent_activities,
                    'recent_notifications' => $recent_notifications,
                    'attendance' => $attendance,
                    'streak' => $streak,
                    'heatmap' => $heatmap_data
                ]);
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

            $semester = !empty($_GET['semester']) && $_GET['semester'] !== 'all' ? (int)$_GET['semester'] : 0;
            $cieType = !empty($_GET['cie']) && $_GET['cie'] !== 'all' ? $_GET['cie'] : '';
            $dept = !empty($_GET['dept']) && $_GET['dept'] !== 'all' ? $_GET['dept'] : '';

            $where = ["m.student_id = ?", "m.is_published = 1"];
            $params = [$stuId];
            $types = 'i';

            if ($semester > 0) {
                $where[] = "s.semester = ?";
                $params[] = $semester;
                $types .= 'i';
            }
            if (!empty($cieType)) {
                $where[] = "a.type = ?";
                $params[] = strtolower($cieType);
                $types .= 's';
            }
            if (!empty($dept)) {
                $where[] = "d.code = ?";
                $params[] = strtoupper($dept);
                $types .= 's';
            }

            $whereSql = implode(' AND ', $where);

            $data = dbFetchAll(
                "SELECT s.code as label,
                        s.name as subject_name,
                        s.semester,
                        d.code as dept_code,
                        ROUND(SUM(m.marks_obtained), 1) as total_obtained,
                        ROUND(SUM(a.max_marks), 1) as total_max,
                        ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as value 
                 FROM marks m 
                 JOIN activities a ON m.activity_id = a.id 
                 JOIN subjects s ON a.subject_id = s.id 
                 LEFT JOIN departments d ON s.department_id = d.id
                 WHERE $whereSql
                 GROUP BY s.id, s.code, s.name, s.semester, d.code
                 ORDER BY s.code", $types, $params
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
