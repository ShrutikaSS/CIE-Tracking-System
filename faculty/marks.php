<?php
$pageTitle = 'Marks Entry';
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'hod', 'faculty', 'coordinator']);
?>

<div class="page-header">
  <div>
    <h1>Marks Entry</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / <a href="/faculty/activities.php">Activities</a> / Marks Entry</div>
  </div>
</div>

<!-- Activity Selector -->
<div class="card mb-3">
  <div class="card-body">
    <div class="form-row">
      <?php if (in_array($_SESSION['user_role'], ['admin', 'hod'])): ?>
        <div class="form-group mb-0">
          <label>Select Department</label>
          <select class="form-control" id="select-department" onchange="loadSubjectsForDepartment()">
            <option value="">— Choose Department —</option>
          </select>
        </div>
        <div class="form-group mb-0">
          <label>Select Subject</label>
          <select class="form-control" id="select-subject" onchange="loadCIESummaryOnly()">
            <option value="">— Choose Subject —</option>
          </select>
        </div>
      <?php else: ?>
        <div class="form-group mb-0">
          <label>Select Subject</label>
          <select class="form-control" id="select-subject" onchange="loadActivitiesForSubject()">
            <option value="">— Choose Subject —</option>
          </select>
        </div>
        <div class="form-group mb-0">
          <label>Select Activity</label>
          <select class="form-control" id="select-activity" onchange="loadMarksForm()">
            <option value="">— Choose Activity —</option>
          </select>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Activity Info -->
<div class="card mb-3 hidden" id="activity-info-card">
  <div class="card-body d-flex justify-between align-center" style="gap:20px;flex-wrap:wrap">
    <div>
      <h3 id="activity-name" style="margin-bottom:4px"></h3>
      <div class="text-muted" id="activity-meta"></div>
    </div>
    <div class="d-flex gap-2" style="align-items:center;">
      <button class="btn btn-outline-primary" onclick="autoFillMarks()" style="display:inline-flex; align-items:center; gap:6px;" title="Fills marks from auto-calculated submission values">
        ⚡ Use Default Marks
      </button>
      <button class="btn btn-outline" onclick="saveMarks(false)" style="display:inline-flex; align-items:center; gap:6px;">
        💾 Save Draft
      </button>
      <button class="btn btn-primary" onclick="submitAndPublishMarks()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; padding:10px 18px; box-shadow: 0 2px 8px rgba(26,115,232,0.3);">
        🚀 Submit & Publish Marks to Students
      </button>
    </div>
  </div>
</div>

<!-- Marks Table -->
<div class="card hidden" id="marks-card">
  <div class="card-header d-flex justify-between align-center" style="flex-wrap:wrap; gap:12px;">
    <h3 style="margin:0;">Student Evaluations</h3>
    <div class="filter-bar" style="margin:0;">
      <select class="form-control" id="filter-submission-status" onchange="renderMarksTable()" style="font-size:0.85rem; padding: 4px 8px;">
        <option value="all">All Students</option>
        <option value="submitted">Submitted Only</option>
        <option value="pending">Pending Only</option>
      </select>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th style="width:40px">#</th>
            <th>USN</th>
            <th>Student Name</th>
            <th>Section</th>
            <th>Submission & File</th>
            <th style="width:120px">Marks</th>
            <th>Remarks</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="marks-tbody"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- CIE Summary Card -->
<div class="card hidden" id="cie-summary-card" style="margin-top: 24px; border-top: 4px solid var(--primary);">
  <div class="card-header">
    <h3>CIE Summary (Subject Level)</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th style="width:40px">#</th>
            <th>USN</th>
            <th>Student Name</th>
            <th>Total Activity Marks (out of 60)</th>
            <th>Converted Final CIE (out of 20)</th>
          </tr>
        </thead>
        <tbody id="cie-summary-tbody"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Submission Text View Modal -->
