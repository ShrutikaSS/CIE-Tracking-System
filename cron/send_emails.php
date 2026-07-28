<?php
/**
 * Email Queue Worker / Scheduler Script
 * Command-line execution: php send_emails.php
 */
if (php_sapi_name() !== 'cli') {
    die("Access denied. CLI only.\n");
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/email.php';

echo "[".date('Y-m-d H:i:s')."] Starting notification email queue processing...\n";

// Fetch pending email notifications
$sql = "SELECT n.*, u.email as user_email, u.name as user_name 
        FROM notifications n
        JOIN users u ON n.user_id = u.id
        WHERE n.channel IN ('email', 'all') 
        AND n.email_status = 'pending'
        ORDER BY n.id ASC 
        LIMIT 50";

$pending = dbFetchAll($sql);

if (empty($pending)) {
    echo "[".date('Y-m-d H:i:s')."] No pending email notifications found.\n";
    exit(0);
}

$sentCount = 0;
$failCount = 0;

foreach ($pending as $item) {
    echo "Processing notification #{$item['id']} for {$item['user_email']}... ";
    
    $subject = $item['title'];
    $body = "<p>Hello <strong>" . htmlspecialchars($item['user_name']) . "</strong>,</p>" .
            "<p>" . nl2br(htmlspecialchars($item['message'])) . "</p>";
            
    if (!empty($item['link'])) {
        $body .= "<p><a href='" . htmlspecialchars($item['link']) . "'>Click here to view details</a></p>";
    }

    $sent = sendEmail($item['user_email'], $subject, $body);

    if ($sent) {
        dbQuery("UPDATE notifications SET email_status = 'sent' WHERE id = ?", 'i', [(int)$item['id']]);
        echo "SENT\n";
        $sentCount++;
    } else {
        dbQuery("UPDATE notifications SET email_status = 'failed' WHERE id = ?", 'i', [(int)$item['id']]);
        echo "FAILED\n";
        $failCount++;
    }
}

echo "[".date('Y-m-d H:i:s')."] Batch finished. Sent: {$sentCount}, Failed: {$failCount}.\n";
