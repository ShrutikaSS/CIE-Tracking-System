<?php
$pageTitle = 'Class Performance';
require_once __DIR__ . '/../includes/header.php';
requireRole(['coordinator']);
?>

<div class="page-header">
  <div>
    <h1>Class Performance</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Class Performance</div>
  </div>
</div>

<!-- Filters -->
<div class="card mb-3">
  <div class="card-body" style="padding: 15px 20px;">
    <form id="perf-filters" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
      <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
        <label style="margin-bottom: 5px; font-weight: 500; font-size: 0.85rem;">Semester</label>
        <select class="form-control" id="filter-semester" onchange="applyFilters()">
          <option value="5" selected>Semester 5</option>
          <option value="6">Semester 6</option>
        </select>
      </div>
      <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
        <label style="margin-bottom: 5px; font-weight: 500; font-size: 0.85rem;">Subject</label>
        <select class="form-control" id="filter-subject" onchange="applyFilters()">
          <option value="all">All Subjects</option>
          <option value="ds">Data Structures</option>
          <option value="dbms">Database Systems</option>
          <option value="se">Software Engineering</option>
          <option value="cn">Computer Networks</option>
          <option value="cc">Cloud Computing</option>
          <option value="web">Web Development</option>
        </select>
      </div>
      <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
        <label style="margin-bottom: 5px; font-weight: 500; font-size: 0.85rem;">Activity Type</label>
        <select class="form-control" id="filter-activity" onchange="applyFilters()">
          <option value="all">All Activities</option>
          <option value="test">Tests</option>
          <option value="quiz">Quizzes</option>
          <option value="assignment">Assignments</option>
          <option value="seminar">Seminars</option>
          <option value="viva">Vivas</option>
        </select>
      </div>
      <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset Filters</button>
    </form>
  </div>
</div>

<!-- Class Statistics Grid -->
<div class="stats-grid stagger mb-3" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
  <div class="stat-card">
    <div class="stat-info">
      <h4>Average Class Percentage</h4>
      <div class="stat-value" id="avg-class-perf">78.5%</div>
      <span class="stat-change positive">📈 Exceeds target (75%)</span>
    </div>
    <div class="stat-icon blue">📊</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Overall Pass Percentage</h4>
      <div class="stat-value" id="pass-percent-perf">92.0%</div>
      <span class="stat-change positive">📈 Up 2% vs last semester</span>
    </div>
    <div class="stat-icon green">🎓</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Top Performer Score</h4>
      <div class="stat-value" id="top-performer-score">96.8%</div>
      <span class="stat-change positive">🏆 Roll 12 - Nikita Shah</span>
    </div>
    <div class="stat-icon gold" style="font-size: 1.5rem;">🏆</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Critical Students</h4>
      <div class="stat-value" id="critical-students-count" style="color: var(--danger)">4</div>
      <span class="stat-change negative" style="color: var(--danger)">⚠️ Below 40% threshold</span>
    </div>
    <div class="stat-icon red">⚠️</div>
  </div>
</div>

<!-- Performance Charts -->
<div class="grid-2 mb-3">
  <div class="card">
    <div class="card-header">
      <h3>📊 Average Marks by Subject</h3>
    </div>
    <div class="card-body">
      <div class="chart-container" style="position: relative; height: 300px;">
        <canvas id="chart-avg-marks"></canvas>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h3>📈 Pass Percentage by Subject</h3>
    </div>
    <div class="card-body">
      <div class="chart-container" style="position: relative; height: 300px;">
        <canvas id="chart-pass-percentage"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Subject-wise Performance Table -->
<div class="card mb-3">
  <div class="card-header">
    <h3>📚 Subject-wise Performance Breakdown</h3>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-responsive">
      <table class="table" id="subject-perf-table">
        <thead>
          <tr>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th>Faculty Coordinator</th>
            <th>Students Enrolled</th>
            <th>Activities Done</th>
            <th>Class Average</th>
            <th>Pass Percentage</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <!-- Dynamic / Mock rows loaded via script -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Activity-wise Performance Breakdown -->
<div class="card">
  <div class="card-header">
    <h3>📋 Activity-wise Performance Breakdown</h3>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-responsive">
      <table class="table" id="activity-perf-table">
        <thead>
          <tr>
            <th>Subject</th>
            <th>Activity Name</th>
            <th>Activity Type</th>
            <th>Max Marks</th>
            <th>Average Marks</th>
            <th>Highest Marks</th>
            <th>Lowest Marks</th>
            <th>Evaluated On</th>
          </tr>
        </thead>
        <tbody>
          <!-- Dynamic / Mock rows loaded via script -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
