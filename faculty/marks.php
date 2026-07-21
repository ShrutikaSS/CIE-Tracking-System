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
    <div class="d-flex gap-1">
      <button class="btn btn-success" onclick="saveMarks()">💾 Save Marks</button>
      <button class="btn btn-primary" onclick="publishMarks()">📢 Publish</button>
    </div>
  </div>
</div>

<!-- Marks Table -->
<div class="card hidden" id="marks-card">
  <div class="card-body p-0">
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th style="width:50px">#</th>
            <th>USN</th>
            <th>Student Name</th>
            <th>Section</th>
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

<script>
let currentActivity = null;
let maxMarks = 0;

async function init() {
  const res = await API.get('/api/subjects.php?for_faculty=1');
  if (res && res.success) {
    document.getElementById('select-subject').innerHTML = '<option value="">— Choose Subject —</option>' +
      res.subjects.map(s => `<option value="${s.id}">${s.code} — ${s.name}</option>`).join('');
  }
  
  // Auto-select from URL
  const params = new URLSearchParams(window.location.search);
  const actParam = params.get('activity');
  if (actParam) {
    // Load the activity to find its subject
    const actRes = await API.get(`/api/activities.php?id=${actParam}`);
    if (actRes && actRes.success) {
      document.getElementById('select-subject').value = actRes.activity.subject_id;
      await loadActivitiesForSubject();
      document.getElementById('select-activity').value = actParam;
      loadMarksForm();
    }
  }
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
  
  // Activity Info
  document.getElementById('activity-info-card').classList.remove('hidden');
  document.getElementById('activity-name').textContent = currentActivity.name;
  document.getElementById('activity-meta').innerHTML = `
    Type: <span class="badge badge-primary">${currentActivity.type}</span> &nbsp;
    Max Marks: <strong>${maxMarks}</strong> &nbsp;
    Status: <span class="badge ${currentActivity.status === 'completed' ? 'badge-success' : 'badge-primary'}">${currentActivity.status}</span>
  `;
  
  // Marks Table
  document.getElementById('marks-card').classList.remove('hidden');
  const tbody = document.getElementById('marks-tbody');
  
  if (res.students.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><div class="icon">🎓</div><h3>No students enrolled</h3></div></td></tr>';
    return;
  }
  
  tbody.innerHTML = res.students.map((s, i) => {
    const marks = s.marks_obtained !== null ? parseFloat(s.marks_obtained).toFixed(1) : '';
    const published = s.is_published == 1;
    
    return `<tr data-student-id="${s.student_id}">
      <td class="text-muted">${i + 1}</td>
      <td><span class="badge badge-info">${s.usn}</span></td>
      <td><strong>${s.student_name}</strong></td>
      <td>${s.section}</td>
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

async function saveMarks() {
  if (!currentActivity) { Toast.warning('Please select an activity.'); return; }
  
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
    return;
  }
  
  const res = await API.post(`/api/marks.php?action=save`, {
    activity_id: parseInt(currentActivity.id),
    marks: marksData
  });
  
  if (res && res.success) {
    Toast.success(res.message);
    if (res.errors && res.errors.length > 0) {
      res.errors.forEach(e => Toast.warning(e));
    }
  } else {
    Toast.error(res?.message || 'Failed to save marks.');
  }
}

async function publishMarks() {
  if (!currentActivity) { Toast.warning('Please select an activity.'); return; }
  if (!confirm('Publish all marks for this activity? Students will be notified.')) return;
  
  // Save first, then publish
  await saveMarks();
  
  const res = await API.post('/api/marks.php?action=publish', {
    activity_id: parseInt(currentActivity.id)
  });
  
  if (res && res.success) {
    Toast.success(res.message);
    loadMarksForm(); // Reload to show updated status
  } else {
    Toast.error(res?.message || 'Failed to publish.');
  }
}

document.addEventListener('DOMContentLoaded', init);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
