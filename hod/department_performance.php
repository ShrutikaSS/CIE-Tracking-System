<?php
/**
 * Department Performance Page for HOD
 */
$pageTitle = 'Department Performance';
require_once __DIR__ . '/../includes/header.php';
requireRole(['hod']);

$deptId = $user['department_id'];

// Get unique semesters, subjects, and academic years for filters
$subjects = dbFetchAll("SELECT id, name, code FROM subjects WHERE department_id = ? AND is_active = 1 ORDER BY code", 'i', [$deptId]);
$academicYears = dbFetchAll("SELECT DISTINCT admission_year FROM students WHERE department_id = ? AND admission_year IS NOT NULL ORDER BY admission_year DESC", 'i', [$deptId]);

// Filters
$selectedSem = $_GET['semester'] ?? '';
$selectedYear = $_GET['academic_year'] ?? '';
$selectedSub = $_GET['subject'] ?? '';

// Build dynamic WHERE clause for performance
$whereClauses = ["s.department_id = ?"];
$types = 'i';
$params = [$deptId];

if ($selectedSem) {
    $whereClauses[] = "s.semester = ?";
    $types .= 'i';
    $params[] = (int)$selectedSem;
}
if ($selectedYear) {
    $whereClauses[] = "st.admission_year = ?";
    $types .= 'i';
    $params[] = (int)$selectedYear;
}
if ($selectedSub) {
    $whereClauses[] = "s.id = ?";
    $types .= 'i';
    $params[] = (int)$selectedSub;
}

$whereSql = implode(' AND ', $whereClauses);

// Fetch Overall Department Performance Stats
$statsQuery = "
    SELECT 
        ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as avg_pct,
        ROUND(MIN(m.marks_obtained / a.max_marks * 100), 1) as min_pct,
        ROUND(MAX(m.marks_obtained / a.max_marks * 100), 1) as max_pct
    FROM marks m
    JOIN activities a ON m.activity_id = a.id
    JOIN subjects s ON a.subject_id = s.id
    JOIN students st ON st.id = m.student_id
    WHERE $whereSql AND m.is_published = 1
";
$perfStats = dbFetchOne($statsQuery, $types, $params);
$avgPerf = $perfStats['avg_pct'] ?? 0;
$minPerf = $perfStats['min_pct'] ?? 0;
$maxPerf = $perfStats['max_pct'] ?? 0;

