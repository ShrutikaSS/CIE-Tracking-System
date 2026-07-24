<?php
/**
 * Interactive Database & System Setup Script
 * CIE Activity Marks Tracking System
 */

$isCli = (php_sapi_name() === 'cli');

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cie_tracking');
define('DB_PORT', 3306);

$steps = [];
$errors = [];
$success = true;

// Step 1: Check PHP Extensions
$requiredExts = ['mysqli', 'json', 'session', 'hash', 'pcre'];
$missingExts = [];
foreach ($requiredExts as $ext) {
    if (!extension_loaded($ext)) {
        $missingExts[] = $ext;
    }
}
if (empty($missingExts)) {
    $steps[] = ["status" => "ok", "msg" => "PHP Extensions (" . implode(', ', $requiredExts) . ") are installed and active."];
} else {
    $steps[] = ["status" => "error", "msg" => "Missing required PHP extension(s): " . implode(', ', $missingExts)];
    $errors[] = "PHP extension missing";
    $success = false;
}

// Step 2: Test MySQL Connection & Create Database
$conn = null;
if ($success) {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, '', DB_PORT);
    if ($conn->connect_error) {
        $conn = @new mysqli('localhost', DB_USER, DB_PASS, '', DB_PORT);
    }

    if ($conn->connect_error) {
        $steps[] = ["status" => "error", "msg" => "Failed to connect to MySQL server on " . DB_HOST . ":" . DB_PORT . " - " . $conn->connect_error];
        $errors[] = "MySQL Connection failed. Make sure MySQL/MariaDB server (e.g. XAMPP/WAMP) is running.";
        $success = false;
    } else {
        $steps[] = ["status" => "ok", "msg" => "Successfully connected to MySQL database server."];
        
        // Create Database
        if ($conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            $steps[] = ["status" => "ok", "msg" => "Database '" . DB_NAME . "' verified/created successfully."];
            $conn->select_db(DB_NAME);
        } else {
            $steps[] = ["status" => "error", "msg" => "Failed to create database '" . DB_NAME . "': " . $conn->error];
            $errors[] = "Database creation failed";
            $success = false;
        }
    }
}

// Step 3: Run Database Schema & Seed Data
if ($success && $conn) {
    $forceReset = isset($_GET['reset']) && $_GET['reset'] == '1';
    $tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
    $hasTables = ($tableCheck && $tableCheck->num_rows > 0);

    if (!$hasTables || $forceReset) {
        $sqlFile = __DIR__ . '/database.sql';
        if (file_exists($sqlFile)) {
            $sqlContent = file_get_contents($sqlFile);
            if ($conn->multi_query($sqlContent)) {
                do {
                    if ($result = $conn->store_result()) {
                        $result->free();
                    }
                } while ($conn->more_results() && $conn->next_result());
                
                if ($conn->error) {
                    $steps[] = ["status" => "error", "msg" => "Error executing database.sql: " . $conn->error];
                } else {
                    $steps[] = ["status" => "ok", "msg" => "Executed database.sql: Schema created and seed data inserted successfully!"];
                }
            } else {
                $steps[] = ["status" => "error", "msg" => "Multi-query execution failed: " . $conn->error];
            }
        } else {
            $steps[] = ["status" => "error", "msg" => "database.sql file not found in project root."];
        }
    } else {
        $steps[] = ["status" => "ok", "msg" => "Database tables already exist. (Use ?reset=1 to re-import database.sql)"];
    }
}

// Step 4: Verify Database Data Stats
$stats = [];
if ($success && $conn) {
    $tables = ['users', 'departments', 'faculty', 'students', 'subjects', 'activities', 'marks', 'notifications'];
    foreach ($tables as $tbl) {
        $res = $conn->query("SELECT COUNT(*) as cnt FROM `$tbl`");
        if ($res) {
            $row = $res->fetch_assoc();
            $stats[$tbl] = $row['cnt'];
        }
    }
    $steps[] = ["status" => "ok", "msg" => "Data verification complete: " . json_encode($stats)];
}

