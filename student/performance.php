<?php
$pageTitle = 'Academic Progress';
require_once __DIR__ . '/../includes/header.php';
requireRole(['student']);
?>

<div class="page-header">
  <div>
    <h1>Academic Progress</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Academic Progress</div>
  </div>
</div>

<div class="stats-grid stagger mb-3" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
  <div class="stat-card">
    <div class="stat-info">
      <h4>Overall Performance</h4>
      <div class="stat-value" id="stat-avg">0<span style="font-size:0.875rem">%</span></div>
    </div>
    <div class="stat-icon indigo" style="display:flex; align-items:center; justify-content:center;">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Semester Average</h4>
      <div class="stat-value" id="stat-sem-avg">0<span style="font-size:0.875rem">%</span></div>
    </div>
    <div class="stat-icon green" style="display:flex; align-items:center; justify-content:center;">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Class Rank</h4>
      <div class="stat-value" id="stat-rank">#0<span style="font-size:0.875rem">/ 0</span></div>
    </div>
    <div class="stat-icon orange" style="display:flex; align-items:center; justify-content:center;">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Subjects Enrolled</h4>
      <div class="stat-value" id="stat-subjects">0</div>
    </div>
    <div class="stat-icon purple" style="display:flex; align-items:center; justify-content:center;">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
    </div>
  </div>
</div>

<div class="grid-2 mb-3">
  <div class="card">
    <div class="card-header">
      <h3 style="display:flex; align-items:center; gap:8px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary);"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
        Subject Performance (Subject-wise %)
      </h3>
    </div>
    <div class="card-body">
      <div class="chart-container"><canvas id="chart-radar"></canvas></div>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h3 style="display:flex; align-items:center; gap:8px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--success);"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
        Marks Trend
      </h3>
    </div>
    <div class="card-body">
      <div class="chart-container"><canvas id="chart-trend"></canvas></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h3 style="display:flex; align-items:center; gap:8px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--warning);"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>
      Activity Type Performance
    </h3>
  </div>
  <div class="card-body">
    <div class="chart-container"><canvas id="chart-type-perf"></canvas></div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  // Stats
  const statsRes = await API.get('/api/dashboard.php?action=stats');
  if (statsRes && statsRes.success) {
    const s = statsRes.stats;
    animateCounter(document.getElementById('stat-avg'), parseFloat(s.avg_percentage));
    animateCounter(document.getElementById('stat-sem-avg'), parseFloat(s.avg_percentage));
    
    // Class Rank
    document.getElementById('stat-rank').innerHTML = `#${s.rank}<span style="font-size:0.875rem">/ ${s.total_students}</span>`;
    
    // Let's query enrolled subjects to get the exact count
    const subRes = await API.get('/api/subjects.php');
    if (subRes && subRes.success) {
      document.getElementById('stat-subjects').textContent = subRes.subjects.length;
    }
  }

  // Subject Performance Radar
  const perfRes = await API.get('/api/dashboard.php?action=chart&type=subject_performance');
  if (perfRes && perfRes.success && perfRes.chart.length) {
    Charts.radar('chart-radar',
      perfRes.chart.map(d => d.label),
      [{
        label: 'Performance %',
        data: perfRes.chart.map(d => parseFloat(d.value)),
        backgroundColor: 'rgba(13, 58, 113, 0.2)',
        borderColor: '#0d3a71',
        borderWidth: 2,
        pointBackgroundColor: '#0d3a71',
        pointRadius: 4
      }]
    );
  } else {
    document.getElementById('chart-radar').parentElement.innerHTML = '<div class="empty-state">No chart data available</div>';
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
  } else {
    document.getElementById('chart-trend').parentElement.innerHTML = '<div class="empty-state">No trend data available</div>';
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
  } else {
    document.getElementById('chart-type-perf').parentElement.innerHTML = '<div class="empty-state">No activity performance data available</div>';
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
