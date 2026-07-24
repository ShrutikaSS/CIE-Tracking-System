<?php
/**
 * Students API — CRUD
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
            $student = dbFetchOne(
                "SELECT s.*, u.name, u.email, u.is_active, d.name as dept_name, d.code as dept_code
                 FROM students s 
                 JOIN users u ON s.user_id = u.id 
                 JOIN departments d ON s.department_id = d.id 
                 WHERE s.id = ?", 'i', [(int)$id]
            );
            if ($student && in_array($user['role'], ['hod', 'faculty', 'coordinator'])) {
                if ((int)$student['department_id'] !== (int)$user['department_id']) {
                    jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
                }
            }
            if ($student && $user['role'] === 'student') {
                if ((int)$student['user_id'] !== (int)$user['id']) {
                    jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
                }
            }
            jsonResponse(['success' => true, 'student' => $student]);
        }
        
        // Filters
        $dept = $_GET['department'] ?? '';
        $sem  = $_GET['semester'] ?? '';
        $sec  = $_GET['section'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $sql = "SELECT s.*, u.name, u.email, u.is_active, d.name as dept_name, d.code as dept_code
                FROM students s 
                JOIN users u ON s.user_id = u.id 
                JOIN departments d ON s.department_id = d.id 
                WHERE 1=1";
        $types = '';
        $params = [];
        
        // HOD, faculty, and coordinator see only their department
        if (in_array($user['role'], ['hod', 'faculty', 'coordinator'])) {
            $sql .= " AND s.department_id = ?";
            $types .= 'i';
            $params[] = $user['department_id'];
        }
        if ($user['role'] === 'student') {
            $sql .= " AND s.user_id = ?";
            $types .= 'i';
            $params[] = $user['id'];
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
        if ($sec) {
            $sql .= " AND s.section = ?";
            $types .= 's';
            $params[] = $sec;
        }
        if ($search) {
            $sql .= " AND (u.name LIKE ? OR s.usn LIKE ? OR u.email LIKE ?)";
            $types .= 'sss';
            $like = "%$search%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        
        $sql .= " ORDER BY s.usn";
        $students = dbFetchAll($sql, $types, $params);
        jsonResponse(['success' => true, 'students' => $students]);
        break;

    case 'POST':
        requireRole(['admin', 'hod']);
        $data = getJsonBody();
        $name   = trim($data['name'] ?? '');
        $email  = trim($data['email'] ?? '');
        $usn    = trim($data['usn'] ?? '');
        $prn    = trim($data['prn_number'] ?? '');
        $roll   = trim($data['roll_number'] ?? '');
        $sem    = (int)($data['semester'] ?? 1);
        $sec    = trim($data['section'] ?? 'A');
        $deptId = (int)($data['department_id'] ?? 0);
        $phone  = trim($data['phone'] ?? '');

        if (empty($name) || empty($email) || empty($usn) || !$deptId) {
            jsonResponse(['success' => false, 'message' => 'All required fields must be filled.'], 400);
        }

        // Check HOD department access
        if ($user['role'] === 'hod' && $deptId !== (int)$user['department_id']) {
            jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
        }

        // Check unique email
        if (dbFetchOne("SELECT id FROM users WHERE email = ?", 's', [$email])) {
            jsonResponse(['success' => false, 'message' => 'Email already exists.'], 400);
        }
        // Check unique USN
        if (dbFetchOne("SELECT id FROM students WHERE usn = ?", 's', [$usn])) {
            jsonResponse(['success' => false, 'message' => 'USN already exists.'], 400);
        }

        // Create user
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $userId = dbInsert(
            "INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, 'student', ?)",
            'sssi', [$name, $email, $password, $deptId]
        );

        if (!$userId) {
            jsonResponse(['success' => false, 'message' => 'Failed to create user.'], 500);
        }

        // Create student record
        $studentId = dbInsert(
            "INSERT INTO students (user_id, usn, prn_number, roll_number, semester, section, department_id, phone, admission_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, YEAR(CURDATE()))",
            'isssisss', [$userId, $usn, $prn, $roll, $sem, $sec, $deptId, $phone]
        );

        jsonResponse(['success' => true, 'message' => 'Student added successfully.', 'id' => $studentId]);
        break;

    case 'PUT':
        requireRole(['admin', 'hod']);
        $data = getJsonBody();
        $id     = (int)($data['id'] ?? 0);
        $name   = trim($data['name'] ?? '');
        $email  = trim($data['email'] ?? '');
        $usn    = trim($data['usn'] ?? '');
        $prn    = trim($data['prn_number'] ?? '');
        $roll   = trim($data['roll_number'] ?? '');
        $sem    = (int)($data['semester'] ?? 1);
        $sec    = trim($data['section'] ?? 'A');
        $deptId = (int)($data['department_id'] ?? 0);
        $phone  = trim($data['phone'] ?? '');

        if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid ID.'], 400);

        $student = dbFetchOne("SELECT user_id, department_id FROM students WHERE id = ?", 'i', [$id]);
        if (!$student) jsonResponse(['success' => false, 'message' => 'Student not found.'], 404);

        // Check HOD department access
        if ($user['role'] === 'hod') {
            if ((int)$student['department_id'] !== (int)$user['department_id'] || $deptId !== (int)$user['department_id']) {
                jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
            }
        }

        // Check unique email
        $existingEmail = dbFetchOne("SELECT id FROM users WHERE email = ? AND id != ?", 'si', [$email, $student['user_id']]);
        if ($existingEmail) {
            jsonResponse(['success' => false, 'message' => 'Email already in use.'], 400);
        }

        // Check unique USN
        $existingUsn = dbFetchOne("SELECT id FROM students WHERE usn = ? AND id != ?", 'si', [$usn, $id]);
        if ($existingUsn) {
            jsonResponse(['success' => false, 'message' => 'USN already in use.'], 400);
        }

        // Update user
        dbExecute("UPDATE users SET name = ?, email = ?, department_id = ? WHERE id = ?",
            'ssii', [$name, $email, $deptId, $student['user_id']]);
        
        // Update student
        dbExecute("UPDATE students SET usn = ?, prn_number = ?, roll_number = ?, semester = ?, section = ?, department_id = ?, phone = ? WHERE id = ?",
            'sssisisi', [$usn, $prn, $roll, $sem, $sec, $deptId, $phone, $id]);

        jsonResponse(['success' => true, 'message' => 'Student updated.']);
        break;

    case 'DELETE':
        requireRole(['admin', 'hod']);
        $data = getJsonBody();
        $id = (int)($data['id'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid ID.'], 400);

        $student = dbFetchOne("SELECT user_id, department_id FROM students WHERE id = ?", 'i', [$id]);
        if (!$student) jsonResponse(['success' => false, 'message' => 'Student not found.'], 404);

        // Check HOD department access
        if ($user['role'] === 'hod' && (int)$student['department_id'] !== (int)$user['department_id']) {
            jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
        }

        dbExecute("DELETE FROM users WHERE id = ?", 'i', [$student['user_id']]);
        jsonResponse(['success' => true, 'message' => 'Student deleted.']);
        break;

    default:
        jsonResponse(['success' => false], 405);
}
