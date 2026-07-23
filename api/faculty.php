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

        // Check HOD department access
        if ($user['role'] === 'hod' && $deptId !== (int)$user['department_id']) {
            jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
        }

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
            'issis', [$userId, $employeeId, $designation, $deptId, $phone]
        );

        // HOD role synchronization
        if ($role === 'hod') {
            $prevHod = dbFetchOne("SELECT hod_id FROM departments WHERE id = ?", 'i', [$deptId]);
            if ($prevHod && $prevHod['hod_id']) {
                dbExecute("UPDATE users SET role = 'faculty' WHERE id = ?", 'i', [$prevHod['hod_id']]);
            }
            dbExecute("UPDATE departments SET hod_id = ? WHERE id = ?", 'ii', [$userId, $deptId]);
        }

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

        $fac = dbFetchOne(
            "SELECT f.user_id, f.department_id, u.role 
             FROM faculty f JOIN users u ON f.user_id = u.id 
             WHERE f.id = ?", 'i', [$id]
        );
        if (!$fac) jsonResponse(['success' => false, 'message' => 'Faculty not found.'], 404);

        $oldRole = $fac['role'];
        $oldDeptId = (int)$fac['department_id'];
        $userId = (int)$fac['user_id'];

        if (!in_array($role, ['faculty', 'coordinator', 'hod'])) $role = 'faculty';

        // Check HOD department access
        if ($user['role'] === 'hod') {
            if ($oldDeptId !== (int)$user['department_id'] || $deptId !== (int)$user['department_id']) {
                jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
            }
        }

        dbExecute("UPDATE users SET name = ?, email = ?, role = ?, department_id = ? WHERE id = ?",
            'sssii', [$name, $email, $role, $deptId, $userId]);
        dbExecute("UPDATE faculty SET employee_id = ?, designation = ?, department_id = ?, phone = ? WHERE id = ?",
            'ssisi', [$employeeId, $designation, $deptId, $phone, $id]);

        // HOD role synchronization
        if ($oldRole === 'hod' && $role !== 'hod') {
            dbExecute("UPDATE departments SET hod_id = NULL WHERE hod_id = ?", 'i', [$userId]);
        }
        if ($role === 'hod') {
            $prevHod = dbFetchOne("SELECT hod_id FROM departments WHERE id = ?", 'i', [$deptId]);
            if ($prevHod && $prevHod['hod_id'] && (int)$prevHod['hod_id'] !== $userId) {
                dbExecute("UPDATE users SET role = 'faculty' WHERE id = ?", 'i', [$prevHod['hod_id']]);
            }
            dbExecute("UPDATE departments SET hod_id = ? WHERE id = ?", 'ii', [$userId, $deptId]);
            if ($oldDeptId !== $deptId) {
                dbExecute("UPDATE departments SET hod_id = NULL WHERE id = ? AND hod_id = ?", 'ii', [$oldDeptId, $userId]);
            }
        }

        jsonResponse(['success' => true, 'message' => 'Faculty updated.']);
        break;

    case 'DELETE':
        $data = getJsonBody();
        $id = (int)($data['id'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid ID.'], 400);

        $fac = dbFetchOne("SELECT user_id, department_id FROM faculty WHERE id = ?", 'i', [$id]);
        if (!$fac) jsonResponse(['success' => false, 'message' => 'Faculty not found.'], 404);

        // Check HOD department access
        if ($user['role'] === 'hod' && (int)$fac['department_id'] !== (int)$user['department_id']) {
            jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
        }

        dbExecute("DELETE FROM users WHERE id = ?", 'i', [$fac['user_id']]);
        jsonResponse(['success' => true, 'message' => 'Faculty deleted.']);
        break;

    default:
        jsonResponse(['success' => false], 405);
}
