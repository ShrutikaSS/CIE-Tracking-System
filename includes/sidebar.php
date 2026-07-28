<?php
/**
 * Sidebar Navigation
 * Role-based menu items with SVG icons
 */
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$userRole = $_SESSION['user_role'] ?? '';

// Inline SVG Icon definitions
$icons = [
    'dashboard'      => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
    'departments'    => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="6" x2="9.01" y2="6"></line><line x1="15" y1="6" x2="15.01" y2="6"></line><line x1="9" y1="10" x2="9.01" y2="10"></line><line x1="15" y1="10" x2="15.01" y2="10"></line><path d="M10 22v-4h4v4"></path></svg>',
    'faculty'        => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>',
    'students'       => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
    'subjects'       => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>',
    'activities'     => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>',
    'marks'          => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
    'reports'        => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>',
    'student_report' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><circle cx="10" cy="13" r="2"></circle><path d="M7 17c0-1.5 1.5-2 3-2s3 .5 3 2"></path></svg>',
    'subject_report' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path><line x1="9" y1="14" x2="9" y2="10"></line><line x1="13" y1="14" x2="13" y2="7"></line><line x1="17" y1="14" x2="17" y2="11"></line></svg>',
    'progress'       => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
    'profile'        => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
    'attendance'     => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
    'notifications'  => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
    'messages'       => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>'
];

$unreadHODMsgCount = null;
$hodSubCount = null;
if ($userRole === 'coordinator') {
    $userId = $_SESSION['user_id'] ?? 0;
    $deptId = $_SESSION['department_id'] ?? 0;
    $row = dbFetchOne(
        "SELECT COUNT(*) as cnt FROM hod_messages WHERE department_id = ? AND (recipient_id = ? OR recipient_id IS NULL) AND is_read = 0",
        'ii', [$deptId, $userId]
    );
    if ($row && $row['cnt'] > 0) {
        $unreadHODMsgCount = (int)$row['cnt'];
    }
} elseif ($userRole === 'hod') {
    $deptId = $_SESSION['department_id'] ?? 0;
    $row = dbFetchOne("SELECT COUNT(*) as cnt FROM coordinator_submissions WHERE department_id = ?", 'i', [$deptId]);
    if ($row && $row['cnt'] > 0) {
        $hodSubCount = (int)$row['cnt'];
    }
}

function navItem($href, $icon, $label, $currentPath, $count = null) {
    $fullUrl = url($href);
    $active = strpos($currentPath, $href) !== false ? 'active' : '';
    $badge = $count !== null ? "<span class=\"nav-count\">$count</span>" : '';
    return "<a href=\"$fullUrl\" class=\"nav-item $active\" data-title=\"$label\">
              <span class=\"nav-icon\">$icon</span>
              <span class=\"nav-label\">$label</span>
              $badge
            </a>";
}
?>

