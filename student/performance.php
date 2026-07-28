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
<!-- CIE Forecast Simulator -->
<div class="card mb-3" id="forecast-card" style="border-left: 4px solid var(--primary);">
  <div class="card-header" style="flex-wrap:wrap; gap:12px;">
    <h3 style="display:flex; align-items:center; gap:8px; margin:0;">
      🎯 CIE Grade Forecast Simulator
    </h3>
    <span class="badge badge-info" style="font-size:0.75rem;">Interactive Tool</span>
  </div>
  <div class="card-body">
    <p class="text-muted" style="font-size:0.85rem; margin-bottom:16px;">
      Enter your <strong>target CIE percentage</strong> to see how many marks you need in your remaining activities.
    </p>
    <div class="form-row" style="align-items:flex-end; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
      <div class="form-group" style="margin-bottom:0; min-width:180px;">
        <label>Subject</label>
        <select class="form-control" id="forecast-subject" onchange="runForecast()">
          <option value="">— Select Subject —</option>
        </select>
      </div>
      <div class="form-group" style="margin-bottom:0; min-width:120px;">
        <label>Target CIE %</label>
        <input type="number" class="form-control" id="forecast-target" value="75" min="0" max="100" step="1" oninput="runForecast()">
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <button class="btn btn-primary btn-sm" onclick="runForecast()">Calculate</button>
      </div>
    </div>
    <div id="forecast-result" style="display:none;">
      <div style="padding:16px; background:var(--bg-hover); border-radius:10px; border:1px solid var(--border-color);">
        <div id="forecast-summary" style="font-size:0.9rem; line-height:1.6;"></div>
        <div id="forecast-bar-container" style="margin-top:12px;">
          <div style="display:flex; justify-content:space-between; font-size:0.75rem; color:var(--text-muted); margin-bottom:4px;">
            <span>Current Progress</span>
            <span id="forecast-progress-label">0%</span>
          </div>
          <div style="width:100%; height:10px; background:var(--bg-input); border-radius:6px; overflow:hidden;">
            <div id="forecast-progress-bar" style="height:100%; border-radius:6px; transition:width 0.6s cubic-bezier(0.16,1,0.3,1); background:linear-gradient(90deg, var(--primary), var(--accent));"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- SUBJECT PERFORMANCE ANALYTICS SECTION -->
