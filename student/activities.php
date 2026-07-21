<?php
$pageTitle = 'My Activities';
require_once __DIR__ . '/../includes/header.php';
requireRole(['student']);
?>

<div class="page-header">
  <div>
    <h1>My Activities</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / My Activities</div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="form-row" style="grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; align-items: end;">
      <div class="form-group mb-0">
        <label>Search Activities</label>
        <input type="text" class="form-control" id="search-input" placeholder="Search by activity name...">
      </div>
      <div class="form-group mb-0">
        <label>Subject</label>
        <select class="form-control" id="subject-filter">
          <option value="">All Subjects</option>
        </select>
      </div>
      <div class="form-group mb-0">
        <label>Activity Type</label>
        <select class="form-control" id="type-filter">
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
      </div>
      <div class="form-group mb-0">
        <label>Status</label>
        <select class="form-control" id="status-filter">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="completed">Completed</option>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-container">
      <table id="activities-table">
        <thead>
          <tr>
            <th data-sortable="true">Activity Name</th>
            <th data-sortable="true">Subject</th>
            <th data-sortable="true">Type</th>
            <th data-sortable="true">Max Marks</th>
            <th data-sortable="true">Activity Date</th>
            <th data-sortable="true">Deadline</th>
            <th data-sortable="true">Status</th>
            <th data-sortable="false">Actions</th>
          </tr>
        </thead>
        <tbody id="activities-tbody">
          <tr><td colspan="8" class="text-center"><div class="spinner spinner-sm" style="margin:20px auto;"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Activity Detail Modal -->
<div class="modal-overlay" id="modal-activity-details">
  <div class="modal">
    <div class="modal-header">
      <h3 id="detail-title">Activity Details</h3>
      <button class="modal-close" onclick="Modal.close('modal-activity-details')">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
        <div>
          <label style="font-weight:600; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Subject</label>
          <div id="detail-subject" style="font-weight:500; margin-top:2px;">—</div>
        </div>
        <div>
          <label style="font-weight:600; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Type</label>
          <div id="detail-type" style="margin-top:2px;">—</div>
        </div>
        <div>
          <label style="font-weight:600; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Max Marks</label>
          <div id="detail-max-marks" style="font-weight:700; margin-top:2px;">—</div>
        </div>
        <div>
          <label style="font-weight:600; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Deadline</label>
          <div id="detail-deadline" style="margin-top:2px;">—</div>
        </div>
      </div>
      <div style="margin-bottom:20px;">
        <label style="font-weight:600; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Description</label>
        <p id="detail-description" style="margin-top:4px; font-size:0.875rem; white-space:pre-wrap; line-height:1.5;">—</p>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-activity-details')">Close</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  let allActivities = [];

  const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  };

  // Load filter options
  const subRes = await API.get('/api/subjects.php');
  if (subRes && subRes.success) {
    const select = document.getElementById('subject-filter');
    subRes.subjects.forEach(s => {
      const opt = document.createElement('option');
      opt.value = s.id;
      opt.textContent = `${s.code} - ${s.name}`;
      select.appendChild(opt);
    });
  }

  // Load activities
  const loadActivities = async () => {
    const subjectId = document.getElementById('subject-filter').value;
    const type = document.getElementById('type-filter').value;
    const status = document.getElementById('status-filter').value;
    const search = document.getElementById('search-input').value;

    let url = `/api/activities.php?action=list`;
    if (subjectId) url += `&subject_id=${subjectId}`;
    if (type) url += `&type=${type}`;
    if (status) url += `&status=${status}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    const res = await API.get(url);
    const tbody = document.getElementById('activities-tbody');
    
    if (res && res.success) {
      allActivities = res.activities;
      if (allActivities.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center" style="padding:40px 0;"><div class="icon" style="margin-bottom:8px; display:flex; justify-content:center;"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted);"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg></div><h3>No activities found</h3><p>No activities match your filters.</p></td></tr>';
        return;
      }

      tbody.innerHTML = allActivities.map(a => `
        <tr>
          <td><strong>${a.name}</strong></td>
          <td>${a.subject_code} - ${a.subject_name}</td>
          <td><span class="badge badge-primary">${a.type}</span></td>
          <td><strong>${parseFloat(a.max_marks).toFixed(1)}</strong></td>
          <td>${formatDate(a.activity_date)}</td>
          <td>${formatDate(a.deadline)}</td>
          <td><span class="badge ${a.status === 'completed' ? 'badge-success' : 'badge-primary'}">${a.status}</span></td>
          <td>
            <button class="btn btn-sm btn-secondary" style="display:inline-flex; align-items:center; gap:6px;" onclick="viewDetails(${a.id})">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
              View
            </button>
          </td>
        </tr>
      `).join('');
    } else {
      tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger" style="padding:20px;">Failed to load activities.</td></tr>';
    }
  };

  // Event Listeners for Filters
  document.getElementById('subject-filter').addEventListener('change', loadActivities);
  document.getElementById('type-filter').addEventListener('change', loadActivities);
  document.getElementById('status-filter').addEventListener('change', loadActivities);
  document.getElementById('search-input').addEventListener('input', debounce(loadActivities, 300));

  // Initial Load
  await loadActivities();

  // Detail Modal view function
  window.viewDetails = (id) => {
    const a = allActivities.find(item => item.id === id);
    if (!a) return;

    document.getElementById('detail-title').textContent = a.name;
    document.getElementById('detail-subject').textContent = `${a.subject_code} - ${a.subject_name}`;
    document.getElementById('detail-type').innerHTML = `<span class="badge badge-primary">${a.type}</span>`;
    document.getElementById('detail-max-marks').textContent = parseFloat(a.max_marks).toFixed(1);
    document.getElementById('detail-deadline').textContent = formatDate(a.deadline);
    document.getElementById('detail-description').textContent = a.description || 'No description provided.';

    Modal.open('modal-activity-details');
  };
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
