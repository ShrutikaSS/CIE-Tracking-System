<?php
/**
 * HOD Messages API
 * Handles sending and fetching messages between HOD and Coordinators
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$method = requestMethod();

switch ($action) {

    // ── Fetch messages for current user (Coordinator) ──
    case 'list':
        requireRole(['coordinator', 'hod']);
        $userId = $_SESSION['user_id'];
        $deptId = $_SESSION['department_id'];
        $filter = $_GET['filter'] ?? 'all'; // all, unread, read

        if ($_SESSION['user_role'] === 'coordinator') {
            // Coordinator sees messages sent to them or broadcast to their dept
            $sql = "SELECT m.*, u.name AS sender_name 
                    FROM hod_messages m 
                    JOIN users u ON u.id = m.sender_id 
                    WHERE m.department_id = ? 
                      AND (m.recipient_id = ? OR m.recipient_id IS NULL)";
            $types = 'ii';
            $params = [$deptId, $userId];
        } else {
            // HOD sees messages they sent
            $sql = "SELECT m.*, u.name AS recipient_name 
                    FROM hod_messages m 
                    LEFT JOIN users u ON u.id = m.recipient_id 
                    WHERE m.sender_id = ?";
            $types = 'i';
            $params = [$userId];
        }

        if ($filter === 'unread') {
            $sql .= " AND m.is_read = 0";
        } elseif ($filter === 'read') {
            $sql .= " AND m.is_read = 1";
        }

        $sql .= " ORDER BY m.created_at DESC LIMIT 50";

        $messages = dbFetchAll($sql, $types, $params);
        jsonResponse(['success' => true, 'messages' => $messages]);
        break;

    // ── Get unread count for coordinator ──
    case 'unread_count':
        requireRole(['coordinator']);
        $userId = $_SESSION['user_id'];
        $deptId = $_SESSION['department_id'];

        $row = dbFetchOne(
            "SELECT COUNT(*) as cnt FROM hod_messages 
             WHERE department_id = ? 
               AND (recipient_id = ? OR recipient_id IS NULL) 
               AND is_read = 0",
            'ii', [$deptId, $userId]
        );

        jsonResponse(['success' => true, 'count' => (int)($row['cnt'] ?? 0)]);
        break;

    // ── Mark message as read ──
    case 'mark_read':
        requireRole(['coordinator']);
        $data = getJsonBody();
        $msgId = (int)($data['message_id'] ?? 0);

        if ($msgId <= 0) {
            jsonResponse(['success' => false, 'message' => 'Invalid message ID.'], 400);
        }

        dbExecute(
            "UPDATE hod_messages SET is_read = 1, read_at = NOW() WHERE id = ? AND (recipient_id = ? OR recipient_id IS NULL)",
            'ii', [$msgId, $_SESSION['user_id']]
        );

        jsonResponse(['success' => true, 'message' => 'Message marked as read.']);
        break;

    // ── Mark all messages as read ──
    case 'mark_all_read':
        requireRole(['coordinator']);
        $userId = $_SESSION['user_id'];
        $deptId = $_SESSION['department_id'];

        dbExecute(
            "UPDATE hod_messages SET is_read = 1, read_at = NOW() 
             WHERE department_id = ? AND (recipient_id = ? OR recipient_id IS NULL) AND is_read = 0",
            'ii', [$deptId, $userId]
        );

        jsonResponse(['success' => true, 'message' => 'All messages marked as read.']);
        break;

    // ── Send message (HOD only) ──
    case 'send':
        requireRole(['hod']);
        $data = getJsonBody();
        
        $subject  = trim($data['subject'] ?? '');
        $message  = trim($data['message'] ?? '');
        $priority = $data['priority'] ?? 'normal';
        $recipientId = !empty($data['recipient_id']) ? (int)$data['recipient_id'] : null;
        $deptId   = $_SESSION['department_id'];
        $senderId = $_SESSION['user_id'];

        if (empty($subject) || empty($message)) {
            jsonResponse(['success' => false, 'message' => 'Subject and message are required.'], 400);
        }

        if (!in_array($priority, ['normal', 'important', 'urgent'])) {
            $priority = 'normal';
        }

        $id = dbInsert(
            "INSERT INTO hod_messages (sender_id, recipient_id, department_id, subject, message, priority) VALUES (?, ?, ?, ?, ?, ?)",
            'iiisss',
            [$senderId, $recipientId, $deptId, $subject, $message, $priority]
        );

        if ($id) {
            // Also create a notification for the coordinator(s)
            if ($recipientId) {
                createNotification($recipientId, '📩 New message from HOD: ' . $subject, $message, 'info', '/coordinator/messages.php');
            } else {
                // Broadcast: notify all coordinators in department
                $coordinators = dbFetchAll(
                    "SELECT id FROM users WHERE role = 'coordinator' AND department_id = ?",
                    'i', [$deptId]
                );
                foreach ($coordinators as $coord) {
                    createNotification($coord['id'], '📩 New message from HOD: ' . $subject, $message, 'info', '/coordinator/messages.php');
                }
            }

            jsonResponse(['success' => true, 'message' => 'Message sent successfully.', 'id' => $id]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to send message.'], 500);
        }
        break;

    // ── Get coordinators list (for HOD to pick recipient) ──
    case 'coordinators':
        requireRole(['hod']);
        $deptId = $_SESSION['department_id'];

        $coordinators = dbFetchAll(
            "SELECT id, name, email FROM users WHERE role = 'coordinator' AND department_id = ? AND is_active = 1",
            'i', [$deptId]
        );

        jsonResponse(['success' => true, 'coordinators' => $coordinators]);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action.'], 400);
}