// Render CLI Output
if ($isCli) {
    echo "===============================================\n";
    echo "  CIE Tracking System - Database Setup Status  \n";
    echo "===============================================\n";
    foreach ($steps as $step) {
        $symbol = ($step['status'] === 'ok') ? "[SUCCESS]" : "[ERROR]";
        echo "$symbol " . $step['msg'] . "\n";
    }
    if ($success) {
        echo "\nSetup completed successfully!\n";
        echo "Launch server using: php -S localhost:8000\n";
        echo "Demo login: admin@cie.edu / password123\n";
    } else {
        echo "\nSetup encountered errors. Please check MySQL server status.\n";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Setup & Initialization — CIE Marks Tracking System</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #0d3a71;
      --bg: #f8fafc;
      --card-bg: #ffffff;
      --text: #1e293b;
      --success: #10b981;
      --error: #ef4444;
    }
    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--bg);
      color: var(--text);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
    }
    .setup-card {
      background: var(--card-bg);
      width: 100%;
      max-width: 680px;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
      border-top: 6px solid var(--primary);
    }
    h1 {
      font-size: 1.8rem;
      color: var(--primary);
      margin-top: 0;
      margin-bottom: 8px;
    }
    .subtitle {
      color: #64748b;
      font-size: 0.95rem;
      margin-bottom: 24px;
    }
    .step-list {
      list-style: none;
      padding: 0;
      margin: 0 0 30px 0;
    }
    .step-item {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 12px;
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .step-item.ok {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }
    .step-item.error {
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }
    .badge {
      font-weight: 700;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.8rem;
      text-transform: uppercase;
    }
    .badge.ok { background: var(--success); color: white; }
    .badge.error { background: var(--error); color: white; }
    
    .stats-box {
      background: #f1f5f9;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 24px;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      text-align: center;
      margin-top: 12px;
    }
    .stat-num {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary);
    }
    .stat-lbl {
      font-size: 0.8rem;
      color: #64748b;
      text-transform: uppercase;
    }

    .btn {
      display: inline-block;
      background: var(--primary);
      color: white;
      text-decoration: none;
      padding: 14px 28px;
      border-radius: 6px;
      font-weight: 600;
      transition: background 0.2s;
    }
    .btn:hover { background: #092850; }
    .btn-secondary {
      background: #64748b;
      margin-left: 10px;
    }
    .btn-secondary:hover { background: #475569; }

    .demo-accounts {
      margin-top: 30px;
      border-top: 1px solid #e2e8f0;
      padding-top: 20px;
    }
    .demo-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.9rem;
      margin-top: 10px;
    }
    .demo-table th, .demo-table td {
      padding: 8px 12px;
      border: 1px solid #e2e8f0;
      text-align: left;
    }
    .demo-table th { background: #f8fafc; font-weight: 600; }
  </style>
</head>
<body>

<div class="setup-card">
  <h1>System Setup & Initialization</h1>
  <p class="subtitle">CIE Activity Marks Tracking System — Database Diagnostics & Auto-Installer</p>

  <ul class="step-list">
    <?php foreach ($steps as $st): ?>
      <li class="step-item <?= $st['status'] ?>">
        <span class="badge <?= $st['status'] ?>"><?= strtoupper($st['status']) ?></span>
        <span><?= htmlspecialchars($st['msg']) ?></span>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if (!empty($stats)): ?>
    <div class="stats-box">
      <strong>Database Summary:</strong>
      <div class="stats-grid">
        <div><div class="stat-num"><?= $stats['users'] ?? 0 ?></div><div class="stat-lbl">Users</div></div>
        <div><div class="stat-num"><?= $stats['departments'] ?? 0 ?></div><div class="stat-lbl">Depts</div></div>
        <div><div class="stat-num"><?= $stats['subjects'] ?? 0 ?></div><div class="stat-lbl">Subjects</div></div>
        <div><div class="stat-num"><?= $stats['activities'] ?? 0 ?></div><div class="stat-lbl">Activities</div></div>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div style="margin-top: 20px;">
      <a href="index.php" class="btn">Launch Application Portal &rarr;</a>
      <a href="setup.php?reset=1" class="btn btn-secondary" onclick="return confirm('Re-import database.sql and reset all tables?');">Reset Database</a>
    </div>

    <div class="demo-accounts">
      <strong>Default Demo Accounts (Password: <code>password123</code>):</strong>
      <table class="demo-table">
        <thead>
          <tr>
            <th>Role</th>
            <th>Email</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>Admin</td><td><code>admin@cie.edu</code></td><td>Dr. Rajesh Kumar</td></tr>
          <tr><td>HOD</td><td><code>hod.cse@cie.edu</code></td><td>Dr. Priya Sharma</td></tr>
          <tr><td>Faculty</td><td><code>anil.mehta@cie.edu</code></td><td>Prof. Anil Mehta</td></tr>
          <tr><td>Coordinator</td><td><code>sneha.patil@cie.edu</code></td><td>Prof. Sneha Patil</td></tr>
          <tr><td>Student</td><td><code>rahul.verma@cie.edu</code></td><td>Rahul Verma</td></tr>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div style="background: #fff1f2; color: #9f1239; padding: 16px; border-radius: 8px; font-size: 0.95rem; margin-top: 20px;">
      <strong>Setup Needs Attention:</strong>
      <p style="margin: 8px 0 0 0;">Please ensure MySQL/MariaDB service is running (e.g. start MySQL service in XAMPP / WAMP / MySQL Workbench) and refresh this page.</p>
    </div>
    <div style="margin-top: 20px;">
      <a href="setup.php" class="btn">Retry Setup &rarr;</a>
    </div>
  <?php endif; ?>
</div>

</body>
</html>
