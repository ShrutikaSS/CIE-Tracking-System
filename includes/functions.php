<?php
/**
 * Shared Utility Functions
 * CIE Activity Marks Tracking System
 */

require_once __DIR__ . '/db.php';

/**
 * Sanitize user input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Send JSON response and exit
 */
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get request method
 */
function requestMethod() {
    return $_SERVER['REQUEST_METHOD'];
}

/**
 * Get JSON body from request
 */
function getJsonBody() {
    $body = file_get_contents('php://input');
    return json_decode($body, true) ?? [];
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'M d, Y') {
    if (empty($date)) return '—';
    return date($format, strtotime($date));
}

/**
 * Time ago helper
 */
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

/**
 * Create a notification for a user
 */
function createNotification($userId, $title, $message, $type = 'info', $link = null) {
    return dbInsert(
        "INSERT INTO notifications (user_id, title, message, type, link) VALUES (?, ?, ?, ?, ?)",
        'issss',
        [$userId, $title, $message, $type, $link]
    );
}

/**
 * Create notifications for multiple users
 */
function createBulkNotifications($userIds, $title, $message, $type = 'info', $link = null) {
    foreach ($userIds as $uid) {
        createNotification($uid, $title, $message, $type, $link);
    }
}

/**
 * Get unread notification count for user
 */
function getUnreadNotificationCount($userId) {
    $row = dbFetchOne(
        "SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0",
        'i', [$userId]
    );
    return $row ? (int)$row['cnt'] : 0;
}

/**
 * Pagination helper
 * @return array ['offset', 'limit', 'page', 'totalPages', 'total']
 */
function paginate($total, $page = 1, $perPage = 15) {
    $page = max(1, (int)$page);
    $totalPages = max(1, ceil($total / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;
    
    return [
        'offset'     => $offset,
        'limit'      => $perPage,
        'page'       => $page,
        'totalPages' => $totalPages,
        'total'      => $total
    ];
}

/**
 * Get role display name
 */
function roleLabel($role) {
    $labels = [
        'admin'       => 'Administrator',
        'hod'         => 'Head of Department',
        'faculty'     => 'Faculty',
        'coordinator' => 'Coordinator',
        'student'     => 'Student'
    ];
    return $labels[$role] ?? ucfirst($role);
}

/**
 * Get role badge color class
 */
function roleBadgeClass($role) {
    $classes = [
        'admin'       => 'badge-danger',
        'hod'         => 'badge-purple',
        'faculty'     => 'badge-primary',
        'coordinator' => 'badge-warning',
        'student'     => 'badge-success'
    ];
    return $classes[$role] ?? 'badge-secondary';
}

/**
 * Get activity type icon
 */
function activityTypeIcon($type) {
    $icons = [
        'assignment' => '📝',
        'quiz'       => '❓',
        'test'       => '📋',
        'seminar'    => '🎤',
        'viva'       => '🗣️'
    ];
    return $icons[$type] ?? '📌';
}

/**
 * Get status badge class
 */
function statusBadgeClass($status) {
    $classes = [
        'draft'     => 'badge-secondary',
        'active'    => 'badge-primary',
        'completed' => 'badge-success',
        'cancelled' => 'badge-danger'
    ];
    return $classes[$status] ?? 'badge-secondary';
}

/**
 * Calculate percentage safely
 */
function calcPercentage($obtained, $total) {
    if ($total <= 0) return 0;
    return round(($obtained / $total) * 100, 1);
}
