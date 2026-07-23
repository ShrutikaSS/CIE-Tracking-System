<?php
/**
 * Announcements API — Broadcast faculty & department announcements
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
header('Content-Type: application/json');

$user = currentUser();
$method = requestMethod();

if ($method === 'POST') {
    requireRole(['admin', 'hod', 'faculty', 'coordinator']);
    $data = getJsonBody();

    $subjectId = (int)($data['subject_id'] ?? 0);
    $title     = trim($data['title'] ?? '');
    $message   = trim($data['message'] ?? '');

    if (empty($title) || empty($message)) {
        jsonResponse(['success' => false, 'message' => 'Title and message are required.'], 400);
    }

    if ($subjectId > 0) {
        // Broadcast to enrolled students of the subject
        $students = dbFetchAll(
            "SELECT s.user_id FROM students s JOIN subject_students ss ON ss.student_id = s.id WHERE ss.subject_id = ?",
            'i', [$subjectId]
        );
        $subjectName = dbFetchOne("SELECT name FROM subjects WHERE id = ?", 'i', [$subjectId])['name'] ?? '';

        foreach ($students as $stu) {
            createNotification(
                $stu['user_id'],
                "Faculty Announcement: " . $title,
                "Announcement for {$subjectName}: {$message}",
                'info',
                '/student/activities.php',
                'announcement',
                'portal'
            );
        }

        jsonResponse(['success' => true, 'message' => 'Announcement broadcast to subject students.']);
    } else {
        // Department-wide announcement
        $deptId = $user['department_id'] ?? 1;
        $students = dbFetchAll("SELECT user_id FROM students WHERE department_id = ?", 'i', [$deptId]);

        foreach ($students as $stu) {
            createNotification(
                $stu['user_id'],
                "Department Announcement: " . $title,
                $message,
                'info',
                '/dashboard.php',
                'announcement',
                'portal'
            );
        }

        jsonResponse(['success' => true, 'message' => 'Announcement broadcast to department students.']);
    }
}

jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
