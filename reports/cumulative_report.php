<?php
$pageTitle = 'Cumulative Report Card';
require_once __DIR__ . '/../includes/header.php';
requireLogin();
?>

<div class="page-header">
  <div>
    <h1>Cumulative Report Card</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Cumulative Report</div>
  </div>
</div>

<!-- Student Selector -->
<div class="card mb-3">
  <div class="card-body">
    <div class="form-row">
      <div class="form-group mb-0">
        <label>Select Student</label>
        <select class="form-control" id="select-student" onchange="loadCumulativeReport()">
          <option value="">— Choose Student —</option>
        </select>
      </div>
      <div class="form-group mb-0 d-flex align-center gap-1 flex-wrap" style="align-self:flex-end; width:100%;">
        <button class="btn btn-primary" onclick="exportCumulativePDF()" id="btn-pdf" disabled style="flex:1;">📄 Export Cumulative PDF</button>
        <button class="btn btn-secondary" onclick="window.print()" id="btn-print" disabled style="flex:1;">🖨️ Print Report</button>
      </div>
    </div>
  </div>
</div>

<!-- Report Content -->
<div id="cumulative-content" class="hidden">
  <!-- Student Info -->
  <div class="card mb-3" id="report-header-card">
    <div class="card-body d-flex justify-between align-center" style="flex-wrap:wrap;gap:16px">
      <div>
        <h3 id="cum-report-name" style="margin-bottom:4px"></h3>
        <div class="text-muted" id="cum-report-meta"></div>
      </div>
      <div id="cum-report-avg-badge"></div>
    </div>
  </div>

  <!-- Summary Table -->
  <div class="card mb-3" id="cum-summary-card">
    <div class="card-header"><h3>Subject Summary</h3></div>
    <div class="card-body p-0">
      <div class="table-container">
        <table>
          <thead>
            <tr><th>Code</th><th>Subject</th><th>Obtained</th><th>Max</th><th>%</th><th>Activities</th></tr>
          </thead>
          <tbody id="cum-summary-tbody"></tbody>
          <tfoot id="cum-summary-tfoot"></tfoot>
        </table>
      </div>
    </div>
  </div>

  <!-- Per-Subject Detail -->
  <div id="cum-detail-container"></div>
</div>

<script>
let cumData = null;

async function init() {
  const role = '<?= $_SESSION["user_role"] ?>';
  let url = '/api/students.php';
  if (role === 'student') {
    // Load own report automatically
    const meRes = await API.get('/api/reports.php?action=student&student_id=me');
    if (meRes && meRes.success) {
      cumData = meRes;
      renderCumulative();
    }
    document.querySelector('.card.mb-3:first-of-type').style.display = 'none'; // hide selector
    return;
  }

  const res = await API.get(url);
  if (res && res.success) {
    document.getElementById('select-student').innerHTML = '<option value="">— Choose Student —</option>' +
      res.students.map(s => `<option value="${s.id}">${s.usn} — ${s.name} (${s.dept_code})</option>`).join('');
  }
}

async function loadCumulativeReport() {
  const studentId = document.getElementById('select-student').value;
  if (!studentId) {
    document.getElementById('cumulative-content').classList.add('hidden');
    document.getElementById('btn-pdf').disabled = true;
    document.getElementById('btn-print').disabled = true;
    return;
  }

  const res = await API.get(`/api/reports.php?action=student&student_id=${studentId}`);
  if (!res || !res.success) return;

  cumData = res;
  renderCumulative();
}

