<?php
/**
 * Subjects API — CRUD
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
            $sub = dbFetchOne(
                "SELECT s.*, d.name as dept_name, d.code as dept_code, u.name as faculty_name
                 FROM subjects s 
                 JOIN departments d ON s.department_id = d.id 
                 LEFT JOIN faculty f ON s.faculty_id = f.id 
                 LEFT JOIN users u ON f.user_id = u.id 
                 WHERE s.id = ?", 'i', [(int)$id]
            );
            jsonResponse(['success' => true, 'subject' => $sub]);
        }
        
        $dept = $_GET['department'] ?? '';
        $sem  = $_GET['semester'] ?? '';
        $search = $_GET['search'] ?? '';
        $forFaculty = $_GET['for_faculty'] ?? '';
        
        $sql = "SELECT s.*, d.name as dept_name, d.code as dept_code, u.name as faculty_name,
                (SELECT COUNT(*) FROM subject_students ss WHERE ss.subject_id = s.id) as student_count,
                (SELECT COUNT(*) FROM activities a WHERE a.subject_id = s.id) as activity_count
                FROM subjects s 
                JOIN departments d ON s.department_id = d.id 
                LEFT JOIN faculty f ON s.faculty_id = f.id 
                LEFT JOIN users u ON f.user_id = u.id 
                WHERE 1=1";
        $types = '';
        $params = [];
        
        // Faculty only sees their subjects
        if ($forFaculty || in_array($user['role'], ['faculty', 'coordinator'])) {
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
            $sql .= " AND s.id IN (SELECT subject_id FROM subject_students WHERE student_id = ?)";
            $types .= 'i';
            $params[] = $stuId;
        }
        
        if ($dept) {
            $sql .= " AND s.department_id = ?";
            $types .= 'i';
            $params[] = (int)$dept;
        }
        if ($sem) {
            $sql .= " AND s.semester = ?";
            $types .= 'i';
            $params[] = (int)$sem;
        }
        if ($search) {
            $sql .= " AND (s.name LIKE ? OR s.code LIKE ?)";
            $types .= 'ss';
            $like = "%$search%";
            array_push($params, $like, $like);
        }
        
        $sql .= " ORDER BY s.code";
        $subjects = dbFetchAll($sql, $types, $params);
        jsonResponse(['success' => true, 'subjects' => $subjects]);
        break;

    case 'POST':
        requireRole(['admin', 'hod']);
        $data = getJsonBody();
        $name   = trim($data['name'] ?? '');
        $code   = trim($data['code'] ?? '');
        $sem    = (int)($data['semester'] ?? 1);
        $credits= (int)($data['credits'] ?? 4);
        $deptId = (int)($data['department_id'] ?? 0);
        $facId  = !empty($data['faculty_id']) ? (int)$data['faculty_id'] : null;

        if (empty($name) || empty($code) || !$deptId) {
            jsonResponse(['success' => false, 'message' => 'Name, code, and department are required.'], 400);
        }

        // Check HOD department access
        if ($user['role'] === 'hod' && $deptId !== (int)$user['department_id']) {
            jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
        }

        if (dbFetchOne("SELECT id FROM subjects WHERE code = ?", 's', [$code])) {
            jsonResponse(['success' => false, 'message' => 'Subject code already exists.'], 400);
        }

        $id = dbInsert(
            "INSERT INTO subjects (name, code, semester, credits, department_id, faculty_id) VALUES (?, ?, ?, ?, ?, ?)",
            'ssiiii', [$name, strtoupper($code), $sem, $credits, $deptId, $facId]
        );

        jsonResponse(['success' => true, 'message' => 'Subject created.', 'id' => $id]);
        break;

    case 'PUT':
        requireRole(['admin', 'hod']);
        $data = getJsonBody();
        $id     = (int)($data['id'] ?? 0);
        $name   = trim($data['name'] ?? '');
        $code   = trim($data['code'] ?? '');
        $sem    = (int)($data['semester'] ?? 1);
        $credits= (int)($data['credits'] ?? 4);
        $deptId = (int)($data['department_id'] ?? 0);
        $facId  = !empty($data['faculty_id']) ? (int)$data['faculty_id'] : null;

        if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid ID.'], 400);

        $sub = dbFetchOne("SELECT department_id FROM subjects WHERE id = ?", 'i', [$id]);
        if (!$sub) jsonResponse(['success' => false, 'message' => 'Subject not found.'], 404);

        // Check HOD department access
        if ($user['role'] === 'hod') {
            if ((int)$sub['department_id'] !== (int)$user['department_id'] || $deptId !== (int)$user['department_id']) {
                jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
            }
        }

        $existing = dbFetchOne("SELECT id FROM subjects WHERE code = ? AND id != ?", 'si', [$code, $id]);
        if ($existing) {
            jsonResponse(['success' => false, 'message' => 'Subject code already exists.'], 400);
        }

        dbExecute(
            "UPDATE subjects SET name = ?, code = ?, semester = ?, credits = ?, department_id = ?, faculty_id = ? WHERE id = ?",
            'ssiiiii', [$name, strtoupper($code), $sem, $credits, $deptId, $facId, $id]
        );

        jsonResponse(['success' => true, 'message' => 'Subject updated.']);
        break;

    case 'DELETE':
        requireRole(['admin', 'hod']);
        $data = getJsonBody();
        $id = (int)($data['id'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid ID.'], 400);

        $sub = dbFetchOne("SELECT department_id FROM subjects WHERE id = ?", 'i', [$id]);
        if (!$sub) jsonResponse(['success' => false, 'message' => 'Subject not found.'], 404);

        // Check HOD department access
        if ($user['role'] === 'hod' && (int)$sub['department_id'] !== (int)$user['department_id']) {
            jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
        }

        dbExecute("DELETE FROM subjects WHERE id = ?", 'i', [$id]);
        jsonResponse(['success' => true, 'message' => 'Subject deleted.']);
        break;

    default:
        jsonResponse(['success' => false], 405);
}
