<?php
/**
 * Submit Activity API — Handles File Upload & Text Submissions for Students
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/cie_marks.php';

requireRole(['student']);
header('Content-Type: application/json');

$user = currentUser();
$method = requestMethod();

if ($method !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

// Get student ID
$stu = dbFetchOne("SELECT id FROM students WHERE user_id = ?", 'i', [$user['id']]);
if (!$stu) {
    jsonResponse(['success' => false, 'message' => 'Student record not found.'], 404);
}
$studentId = $stu['id'];

// Get parameters from POST
$activityId = (int)($_POST['activity_id'] ?? 0);
$submissionText = trim($_POST['submission_text'] ?? '');

if (!$activityId) {
    jsonResponse(['success' => false, 'message' => 'Activity ID is required.'], 400);
}

// Verify activity exists and student is enrolled in the subject
$activity = dbFetchOne(
    "SELECT a.*, s.name as subject_name 
     FROM activities a 
     JOIN subjects s ON a.subject_id = s.id 
     JOIN subject_students ss ON ss.subject_id = s.id 
     WHERE a.id = ? AND ss.student_id = ? AND a.status IN ('active', 'completed')",
    'ii', [$activityId, $studentId]
);

if (!$activity) {
    jsonResponse(['success' => false, 'message' => 'Activity not found or not enrolled in this subject.'], 404);
}

// Handle file upload
$filePath = null;
if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['submission_file'];
    
    // File validation
    $maxSize = 5 * 1024 * 1024; // 5MB
    $allowedMimes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/x-png'];
    
    // Check extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
    
    if (!in_array($ext, $allowedExts) || !in_array($file['type'], $allowedMimes)) {
        jsonResponse(['success' => false, 'message' => 'Invalid file type. Only PDF, JPG, JPEG, and PNG files are allowed.'], 400);
    }
    
    if ($file['size'] > $maxSize) {
        jsonResponse(['success' => false, 'message' => 'File size exceeds 5MB limit.'], 400);
    }
    
    // Create directory
    $uploadDir = __DIR__ . '/../uploads/submissions/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = 'sub_' . $studentId . '_' . $activityId . '_' . time() . '.' . $ext;
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $filePath = '/uploads/submissions/' . $fileName;
    } else {
        jsonResponse(['success' => false, 'message' => 'Failed to move uploaded file.'], 500);
    }
}

// Calculate auto marks based on submission time
$nowStr = date('Y-m-d H:i:s');
$marksAwarded = calculateMarks($activity['start_time'] ?? null, $activity['end_time'] ?? null, $nowStr, floatval($activity['max_marks']));

// Check if submission already exists
$existing = dbFetchOne(
    "SELECT id, file_path FROM submissions WHERE student_id = ? AND activity_id = ?",
    'ii', [$studentId, $activityId]
);

if ($existing) {
    // Overwriting submission: delete old file if a new one is uploaded
    if ($filePath && $existing['file_path']) {
        $oldFileRealPath = __DIR__ . '/..' . $existing['file_path'];
        if (file_exists($oldFileRealPath)) {
            @unlink($oldFileRealPath);
        }
    }
    
    // Update
    if ($filePath) {
        dbExecute(
            "UPDATE submissions SET file_path = ?, submission_text = ?, marks_awarded = ?, submitted_at = NOW() WHERE id = ?",
            'ssdi', [$filePath, $submissionText ?: null, $marksAwarded, $existing['id']]
        );
    } else {
        dbExecute(
            "UPDATE submissions SET submission_text = ?, marks_awarded = ?, submitted_at = NOW() WHERE id = ?",
            'sdi', [$submissionText ?: null, $marksAwarded, $existing['id']]
        );
    }
    $message = 'Submission updated successfully.';
} else {
    // Insert
    dbInsert(
        "INSERT INTO submissions (activity_id, student_id, file_path, submission_text, marks_awarded, submitted_at) VALUES (?, ?, ?, ?, ?, NOW())",
        'iissd', [$activityId, $studentId, $filePath, $submissionText ?: null, $marksAwarded]
    );
    $message = 'Submission uploaded successfully.';
}

// Notify faculty assigned to the subject
$facultyInfo = dbFetchOne(
    "SELECT f.user_id 
     FROM activities a 
     JOIN subjects s ON a.subject_id = s.id 
     JOIN faculty f ON s.faculty_id = f.id 
     WHERE a.id = ?",
    'i', [$activityId]
);

// Get student name and USN separately
$studentInfo = dbFetchOne(
    "SELECT u.name as student_name, st.usn 
     FROM students st 
     JOIN users u ON st.user_id = u.id 
     WHERE st.id = ?",
    'i', [$studentId]
);

if ($facultyInfo && !empty($facultyInfo['user_id']) && $studentInfo) {
    $title = $existing ? 'Updated Activity Submission' : 'New Activity Submission';
    $notifMsg = sprintf(
        '%s (%s) %s "%s" for %s.',
        $studentInfo['student_name'],
        $studentInfo['usn'],
        $existing ? 'updated their submission for' : 'submitted',
        $activity['name'],
        $activity['subject_name']
    );
    createNotification(
        $facultyInfo['user_id'],
        $title,
        $notifMsg,
        'info',
        '/faculty/marks.php?activity=' . $activityId,
        'new_submission',
        'portal'
    );
}

// Notify the student that their submission was received
$submissionLabel = $existing ? 'updated' : 'received';
createNotification(
    $user['id'],
    'Submission ' . ucfirst($submissionLabel),
    sprintf('Your submission for "%s" has been %s successfully.', $activity['name'], $submissionLabel),
    'success',
    '/student/activities.php',
    'submission_received',
    'portal'
);

jsonResponse(['success' => true, 'message' => $message]);
