<!-- Coordinator Dashboard View -->
<?php
$deptId = (int)($_SESSION['department_id'] ?? 0);
$userId = (int)($_SESSION['user_id'] ?? 0);

// Fetch stats
$totalStudents = dbFetchOne("SELECT COUNT(*) as cnt FROM students WHERE department_id = ?", 'i', [$deptId])['cnt'];
$totalSubjects = dbFetchOne("SELECT COUNT(*) as cnt FROM subjects WHERE department_id = ? AND is_active = 1", 'i', [$deptId])['cnt'];
$totalActivities = dbFetchOne("SELECT COUNT(*) as cnt FROM activities a JOIN subjects s ON a.subject_id = s.id WHERE s.department_id = ?", 'i', [$deptId])['cnt'];
$completedActivities = dbFetchOne("SELECT COUNT(*) as cnt FROM activities a JOIN subjects s ON a.subject_id = s.id WHERE s.department_id = ? AND a.status = 'completed'", 'i', [$deptId])['cnt'];
$pendingActivities = dbFetchOne("SELECT COUNT(*) as cnt FROM activities a JOIN subjects s ON a.subject_id = s.id WHERE s.department_id = ? AND a.status = 'active'", 'i', [$deptId])['cnt'];

// Class Average
$avgClassPctResult = dbFetchOne("
    SELECT ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as avg_pct 
    FROM marks m 
    JOIN activities a ON m.activity_id = a.id 
    JOIN subjects s ON a.subject_id = s.id 
    WHERE s.department_id = ? AND m.is_published = 1", 'i', [$deptId]);
$avgClassPct = $avgClassPctResult ? ($avgClassPctResult['avg_pct'] ?? 0) : 0;
$avgClassPct = min(100.0, max(0.0, (float)$avgClassPct));

// Fetch unread HOD messages
$unreadHODMessages = dbFetchAll(
    "SELECT m.*, u.name as sender_name FROM hod_messages m 
     JOIN users u ON u.id = m.sender_id 
     WHERE m.department_id = ? AND (m.recipient_id = ? OR m.recipient_id IS NULL) AND m.is_read = 0 
     ORDER BY m.created_at DESC LIMIT 3",
    'ii', [$deptId, $userId]
);

// Fetch recent activity logs from database
$dbRecentActivities = dbFetchAll("
    SELECT a.name as act_name, s.code as sub_code, u.name as fac_name, a.created_at
    FROM activities a
    JOIN subjects s ON a.subject_id = s.id
    JOIN faculty f ON s.faculty_id = f.id
    JOIN users u ON f.user_id = u.id
    WHERE s.department_id = ?
    ORDER BY a.created_at DESC LIMIT 5", 'i', [$deptId]
);

// Fetch recent notifications
$dbNotifications = dbFetchAll("
    SELECT title, is_read, created_at FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC LIMIT 5", 'i', [$userId]
);

// Fetch upcoming deadlines
$dbDeadlines = dbFetchAll("
    SELECT a.name as act_name, s.code as sub_code, a.deadline 
    FROM activities a
    JOIN subjects s ON a.subject_id = s.id
    WHERE s.department_id = ? AND a.deadline >= CURDATE() AND a.status = 'active'
    ORDER BY a.deadline ASC LIMIT 4", 'i', [$deptId]
);
?>

<div class="page-header">
  <div>
    <h1>Class Coordinator Dashboard</h1>
    <div class="breadcrumb">Welcome, <?= sanitize($user['name']) ?> 👋 (Class Coordinator)</div>
  </div>
</div>

<?php if (!empty($unreadHODMessages)): ?>
<div class="card mb-3" style="border-left: 4px solid var(--danger); background: var(--danger-light); border-top: none; box-shadow: var(--shadow-sm);">
  <div class="card-body" style="padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-wrap: wrap;">
    <div style="display: flex; align-items: center; gap: 12px;">
      <span style="font-size: 1.5rem;">📩</span>
      <div>
        <strong style="color: var(--danger);">Messages from HOD</strong>
        <div class="fs-sm text-secondary" style="margin-top: 2px;">
          You have <?= count($unreadHODMessages) ?> unread HOD message(s). Latest: "<strong><?= sanitize($unreadHODMessages[0]['subject']) ?></strong>"
        </div>
      </div>
    </div>
    <a href="/coordinator/messages.php" class="btn btn-sm btn-danger" style="background: var(--danger); border-radius: 4px; color: white;">View Messages</a>
  </div>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="stats-grid stagger mb-3" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
  <div class="stat-card">
    <div class="stat-info">
      <h4>Total Students</h4>
      <div class="stat-value" id="stat-students">0</div>
      <span class="stat-change positive">📈 Active</span>
    </div>
    <div class="stat-icon blue">🎓</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Total Subjects</h4>
      <div class="stat-value" id="stat-subjects">0</div>
      <span class="stat-change positive">📚 Curricular</span>
    </div>
    <div class="stat-icon green">📚</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Total Activities</h4>
      <div class="stat-value" id="stat-activities">0</div>
      <span class="stat-change positive">📋 Planned</span>
    </div>
    <div class="stat-icon purple">📋</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Completed Activities</h4>
      <div class="stat-value" id="stat-completed">0</div>
      <span class="stat-change positive">✅ Evaluated</span>
    </div>
    <div class="stat-icon green">✅</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Pending Activities</h4>
      <div class="stat-value" id="stat-pending">0</div>
      <span class="stat-change negative" style="color:var(--danger)">⏳ In Progress</span>
    </div>
    <div class="stat-icon orange">⏳</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Avg Class %</h4>
      <div class="stat-value" id="stat-avg-percentage">0<span style="font-size:0.875rem">%</span></div>
      <span class="stat-change positive">📈 Good</span>
    </div>
    <div class="stat-icon indigo">📊</div>
  </div>
</div>

<!-- Dashboard Grid Section -->
<div class="grid-2 mb-3">
  <!-- Class Performance Trend -->
  <div class="card">
    <div class="card-header">
      <h3>📈 Class Performance Trend (Average Marks %)</h3>
    </div>
    <div class="card-body">
      <div class="chart-container" style="position: relative; height: 300px;">
        <canvas id="chart-class-performance"></canvas>
      </div>
    </div>
  </div>

  <!-- Recent Activities -->
  <div class="card">
    <div class="card-header">
      <h3>⚡ Recent Activities</h3>
    </div>
    <div class="card-body" style="padding: 0;">
      <div class="list-group" id="recent-activities-list" style="max-height: 300px; overflow-y: auto;">
        <!-- Loaded via PHP -->
        <?php if (empty($dbRecentActivities)): ?>
          <div class="p-3 text-muted">No recent activities found.</div>
        <?php else: ?>
          <?php foreach ($dbRecentActivities as $act): ?>
            <div class="list-group-item">
              <span class="item-desc">New Activity <strong><?= sanitize($act['act_name']) ?> (<?= sanitize($act['sub_code']) ?>)</strong> was created by <?= sanitize($act['fac_name']) ?>.</span>
              <span class="item-meta"><?= formatDate($act['created_at'], 'M d, H:i') ?></span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="grid-2">
  <!-- Recent Notifications -->
  <div class="card">
    <div class="card-header">
      <h3>🔔 Recent Notifications</h3>
    </div>
    <div class="card-body" style="padding: 0;">
      <div class="list-group" id="recent-notifications-list" style="max-height: 300px; overflow-y: auto;">
        <!-- Loaded via PHP -->
        <?php if (empty($dbNotifications)): ?>
          <div class="p-3 text-muted">No recent notifications.</div>
        <?php else: ?>
          <?php foreach ($dbNotifications as $notif): ?>
            <div class="list-group-item" style="cursor: pointer;" onclick="window.location.href='/coordinator/notifications.php'">
              <span class="item-desc" style="<?= $notif['is_read'] ? '' : 'font-weight: 600;' ?>"><?= sanitize($notif['title']) ?></span>
              <div style="display: flex; align-items: center; gap: 8px;">
                <span class="badge <?= $notif['is_read'] ? 'badge-read' : 'badge-unread' ?>"><?= $notif['is_read'] ? 'READ' : 'UNREAD' ?></span>
                <span class="item-meta"><?= formatDate($notif['created_at'], 'M d') ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Upcoming Deadlines -->
  <div class="card">
    <div class="card-header">
      <h3>⏳ Upcoming Deadlines</h3>
    </div>
    <div class="card-body" style="padding: 0;">
      <div class="list-group" id="upcoming-deadlines-list" style="max-height: 300px; overflow-y: auto;">
        <!-- Loaded via PHP -->
        <?php if (empty($dbDeadlines)): ?>
          <div class="p-3 text-muted">No upcoming deadlines.</div>
        <?php else: ?>
          <?php foreach ($dbDeadlines as $dl): ?>
            <?php 
              $diff = strtotime($dl['deadline']) - time();
              $type = 'warning';
              if ($diff < 86400 * 2) $type = 'danger';
            ?>
            <div class="list-group-item">
              <span class="item-desc" style="font-weight: 500;"><?= sanitize($dl['act_name']) ?> (<?= sanitize($dl['sub_code']) ?>)</span>
              <div style="display: flex; align-items: center; gap: 8px;">
                <span class="badge badge-<?= $type ?>"><?= formatDate($dl['deadline'], 'M d, Y') ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
/* Clean style updates for list groups */
.list-group {
  display: flex;
  flex-direction: column;
}
.list-group-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 20px;
  border-bottom: 1px solid var(--border-color);
  font-size: 0.9rem;
}
.list-group-item:last-child {
  border-bottom: none;
}
.list-group-item .item-desc {
  flex-grow: 1;
  color: var(--text-primary);
}
.list-group-item .item-meta {
  font-size: 0.8rem;
  color: var(--text-muted);
  white-space: nowrap;
  margin-left: 15px;
}
.badge {
  padding: 4px 8px;
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  font-weight: 600;
}
.badge-unread {
  background-color: var(--primary-lighter);
  color: var(--primary);
}
.badge-read {
  background-color: var(--bg-hover);
  color: var(--text-muted);
}
.badge-danger {
  background-color: var(--danger-light);
  color: var(--danger);
}
.badge-warning {
  background-color: var(--warning-light);
  color: var(--warning);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // 1. Animate Statistics Counters (using live DB stats)
  animateCounter(document.getElementById('stat-students'), <?= $totalStudents ?>);
  animateCounter(document.getElementById('stat-subjects'), <?= $totalSubjects ?>);
  animateCounter(document.getElementById('stat-activities'), <?= $totalActivities ?>);
  animateCounter(document.getElementById('stat-completed'), <?= $completedActivities ?>);
  animateCounter(document.getElementById('stat-pending'), <?= $pendingActivities ?>);
  animateCounter(document.getElementById('stat-avg-percentage'), <?= $avgClassPct ?>);

  // 2. Load Class Performance Chart
  const ctx = document.getElementById('chart-class-performance');
  if (ctx) {
    Charts.line('chart-class-performance', 
      ['Test 1', 'Quiz 1', 'Assignment 1', 'Test 2', 'Quiz 2', 'Assignment 2'],
      [{
        label: 'Class Avg %',
        data: [72, 79, 83, 75, 81, 85],
        borderColor: '#0F4C81',
        backgroundColor: 'rgba(15, 76, 129, 0.1)',
        fill: true,
        pointBackgroundColor: '#0F4C81',
        pointRadius: 4,
        tension: 0.3
      }]
    );
  }
});
</script>
