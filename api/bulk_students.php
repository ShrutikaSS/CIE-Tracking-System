<?php
/**
 * Bulk Student Import API (Supports Excel, CSV, TXT with smart header matching)
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
requireRole(['admin', 'hod']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

// Map department codes and names to IDs
$depts = dbFetchAll("SELECT id, name, code FROM departments");
$deptMap = [];
foreach ($depts as $d) {
    $deptMap[strtoupper($d['code'])] = (int)$d['id'];
    $deptMap[strtoupper($d['name'])] = (int)$d['id'];
}

function removeBOM($str) {
    return preg_replace('/^\xEF\xBB\xBF/', '', trim((string)$str));
}

function normalizeKey($key) {
    $clean = strtolower(removeBOM($key));
    $clean = preg_replace('/[^a-z0-9]/', '_', $clean);
    $clean = preg_replace('/_+/', '_', trim($clean, '_'));
    
    if (in_array($clean, ['name', 'full_name', 'student_name'])) return 'name';
    if (in_array($clean, ['email', 'email_address', 'mail'])) return 'email';
    if (in_array($clean, ['usn', 'usn_number', 'student_usn'])) return 'usn';
    if (in_array($clean, ['prn', 'prn_number'])) return 'prn_number';
    if (in_array($clean, ['roll', 'roll_no', 'roll_number'])) return 'roll_number';
    if (in_array($clean, ['semester', 'sem'])) return 'semester';
    if (in_array($clean, ['section', 'sec', 'div', 'division'])) return 'section';
    if (in_array($clean, ['department', 'dept', 'department_code', 'dept_code', 'branch'])) return 'department_code';
    if (in_array($clean, ['phone', 'mobile', 'contact', 'phone_number'])) return 'phone';
    return $clean;
}

$rowsToProcess = [];

// 1. Check if payload is JSON (from browser-side SheetJS / Excel parser)
$jsonInput = getJsonBody();
if (!empty($jsonInput) && isset($jsonInput['students']) && is_array($jsonInput['students'])) {
    foreach ($jsonInput['students'] as $rawRow) {
        $normalizedRow = [];
        foreach ($rawRow as $k => $v) {
            $normalizedRow[normalizeKey($k)] = trim((string)$v);
        }
        $rowsToProcess[] = $normalizedRow;
    }
}
// 2. Fallback to raw file upload handling ($_FILES['file'])
elseif (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileContent = file_get_contents($fileTmpPath);
    if ($fileContent === false) {
        jsonResponse(['success' => false, 'message' => 'Failed to read uploaded file.'], 400);
    }
    
    $fileContent = removeBOM($fileContent);
    $lines = preg_split('/\r\n|\r|\n/', trim($fileContent));
    
    if (count($lines) < 2) {
        jsonResponse(['success' => false, 'message' => 'File is empty or has no data rows.'], 400);
    }
    
    // Detect delimiter (, or \t or ;)
    $firstLine = $lines[0];
    $delimiter = ',';
    if (substr_count($firstLine, "\t") > substr_count($firstLine, ",")) {
        $delimiter = "\t";
    } elseif (substr_count($firstLine, ";") > substr_count($firstLine, ",")) {
        $delimiter = ";";
    }
    
    $rawHeaders = str_getcsv($firstLine, $delimiter);
    $headers = array_map('normalizeKey', $rawHeaders);
    
    for ($i = 1; $i < count($lines); $i++) {
        if (trim($lines[$i]) === '') continue;
        $rowCols = str_getcsv($lines[$i], $delimiter);
        $rowObj = [];
        foreach ($headers as $idx => $hKey) {
            if ($hKey) {
                $rowObj[$hKey] = trim((string)($rowCols[$idx] ?? ''));
            }
        }
        $rowsToProcess[] = $rowObj;
    }
} else {
    jsonResponse(['success' => false, 'message' => 'No valid data rows or file received.'], 400);
}

if (empty($rowsToProcess)) {
    jsonResponse(['success' => false, 'message' => 'No student data rows found in the file.'], 400);
}

$createdCount = 0;
$skippedCount = 0;
$errors = [];
$rowNum = 1;

$userRole = currentUser()['role'];
$userDeptId = (int)(currentUser()['department_id'] ?? 0);

// Default department code fallback if single department exists or user is HOD
$defaultDeptCode = '';
if ($userDeptId) {
    $dObj = dbFetchOne("SELECT code FROM departments WHERE id = ?", 'i', [$userDeptId]);
    if ($dObj) $defaultDeptCode = $dObj['code'];
}

foreach ($rowsToProcess as $data) {
    $rowNum++;
    
    $name   = trim($data['name'] ?? '');
    $email  = trim($data['email'] ?? '');
    $usn    = trim($data['usn'] ?? '');
    $prn    = trim($data['prn_number'] ?? '');
    $roll   = trim($data['roll_number'] ?? '');
    $sem    = (int)($data['semester'] ?? 1);
    $sec    = trim($data['section'] ?? 'A');
    $deptCode = strtoupper(trim($data['department_code'] ?? $defaultDeptCode));
    $phone  = trim($data['phone'] ?? '');
    
    if (empty($name) || empty($email) || empty($usn)) {
        $errors[] = "Row {$rowNum}: Missing required student fields (name, email, usn).";
        $skippedCount++;
        continue;
    }
    
    // Resolve department
    if (!isset($deptMap[$deptCode])) {
        // Fallback to user department
        if ($userDeptId) {
            $deptId = $userDeptId;
        } else {
            $errors[] = "Row {$rowNum}: Department code '{$deptCode}' not recognized.";
            $skippedCount++;
            continue;
        }
    } else {
        $deptId = $deptMap[$deptCode];
    }
    
    // HOD scope check
    if ($userRole === 'hod' && $deptId !== $userDeptId) {
        $errors[] = "Row {$rowNum}: HOD cannot import students for department '{$deptCode}'.";
        $skippedCount++;
        continue;
    }
    
    // Unique checks
    if (dbFetchOne("SELECT id FROM users WHERE email = ?", 's', [$email])) {
        $errors[] = "Row {$rowNum}: Email '{$email}' already exists.";
        $skippedCount++;
        continue;
    }
    
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
        dbQuery("DELETE FROM users WHERE id = ?", 'i', [$userId]);
        $errors[] = "Row {$rowNum}: Database error creating student profile.";
        $skippedCount++;
        continue;
    }
    
    $createdCount++;
}

jsonResponse([
    'success' => true,
    'message' => "Import complete. Successfully created {$createdCount} students. Skipped {$skippedCount} rows.",
    'created' => $createdCount,
    'skipped' => $skippedCount,
    'errors' => $errors
]);

