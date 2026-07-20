<?php
$pageTitle = 'Faculty';
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'hod']);
?>

<div class="page-header">
  <div>
    <h1>Faculty</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Faculty</div>
  </div>
  <div class="actions">
    <button class="btn btn-primary" onclick="openAddFac()">+ Add Faculty</button>
  </div>
</div>

<div class="card">
  <div class="card-header" style="flex-wrap:wrap;gap:12px;">
    <h3>All Faculty</h3>
    <div class="filter-bar" style="margin:0">
      <select class="form-control" id="filter-dept" onchange="loadFaculty()">
        <option value="">All Departments</option>
      </select>
      <div class="search-filter">
        <span class="icon">🔍</span>
        <input type="text" id="search-fac" placeholder="Search faculty..." oninput="debounce(loadFaculty, 400)()">
      </div>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-container">
      <table id="fac-table" data-sortable>
        <thead>
          <tr>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Designation</th>
            <th>Department</th>
            <th>Role</th>
            <th>Subjects</th>
            <th data-sortable="false">Actions</th>
          </tr>
        </thead>
        <tbody id="fac-tbody">
          <tr><td colspan="8" class="text-center text-muted" style="padding:40px">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal-overlay" id="modal-fac">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-fac-title">Add Faculty</h3>
      <button class="modal-close" onclick="Modal.close('modal-fac')">✕</button>
    </div>
    <div class="modal-body">
      <form id="form-fac">
        <input type="hidden" id="fac-id">
        <div class="form-row">
          <div class="form-group">
            <label>Full Name *</label>
            <input type="text" class="form-control" id="fac-name" data-required placeholder="Faculty name">
            <div class="form-error"></div>
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" class="form-control" id="fac-email" data-required data-email placeholder="Email">
            <div class="form-error"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Employee ID *</label>
            <input type="text" class="form-control" id="fac-empid" data-required placeholder="e.g. FAC006">
            <div class="form-error"></div>
          </div>
          <div class="form-group">
            <label>Department *</label>
            <select class="form-control" id="fac-dept" data-required>
              <option value="">Select Department</option>
            </select>
            <div class="form-error"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Designation</label>
            <select class="form-control" id="fac-desig">
              <option value="Assistant Professor">Assistant Professor</option>
              <option value="Associate Professor">Associate Professor</option>
              <option value="Professor">Professor</option>
              <option value="Professor & HOD">Professor & HOD</option>
            </select>
          </div>
          <div class="form-group">
            <label>Role</label>
            <select class="form-control" id="fac-role">
              <option value="faculty">Faculty</option>
              <option value="coordinator">Coordinator</option>
              <option value="hod">HOD</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" class="form-control" id="fac-phone" placeholder="Phone number">
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-fac')">Cancel</button>
      <button class="btn btn-primary" onclick="saveFac()">Save Faculty</button>
    </div>
  </div>
</div>

<script>
async function loadDeptOptions() {
  const res = await API.get('/api/departments.php');
  if (!res || !res.success) return;
  const options = res.departments.map(d => `<option value="${d.id}">${d.name} (${d.code})</option>`).join('');
  document.getElementById('filter-dept').innerHTML = '<option value="">All Departments</option>' + options;
  document.getElementById('fac-dept').innerHTML = '<option value="">Select Department</option>' + options;
}

async function loadFaculty() {
  const dept = document.getElementById('filter-dept').value;
  const search = document.getElementById('search-fac').value;
  let url = '/api/faculty.php?';
  if (dept) url += `department=${dept}&`;
  if (search) url += `search=${encodeURIComponent(search)}&`;
  
  const res = await API.get(url);
  if (!res || !res.success) return;
  
  const tbody = document.getElementById('fac-tbody');
  if (res.faculty.length === 0) {
    tbody.innerHTML = '<tr><td colspan="8"><div class="empty-state"><div class="icon">👨‍🏫</div><h3>No faculty found</h3></div></td></tr>';
    return;
  }
  
  const roleBadge = (r) => {
    const cls = r === 'hod' ? 'badge-purple' : r === 'coordinator' ? 'badge-warning' : 'badge-primary';
    return `<span class="badge ${cls}">${r}</span>`;
  };
  
  tbody.innerHTML = res.faculty.map(f => `
    <tr>
      <td><span class="badge badge-info">${f.employee_id}</span></td>
      <td><strong>${f.name}</strong></td>
      <td class="text-muted">${f.email}</td>
      <td>${f.designation}</td>
      <td>${f.dept_code}</td>
      <td>${roleBadge(f.role)}</td>
      <td>${f.subject_count}</td>
      <td>
        <button class="btn btn-sm btn-secondary" onclick="editFac(${f.id})">✏️</button>
        <button class="btn btn-sm btn-danger" onclick="deleteFac(${f.id}, '${f.name}')">🗑️</button>
      </td>
    </tr>
  `).join('');
}

function openAddFac() {
  document.getElementById('modal-fac-title').textContent = 'Add Faculty';
  document.getElementById('fac-id').value = '';
  document.getElementById('form-fac').reset();
  Modal.open('modal-fac');
}

async function editFac(id) {
  const res = await API.get(`/api/faculty.php?id=${id}`);
  if (!res || !res.success) return;
  const f = res.faculty;
  document.getElementById('modal-fac-title').textContent = 'Edit Faculty';
  document.getElementById('fac-id').value = f.id;
  document.getElementById('fac-name').value = f.name;
  document.getElementById('fac-email').value = f.email;
  document.getElementById('fac-empid').value = f.employee_id;
  document.getElementById('fac-dept').value = f.department_id;
  document.getElementById('fac-desig').value = f.designation;
  document.getElementById('fac-role').value = f.role;
  document.getElementById('fac-phone').value = f.phone || '';
  Modal.open('modal-fac');
}

async function saveFac() {
  if (!FormValidator.validate('form-fac')) return;
  const id = document.getElementById('fac-id').value;
  const data = {
    name: document.getElementById('fac-name').value.trim(),
    email: document.getElementById('fac-email').value.trim(),
    employee_id: document.getElementById('fac-empid').value.trim(),
    department_id: document.getElementById('fac-dept').value,
    designation: document.getElementById('fac-desig').value,
    role: document.getElementById('fac-role').value,
    phone: document.getElementById('fac-phone').value.trim()
  };
  const res = id 
    ? await API.put('/api/faculty.php', { ...data, id: parseInt(id) })
    : await API.post('/api/faculty.php', data);
  if (res && res.success) { Toast.success(res.message); Modal.close('modal-fac'); loadFaculty(); }
  else Toast.error(res?.message || 'Failed to save.');
}

async function deleteFac(id, name) {
  if (!confirm(`Delete faculty "${name}"?`)) return;
  const res = await API.request('/api/faculty.php', { method: 'DELETE', body: JSON.stringify({ id }) });
  if (res && res.success) { Toast.success(res.message); loadFaculty(); }
  else Toast.error(res?.message || 'Failed.');
}

document.addEventListener('DOMContentLoaded', () => { loadDeptOptions(); loadFaculty(); });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
