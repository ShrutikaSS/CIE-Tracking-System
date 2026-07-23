<?php
$pageTitle = 'Activities';
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'hod', 'faculty', 'coordinator']);
?>

<div class="page-header">
  <div>
    <h1>CIE Activities</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Activities</div>
  </div>
  <div class="actions">
    <button class="btn btn-primary" onclick="openAddActivity()">+ Create Activity</button>
  </div>
</div>

<div class="card">
  <div class="card-header" style="flex-wrap:wrap;gap:12px;">
    <h3>All Activities</h3>
    <div class="filter-bar" style="margin:0">
      <select class="form-control" id="filter-subject" onchange="loadActivities()">
        <option value="">All Subjects</option>
      </select>
      <select class="form-control" id="filter-type" onchange="loadActivities()">
        <option value="">All Types</option>
        <option value="assignment">Assignment</option>
        <option value="quiz">Quiz</option>
        <option value="test">Test</option>
        <option value="seminar">Seminar</option>
        <option value="viva">Viva</option>
        <option value="practical">Practical</option>
        <option value="project_review">Project Review</option>
        <option value="presentation">Presentation</option>
      </select>
      <select class="form-control" id="filter-status" onchange="loadActivities()">
        <option value="">All Status</option>
        <option value="draft">Draft</option>
        <option value="active">Active</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <div class="search-filter">
        <span class="icon">🔍</span>
        <input type="text" id="search-act" placeholder="Search activities..." oninput="debounce(loadActivities, 400)()">
      </div>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-container">
      <table id="act-table" data-sortable>
        <thead>
          <tr>
            <th>Unit</th>
            <th>Activity</th>
            <th>Subject</th>
            <th>Type</th>
            <th>Max Marks</th>
            <th>Time Window</th>
            <th>Auto Status</th>
            <th>Status</th>
            <th>Marks</th>
            <th data-sortable="false">Actions</th>
          </tr>
        </thead>
        <tbody id="act-tbody">
          <tr><td colspan="9" class="text-center text-muted" style="padding:40px">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal-overlay" id="modal-activity">
  <div class="modal lg">
    <div class="modal-header">
      <h3 id="modal-act-title">Create Activity</h3>
      <button class="modal-close" onclick="Modal.close('modal-activity')">✕</button>
    </div>
    <div class="modal-body">
      <form id="form-activity">
        <input type="hidden" id="act-id">
        <div class="form-group">
          <label>Activity Name *</label>
          <input type="text" class="form-control" id="act-name" data-required placeholder="e.g. Assignment 1: UML Diagrams">
          <div class="form-error"></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Subject *</label>
            <select class="form-control" id="act-subject" data-required>
              <option value="">Select Subject</option>
            </select>
            <div class="form-error"></div>
          </div>
          <div class="form-group">
            <label>Unit No (1-6) *</label>
            <select class="form-control" id="act-unit" data-required>
              <option value="1">Unit 1</option>
              <option value="2">Unit 2</option>
              <option value="3">Unit 3</option>
              <option value="4">Unit 4</option>
              <option value="5">Unit 5</option>
              <option value="6">Unit 6</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Activity Type *</label>
            <select class="form-control" id="act-type" data-required>
              <option value="assignment">📝 Assignment</option>
              <option value="quiz">❓ Quiz</option>
              <option value="test">📋 Test</option>
              <option value="seminar">🎤 Seminar</option>
              <option value="viva">🗣️ Viva</option>
              <option value="practical">🔬 Practical</option>
              <option value="project_review">🔍 Project Review</option>
              <option value="presentation">📽️ Presentation</option>
            </select>
            <div class="form-error"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Max Marks *</label>
            <input type="number" class="form-control" id="act-maxmarks" data-required data-min="1" value="10" step="0.5">
            <div class="form-error"></div>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select class="form-control" id="act-status">
              <option value="active">Active</option>
              <option value="draft">Draft</option>
              <option value="completed">Completed</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Start Time</label>
            <input type="datetime-local" class="form-control" id="act-starttime">
          </div>
          <div class="form-group">
            <label>End Time</label>
            <input type="datetime-local" class="form-control" id="act-endtime">
          </div>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea class="form-control" id="act-desc" placeholder="Activity description (optional)" rows="3"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-activity')">Cancel</button>
      <button class="btn btn-primary" onclick="saveActivity()">Save Activity</button>
    </div>
  </div>
</div>

<script>
const typeIcons = { assignment: '📝', quiz: '❓', test: '📋', seminar: '🎤', viva: '🗣️', practical: '🔬', project_review: '🔍', presentation: '📽️' };
const statusClass = { draft: 'badge-secondary', active: 'badge-primary', completed: 'badge-success', cancelled: 'badge-danger' };
const autoStatusClass = { NOT_STARTED: 'badge-secondary', ACTIVE: 'badge-success', CLOSED: 'badge-danger', draft: 'badge-secondary', cancelled: 'badge-danger' };

async function loadSubjectOptions() {
  const res = await API.get('/api/subjects.php?for_faculty=1');
  if (!res || !res.success) return;
  const opts = res.subjects.map(s => `<option value="${s.id}">${s.code} — ${s.name}</option>`).join('');
  document.getElementById('filter-subject').innerHTML = '<option value="">All Subjects</option>' + opts;
  document.getElementById('act-subject').innerHTML = '<option value="">Select Subject</option>' + opts;
  
  // Auto-select from URL params
  const urlParams = new URLSearchParams(window.location.search);
  const subjectParam = urlParams.get('subject');
  if (subjectParam) {
    document.getElementById('filter-subject').value = subjectParam;
  }
}

