<?php
$pageTitle = 'Reports';
require_once __DIR__ . '/../includes/header.php';
requireRole(['coordinator']);
?>

<div class="page-header">
  <div>
    <h1>Academic Reports</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Reports</div>
  </div>
</div>

<div class="grid-2" style="grid-template-columns: 1fr 2fr;">
  <!-- Left Side: Report Selection Cards -->
  <div style="display:flex; flex-direction:column; gap:16px;">
    <div class="card report-selector-card active-card" onclick="selectReport('class')" id="card-report-class" style="cursor:pointer; border-left: 5px solid var(--primary); transition: all var(--transition-fast);">
      <div class="card-body">
        <h4 style="color:var(--primary); margin-bottom:8px; display:flex; align-items:center; gap:8px;">
          <span>📊</span> Class Performance Report
        </h4>
        <p style="font-size:0.85rem; color:var(--text-secondary); margin:0;">Overall statistics, class pass/fail averages, and performance summary across all subjects.</p>
      </div>
    </div>

    <div class="card report-selector-card" onclick="selectReport('student')" id="card-report-student" style="cursor:pointer; transition: all var(--transition-fast);">
      <div class="card-body">
        <h4 style="color:var(--text-primary); margin-bottom:8px; display:flex; align-items:center; gap:8px;">
          <span>🎓</span> Student Performance Report
        </h4>
        <p style="font-size:0.85rem; color:var(--text-secondary); margin:0;">Detailed student list with PRN, Roll numbers, individual marks, and performance status.</p>
      </div>
    </div>

    <div class="card report-selector-card" onclick="selectReport('subject')" id="card-report-subject" style="cursor:pointer; transition: all var(--transition-fast);">
      <div class="card-body">
        <h4 style="color:var(--text-primary); margin-bottom:8px; display:flex; align-items:center; gap:8px;">
          <span>📚</span> Subject Performance Report
        </h4>
        <p style="font-size:0.85rem; color:var(--text-secondary); margin:0;">Activity-wise marks breakdown, average, highest and lowest scores per curricular subject.</p>
      </div>
    </div>
  </div>

  <!-- Right Side: Report Preview Panel -->
  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <h3 id="preview-title">Report Preview: Class Performance</h3>
      <div style="display:flex; gap:10px;">
        <button class="btn btn-secondary btn-sm" onclick="exportReport('Excel')" style="display:flex; align-items:center; gap:6px;">
          <span>📥</span> Export Excel
        </button>
        <button class="btn btn-primary btn-sm" onclick="exportReport('PDF')" style="display:flex; align-items:center; gap:6px;">
          <span>📄</span> Download PDF
        </button>
      </div>
    </div>
    
    <div class="card-body" style="background-color: #fcfcfc;">
      <!-- Mock Report Preview Paper -->
      <div id="report-paper" style="background-color:#ffffff; border:1px solid var(--border-color); border-radius:var(--radius-sm); padding:30px; box-shadow: var(--shadow-sm); min-height: 500px; font-family:'Inter', sans-serif;">
        <!-- Dynamic report preview rendered here -->
      </div>
    </div>
  </div>
</div>

<style>
.report-selector-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}
.report-selector-card.active-card {
  border-left: 5px solid var(--primary) !important;
  background-color: var(--primary-lighter);
}
.report-selector-card.active-card h4 {
  color: var(--primary) !important;
}
.report-header-preview {
  text-align: center;
  border-bottom: 2px double var(--border-color);
  padding-bottom: 15px;
  margin-bottom: 20px;
}
.report-header-preview h2 {
  font-size: 1.4rem;
  color: var(--primary);
  margin-bottom: 5px;
}
.report-header-preview p {
  font-size: 0.85rem;
  color: var(--text-secondary);
  margin: 2px 0;
}
.report-section-preview {
  margin-bottom: 20px;
}
.report-section-preview h4 {
  font-size: 1rem;
  color: var(--text-primary);
  border-bottom: 1px solid var(--border-color);
  padding-bottom: 5px;
  margin-bottom: 10px;
}
</style>