function renderCumulative() {
  if (!cumData) return;
  document.getElementById('cumulative-content').classList.remove('hidden');
  document.getElementById('btn-pdf').disabled = false;
  document.getElementById('btn-print').disabled = false;

  const s = cumData.student;
  document.getElementById('cum-report-name').textContent = s.name;
  document.getElementById('cum-report-meta').innerHTML = `USN: <strong>${s.usn}</strong> | Dept: <strong>${s.dept_name}</strong> | Sem: <strong>${s.semester}</strong> | Section: <strong>${s.section}</strong>`;

  let totalObt = 0, totalMax = 0, totalAct = 0;
  cumData.subjects.forEach(sub => { totalObt += sub.total_obtained; totalMax += sub.total_max; totalAct += sub.activities.length; });
  const overallPct = totalMax > 0 ? ((totalObt / totalMax) * 100).toFixed(1) : 0;
  document.getElementById('cum-report-avg-badge').innerHTML = `<span class="badge ${overallPct >= 75 ? 'badge-success' : overallPct >= 50 ? 'badge-primary' : 'badge-warning'}" style="font-size:1.1rem;padding:10px 20px">Overall: ${overallPct}%</span>`;

  // Summary table
  document.getElementById('cum-summary-tbody').innerHTML = cumData.subjects.map(sub => `
    <tr>
      <td><span class="badge badge-primary">${sub.subject_code}</span></td>
      <td><strong>${sub.subject_name}</strong></td>
      <td>${sub.total_obtained.toFixed(1)}</td>
      <td>${sub.total_max.toFixed(1)}</td>
      <td><strong>${sub.percentage}%</strong></td>
      <td>${sub.activities.length}</td>
    </tr>
  `).join('');

  document.getElementById('cum-summary-tfoot').innerHTML = `
    <tr style="font-weight:700; background:var(--bg-input);">
      <td colspan="2">Grand Total</td>
      <td>${totalObt.toFixed(1)}</td>
      <td>${totalMax.toFixed(1)}</td>
      <td><strong>${overallPct}%</strong></td>
      <td>${totalAct}</td>
    </tr>
  `;

  // Detail cards
  let detailHTML = '';
  cumData.subjects.forEach(sub => {
    const pct = sub.percentage;
    detailHTML += `
      <div class="card mb-3">
        <div class="card-header">
          <div><h3 style="font-size:0.9375rem">${sub.subject_code} — ${sub.subject_name}</h3></div>
          <span class="badge ${pct >= 75 ? 'badge-success' : pct >= 50 ? 'badge-primary' : 'badge-warning'}">${pct}%</span>
        </div>
        <div class="card-body p-0">
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
                  <td colspan="2">Subject Total</td>
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
  document.getElementById('cum-detail-container').innerHTML = detailHTML;
}

function exportCumulativePDF() {
  if (!cumData) return;
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  const s = cumData.student;
  doc.setFontSize(18);
  doc.text('CIE Cumulative Report Card', 14, 20);
  doc.setFontSize(11);
  doc.text(`Name: ${s.name}`, 14, 32);
  doc.text(`USN: ${s.usn} | Dept: ${s.dept_name} | Sem: ${s.semester} | Section: ${s.section}`, 14, 39);
  doc.text(`Generated: ${new Date().toLocaleString()}`, 14, 46);

  // Summary table
  let totalObt = 0, totalMax = 0;
  cumData.subjects.forEach(sub => { totalObt += sub.total_obtained; totalMax += sub.total_max; });
  const overallPct = totalMax > 0 ? ((totalObt / totalMax) * 100).toFixed(1) : 0;

  const summaryRows = cumData.subjects.map(sub => [
    sub.subject_code, sub.subject_name,
    sub.total_obtained.toFixed(1), sub.total_max.toFixed(1),
    sub.percentage + '%', sub.activities.length.toString()
  ]);
  summaryRows.push(['', 'Grand Total', totalObt.toFixed(1), totalMax.toFixed(1), overallPct + '%', '']);

  doc.autoTable({
    startY: 54,
    head: [['Code', 'Subject', 'Obtained', 'Max', '%', 'Activities']],
    body: summaryRows,
    theme: 'striped',
    headStyles: { fillColor: [79, 70, 229] },
    styles: { fontSize: 9 }
  });

  let y = doc.lastAutoTable.finalY + 12;

  // Per-subject detail
  cumData.subjects.forEach(sub => {
    if (y > 250) { doc.addPage(); y = 20; }

    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.text(`${sub.subject_code} — ${sub.subject_name} (${sub.percentage}%)`, 14, y);
    y += 4;

    const rows = sub.activities.map(a => [
      a.activity_name, a.type, parseFloat(a.marks_obtained).toFixed(1),
      parseFloat(a.max_marks).toFixed(1),
      ((parseFloat(a.marks_obtained) / parseFloat(a.max_marks)) * 100).toFixed(1) + '%'
    ]);
    rows.push(['Total', '', sub.total_obtained.toFixed(1), sub.total_max.toFixed(1), sub.percentage + '%']);

    doc.autoTable({
      startY: y,
      head: [['Activity', 'Type', 'Obtained', 'Max', '%']],
      body: rows,
      theme: 'striped',
      headStyles: { fillColor: [99, 102, 241] },
      margin: { left: 14 },
      styles: { fontSize: 8 }
    });

    y = doc.lastAutoTable.finalY + 10;
  });

  doc.save(`Cumulative_Report_${s.name.replace(/\s+/g, '_')}_${s.usn}.pdf`);
  Toast.success('Cumulative PDF exported.');
}

document.addEventListener('DOMContentLoaded', init);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
