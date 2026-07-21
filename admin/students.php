<?php
$pageTitle = 'Students';
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'hod']);
?>

<div class="page-header">
  <div>
    <h1>Students</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Students</div>
  </div>
  <div class="actions">
    <button class="btn btn-primary" onclick="openAddStudent()">+ Add Student</button>
  </div>
</div>

<div class="card">
  <div class="card-header" style="flex-wrap:wrap;gap:12px;">
    <h3>All Students</h3>
    <div class="filter-bar" style="margin:0">
      <select class="form-control" id="filter-dept" onchange="loadStudents()">
        <option value="">All Departments</option>
      </select>
      <select class="form-control" id="filter-sem" onchange="loadStudents()">
        <option value="">All Semesters</option>
        <?php for($i=1;$i<=8;$i++): ?><option value="<?=$i?>">Semester <?=$i?></option><?php endfor; ?>
      </select>
      <select class="form-control" id="filter-sec" onchange="loadStudents()">
        <option value="">All Sections</option>
        <option value="A">Section A</option>
        <option value="B">Section B</option>
        <option value="C">Section C</option>
      </select>
      <div class="search-filter">
        <span class="icon">🔍</span>
        <input type="text" id="search-stu" placeholder="Search students..." oninput="debounce(loadStudents, 400)()">
      </div>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-container">
      <table id="stu-table" data-sortable>
        <thead>
          <tr>
            <th>USN / PRN</th>
            <th>Name</th>
            <th>Email</th>
            <th>Dept</th>
            <th>Sem</th>
            <th>Sec / Roll</th>
            <th data-sortable="false">Actions</th>
          </tr>
        </thead>
        <tbody id="stu-tbody">
          <tr><td colspan="7" class="text-center text-muted" style="padding:40px">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal-overlay" id="modal-student">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-stu-title">Add Student</h3>
      <button class="modal-close" onclick="Modal.close('modal-student')">✕</button>
    </div>
    <div class="modal-body">
      <form id="form-student">
        <input type="hidden" id="stu-id">
        <div class="form-row">
          <div class="form-group">
            <label>Full Name *</label>
            <input type="text" class="form-control" id="stu-name" data-required placeholder="Student name">
            <div class="form-error"></div>
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" class="form-control" id="stu-email" data-required data-email placeholder="Email address">
            <div class="form-error"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>USN *</label>
            <input type="text" class="form-control" id="stu-usn" data-required placeholder="e.g. 1ZL21CS001">
            <div class="form-error"></div>
          </div>
          <div class="form-group">
            <label>PRN Number</label>
            <input type="text" class="form-control" id="stu-prn" placeholder="PRN Number">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Roll Number</label>
            <input type="text" class="form-control" id="stu-roll" placeholder="Roll Number">
          </div>
          <div class="form-group">
            <label>Department *</label>
            <select class="form-control" id="stu-dept" data-required>
              <option value="">Select Department</option>
            </select>
            <div class="form-error"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Semester</label>
            <select class="form-control" id="stu-sem">
              <?php for($i=1;$i<=8;$i++): ?><option value="<?=$i?>">Semester <?=$i?></option><?php endfor; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Section</label>
            <select class="form-control" id="stu-sec">
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" class="form-control" id="stu-phone" placeholder="Phone number">
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-student')">Cancel</button>
      <button class="btn btn-primary" onclick="saveStudent()">Save Student</button>
    </div>
  </div>
</div>

<script>
async function loadDeptOptions() {
  const res = await API.get('/api/departments.php');
  if (!res || !res.success) return;
  const options = res.departments.map(d => `<option value="${d.id}">${d.name} (${d.code})</option>`).join('');
  document.getElementById('filter-dept').innerHTML = '<option value="">All Departments</option>' + options;
  document.getElementById('stu-dept').innerHTML = '<option value="">Select Department</option>' + options;
}

