<?php
/**
 * Shared Utility Functions
 * CIE Activity Marks Tracking System
 */

require_once __DIR__ . '/db.php';

if (!defined('BASE_URL')) {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = dirname($scriptName);
    $knownSubDirs = ['/admin', '/faculty', '/student', '/reports', '/views', '/api', '/hod', '/coordinator', '/include', '/includes'];
    foreach ($knownSubDirs as $sub) {
        if (substr($dir, -strlen($sub)) === $sub) {
            $dir = substr($dir, 0, -strlen($sub));
        }
    }
    define('BASE_URL', rtrim(str_replace('\\', '/', $dir), '/'));
}

/**
 * Generate full URL path relative to application root
 */
function url($path = '') {
    $path = '/' . ltrim($path, '/');
    return BASE_URL . $path;
}

/**
 * Sanitize user input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if (ob_get_level() === 0) {
    ob_start();
}

/**
 * Send JSON response and exit
 */
function jsonResponse($data, $code = 200) {
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get request method
 */
function requestMethod() {
    return $_SERVER['REQUEST_METHOD'] ?? 'GET';
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

require_once __DIR__ . '/email.php';

/**
 * Create a notification for a user with event type and multi-channel support
 */
function createNotification($userId, $title, $message, $type = 'info', $link = null, $eventType = null, $channel = 'portal') {
    $emailStatus = 'sent';
    
    // Insert notification record
    $id = dbInsert(
        "INSERT INTO notifications (user_id, title, message, type, link, event_type, channel, email_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        'isssssss',
        [$userId, $title, $message, $type, $link, $eventType, $channel, $emailStatus]
    );

    // Send email alert for critical event types or when channel requires email/all
    if (in_array($type, ['danger', 'warning']) || in_array($channel, ['email', 'all']) || in_array($eventType, ['password_changed', 'low_performance', 'missing_submission'])) {
        $u = dbFetchOne("SELECT email FROM users WHERE id = ?", 'i', [$userId]);
        if ($u && !empty($u['email'])) {
            sendEmailAlert($u['email'], $title, $message, $link);
        }
    }

    return $id;
}

/**
 * Create notifications for multiple users
 */
function createBulkNotifications($userIds, $title, $message, $type = 'info', $link = null, $eventType = null, $channel = 'portal') {
    foreach ($userIds as $uid) {
        createNotification($uid, $title, $message, $type, $link, $eventType, $channel);
    }
}

/**
 * Run automated system alert checks for:
 * 1. Activity Deadline Reminders (Yellow - 24 hours before deadline)
 * 2. Missing Activity Submissions (Red - Overdue activities)
 */
function runSystemAlertCheck() {
    // 1. Deadline Reminders (Due within next 24 hours)
    $upcomingActivities = dbFetchAll(
        "SELECT a.*, s.name as subject_name 
         FROM activities a 
         JOIN subjects s ON a.subject_id = s.id 
         WHERE a.status = 'active' 
         AND a.deadline IS NOT NULL 
         AND a.deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)"
    );

    foreach ($upcomingActivities as $act) {
        // Find enrolled students who have NOT submitted yet
        $students = dbFetchAll(
            "SELECT st.user_id 
             FROM students st 
             JOIN subject_students ss ON ss.student_id = st.id 
             LEFT JOIN submissions sub ON sub.student_id = st.id AND sub.activity_id = ? 
             WHERE ss.subject_id = ? AND sub.id IS NULL",
            'ii', [$act['id'], $act['subject_id']]
        );

        foreach ($students as $s) {
            // Check if reminder was already sent today
            $alreadyNotified = dbFetchOne(
                "SELECT id FROM notifications WHERE user_id = ? AND link = ? AND event_type = 'deadline_reminder' AND DATE(created_at) = CURDATE()",
                'is', [$s['user_id'], '/student/activities.php']
            );

            if (!$alreadyNotified) {
                createNotification(
                    $s['user_id'],
                    'Activity Deadline Reminder',
                    sprintf('Reminder: "%s" for %s is due by %s.', $act['name'], $act['subject_name'], formatDate($act['deadline'])),
                    'warning',
                    '/student/activities.php',
                    'deadline_reminder',
                    'all'
                );
            }
        }
    }

    // 2. Missing Submissions (Deadline passed, student hasn't submitted)
    $overdueActivities = dbFetchAll(
        "SELECT a.*, s.name as subject_name 
         FROM activities a 
         JOIN subjects s ON a.subject_id = s.id 
         WHERE a.status IN ('active', 'completed') 
         AND a.deadline IS NOT NULL 
         AND a.deadline < CURDATE()"
    );

    foreach ($overdueActivities as $act) {
        $missingStudents = dbFetchAll(
            "SELECT st.user_id, st.id as student_id 
             FROM students st 
             JOIN subject_students ss ON ss.student_id = st.id 
             LEFT JOIN submissions sub ON sub.student_id = st.id AND sub.activity_id = ? 
             WHERE ss.subject_id = ? AND sub.id IS NULL",
            'ii', [$act['id'], $act['subject_id']]
        );

        foreach ($missingStudents as $ms) {
            $alreadyNotified = dbFetchOne(
                "SELECT id FROM notifications WHERE user_id = ? AND event_type = 'missing_submission' AND link = ?",
                'is', [$ms['user_id'], '/student/activities.php']
            );

            if (!$alreadyNotified) {
                createNotification(
                    $ms['user_id'],
                    'Missing Activity Submission',
                    sprintf('Overdue Alert: You have not submitted "%s" for %s.', $act['name'], $act['subject_name']),
                    'danger',
                    '/student/activities.php',
                    'missing_submission',
                    'all'
                );
            }
        }
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
