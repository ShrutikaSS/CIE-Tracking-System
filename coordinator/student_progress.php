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

<script>
// Mock data list for students (updated with overall attendance)
const mockStudents = [
  { roll: 1, prn: '120230001', name: 'Aarav Mehta', dept: 'Computer Science & Engineering', sem: 5, avg: 82.4, attendance: 88, status: 'excellent' },
  { roll: 12, prn: '120230012', name: 'Nikita Shah', dept: 'Computer Science & Engineering', sem: 5, avg: 96.8, attendance: 95, status: 'excellent' },
  { roll: 23, prn: '120230023', name: 'Ananya Sharma', dept: 'Computer Science & Engineering', sem: 5, avg: 74.5, attendance: 78, status: 'average' },
  { roll: 35, prn: '120230035', name: 'Rohan Deshmukh', dept: 'Computer Science & Engineering', sem: 5, avg: 38.2, attendance: 65, status: 'critical' },
  { roll: 45, prn: '120230045', name: 'Rahul Verma', dept: 'Computer Science & Engineering', sem: 5, avg: 78.5, attendance: 85, status: 'excellent' },
  { roll: 48, prn: '120230048', name: 'Siddharth Patil', dept: 'Computer Science & Engineering', sem: 5, avg: 61.2, attendance: 74, status: 'average' },
  { roll: 52, prn: '120230052', name: 'Sneha Joshi', dept: 'Computer Science & Engineering', sem: 5, avg: 35.0, attendance: 58, status: 'critical' },
  { roll: 55, prn: '120230055', name: 'Vikram Singh', dept: 'Computer Science & Engineering', sem: 5, avg: 48.6, attendance: 72, status: 'average' },
  { roll: 59, prn: '120230059', name: 'Yash Vardhan', dept: 'Computer Science & Engineering', sem: 5, avg: 39.5, attendance: 62, status: 'critical' },
  { roll: 60, prn: '120230060', name: 'Zoya Khan', dept: 'Computer Science & Engineering', sem: 5, avg: 91.3, attendance: 92, status: 'excellent' }
];

// Mock Subject performance detail for selected student
const mockStudentSubjectMarks = {
  'Aarav Mehta': [
    { code: 'CS501', name: 'Data Structures', obtained: 80, max: 100, pct: 80, status: 'Pass' },
    { code: 'CS502', name: 'Database Systems', obtained: 85, max: 100, pct: 85, status: 'Pass' },
    { code: 'CS503', name: 'Software Engineering', obtained: 78, max: 100, pct: 78, status: 'Pass' },
    { code: 'CS504', name: 'Computer Networks', obtained: 82, max: 100, pct: 82, status: 'Pass' },
    { code: 'CS505', name: 'Cloud Computing', obtained: 88, max: 100, pct: 88, status: 'Pass' },
    { code: 'CS506', name: 'Web Development', obtained: 81.4, max: 100, pct: 81.4, status: 'Pass' }
  ],
  'Nikita Shah': [
    { code: 'CS501', name: 'Data Structures', obtained: 95, max: 100, pct: 95, status: 'Pass' },
    { code: 'CS502', name: 'Database Systems', obtained: 98, max: 100, pct: 98, status: 'Pass' },
    { code: 'CS503', name: 'Software Engineering', obtained: 96, max: 100, pct: 96, status: 'Pass' },
    { code: 'CS504', name: 'Computer Networks', obtained: 94, max: 100, pct: 94, status: 'Pass' },
    { code: 'CS505', name: 'Cloud Computing', obtained: 99, max: 100, pct: 99, status: 'Pass' },
    { code: 'CS506', name: 'Web Development', obtained: 98.4, max: 100, pct: 98.4, status: 'Pass' }
  ],
  'Rahul Verma': [
    { code: 'CS501', name: 'Data Structures', obtained: 75, max: 100, pct: 75, status: 'Pass' },
    { code: 'CS502', name: 'Database Systems', obtained: 81, max: 100, pct: 81, status: 'Pass' },
    { code: 'CS503', name: 'Software Engineering', obtained: 80, max: 100, pct: 80, status: 'Pass' },
    { code: 'CS504', name: 'Computer Networks', obtained: 72, max: 100, pct: 72, status: 'Pass' },
    { code: 'CS505', name: 'Cloud Computing', obtained: 79, max: 100, pct: 79, status: 'Pass' },
    { code: 'CS506', name: 'Web Development', obtained: 84, max: 100, pct: 84, status: 'Pass' }
  ],
  'Rohan Deshmukh': [
    { code: 'CS501', name: 'Data Structures', obtained: 32, max: 100, pct: 32, status: 'Fail' },
    { code: 'CS502', name: 'Database Systems', obtained: 42, max: 100, pct: 42, status: 'Pass' },
    { code: 'CS503', name: 'Software Engineering', obtained: 38, max: 100, pct: 38, status: 'Fail' },
    { code: 'CS504', name: 'Computer Networks', obtained: 35, max: 100, pct: 35, status: 'Fail' },
    { code: 'CS505', name: 'Cloud Computing', obtained: 45, max: 100, pct: 45, status: 'Pass' },
    { code: 'CS506', name: 'Web Development', obtained: 37.2, max: 100, pct: 37.2, status: 'Fail' }
  ]
};

