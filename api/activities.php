<?php
/**
 * Activities API — CRUD
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
header('Content-Type: application/json');

$user = currentUser();
$method = requestMethod();

switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $act = dbFetchOne(
                "SELECT a.*, s.name as subject_name, s.code as subject_code, u.name as created_by_name
                 FROM activities a 
                 JOIN subjects s ON a.subject_id = s.id 
                 JOIN users u ON a.created_by = u.id 
                 WHERE a.id = ?", 'i', [(int)$id]
            );
            jsonResponse(['success' => true, 'activity' => $act]);
        }
        
        $subjectId = $_GET['subject_id'] ?? '';
        $type      = $_GET['type'] ?? '';
        $status    = $_GET['status'] ?? '';
        $search    = $_GET['search'] ?? '';
        
        $sql = "SELECT a.*, s.name as subject_name, s.code as subject_code, u.name as created_by_name,
                (SELECT COUNT(*) FROM marks m WHERE m.activity_id = a.id) as marks_entered,
                (SELECT COUNT(*) FROM subject_students ss WHERE ss.subject_id = a.subject_id) as total_students
                FROM activities a 
                JOIN subjects s ON a.subject_id = s.id 
                JOIN users u ON a.created_by = u.id 
                WHERE 1=1";
        $types = '';
        $params = [];
        
        // Faculty/Coordinator see only their subjects' activities
        if (in_array($user['role'], ['faculty', 'coordinator'])) {
            $fac = dbFetchOne("SELECT id FROM faculty WHERE user_id = ?", 'i', [$user['id']]);
            if ($fac) {
                $sql .= " AND s.faculty_id = ?";
                $types .= 'i';
                $params[] = $fac['id'];
            }
        }
        
        if ($user['role'] === 'hod') {
            $sql .= " AND s.department_id = ?";
            $types .= 'i';
            $params[] = $user['department_id'];
        }

        if ($user['role'] === 'student') {
            $stu = dbFetchOne("SELECT id FROM students WHERE user_id = ?", 'i', [$user['id']]);
            $stuId = $stu ? $stu['id'] : 0;
            $sql .= " AND s.id IN (SELECT subject_id FROM subject_students WHERE student_id = ?) AND a.status IN ('active', 'completed')";
            $types .= 'i';
            $params[] = $stuId;
        }
        
        if ($subjectId) {
            $sql .= " AND a.subject_id = ?";
            $types .= 'i';
            $params[] = (int)$subjectId;
        }
        if ($type) {
            $sql .= " AND a.type = ?";
            $types .= 's';
            $params[] = $type;
        }
        if ($status) {
            $sql .= " AND a.status = ?";
            $types .= 's';
            $params[] = $status;
        }
        if ($search) {
            $sql .= " AND (a.name LIKE ? OR s.name LIKE ?)";
            $types .= 'ss';
            $like = "%$search%";
            array_push($params, $like, $like);
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        $activities = dbFetchAll($sql, $types, $params);
        jsonResponse(['success' => true, 'activities' => $activities]);
        break;

    case 'POST':
        requireRole(['admin', 'hod', 'faculty', 'coordinator']);
        $data = getJsonBody();
        
        $subjectId  = (int)($data['subject_id'] ?? 0);
        $name       = trim($data['name'] ?? '');
        $type       = $data['type'] ?? 'assignment';
        $maxMarks   = (float)($data['max_marks'] ?? 10);
        $date       = $data['activity_date'] ?? null;
        $deadline   = $data['deadline'] ?? null;
        $description= trim($data['description'] ?? '');
        $status     = $data['status'] ?? 'active';

        if (!$subjectId || empty($name) || $maxMarks <= 0) {
            jsonResponse(['success' => false, 'message' => 'Subject, name, and max marks are required.'], 400);
        }

        if (!in_array($type, ['assignment','quiz','test','seminar','viva','practical','project_review','presentation'])) {
            jsonResponse(['success' => false, 'message' => 'Invalid activity type.'], 400);
        }

        $id = dbInsert(
            "INSERT INTO activities (subject_id, name, type, max_marks, activity_date, deadline, description, status, created_by) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'issdssssi',
            [$subjectId, $name, $type, $maxMarks, $date ?: null, $deadline ?: null, $description, $status, $user['id']]
        );

        // Notify enrolled students if active
        if ($status === 'active') {
            $students = dbFetchAll(
                "SELECT s.user_id FROM students s 
                 JOIN subject_students ss ON ss.student_id = s.id 
                 WHERE ss.subject_id = ?", 'i', [$subjectId]
            );
            $subName = dbFetchOne("SELECT name FROM subjects WHERE id = ?", 'i', [$subjectId])['name'] ?? '';
            foreach ($students as $stu) {
                createNotification($stu['user_id'], 'New Activity', "New $type \"$name\" created for $subName.", 'info');
            }
        }

        jsonResponse(['success' => true, 'message' => 'Activity created.', 'id' => $id]);
        break;

    case 'PUT':
        requireRole(['admin', 'hod', 'faculty', 'coordinator']);
        $data = getJsonBody();
        $id         = (int)($data['id'] ?? 0);
        $name       = trim($data['name'] ?? '');
        $type       = $data['type'] ?? 'assignment';
        $maxMarks   = (float)($data['max_marks'] ?? 10);
        $date       = $data['activity_date'] ?? null;
        $deadline   = $data['deadline'] ?? null;
        $description= trim($data['description'] ?? '');
        $status     = $data['status'] ?? 'active';

        if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid ID.'], 400);

        dbExecute(
            "UPDATE activities SET name = ?, type = ?, max_marks = ?, activity_date = ?, deadline = ?, description = ?, status = ? WHERE id = ?",
            'ssdsssi' . 'i',
            [$name, $type, $maxMarks, $date, $deadline, $description, $status, $id]
        );

        jsonResponse(['success' => true, 'message' => 'Activity updated.']);
        break;

    case 'DELETE':
        requireRole(['admin', 'hod', 'faculty', 'coordinator']);
        $data = getJsonBody();
        $id = (int)($data['id'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid ID.'], 400);

        dbExecute("DELETE FROM activities WHERE id = ?", 'i', [$id]);
        jsonResponse(['success' => true, 'message' => 'Activity deleted.']);
        break;

    default:
        jsonResponse(['success' => false], 405);
}