<div class="card mb-3" style="padding: 24px;">
  <!-- Header Title -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
    <div>
      <h3 style="display:flex; align-items:center; gap:10px; font-family:'Lora', serif; font-size:1.35rem; font-weight:700; color:var(--text-primary); margin:0;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:10px; background:rgba(13, 58, 113, 0.08); color:var(--primary);">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
        </span>
        Subject Performance (Subject-wise %)
      </h3>
      <div style="font-size:0.85rem; color:var(--text-secondary); margin-top:4px;">Interactive subject evaluation, scoring trends & comparative analytics</div>
    </div>
  </div>

  <!-- Interactive Filters Bar -->
  <div class="perf-filters-bar mb-3" style="display: flex; flex-wrap: wrap; gap: 10px; padding: 14px; background: rgba(0,0,0,0.025); border-radius: 12px; border: 1px solid var(--border-light); align-items: center;">
    <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg> Filters:
    </div>

    <select id="perf-filter-year" class="form-control perf-filter-input" style="width: auto; min-width: 120px; font-size: 0.825rem; height: 34px; padding: 4px 10px; border-radius: 6px;">
      <option value="all">Year: All</option>
      <option value="2025-2026" selected>2025-2026</option>
      <option value="2024-2025">2024-2025</option>
    </select>

    <select id="perf-filter-sem" class="form-control perf-filter-input" style="width: auto; min-width: 120px; font-size: 0.825rem; height: 34px; padding: 4px 10px; border-radius: 6px;">
      <option value="all">Semester: All</option>
      <option value="1">Sem 1</option>
      <option value="2">Sem 2</option>
      <option value="3">Sem 3</option>
      <option value="4">Sem 4</option>
      <option value="5" selected>Sem 5</option>
      <option value="6">Sem 6</option>
    </select>

    <select id="perf-filter-dept" class="form-control perf-filter-input" style="width: auto; min-width: 120px; font-size: 0.825rem; height: 34px; padding: 4px 10px; border-radius: 6px;">
      <option value="all">Dept: All</option>
      <option value="CSE">CSE</option>
      <option value="AIML">AIML</option>
      <option value="AIDS">AIDS</option>
      <option value="IT">IT</option>
      <option value="ENTC">ENTC</option>
      <option value="ME">ME</option>
      <option value="CE">CE</option>
      <option value="EE">EE</option>
    </select>

    <select id="perf-filter-div" class="form-control perf-filter-input" style="width: auto; min-width: 110px; font-size: 0.825rem; height: 34px; padding: 4px 10px; border-radius: 6px;">
      <option value="all">Division: All</option>
      <option value="A">Div A</option>
      <option value="B">Div B</option>
    </select>

    <select id="perf-filter-cie" class="form-control perf-filter-input" style="width: auto; min-width: 130px; font-size: 0.825rem; height: 34px; padding: 4px 10px; border-radius: 6px;">
      <option value="all">CIE Exam: All</option>
      <option value="assignment">CIE-1 (Assignment)</option>
      <option value="quiz">CIE-2 (Quiz)</option>
      <option value="test">CIE-3 (Unit Test)</option>
      <option value="viva">Viva / Practical</option>
    </select>

    <button type="button" id="perf-filter-reset" class="btn btn-sm btn-secondary" style="height: 34px; font-size: 0.8rem; padding: 0 12px; margin-left: auto; border-radius: 6px;">
      Reset Filters
    </button>
  </div>

  <!-- Summary Statistics Header Bar -->
  <div class="perf-stats-summary mb-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px;">
    <div style="background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.15); padding: 12px 16px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #2563eb; letter-spacing: 0.5px;">Overall Average</div>
        <div id="summary-perf-avg" style="font-size: 1.3rem; font-weight: 800; color: #1e40af; margin-top: 2px;">0%</div>
      </div>
      <div style="font-size: 1.4rem;">📊</div>
    </div>

    <div style="background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.15); padding: 12px 16px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #059669; letter-spacing: 0.5px;">Highest Scoring</div>
        <div id="summary-perf-highest" style="font-size: 1.05rem; font-weight: 800; color: #065f46; margin-top: 2px;">—</div>
      </div>
      <div style="font-size: 1.4rem;">🏆</div>
    </div>

    <div style="background: rgba(245, 158, 11, 0.06); border: 1px solid rgba(245, 158, 11, 0.15); padding: 12px 16px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #d97706; letter-spacing: 0.5px;">Lowest Scoring</div>
        <div id="summary-perf-lowest" style="font-size: 1.05rem; font-weight: 800; color: #92400e; margin-top: 2px;">—</div>
      </div>
      <div style="font-size: 1.4rem;">⚠️</div>
    </div>

    <div style="background: rgba(139, 92, 246, 0.06); border: 1px solid rgba(139, 92, 246, 0.15); padding: 12px 16px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #7c3aed; letter-spacing: 0.5px;">Total Subjects</div>
        <div id="summary-perf-total" style="font-size: 1.3rem; font-weight: 800; color: #5b21b6; margin-top: 2px;">0</div>
      </div>
      <div style="font-size: 1.4rem;">📚</div>
    </div>
  </div>

  <!-- Chart Canvas Container -->
  <div style="position: relative; height: 350px; width: 100%;">
    <canvas id="chart-subject-perf"></canvas>
  </div>
</div>

