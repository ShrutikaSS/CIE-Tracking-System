<!-- Coordinator Dashboard View -->
<div class="page-header">
  <div>
    <h1>Class Coordinator Dashboard</h1>
    <div class="breadcrumb">Welcome, <?= sanitize($user['name']) ?> 👋 (Class Coordinator - TE-CSE-A)</div>
  </div>
</div>

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
        <!-- Mock Data Loaded via JS -->
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
        <!-- Mock Data Loaded via JS -->
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
        <!-- Mock Data Loaded via JS -->
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
  // 1. Animate Statistics Counters (using Mock Data)
  animateCounter(document.getElementById('stat-students'), 60);
  animateCounter(document.getElementById('stat-subjects'), 6);
  animateCounter(document.getElementById('stat-activities'), 18);
  animateCounter(document.getElementById('stat-completed'), 12);
  animateCounter(document.getElementById('stat-pending'), 6);
  animateCounter(document.getElementById('stat-avg-percentage'), 78);

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

  // 3. Load Recent Activities Mock Data
  const recentActivities = [
    { desc: 'Marks updated for <strong>Test 2 (Database Systems)</strong> by Prof. Anil Mehta', time: '10 mins ago' },
    { desc: 'New Activity <strong>Assignment 3 (Data Structures)</strong> was created', time: '2 hours ago' },
    { desc: 'viva-voce activity marks published for <strong>Third Year A</strong>', time: '1 day ago' },
    { desc: 'Student <strong>Rahul Verma (Roll 45)</strong> profile updated', time: '2 days ago' },
    { desc: 'Marks updated for <strong>Quiz 2 (Web Dev)</strong> by Prof. Sneha Patil', time: '3 days ago' }
  ];
  document.getElementById('recent-activities-list').innerHTML = recentActivities.map(act => `
    <div class="list-group-item">
      <span class="item-desc">${act.desc}</span>
      <span class="item-meta">${act.time}</span>
    </div>
  `).join('');

  // 4. Load Recent Notifications Mock Data
  const recentNotifications = [
    { title: 'Urgent: Mid-Term marks submission deadline approaching', status: 'unread', time: 'Today' },
    { title: 'Class attendance reports for June 2026 published', status: 'unread', time: 'Yesterday' },
    { title: 'Curriculum update: Lab schedule modified for Term II', status: 'read', time: '3 days ago' },
    { title: 'Notifications: HOD meeting scheduled for Friday', status: 'read', time: '4 days ago' }
  ];
  document.getElementById('recent-notifications-list').innerHTML = recentNotifications.map(notif => `
    <div class="list-group-item" style="cursor: pointer;" onclick="window.location.href='/coordinator/notifications.php'">
      <span class="item-desc" style="${notif.status === 'unread' ? 'font-weight: 600;' : ''}">${notif.title}</span>
      <div style="display: flex; align-items: center; gap: 8px;">
        <span class="badge ${notif.status === 'unread' ? 'badge-unread' : 'badge-read'}">${notif.status.toUpperCase()}</span>
        <span class="item-meta">${notif.time}</span>
      </div>
    </div>
  `).join('');

  // 5. Load Upcoming Deadlines Mock Data
  const upcomingDeadlines = [
    { activity: 'Assignment 3 (Software Eng.)', date: '25 Jul 2026', type: 'Danger' },
    { activity: 'Viva Voce (Networks Lab)', date: '28 Jul 2026', type: 'Warning' },
    { activity: 'Quiz 3 (Cloud Computing)', date: '02 Aug 2026', type: 'Warning' },
    { activity: 'Project Review Phase-I', date: '10 Aug 2026', type: 'Warning' }
  ];
  document.getElementById('upcoming-deadlines-list').innerHTML = upcomingDeadlines.map(d => `
    <div class="list-group-item">
      <span class="item-desc" style="font-weight: 500;">${d.activity}</span>
      <div style="display: flex; align-items: center; gap: 8px;">
        <span class="badge badge-${d.type.toLowerCase()}">${d.date}</span>
      </div>
    </div>
  `).join('');
});
</script>