<script>
// Mock reports data
const reportTemplates = {
  class: `
    <div class="report-header-preview">
      <h2>CIE MARKS TRACKING SYSTEM</h2>
      <p><strong>CLASS PERFORMANCE REPORT — SEMESTER 5</strong></p>
      <p>Class: TE-CSE-A | Department: Computer Science & Engineering</p>
      <p>Report Generated On: 22-Jul-2026</p>
    </div>
    
    <div class="report-section-preview">
      <h4>1. Class Metrics Summary</h4>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap:10px; margin-bottom:15px;">
        <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:10px; border-radius:6px; text-align:center;">
          <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">Total Enrolled</span>
          <h3 style="margin:5px 0 0 0; color:var(--primary)">60</h3>
        </div>
        <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:10px; border-radius:6px; text-align:center;">
          <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">Class Average</span>
          <h3 style="margin:5px 0 0 0; color:var(--primary)">78.5%</h3>
        </div>
        <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:10px; border-radius:6px; text-align:center;">
          <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">Overall Pass %</span>
          <h3 style="margin:5px 0 0 0; color:var(--success)">92.0%</h3>
        </div>
        <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:10px; border-radius:6px; text-align:center;">
          <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">Critical Students</span>
          <h3 style="margin:5px 0 0 0; color:var(--danger)">4</h3>
        </div>
      </div>
    </div>

    <div class="report-section-preview">
      <h4>2. Subject-wise Statistics Table</h4>
      <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
        <thead>
          <tr style="border-bottom:2px solid var(--primary); text-align:left; background:var(--primary); color:white; font-weight:bold;">
            <th style="padding:8px;">Code</th>
            <th style="padding:8px;">Subject Name</th>
            <th style="padding:8px; text-align:right;">Class Average</th>
            <th style="padding:8px; text-align:right;">Pass %</th>
            <th style="padding:8px; text-align:right;">Activities</th>
          </tr>
        </thead>
        <tbody>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">CS501</td>
            <td style="padding:8px;">Data Structures</td>
            <td style="padding:8px; text-align:right; font-weight:600;">74.2%</td>
            <td style="padding:8px; text-align:right; font-weight:600; color:var(--success);">88%</td>
            <td style="padding:8px; text-align:right;">3</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">CS502</td>
            <td style="padding:8px;">Database Systems</td>
            <td style="padding:8px; text-align:right; font-weight:600;">78.5%</td>
            <td style="padding:8px; text-align:right; font-weight:600; color:var(--success);">92%</td>
            <td style="padding:8px; text-align:right;">3</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">CS503</td>
            <td style="padding:8px;">Software Engineering</td>
            <td style="padding:8px; text-align:right; font-weight:600;">82.1%</td>
            <td style="padding:8px; text-align:right; font-weight:600; color:var(--success);">95%</td>
            <td style="padding:8px; text-align:right;">2</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">CS504</td>
            <td style="padding:8px;">Computer Networks</td>
            <td style="padding:8px; text-align:right; font-weight:600;">71.0%</td>
            <td style="padding:8px; text-align:right; font-weight:600; color:var(--warning);">85%</td>
            <td style="padding:8px; text-align:right;">3</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">CS505</td>
            <td style="padding:8px;">Cloud Computing</td>
            <td style="padding:8px; text-align:right; font-weight:600;">81.4%</td>
            <td style="padding:8px; text-align:right; font-weight:600; color:var(--success);">97%</td>
            <td style="padding:8px; text-align:right;">4</td>
          </tr>
        </tbody>
      </table>
    </div>
  `,
  student: `
    <div class="report-header-preview">
      <h2>CIE MARKS TRACKING SYSTEM</h2>
      <p><strong>STUDENT PERFORMANCE REPORT — DIVISION A</strong></p>
      <p>Class: TE-CSE-A | Department: Computer Science & Engineering</p>
      <p>Report Generated On: 22-Jul-2026</p>
    </div>

    <div class="report-section-preview">
      <h4>Student Score Sheet Overview</h4>
      <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
        <thead>
          <tr style="border-bottom:2px solid var(--primary); text-align:left; background:var(--primary); color:white; font-weight:bold;">
            <th style="padding:8px;">Roll</th>
            <th style="padding:8px;">PRN</th>
            <th style="padding:8px;">Student Name</th>
            <th style="padding:8px; text-align:right;">Overall Avg %</th>
            <th style="padding:8px; text-align:right;">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">1</td>
            <td style="padding:8px;">120230001</td>
            <td style="padding:8px;">Aarav Mehta</td>
            <td style="padding:8px; text-align:right; font-weight:600;">82.4%</td>
            <td style="padding:8px; text-align:right; color:var(--success);">Excellent</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">12</td>
            <td style="padding:8px;">120230012</td>
            <td style="padding:8px;">Nikita Shah</td>
            <td style="padding:8px; text-align:right; font-weight:600;">96.8%</td>
            <td style="padding:8px; text-align:right; color:var(--success);">Excellent</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">23</td>
            <td style="padding:8px;">120230023</td>
            <td style="padding:8px;">Ananya Sharma</td>
            <td style="padding:8px; text-align:right; font-weight:600;">74.5%</td>
            <td style="padding:8px; text-align:right; color:var(--warning);">Average</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">35</td>
            <td style="padding:8px;">120230035</td>
            <td style="padding:8px;">Rohan Deshmukh</td>
            <td style="padding:8px; text-align:right; font-weight:600;">38.2%</td>
            <td style="padding:8px; text-align:right; color:var(--danger); font-weight:600;">Critical</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">45</td>
            <td style="padding:8px;">120230045</td>
            <td style="padding:8px;">Rahul Verma</td>
            <td style="padding:8px; text-align:right; font-weight:600;">78.5%</td>
            <td style="padding:8px; text-align:right; color:var(--success);">Excellent</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">48</td>
            <td style="padding:8px;">120230048</td>
            <td style="padding:8px;">Siddharth Patil</td>
            <td style="padding:8px; text-align:right; font-weight:600;">61.2%</td>
            <td style="padding:8px; text-align:right; color:var(--warning);">Average</td>
          </tr>
        </tbody>
      </table>
    </div>
  `,
  subject: `
    <div class="report-header-preview">
      <h2>CIE MARKS TRACKING SYSTEM</h2>
      <p><strong>SUBJECT PERFORMANCE METRICS</strong></p>
      <p>Class: TE-CSE-A | Department: Computer Science & Engineering</p>
      <p>Report Generated On: 22-Jul-2026</p>
    </div>

    <div class="report-section-preview">
      <h4>Activity Evaluation Performance Breakdown</h4>
      <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
        <thead>
          <tr style="border-bottom:2px solid var(--primary); text-align:left; background:var(--primary); color:white; font-weight:bold;">
            <th style="padding:8px;">Subject</th>
            <th style="padding:8px;">Activity Name</th>
            <th style="padding:8px;">Type</th>
            <th style="padding:8px; text-align:right;">Max</th>
            <th style="padding:8px; text-align:right;">Avg Score</th>
            <th style="padding:8px; text-align:right;">Highest</th>
            <th style="padding:8px; text-align:right;">Lowest</th>
          </tr>
        </thead>
        <tbody>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">Data Structures</td>
            <td style="padding:8px;">Mid-Sem Test 1</td>
            <td style="padding:8px;">Test</td>
            <td style="padding:8px; text-align:right;">50</td>
            <td style="padding:8px; text-align:right; font-weight:600;">36.5 (73%)</td>
            <td style="padding:8px; text-align:right; color:var(--success);">49</td>
            <td style="padding:8px; text-align:right; color:var(--danger);">18</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">Data Structures</td>
            <td style="padding:8px;">Assignment 1</td>
            <td style="padding:8px;">Assignment</td>
            <td style="padding:8px; text-align:right;">20</td>
            <td style="padding:8px; text-align:right; font-weight:600;">17.8 (89%)</td>
            <td style="padding:8px; text-align:right; color:var(--success);">20</td>
            <td style="padding:8px; text-align:right; color:var(--danger);">10</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">Database Systems</td>
            <td style="padding:8px;">Mid-Sem Test 2</td>
            <td style="padding:8px;">Test</td>
            <td style="padding:8px; text-align:right;">50</td>
            <td style="padding:8px; text-align:right; font-weight:600;">38.0 (76%)</td>
            <td style="padding:8px; text-align:right; color:var(--success);">48</td>
            <td style="padding:8px; text-align:right; color:var(--danger);">22</td>
          </tr>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:8px;">Software Engineering</td>
            <td style="padding:8px;">Seminar 1</td>
            <td style="padding:8px;">Seminar</td>
            <td style="padding:8px; text-align:right;">30</td>
            <td style="padding:8px; text-align:right; font-weight:600;">24.6 (82%)</td>
            <td style="padding:8px; text-align:right; color:var(--success);">30</td>
            <td style="padding:8px; text-align:right; color:var(--danger);">15</td>
          </tr>
        </tbody>
      </table>
    </div>
  `
};