<aside class="app-sidebar">
  <!-- Logo -->
  <div class="sidebar-logo">
    <a href="<?= url('/dashboard.php') ?>" style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; text-decoration: none;">
      <div class="logo-icon">
        <img src="<?= url('/assets/logo.jpg') ?>" alt="ZCOER Logo">
      </div>
      <span class="logo-text">CIE Tracker</span>
    </a>
    <button class="sidebar-close-btn" onclick="Sidebar.close()" title="Close Sidebar">✕</button>
  </div>
  
  <!-- Navigation -->
  <nav class="sidebar-nav">
    <?php if ($userRole === 'student'): ?>
    <!-- Student Menu -->
    <div class="nav-section">
      <div class="nav-section-title">Student Portal</div>
      <?= navItem('/dashboard.php', $icons['dashboard'], 'Dashboard', $currentPath) ?>
      <?= navItem('/student/activities.php', $icons['activities'], 'My Activities', $currentPath) ?>
      <?= navItem('/student/marks.php', $icons['marks'], 'My Marks', $currentPath) ?>
      <?= navItem('/student/performance.php', $icons['progress'], 'Academic Progress', $currentPath) ?>
      <?= navItem('/reports/cumulative_report.php', $icons['reports'], 'Cumulative Report', $currentPath) ?>
      <?= navItem('/student/attendance.php', $icons['attendance'], 'Attendance', $currentPath) ?>
    </div>
    <?php else: ?>
    <!-- Main -->
    <div class="nav-section">
      <div class="nav-section-title">Main</div>
      <?= navItem('/dashboard.php', $icons['dashboard'], 'Dashboard', $currentPath) ?>
    </div>
    
    <?php if ($userRole === 'admin'): ?>
    <!-- Admin Menu -->
    <div class="nav-section">
      <div class="nav-section-title">Management</div>
      <?= navItem('/admin/departments.php', $icons['departments'], 'Departments', $currentPath) ?>
      <?= navItem('/admin/faculty.php', $icons['faculty'], 'Faculty', $currentPath) ?>
      <?= navItem('/admin/students.php', $icons['students'], 'Students', $currentPath) ?>
      <?= navItem('/admin/subjects.php', $icons['subjects'], 'Subjects', $currentPath) ?>
    </div>
    <div class="nav-section">
      <div class="nav-section-title">Activities</div>
      <?= navItem('/faculty/activities.php', $icons['activities'], 'Activities', $currentPath) ?>
      <?= navItem('/faculty/marks.php', $icons['marks'], 'Marks Entry', $currentPath) ?>
    </div>
    <div class="nav-section">
      <div class="nav-section-title">Reports</div>
      <?= navItem('/reports/student_report.php', $icons['student_report'], 'Student Report', $currentPath) ?>
      <?= navItem('/reports/subject_report.php', $icons['subject_report'], 'Subject Report', $currentPath) ?>
      <?= navItem('/reports/cumulative_report.php', $icons['reports'], 'Cumulative Report', $currentPath) ?>
    </div>
    
    <?php elseif ($userRole === 'hod'): ?>
    <!-- HOD Menu -->
    <div class="nav-section">
      <div class="nav-section-title">Department</div>
      <?= navItem('/hod/department_performance.php', $icons['progress'], 'Dept Performance', $currentPath) ?>
      <?= navItem('/hod/faculty_performance.php', $icons['faculty'], 'Faculty Performance', $currentPath) ?>
      <?= navItem('/hod/student_performance.php', $icons['students'], 'Student Performance', $currentPath) ?>
      <?= navItem('/admin/students.php', $icons['students'], 'Students List', $currentPath) ?>
      <?= navItem('/admin/subjects.php', $icons['subjects'], 'Subjects', $currentPath) ?>
      <?= navItem('/hod/submissions.php', $icons['messages'], 'Coordinators Submissions', $currentPath, $hodSubCount) ?>
    </div>
    <div class="nav-section">
      <div class="nav-section-title">Activities & Reports</div>
      <?= navItem('/faculty/activities.php', $icons['activities'], 'Activities', $currentPath) ?>
      <?= navItem('/reports/cumulative_report.php', $icons['reports'], 'Cumulative Report', $currentPath) ?>
      <?= navItem('/hod/reports.php', $icons['reports'], 'Reports Center', $currentPath) ?>
      <?= navItem('/hod/notifications.php', $icons['notifications'], 'Notifications', $currentPath) ?>
    </div>
    
    <?php elseif ($userRole === 'faculty'): ?>
    <!-- Faculty Menu -->
    <div class="nav-section">
      <div class="nav-section-title">Teaching</div>
      <?= navItem('/faculty/activities.php', $icons['activities'], 'Activities', $currentPath) ?>
      <?= navItem('/faculty/marks.php', $icons['marks'], 'Marks Entry', $currentPath) ?>
    </div>
    <div class="nav-section">
      <div class="nav-section-title">Reports</div>
      <?= navItem('/reports/student_report.php', $icons['student_report'], 'Student Report', $currentPath) ?>
      <?= navItem('/reports/subject_report.php', $icons['subject_report'], 'Subject Report', $currentPath) ?>
      <?= navItem('/reports/cumulative_report.php', $icons['reports'], 'Cumulative Report', $currentPath) ?>
    </div>
    
    <?php elseif ($userRole === 'coordinator'): ?>
    <!-- Class Coordinator Menu -->
    <div class="nav-section">
      <div class="nav-section-title">Class Coordinator</div>
      <?= navItem('/coordinator/class_performance.php', $icons['progress'], 'Class Performance', $currentPath) ?>
      <?= navItem('/coordinator/student_progress.php', $icons['students'], 'Student Progress', $currentPath) ?>
      <?= navItem('/coordinator/reports.php', $icons['reports'], 'Reports', $currentPath) ?>
      <?= navItem('/coordinator/messages.php', $icons['messages'], 'HOD Messages', $currentPath, $unreadHODMsgCount) ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </nav>
</aside>
<div class="sidebar-overlay" id="sidebar-overlay" onclick="Sidebar.close()"></div>
