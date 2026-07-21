<?php
/**
 * Departments API — CRUD
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
requireRole(['admin', 'hod']);
header('Content-Type: application/json');

$method = requestMethod();

switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $dept = dbFetchOne("SELECT d.*, u.name as hod_name FROM departments d LEFT JOIN users u ON d.hod_id = u.id WHERE d.id = ?", 'i', [(int)$id]);
            jsonResponse(['success' => true, 'department' => $dept]);
        } else {
            $depts = dbFetchAll(
                "SELECT d.*, u.name as hod_name, 
                 (SELECT COUNT(*) FROM students s WHERE s.department_id = d.id) as student_count,
                 (SELECT COUNT(*) FROM faculty f WHERE f.department_id = d.id) as faculty_count,
                 (SELECT COUNT(*) FROM subjects sub WHERE sub.department_id = d.id) as subject_count
                 FROM departments d LEFT JOIN users u ON d.hod_id = u.id ORDER BY d.name"
            );
            jsonResponse(['success' => true, 'departments' => $depts]);
        }
        break;

    case 'POST':
        $data = getJsonBody();
        $name = trim($data['name'] ?? '');
        $code = trim($data['code'] ?? '');
        $hodId = !empty($data['hod_id']) ? (int)$data['hod_id'] : null;

        if (empty($name) || empty($code)) {
            jsonResponse(['success' => false, 'message' => 'Name and code are required.'], 400);
        }

        // Check unique code
        $existing = dbFetchOne("SELECT id FROM departments WHERE code = ?", 's', [$code]);
        if ($existing) {
            jsonResponse(['success' => false, 'message' => 'Department code already exists.'], 400);
        }

        $id = dbInsert(
            "INSERT INTO departments (name, code, hod_id) VALUES (?, ?, ?)",
            'ssi', [$name, strtoupper($code), $hodId]
        );

        jsonResponse(['success' => true, 'message' => 'Department created.', 'id' => $id]);
        break;

    case 'PUT':
        $data = getJsonBody();
        $id = (int)($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $code = trim($data['code'] ?? '');
        $hodId = !empty($data['hod_id']) ? (int)$data['hod_id'] : null;

        if (!$id || empty($name) || empty($code)) {
            jsonResponse(['success' => false, 'message' => 'Invalid data.'], 400);
        }

        $existing = dbFetchOne("SELECT id FROM departments WHERE code = ? AND id != ?", 'si', [$code, $id]);
        if ($existing) {
            jsonResponse(['success' => false, 'message' => 'Department code already exists.'], 400);
        }

        dbExecute(
            "UPDATE departments SET name = ?, code = ?, hod_id = ? WHERE id = ?",
            'ssii', [$name, strtoupper($code), $hodId, $id]
        );

        jsonResponse(['success' => true, 'message' => 'Department updated.']);
        break;

    case 'DELETE':
        $data = getJsonBody();
        $id = (int)($data['id'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid ID.'], 400);

        dbExecute("DELETE FROM departments WHERE id = ?", 'i', [$id]);
        jsonResponse(['success' => true, 'message' => 'Department deleted.']);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
