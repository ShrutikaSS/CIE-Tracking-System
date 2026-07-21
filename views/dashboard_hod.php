<!-- HOD Dashboard View -->
<div class="page-header">
  <div>
    <h1>Department Dashboard</h1>
    <div class="breadcrumb">Head of Department — <?= sanitize($user['name']) ?></div>
  </div>
</div>

<div class="stats-grid stagger">
  <div class="stat-card">
    <div class="stat-info">
      <h4>Dept. Students</h4>
      <div class="stat-value" id="stat-students">0</div>
    </div>
    <div class="stat-icon blue">🎓</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Dept. Faculty</h4>
      <div class="stat-value" id="stat-faculty">0</div>
    </div>
    <div class="stat-icon green">👨‍🏫</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Subjects</h4>
      <div class="stat-value" id="stat-subjects">0</div>
    </div>
    <div class="stat-icon purple">📚</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Activities</h4>
      <div class="stat-value" id="stat-activities">0</div>
    </div>
    <div class="stat-icon orange">📝</div>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <div class="card-header"><h3>📊 Marks Trend</h3></div>
    <div class="card-body">
      <div class="chart-container"><canvas id="chart-marks-trend"></canvas></div>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><h3>⚡ Quick Actions</h3></div>
    <div class="card-body">
      <div style="display:flex;flex-direction:column;gap:12px;">
        <a href="/admin/faculty.php" class="btn btn-secondary w-100" style="justify-content:flex-start">👨‍🏫 Department Faculty</a>
        <a href="/admin/students.php" class="btn btn-secondary w-100" style="justify-content:flex-start">🎓 Department Students</a>
        <a href="/admin/subjects.php" class="btn btn-secondary w-100" style="justify-content:flex-start">📚 Subjects</a>
        <a href="/reports/subject_report.php" class="btn btn-secondary w-100" style="justify-content:flex-start">📈 Subject Reports</a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  const res = await API.get('/api/dashboard.php?action=stats');
  if (res && res.success) {
    const s = res.stats;
    animateCounter(document.getElementById('stat-students'), parseInt(s.students));
    animateCounter(document.getElementById('stat-faculty'), parseInt(s.faculty));
    animateCounter(document.getElementById('stat-subjects'), parseInt(s.subjects));
    animateCounter(document.getElementById('stat-activities'), parseInt(s.activities));
  }

  const trendRes = await API.get('/api/dashboard.php?action=chart&type=marks_trend');
  if (trendRes && trendRes.success && trendRes.chart.length) {
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
});
</script>
