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

<div class="card" style="border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-sm);">
  <div class="card-header" style="padding: 16px 20px; display: flex; flex-wrap: wrap; gap: 14px; align-items: center; justify-content: space-between;">
    <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700;">All Activities</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
      <select class="form-control" id="filter-subject" onchange="loadActivities()" style="height: 38px; border-radius: 8px; font-size: 0.85rem;">
        <option value="">All Subjects</option>
      </select>
      <select class="form-control" id="filter-type" onchange="loadActivities()" style="height: 38px; border-radius: 8px; font-size: 0.85rem;">
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
      <select class="form-control" id="filter-status" onchange="loadActivities()" style="height: 38px; border-radius: 8px; font-size: 0.85rem;">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="draft">Draft</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <div style="position: relative; min-width: 200px;">
        <input type="text" class="form-control" id="search-act" placeholder="Search activities..." oninput="debounce(loadActivities, 300)()" style="padding-left: 36px; height: 38px; border-radius: 8px; font-size: 0.85rem;">
        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; display: flex; align-items: center;">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </span>
      </div>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-container">
      <table id="act-table" style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr>
            <th data-sortable="true" style="min-width: 220px; padding: 14px 18px;">Activity & Subject</th>
            <th data-sortable="true" style="width: 110px; padding: 14px 14px;">Type</th>
            <th data-sortable="true" style="width: 100px; padding: 14px 14px;">Max Marks</th>
            <th data-sortable="true" style="min-width: 160px; padding: 14px 14px;">Timeline</th>
            <th data-sortable="true" style="width: 130px; padding: 14px 14px;">Status</th>
            <th data-sortable="true" style="width: 140px; padding: 14px 14px;">Grading Progress</th>
            <th data-sortable="false" style="width: 200px; text-align: right; padding: 14px 18px;">Actions</th>
          </tr>
        </thead>
        <tbody id="act-tbody">
          <tr><td colspan="7" class="text-center text-muted" style="padding:40px">Loading...</td></tr>
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
              <option value="assignment">Assignment</option>
              <option value="quiz">Quiz</option>
              <option value="test">Test</option>
              <option value="seminar">Seminar</option>
              <option value="viva">Viva</option>
              <option value="practical">Practical</option>
              <option value="project_review">Project Review</option>
              <option value="presentation">Presentation</option>
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
    tbody.innerHTML = '<tr><td colspan="7" class="text-center" style="padding:40px 0;"><div class="icon" style="margin-bottom:8px; display:flex; justify-content:center;"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted);"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg></div><h3>No activities found</h3><p>Create your first CIE activity.</p></td></tr>';
    return;
  }
  
  tbody.innerHTML = res.activities.map(a => {
    const totalStudents = a.total_students || 0;
    const marksEntered = a.marks_entered || 0;
    const marksInfo = `${marksEntered} / ${totalStudents} Graded`;
    const pctGraded = totalStudents > 0 ? Math.round((marksEntered / totalStudents) * 100) : 0;
    const formatDateTime = d => d ? new Date(d).toLocaleString('en-IN', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }) : '—';
    
    // Status Badge
    let statusBadgeHtml = '';
    if (a.status === 'completed') {
      statusBadgeHtml = '<span class="badge badge-success" style="font-size:0.7rem;">COMPLETED</span>';
    } else if (a.status === 'active') {
      statusBadgeHtml = '<span class="badge badge-primary" style="font-size:0.7rem;">ACTIVE</span>';
    } else if (a.status === 'draft') {
      statusBadgeHtml = '<span class="badge badge-secondary" style="font-size:0.7rem;">DRAFT</span>';
    } else {
      statusBadgeHtml = `<span class="badge badge-danger" style="font-size:0.7rem;">${a.status.toUpperCase()}</span>`;
    }

    return `<tr>
      <td style="padding: 12px 18px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px; flex-wrap:wrap;">
          <span class="badge badge-secondary" style="font-size:0.68rem; font-weight:700; padding:2px 6px;">Unit ${a.unit_no}</span>
          <strong style="font-size:0.92rem; color:var(--text-primary);">${a.name}</strong>
        </div>
        <div style="font-size:0.78rem; color:var(--text-muted); font-weight:500;">
          ${a.subject_code} &bull; ${a.subject_name || ''}
        </div>
      </td>
      <td style="padding: 12px 14px;">
        <span class="badge badge-primary" style="font-size:0.7rem; text-transform:uppercase;">${a.type}</span>
      </td>
      <td style="padding: 12px 14px;">
        <strong style="font-size:0.9rem;">${parseFloat(a.max_marks).toFixed(1)}</strong>
      </td>
      <td style="font-size:0.78rem; padding: 12px 14px;">
        <div style="display:flex; align-items:center; gap:4px; color:var(--text-secondary); margin-bottom:2px;">
          <span style="color:var(--success); font-size:9px;">▶</span> ${formatDateTime(a.start_time)}
        </div>
        <div style="display:flex; align-items:center; gap:4px; color:var(--text-secondary);">
          <span style="color:var(--danger); font-size:9px;">⏹</span> ${formatDateTime(a.end_time)}
        </div>
      </td>
      <td style="padding: 12px 14px;">
        ${statusBadgeHtml}
      </td>
      <td style="padding: 12px 14px;">
        <div style="display:flex; flex-direction:column; gap:2px;">
          <span style="font-size:0.8rem; font-weight:600; color:var(--text-primary);">${marksInfo}</span>
          <small style="font-size:0.72rem; color:var(--text-muted); font-weight:600;">${pctGraded}% complete</small>
        </div>
      </td>
      <td style="text-align:right; padding: 12px 18px;">
        <div style="display:flex; gap:6px; justify-content:flex-end; align-items:center;">
          <a href="/faculty/marks.php?activity=${a.id}" class="btn btn-sm btn-primary" title="Review & Grade Submissions" style="padding:5px 10px; font-size:0.75rem; display:inline-flex; align-items:center; gap:4px; text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            Review & Grade
          </a>
          <button class="btn btn-sm btn-secondary" title="Edit Activity" style="padding:5px 8px; font-size:0.75rem; display:inline-flex; align-items:center;" onclick="editActivity(${a.id})">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
          </button>
          <button class="btn btn-sm btn-danger" title="Delete Activity" style="padding:5px 8px; font-size:0.75rem; display:inline-flex; align-items:center;" onclick="deleteActivity(${a.id}, '${a.name.replace(/'/g, "\\'")}')">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          </button>
        </div>
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
