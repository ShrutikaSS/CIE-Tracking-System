<?php
/**
 * Auth API Endpoint
 * POST: login / logout
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (requestMethod() !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$data = getJsonBody();
$action = $data['action'] ?? '';

switch ($action) {
    case 'login':
        $email    = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            jsonResponse(['success' => false, 'message' => 'Email and password are required.'], 400);
        }
        
        $result = login($email, $password);
        jsonResponse($result, $result['success'] ? 200 : 401);
        break;
    
    case 'logout':
        logout();
        jsonResponse(['success' => true, 'message' => 'Logged out.']);
        break;
    
    case 'change_password':
        requireLogin();
        $user = currentUser();
        $currentPw = $data['current_password'] ?? '';
        $newPw = $data['new_password'] ?? '';
        
        if (empty($currentPw) || empty($newPw)) {
            jsonResponse(['success' => false, 'message' => 'All fields required.'], 400);
        }
        
        $dbUser = dbFetchOne("SELECT password FROM users WHERE id = ?", 'i', [$user['id']]);
        if (!$dbUser || !password_verify($currentPw, $dbUser['password'])) {
            jsonResponse(['success' => false, 'message' => 'Incorrect current password.'], 400);
        }
        
        $hashed = password_hash($newPw, PASSWORD_DEFAULT);
        dbExecute("UPDATE users SET password = ? WHERE id = ?", 'si', [$hashed, $user['id']]);
        
        jsonResponse(['success' => true, 'message' => 'Password updated successfully.']);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action.'], 400);
}
