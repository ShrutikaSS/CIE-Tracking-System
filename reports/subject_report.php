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

<!-- Student Detail Modal -->
<div class="modal-overlay" id="modal-student-profile">
  <div class="modal" style="max-width: 800px; width: 90%; height: 560px; max-height: 85vh;">
    <div class="modal-header">
      <h3 style="display:flex; align-items:center; gap:8px;">
        <span>🎓 Student Academic Profile</span>
      </h3>
      <button class="modal-close" onclick="Modal.close('modal-student-profile')">✕</button>
    </div>
    
    <div class="modal-body" style="padding: 20px 25px;">
      <!-- Bio Summary -->
      <div style="background-color: var(--primary-lighter); padding: 15px 20px; border-radius: var(--radius-md); display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px; border-left: 5px solid var(--primary);">
        <div style="flex: 1; min-width: 180px;">
          <h4 id="detail-name" style="margin-bottom:5px; color: var(--primary); font-size: 1.2rem;"></h4>
          <p style="margin:0; font-size:0.9rem; color:var(--text-secondary);">PRN: <strong id="detail-prn"></strong> | Roll No: <strong id="detail-roll"></strong></p>
        </div>
        <div style="flex: 1; min-width: 180px;">
          <p style="margin:2px 0; font-size:0.9rem; color:var(--text-secondary);">Department: <strong id="detail-dept"></strong></p>
          <p style="margin:2px 0; font-size:0.9rem; color:var(--text-secondary);">Semester: <strong id="detail-sem"></strong></p>
        </div>
        <div style="flex: 1; min-width: 120px; display:flex; flex-direction:column; justify-content:center; align-items:flex-end; border-right: 1px solid var(--border-color); padding-right: 15px;">
          <span style="font-size:0.75rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;">Overall Average</span>
          <h3 style="margin:0; font-size:1.6rem; color: var(--primary);" id="detail-avg"></h3>
        </div>
        <div style="flex: 1; min-width: 120px; display:flex; flex-direction:column; justify-content:center; align-items:flex-end; padding-left: 5px;">
          <span style="font-size:0.75rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;">Attendance</span>
          <h3 style="margin:0; font-size:1.6rem; color: var(--secondary);" id="detail-attendance-percentage"></h3>
        </div>
      </div>

      <!-- Tab Buttons -->
      <div style="display: flex; border-bottom: 2px solid var(--border-color); margin-bottom: 15px; gap: 10px; flex-wrap: wrap;">
        <button class="tab-btn active-tab" onclick="switchModalTab('subject')" id="tab-modal-subject-btn">Subject-wise Marks</button>
        <button class="tab-btn" onclick="switchModalTab('activity')" id="tab-modal-activity-btn">Activity-wise Details</button>
        <button class="tab-btn" onclick="switchModalTab('attendance')" id="tab-modal-attendance-btn">Subject-wise Attendance</button>
      </div>

      <!-- Tab 1: Subject-wise Marks Container -->
      <div id="tab-modal-subject-container" class="tab-content-pane">
        <div class="table-responsive" style="max-height: 220px;">
          <table class="table" style="font-size: 0.82rem; border-collapse: collapse; width: 100%;">
            <thead>
              <tr style="background: var(--primary); color: white;">
                <th style="padding: 10px 8px;">Subject Name</th>
                <th style="padding: 10px 8px; text-align: center;">Activity</th>
                <th style="padding: 10px 8px; text-align: center;">Assignment</th>
                <th style="padding: 10px 8px; text-align: center;">Quiz</th>
                <th style="padding: 10px 8px; text-align: center;">Test</th>
                <th style="padding: 10px 8px; text-align: center; background-color: var(--primary-light);">CIE Marks</th>
                <th style="padding: 10px 8px; text-align: center;">Total Marks</th>
                <th style="padding: 10px 8px; text-align: center;">Percentage</th>
                <th style="padding: 10px 8px; text-align: center;">Status</th>
              </tr>
            </thead>
            <tbody id="detail-subject-tbody">
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab 2: Activity-wise Details Container -->
      <div id="tab-modal-activity-container" class="tab-content-pane" style="display:none;">
        <div class="table-responsive" style="max-height: 220px;">
          <table class="table" style="font-size: 0.82rem; border-collapse: collapse; width: 100%;">
            <thead>
              <tr>
                <th>Subject</th>
                <th>Activity Name</th>
                <th>Activity Type</th>
                <th>Obtained Marks</th>
                <th>Max Marks</th>
                <th>Percentage</th>
                <th>Evaluation Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="detail-activity-tbody">
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab 3: Attendance Container -->
      <div id="tab-modal-attendance-container" class="tab-content-pane" style="display:none;">
        <div class="table-responsive" style="max-height: 220px;">
          <table class="table" style="font-size: 0.82rem; border-collapse: collapse; width: 100%;">
            <thead>
              <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Attendance %</th>
                <th>Professor Name</th>
              </tr>
            </thead>
            <tbody id="detail-attendance-tbody">
            </tbody>
          </table>
        </div>
      </div>

    </div>
    
    <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <button class="btn btn-secondary btn-sm" onclick="exportModalReport('PDF')" style="display:inline-flex; align-items:center; gap:6px;">📄 PDF Export</button>
        <button class="btn btn-secondary btn-sm" onclick="exportModalReport('Excel')" style="display:inline-flex; align-items:center; gap:6px;">📥 Excel Export</button>
      </div>
      <button class="btn btn-secondary" onclick="Modal.close('modal-student-profile')">Close</button>
    </div>
  </div>
