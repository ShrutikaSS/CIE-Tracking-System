<?php
/**
 * Notifications API
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
header('Content-Type: application/json');

$user = currentUser();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'count':
        $count = getUnreadNotificationCount($user['id']);
        jsonResponse(['success' => true, 'count' => $count]);
        break;
    
    case 'list':
        $notifications = dbFetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 20",
            'i', [$user['id']]
        );
        
        foreach ($notifications as &$n) {
            $n['time_ago'] = timeAgo($n['created_at']);
        }
        
        jsonResponse(['success' => true, 'notifications' => $notifications]);
        break;
    
    case 'read':
        if (requestMethod() !== 'POST') jsonResponse(['success' => false], 405);
        $data = getJsonBody();
        $id = (int)($data['id'] ?? 0);
        if ($id > 0) {
            dbExecute("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?", 'ii', [$id, $user['id']]);
        }
        jsonResponse(['success' => true]);
        break;
    
    case 'read_all':
        if (requestMethod() !== 'POST') jsonResponse(['success' => false], 405);
        dbExecute("UPDATE notifications SET is_read = 1 WHERE user_id = ?", 'i', [$user['id']]);
        jsonResponse(['success' => true]);
        break;
    
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action.'], 400);
}
