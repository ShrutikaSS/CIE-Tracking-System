<?php
$pageTitle = 'Student Report';
require_once __DIR__ . '/../includes/header.php';
requireLogin();
?>

<div class="page-header">
  <div>
    <h1>Student Report</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Student Report</div>
  </div>
</div>

<!-- Student Selector -->
<div class="card mb-3">
  <div class="card-body">
    <div class="form-row">
      <div class="form-group mb-0">
        <label>Select Student</label>
        <select class="form-control" id="select-student" onchange="loadReport()">
          <option value="">— Choose Student —</option>
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
  <!-- Student Info -->
  <div class="card mb-3">
    <div class="card-body d-flex justify-between align-center" style="flex-wrap:wrap;gap:16px">
      <div>
        <h3 id="report-name" style="margin-bottom:4px"></h3>
        <div class="text-muted" id="report-meta"></div>
      </div>
      <div id="report-avg-badge"></div>
    </div>
  </div>
  
  <!-- Subject Cards -->
  <div id="report-subjects"></div>
</div>

<script>
let reportData = null;

async function init() {
  const res = await API.get('/api/students.php');
  if (res && res.success) {
    document.getElementById('select-student').innerHTML = '<option value="">— Choose Student —</option>' +
      res.students.map(s => `<option value="${s.id}">${s.usn} — ${s.name} (${s.dept_code})</option>`).join('');
    
    // Check URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const studentId = urlParams.get('student_id');
    if (studentId) {
      const select = document.getElementById('select-student');
      select.value = studentId;
      if (select.value) {
        loadReport();
      }
    }
  }
}