<div class="card mb-3">
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

  // Subject Performance Chart Redesign Implementation
  let subjectChartInstance = null;

  async function loadSubjectPerformanceChart() {
    const year = document.getElementById('perf-filter-year')?.value || 'all';
    const sem = document.getElementById('perf-filter-sem')?.value || 'all';
    const dept = document.getElementById('perf-filter-dept')?.value || 'all';
    const div = document.getElementById('perf-filter-div')?.value || 'all';
    const cie = document.getElementById('perf-filter-cie')?.value || 'all';

    const params = new URLSearchParams({
      action: 'chart',
      type: 'subject_performance'
    });

    if (sem !== 'all') params.append('semester', sem);
    if (cie !== 'all') params.append('cie', cie);
    if (dept !== 'all') params.append('dept', dept);

    const res = await API.get('/api/dashboard.php?' + params.toString());
    
    const avgEl = document.getElementById('summary-perf-avg');
    const highEl = document.getElementById('summary-perf-highest');
    const lowEl = document.getElementById('summary-perf-lowest');
    const totEl = document.getElementById('summary-perf-total');

    if (!res || !res.success || !res.chart || !res.chart.length) {
      if (avgEl) avgEl.textContent = '0%';
      if (highEl) highEl.textContent = 'N/A';
      if (lowEl) lowEl.textContent = 'N/A';
      if (totEl) totEl.textContent = '0';

      if (subjectChartInstance) {
        subjectChartInstance.destroy();
        subjectChartInstance = null;
      }
      return;
    }

    let chartData = res.chart;

    // Summary Metrics
    let totalVal = 0;
    let highestItem = chartData[0];
    let lowestItem = chartData[0];

    chartData.forEach(item => {
      const val = parseFloat(item.value);
      totalVal += val;
      if (val > parseFloat(highestItem.value)) highestItem = item;
      if (val < parseFloat(lowestItem.value)) lowestItem = item;
    });

    const avgVal = (totalVal / chartData.length).toFixed(1);

    if (avgEl) avgEl.textContent = avgVal + '%';
    if (highEl) highEl.textContent = `${highestItem.label} (${highestItem.value}%)`;
    if (lowEl) lowEl.textContent = `${lowestItem.label} (${lowestItem.value}%)`;
    if (totEl) totEl.textContent = chartData.length;

    const canvas = document.getElementById('chart-subject-perf');
    if (!canvas) return;

    if (subjectChartInstance) {
      subjectChartInstance.destroy();
    }

    // Colors: Highlight Highest in vibrant Emerald Green (#10b981), others in palette
    const bgColors = chartData.map(d => {
      if (d.label === highestItem.label) return '#10b981';
      const val = parseFloat(d.value);
      if (val >= 85) return '#3b82f6';
      if (val >= 75) return '#6366f1';
      if (val >= 60) return '#8b5cf6';
      return '#f59e0b';
    });

    const borderColors = chartData.map(d => d.label === highestItem.label ? '#059669' : 'transparent');

    // Value on Top Plugin
    const valueOnTopPlugin = {
      id: 'valueOnTopPlugin',
      afterDatasetsDraw(chart) {
        const { ctx } = chart;
        chart.data.datasets.forEach((dataset, datasetIndex) => {
          const meta = chart.getDatasetMeta(datasetIndex);
          meta.data.forEach((bar, index) => {
            const val = dataset.data[index];
            if (val !== undefined && val !== null) {
              ctx.save();
              ctx.font = 'bold 11px Inter, sans-serif';
              ctx.fillStyle = chart.data.labels[index] === highestItem.label ? '#047857' : '#475569';
              ctx.textAlign = 'center';
              ctx.textBaseline = 'bottom';
              ctx.fillText(val + '%', bar.x, bar.y - 6);
              ctx.restore();
            }
          });
        });
      }
    };

    subjectChartInstance = new Chart(canvas, {
      type: 'bar',
      data: {
        labels: chartData.map(d => d.label),
        datasets: [{
          label: 'Performance %',
          data: chartData.map(d => parseFloat(d.value)),
          backgroundColor: bgColors,
          borderColor: borderColors,
          borderWidth: 2,
          borderRadius: 8,
          borderSkipped: false,
          maxBarThickness: 48
        }]
      },
      plugins: [valueOnTopPlugin],
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 800,
          easing: 'easeOutQuart'
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0f172a',
            titleFont: { family: 'Inter', size: 13, weight: '700' },
            bodyFont: { family: 'Inter', size: 12 },
            padding: 14,
            cornerRadius: 10,
            displayColors: true,
            boxPadding: 6,
            callbacks: {
              title: (items) => {
                const idx = items[0].dataIndex;
                const d = chartData[idx];
                return `${d.label} - ${d.subject_name || d.label}`;
              },
              label: (item) => {
                const idx = item.dataIndex;
                const d = chartData[idx];
                const score = item.raw;
                const isTop = d.label === highestItem.label;
                return [
                  `Score: ${score}%${isTop ? ' 🏆 Highest Performer!' : ''}`,
                  `Total Marks: ${d.total_obtained || '—'} / ${d.total_max || '—'}`,
                  `Semester: ${d.semester || '5'} (${d.dept_code || 'CSE'})`,
                  `Evaluation: CIE Assessment`
                ];
              }
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              font: { family: 'Inter', size: 12, weight: '600' },
              color: '#64748b'
            }
          },
          y: {
            min: 0,
            max: 100,
            ticks: {
              stepSize: 20,
              font: { family: 'Inter', size: 11 },
              color: '#94a3b8',
              callback: (v) => v + '%'
            },
            grid: {
              color: 'rgba(226, 232, 240, 0.6)'
            }
          }
        }
      }
    });
  }

  // Bind filter events
  document.querySelectorAll('.perf-filter-input').forEach(select => {
    select.addEventListener('change', loadSubjectPerformanceChart);
  });

  const resetBtn = document.getElementById('perf-filter-reset');
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      document.querySelectorAll('.perf-filter-input').forEach(s => s.value = 'all');
      loadSubjectPerformanceChart();
    });
  }

  // Initial load
  loadSubjectPerformanceChart();

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

  // Populate Forecast Subject Dropdown
  if (perfRes && perfRes.success && perfRes.subjects) {
    const fSel = document.getElementById('forecast-subject');
    fSel.innerHTML = '<option value="">— Select Subject —</option>' +
      perfRes.subjects.map((s, i) => `<option value="${i}">${s.subject_code} — ${s.subject_name}</option>`).join('');
    window._forecastSubjects = perfRes.subjects;
  }
});

