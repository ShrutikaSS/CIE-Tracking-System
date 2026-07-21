<!-- Faculty Dashboard View -->
<div class="page-header">
  <div>
    <h1>Faculty Dashboard</h1>
    <div class="breadcrumb">Welcome, <?= sanitize($user['name']) ?> 👋</div>
  </div>
  <div class="actions">
    <a href="/faculty/activities.php" class="btn btn-primary">+ New Activity</a>
  </div>
</div>

<div class="stats-grid stagger">
  <div class="stat-card">
    <div class="stat-info">
      <h4>My Subjects</h4>
      <div class="stat-value" id="stat-subjects">0</div>
    </div>
    <div class="stat-icon blue">📚</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Active Activities</h4>
      <div class="stat-value" id="stat-active">0</div>
    </div>
    <div class="stat-icon green">📝</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Total Activities</h4>
      <div class="stat-value" id="stat-total">0</div>
    </div>
    <div class="stat-icon purple">📋</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Pending Marks</h4>
      <div class="stat-value" id="stat-pending">0</div>
      <span class="stat-change negative">⚠ Needs entry</span>
    </div>
    <div class="stat-icon orange">✏️</div>
  </div>
</div>

<!-- Assigned Subjects -->
<div class="card mb-3">
  <div class="card-header"><h3>📚 Assigned Subjects</h3></div>
  <div class="card-body">
    <div class="subject-cards" id="subject-list">
      <div class="empty-state"><div class="spinner"></div></div>
    </div>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <div class="card-header"><h3>📈 Marks Trend</h3></div>
    <div class="card-body">
      <div class="chart-container"><canvas id="chart-marks-trend"></canvas></div>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><h3>⚡ Quick Actions</h3></div>
    <div class="card-body">
      <div style="display:flex;flex-direction:column;gap:12px;">
        <a href="/faculty/activities.php" class="btn btn-secondary w-100" style="justify-content:flex-start">📝 Manage Activities</a>
        <a href="/faculty/marks.php" class="btn btn-secondary w-100" style="justify-content:flex-start">✏️ Enter Marks</a>
        <a href="/reports/subject_report.php" class="btn btn-secondary w-100" style="justify-content:flex-start">📈 View Reports</a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  const res = await API.get('/api/dashboard.php?action=stats');
  if (res && res.success) {
    const s = res.stats;
    animateCounter(document.getElementById('stat-subjects'), parseInt(s.subjects));
    animateCounter(document.getElementById('stat-active'), parseInt(s.active_activities));
    animateCounter(document.getElementById('stat-total'), parseInt(s.total_activities));
    animateCounter(document.getElementById('stat-pending'), parseInt(s.pending_marks));
  }

  // Load subjects
  const subRes = await API.get('/api/subjects.php?for_faculty=1');
  if (subRes && subRes.success) {
    const container = document.getElementById('subject-list');
    if (subRes.subjects.length === 0) {
      container.innerHTML = '<div class="empty-state"><div class="icon">📚</div><h3>No subjects assigned</h3></div>';
    } else {
      container.innerHTML = subRes.subjects.map(s => `
        <div class="subject-card" onclick="window.location.href='/faculty/activities.php?subject=${s.id}'">
          <div class="code">${s.code}</div>
          <div class="name">${s.name}</div>
          <div class="meta">
            <span>📖 Sem ${s.semester}</span>
            <span>🎓 ${s.student_count} students</span>
            <span>📝 ${s.activity_count} activities</span>
          </div>
        </div>
      `).join('');
    }
  }

  // Marks trend
  const trendRes = await API.get('/api/dashboard.php?action=chart&type=marks_trend');
  if (trendRes && trendRes.success && trendRes.chart.length) {
    Charts.line('chart-marks-trend',
      trendRes.chart.map(d => d.label),
      [{
        label: 'Avg Performance %',
        data: trendRes.chart.map(d => parseFloat(d.value)),
        borderColor: '#4f46e5',
        backgroundColor: 'rgba(79, 70, 229, 0.1)',
        fill: true
      }]
    );
  }
});
</script>