// Fetch Subject-wise averages for charts
$subjectPerf = dbFetchAll("
    SELECT s.code as label, ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as value
    FROM marks m
    JOIN activities a ON m.activity_id = a.id
    JOIN subjects s ON a.subject_id = s.id
    JOIN students st ON st.id = m.student_id
    WHERE $whereSql AND m.is_published = 1
    GROUP BY s.id, s.code
    ORDER BY s.code
", $types, $params);

// Fetch Semester-wise averages for charts
$semesterPerf = dbFetchAll("
    SELECT CONCAT('Sem ', s.semester) as label, ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) as value
    FROM marks m
    JOIN activities a ON m.activity_id = a.id
    JOIN subjects s ON a.subject_id = s.id
    JOIN students st ON st.id = m.student_id
    WHERE $whereSql AND m.is_published = 1
    GROUP BY s.semester
    ORDER BY s.semester
", $types, $params);

// Faculty summary statistics
$facultySummary = dbFetchAll("
    SELECT u.name as faculty_name, f.designation,
           (SELECT GROUP_CONCAT(code SEPARATOR ', ') FROM subjects WHERE faculty_id = f.id AND is_active = 1) as subjects,
           (SELECT COUNT(*) FROM activities WHERE subject_id IN (SELECT id FROM subjects WHERE faculty_id = f.id)) as activities_conducted,
           (SELECT ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) FROM marks m JOIN activities a ON m.activity_id = a.id JOIN subjects s ON a.subject_id = s.id WHERE s.faculty_id = f.id AND m.is_published = 1) as avg_pct
    FROM faculty f
    JOIN users u ON f.user_id = u.id
    WHERE f.department_id = ?
    ORDER BY u.name
", 'i', [$deptId]);
?>

<div class="page-header">
  <div>
    <h1>Department Performance</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Performance / Department</div>
  </div>
</div>

<!-- Filters -->
<style>
@media (max-width: 600px) {
  .dept-filter-form {
    display: flex !important;
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 12px !important;
  }
  .dept-filter-form .form-group {
    min-width: 100% !important;
    width: 100% !important;
  }
  .dept-filter-form .btn {
    width: 100% !important;
  }
}
</style>
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" class="form-row align-end mb-0 dept-filter-form" style="gap:16px;">
      <div class="form-group mb-0" style="flex:1; min-width:200px;">
        <label for="filter-sem">Semester</label>
        <select class="form-control" name="semester" id="filter-sem" onchange="this.form.submit()">
          <option value="">All Semesters</option>
          <?php for($i=1;$i<=8;$i++): ?>
            <option value="<?=$i?>" <?= $selectedSem == $i ? 'selected' : '' ?>>Semester <?=$i?></option>
          <?php endfor; ?>
        </select>
      </div>
      
      <div class="form-group mb-0" style="flex:1; min-width:200px;">
        <label for="filter-year">Academic Year</label>
        <select class="form-control" name="academic_year" id="filter-year" onchange="this.form.submit()">
          <option value="">All Admission Years</option>
          <?php foreach ($academicYears as $year): ?>
            <option value="<?=$year['admission_year']?>" <?= $selectedYear == $year['admission_year'] ? 'selected' : '' ?>><?=$year['admission_year']?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group mb-0" style="flex:1; min-width:200px;">
        <label for="filter-sub">Subject</label>
        <select class="form-control" name="subject" id="filter-sub" onchange="this.form.submit()">
          <option value="">All Subjects</option>
          <?php foreach ($subjects as $sub): ?>
            <option value="<?=$sub['id']?>" <?= $selectedSub == $sub['id'] ? 'selected' : '' ?>><?=$sub['code']?> — <?=$sub['name']?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group mb-0">
        <a href="/hod/department_performance.php" class="btn btn-secondary w-100">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid stagger mb-3">
  <div class="stat-card">
    <div class="stat-info">
      <h4>Overall Department Average</h4>
      <div class="stat-value text-indigo"><?=$avgPerf?>%</div>
    </div>
    <div class="stat-icon indigo">📊</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Highest Subject Average</h4>
      <div class="stat-value text-success"><?=$maxPerf?>%</div>
    </div>
    <div class="stat-icon green">📈</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Lowest Subject Average</h4>
      <div class="stat-value text-warning"><?=$minPerf?>%</div>
    </div>
    <div class="stat-icon orange">📉</div>
  </div>
</div>

<!-- Charts Row -->
<div class="grid-2 mb-3">
  <div class="card">
    <div class="card-header">
      <h3>📚 Subject-wise Performance Average</h3>
    </div>
    <div class="card-body">
      <div class="chart-container">
        <?php if (!empty($subjectPerf)): ?>
          <canvas id="chart-subject-perf"></canvas>
        <?php else: ?>
          <div class="empty-state"><div class="icon">📊</div><h3>No performance data available for filters</h3></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <div class="card">
    <div class="card-header">
      <h3>🏫 Semester-wise Performance Average</h3>
    </div>
    <div class="card-body">
      <div class="chart-container">
        <?php if (!empty($semesterPerf)): ?>
          <canvas id="chart-semester-perf"></canvas>
        <?php else: ?>
          <div class="empty-state"><div class="icon">📊</div><h3>No performance data available for filters</h3></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Faculty Summary Table -->
<div class="card">
  <div class="card-header">
    <h3>👨‍🏫 Faculty-wise Performance Summary</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Faculty Name</th>
            <th>Designation</th>
            <th>Assigned Subjects</th>
            <th>Activities Conducted</th>
            <th>Average Performance</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($facultySummary) === 0): ?>
            <tr><td colspan="5" class="text-center text-muted" style="padding:40px">No faculty members found.</td></tr>
          <?php else: ?>
            <?php foreach ($facultySummary as $fac): ?>
              <tr>
                <td><strong><?=sanitize($fac['faculty_name'])?></strong></td>
                <td><?=sanitize($fac['designation'])?></td>
                <td>
                  <?php if ($fac['subjects']): ?>
                    <?php foreach(explode(', ', $fac['subjects']) as $subCode): ?>
                      <span class="badge badge-info"><?=$subCode?></span>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <span class="text-muted">—</span>
                  <?php endif; ?>
                </td>
                <td><span class="badge badge-primary"><?=$fac['activities_conducted']?></span></td>
                <td>
                  <?php if ($fac['avg_pct'] !== null): ?>
                    <strong><?=$fac['avg_pct']?>%</strong>
                  <?php else: ?>
                    <span class="text-muted">No marks published</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  <?php if (!empty($subjectPerf)): ?>
  Charts.bar('chart-subject-perf', 
    <?= json_encode(array_column($subjectPerf, 'label')) ?>,
    [{
      label: 'Performance Average %',
      data: <?= json_encode(array_map('floatval', array_column($subjectPerf, 'value'))) ?>,
      backgroundColor: '#6366f1',
      borderRadius: 6,
      barThickness: 25
    }]
  );
  <?php endif; ?>

  <?php if (!empty($semesterPerf)): ?>
  Charts.bar('chart-semester-perf',
    <?= json_encode(array_column($semesterPerf, 'label')) ?>,
    [{
      label: 'Performance Average %',
      data: <?= json_encode(array_map('floatval', array_column($semesterPerf, 'value'))) ?>,
      backgroundColor: '#10b981',
      borderRadius: 6,
      barThickness: 25
    }]
  );
  <?php endif; ?>
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
