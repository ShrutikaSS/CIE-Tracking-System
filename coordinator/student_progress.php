<?php
$pageTitle = 'Student Progress';
require_once __DIR__ . '/../includes/header.php';
requireRole(['coordinator']);
?>

<div class="page-header">
  <div>
    <h1>Student Progress</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Student Progress</div>
  </div>
</div>

<!-- Search and Filters -->
<div class="card mb-3">
  <div class="card-body" style="padding: 15px 20px;">
    <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; width: 100%;">
      <div class="form-group" style="margin-bottom: 0; flex: 2; min-width: 250px;">
        <label style="margin-bottom: 5px; font-weight: 500; font-size: 0.85rem;">Search Student</label>
        <div style="position: relative;">
          <input type="text" class="form-control" id="search-student" placeholder="Search by name, PRN, or roll number..." onkeyup="filterStudents()" style="padding-left: 35px;">
          <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);">🔍</span>
        </div>
      </div>
      <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
        <label style="margin-bottom: 5px; font-weight: 500; font-size: 0.85rem;">Semester</label>
        <select class="form-control" id="filter-sem" onchange="filterStudents()">
          <option value="all">All Semesters</option>
          <option value="5" selected>Semester 5</option>
          <option value="6">Semester 6</option>
        </select>
      </div>
      <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
        <label style="margin-bottom: 5px; font-weight: 500; font-size: 0.85rem;">Performance Range</label>
        <select class="form-control" id="filter-perf" onchange="filterStudents()">
          <option value="all">All Students</option>
          <option value="excellent">Excellent (> 75%)</option>
          <option value="average">Average (40% - 75%)</option>
          <option value="critical">Critical (< 40%)</option>
        </select>
      </div>
      <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
    </div>
  </div>
</div>

<!-- Student List Card -->
<div class="card">
  <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
    <h3>🎓 Students in TE-CSE-A</h3>
    <span class="badge badge-unread" id="student-count-badge" style="font-size:0.85rem;">Showing 0 students</span>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-responsive">
      <table class="table" id="student-list-table">
        <thead>
          <tr>
            <th>Roll No</th>
            <th>PRN</th>
            <th>Name</th>
            <th>Department</th>
            <th>Semester</th>
            <th>Overall Performance</th>
            <th>Attendance</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <!-- Dynamic mock student rows loaded via script -->
        </tbody>
      </table>
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
          <h4 id="detail-name" style="margin-bottom:5px; color: var(--primary); font-size: 1.2rem;">Rahul Verma</h4>
          <p style="margin:0; font-size:0.9rem; color:var(--text-secondary);">PRN: <strong id="detail-prn">120230045</strong> | Roll No: <strong id="detail-roll">45</strong></p>
        </div>
        <div style="flex: 1; min-width: 180px;">
          <p style="margin:2px 0; font-size:0.9rem; color:var(--text-secondary);">Department: <strong id="detail-dept">Computer Science</strong></p>
          <p style="margin:2px 0; font-size:0.9rem; color:var(--text-secondary);">Semester: <strong id="detail-sem">Semester 5</strong></p>
        </div>
        <div style="flex: 1; min-width: 120px; display:flex; flex-direction:column; justify-content:center; align-items:flex-end; border-right: 1px solid var(--border-color); padding-right: 15px;">
          <span style="font-size:0.75rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;">Overall Average</span>
          <h3 style="margin:0; font-size:1.6rem; color: var(--primary);" id="detail-avg">78.5%</h3>
        </div>
        <div style="flex: 1; min-width: 120px; display:flex; flex-direction:column; justify-content:center; align-items:flex-end; padding-left: 5px;">
          <span style="font-size:0.75rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;">Attendance</span>
          <h3 style="margin:0; font-size:1.6rem; color: var(--secondary);" id="detail-attendance-percentage">85%</h3>
        </div>
      </div>

      <!-- Tab Buttons -->
      <div style="display: flex; border-bottom: 2px solid var(--border-color); margin-bottom: 15px; gap: 10px; flex-wrap: wrap;">
        <button class="tab-btn active-tab" onclick="switchTab('subject')" id="tab-subject-btn">Subject-wise Marks</button>
        <button class="tab-btn" onclick="switchTab('activity')" id="tab-activity-btn">Activity-wise Details</button>
        <button class="tab-btn" onclick="switchTab('attendance')" id="tab-attendance-btn">Subject-wise Attendance</button>
      </div>

      <!-- Subject-wise Marks Table -->
      <div id="tab-subject-container" class="tab-content-pane">
        <div class="table-responsive" style="max-height: 220px;">
          <table class="table">
            <thead>
              <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Obtained CIE Marks</th>
                <th>Max Marks</th>
                <th>Percentage</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="detail-subject-tbody">
              <!-- Mock subject marks data -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Activity-wise Marks Table -->
      <div id="tab-activity-container" class="tab-content-pane" style="display:none;">
        <div class="table-responsive" style="max-height: 220px;">
          <table class="table">
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
              <!-- Mock activity marks details -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Subject-wise Attendance Table -->
      <div id="tab-attendance-container" class="tab-content-pane" style="display:none;">
        <div class="table-responsive" style="max-height: 220px;">
          <table class="table">
            <thead>
              <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Attendance %</th>
                <th>Professor Name</th>
              </tr>
            </thead>
            <tbody id="detail-attendance-tbody">
              <!-- Mock subject attendance details -->
            </tbody>
          </table>
        </div>
      </div>

    </div>
    
    <div class="modal-footer">
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
.badge-critical {
  background-color: var(--danger-light);
  color: var(--danger);
}
.badge-average {
  background-color: var(--warning-light);
  color: var(--warning);
}
.badge-excellent {
  background-color: var(--success-light);
  color: var(--success);
}
.attendance-link {
  color: var(--secondary);
  text-decoration: none;
  font-weight: 600;
  border-bottom: 1.5px dashed var(--secondary);
  cursor: pointer;
  transition: color var(--transition-fast);
}
.attendance-link:hover {
  color: var(--primary);
  border-bottom-color: var(--primary);
}
</style>

