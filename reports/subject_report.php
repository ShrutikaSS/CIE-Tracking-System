<?php
$pageTitle = 'Subject Report';
require_once __DIR__ . '/../includes/header.php';
requireLogin();
?>

<div class="page-header">
  <div>
    <h1>Subject Report</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Subject Report</div>
  </div>
</div>

<!-- Subject Selector -->
<div class="card mb-3">
  <div class="card-body">
    <div class="form-row">
      <div class="form-group mb-0">
        <label>Select Subject</label>
        <select class="form-control" id="select-subject" onchange="loadReport()">
          <option value="">— Choose Subject —</option>
        </select>
      </div>
      <div class="form-group mb-0 d-flex align-center gap-1 flex-wrap" style="align-self:flex-end; width:100%;">
        <button class="btn btn-secondary" onclick="exportPDF()" id="btn-pdf" disabled style="flex:1;">📄 Export PDF</button>
        <button class="btn btn-secondary" onclick="exportExcel()" id="btn-excel" disabled style="flex:1;">📊 Export Excel</button>
      </div>
    </div>
  </div>
</div>

<!-- Report Content -->
<div id="report-content" class="hidden">
  <!-- Subject Info & Stats -->
  <div class="stats-grid stagger mb-3" id="report-stats"></div>
  
  <!-- Chart -->
  <div class="card mb-3">
    <div class="card-header"><h3>📊 Student Performance Distribution</h3></div>
    <div class="card-body">
      <div class="chart-container"><canvas id="chart-dist"></canvas></div>
    </div>
  </div>
  
  <!-- Students Table -->
  <div class="card">
    <div class="card-header">
      <h3>🎓 Student Marks</h3>
      <div class="search-filter" style="max-width:250px">
        <span class="icon">🔍</span>
        <input type="text" id="search-report" placeholder="Search students..." data-search-table="report-table">
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-container">
        <table id="report-table" data-sortable>
          <thead>
            <tr>
              <th>USN</th>
              <th>Student Name</th>
              <th>Section</th>
              <th>Marks Obtained</th>
              <th>Max Marks</th>
              <th>Percentage</th>
              <th>Activities Done</th>
            </tr>
          </thead>
          <tbody id="report-tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
let reportData = null;
let distChart = null;

async function init() {
  const res = await API.get('/api/subjects.php');
  if (res && res.success) {
    document.getElementById('select-subject').innerHTML = '<option value="">— Choose Subject —</option>' +
      res.subjects.map(s => `<option value="${s.id}">${s.code} — ${s.name}</option>`).join('');
  }
}