// Mock student subject-wise attendance data (with Professor Name column)
const mockStudentSubjectAttendance = {
  'Aarav Mehta': [
    { code: 'CS501', name: 'Data Structures', pct: 88, professor: 'Prof. Anil Mehta' },
    { code: 'CS502', name: 'Database Systems', pct: 90, professor: 'Prof. Anil Mehta' },
    { code: 'CS503', name: 'Software Engineering', pct: 85, professor: 'Prof. Sneha Patil' },
    { code: 'CS504', name: 'Computer Networks', pct: 86, professor: 'Prof. Rajesh K.' },
    { code: 'CS505', name: 'Cloud Computing', pct: 92, professor: 'Prof. Sneha Patil' },
    { code: 'CS506', name: 'Web Development', pct: 87, professor: 'Prof. Meera Sen' }
  ],
  'Nikita Shah': [
    { code: 'CS501', name: 'Data Structures', pct: 96, professor: 'Prof. Anil Mehta' },
    { code: 'CS502', name: 'Database Systems', pct: 94, professor: 'Prof. Anil Mehta' },
    { code: 'CS503', name: 'Software Engineering', pct: 95, professor: 'Prof. Sneha Patil' },
    { code: 'CS504', name: 'Computer Networks', pct: 93, professor: 'Prof. Rajesh K.' },
    { code: 'CS505', name: 'Cloud Computing', pct: 98, professor: 'Prof. Sneha Patil' },
    { code: 'CS506', name: 'Web Development', pct: 94, professor: 'Prof. Meera Sen' }
  ],
  'Rahul Verma': [
    { code: 'CS501', name: 'Data Structures', pct: 85, professor: 'Prof. Anil Mehta' },
    { code: 'CS502', name: 'Database Systems', pct: 88, professor: 'Prof. Anil Mehta' },
    { code: 'CS503', name: 'Software Engineering', pct: 90, professor: 'Prof. Sneha Patil' },
    { code: 'CS504', name: 'Computer Networks', pct: 80, professor: 'Prof. Rajesh K.' },
    { code: 'CS505', name: 'Cloud Computing', pct: 92, professor: 'Prof. Sneha Patil' },
    { code: 'CS506', name: 'Web Development', pct: 85, professor: 'Prof. Meera Sen' }
  ],
  'Rohan Deshmukh': [
    { code: 'CS501', name: 'Data Structures', pct: 60, professor: 'Prof. Anil Mehta' },
    { code: 'CS502', name: 'Database Systems', pct: 68, professor: 'Prof. Anil Mehta' },
    { code: 'CS503', name: 'Software Engineering', pct: 62, professor: 'Prof. Sneha Patil' },
    { code: 'CS504', name: 'Computer Networks', pct: 70, professor: 'Prof. Rajesh K.' },
    { code: 'CS505', name: 'Cloud Computing', pct: 65, professor: 'Prof. Sneha Patil' },
    { code: 'CS506', name: 'Web Development', pct: 65, professor: 'Prof. Meera Sen' }
  ]
};

