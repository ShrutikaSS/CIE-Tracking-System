<!-- Student Dashboard View -->
<div class="page-header">
  <div>
    <h1>My Dashboard</h1>
    <div class="breadcrumb">Welcome, <?= sanitize($user['name']) ?> 📚</div>
  </div>
</div>

<div class="stats-grid stagger">
  <div class="stat-card">
    <div class="stat-info">
      <h4>Total Activities</h4>
      <div class="stat-value" id="stat-total">0</div>
    </div>
    <div class="stat-icon blue">📝</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Completed</h4>
      <div class="stat-value" id="stat-completed">0</div>
    </div>
    <div class="stat-icon green">✅</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Enrolled Subjects</h4>
      <div class="stat-value" id="stat-subjects">0</div>
    </div>
    <div class="stat-icon purple">📚</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Avg. Performance</h4>
      <div class="stat-value" id="stat-avg">0<span style="font-size:0.875rem;font-weight:500">%</span></div>
    </div>
    <div class="stat-icon orange">📊</div>
  </div>
</div>

<div class="grid-2 mb-3">
  <div class="card">
    <div class="card-header"><h3>📊 Subject-wise Performance</h3></div>
    <div class="card-body">
      <div class="chart-container"><canvas id="chart-subject-perf"></canvas></div>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><h3>📈 Marks Trend</h3></div>
    <div class="card-body">
      <div class="chart-container"><canvas id="chart-marks-trend"></canvas></div>
    </div>
  </div>
</div>

<!-- Subject-wise marks breakdown -->
<div class="card">
  <div class="card-header"><h3>📋 Subject-wise Marks</h3></div>
  <div class="card-body" id="marks-breakdown">
    <div class="empty-state"><div class="spinner"></div></div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  // Stats
  const res = await API.get('/api/dashboard.php?action=stats');
  if (res && res.success) {
    const s = res.stats;
    animateCounter(document.getElementById('stat-total'), parseInt(s.total_activities));
    animateCounter(document.getElementById('stat-completed'), parseInt(s.completed));
    animateCounter(document.getElementById('stat-subjects'), parseInt(s.subjects));
    animateCounter(document.getElementById('stat-avg'), parseFloat(s.avg_percentage));
  }

  // Subject Performance Chart
  const perfRes = await API.get('/api/dashboard.php?action=chart&type=subject_performance');
  if (perfRes && perfRes.success && perfRes.chart.length) {
    Charts.bar('chart-subject-perf',
      perfRes.chart.map(d => d.label),
      [{
        label: 'Performance %',
        data: perfRes.chart.map(d => parseFloat(d.value)),
        backgroundColor: perfRes.chart.map((_, i) => 
          ['#818cf8', '#6366f1', '#4f46e5', '#4338ca', '#3730a3', '#312e81'][i % 6]
        ),
        borderRadius: 8,
        barThickness: 40
      }]
    );
  }

  // Marks Trend
  const trendRes = await API.get('/api/dashboard.php?action=chart&type=marks_trend');
  if (trendRes && trendRes.success && trendRes.chart.length) {
    Charts.line('chart-marks-trend',
      trendRes.chart.map(d => d.label.substring(0, 15)),
      [{
        label: 'Performance %',
        data: trendRes.chart.map(d => parseFloat(d.value)),
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        fill: true,
        pointBackgroundColor: '#10b981'
      }]
    );
  }

  // Marks Breakdown
  const marksRes = await API.get('/api/marks.php?action=by_student');
  if (marksRes && marksRes.success) {
    const container = document.getElementById('marks-breakdown');
    if (marksRes.marks.length === 0) {
      container.innerHTML = '<div class="empty-state"><div class="icon">📊</div><h3>No marks published yet</h3><p>Your marks will appear here once your teachers publish them.</p></div>';
      return;
    }

    // Group by subject
    const subjects = {};
    marksRes.marks.forEach(m => {
      if (!subjects[m.subject_code]) {
        subjects[m.subject_code] = { name: m.subject_name, code: m.subject_code, items: [], total: 0, max: 0 };
      }
      subjects[m.subject_code].items.push(m);
      subjects[m.subject_code].total += parseFloat(m.marks_obtained);
      subjects[m.subject_code].max += parseFloat(m.max_marks);
    });

    let html = '';
    Object.values(subjects).forEach(sub => {
      const pct = sub.max > 0 ? ((sub.total / sub.max) * 100).toFixed(1) : 0;
      const colorClass = pct >= 75 ? 'success' : pct >= 50 ? '' : pct >= 35 ? 'warning' : 'danger';
      
      html += `
        <div style="margin-bottom:20px;">
          <div class="progress-label">
            <span class="label"><strong>${sub.code}</strong> — ${sub.name}</span>
            <span class="value">${sub.total.toFixed(1)} / ${sub.max.toFixed(1)} (${pct}%)</span>
          </div>
          <div class="progress ${colorClass}">
            <div class="progress-fill" style="width:${pct}%"></div>
          </div>
          <table style="margin-top:10px;font-size:0.8125rem;">
            <thead><tr><th>Activity</th><th>Type</th><th>Marks</th><th>Max</th><th>%</th></tr></thead>
            <tbody>
              ${sub.items.map(m => {
                const apct = ((parseFloat(m.marks_obtained) / parseFloat(m.max_marks)) * 100).toFixed(1);
                return `<tr>
                  <td>${m.activity_name}</td>
                  <td><span class="badge badge-primary">${m.activity_type}</span></td>
                  <td><strong>${parseFloat(m.marks_obtained).toFixed(1)}</strong></td>
                  <td>${parseFloat(m.max_marks).toFixed(1)}</td>
                  <td>${apct}%</td>
                </tr>`;
              }).join('')}
            </tbody>
          </table>
        </div>
      `;
    });
    container.innerHTML = html;
  }
});
</script>
