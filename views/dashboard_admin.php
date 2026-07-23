<!-- Admin Dashboard View -->
<div class="page-header">
  <div>
    <h1>Admin Dashboard</h1>
    <div class="breadcrumb">Welcome back, <?= sanitize($user['name']) ?> 👋</div>
  </div>
  
</div>

<!-- Stats Cards -->
<div class="stats-grid stagger" id="admin-stats">
  <div class="stat-card">
    <div class="stat-info">
      <h4>Total Students</h4>
      <div class="stat-value" data-count="0" id="stat-students">0</div>
      <span class="stat-change positive">📈 Active</span>
    </div>
    <div class="stat-icon blue">🎓</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Total Faculty</h4>
      <div class="stat-value" data-count="0" id="stat-faculty">0</div>
      <span class="stat-change positive">👨‍🏫 Members</span>
    </div>
    <div class="stat-icon green">👨‍🏫</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Total Subjects</h4>
      <div class="stat-value" data-count="0" id="stat-subjects">0</div>
      <span class="stat-change positive">📚 Active</span>
    </div>
    <div class="stat-icon purple">📚</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Total Activities</h4>
      <div class="stat-value" data-count="0" id="stat-activities">0</div>
      <span class="stat-change positive">📝 Created</span>
    </div>
    <div class="stat-icon orange">📝</div>
  </div>
</div>

<!-- Charts Row -->
<div class="grid-2 mb-3">
  <div class="card">
    <div class="card-header">
      <h3>📊 Activities by Department</h3>
    </div>
    <div class="card-body">
      <div class="chart-container">
        <canvas id="chart-activities-dept"></canvas>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h3>📈 Average Marks Trend</h3>
    </div>
    <div class="card-body">
      <div class="chart-container">
        <canvas id="chart-marks-trend"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Activity Type Distribution -->
<div class="grid-2">
  <div class="card">
    <div class="card-header">
      <h3>🎯 Activity Type Distribution</h3>
    </div>
    <div class="card-body">
      <div class="chart-container">
        <canvas id="chart-type-dist"></canvas>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h3>⚡ Quick Actions</h3>
    </div>
    <div class="card-body">
      <div style="display:flex;flex-direction:column;gap:12px;">
        <a href="<?= url('/admin/departments.php') ?>" class="btn btn-secondary w-100" style="justify-content:flex-start">🏢 Manage Departments</a>
        <a href="<?= url('/admin/faculty.php') ?>" class="btn btn-secondary w-100" style="justify-content:flex-start">👨‍🏫 Manage Faculty</a>
        <a href="<?= url('/admin/students.php') ?>" class="btn btn-secondary w-100" style="justify-content:flex-start">🎓 Manage Students</a>
        <a href="<?= url('/admin/subjects.php') ?>" class="btn btn-secondary w-100" style="justify-content:flex-start">📚 Manage Subjects</a>
        <a href="<?= url('/faculty/activities.php') ?>" class="btn btn-secondary w-100" style="justify-content:flex-start">📝 View Activities</a>
        <a href="<?= url('/reports/student_report.php') ?>" class="btn btn-secondary w-100" style="justify-content:flex-start">📄 Generate Reports</a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  // Load stats
  const statsRes = await API.get('/api/dashboard.php?action=stats');
  if (statsRes && statsRes.success) {
    const s = statsRes.stats;
    animateCounter(document.getElementById('stat-students'), parseInt(s.students));
    animateCounter(document.getElementById('stat-faculty'), parseInt(s.faculty));
    animateCounter(document.getElementById('stat-subjects'), parseInt(s.subjects));
    animateCounter(document.getElementById('stat-activities'), parseInt(s.activities));
  }

  // Activities by Department chart
  const deptRes = await API.get('/api/dashboard.php?action=chart&type=activities_by_dept');
  if (deptRes && deptRes.success) {
    Charts.bar('chart-activities-dept',
      deptRes.chart.map(d => d.label),
      [{
        label: 'Activities',
        data: deptRes.chart.map(d => parseInt(d.value)),
        backgroundColor: ['#818cf8', '#6366f1', '#4f46e5', '#4338ca'],
        borderRadius: 8,
        barThickness: 40
      }]
    );
  }

  // Marks Trend chart
  const trendRes = await API.get('/api/dashboard.php?action=chart&type=marks_trend');
  if (trendRes && trendRes.success) {
    Charts.line('chart-marks-trend',
      trendRes.chart.map(d => d.label),
      [{
        label: 'Avg Performance %',
        data: trendRes.chart.map(d => parseFloat(d.value)),
        borderColor: '#4f46e5',
        backgroundColor: 'rgba(79, 70, 229, 0.1)',
        fill: true,
        pointBackgroundColor: '#4f46e5'
      }]
    );
  }

  // Type Distribution chart
  const typeRes = await API.get('/api/dashboard.php?action=chart&type=activity_type_dist');
  if (typeRes && typeRes.success) {
    Charts.doughnut('chart-type-dist',
      typeRes.chart.map(d => d.label.charAt(0).toUpperCase() + d.label.slice(1)),
      typeRes.chart.map(d => parseInt(d.value)),
      ['#818cf8', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6']
    );
  }
});
</script>