async function loadReport() {
  const studentId = document.getElementById('select-student').value;
  if (!studentId) {
    document.getElementById('report-content').classList.add('hidden');
    document.getElementById('btn-pdf').disabled = true;
    document.getElementById('btn-excel').disabled = true;
    return;
  }
  
  const res = await API.get(`/api/reports.php?action=student&student_id=${studentId}`);
  if (!res || !res.success) return;
  
  reportData = res;
  document.getElementById('report-content').classList.remove('hidden');
  document.getElementById('btn-pdf').disabled = false;
  document.getElementById('btn-excel').disabled = false;
  
  const s = res.student;
  document.getElementById('report-name').textContent = s.name;
  document.getElementById('report-meta').innerHTML = `USN: <strong>${s.usn}</strong> | Dept: <strong>${s.dept_name}</strong> | Sem: <strong>${s.semester}</strong> | Section: <strong>${s.section}</strong>`;
  
  // Calculate overall avg
  let totalObt = 0, totalMax = 0;
  res.subjects.forEach(sub => { totalObt += sub.total_obtained; totalMax += sub.total_max; });
  const overallPct = totalMax > 0 ? ((totalObt / totalMax) * 100).toFixed(1) : 0;
  document.getElementById('report-avg-badge').innerHTML = `<span class="badge ${overallPct >= 75 ? 'badge-success' : overallPct >= 50 ? 'badge-primary' : 'badge-warning'}" style="font-size:1rem;padding:8px 16px">Overall: ${overallPct}%</span>`;
  
  // Subjects
  let html = '';
  res.subjects.forEach(sub => {
    const pct = sub.percentage;
    const colorClass = pct >= 75 ? 'success' : pct >= 50 ? '' : pct >= 35 ? 'warning' : 'danger';
    
    html += `
      <div class="card mb-3">
        <div class="card-header">
          <div><h3 style="font-size:0.9375rem">${sub.subject_code} — ${sub.subject_name}</h3></div>
          <span class="badge ${pct >= 75 ? 'badge-success' : pct >= 50 ? 'badge-primary' : 'badge-warning'}">${pct}%</span>
        </div>
        <div class="card-body">
          <div class="progress ${colorClass}" style="margin-bottom:12px">
            <div class="progress-fill" style="width:${pct}%"></div>
          </div>
          <div class="table-container">
            <table>
              <thead><tr><th>Activity</th><th>Type</th><th>Obtained</th><th>Max</th><th>%</th></tr></thead>
              <tbody>
                ${sub.activities.map(a => {
                  const apct = ((parseFloat(a.marks_obtained) / parseFloat(a.max_marks)) * 100).toFixed(1);
                  return `<tr>
                    <td>${a.activity_name}</td>
                    <td><span class="badge badge-primary">${a.type}</span></td>
                    <td><strong>${parseFloat(a.marks_obtained).toFixed(1)}</strong></td>
                    <td>${parseFloat(a.max_marks).toFixed(1)}</td>
                    <td>${apct}%</td>
                  </tr>`;
                }).join('')}
                <tr style="font-weight:700;background:var(--bg-input)">
                  <td colspan="2">Total</td>
                  <td>${sub.total_obtained.toFixed(1)}</td>
                  <td>${sub.total_max.toFixed(1)}</td>
                  <td>${pct}%</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
  });
  
  document.getElementById('report-subjects').innerHTML = html || '<div class="empty-state"><div class="icon">📊</div><h3>No marks data</h3></div>';
}

function exportPDF() {
  if (!reportData) return;
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  
  const s = reportData.student;
  doc.setFontSize(18);
  doc.text('CIE Marks Report — Student', 14, 20);
  doc.setFontSize(11);
  doc.text(`Name: ${s.name}`, 14, 32);
  doc.text(`USN: ${s.usn} | Dept: ${s.dept_name} | Sem: ${s.semester} | Section: ${s.section}`, 14, 39);
  doc.text(`Generated: ${new Date().toLocaleString()}`, 14, 46);
  
  let y = 56;
  reportData.subjects.forEach(sub => {
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.text(`${sub.subject_code} — ${sub.subject_name} (${sub.percentage}%)`, 14, y);
    y += 4;
    
    const rows = sub.activities.map(a => [
      a.activity_name, a.type, parseFloat(a.marks_obtained).toFixed(1), parseFloat(a.max_marks).toFixed(1),
      ((parseFloat(a.marks_obtained) / parseFloat(a.max_marks)) * 100).toFixed(1) + '%'
    ]);
    rows.push(['Total', '', sub.total_obtained.toFixed(1), sub.total_max.toFixed(1), sub.percentage + '%']);
    
    doc.autoTable({
      startY: y,
      head: [['Activity', 'Type', 'Obtained', 'Max', '%']],
      body: rows,
      theme: 'striped',
      headStyles: { fillColor: [79, 70, 229] },
      margin: { left: 14 },
      styles: { fontSize: 9 }
    });
    
    y = doc.lastAutoTable.finalY + 10;
    if (y > 260) { doc.addPage(); y = 20; }
  });
  
  doc.save(`Student_Report_${s.name.replace(/\s+/g, '_')}_${s.usn}.pdf`);
  Toast.success('PDF exported successfully.');
}

function exportExcel() {
  if (!reportData) return;
  const wb = XLSX.utils.book_new();
  const s = reportData.student;
  
  reportData.subjects.forEach(sub => {
    const rows = sub.activities.map(a => ({
      'Activity': a.activity_name,
      'Type': a.type,
      'Marks Obtained': parseFloat(a.marks_obtained).toFixed(1),
      'Max Marks': parseFloat(a.max_marks).toFixed(1),
      'Percentage': ((parseFloat(a.marks_obtained) / parseFloat(a.max_marks)) * 100).toFixed(1) + '%'
    }));
    rows.push({ Activity: 'TOTAL', Type: '', 'Marks Obtained': sub.total_obtained.toFixed(1), 'Max Marks': sub.total_max.toFixed(1), Percentage: sub.percentage + '%' });
    
    const ws = XLSX.utils.json_to_sheet(rows);
    XLSX.utils.book_append_sheet(wb, ws, sub.subject_code.substring(0, 31));
  });
  
  XLSX.writeFile(wb, `Student_Report_${s.name.replace(/\s+/g, '_')}_${s.usn}.xlsx`);
  Toast.success('Excel exported successfully.');
}

document.addEventListener('DOMContentLoaded', init);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