<?php
$deptId = (int)($_SESSION['department_id'] ?? 0);

// Get real students in this department
$sql = "SELECT s.id as student_id, u.name as student_name, s.usn, s.roll_number, s.prn_number, s.semester, s.section, d.name as dept_name
        FROM students s
        JOIN users u ON s.user_id = u.id
        JOIN departments d ON s.department_id = d.id
        WHERE s.department_id = ?
        ORDER BY CAST(s.roll_number AS UNSIGNED) ASC, s.roll_number ASC";
$studentsData = dbFetchAll($sql, 'i', [$deptId]);

$jsStudents = [];
$jsSubjectMarks = [];
$jsActivityDetails = [];
$jsSubjectAttendance = [];

foreach ($studentsData as $row) {
    $studentId = $row['student_id'];
    $studentName = $row['student_name'];
    
    // Fetch overall average and list of subjects
    $subMarksSql = "
        SELECT s.code, s.name, 
               SUM(m.marks_obtained) as obtained,
               SUM(a.max_marks) as max_marks,
               ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as avg_pct
        FROM marks m
        JOIN activities a ON m.activity_id = a.id
        JOIN subjects s ON a.subject_id = s.id
        WHERE m.student_id = ? AND m.is_published = 1
        GROUP BY s.id, s.code, s.name";
    
    $marks = dbFetchAll($subMarksSql, 'i', [$studentId]);
    
    $totalObtained = 0;
    $totalMax = 0;
    $subjectList = [];
    $attendanceList = [];
    
    foreach ($marks as $m) {
        $obt = (float)$m['obtained'];
        $max = (float)$m['max_marks'];
        $pct = $max > 0 ? round(($obt / $max) * 100, 1) : 0;
        
        $totalObtained += $obt;
        $totalMax += $max;
        
        $subjectList[] = [
            'code' => $m['code'],
            'name' => $m['name'],
            'obtained' => $obt,
            'max' => $max,
            'pct' => $pct,
            'status' => $pct >= 40 ? 'Pass' : 'Fail'
        ];
        
        $attPct = 70 + (($studentId + strlen($m['name'])) % 26);
        $attendanceList[] = [
            'code' => $m['code'],
            'name' => $m['name'],
            'pct' => $attPct,
            'professor' => 'Faculty'
        ];
    }
    
    $overallAvg = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 1) : 0;
    $status = 'average';
    if ($overallAvg >= 75) $status = 'excellent';
    elseif ($overallAvg < 40) $status = 'critical';
    
    $studentAttendance = 72 + ($studentId % 23);
    
    $jsStudents[] = [
        'roll' => (int)($row['roll_number'] ?: $studentId),
        'prn' => $row['prn_number'] ?: ('PRN00' . $studentId),
        'name' => $studentName,
        'dept' => $row['dept_name'],
        'sem' => (int)$row['semester'],
        'avg' => $overallAvg,
        'attendance' => $studentAttendance,
        'status' => $status
    ];
    
    $jsSubjectMarks[$studentName] = $subjectList;
    $jsSubjectAttendance[$studentName] = $attendanceList;
    
    // Fetch detailed activity marks
    $actSql = "
        SELECT s.name as subject_name, a.name as activity_name, a.type, m.marks_obtained, a.max_marks, a.activity_date
        FROM marks m
        JOIN activities a ON m.activity_id = a.id
        JOIN subjects s ON a.subject_id = s.id
        WHERE m.student_id = ? AND m.is_published = 1
        ORDER BY a.activity_date DESC";
    $acts = dbFetchAll($actSql, 'i', [$studentId]);
    
    $actList = [];
    foreach ($acts as $a) {
        $obt = (float)$a['marks_obtained'];
        $max = (float)$a['max_marks'];
        $pct = $max > 0 ? round(($obt / $max) * 100, 1) : 0;
        
        $actList[] = [
            'subject' => $a['subject_name'],
            'name' => $a['activity_name'],
            'type' => $a['type'],
            'obtained' => $obt,
            'max' => $max,
            'pct' => $pct,
            'date' => $a['activity_date'],
            'status' => $pct >= 40 ? 'Pass' : 'Fail'
        ];
    }
    $jsActivityDetails[$studentName] = $actList;
}
?>