// Mock data set matching the filters
const mockSubjectData = [
  { code: 'CS501', name: 'Data Structures', faculty: 'Prof. Anil Mehta', enrolled: 60, activities: 3, avg: 74.2, pass: 88, status: 'Good' },
  { code: 'CS502', name: 'Database Systems', faculty: 'Prof. Anil Mehta', enrolled: 60, activities: 3, avg: 78.5, pass: 92, status: 'Excellent' },
  { code: 'CS503', name: 'Software Engineering', faculty: 'Prof. Sneha Patil', enrolled: 60, activities: 2, avg: 82.1, pass: 95, status: 'Excellent' },
  { code: 'CS504', name: 'Computer Networks', faculty: 'Prof. Rajesh K.', enrolled: 60, activities: 3, avg: 71.0, pass: 85, status: 'Good' },
  { code: 'CS505', name: 'Cloud Computing', faculty: 'Prof. Sneha Patil', enrolled: 60, activities: 4, avg: 81.4, pass: 97, status: 'Excellent' },
  { code: 'CS506', name: 'Web Development', faculty: 'Prof. Meera Sen', enrolled: 60, activities: 3, avg: 83.8, pass: 98, status: 'Excellent' }
];

const mockActivityData = [
  { subject: 'Data Structures', name: 'Mid-Sem Test 1', type: 'test', max: 50, avg: 36.5, high: 49, low: 18, date: '2026-06-15' },
  { subject: 'Data Structures', name: 'Assignment 1', type: 'assignment', max: 20, avg: 17.8, high: 20, low: 10, date: '2026-06-25' },
  { subject: 'Data Structures', name: 'Quiz 1', type: 'quiz', max: 10, avg: 7.2, high: 10, low: 3, date: '2026-07-02' },
  { subject: 'Database Systems', name: 'Mid-Sem Test 2', type: 'test', max: 50, avg: 38.0, high: 48, low: 22, date: '2026-06-20' },
  { subject: 'Database Systems', name: 'Assignment 2', type: 'assignment', max: 20, avg: 16.5, high: 20, low: 12, date: '2026-06-30' },
  { subject: 'Database Systems', name: 'Quiz 2', type: 'quiz', max: 10, avg: 8.5, high: 10, low: 4, date: '2026-07-10' },
  { subject: 'Software Engineering', name: 'Seminar 1', type: 'seminar', max: 30, avg: 24.6, high: 30, low: 15, date: '2026-06-18' },
  { subject: 'Software Engineering', name: 'Viva 1', type: 'viva', max: 20, avg: 16.4, high: 20, low: 11, date: '2026-07-05' },
  { subject: 'Computer Networks', name: 'Test 1', type: 'test', max: 50, avg: 34.2, high: 47, low: 15, date: '2026-06-12' },
  { subject: 'Computer Networks', name: 'Assignment 1', type: 'assignment', max: 20, avg: 15.6, high: 20, low: 8, date: '2026-07-01' },
  { subject: 'Cloud Computing', name: 'Quiz 1', type: 'quiz', max: 20, avg: 16.8, high: 20, low: 10, date: '2026-06-22' },
  { subject: 'Cloud Computing', name: 'Test 2', type: 'test', max: 50, avg: 41.2, high: 50, low: 25, date: '2026-07-14' }
];

let avgMarksChart = null;
let passPercentageChart = null;

document.addEventListener('DOMContentLoaded', () => {
  renderData();
});

function applyFilters() {
  renderData();
  Toast.success('Filters applied successfully.');
}

function resetFilters() {
  document.getElementById('filter-semester').value = '5';
  document.getElementById('filter-subject').value = 'all';
  document.getElementById('filter-activity').value = 'all';
  renderData();
  Toast.success('Filters reset.');
}