async function loadReport() {
  const subjectId = document.getElementById('select-subject').value;
  if (!subjectId) {
    document.getElementById('report-content').classList.add('hidden');
    document.getElementById('btn-pdf').disabled = true;
    document.getElementById('btn-excel').disabled = true;
    return;
  }
  
  const res = await API.get(`/api/reports.php?action=subject&subject_id=${subjectId}`);
  if (!res || !res.success) return;
  
  reportData = res;
  document.getElementById('report-content').classList.remove('hidden');
  document.getElementById('btn-pdf').disabled = false;
  document.getElementById('btn-excel').disabled = false;
  
  // Stats
  const st = res.stats;
  document.getElementById('report-stats').innerHTML = `
    <div class="stat-card"><div class="stat-info"><h4>Total Students</h4><div class="stat-value">${st.total_students}</div></div><div class="stat-icon blue">🎓</div></div>
    <div class="stat-card"><div class="stat-info"><h4>Class Average</h4><div class="stat-value">${st.average}%</div></div><div class="stat-icon green">📊</div></div>
    <div class="stat-card"><div class="stat-info"><h4>Highest</h4><div class="stat-value">${st.highest}%</div></div><div class="stat-icon purple">🏆</div></div>
    <div class="stat-card"><div class="stat-info"><h4>Pass Rate</h4><div class="stat-value">${st.pass_rate}%</div></div><div class="stat-icon orange">✅</div></div>
  `;
  
  // Distribution Chart
  const ranges = { '90-100': 0, '75-89': 0, '60-74': 0, '40-59': 0, 'Below 40': 0 };
  res.students.forEach(s => {
    const p = s.percentage;
    if (p >= 90) ranges['90-100']++;
    else if (p >= 75) ranges['75-89']++;
    else if (p >= 60) ranges['60-74']++;
    else if (p >= 40) ranges['40-59']++;
    else ranges['Below 40']++;
  });
  
  if (distChart) distChart.destroy();
  distChart = Charts.bar('chart-dist',
    Object.keys(ranges),
    [{
      label: 'Students',
      data: Object.values(ranges),
      backgroundColor: ['#10b981', '#4f46e5', '#f59e0b', '#f97316', '#ef4444'],
      borderRadius: 8,
      barThickness: 50
    }]
  );
  
  // Students Table
  const tbody = document.getElementById('report-tbody');
  if (res.students.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>No data</h3></div></td></tr>';
    return;
  }
  
  tbody.innerHTML = res.students.map(s => {
    const pct = s.percentage;
    const colorClass = pct >= 75 ? 'success' : pct >= 50 ? '' : pct >= 35 ? 'warning' : 'danger';
    return `<tr>
      <td><span class="badge badge-info">${s.usn}</span></td>
      <td><strong>${s.name}</strong></td>
      <td>${s.section}</td>
      <td>${s.total_obtained !== null ? parseFloat(s.total_obtained).toFixed(1) : '—'}</td>
      <td>${s.total_max !== null ? parseFloat(s.total_max).toFixed(1) : '—'}</td>
      <td>
        <div class="d-flex align-center gap-1">
          <div class="progress ${colorClass}" style="width:80px;height:6px">
            <div class="progress-fill" style="width:${pct}%"></div>
          </div>
          <span class="fs-sm fw-600">${pct}%</span>
        </div>
      </td>
      <td>${s.activities_completed}</td>
    </tr>`;
  }).join('');
  
  // Reinit sorting
  DataTable.init('report-table');
}

function exportPDF() {
  if (!reportData) return;
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  const sub = reportData.subject;
  const st = reportData.stats;
  
  doc.setFontSize(18);
  doc.text('CIE Marks Report — Subject', 14, 20);
  doc.setFontSize(11);
  doc.text(`${sub.code} — ${sub.name}`, 14, 32);
  doc.text(`Faculty: ${sub.faculty_name || 'N/A'} | Dept: ${sub.dept_name}`, 14, 39);
  doc.text(`Avg: ${st.average}% | Highest: ${st.highest}% | Pass Rate: ${st.pass_rate}%`, 14, 46);
  doc.text(`Generated: ${new Date().toLocaleString()}`, 14, 53);
  
  const rows = reportData.students.map(s => [
    s.usn, s.name, s.section,
    s.total_obtained ? parseFloat(s.total_obtained).toFixed(1) : '—',
    s.total_max ? parseFloat(s.total_max).toFixed(1) : '—',
    s.percentage + '%', s.activities_completed
  ]);
  
  doc.autoTable({
    startY: 60,
    head: [['USN', 'Name', 'Sec', 'Obtained', 'Max', '%', 'Activities']],
    body: rows,
    theme: 'striped',
    headStyles: { fillColor: [79, 70, 229] },
    styles: { fontSize: 9 }
  });
  
  doc.save(`Subject_Report_${sub.code}.pdf`);
  Toast.success('PDF exported.');
}

function exportExcel() {
  if (!reportData) return;
  const sub = reportData.subject;
  const rows = reportData.students.map(s => ({
    'USN': s.usn,
    'Name': s.name,
    'Section': s.section,
    'Marks Obtained': s.total_obtained ? parseFloat(s.total_obtained).toFixed(1) : '—',
    'Max Marks': s.total_max ? parseFloat(s.total_max).toFixed(1) : '—',
    'Percentage': s.percentage + '%',
    'Activities Completed': s.activities_completed
  }));
  
  const wb = XLSX.utils.book_new();
  const ws = XLSX.utils.json_to_sheet(rows);
  XLSX.utils.book_append_sheet(wb, ws, sub.code);
  XLSX.writeFile(wb, `Subject_Report_${sub.code}.xlsx`);
  Toast.success('Excel exported.');
}

document.addEventListener('DOMContentLoaded', init);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