async function loadStudents() {
  const dept = document.getElementById('filter-dept').value;
  const sem  = document.getElementById('filter-sem').value;
  const sec  = document.getElementById('filter-sec').value;
  const search = document.getElementById('search-stu').value;
  
  let url = '/api/students.php?';
  if (dept) url += `department=${dept}&`;
  if (sem) url += `semester=${sem}&`;
  if (sec) url += `section=${sec}&`;
  if (search) url += `search=${encodeURIComponent(search)}&`;
  
  const res = await API.get(url);
  if (!res || !res.success) return;
  
  const tbody = document.getElementById('stu-tbody');
  if (res.students.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><div class="icon">🎓</div><h3>No students found</h3></div></td></tr>';
    return;
  }
  
  tbody.innerHTML = res.students.map(s => `
    <tr>
      <td>
        <span class="badge badge-info">${s.usn}</span>
        <div style="font-size:0.8rem; margin-top:4px; color:#64748b;">PRN: ${s.prn_number || 'N/A'}</div>
      </td>
      <td><strong>${s.name}</strong></td>
      <td class="text-muted">${s.email}</td>
      <td>${s.dept_code}</td>
      <td>Sem ${s.semester}</td>
      <td>
        <div>${s.section}</div>
        <div style="font-size:0.8rem; color:#64748b;">Roll: ${s.roll_number || 'N/A'}</div>
      </td>
      <td>
        <button class="btn btn-sm btn-secondary" onclick="editStudent(${s.id})">✏️</button>
        <button class="btn btn-sm btn-danger" onclick="deleteStudent(${s.id}, '${s.name}')">🗑️</button>
      </td>
    </tr>
  `).join('');
}

function openAddStudent() {
  document.getElementById('modal-stu-title').textContent = 'Add Student';
  document.getElementById('stu-id').value = '';
  document.getElementById('form-student').reset();
  Modal.open('modal-student');
}

async function editStudent(id) {
  const res = await API.get(`/api/students.php?id=${id}`);
  if (!res || !res.success) return;
  const s = res.student;
  document.getElementById('modal-stu-title').textContent = 'Edit Student';
  document.getElementById('stu-id').value = s.id;
  document.getElementById('stu-name').value = s.name;
  document.getElementById('stu-email').value = s.email;
  document.getElementById('stu-usn').value = s.usn;
  document.getElementById('stu-prn').value = s.prn_number || '';
  document.getElementById('stu-roll').value = s.roll_number || '';
  document.getElementById('stu-dept').value = s.department_id;
  document.getElementById('stu-sem').value = s.semester;
  document.getElementById('stu-sec').value = s.section;
  document.getElementById('stu-phone').value = s.phone || '';
  Modal.open('modal-student');
}

async function saveStudent() {
  if (!FormValidator.validate('form-student')) return;
  
  const id = document.getElementById('stu-id').value;
  const data = {
    name: document.getElementById('stu-name').value.trim(),
    email: document.getElementById('stu-email').value.trim(),
    usn: document.getElementById('stu-usn').value.trim(),
    prn_number: document.getElementById('stu-prn').value.trim(),
    roll_number: document.getElementById('stu-roll').value.trim(),
    department_id: document.getElementById('stu-dept').value,
    semester: document.getElementById('stu-sem').value,
    section: document.getElementById('stu-sec').value,
    phone: document.getElementById('stu-phone').value.trim()
  };
  
  const res = id 
    ? await API.put('/api/students.php', { ...data, id: parseInt(id) })
    : await API.post('/api/students.php', data);
  
  if (res && res.success) {
    Toast.success(res.message);
    Modal.close('modal-student');
    loadStudents();
  } else {
    Toast.error(res?.message || 'Failed to save.');
  }
}

async function deleteStudent(id, name) {
  if (!confirm(`Delete student "${name}"? This action cannot be undone.`)) return;
  const res = await API.request('/api/students.php', { method: 'DELETE', body: JSON.stringify({ id }) });
  if (res && res.success) { Toast.success(res.message); loadStudents(); }
  else Toast.error(res?.message || 'Failed to delete.');
}

document.addEventListener('DOMContentLoaded', () => { loadDeptOptions(); loadStudents(); });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