function renderData() {
  const selectedSem = document.getElementById('filter-semester').value;
  const selectedSub = document.getElementById('filter-subject').value;
  const selectedAct = document.getElementById('filter-activity').value;

  // Filter subject performance data
  let filteredSubjects = [...mockSubjectData];
  if (selectedSub !== 'all') {
    filteredSubjects = filteredSubjects.filter(s => s.name.toLowerCase().replace(' ', '') === selectedSub);
  }

  // Filter activity performance data
  let filteredActivities = [...mockActivityData];
  if (selectedSub !== 'all') {
    const subNameMap = {
      ds: 'data structures',
      dbms: 'database systems',
      se: 'software engineering',
      cn: 'computer networks',
      cc: 'cloud computing',
      web: 'web development'
    };
    filteredActivities = filteredActivities.filter(a => a.subject.toLowerCase() === subNameMap[selectedSub]);
  }
  if (selectedAct !== 'all') {
    filteredActivities = filteredActivities.filter(a => a.type === selectedAct);
  }

  // Render stats cards dynamically
  if (filteredSubjects.length > 0) {
    const totalAvg = (filteredSubjects.reduce((acc, curr) => acc + curr.avg, 0) / filteredSubjects.length).toFixed(1);
    const totalPass = (filteredSubjects.reduce((acc, curr) => acc + curr.pass, 0) / filteredSubjects.length).toFixed(1);
    document.getElementById('avg-class-perf').innerText = totalAvg + '%';
    document.getElementById('pass-percent-perf').innerText = totalPass + '%';
    
    // Scale stats down slightly if filtering to critical areas
    if (selectedSub === 'cn') {
      document.getElementById('critical-students-count').innerText = '7';
    } else {
      document.getElementById('critical-students-count').innerText = '4';
    }
  } else {
    document.getElementById('avg-class-perf').innerText = '0%';
    document.getElementById('pass-percent-perf').innerText = '0%';
    document.getElementById('critical-students-count').innerText = '0';
  }

  // Populate tables
  const subTableBody = document.querySelector('#subject-perf-table tbody');
  subTableBody.innerHTML = filteredSubjects.map(s => `
    <tr>
      <td><strong>${s.code}</strong></td>
      <td>${s.name}</td>
      <td>${s.faculty}</td>
      <td>${s.enrolled}</td>
      <td>${s.activities}</td>
      <td><strong>${s.avg}%</strong></td>
      <td>
        <div style="display:flex; align-items:center; gap:8px;">
          <div class="progress-bar-container" style="flex:1; height: 6px; background-color: var(--border-color); border-radius:3px;">
            <div style="width: ${s.pass}%; height: 100%; border-radius:3px; background-color: ${s.pass >= 90 ? 'var(--success)' : 'var(--warning)'};"></div>
          </div>
          <span style="font-weight:600; font-size:0.85rem;">${s.pass}%</span>
        </div>
      </td>
      <td><span class="badge" style="background-color: ${s.status === 'Excellent' ? 'var(--success-light)' : 'var(--primary-lighter)'}; color: ${s.status === 'Excellent' ? 'var(--success)' : 'var(--primary)'}">${s.status}</span></td>
    </tr>
  `).join('');

  if (filteredSubjects.length === 0) {
    subTableBody.innerHTML = `<tr><td colspan="8" class="text-center" style="padding:20px; color:var(--text-muted)">No subjects match the selected filters.</td></tr>`;
  }

  const actTableBody = document.querySelector('#activity-perf-table tbody');
  actTableBody.innerHTML = filteredActivities.map(a => `
    <tr>
      <td>${a.subject}</td>
      <td><strong>${a.name}</strong></td>
      <td><span style="text-transform: capitalize;">${a.type}</span></td>
      <td>${a.max}</td>
      <td><strong>${a.avg}</strong> <span style="font-size:0.75rem; color:var(--text-muted)">(${(a.avg/a.max*100).toFixed(0)}%)</span></td>
      <td style="color:var(--success); font-weight:600;">${a.high}</td>
      <td style="color:var(--danger); font-weight:600;">${a.low}</td>
      <td>${a.date}</td>
    </tr>
  `).join('');

  if (filteredActivities.length === 0) {
    actTableBody.innerHTML = `<tr><td colspan="8" class="text-center" style="padding:20px; color:var(--text-muted)">No activities match the selected filters.</td></tr>`;
  }

  // Redraw charts
  renderCharts(filteredSubjects);
}

function renderCharts(subjects) {
  const labels = subjects.map(s => s.name);
  const avgData = subjects.map(s => s.avg);
  const passData = subjects.map(s => s.pass);

  // 1. Avg Marks Bar Chart
  if (avgMarksChart) avgMarksChart.destroy();
  const ctx1 = document.getElementById('chart-avg-marks');
  if (ctx1) {
    avgMarksChart = new Chart(ctx1, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Average Performance %',
          data: avgData,
          backgroundColor: '#0F4C81',
          borderRadius: 6,
          maxBarThickness: 40
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            min: 0,
            max: 100,
            grid: { color: '#f1f5f9' },
            ticks: { callback: value => value + '%' }
          },
          x: { grid: { display: false } }
        },
        plugins: {
          legend: { display: false }
        }
      }
    });
  }

  // 2. Pass Percentage Line Chart
  if (passPercentageChart) passPercentageChart.destroy();
  const ctx2 = document.getElementById('chart-pass-percentage');
  if (ctx2) {
    passPercentageChart = new Chart(ctx2, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Pass Percentage %',
          data: passData,
          borderColor: '#2A9D8F',
          backgroundColor: 'rgba(42, 157, 143, 0.1)',
          fill: true,
          pointBackgroundColor: '#2A9D8F',
          pointRadius: 5,
          tension: 0.2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            min: 0,
            max: 100,
            grid: { color: '#f1f5f9' },
            ticks: { callback: value => value + '%' }
          },
          x: { grid: { display: false } }
        },
        plugins: {
          legend: { display: false }
        }
      }
    });
  }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
