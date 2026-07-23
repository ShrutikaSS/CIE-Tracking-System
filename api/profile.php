<?php
/**
 * Profile API Endpoint
 * Handles GET (view profile) and POST (update profile details + avatar file upload)
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
header('Content-Type: application/json');

$user = currentUser();
$userId = $user['id'];
$method = requestMethod();

if ($method === 'GET') {
    // Retrieve profile details
    $profile = dbFetchOne(
        "SELECT u.id as user_id, u.name, u.email, u.role, u.avatar,
                s.id as student_id, s.usn, s.prn_number, s.roll_number, s.semester, s.section,
                f.id as faculty_id, f.employee_id, f.designation,
                COALESCE(s.phone, f.phone) as phone,
                COALESCE(d.name, fd.name) as department_name
         FROM users u
         LEFT JOIN students s ON s.user_id = u.id
         LEFT JOIN departments d ON s.department_id = d.id
         LEFT JOIN faculty f ON f.user_id = u.id
         LEFT JOIN departments fd ON f.department_id = fd.id
         WHERE u.id = ?", 'i', [$userId]
    );
    
    if (!$profile) {
        jsonResponse(['success' => false, 'message' => 'Profile not found.'], 404);
    }
    
    jsonResponse(['success' => true, 'profile' => $profile]);
}

if ($method === 'POST') {
    // Update profile details
    // Note: Since this may contain file uploads, we read from $_POST instead of JSON body
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    if (empty($email)) {
        jsonResponse(['success' => false, 'message' => 'Email is required.'], 400);
    }
    
    // Check if email already in use
    $existing = dbFetchOne("SELECT id FROM users WHERE email = ? AND id != ?", 'si', [$email, $userId]);
    if ($existing) {
        jsonResponse(['success' => false, 'message' => 'Email is already in use by another user.'], 400);
    }
    
    $avatarUrl = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            jsonResponse(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.'], 400);
        }
        
        if ($file['size'] > $maxSize) {
            jsonResponse(['success' => false, 'message' => 'File size exceeds 2MB limit.'], 400);
        }
        
        $uploadDir = __DIR__ . '/../uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $avatarUrl = '/uploads/avatars/' . $fileName;
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to upload image.'], 500);
        }
    }
    
    // Update users table
    if ($avatarUrl) {
        dbExecute("UPDATE users SET email = ?, avatar = ? WHERE id = ?", 'ssi', [$email, $avatarUrl, $userId]);
        $_SESSION['user_avatar'] = $avatarUrl; // Update session
    } else {
        dbExecute("UPDATE users SET email = ? WHERE id = ?", 'si', [$email, $userId]);
    }
    $_SESSION['user_email'] = $email; // Update session
    
    // Update students or faculty table
    if ($user['role'] === 'student') {
        dbExecute("UPDATE students SET phone = ? WHERE user_id = ?", 'si', [$phone, $userId]);
    } elseif (in_array($user['role'], ['hod', 'faculty', 'coordinator'])) {
        dbExecute("UPDATE faculty SET phone = ? WHERE user_id = ?", 'si', [$phone, $userId]);
    }
    
    // Create notification
    createNotification($userId, 'Profile Updated', 'Your profile details have been successfully updated.', 'success', '/student/profile.php', 'profile_updated', 'portal');
    
    jsonResponse(['success' => true, 'message' => 'Profile updated successfully.', 'avatar' => $avatarUrl]);
}

jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