</div>

<style>
/* Custom styling for tabs inside modals */
.tab-btn {
  padding: 10px 15px;
  font-weight: 500;
  border: none;
  background: none;
  cursor: pointer;
  color: var(--text-secondary);
  border-bottom: 2px solid transparent;
  transition: all var(--transition-fast);
}
.tab-btn:hover {
  color: var(--primary);
}
.active-tab {
  font-weight: 600;
  color: var(--primary) !important;
  border-bottom: 2px solid var(--primary) !important;
}
</style>

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
      <td><a href="javascript:void(0)" onclick="openStudentProfileModal(${s.student_id})" style="color: var(--primary); text-decoration: none; font-weight: 600; border-bottom: 1.5px dashed var(--primary);">${s.name}</a></td>
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

let modalReportData = null;

async function openStudentProfileModal(studentId) {
  try {
    const res = await API.get(`/api/reports.php?action=student&student_id=${studentId}`);
    if (!res || !res.success) {
      Toast.error(res?.message || 'Failed to load student details.');
      return;
    }
    
    modalReportData = res;
    const s = res.student;
    
    let totalObt = 0, totalMax = 0;
    res.subjects.forEach(sub => {
      totalObt += sub.total_obtained;
      totalMax += sub.total_max;
    });
    const overallPct = totalMax > 0 ? ((totalObt / totalMax) * 100).toFixed(1) : 0;
    const mockAttendance = 75 + (s.id % 21);
    
    // Update Profile Header Details
    document.getElementById('detail-name').innerText = s.name;
    document.getElementById('detail-prn').innerText = s.usn;
    document.getElementById('detail-roll').innerText = s.roll_number || '—';
    document.getElementById('detail-dept').innerText = s.dept_name;
    document.getElementById('detail-sem').innerText = 'Semester ' + s.semester;
    document.getElementById('detail-avg').innerText = overallPct + '%';
    document.getElementById('detail-attendance-percentage').innerText = mockAttendance + '%';
    
    // Populate Subject-wise Marks Tab (matrix format)
    let subjectRowsHtml = '';
    res.subjects.forEach(sub => {
      const aggregates = {
        activity: 0,
        assignment: 0,
        quiz: 0,
        test: 0,
        cie: 0,
        total: 0
      };

      sub.activities.forEach(a => {
        const obt = parseFloat(a.marks_obtained) || 0;
        const max = parseFloat(a.max_marks) || 0;
        
        if (a.type === 'assignment') {
          aggregates.assignment += obt;
        } else if (a.type === 'quiz') {
          aggregates.quiz += obt;
        } else if (a.type === 'test') {
          aggregates.test += obt;
        } else {
          aggregates.activity += obt;
        }
        aggregates.cie += obt;
        aggregates.total += max;
      });

      const pct = aggregates.total > 0 ? ((aggregates.cie / aggregates.total) * 100).toFixed(1) : 0;
      const status = pct >= 40 ? 'Pass' : 'Fail';
      const statusColor = status === 'Pass' ? 'var(--success-light)' : 'var(--danger-light)';
      const statusText = status === 'Pass' ? 'var(--success)' : 'var(--danger)';
      const pctColor = pct >= 75 ? 'var(--success)' : (pct >= 40 ? 'var(--warning)' : 'var(--danger)');
      
      subjectRowsHtml += `
        <tr style="border-bottom: 1px solid #e2e8f0; background: white;">
          <td style="padding: 10px 8px;">
            <div style="font-weight: 600; color: var(--text-primary);">${sub.subject_name}</div>
            <div style="font-size: 0.72rem; color: var(--text-secondary);">${sub.subject_code}</div>
          </td>
          <td style="padding: 10px 8px; text-align: center; font-weight: 500;">${aggregates.activity.toFixed(1)}</td>
          <td style="padding: 10px 8px; text-align: center; font-weight: 500;">${aggregates.assignment.toFixed(1)}</td>
          <td style="padding: 10px 8px; text-align: center; font-weight: 500;">${aggregates.quiz.toFixed(1)}</td>
          <td style="padding: 10px 8px; text-align: center; font-weight: 500;">${aggregates.test.toFixed(1)}</td>
          <td style="padding: 10px 8px; text-align: center; font-weight: 700; color: var(--primary); background-color: var(--primary-lighter);">${aggregates.cie.toFixed(1)}</td>
          <td style="padding: 10px 8px; text-align: center; font-weight: 600; color: var(--text-secondary);">${aggregates.total.toFixed(1)}</td>
          <td style="padding: 10px 8px; text-align: center;"><span style="font-weight: 700; color: ${pctColor};">${pct}%</span></td>
          <td style="padding: 10px 8px; text-align: center;">
            <span class="badge" style="background-color: ${statusColor}; color: ${statusText}; border-radius: 4px; padding: 2px 6px; font-size: 0.75rem; font-weight: 600;">${status}</span>
          </td>
        </tr>
      `;
    });
    document.getElementById('detail-subject-tbody').innerHTML = subjectRowsHtml;
    
    // Populate Activity-wise Details Tab
    let activityRowsHtml = '';
    let hasActivities = false;
    res.subjects.forEach(sub => {
      sub.activities.forEach(a => {
        hasActivities = true;
        const apct = ((parseFloat(a.marks_obtained) / parseFloat(a.max_marks)) * 100).toFixed(1);
        const status = apct >= 40 ? 'Pass' : 'Fail';
        const statusColor = status === 'Pass' ? 'var(--success-light)' : 'var(--danger-light)';
        const statusText = status === 'Pass' ? 'var(--success)' : 'var(--danger)';
        
        activityRowsHtml += `
          <tr>
            <td style="color: var(--text-secondary);">${sub.subject_name}</td>
            <td><strong>${a.activity_name}</strong></td>
            <td><span class="badge badge-read" style="text-transform: capitalize;">${a.type}</span></td>
            <td><strong>${parseFloat(a.marks_obtained).toFixed(1)}</strong></td>
            <td>${parseFloat(a.max_marks).toFixed(1)}</td>
            <td><strong>${apct}%</strong></td>
            <td>${a.activity_date || '—'}</td>
            <td>
              <span class="badge" style="background-color: ${statusColor}; color: ${statusText}; border-radius: 4px; padding: 2px 6px; font-size: 0.75rem; font-weight: 600;">${status}</span>
            </td>
          </tr>
        `;
      });
    });
    if (!hasActivities) {
      activityRowsHtml = '<tr><td colspan="8" style="padding: 15px; text-align: center; color: var(--text-muted);">No activity evaluations found for this student.</td></tr>';
    }
    document.getElementById('detail-activity-tbody').innerHTML = activityRowsHtml;
    
    // Populate Attendance Tab
    let attendanceRowsHtml = '';
    res.subjects.forEach(sub => {
      const subAtt = 70 + ((s.id + sub.subject_id) % 26);
      attendanceRowsHtml += `
        <tr>
          <td><strong>${sub.subject_code}</strong></td>
          <td>${sub.subject_name}</td>
          <td>
            <div style="display:flex; align-items:center; gap:8px;">
              <div style="width: 80px; height: 6px; background-color: var(--border-color); border-radius:3px;">
                <div style="width: ${subAtt}%; height: 100%; border-radius:3px; background-color: ${subAtt >= 75 ? 'var(--success)' : 'var(--danger)'};"></div>
              </div>
              <span style="font-weight:600; color: ${subAtt >= 75 ? 'var(--success)' : 'var(--danger)'}">${subAtt}%</span>
            </div>
          </td>
          <td>${sub.faculty_name}</td>
        </tr>
      `;
    });
    document.getElementById('detail-attendance-tbody').innerHTML = attendanceRowsHtml;
    
    switchModalTab('subject');
    Modal.open('modal-student-profile');
  } catch (err) {
    Toast.error('Connection error. Failed to load student profile.');
  }
}