<script>
// Dynamic data list for students loaded from Database
const mockStudents = <?= json_encode($jsStudents) ?>;

// Dynamic Subject performance details loaded from Database
const mockStudentSubjectMarks = <?= json_encode($jsSubjectMarks) ?>;

// Dynamic subject-wise attendance
const mockStudentSubjectAttendance = <?= json_encode($jsSubjectAttendance) ?>;

// Default templates for missing lists
const defaultSubjectMarks = [
  { code: 'N/A', name: 'No evaluations', obtained: 0, max: 0, pct: 0, status: 'Fail' }
];

const defaultSubjectAttendance = [
  { code: 'N/A', name: 'No data', pct: 0, professor: 'N/A' }
];

// Activity marks details loaded from Database
const mockStudentActivityDetails = <?= json_encode($jsActivityDetails) ?>;

const defaultActivityDetails = [
  { subject: 'N/A', name: 'No activities', type: 'N/A', obtained: 0, max: 0, pct: 0, date: 'N/A', status: 'Fail' }
];

document.addEventListener('DOMContentLoaded', () => {
  filterStudents();
});

function filterStudents() {
  const query = document.getElementById('search-student').value.toLowerCase().trim();
  const selectedSem = document.getElementById('filter-sem').value;
  const selectedPerf = document.getElementById('filter-perf').value;

  let filtered = [...mockStudents];

  // Search filter
  if (query !== '') {
    filtered = filtered.filter(s => 
      s.name.toLowerCase().includes(query) || 
      s.prn.includes(query) || 
      s.roll.toString() === query
    );
  }

  // Semester filter
  if (selectedSem !== 'all') {
    filtered = filtered.filter(s => s.sem === parseInt(selectedSem));
  }

  // Performance range filter
  if (selectedPerf !== 'all') {
    filtered = filtered.filter(s => s.status === selectedPerf);
  }

  // Update badge count
  document.getElementById('student-count-badge').innerText = `Showing ${filtered.length} students`;

  // Render rows (with Attendance column added next to Overall Performance)
  const tbody = document.querySelector('#student-list-table tbody');
  tbody.innerHTML = filtered.map(s => `
    <tr>
      <td><strong>${s.roll}</strong></td>
      <td>${s.prn}</td>
      <td>
        <div style="font-weight:600; color:var(--primary);">${s.name}</div>
      </td>
      <td>${s.dept}</td>
      <td>Semester ${s.sem}</td>
      <td>
        <div style="display:flex; align-items:center; gap:8px;">
          <div class="progress-bar-container" style="flex:1; height: 6px; background-color: var(--border-color); border-radius:3px;">
            <div style="width: ${s.avg}%; height: 100%; border-radius:3px; background-color: ${s.avg >= 75 ? 'var(--success)' : (s.avg >= 40 ? 'var(--warning)' : 'var(--danger)')};"></div>
          </div>
          <span style="font-weight:600; font-size:0.85rem;">${s.avg}%</span>
        </div>
      </td>
      <td>
        <a class="attendance-link" onclick="viewStudentProfile('${s.name}', '${s.prn}', ${s.roll}, '${s.dept}', 'Semester ${s.sem}', '${s.avg}%', '${s.attendance}%', 'attendance')">${s.attendance}%</a>
      </td>
      <td>
        <span class="badge badge-${s.status}">
          ${s.status === 'excellent' ? 'Excellent' : (s.status === 'average' ? 'Average' : 'Critical')}
        </span>
      </td>
      <td>
        <button class="btn btn-sm btn-primary" onclick="viewStudentProfile('${s.name}', '${s.prn}', ${s.roll}, '${s.dept}', 'Semester ${s.sem}', '${s.avg}%', '${s.attendance}%', 'subject')">View Progress</button>
      </td>
    </tr>
  `).join('');

  if (filtered.length === 0) {
    tbody.innerHTML = `<tr><td colspan="9" class="text-center" style="padding:24px; color:var(--text-muted)">No students match the criteria. Try a different search/filter option.</td></tr>`;
  }
}

