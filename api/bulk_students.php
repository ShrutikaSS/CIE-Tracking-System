<?php
/**
 * Bulk Student Import API
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
requireRole(['admin', 'hod']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['success' => false, 'message' => 'No file uploaded or file upload error.'], 400);
}

$fileTmpPath = $_FILES['file']['tmp_name'];
$fileName = $_FILES['file']['name'];
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if ($fileExtension !== 'csv') {
    jsonResponse(['success' => false, 'message' => 'Only CSV files are allowed.'], 400);
}

// Map department codes to IDs for validation and insertion
$depts = dbFetchAll("SELECT id, code FROM departments");
$deptMap = [];
foreach ($depts as $d) {
    $deptMap[strtoupper($d['code'])] = (int)$d['id'];
}

$handle = fopen($fileTmpPath, 'r');
if ($handle === false) {
    jsonResponse(['success' => false, 'message' => 'Failed to open CSV file.'], 500);
}

// Read header row
$headers = fgetcsv($handle);
// Expected headers: name, email, usn, prn_number, roll_number, semester, section, department_code, phone
$expectedHeaders = ['name', 'email', 'usn', 'prn_number', 'roll_number', 'semester', 'section', 'department_code', 'phone'];

// Simple case-insensitive verification
if ($headers) {
    $headers = array_map('trim', array_map('strtolower', $headers));
    // If headers don't match expected names, assume no headers or invalid format
    if (!in_array('usn', $headers) || !in_array('email', $headers) || !in_array('name', $headers)) {
        jsonResponse(['success' => false, 'message' => 'CSV must contain at least "name", "email", and "usn" headers.'], 400);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Empty CSV file.'], 400);
}

$createdCount = 0;
$skippedCount = 0;
$errors = [];
$rowNum = 1;

$userRole = currentUser()['role'];
$userDeptId = (int)(currentUser()['department_id'] ?? 0);

while (($row = fgetcsv($handle)) !== false) {
    $rowNum++;
    
    // Combine headers with row values
    $data = array_combine($headers, array_pad($row, count($headers), ''));
    if (!$data) {
        $errors[] = "Row {$rowNum}: Invalid column structure.";
        $skippedCount++;
        continue;
    }
    
    $name   = trim($data['name'] ?? '');
    $email  = trim($data['email'] ?? '');
    $usn    = trim($data['usn'] ?? '');
    $prn    = trim($data['prn_number'] ?? '');
    $roll   = trim($data['roll_number'] ?? '');
    $sem    = (int)($data['semester'] ?? 1);
    $sec    = trim($data['section'] ?? 'A');
    $deptCode = strtoupper(trim($data['department_code'] ?? ''));
    $phone  = trim($data['phone'] ?? '');
    
    if (empty($name) || empty($email) || empty($usn) || empty($deptCode)) {
        $errors[] = "Row {$rowNum}: Missing required fields (name, email, usn, department_code).";
        $skippedCount++;
        continue;
    }
    
    // Resolve department
    if (!isset($deptMap[$deptCode])) {
        $errors[] = "Row {$rowNum}: Invalid department code '{$deptCode}'.";
        $skippedCount++;
        continue;
    }
    
    $deptId = $deptMap[$deptCode];
    
    // If HOD, can only import into their own department
    if ($userRole === 'hod' && $deptId !== $userDeptId) {
        $errors[] = "Row {$rowNum}: HOD cannot import students to department code '{$deptCode}'.";
        $skippedCount++;
        continue;
    }
    
    // Check if user email exists
    if (dbFetchOne("SELECT id FROM users WHERE email = ?", 's', [$email])) {
        $errors[] = "Row {$rowNum}: Email '{$email}' already exists.";
        $skippedCount++;
        continue;
    }
    
    // Check if USN exists
    if (dbFetchOne("SELECT id FROM students WHERE usn = ?", 's', [$usn])) {
        $errors[] = "Row {$rowNum}: USN '{$usn}' already exists.";
        $skippedCount++;
        continue;
    }
    
    // Create user
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $userId = dbInsert(
        "INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, 'student', ?)",
        'sssi', [$name, $email, $password, $deptId]
    );
    
    if (!$userId) {
        $errors[] = "Row {$rowNum}: Database error creating user.";
        $skippedCount++;
        continue;
    }
    
    // Create student
    $studentId = dbInsert(
        "INSERT INTO students (user_id, usn, prn_number, roll_number, semester, section, department_id, phone, admission_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, YEAR(CURDATE()))",
        'isssisss', [$userId, $usn, $prn, $roll, $sem, $sec, $deptId, $phone]
    );
    
    if (!$studentId) {
        // Rollback user creation
        dbQuery("DELETE FROM users WHERE id = ?", 'i', [$userId]);
        $errors[] = "Row {$rowNum}: Database error creating student profile.";
        $skippedCount++;
        continue;
    }
    
    $createdCount++;
}

fclose($handle);

jsonResponse([
    'success' => true,
    'message' => "Import complete. Created {$createdCount} students. Skipped {$skippedCount} rows.",
    'created' => $createdCount,
    'skipped' => $skippedCount,
    'errors' => $errors
]);
