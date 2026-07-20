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
          <button class="sidebar-toggle" onclick="Sidebar.toggle()" title="Toggle Sidebar">☰</button>
          <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" id="global-search" placeholder="Search anything..." autocomplete="off">
          </div>
        </div>
        
        <div class="header-right">
          <!-- Dark Mode Toggle -->
          <button class="notification-bell" onclick="toggleTheme()" title="Toggle Dark Mode" style="margin-right: 5px;">
            🌓
          </button>
          
          <!-- Language Selection -->
          <div class="language-select" style="margin-right: 15px; display: flex; align-items: center; gap: 5px;">
            <span style="font-size: 1.2rem;">🌐</span>
            <select class="form-control" style="padding: 4px 8px; font-size: 0.85rem; height: auto;" onchange="alert('Language changed to ' + this.value)">
              <option value="English">English</option>
              <option value="Marathi">मराठी (Marathi)</option>
            </select>
          </div>

          <!-- Notifications -->
          <div style="position:relative">
            <button class="notification-bell" onclick="Notifications.toggle()" title="Notifications">
              🔔
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
              <div class="user-avatar"><?= $initials ?></div>
              <div class="user-meta">
                <div class="name"><?= sanitize($user['name'] ?? '') ?></div>
                <div class="role"><?= roleLabel($user['role'] ?? '') ?></div>
              </div>
              <span style="font-size:12px;color:var(--text-muted)">▼</span>
            </button>
            
            <div class="user-dropdown" id="user-dropdown">
              <a href="/dashboard.php">📊 Dashboard</a>
              <button class="logout-btn" onclick="openChangePasswordModal()" style="color:var(--text-dark); margin-bottom:5px;">🔑 Change Password</button>
              <div class="divider"></div>
              <button class="logout-btn" onclick="window.location.href='/logout.php'">🚪 Logout</button>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Main Content Area -->
      <main class="main-content">