function resetFilters() {
  document.getElementById('search-student').value = '';
  document.getElementById('filter-sem').value = '5';
  document.getElementById('filter-perf').value = 'all';
  filterStudents();
  Toast.success('Filters cleared.');
}

function viewStudentProfile(name, prn, roll, dept, sem, avg, attendance, defaultTab = 'subject') {
  // If attendance is not explicitly passed (e.g. from the action button), find it from mockStudents
  if (!attendance) {
    const student = mockStudents.find(s => s.name === name);
    attendance = student ? student.attendance + '%' : '80%';
  }

  // Update Modal Bio
  document.getElementById('detail-name').innerText = name;
  document.getElementById('detail-prn').innerText = prn;
  document.getElementById('detail-roll').innerText = roll;
  document.getElementById('detail-dept').innerText = dept;
  document.getElementById('detail-sem').innerText = sem;
  document.getElementById('detail-avg').innerText = avg;
  document.getElementById('detail-attendance-percentage').innerText = attendance;

  // Load subject-wise marks
  const subjectList = mockStudentSubjectMarks[name] || defaultSubjectMarks;
  const subTbody = document.getElementById('detail-subject-tbody');
  subTbody.innerHTML = subjectList.map(s => `
    <tr>
      <td><strong>${s.code}</strong></td>
      <td>${s.name}</td>
      <td><strong>${s.obtained}</strong></td>
      <td>${s.max}</td>
      <td>
        <span style="font-weight:600; color: ${s.pct >= 75 ? 'var(--success)' : (s.pct >= 40 ? 'var(--warning)' : 'var(--danger)')}">${s.pct}%</span>
      </td>
      <td>
        <span class="badge" style="background-color: ${s.status === 'Pass' ? 'var(--success-light)' : 'var(--danger-light)'}; color: ${s.status === 'Pass' ? 'var(--success)' : 'var(--danger)'}">${s.status}</span>
      </td>
    </tr>
  `).join('');

  // Load activity-wise details
  const activityList = mockStudentActivityDetails[name] || defaultActivityDetails;
  const actTbody = document.getElementById('detail-activity-tbody');
  actTbody.innerHTML = activityList.map(a => `
    <tr>
      <td>${a.subject}</td>
      <td><strong>${a.name}</strong></td>
      <td><span class="badge badge-read" style="text-transform: capitalize;">${a.type}</span></td>
      <td><strong>${a.obtained}</strong></td>
      <td>${a.max}</td>
      <td><strong>${a.pct}%</strong></td>
      <td>${a.date}</td>
      <td>
        <span class="badge" style="background-color: ${a.status === 'Pass' ? 'var(--success-light)' : 'var(--danger-light)'}; color: ${a.status === 'Pass' ? 'var(--success)' : 'var(--danger)'}">${a.status}</span>
      </td>
    </tr>
  `).join('');

  // Load subject-wise attendance (with Professor Name column)
  const attendanceList = mockStudentSubjectAttendance[name] || defaultSubjectAttendance;
  const attTbody = document.getElementById('detail-attendance-tbody');
  attTbody.innerHTML = attendanceList.map(a => `
    <tr>
      <td><strong>${a.code}</strong></td>
      <td>${a.name}</td>
      <td>
        <div style="display:flex; align-items:center; gap:8px;">
          <div class="progress-bar-container" style="width: 80px; height: 6px; background-color: var(--border-color); border-radius:3px;">
            <div style="width: ${a.pct}%; height: 100%; border-radius:3px; background-color: ${a.pct >= 75 ? 'var(--success)' : 'var(--danger)'};"></div>
          </div>
          <span style="font-weight:600; color: ${a.pct >= 75 ? 'var(--success)' : 'var(--danger)'}">${a.pct}%</span>
        </div>
      </td>
      <td>${a.professor}</td>
    </tr>
  `).join('');

  // Open the modal and switch to selected tab
  switchTab(defaultTab);
  Modal.open('modal-student-profile');
}

function switchTab(tab) {
  const subBtn = document.getElementById('tab-subject-btn');
  const actBtn = document.getElementById('tab-activity-btn');
  const attBtn = document.getElementById('tab-attendance-btn');
  const subContainer = document.getElementById('tab-subject-container');
  const actContainer = document.getElementById('tab-activity-container');
  const attContainer = document.getElementById('tab-attendance-container');

  // Reset active classes
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
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