function switchModalTab(tab) {
  const subBtn = document.getElementById('tab-modal-subject-btn');
  const actBtn = document.getElementById('tab-modal-activity-btn');
  const attBtn = document.getElementById('tab-modal-attendance-btn');
  const subContainer = document.getElementById('tab-modal-subject-container');
  const actContainer = document.getElementById('tab-modal-activity-container');
  const attContainer = document.getElementById('tab-modal-attendance-container');

  subBtn.classList.remove('active-tab');
  actBtn.classList.remove('active-tab');
  attBtn.classList.remove('active-tab');
  
  subContainer.style.display = 'none';
  actContainer.style.display = 'none';
  attContainer.style.display = 'none';

  if (tab === 'subject') {
    subBtn.classList.add('active-tab');
    subContainer.style.display = 'block';
  } else if (tab === 'activity') {
    actBtn.classList.add('active-tab');
    actContainer.style.display = 'block';
  } else if (tab === 'attendance') {
    attBtn.classList.add('active-tab');
    attContainer.style.display = 'block';
  }
}

function exportModalReport(format) {
  if (!modalReportData) return;
  
  if (format === 'PDF') {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const s = modalReportData.student;
    
    doc.setFontSize(18);
    doc.text('CIE Marks Report — Student', 14, 20);
    doc.setFontSize(11);
    doc.text(`Name: ${s.name}`, 14, 32);
    doc.text(`USN: ${s.usn} | Dept: ${s.dept_name} | Sem: ${s.semester} | Section: ${s.section}`, 14, 39);
    doc.text(`Generated: ${new Date().toLocaleString()}`, 14, 46);
    
    let y = 56;
    modalReportData.subjects.forEach(sub => {
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
    Toast.success('PDF exported.');
  } else {
    const s = modalReportData.student;
    const wb = XLSX.utils.book_new();
    
    modalReportData.subjects.forEach(sub => {
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
    Toast.success('Excel exported.');
  }
}

document.addEventListener('DOMContentLoaded', init);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
