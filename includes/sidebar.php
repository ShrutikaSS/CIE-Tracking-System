<?php
/**
 * Sidebar Navigation
 * Role-based menu items
 */
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$userRole = $_SESSION['user_role'] ?? '';

function navItem($href, $icon, $label, $currentPath, $count = null) {
    $active = strpos($currentPath, $href) !== false ? 'active' : '';
    $badge = $count !== null ? "<span class=\"nav-count\">$count</span>" : '';
    return "<a href=\"$href\" class=\"nav-item $active\" data-title=\"$label\">
              <span class=\"nav-icon\">$icon</span>
              <span class=\"nav-label\">$label</span>
              $badge
            </a>";
}
?>

<aside class="app-sidebar">
  <!-- Logo -->
  <div class="sidebar-logo">
    <div class="logo-icon">📊</div>
    <span class="logo-text">CIE Tracker</span>
  </div>
  
  <!-- Navigation -->
  <nav class="sidebar-nav">
    <!-- Main -->
    <div class="nav-section">
      <div class="nav-section-title">Main</div>
      <?= navItem('/dashboard.php', '🏠', 'Dashboard', $currentPath) ?>
    </div>
    
    <?php if ($userRole === 'admin'): ?>
    <!-- Admin Menu -->
    <div class="nav-section">
      <div class="nav-section-title">Management</div>
      <?= navItem('/admin/departments.php', '🏢', 'Departments', $currentPath) ?>
      <?= navItem('/admin/faculty.php', '👨‍🏫', 'Faculty', $currentPath) ?>
      <?= navItem('/admin/students.php', '🎓', 'Students', $currentPath) ?>
      <?= navItem('/admin/subjects.php', '📚', 'Subjects', $currentPath) ?>
    </div>
    <div class="nav-section">
      <div class="nav-section-title">Activities</div>
      <?= navItem('/faculty/activities.php', '📝', 'Activities', $currentPath) ?>
      <?= navItem('/faculty/marks.php', '✏️', 'Marks Entry', $currentPath) ?>
    </div>
    <div class="nav-section">
      <div class="nav-section-title">Reports</div>
      <?= navItem('/reports/student_report.php', '📄', 'Student Report', $currentPath) ?>
      <?= navItem('/reports/subject_report.php', '📈', 'Subject Report', $currentPath) ?>
    </div>
    
    <?php elseif ($userRole === 'hod'): ?>
    <!-- HOD Menu -->
    <div class="nav-section">
      <div class="nav-section-title">Department</div>
      <?= navItem('/admin/faculty.php', '👨‍🏫', 'Faculty', $currentPath) ?>
      <?= navItem('/admin/students.php', '🎓', 'Students', $currentPath) ?>
      <?= navItem('/admin/subjects.php', '📚', 'Subjects', $currentPath) ?>
    </div>
    <div class="nav-section">
      <div class="nav-section-title">Activities</div>
      <?= navItem('/faculty/activities.php', '📝', 'Activities', $currentPath) ?>
      <?= navItem('/faculty/marks.php', '✏️', 'Marks Entry', $currentPath) ?>
    </div>
    <div class="nav-section">
      <div class="nav-section-title">Reports</div>
      <?= navItem('/reports/student_report.php', '📄', 'Student Report', $currentPath) ?>
      <?= navItem('/reports/subject_report.php', '📈', 'Subject Report', $currentPath) ?>
    </div>
    
    <?php elseif ($userRole === 'faculty' || $userRole === 'coordinator'): ?>
    <!-- Faculty / Coordinator Menu -->
    <div class="nav-section">
      <div class="nav-section-title">Teaching</div>
      <?= navItem('/faculty/activities.php', '📝', 'Activities', $currentPath) ?>
      <?= navItem('/faculty/marks.php', '✏️', 'Marks Entry', $currentPath) ?>
    </div>
    <div class="nav-section">
      <div class="nav-section-title">Reports</div>
      <?= navItem('/reports/student_report.php', '📄', 'Student Report', $currentPath) ?>
      <?= navItem('/reports/subject_report.php', '📈', 'Subject Report', $currentPath) ?>
    </div>
    
    <?php elseif ($userRole === 'student'): ?>
    <!-- Student Menu -->
    <div class="nav-section">
      <div class="nav-section-title">Academics</div>
      <?= navItem('/student/marks.php', '📊', 'My Marks', $currentPath) ?>
      <?= navItem('/student/performance.php', '📈', 'Performance', $currentPath) ?>
    </div>
    <?php endif; ?>
  </nav>
</aside>