let _forecastSubjects = [];

function runForecast() {
  const idx = document.getElementById('forecast-subject').value;
  const targetPct = parseFloat(document.getElementById('forecast-target').value) || 0;
  const resultDiv = document.getElementById('forecast-result');
  
  if (idx === '' || !_forecastSubjects[idx]) { resultDiv.style.display = 'none'; return; }
  resultDiv.style.display = 'block';
  
  const sub = _forecastSubjects[idx];
  const obtained = parseFloat(sub.total_obtained) || 0;
  const totalMax = parseFloat(sub.total_max) || 0;
  const activitiesCompleted = parseInt(sub.activities_completed) || 0;
  const totalActivities = parseInt(sub.total_activities) || activitiesCompleted;
  const remaining = totalActivities - activitiesCompleted;
  
  const currentPct = totalMax > 0 ? ((obtained / totalMax) * 100) : 0;
  
  // Estimate remaining max marks: average max per completed activity * remaining
  const avgMaxPerActivity = activitiesCompleted > 0 ? (totalMax / activitiesCompleted) : 10;
  const estimatedRemainingMax = remaining * avgMaxPerActivity;
  const estimatedTotalMax = totalMax + estimatedRemainingMax;
  
  // Target marks needed
  const targetTotalMarks = (targetPct / 100) * estimatedTotalMax;
  const neededMarks = Math.max(0, targetTotalMarks - obtained);
  
  let summaryHTML = '';
  summaryHTML += `<strong>${sub.subject_code} — ${sub.subject_name}</strong><br>`;
  summaryHTML += `📋 Completed: <strong>${activitiesCompleted}</strong> activities | Remaining: <strong>${remaining}</strong><br>`;
  summaryHTML += `📊 Current: <strong>${obtained.toFixed(1)} / ${totalMax.toFixed(1)}</strong> (${currentPct.toFixed(1)}%)<br>`;
  
  if (remaining > 0) {
    const perActivity = neededMarks / remaining;
    const achievable = perActivity <= avgMaxPerActivity;
    
    if (neededMarks <= 0) {
      summaryHTML += `<span style="color:var(--success);">✅ You've already met your ${targetPct}% target! Keep it up.</span>`;
    } else if (achievable) {
      summaryHTML += `🎯 To reach <strong>${targetPct}%</strong>, you need <strong>${neededMarks.toFixed(1)}</strong> more marks `;
      summaryHTML += `(~<strong>${perActivity.toFixed(1)}</strong> avg per remaining activity).<br>`;
      summaryHTML += `<span style="color:var(--success);">✅ This target is achievable.</span>`;
    } else {
      summaryHTML += `🎯 To reach <strong>${targetPct}%</strong>, you need <strong>${neededMarks.toFixed(1)}</strong> more marks `;
      summaryHTML += `(~<strong>${perActivity.toFixed(1)}</strong> avg per remaining activity).<br>`;
      summaryHTML += `<span style="color:var(--danger);">⚠️ This exceeds the estimated max per activity (~${avgMaxPerActivity.toFixed(0)}). Consider a lower target.</span>`;
    }
  } else {
    summaryHTML += `All activities completed. Final percentage: <strong>${currentPct.toFixed(1)}%</strong>`;
  }
  
  document.getElementById('forecast-summary').innerHTML = summaryHTML;
  
  const progressPct = Math.min(100, currentPct);
  document.getElementById('forecast-progress-bar').style.width = progressPct + '%';
  document.getElementById('forecast-progress-label').textContent = currentPct.toFixed(1) + '%';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
