<?php
$pageTitle = 'Departments';
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'hod']);
?>

<div class="page-header">
  <div>
    <h1>Departments</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Departments</div>
  </div>
  <div class="actions">
    <button class="btn btn-primary" onclick="openAddDept()">+ Add Department</button>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h3>All Departments</h3>
    <div class="filter-bar" style="margin:0">
      <div class="search-filter">
        <span class="icon">🔍</span>
        <input type="text" id="search-dept" placeholder="Search departments..." data-search-table="dept-table">
      </div>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-container">
      <table id="dept-table" data-sortable>
        <thead>
          <tr>
            <th>Code</th>
            <th>Department Name</th>
            <th>HOD</th>
            <th>Faculty</th>
            <th>Students</th>
            <th>Subjects</th>
            <th data-sortable="false">Actions</th>
          </tr>
        </thead>
        <tbody id="dept-tbody">
          <tr><td colspan="7" class="text-center text-muted" style="padding:40px">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal-overlay" id="modal-dept">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-dept-title">Add Department</h3>
      <button class="modal-close" onclick="Modal.close('modal-dept')">✕</button>
    </div>
    <div class="modal-body">
      <form id="form-dept">
        <input type="hidden" id="dept-id">
        <div class="form-group">
          <label>Department Name *</label>
          <input type="text" class="form-control" id="dept-name" data-required placeholder="e.g. Computer Science & Engineering">
          <div class="form-error"></div>
        </div>
        <div class="form-group">
          <label>Code *</label>
          <input type="text" class="form-control" id="dept-code" data-required placeholder="e.g. CSE" style="text-transform:uppercase">
          <div class="form-error"></div>
        </div>
        <div class="form-group">
          <label>HOD</label>
          <select class="form-control" id="dept-hod">
            <option value="">— Select HOD —</option>
          </select>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-dept')">Cancel</button>
      <button class="btn btn-primary" onclick="saveDept()">Save Department</button>
    </div>
  </div>
</div>

<script>
let allHods = [];

async function loadDepartments() {
  const res = await API.get('/api/departments.php');
  if (!res || !res.success) return;
  
  const tbody = document.getElementById('dept-tbody');
  if (res.departments.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><div class="icon">🏢</div><h3>No departments yet</h3><p>Add your first department to get started.</p></div></td></tr>';
    return;
  }
  
  tbody.innerHTML = res.departments.map(d => `
    <tr>
      <td><span class="badge badge-primary">${d.code}</span></td>
      <td><strong>${d.name}</strong></td>
      <td>${d.hod_name || '<span class="text-muted">Not assigned</span>'}</td>
      <td>${d.faculty_count}</td>
      <td>${d.student_count}</td>
      <td>${d.subject_count}</td>
      <td>
        <button class="btn btn-sm btn-secondary" onclick="editDept(${d.id})">✏️ Edit</button>
        <button class="btn btn-sm btn-danger" onclick="deleteDept(${d.id}, '${d.name}')">🗑️</button>
      </td>
    </tr>
  `).join('');
}

async function loadHods() {
  // Fetch faculty/HOD users for dropdown
  const res = await API.get('/api/faculty.php');
  if (res && res.success) {
    allHods = res.faculty;
    const select = document.getElementById('dept-hod');
    select.innerHTML = '<option value="">— Select HOD —</option>' + 
      res.faculty.map(f => `<option value="${f.user_id}">${f.name} (${f.employee_id})</option>`).join('');
  }
}

function openAddDept() {
  document.getElementById('modal-dept-title').textContent = 'Add Department';
  document.getElementById('dept-id').value = '';
  document.getElementById('form-dept').reset();
  Modal.open('modal-dept');
}

async function editDept(id) {
  const res = await API.get(`/api/departments.php?id=${id}`);
  if (!res || !res.success) return;
  
  const d = res.department;
  document.getElementById('modal-dept-title').textContent = 'Edit Department';
  document.getElementById('dept-id').value = d.id;
  document.getElementById('dept-name').value = d.name;
  document.getElementById('dept-code').value = d.code;
  document.getElementById('dept-hod').value = d.hod_id || '';
  Modal.open('modal-dept');
}

async function saveDept() {
  if (!FormValidator.validate('form-dept')) return;
  
  const id = document.getElementById('dept-id').value;
  const data = {
    name: document.getElementById('dept-name').value.trim(),
    code: document.getElementById('dept-code').value.trim().toUpperCase(),
    hod_id: document.getElementById('dept-hod').value || null
  };
  
  let res;
  if (id) {
    data.id = parseInt(id);
    res = await API.put('/api/departments.php', data);
  } else {
    res = await API.post('/api/departments.php', data);
  }
  
  if (res && res.success) {
    Toast.success(res.message);
    Modal.close('modal-dept');
    loadDepartments();
  } else {
    Toast.error(res?.message || 'Failed to save.');
  }
}

async function deleteDept(id, name) {
  if (!confirm(`Delete department "${name}"? This will also remove all associated data.`)) return;
  
  // delete sends body via request, let's use API.request
  const result = await API.request('/api/departments.php', {
    method: 'DELETE',
    body: JSON.stringify({ id })
  });
  
  if (result && result.success) {
    Toast.success(result.message);
    loadDepartments();
  } else {
    Toast.error(result?.message || 'Failed to delete.');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  loadDepartments();
  loadHods();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
