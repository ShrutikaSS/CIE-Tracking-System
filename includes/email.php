<?php
/**
 * Email Alert Service & Template Generator
 * CIE Activity Marks Tracking System
 */

require_once __DIR__ . '/db.php';

/**
 * Send or log an email alert for critical events
 * @param string $toEmail User's email address
 * @param string $subject Email subject line
 * @param string $bodyText Email body message
 * @param string $actionUrl Optional action button URL
 * @return bool
 */
function sendEmailAlert($toEmail, $subject, $bodyText, $actionUrl = null) {
    if (empty($toEmail)) return false;

    $siteName = "Zeal CIE Tracking System";
    $year = date('Y');
    
    // HTML Email Template
    $htmlContent = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <style>
            body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
            .header { background: #1a73e8; color: #ffffff; padding: 20px; text-align: center; }
            .header h2 { margin: 0; font-size: 20px; font-weight: 600; }
            .content { padding: 30px 25px; line-height: 1.6; }
            .btn { display: inline-block; padding: 12px 24px; background: #1a73e8; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 15px; }
            .footer { background: #f8f9fa; text-align: center; padding: 15px; font-size: 0.8rem; color: #777; border-top: 1px solid #eeeeee; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>{$siteName}</h2>
            </div>
            <div class='content'>
                <h3>{$subject}</h3>
                <p>" . nl2br(htmlspecialchars($bodyText)) . "</p>";
                
    if (!empty($actionUrl)) {
        $fullUrl = (strpos($actionUrl, 'http') === 0) ? $actionUrl : url($actionUrl);
        $htmlContent .= "<p><a href='{$fullUrl}' class='btn'>View in Portal</a></p>";
    }

    $htmlContent .= "
            </div>
            <div class='footer'>
                &copy; {$year} {$siteName}. All rights reserved.
            </div>
        </div>
    </body>
    </html>";

    // Attempt PHP mail()
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@cie.edu" . "\r\n";

    @mail($toEmail, $subject, $htmlContent, $headers);

    // Log structured email alert to disk for development / verification
    $logDir = __DIR__ . '/../logs/';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logEntry = sprintf(
        "[%s] TO: %s | SUBJECT: %s | MSG: %s | URL: %s\n",
        date('Y-m-d H:i:s'),
        $toEmail,
        $subject,
        $bodyText,
        $actionUrl ?: 'N/A'
    );
    @file_put_contents($logDir . 'email_alerts.log', $logEntry, FILE_APPEND);

    return true;
}
