<?php
/**
 * Faculty API — CRUD
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
requireRole(['admin', 'hod']);
header('Content-Type: application/json');

$user = currentUser();
$method = requestMethod();

switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $fac = dbFetchOne(
                "SELECT f.*, u.name, u.email, u.is_active, u.role, d.name as dept_name, d.code as dept_code
                 FROM faculty f JOIN users u ON f.user_id = u.id JOIN departments d ON f.department_id = d.id 
                 WHERE f.id = ?", 'i', [(int)$id]
            );
            jsonResponse(['success' => true, 'faculty' => $fac]);
        }
        
        $dept = $_GET['department'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $sql = "SELECT f.*, u.name, u.email, u.is_active, u.role, d.name as dept_name, d.code as dept_code,
                (SELECT COUNT(*) FROM subjects s WHERE s.faculty_id = f.id) as subject_count
                FROM faculty f JOIN users u ON f.user_id = u.id JOIN departments d ON f.department_id = d.id WHERE 1=1";
        $types = '';
        $params = [];
        
        if ($user['role'] === 'hod') {
            $sql .= " AND f.department_id = ?";
            $types .= 'i';
            $params[] = $user['department_id'];
        }
        if ($dept) {
            $sql .= " AND f.department_id = ?";
            $types .= 'i';
            $params[] = (int)$dept;
        }
        if ($search) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR f.employee_id LIKE ?)";
            $types .= 'sss';
            $like = "%$search%";
            array_push($params, $like, $like, $like);
        }
        
        $sql .= " ORDER BY u.name";
        $faculty = dbFetchAll($sql, $types, $params);
        jsonResponse(['success' => true, 'faculty' => $faculty]);
        break;

    case 'POST':
        $data = getJsonBody();
        $name        = trim($data['name'] ?? '');
        $email       = trim($data['email'] ?? '');
        $employeeId  = trim($data['employee_id'] ?? '');
        $designation = trim($data['designation'] ?? 'Assistant Professor');
        $deptId      = (int)($data['department_id'] ?? 0);
        $role        = $data['role'] ?? 'faculty';
        $phone       = trim($data['phone'] ?? '');

        if (empty($name) || empty($email) || empty($employeeId) || !$deptId) {
            jsonResponse(['success' => false, 'message' => 'All required fields must be filled.'], 400);
        }

        if (!in_array($role, ['faculty', 'coordinator', 'hod'])) $role = 'faculty';

        if (dbFetchOne("SELECT id FROM users WHERE email = ?", 's', [$email])) {
            jsonResponse(['success' => false, 'message' => 'Email already exists.'], 400);
        }
        if (dbFetchOne("SELECT id FROM faculty WHERE employee_id = ?", 's', [$employeeId])) {
            jsonResponse(['success' => false, 'message' => 'Employee ID already exists.'], 400);
        }

        $password = password_hash('password123', PASSWORD_DEFAULT);
        $userId = dbInsert(
            "INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, ?, ?)",
            'ssssi', [$name, $email, $password, $role, $deptId]
        );

        $facId = dbInsert(
            "INSERT INTO faculty (user_id, employee_id, designation, department_id, phone) VALUES (?, ?, ?, ?, ?)",
            'isssi', [$userId, $employeeId, $designation, $deptId, $phone]
        );

        jsonResponse(['success' => true, 'message' => 'Faculty added.', 'id' => $facId]);
        break;

    case 'PUT':
        $data = getJsonBody();
        $id          = (int)($data['id'] ?? 0);
        $name        = trim($data['name'] ?? '');
        $email       = trim($data['email'] ?? '');
        $employeeId  = trim($data['employee_id'] ?? '');
        $designation = trim($data['designation'] ?? '');
        $deptId      = (int)($data['department_id'] ?? 0);
        $role        = $data['role'] ?? 'faculty';
        $phone       = trim($data['phone'] ?? '');

        if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid ID.'], 400);

        $fac = dbFetchOne("SELECT user_id FROM faculty WHERE id = ?", 'i', [$id]);
        if (!$fac) jsonResponse(['success' => false, 'message' => 'Faculty not found.'], 404);

        if (!in_array($role, ['faculty', 'coordinator', 'hod'])) $role = 'faculty';

        dbExecute("UPDATE users SET name = ?, email = ?, role = ?, department_id = ? WHERE id = ?",
            'sssii', [$name, $email, $role, $deptId, $fac['user_id']]);
        dbExecute("UPDATE faculty SET employee_id = ?, designation = ?, department_id = ?, phone = ? WHERE id = ?",
            'sssis', [$employeeId, $designation, $deptId, $phone, $id]);

        jsonResponse(['success' => true, 'message' => 'Faculty updated.']);
        break;

    case 'DELETE':
        $data = getJsonBody();
        $id = (int)($data['id'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid ID.'], 400);

        $fac = dbFetchOne("SELECT user_id FROM faculty WHERE id = ?", 'i', [$id]);
        if ($fac) {
            dbExecute("DELETE FROM users WHERE id = ?", 'i', [$fac['user_id']]);
        }
        jsonResponse(['success' => true, 'message' => 'Faculty deleted.']);
        break;

    default:
        jsonResponse(['success' => false], 405);
}
