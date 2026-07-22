<?php
/**
 * Student Performance Page for HOD
 */
$pageTitle = 'Student Performance';
require_once __DIR__ . '/../includes/header.php';
requireRole(['hod']);

$deptId = $user['department_id'];

// Get search query
$search = trim($_GET['search'] ?? '');

// Fetch students list matching search
$sql = "
    SELECT s.id, s.usn, u.name, s.semester, s.section
    FROM students s
    JOIN users u ON s.user_id = u.id
    WHERE s.department_id = ?
";
$params = [$deptId];
$types = 'i';

if ($search) {
    $sql .= " AND (u.name LIKE ? OR s.usn LIKE ?)";
    $types .= 'ss';
    $like = "%$search%";
    array_push($params, $like, $like);
}

$sql .= " ORDER BY s.usn LIMIT 50";
$students = dbFetchAll($sql, $types, $params);

// Selected student
$selectedStudentId = $_GET['student_id'] ?? '';
if (!$selectedStudentId && !empty($students)) {
    $selectedStudentId = $students[0]['id'];
}

$studentInfo = null;
$subjectPerformance = [];
$overallPercentage = 0;
$activitiesCount = 0;

if ($selectedStudentId) {
    // Verify student belongs to HOD's department
    $studentInfo = dbFetchOne("
        SELECT s.*, u.name, u.email, d.name as dept_name, d.code as dept_code
        FROM students s
        JOIN users u ON s.user_id = u.id
        JOIN departments d ON s.department_id = d.id
        WHERE s.id = ? AND s.department_id = ?
    ", 'ii', [$selectedStudentId, $deptId]);
    
    if ($studentInfo) {
        // Fetch subject-wise marks
        $marksData = dbFetchAll("
            SELECT s.code as subject_code, s.name as subject_name,
                   ROUND(SUM(m.marks_obtained), 2) as obtained,
                   ROUND(SUM(a.max_marks), 2) as max_marks,
                   COUNT(m.id) as activities_completed
            FROM subject_students ss
            JOIN subjects s ON ss.subject_id = s.id
            LEFT JOIN activities a ON a.subject_id = s.id AND a.status = 'completed'
            LEFT JOIN marks m ON m.activity_id = a.id AND m.student_id = ? AND m.is_published = 1
            WHERE ss.student_id = ? AND s.is_active = 1
            GROUP BY s.id, s.code, s.name
            ORDER BY s.code
        ", 'ii', [$selectedStudentId, $selectedStudentId]);
        
        $totalObtained = 0;
        $totalMax = 0;
        
        foreach ($marksData as $m) {
            $pct = $m['max_marks'] > 0 ? round(($m['obtained'] / $m['max_marks']) * 100, 1) : 0;
            $subjectPerformance[] = [
                'code' => $m['subject_code'],
                'name' => $m['subject_name'],
                'obtained' => $m['obtained'] ?? 0,
                'max_marks' => $m['max_marks'] ?? 0,
                'percentage' => $pct,
                'activities_completed' => $m['activities_completed']
            ];
            
            $totalObtained += (float)$m['obtained'];
            $totalMax += (float)$m['max_marks'];
            $activitiesCount += (int)$m['activities_completed'];
        }
        
        $overallPercentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 1) : 0;
    }
}
?>

<div class="page-header">
  <div>
    <h1>Student Performance</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Performance / Student</div>
  </div>
</div>

<div class="split-layout" style="display:grid; grid-template-columns: 320px 1fr; gap:20px; align-items: start;">
  <!-- Left Column: Search & Student Selection -->
  <div class="card" style="position: sticky; top: 85px;">
    <div class="card-header" style="flex-direction:column; gap:12px; align-items: stretch;">
      <h3>Students</h3>
      <form method="GET" style="margin:0; position:relative;">
        <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);">🔍</span>
        <input type="text" name="search" class="form-control" placeholder="Search USN or Name..." value="<?=sanitize($search)?>" style="padding-left:36px;" oninput="if(this.value.length == 0) this.form.submit();">
      </form>
    </div>
    <div class="card-body p-0">
      <div class="list-group" style="display:flex; flex-direction:column; max-height: 450px; overflow-y:auto;">
        <?php if (empty($students)): ?>
          <div class="empty-state" style="padding:20px;">No students found.</div>
        <?php else: ?>
          <?php foreach ($students as $stu): ?>
            <a href="?student_id=<?=$stu['id']?>&search=<?=urlencode($search)?>" 
               class="list-group-item" 
               style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); display:flex; flex-direction:column; gap:4px; text-decoration:none; color:inherit; background-color: <?=$selectedStudentId == $stu['id'] ? 'var(--light-hover)' : 'transparent'?>;">
              <strong><?=sanitize($stu['name'])?></strong>
              <span class="text-muted" style="font-size:0.75rem;"><?=$stu['usn']?> | Sem <?=$stu['semester']?> — Sec <?=$stu['section']?></span>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Right Column: Student Performance Details -->
  <div class="details-pane">
    <?php if (!$studentInfo): ?>
      <div class="card">
        <div class="card-body">
          <div class="empty-state">
            <div class="icon">🎓</div>
            <h3>No Student Selected</h3>
            <p>Please select a student from the list or search to view their performance details.</p>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- Student Header Card -->
      <div class="card mb-3">
        <div class="card-body d-flex justify-between align-center" style="flex-wrap:wrap; gap:16px;">
          <div>
            <h2 style="font-family:var(--font-sans); margin-bottom:4px;"><?=sanitize($studentInfo['name'])?></h2>
            <div class="text-muted">
              USN: <strong><?=$studentInfo['usn']?></strong> | 
              Dept: <strong><?=sanitize($studentInfo['dept_name'])?> (<?=$studentInfo['dept_code']?>)</strong> | 
              Semester: <strong><?=$studentInfo['semester']?></strong> | 
              Section: <strong><?=$studentInfo['section']?></strong>
              <?php if($studentInfo['phone']): ?> | Phone: <strong><?=sanitize($studentInfo['phone'])?></strong><?php endif; ?>
            </div>
          </div>
          <div>
            <span class="badge <?= $overallPercentage >= 75 ? 'badge-success' : ($overallPercentage >= 50 ? 'badge-primary' : 'badge-warning') ?>" style="font-size:1.1rem; padding:8px 16px;">
              Overall: <?=$overallPercentage?>%
            </span>
          </div>
        </div>
      </div>
      
      <!-- Stats Summary cards -->
      <div class="stats-grid stagger mb-3" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="stat-card">
          <div class="stat-info">
            <h4>Overall Average</h4>
            <div class="stat-value text-indigo"><?=$overallPercentage?>%</div>
          </div>
          <div class="stat-icon indigo">📊</div>
        </div>
        <div class="stat-card">
          <div class="stat-info">
            <h4>Activities Done</h4>
            <div class="stat-value text-success"><?=$activitiesCount?></div>
          </div>
          <div class="stat-icon green">✅</div>
        </div>
        <div class="stat-card">
          <div class="stat-info">
            <h4>Enrolled Subjects</h4>
            <div class="stat-value text-purple"><?=count($subjectPerformance)?></div>
          </div>
          <div class="stat-icon purple">📚</div>
        </div>
      </div>
      
      <div class="grid-2 mb-3">
        <!-- Subject Marks Breakdown -->
        <div class="card">
          <div class="card-header">
            <h3>📊 Subject-wise Performance</h3>
          </div>
          <div class="card-body p-0">
            <div class="table-container">
              <table style="margin:0; width:100%;">
                <thead>
                  <tr>
                    <th>Subject</th>
                    <th>Activities</th>
                    <th>Marks</th>
                    <th>Percentage</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($subjectPerformance)): ?>
                    <tr><td colspan="4" class="text-center text-muted" style="padding:20px;">No subject data found.</td></tr>
                  <?php else: ?>
                    <?php foreach($subjectPerformance as $sub): ?>
                      <tr>
                        <td>
                          <div style="font-weight:600;"><?=sanitize($sub['code'])?></div>
                          <div class="text-muted" style="font-size:0.75rem;"><?=sanitize($sub['name'])?></div>
                        </td>
                        <td><?=$sub['activities_completed']?></td>
                        <td><?=$sub['obtained']?> / <?=$sub['max_marks']?></td>
                        <td>
                          <span class="badge <?= $sub['percentage'] >= 75 ? 'badge-success' : ($sub['percentage'] >= 40 ? 'badge-primary' : 'badge-danger') ?>">
                            <?=$sub['percentage']?>%
                          </span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <!-- Subject Performance Radar/Bar chart -->
        <div class="card">
          <div class="card-header">
            <h3>📈 Subject Summary Chart</h3>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <?php if(!empty($subjectPerformance)): ?>
                <canvas id="chart-student-subjects"></canvas>
              <?php else: ?>
                <div class="empty-state"><div class="icon">📊</div><h3>No performance chart available</h3></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Student Progress Summary (Progress bars) -->
      <div class="card">
        <div class="card-header">
          <h3>🎯 Subject Progress Summary</h3>
        </div>
        <div class="card-body">
          <div style="display:flex; flex-direction:column; gap:20px;">
            <?php if (empty($subjectPerformance)): ?>
              <div class="text-center text-muted">No subject progress data.</div>
            <?php else: ?>
              <?php foreach ($subjectPerformance as $sub): 
                $color = $sub['percentage'] >= 75 ? 'success' : ($sub['percentage'] >= 50 ? 'primary' : ($sub['percentage'] >= 35 ? 'warning' : 'danger'));
              ?>
                <div>
                  <div style="display:flex; justify-content:between; align-items:center; margin-bottom:6px;">
                    <span style="font-weight:600;"><?=sanitize($sub['code'])?> — <?=sanitize($sub['name'])?></span>
                    <span style="font-weight:600; font-size:0.9rem;"><?=$sub['percentage']?>%</span>
                  </div>
                  <div class="progress <?=$color?>">
                    <div class="progress-bar" style="width: <?=$sub['percentage']?>%;"></div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  <?php if (!empty($subjectPerformance)): ?>
  Charts.bar('chart-student-subjects',
    <?= json_encode(array_column($subjectPerformance, 'code')) ?>,
    [{
      label: 'Marks Percentage %',
      data: <?= json_encode(array_map('floatval', array_column($subjectPerformance, 'percentage'))) ?>,
      backgroundColor: '#3b82f6',
      borderRadius: 6,
      barThickness: 20
    }]
  );
  <?php endif; ?>
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