let currentReportType = 'class';

document.addEventListener('DOMContentLoaded', () => {
  selectReport('class');
});

function selectReport(type) {
  currentReportType = type;
  
  // Set active selector card
  document.querySelectorAll('.report-selector-card').forEach(card => {
    card.classList.remove('active-card');
    card.style.borderLeft = 'none';
    card.style.backgroundColor = 'var(--bg-card)';
    card.querySelector('h4').style.color = 'var(--text-primary)';
  });

  const activeCard = document.getElementById(`card-report-${type}`);
  activeCard.classList.add('active-card');
  activeCard.style.borderLeft = '5px solid var(--primary)';
  activeCard.style.backgroundColor = 'var(--primary-lighter)';
  activeCard.querySelector('h4').style.color = 'var(--primary)';

  // Update Title and Content
  const titles = {
    class: 'Report Preview: Class Performance',
    student: 'Report Preview: Student Performance',
    subject: 'Report Preview: Subject Performance'
  };
  document.getElementById('preview-title').innerText = titles[type];
  document.getElementById('report-paper').innerHTML = reportTemplates[type];
}

function exportReport(format) {
  const fileNames = {
    class: 'Class_Performance_Report',
    student: 'Student_Performance_Report',
    subject: 'Subject_Performance_Report'
  };
  
  const ext = format === 'Excel' ? 'xlsx' : 'pdf';
  Toast.info(`Generating ${format} file...`);
  
  setTimeout(() => {
    Toast.success(`Downloaded ${fileNames[currentReportType]}.${ext} successfully!`);
  }, 1200);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
