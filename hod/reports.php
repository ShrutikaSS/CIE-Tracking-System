<?php
/**
 * Reports Center Page for HOD
 */
$pageTitle = 'Reports Center';
require_once __DIR__ . '/../includes/header.php';
requireRole(['hod']);

$deptId    = $user['department_id'];
$deptName  = dbFetchOne("SELECT name, code FROM departments WHERE id = ?", 'i', [$deptId]);
$activeTab = $_GET['tab'] ?? 'department';

$reportTitle = '';
$reportData  = [];
$studentInfo = null;
$studentsList = [];
$selectedStudentId = null;

switch ($activeTab) {
    case 'department':
        $reportTitle = 'Department Performance Report';
        $reportData = dbFetchAll("
            SELECT s.code as subject_code, s.name as subject_name, s.semester,
                   (SELECT COUNT(*) FROM subject_students ss WHERE ss.subject_id = s.id) as student_count,
                   (SELECT COUNT(*) FROM activities a WHERE a.subject_id = s.id) as activity_count,
                   (SELECT ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) FROM marks m JOIN activities a ON m.activity_id = a.id WHERE a.subject_id = s.id AND m.is_published = 1) as class_average
            FROM subjects s
            WHERE s.department_id = ? AND s.is_active = 1
            ORDER BY s.semester, s.code
        ", 'i', [$deptId]);
        break;

    case 'faculty':
        $reportTitle = 'Faculty Activities & Performance Report';
        $reportData = dbFetchAll("
            SELECT u.name as faculty_name, f.employee_id, f.designation,
                   (SELECT COUNT(*) FROM subjects WHERE faculty_id = f.id AND is_active = 1) as subject_count,
                   (SELECT COUNT(*) FROM activities WHERE subject_id IN (SELECT id FROM subjects WHERE faculty_id = f.id)) as activity_count,
                   (SELECT ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) FROM marks m JOIN activities a ON m.activity_id = a.id JOIN subjects s ON a.subject_id = s.id WHERE s.faculty_id = f.id AND m.is_published = 1) as avg_performance
            FROM faculty f JOIN users u ON f.user_id = u.id
            WHERE f.department_id = ? ORDER BY u.name
        ", 'i', [$deptId]);
        break;

    case 'student':
        $reportTitle = 'Student Performance Report';
        $studentsList = dbFetchAll("
            SELECT s.id, s.usn, u.name
            FROM students s JOIN users u ON s.user_id = u.id
            WHERE s.department_id = ? ORDER BY s.usn
        ", 'i', [$deptId]);

        $selectedStudentId = $_GET['student_id'] ?? '';
        if (!$selectedStudentId && !empty($studentsList)) {
            $selectedStudentId = $studentsList[0]['id'];
        }

        if ($selectedStudentId) {
            $studentInfo = dbFetchOne("
                SELECT s.usn, u.name, s.semester, s.section
                FROM students s JOIN users u ON s.user_id = u.id
                WHERE s.id = ? AND s.department_id = ?
            ", 'ii', [$selectedStudentId, $deptId]);

            if ($studentInfo) {
                $reportData = dbFetchAll("
                    SELECT s.code as subject_code, s.name as subject_name,
                           ROUND(SUM(m.marks_obtained), 2) as obtained,
                           ROUND(SUM(a.max_marks), 2) as max_marks,
                           COUNT(m.id) as activities_done
                    FROM subject_students ss
                    JOIN subjects s ON ss.subject_id = s.id
                    LEFT JOIN activities a ON a.subject_id = s.id AND a.status = 'completed'
                    LEFT JOIN marks m ON m.activity_id = a.id AND m.student_id = ? AND m.is_published = 1
                    WHERE ss.student_id = ? AND s.is_active = 1
                    GROUP BY s.id, s.code, s.name ORDER BY s.code
                ", 'ii', [$selectedStudentId, $selectedStudentId]);
            }
        }
        break;

    case 'semester':
        $reportTitle = 'Semester Performance Summary Report';
        $reportData = dbFetchAll("
            SELECT s.semester,
                   COUNT(DISTINCT s.id) as subject_count,
                   (SELECT COUNT(*) FROM students WHERE semester = s.semester AND department_id = ?) as student_count,
                   COUNT(DISTINCT a.id) as activity_count,
                   ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as avg_performance
            FROM subjects s
            LEFT JOIN activities a ON a.subject_id = s.id
            LEFT JOIN marks m ON m.activity_id = a.id AND m.is_published = 1
            WHERE s.department_id = ? AND s.is_active = 1
            GROUP BY s.semester ORDER BY s.semester
        ", 'ii', [$deptId, $deptId]);
        break;
}

$generatedAt = date('d M Y, h:i A');
$deptLabel   = ($deptName['name'] ?? 'Department') . ' (' . ($deptName['code'] ?? '') . ')';
?>

<style>
/* ── Tab nav ── */
.report-tabs { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:20px; }
.report-tab {
  display:flex; align-items:center; gap:8px;
  padding:10px 20px; border-radius:10px; font-size:0.875rem; font-weight:600;
  text-decoration:none; transition:all 0.2s; border:2px solid var(--border-color);
  color:var(--text-muted); background:var(--card-bg);
}
.report-tab:hover { border-color:var(--primary); color:var(--primary); }
.report-tab.active {
  background:var(--primary); color:#fff; border-color:var(--primary);
  box-shadow:0 4px 12px rgba(99,102,241,0.3);
}
.report-tab .tab-icon { font-size:1.1rem; }

/* ── Export buttons ── */
.export-bar {
  display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;
  padding:16px 20px;
  background:linear-gradient(135deg, #f8fafc, #f1f5f9);
  border-bottom:1px solid var(--border-color);
}
.export-bar-left h3 { margin:0; font-size:1rem; }
.export-bar-left p  { margin:4px 0 0; font-size:0.78rem; color:var(--text-muted); }
.export-btns { display:flex; gap:8px; }
.btn-export-pdf {
  display:inline-flex; align-items:center; gap:7px;
  padding:9px 18px; border-radius:8px; font-size:0.85rem; font-weight:600;
  background:#ef4444; color:#fff; border:none; cursor:pointer; transition:all 0.2s;
  box-shadow:0 2px 8px rgba(239,68,68,0.25);
}
.btn-export-pdf:hover { background:#dc2626; transform:translateY(-1px); }
.btn-export-excel {
  display:inline-flex; align-items:center; gap:7px;
  padding:9px 18px; border-radius:8px; font-size:0.85rem; font-weight:600;
  background:#16a34a; color:#fff; border:none; cursor:pointer; transition:all 0.2s;
  box-shadow:0 2px 8px rgba(22,163,74,0.25);
}
.btn-export-excel:hover { background:#15803d; transform:translateY(-1px); }

/* ── Report header block (shows in print) ── */
.report-meta-strip {
  display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:0;
  border-bottom:1px solid var(--border-color);
}
.rms-cell {
  padding:12px 20px; border-right:1px solid var(--border-color);
  font-size:0.78rem;
}
.rms-cell:last-child { border-right:none; }
.rms-label { color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; font-weight:600; margin-bottom:2px; }
.rms-val   { font-weight:700; color:var(--text-dark); font-size:0.9rem; }

/* ── Student selector ── */
.student-selector {
  display:flex; align-items:center; gap:12px; padding:14px 20px;
  border-bottom:1px solid var(--border-color); background:var(--card-bg);
  flex-wrap:wrap;
}
.student-selector label { font-weight:600; font-size:0.875rem; white-space:nowrap; }
.student-selector select { max-width:340px; }

/* ── Performance badge in table ── */
.perf-badge {
  display:inline-block; padding:3px 10px; border-radius:20px;
  font-size:0.75rem; font-weight:700;
}
.perf-good    { background:#dcfce7; color:#15803d; }
.perf-avg     { background:#fef3c7; color:#92400e; }
.perf-low     { background:#fee2e2; color:#b91c1c; }
.perf-none    { background:#f1f5f9; color:#64748b; }

/* ── Print styles ── */
@media print {
  .report-tabs, .export-bar, .app-sidebar, .app-header, .page-header { display:none !important; }
  .print-header { display:block !important; }
  body, .main-content { margin:0 !important; padding:0 !important; }
}
.print-header { display:none; padding:16px 20px 12px; border-bottom:2px solid #e2e8f0; }
</style>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h1>Reports Center</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Reports</div>
  </div>
</div>

<!-- Tab Navigation -->
<div class="report-tabs">
  <a href="?tab=department" class="report-tab <?= $activeTab === 'department' ? 'active' : '' ?>">
    <span class="tab-icon">🏢</span> Department Report
  </a>
  <a href="?tab=faculty" class="report-tab <?= $activeTab === 'faculty' ? 'active' : '' ?>">
    <span class="tab-icon">👨‍🏫</span> Faculty Report
  </a>
  <a href="?tab=student" class="report-tab <?= $activeTab === 'student' ? 'active' : '' ?>">
    <span class="tab-icon">🎓</span> Student Performance
  </a>
  <a href="?tab=semester" class="report-tab <?= $activeTab === 'semester' ? 'active' : '' ?>">
    <span class="tab-icon">📅</span> Semester Report
  </a>
</div>

<!-- Report Card -->
<div class="card" style="border-radius:16px; overflow:hidden;">

  <!-- Print-only header -->
  <div class="print-header">
    <h2 style="margin:0 0 4px;"><?= $reportTitle ?></h2>
    <p style="margin:0; font-size:0.85rem; color:#64748b;"><?= $deptLabel ?> | Generated: <?= $generatedAt ?></p>
  </div>

  <!-- Export bar -->
  <div class="export-bar">
    <div class="export-bar-left">
      <h3><?= $reportTitle ?></h3>
      <p><?= $deptLabel ?> &nbsp;·&nbsp; Generated on <?= $generatedAt ?></p>
    </div>
    <div class="export-btns">
      <button class="btn-export-pdf" onclick="exportReport('pdf')" id="btn-pdf">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Download PDF
      </button>
      <button class="btn-export-excel" onclick="exportReport('excel')" id="btn-excel">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
        Export Excel
      </button>
    </div>
  </div>

  <!-- Report meta strip -->
  <div class="report-meta-strip">
    <div class="rms-cell">
      <div class="rms-label">Report Type</div>
      <div class="rms-val"><?= ucfirst($activeTab) ?> Report</div>
    </div>
    <div class="rms-cell">
      <div class="rms-label">Department</div>
      <div class="rms-val"><?= sanitize($deptName['code'] ?? '—') ?></div>
    </div>
    <div class="rms-cell">
      <div class="rms-label">Total Records</div>
      <div class="rms-val"><?= count($reportData) ?></div>
    </div>
    <div class="rms-cell">
      <div class="rms-label">Generated At</div>
      <div class="rms-val"><?= $generatedAt ?></div>
    </div>
  </div>

  <!-- Student selector (Student tab only) -->
  <?php if ($activeTab === 'student'): ?>
    <div class="student-selector">
      <label>🎓 Select Student:</label>
      <form method="GET" style="margin:0; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <input type="hidden" name="tab" value="student">
        <select name="student_id" class="form-control" style="max-width:340px;" onchange="this.form.submit()">
          <?php foreach ($studentsList as $stu): ?>
            <option value="<?= $stu['id'] ?>" <?= $selectedStudentId == $stu['id'] ? 'selected' : '' ?>>
              <?= $stu['usn'] ?> — <?= sanitize($stu['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
      <?php if ($studentInfo): ?>
        <span style="font-size:0.82rem; color:var(--text-muted); margin-left:6px;">
          Sem <?= $studentInfo['semester'] ?> | Section <?= sanitize($studentInfo['section'] ?? '—') ?>
        </span>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- ── Data Table ── -->
  <div class="table-container" id="report-table-div" style="border-radius:0;">
    <table id="reports-table" style="margin:0; width:100%;">

      <?php if ($activeTab === 'department'): ?>
        <thead>
          <tr>
            <th>Sem</th>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th style="text-align:center;">Students</th>
            <th style="text-align:center;">CIE Activities</th>
            <th style="text-align:center;">Class Average</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reportData)): ?>
            <tr><td colspan="6" class="text-center text-muted" style="padding:36px;">No data available.</td></tr>
          <?php else: ?>
            <?php foreach ($reportData as $row):
              $avg = $row['class_average'];
              $pClass = $avg === null ? 'perf-none' : ($avg >= 75 ? 'perf-good' : ($avg >= 50 ? 'perf-avg' : 'perf-low'));
            ?>
              <tr>
                <td><span class="badge badge-secondary">Sem <?= $row['semester'] ?></span></td>
                <td><span class="badge badge-info"><?= sanitize($row['subject_code']) ?></span></td>
                <td><strong><?= sanitize($row['subject_name']) ?></strong></td>
                <td style="text-align:center;"><?= $row['student_count'] ?></td>
                <td style="text-align:center;"><?= $row['activity_count'] ?></td>
                <td style="text-align:center;">
                  <span class="perf-badge <?= $pClass ?>"><?= $avg !== null ? $avg.'%' : '—' ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>

      <?php elseif ($activeTab === 'faculty'): ?>
        <thead>
          <tr>
            <th>Employee ID</th>
            <th>Faculty Name</th>
            <th>Designation</th>
            <th style="text-align:center;">Subjects</th>
            <th style="text-align:center;">Activities</th>
            <th style="text-align:center;">Avg Performance</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reportData)): ?>
            <tr><td colspan="6" class="text-center text-muted" style="padding:36px;">No data available.</td></tr>
          <?php else: ?>
            <?php foreach ($reportData as $row):
              $avg = $row['avg_performance'];
              $pClass = $avg === null ? 'perf-none' : ($avg >= 75 ? 'perf-good' : ($avg >= 50 ? 'perf-avg' : 'perf-low'));
            ?>
              <tr>
                <td><span class="badge badge-info"><?= sanitize($row['employee_id']) ?></span></td>
                <td><strong><?= sanitize($row['faculty_name']) ?></strong></td>
                <td style="color:var(--text-muted);"><?= sanitize($row['designation']) ?></td>
                <td style="text-align:center;"><?= $row['subject_count'] ?></td>
                <td style="text-align:center;"><?= $row['activity_count'] ?></td>
                <td style="text-align:center;">
                  <span class="perf-badge <?= $pClass ?>"><?= $avg !== null ? $avg.'%' : '—' ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>

      <?php elseif ($activeTab === 'student'): ?>
        <thead>
          <tr>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th style="text-align:center;">Activities Done</th>
            <th style="text-align:center;">Marks Obtained</th>
            <th style="text-align:center;">Max Marks</th>
            <th style="text-align:center;">Percentage</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reportData) || !$studentInfo): ?>
            <tr><td colspan="6" class="text-center text-muted" style="padding:36px;">No data available for selected student.</td></tr>
          <?php else: ?>
            <?php foreach ($reportData as $row):
              $pct = $row['max_marks'] > 0 ? round(($row['obtained'] / $row['max_marks']) * 100, 1) : 0;
              $pClass = $pct >= 75 ? 'perf-good' : ($pct >= 50 ? 'perf-avg' : 'perf-low');
            ?>
              <tr>
                <td><span class="badge badge-info"><?= sanitize($row['subject_code']) ?></span></td>
                <td><strong><?= sanitize($row['subject_name']) ?></strong></td>
                <td style="text-align:center;"><?= $row['activities_done'] ?></td>
                <td style="text-align:center;"><?= $row['obtained'] !== null ? $row['obtained'] : '—' ?></td>
                <td style="text-align:center;"><?= $row['max_marks'] !== null ? $row['max_marks'] : '—' ?></td>
                <td style="text-align:center;">
                  <span class="perf-badge <?= $pClass ?>"><?= $pct ?>%</span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>

      <?php elseif ($activeTab === 'semester'): ?>
        <thead>
          <tr>
            <th>Semester</th>
            <th style="text-align:center;">Subjects</th>
            <th style="text-align:center;">Students</th>
            <th style="text-align:center;">CIE Activities</th>
            <th style="text-align:center;">Avg Performance</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reportData)): ?>
            <tr><td colspan="5" class="text-center text-muted" style="padding:36px;">No data available.</td></tr>
          <?php else: ?>
            <?php foreach ($reportData as $row):
              $avg = $row['avg_performance'];
              $pClass = $avg === null ? 'perf-none' : ($avg >= 75 ? 'perf-good' : ($avg >= 50 ? 'perf-avg' : 'perf-low'));
            ?>
              <tr>
                <td><strong>Semester <?= $row['semester'] ?></strong></td>
                <td style="text-align:center;"><?= $row['subject_count'] ?></td>
                <td style="text-align:center;"><?= $row['student_count'] ?></td>
                <td style="text-align:center;"><?= $row['activity_count'] ?></td>
                <td style="text-align:center;">
                  <span class="perf-badge <?= $pClass ?>"><?= $avg !== null ? $avg.'%' : '—' ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      <?php endif; ?>
    </table>
  </div>

  <!-- Footer note -->
  <div style="padding:12px 20px; background:var(--light-hover); border-top:1px solid var(--border-color); font-size:0.78rem; color:var(--text-muted); display:flex; justify-content:space-between;">
    <span>CIE Marks Tracking System &nbsp;·&nbsp; <?= $deptLabel ?></span>
    <span>Report generated: <?= $generatedAt ?></span>
  </div>
</div><!-- /.card -->

<script>
function exportReport(format) {
  const tabName     = '<?= $activeTab ?>';
  const reportTitle = '<?= addslashes($reportTitle) ?>';
  const dept        = '<?= addslashes($deptLabel) ?>';
  const generated   = '<?= $generatedAt ?>';

  if (format === 'pdf') {
    const btn = document.getElementById('btn-pdf');
    btn.disabled = true;
    btn.textContent = '⏳ Generating…';
    Toast.info(`Generating PDF for "${reportTitle}"…`);

    setTimeout(() => {
      try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'landscape' });

        // Header
        doc.setFillColor(79, 70, 229);
        doc.rect(0, 0, 300, 22, 'F');
        doc.setFontSize(14);
        doc.setTextColor(255, 255, 255);
        doc.setFont(undefined, 'bold');
        doc.text(reportTitle, 14, 14);
        doc.setFontSize(9);
        doc.setFont(undefined, 'normal');
        doc.text(`${dept}   |   Generated: ${generated}`, 14, 20);

        // Table
        doc.autoTable({
          html: '#reports-table',
          startY: 28,
          theme: 'striped',
          styles: { fontSize: 9 },
          headStyles: { fillColor: [79, 70, 229], textColor: 255 },
          alternateRowStyles: { fillColor: [248, 250, 252] }
        });

        // Footer
        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
          doc.setPage(i);
          doc.setFontSize(8);
          doc.setTextColor(148, 163, 184);
          doc.text(`Page ${i} of ${pageCount}`, doc.internal.pageSize.width - 30, doc.internal.pageSize.height - 8);
        }

        doc.save(`${tabName}_report_${Date.now()}.pdf`);
        Toast.success('PDF downloaded successfully!');
      } catch (e) {
        console.error(e);
        window.print();
      }
      btn.disabled = false;
      btn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Download PDF';
    }, 800);

  } else if (format === 'excel') {
    const btn = document.getElementById('btn-excel');
    btn.disabled = true;
    btn.textContent = '⏳ Exporting…';
    Toast.info(`Exporting "${reportTitle}" to Excel…`);

    setTimeout(() => {
      try {
        const table = document.getElementById('reports-table');
        const wb    = XLSX.utils.book_new();

        // Add a cover sheet
        const coverData = [
          ['CIE Marks Tracking System'],
          [reportTitle],
          ['Department:', dept],
          ['Generated:', generated],
          []
        ];
        const coverSheet = XLSX.utils.aoa_to_sheet(coverData);
        XLSX.utils.book_append_sheet(wb, coverSheet, 'Info');

        // Add actual report data
        const ws = XLSX.utils.table_to_sheet(table);
        XLSX.utils.book_append_sheet(wb, ws, 'Report Data');

        XLSX.writeFile(wb, `${tabName}_report_${Date.now()}.xlsx`);
        Toast.success('Excel file exported successfully!');
      } catch (e) {
        console.error(e);
        Toast.error('Export failed. Please try again.');
      }
      btn.disabled = false;
      btn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg> Export Excel';
    }, 800);
  }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
