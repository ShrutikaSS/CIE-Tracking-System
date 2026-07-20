<?php
$pageTitle = 'Subjects';
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin', 'hod']);
?>

<div class="page-header">
  <div>
    <h1>Subjects</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Subjects</div>
  </div>
  <div class="actions">
    <button class="btn btn-primary" onclick="openAddSubject()">+ Add Subject</button>
  </div>
</div>

<div class="card">
  <div class="card-header" style="flex-wrap:wrap;gap:12px;">
    <h3>All Subjects</h3>
    <div class="filter-bar" style="margin:0">
      <select class="form-control" id="filter-dept" onchange="loadSubjects()">
        <option value="">All Departments</option>
      </select>
      <select class="form-control" id="filter-sem" onchange="loadSubjects()">
        <option value="">All Semesters</option>
        <?php for($i=1;$i<=8;$i++): ?><option value="<?=$i?>">Semester <?=$i?></option><?php endfor; ?>
      </select>
      <div class="search-filter">
        <span class="icon">🔍</span>
        <input type="text" id="search-sub" placeholder="Search subjects..." oninput="debounce(loadSubjects, 400)()">
      </div>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-container">
      <table id="sub-table" data-sortable>
        <thead>
          <tr><th>Code</th><th>Subject Name</th><th>Semester</th><th>Credits</th><th>Faculty</th><th>Department</th><th>Students</th><th>Activities</th><th data-sortable="false">Actions</th></tr>
        </thead>
        <tbody id="sub-tbody">
          <tr><td colspan="9" class="text-center text-muted" style="padding:40px">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modal-sub">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-sub-title">Add Subject</h3>
      <button class="modal-close" onclick="Modal.close('modal-sub')">✕</button>
    </div>
    <div class="modal-body">
      <form id="form-sub">
        <input type="hidden" id="sub-id">
        <div class="form-row">
          <div class="form-group">
            <label>Subject Name *</label>
            <input type="text" class="form-control" id="sub-name" data-required placeholder="Subject name">
            <div class="form-error"></div>
          </div>
          <div class="form-group">
            <label>Code *</label>
            <input type="text" class="form-control" id="sub-code" data-required placeholder="e.g. CS501" style="text-transform:uppercase">
            <div class="form-error"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Semester *</label>
            <select class="form-control" id="sub-sem" data-required>
              <?php for($i=1;$i<=8;$i++): ?><option value="<?=$i?>">Semester <?=$i?></option><?php endfor; ?>
            </select>
            <div class="form-error"></div>
          </div>
          <div class="form-group">
            <label>Credits</label>
            <select class="form-control" id="sub-credits">
              <?php for($i=1;$i<=6;$i++): ?><option value="<?=$i?>" <?=$i==4?'selected':''?>><?=$i?></option><?php endfor; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Department *</label>
            <select class="form-control" id="sub-dept" data-required>
              <option value="">Select Department</option>
            </select>
            <div class="form-error"></div>
          </div>
          <div class="form-group">
            <label>Faculty</label>
            <select class="form-control" id="sub-faculty">
              <option value="">— Unassigned —</option>
            </select>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-sub')">Cancel</button>
      <button class="btn btn-primary" onclick="saveSubject()">Save Subject</button>
    </div>
  </div>
</div>

<script>
async function loadOptions() {
  const deptRes = await API.get('/api/departments.php');
  if (deptRes && deptRes.success) {
    const opts = deptRes.departments.map(d => `<option value="${d.id}">${d.name} (${d.code})</option>`).join('');
    document.getElementById('filter-dept').innerHTML = '<option value="">All Departments</option>' + opts;
    document.getElementById('sub-dept').innerHTML = '<option value="">Select Department</option>' + opts;
  }
  const facRes = await API.get('/api/faculty.php');
  if (facRes && facRes.success) {
    document.getElementById('sub-faculty').innerHTML = '<option value="">— Unassigned —</option>' +
      facRes.faculty.map(f => `<option value="${f.id}">${f.name} (${f.dept_code})</option>`).join('');
  }
}

async function loadSubjects() {
  const dept = document.getElementById('filter-dept').value;
  const sem = document.getElementById('filter-sem').value;
  const search = document.getElementById('search-sub').value;
  let url = '/api/subjects.php?';
  if (dept) url += `department=${dept}&`;
  if (sem) url += `semester=${sem}&`;
  if (search) url += `search=${encodeURIComponent(search)}&`;

  const res = await API.get(url);
  if (!res || !res.success) return;
  const tbody = document.getElementById('sub-tbody');
  if (res.subjects.length === 0) {
    tbody.innerHTML = '<tr><td colspan="9"><div class="empty-state"><div class="icon">📚</div><h3>No subjects found</h3></div></td></tr>';
    return;
  }
  tbody.innerHTML = res.subjects.map(s => `
    <tr>
      <td><span class="badge badge-primary">${s.code}</span></td>
      <td><strong>${s.name}</strong></td>
      <td>Sem ${s.semester}</td>
      <td>${s.credits}</td>
      <td>${s.faculty_name || '<span class="text-muted">Unassigned</span>'}</td>
      <td>${s.dept_code}</td>
      <td>${s.student_count}</td>
      <td>${s.activity_count}</td>
      <td>
        <button class="btn btn-sm btn-secondary" onclick="editSubject(${s.id})">✏️</button>
        <button class="btn btn-sm btn-danger" onclick="deleteSubject(${s.id}, '${s.name}')">🗑️</button>
      </td>
    </tr>
  `).join('');
}

function openAddSubject() {
  document.getElementById('modal-sub-title').textContent = 'Add Subject';
  document.getElementById('sub-id').value = '';
  document.getElementById('form-sub').reset();
  Modal.open('modal-sub');
}

async function editSubject(id) {
  const res = await API.get(`/api/subjects.php?id=${id}`);
  if (!res || !res.success) return;
  const s = res.subject;
  document.getElementById('modal-sub-title').textContent = 'Edit Subject';
  document.getElementById('sub-id').value = s.id;
  document.getElementById('sub-name').value = s.name;
  document.getElementById('sub-code').value = s.code;
  document.getElementById('sub-sem').value = s.semester;
  document.getElementById('sub-credits').value = s.credits;
  document.getElementById('sub-dept').value = s.department_id;
  document.getElementById('sub-faculty').value = s.faculty_id || '';
  Modal.open('modal-sub');
}

async function saveSubject() {
  if (!FormValidator.validate('form-sub')) return;
  const id = document.getElementById('sub-id').value;
  const data = {
    name: document.getElementById('sub-name').value.trim(),
    code: document.getElementById('sub-code').value.trim().toUpperCase(),
    semester: document.getElementById('sub-sem').value,
    credits: document.getElementById('sub-credits').value,
    department_id: document.getElementById('sub-dept').value,
    faculty_id: document.getElementById('sub-faculty').value || null
  };
  const res = id ? await API.put('/api/subjects.php', { ...data, id: parseInt(id) })
                  : await API.post('/api/subjects.php', data);
  if (res && res.success) { Toast.success(res.message); Modal.close('modal-sub'); loadSubjects(); }
  else Toast.error(res?.message || 'Failed.');
}

async function deleteSubject(id, name) {
  if (!confirm(`Delete subject "${name}"? All activities and marks will be removed.`)) return;
  const res = await API.request('/api/subjects.php', { method: 'DELETE', body: JSON.stringify({ id }) });
  if (res && res.success) { Toast.success(res.message); loadSubjects(); }
  else Toast.error(res?.message || 'Failed.');
}

document.addEventListener('DOMContentLoaded', () => { loadOptions(); loadSubjects(); });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