<div class="modal-overlay" id="modal-submission-text">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-student-name">Student Submission</h3>
      <button class="modal-close" onclick="Modal.close('modal-submission-text')">✕</button>
    </div>
    <div class="modal-body">
      <label style="font-weight:600; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Submission Notes / Text</label>
      <div id="modal-text-content" style="margin-top:6px; padding:12px; background:var(--bg-light); border-radius:6px; font-size:0.875rem; white-space:pre-wrap; line-height:1.5; border:1px solid var(--border-color);"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-submission-text')">Close</button>
    </div>
  </div>
</div>

<script>
const userRole = '<?= $_SESSION['user_role'] ?>';
const userDeptId = '<?= $_SESSION['department_id'] ?? '' ?>';
let currentActivity = null;
let maxMarks = 0;
let rawStudentsData = [];

async function init() {
  if (userRole === 'admin' || userRole === 'hod') {
    const res = await API.get('/api/departments.php');
    if (res && res.success) {
      const deptSelect = document.getElementById('select-department');
      deptSelect.innerHTML = '<option value="">— Choose Department —</option>' +
        res.departments.map(d => `<option value="${d.id}">${d.name} (${d.code})</option>`).join('');
      
      // Auto-select department if set
      if (userDeptId) {
        deptSelect.value = userDeptId;
        await loadSubjectsForDepartment();
      }
    }
  } else {
    const res = await API.get('/api/subjects.php?for_faculty=1');
    if (res && res.success) {
      document.getElementById('select-subject').innerHTML = '<option value="">— Choose Subject —</option>' +
        res.subjects.map(s => `<option value="${s.id}">${s.code} — ${s.name}</option>`).join('');
    }
    
    // Auto-select from URL
    const params = new URLSearchParams(window.location.search);
    const actParam = params.get('activity');
    if (actParam) {
      const actRes = await API.get(`/api/activities.php?id=${actParam}`);
      if (actRes && actRes.success) {
        document.getElementById('select-subject').value = actRes.activity.subject_id;
        await loadActivitiesForSubject();
        document.getElementById('select-activity').value = actParam;
        loadMarksForm();
      }
    }
  }
}

async function loadSubjectsForDepartment() {
  const deptId = document.getElementById('select-department').value;
  const subSelect = document.getElementById('select-subject');
  
  if (!deptId) {
    subSelect.innerHTML = '<option value="">— Choose Subject —</option>';
    document.getElementById('cie-summary-card').classList.add('hidden');
    return;
  }
  
  const res = await API.get(`/api/subjects.php?department=${deptId}`);
  if (res && res.success) {
    subSelect.innerHTML = '<option value="">— Choose Subject —</option>' +
      res.subjects.map(s => `<option value="${s.id}">${s.code} — ${s.name}</option>`).join('');
  }
}

async function loadCIESummaryOnly() {
  const subjectId = document.getElementById('select-subject').value;
  if (!subjectId) {
    document.getElementById('cie-summary-card').classList.add('hidden');
    return;
  }
  await loadCIESummary();
}

async function loadActivitiesForSubject() {
  const subjectId = document.getElementById('select-subject').value;
  const actSelect = document.getElementById('select-activity');
  
  if (!subjectId) {
    actSelect.innerHTML = '<option value="">— Choose Activity —</option>';
    return;
  }
  
  const res = await API.get(`/api/activities.php?subject_id=${subjectId}`);
  if (res && res.success) {
    actSelect.innerHTML = '<option value="">— Choose Activity —</option>' +
      res.activities.map(a => `<option value="${a.id}">${a.name} (${a.type}, max: ${parseFloat(a.max_marks).toFixed(0)})</option>`).join('');
  }
}

