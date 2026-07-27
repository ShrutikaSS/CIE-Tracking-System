<?php
/**
 * Authentication & Session Management
 * CIE Activity Marks Tracking System
 */

// Ensure output buffering is active to prevent stray PHP notices/warnings in API responses
if (ob_get_level() === 0) {
    ob_start();
}

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly'  => true,
        'samesite'  => 'Strict'
    ]);
    session_start();
}

require_once __DIR__ . '/db.php';

/**
 * Attempt login with email and password
 * @return array ['success' => bool, 'message' => string, 'user' => array|null]
 */
function login($email, $password) {
    $user = dbFetchOne(
        "SELECT id, name, email, password, role, department_id, avatar, is_active FROM users WHERE email = ?",
        's',
        [$email]
    );
    
    if (!$user) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }
    
    if (!$user['is_active']) {
        return ['success' => false, 'message' => 'Your account has been deactivated.'];
    }
    
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }
    
    // Set session data
    $_SESSION['user_id']       = $user['id'];
    $_SESSION['user_name']     = $user['name'];
    $_SESSION['user_email']    = $user['email'];
    $_SESSION['user_role']     = $user['role'];
    $_SESSION['department_id'] = $user['department_id'];
    $_SESSION['user_avatar']   = $user['avatar'];
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Update last login
    dbExecute("UPDATE users SET last_login = NOW() WHERE id = ?", 'i', [$user['id']]);
    
    return [
        'success' => true,
        'message' => 'Login successful.',
        'user'    => [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role']
        ]
    ];
}

/**
 * Logout — destroy session
 */
function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user data from session
 */
function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id'            => $_SESSION['user_id'],
        'name'          => $_SESSION['user_name'],
        'email'         => $_SESSION['user_email'],
        'role'          => $_SESSION['user_role'],
        'department_id' => $_SESSION['department_id'],
        'avatar'        => $_SESSION['user_avatar']
    ];
}

/**
 * Require login — redirect if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $isApi = (strpos($uri, '/api/') !== false) || 
                 (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        
        if ($isApi) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
            exit;
        }
        header('Location: /index.php');
        exit;
    }
}

/**
 * Require specific role(s)
 * @param string|array $roles Allowed role(s)
 */
function requireRole($roles) {
    requireLogin();
    
    if (is_string($roles)) {
        $roles = [$roles];
    }
    
    if (!in_array($_SESSION['user_role'], $roles)) {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $isApi = (strpos($uri, '/api/') !== false) || 
                 (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        
        if ($isApi) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }
        header('Location: /dashboard.php');
        exit;
    }
}

/**
 * Generate CSRF token
 */
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get base URL for the application
 */
function baseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'];
}