// Default templates for missing mock lists
const defaultSubjectMarks = [
  { code: 'CS501', name: 'Data Structures', obtained: 60, max: 100, pct: 60, status: 'Pass' },
  { code: 'CS502', name: 'Database Systems', obtained: 68, max: 100, pct: 68, status: 'Pass' },
  { code: 'CS503', name: 'Software Engineering', obtained: 71, max: 100, pct: 71, status: 'Pass' },
  { code: 'CS504', name: 'Computer Networks', obtained: 55, max: 100, pct: 55, status: 'Pass' },
  { code: 'CS505', name: 'Cloud Computing', obtained: 75, max: 100, pct: 75, status: 'Pass' },
  { code: 'CS506', name: 'Web Development', obtained: 65, max: 100, pct: 65, status: 'Pass' }
];

const defaultSubjectAttendance = [
  { code: 'CS501', name: 'Data Structures', pct: 80, professor: 'Prof. Anil Mehta' },
  { code: 'CS502', name: 'Database Systems', pct: 78, professor: 'Prof. Anil Mehta' },
  { code: 'CS503', name: 'Software Engineering', pct: 82, professor: 'Prof. Sneha Patil' },
  { code: 'CS504', name: 'Computer Networks', pct: 75, professor: 'Prof. Rajesh K.' },
  { code: 'CS505', name: 'Cloud Computing', pct: 85, professor: 'Prof. Sneha Patil' },
  { code: 'CS506', name: 'Web Development', pct: 80, professor: 'Prof. Meera Sen' }
];

// Activity marks mock details
const mockStudentActivityDetails = {
  'Rahul Verma': [
    { subject: 'Data Structures', name: 'Mid-Sem Test 1', type: 'Test', obtained: 37, max: 50, pct: 74, date: '2026-06-15', status: 'Pass' },
    { subject: 'Data Structures', name: 'Assignment 1', type: 'Assignment', obtained: 18, max: 20, pct: 90, date: '2026-06-25', status: 'Pass' },
    { subject: 'Data Structures', name: 'Quiz 1', type: 'Quiz', obtained: 8, max: 10, pct: 80, date: '2026-07-02', status: 'Pass' },
    { subject: 'Database Systems', name: 'Mid-Sem Test 2', type: 'Test', obtained: 42, max: 50, pct: 84, date: '2026-06-20', status: 'Pass' },
    { subject: 'Database Systems', name: 'Assignment 2', type: 'Assignment', obtained: 17, max: 20, pct: 85, date: '2026-06-30', status: 'Pass' },
    { subject: 'Database Systems', name: 'Quiz 2', type: 'Quiz', obtained: 9, max: 10, pct: 90, date: '2026-07-10', status: 'Pass' }
  ],
  'Rohan Deshmukh': [
    { subject: 'Data Structures', name: 'Mid-Sem Test 1', type: 'Test', obtained: 15, max: 50, pct: 30, date: '2026-06-15', status: 'Fail' },
    { subject: 'Data Structures', name: 'Assignment 1', type: 'Assignment', obtained: 12, max: 20, pct: 60, date: '2026-06-25', status: 'Pass' },
    { subject: 'Data Structures', name: 'Quiz 1', type: 'Quiz', obtained: 3, max: 10, pct: 30, date: '2026-07-02', status: 'Fail' },
    { subject: 'Database Systems', name: 'Mid-Sem Test 2', type: 'Test', obtained: 20, max: 50, pct: 40, date: '2026-06-20', status: 'Pass' },
    { subject: 'Database Systems', name: 'Assignment 2', type: 'Assignment', obtained: 11, max: 20, pct: 55, date: '2026-06-30', status: 'Pass' }
  ]
};

const defaultActivityDetails = [
  { subject: 'Data Structures', name: 'Mid-Sem Test 1', type: 'Test', obtained: 30, max: 50, pct: 60, date: '2026-06-15', status: 'Pass' },
  { subject: 'Data Structures', name: 'Assignment 1', type: 'Assignment', obtained: 15, max: 20, pct: 75, date: '2026-06-25', status: 'Pass' },
  { subject: 'Database Systems', name: 'Mid-Sem Test 2', type: 'Test', obtained: 34, max: 50, pct: 68, date: '2026-06-20', status: 'Pass' },
  { subject: 'Database Systems', name: 'Assignment 2', type: 'Assignment', obtained: 14, max: 20, pct: 70, date: '2026-06-30', status: 'Pass' }
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