async function loadActivities() {
  const subject = document.getElementById('filter-subject').value;
  const type = document.getElementById('filter-type').value;
  const status = document.getElementById('filter-status').value;
  const search = document.getElementById('search-act').value;
  
  let url = '/api/activities.php?';
  if (subject) url += `subject_id=${subject}&`;
  if (type) url += `type=${type}&`;
  if (status) url += `status=${status}&`;
  if (search) url += `search=${encodeURIComponent(search)}&`;
  
  const res = await API.get(url);
  if (!res || !res.success) return;
  
  const tbody = document.getElementById('act-tbody');
  if (res.activities.length === 0) {
    tbody.innerHTML = '<tr><td colspan="9"><div class="empty-state"><div class="icon">📝</div><h3>No activities found</h3><p>Create your first CIE activity.</p></div></td></tr>';
    return;
  }
  
  tbody.innerHTML = res.activities.map(a => {
    const marksInfo = `${a.marks_entered}/${a.total_students}`;
    const formatDateTime = d => d ? new Date(d).toLocaleString('en-IN', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }) : '—';
    
    return `<tr>
      <td><span class="badge badge-secondary">Unit ${a.unit_no}</span></td>
      <td><strong>${a.name}</strong></td>
      <td><span class="badge badge-info">${a.subject_code}</span></td>
      <td><span class="activity-type">${typeIcons[a.type] || '📌'} ${a.type}</span></td>
      <td>${parseFloat(a.max_marks).toFixed(0)}</td>
      <td class="text-muted" style="font-size:0.8rem">
        <div><span style="color:var(--success)">▶</span> ${formatDateTime(a.start_time)}</div>
        <div><span style="color:var(--danger)">⏹</span> ${formatDateTime(a.end_time)}</div>
      </td>
      <td><span class="badge ${autoStatusClass[a.auto_status] || 'badge-secondary'}">${a.auto_status}</span></td>
      <td><span class="badge ${statusClass[a.status] || 'badge-secondary'}">${a.status}</span></td>
      <td>${marksInfo}</td>
      <td>
        <button class="btn btn-sm btn-secondary" onclick="editActivity(${a.id})">✏️</button>
        <a href="/faculty/marks.php?activity=${a.id}" class="btn btn-sm btn-primary">📊 Review & Grade</a>
        <button class="btn btn-sm btn-danger" onclick="deleteActivity(${a.id}, '${a.name.replace(/'/g, "\\'")}')">🗑️</button>
      </td>
    </tr>`;
  }).join('');
}

function openAddActivity() {
  document.getElementById('modal-act-title').textContent = 'Create Activity';
  document.getElementById('act-id').value = '';
  document.getElementById('form-activity').reset();
  Modal.open('modal-activity');
}

async function editActivity(id) {
  const res = await API.get(`/api/activities.php?id=${id}`);
  if (!res || !res.success) return;
  const a = res.activity;
  document.getElementById('modal-act-title').textContent = 'Edit Activity';
  document.getElementById('act-id').value = a.id;
  document.getElementById('act-name').value = a.name;
  document.getElementById('act-subject').value = a.subject_id;
  document.getElementById('act-unit').value = a.unit_no || 1;
  document.getElementById('act-type').value = a.type;
  document.getElementById('act-maxmarks').value = a.max_marks;
  document.getElementById('act-status').value = a.status;
  document.getElementById('act-starttime').value = a.start_time ? a.start_time.substring(0, 16) : '';
  document.getElementById('act-endtime').value = a.end_time ? a.end_time.substring(0, 16) : '';
  document.getElementById('act-desc').value = a.description || '';
  Modal.open('modal-activity');
}

async function saveActivity() {
  if (!FormValidator.validate('form-activity')) return;
  const id = document.getElementById('act-id').value;
  const data = {
    name: document.getElementById('act-name').value.trim(),
    subject_id: document.getElementById('act-subject').value,
    unit_no: document.getElementById('act-unit').value,
    type: document.getElementById('act-type').value,
    max_marks: document.getElementById('act-maxmarks').value,
    status: document.getElementById('act-status').value,
    start_time: document.getElementById('act-starttime').value ? document.getElementById('act-starttime').value.replace('T', ' ') + ':00' : null,
    end_time: document.getElementById('act-endtime').value ? document.getElementById('act-endtime').value.replace('T', ' ') + ':00' : null,
    description: document.getElementById('act-desc').value.trim()
  };
  const res = id ? await API.put('/api/activities.php', { ...data, id: parseInt(id) })
                  : await API.post('/api/activities.php', data);
  if (res && res.success) { Toast.success(res.message); Modal.close('modal-activity'); loadActivities(); }
  else Toast.error(res?.message || 'Failed.');
}

async function deleteActivity(id, name) {
  if (!confirm(`Delete activity "${name}"? All marks will be removed.`)) return;
  const res = await API.request('/api/activities.php', { method: 'DELETE', body: JSON.stringify({ id }) });
  if (res && res.success) { Toast.success(res.message); loadActivities(); }
  else Toast.error(res?.message || 'Failed.');
}

document.addEventListener('DOMContentLoaded', () => { loadSubjectOptions().then(loadActivities); });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
