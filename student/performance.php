<?php
$pageTitle = 'Performance';
require_once __DIR__ . '/../includes/header.php';
requireRole(['student']);
?>

<div class="page-header">
  <div>
    <h1>My Performance</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Performance</div>
  </div>
</div>

<div class="stats-grid stagger mb-3">
  <div class="stat-card">
    <div class="stat-info">
      <h4>Overall Average</h4>
      <div class="stat-value" id="stat-avg">0<span style="font-size:0.875rem">%</span></div>
    </div>
    <div class="stat-icon indigo">📊</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Activities Completed</h4>
      <div class="stat-value" id="stat-completed">0</div>
    </div>
    <div class="stat-icon green">✅</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Subjects Enrolled</h4>
      <div class="stat-value" id="stat-subjects">0</div>
    </div>
    <div class="stat-icon purple">📚</div>
  </div>
</div>

<div class="grid-2 mb-3">
  <div class="card">
    <div class="card-header"><h3>📊 Subject Performance</h3></div>
    <div class="card-body">
      <div class="chart-container"><canvas id="chart-radar"></canvas></div>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><h3>📈 Marks Trend</h3></div>
    <div class="card-body">
      <div class="chart-container"><canvas id="chart-trend"></canvas></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3>🎯 Activity Type Performance</h3></div>
  <div class="card-body">
    <div class="chart-container"><canvas id="chart-type-perf"></canvas></div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  // Stats
  const statsRes = await API.get('/api/dashboard.php?action=stats');
  if (statsRes && statsRes.success) {
    animateCounter(document.getElementById('stat-avg'), parseFloat(statsRes.stats.avg_percentage));
    animateCounter(document.getElementById('stat-completed'), parseInt(statsRes.stats.completed));
    animateCounter(document.getElementById('stat-subjects'), parseInt(statsRes.stats.subjects));
  }

  // Subject Performance Radar
  const perfRes = await API.get('/api/dashboard.php?action=chart&type=subject_performance');
  if (perfRes && perfRes.success && perfRes.chart.length) {
    Charts.radar('chart-radar',
      perfRes.chart.map(d => d.label),
      [{
        label: 'Performance %',
        data: perfRes.chart.map(d => parseFloat(d.value)),
        backgroundColor: 'rgba(79, 70, 229, 0.2)',
        borderColor: '#4f46e5',
        borderWidth: 2,
        pointBackgroundColor: '#4f46e5',
        pointRadius: 4
      }]
    );
  }

  // Marks Trend
  const trendRes = await API.get('/api/dashboard.php?action=chart&type=marks_trend');
  if (trendRes && trendRes.success && trendRes.chart.length) {
    Charts.line('chart-trend',
      trendRes.chart.map(d => d.label.substring(0, 20)),
      [{
        label: 'Score %',
        data: trendRes.chart.map(d => parseFloat(d.value)),
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        fill: true,
        pointBackgroundColor: '#10b981'
      }]
    );
  }

  // Activity Type Performance
  const marksRes = await API.get('/api/marks.php?action=by_student');
  if (marksRes && marksRes.success && marksRes.marks.length) {
    const typeData = {};
    marksRes.marks.forEach(m => {
      if (!typeData[m.activity_type]) typeData[m.activity_type] = { total: 0, max: 0, count: 0 };
      typeData[m.activity_type].total += parseFloat(m.marks_obtained);
      typeData[m.activity_type].max += parseFloat(m.max_marks);
      typeData[m.activity_type].count++;
    });

    const labels = Object.keys(typeData).map(t => t.charAt(0).toUpperCase() + t.slice(1));
    const values = Object.values(typeData).map(t => t.max > 0 ? ((t.total / t.max) * 100).toFixed(1) : 0);

    Charts.bar('chart-type-perf', labels, [{
      label: 'Avg %',
      data: values.map(Number),
      backgroundColor: ['#818cf8', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6'],
      borderRadius: 8,
      barThickness: 50
    }]);
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
