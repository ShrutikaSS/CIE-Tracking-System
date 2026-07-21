<?php
/**
 * Dashboard — Routes to role-specific view
 */
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
requireLogin();

$user = currentUser();
$role = $user['role'];

switch ($role) {
    case 'admin':
        include __DIR__ . '/views/dashboard_admin.php';
        break;
    case 'hod':
        include __DIR__ . '/views/dashboard_hod.php';
        break;
    case 'faculty':
        include __DIR__ . '/views/dashboard_faculty.php';
        break;
    case 'coordinator':
        include __DIR__ . '/views/dashboard_coordinator.php';
        break;
    case 'student':
        include __DIR__ . '/views/dashboard_student.php';
        break;
    default:
        echo '<div class="empty-state"><div class="icon">🚫</div><h3>Unknown Role</h3></div>';
}

require_once __DIR__ . '/includes/footer.php';