async function loadMarksForm() {
  const activityId = document.getElementById('select-activity').value;
  if (!activityId) {
    document.getElementById('activity-info-card').classList.add('hidden');
    document.getElementById('marks-card').classList.add('hidden');
    return;
  }
  
  const res = await API.get(`/api/marks.php?action=students_for_activity&activity_id=${activityId}`);
  if (!res || !res.success) return;
  
  currentActivity = res.activity;
  maxMarks = parseFloat(currentActivity.max_marks);
  rawStudentsData = res.students || [];
  
  const submittedCount = rawStudentsData.filter(s => s.submission_id).length;
  
  // Activity Info
  document.getElementById('activity-info-card').classList.remove('hidden');
  document.getElementById('activity-name').textContent = currentActivity.name;
  document.getElementById('activity-meta').innerHTML = `
    Type: <span class="badge badge-primary">${currentActivity.type}</span> &nbsp;
    Max Marks: <strong>${maxMarks}</strong> &nbsp;
    Submissions: <span class="badge badge-info">${submittedCount} / ${rawStudentsData.length} Submitted</span> &nbsp;
    Status: <span class="badge ${currentActivity.status === 'completed' ? 'badge-success' : 'badge-primary'}">${currentActivity.status}</span>
  `;
  
  // Marks Table
  document.getElementById('marks-card').classList.remove('hidden');
  renderMarksTable();
  
  // Load CIE Summary for the subject
  loadCIESummary();
}

async function loadCIESummary() {
  const subjectId = document.getElementById('select-subject').value;
  if (!subjectId) return;
  
  const res = await API.get(`/api/marks.php?action=cie_summary&subject_id=${subjectId}`);
  if (!res || !res.success) return;
  
  document.getElementById('cie-summary-card').classList.remove('hidden');
  const tbody = document.getElementById('cie-summary-tbody');
  
  if (res.summary.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center">No students found.</td></tr>';
    return;
  }
  
  tbody.innerHTML = res.summary.map((s, i) => {
    return `<tr>
      <td class="text-muted">${i + 1}</td>
      <td><span class="badge badge-info">${s.usn}</span></td>
      <td><strong>${s.student_name}</strong></td>
      <td><strong>${parseFloat(s.total_out_of_60).toFixed(2)}</strong></td>
      <td>
        <span class="badge badge-primary" style="font-size:1rem; padding: 4px 10px;">
          ${parseFloat(s.cie_out_of_20).toFixed(2)} / 20
        </span>
      </td>
    </tr>`;
  }).join('');
}

