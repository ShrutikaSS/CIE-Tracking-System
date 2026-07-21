<?php
/**
 * Header Template
 * Included by all authenticated pages
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$user = currentUser();
$unreadCount = $user ? getUnreadNotificationCount($user['id']) : 0;
$initials = $user ? strtoupper(substr($user['name'], 0, 1) . substr(strrchr($user['name'], ' ') ?: $user['name'], 1, 1)) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="CIE Activity Marks Tracking System - Track and manage Continuous Internal Evaluation marks efficiently.">
  <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?>CIE Marks Tracker</title>
  
  <!-- Favicon -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">
  
  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
  
  <!-- jsPDF + SheetJS for exports -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.2/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.4/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
  
  <!-- App CSS -->
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <script>
    // Prevent flash of unstyled content by applying theme immediately
    if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.body.classList.add('dark-theme');
    }
    
    function toggleTheme() {
      const isDark = document.body.classList.toggle('dark-theme');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }
  </script>
  <div class="app-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <div class="main-wrapper">
      <!-- Sticky Header -->
      <header class="app-header">
        <div class="header-left">
          <button class="sidebar-toggle" onclick="Sidebar.toggle()" title="Toggle Sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
          </button>
          <div class="search-bar">
            <span class="search-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </span>
            <input type="text" id="global-search" placeholder="Search anything..." autocomplete="off">
          </div>
        </div>
        
        <div class="header-right">
          <!-- Dark Mode Toggle -->
          <button class="notification-bell" onclick="toggleTheme()" title="Toggle Dark Mode" style="margin-right: 5px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
          </button>
          
          <!-- Language Selection -->
          <div class="language-select" style="margin-right: 15px; display: flex; align-items: center; gap: 8px; color: var(--text-secondary);">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
            <select class="form-control" style="padding: 4px 8px; font-size: 0.85rem; height: auto;" onchange="alert('Language changed to ' + this.value)">
              <option value="English">English</option>
              <option value="Marathi">मराठी (Marathi)</option>
            </select>
          </div>

          <!-- Notifications -->
          <div style="position:relative">
            <button class="notification-bell" onclick="Notifications.toggle()" title="Notifications">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
              <span class="notification-badge" id="notification-count" style="<?= $unreadCount > 0 ? '' : 'display:none' ?>"><?= $unreadCount ?></span>
            </button>
            
            <div class="notification-dropdown" id="notification-dropdown">
              <div class="notification-dropdown-header">
                <h4>Notifications</h4>
                <button class="btn btn-sm btn-secondary" onclick="Notifications.markAllRead()">Mark all read</button>
              </div>
              <div class="notification-list" id="notification-list">
                <div class="notification-empty">Loading...</div>
              </div>
            </div>
          </div>
          
          <!-- User Menu -->
          <div class="user-menu">
            <button class="user-menu-trigger" onclick="UserMenu.toggle()">
              <div class="user-avatar" style="padding: 0; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                <?php if (!empty($user['avatar'])): ?>
                  <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                <?php else: ?>
                  <?= $initials ?>
                <?php endif; ?>
              </div>
              <div class="user-meta">
                <div class="name"><?= sanitize($user['name'] ?? '') ?></div>
                <div class="role"><?= roleLabel($user['role'] ?? '') ?></div>
              </div>
              <span style="display:flex; align-items:center; color:var(--text-muted)">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
              </span>
            </button>
            
            <div class="user-dropdown" id="user-dropdown">
              <a href="/dashboard.php" style="display:flex; align-items:center; gap:8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Dashboard
              </a>
              <?php if ($_SESSION['user_role'] === 'student'): ?>
                <a href="/student/profile.php" style="display:flex; align-items:center; gap:8px;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                  Profile
                </a>
              <?php else: ?>
                <button class="logout-btn" onclick="openChangePasswordModal()" style="color:var(--text-dark); margin-bottom:5px; padding: 10px 12px; font-weight: normal; text-transform: none; display: flex; align-items: center; gap: 8px;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                  Change Password
                </button>
              <?php endif; ?>
              <div class="divider"></div>
              <button class="logout-btn" onclick="window.location.href='/logout.php'" style="display:flex; align-items:center; gap:8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--danger);"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Logout
              </button>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Main Content Area -->
      <main class="main-content">