function renderMarksTable() {
  const tbody = document.getElementById('marks-tbody');
  const filter = document.getElementById('filter-submission-status').value;
  
  let students = rawStudentsData;
  if (filter === 'submitted') {
    students = rawStudentsData.filter(s => s.submission_id);
  } else if (filter === 'pending') {
    students = rawStudentsData.filter(s => !s.submission_id);
  }
  
  if (students.length === 0) {
    tbody.innerHTML = '<tr><td colspan="8"><div class="empty-state"><div class="icon">🎓</div><h3>No students found</h3><p>No students match the selected filter.</p></div></td></tr>';
    return;
  }
  
  tbody.innerHTML = students.map((s, i) => {
    const marks = s.marks_obtained !== null ? parseFloat(s.marks_obtained).toFixed(1) : '';
    const published = s.is_published == 1;
    const hasSubmission = !!s.submission_id;
    
    let submissionCell = '';
    if (hasSubmission) {
      const subTime = s.submitted_at ? new Date(s.submitted_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Submitted';
      submissionCell = `<div style="display:flex; flex-direction:column; gap:4px;">
        <div><span class="badge badge-success" style="font-size:0.75rem;">Submitted</span> <small class="text-muted" style="font-size:0.75rem;">${subTime}</small></div>
        <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:2px;">`;
      
      if (s.file_path) {
        submissionCell += `<a href="${s.file_path}" target="_blank" class="btn btn-sm btn-outline-primary" style="padding:2px 8px; font-size:0.75rem; display:inline-flex; align-items:center; gap:4px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
          View File
        </a>`;
      }
      
      if (s.submission_text) {
        submissionCell += `<button type="button" onclick="viewSubmissionText('${s.student_name.replace(/'/g, "\\'")}', '${encodeURIComponent(s.submission_text)}')" class="btn btn-sm btn-outline-secondary" style="padding:2px 8px; font-size:0.75rem; display:inline-flex; align-items:center; gap:4px;">
          💬 View Text
        </button>`;
      }
      
      submissionCell += `</div></div>`;
    } else {
      submissionCell = `<span class="badge badge-secondary" style="font-size:0.75rem;">Pending</span>`;
    }
    
    return `<tr data-student-id="${s.student_id}">
      <td class="text-muted">${i + 1}</td>
      <td><span class="badge badge-info">${s.usn}</span></td>
      <td><strong>${s.student_name}</strong></td>
      <td>${s.section}</td>
      <td>${submissionCell}</td>
      <td>
        <input type="number" class="form-control marks-input" 
               value="${marks}" 
               min="0" max="${maxMarks}" step="0.5"
               data-student="${s.student_id}"
               onchange="validateMark(this)"
               placeholder="—">
      </td>
      <td>
        <input type="text" class="form-control" style="font-size:0.8125rem"
               value="${s.remarks || ''}"
               data-remarks="${s.student_id}"
               placeholder="Optional remarks">
      </td>
      <td>${published ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-secondary">Draft</span>'}</td>
    </tr>`;
  }).join('');
}

function viewSubmissionText(studentName, encodedText) {
  const text = decodeURIComponent(encodedText);
  document.getElementById('modal-student-name').textContent = `Submission from ${studentName}`;
  document.getElementById('modal-text-content').textContent = text;
  Modal.open('modal-submission-text');
}

function validateMark(input) {
  const val = parseFloat(input.value);
  if (isNaN(val)) return;
  
  if (val > maxMarks) {
    input.classList.add('over-max');
    Toast.warning(`Marks cannot exceed ${maxMarks}.`);
  } else if (val < 0) {
    input.classList.add('over-max');
    Toast.warning('Marks cannot be negative.');
  } else {
    input.classList.remove('over-max');
  }
}

async function saveMarks(showToast = true) {
  if (!currentActivity) { Toast.warning('Please select an activity.'); return false; }
  
  const marksData = [];
  document.querySelectorAll('[data-student]').forEach(input => {
    const studentId = input.dataset.student;
    const marks = input.value.trim();
    const remarks = document.querySelector(`[data-remarks="${studentId}"]`).value.trim();
    
    marksData.push({
      student_id: parseInt(studentId),
      marks: marks !== '' ? parseFloat(marks) : null,
      remarks: remarks
    });
  });
  
  // Check for over-max
  const hasOverMax = document.querySelector('.marks-input.over-max');
  if (hasOverMax) {
    Toast.error('Fix marks exceeding maximum before saving.');
    return false;
  }
  
  const res = await API.post(`/api/marks.php?action=save`, {
    activity_id: parseInt(currentActivity.id),
    marks: marksData
  });
  
  if (res && res.success) {
    if (showToast) Toast.success(res.message);
    if (res.errors && res.errors.length > 0) {
      res.errors.forEach(e => Toast.warning(e));
    }
    return true;
  } else {
    Toast.error(res?.message || 'Failed to save marks.');
    return false;
  }
}

async function autoFillMarks() {
  if (!currentActivity) { Toast.warning('Please select an activity.'); return; }
  
  if (!confirm('This will fill student marks with auto-calculated values based on submission time and rules. Overwrite existing marks?')) {
    return;
  }
  
  showLoading();
  const res = await API.post('/api/marks.php?action=auto_fill', {
    activity_id: parseInt(currentActivity.id)
  });
  
  hideLoading();
  
  if (res && res.success) {
    Toast.success(res.message);
    await loadMarksForm(); // Reload form to show filled marks
  } else {
    Toast.error(res?.message || 'Failed to auto-fill marks.');
  }
}

async function submitAndPublishMarks() {
  if (!currentActivity) { Toast.warning('Please select an activity.'); return; }
  
  showLoading();
  const saved = await saveMarks(false);
  if (!saved) {
    hideLoading();
    return;
  }
  
  const res = await API.post('/api/marks.php?action=publish', {
    activity_id: parseInt(currentActivity.id)
  });
  
  hideLoading();
  
  if (res && res.success) {
    Toast.success('🚀 Marks submitted and published! Students can now view their marks.');
    await loadMarksForm(); // Reload table to reflect published status
  } else {
    Toast.error(res?.message || 'Failed to publish marks.');
  }
}

async function publishMarks() {
  return submitAndPublishMarks();
}

document.addEventListener('DOMContentLoaded', init);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
