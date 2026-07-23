<?php
/**
 * Faculty Performance Page for HOD — Enhanced UI
 */
$pageTitle = 'Faculty Performance';
require_once __DIR__ . '/../includes/header.php';
requireRole(['hod']);

$deptId = $user['department_id'];

$facultyList = dbFetchAll("
    SELECT
        f.id, f.employee_id, f.designation, f.phone,
        u.name, u.email,
        (SELECT COUNT(*) FROM subjects s WHERE s.faculty_id = f.id AND s.is_active = 1) AS subjects_count,
        (SELECT COUNT(*) FROM activities a JOIN subjects s ON a.subject_id = s.id WHERE s.faculty_id = f.id) AS activities_count,
        (SELECT ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1)
            FROM marks m
            JOIN activities a ON m.activity_id = a.id
            JOIN subjects s ON a.subject_id = s.id
            WHERE s.faculty_id = f.id AND m.is_published = 1
        ) AS avg_performance
    FROM faculty f
    JOIN users u ON f.user_id = u.id
    WHERE f.department_id = ?
    ORDER BY u.name
", 'i', [$deptId]);

$selectedId = isset($_GET['faculty_id']) ? (int)$_GET['faculty_id'] : ($facultyList[0]['id'] ?? null);

$facultyInfo = null;
$assignedSubjects = [];
$activitiesConducted = [];
$performanceSummary = [];
$marksStats = [];

if ($selectedId) {
    $facultyInfo = dbFetchOne("
        SELECT f.*, u.name, u.email
        FROM faculty f JOIN users u ON f.user_id = u.id
        WHERE f.id = ? AND f.department_id = ?
    ", 'ii', [$selectedId, $deptId]);

    if ($facultyInfo) {
        $assignedSubjects = dbFetchAll("
            SELECT s.*,
                (SELECT COUNT(*) FROM subject_students ss WHERE ss.subject_id = s.id) AS student_count,
                (SELECT COUNT(*) FROM activities a WHERE a.subject_id = s.id) AS activity_count
            FROM subjects s
            WHERE s.faculty_id = ? AND s.is_active = 1 ORDER BY s.code
        ", 'i', [$selectedId]);

        $activitiesConducted = dbFetchAll("
            SELECT a.*, s.code AS subject_code, s.name AS subject_name,
                (SELECT COUNT(*) FROM subject_students ss WHERE ss.subject_id = a.subject_id) AS total_students,
                (SELECT COUNT(*) FROM marks m WHERE m.activity_id = a.id) AS entered_marks,
                (SELECT COUNT(*) FROM marks m WHERE m.activity_id = a.id AND m.is_published = 1) AS published_marks
            FROM activities a JOIN subjects s ON a.subject_id = s.id
            WHERE s.faculty_id = ? ORDER BY a.activity_date DESC
        ", 'i', [$selectedId]);

        $performanceSummary = dbFetchAll("
            SELECT s.code, s.name,
                ROUND(AVG(m.marks_obtained / a.max_marks * 100), 1) AS avg_pct
            FROM marks m
            JOIN activities a ON m.activity_id = a.id
            JOIN subjects s ON a.subject_id = s.id
            WHERE s.faculty_id = ? AND m.is_published = 1
            GROUP BY s.id, s.code, s.name ORDER BY s.code
        ", 'i', [$selectedId]);

        $totalPub = $totalDraft = $totalPend = 0;
        foreach ($activitiesConducted as $act) {
            if ($act['entered_marks'] == 0) $totalPend++;
            elseif ($act['published_marks'] >= $act['entered_marks']) $totalPub++;
            else $totalDraft++;
        }
        $marksStats = ['total' => count($activitiesConducted), 'published' => $totalPub, 'draft' => $totalDraft, 'pending' => $totalPend];
    }
}

function getInitials($name) {
    $parts = explode(' ', trim($name));
    $i = strtoupper(substr($parts[0], 0, 1));
    if (count($parts) > 1) $i .= strtoupper(substr(end($parts), 0, 1));
    return $i;
}

function perfColor($pct) {
    if ($pct === null) return ['#94a3b8', '#f1f5f9', 'No Data'];
    if ($pct >= 75)    return ['#16a34a', '#dcfce7', 'Excellent'];
    if ($pct >= 50)    return ['#d97706', '#fef3c7', 'Average'];
    return ['#dc2626', '#fee2e2', 'Below Avg'];
}
?>

<style>
/* ── Page Layout ── */
.fp-layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 20px;
  align-items: start;
}
@media (max-width: 900px) {
  .fp-layout { grid-template-columns: 1fr; }
}

/* ── Faculty Card (left panel) ── */
.faculty-card-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 12px;
  max-height: calc(100vh - 160px);
  overflow-y: auto;
}
.fac-card {
  background: var(--card-bg);
  border: 2px solid var(--border-color);
  border-radius: 12px;
  padding: 14px;
  cursor: pointer;
  text-decoration: none;
  color: inherit;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: all 0.2s ease;
}
.fac-card:hover { border-color: var(--primary); transform: translateX(3px); }
.fac-card.active {
  border-color: var(--primary);
  background: linear-gradient(135deg, var(--primary)10, var(--card-bg));
  box-shadow: 0 4px 14px rgba(99,102,241,0.15);
}
.fac-avatar {
  width: 46px; height: 46px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem; font-weight: 700; flex-shrink: 0;
  color: white;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
}
.fac-card.active .fac-avatar { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
.fac-card-info { flex: 1; min-width: 0; }
.fac-card-name { font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fac-card-sub  { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.fac-card-stats { display: flex; gap: 6px; margin-top: 6px; }
.fac-mini-badge {
  font-size: 0.7rem; padding: 2px 7px; border-radius: 20px;
  background: var(--light-hover); color: var(--text-muted); font-weight: 600;
}
.fac-perf-pill {
  font-size: 0.7rem; padding: 3px 8px; border-radius: 20px; font-weight: 700; white-space: nowrap; flex-shrink: 0;
}

/* ── Right detail panel ── */
.fp-right { display: flex; flex-direction: column; gap: 20px; }

/* ── Profile strip ── */
.faculty-profile-strip {
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  border-radius: 16px;
  padding: 24px 28px;
  color: white;
  display: flex;
  align-items: center;
  gap: 20px;
  box-shadow: 0 8px 24px rgba(99,102,241,0.3);
}
.profile-big-avatar {
  width: 72px; height: 72px; border-radius: 50%;
  background: rgba(255,255,255,0.25);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.8rem; font-weight: 700; flex-shrink: 0;
  border: 3px solid rgba(255,255,255,0.5);
}
.profile-strip-info h2 { margin: 0 0 4px; font-size: 1.3rem; font-weight: 700; }
.profile-strip-info p  { margin: 0; opacity: 0.85; font-size: 0.875rem; }
.profile-strip-stats   { margin-left: auto; display: flex; gap: 20px; text-align: center; }
.pss-item .pss-val     { font-size: 1.6rem; font-weight: 800; }
.pss-item .pss-lbl     { font-size: 0.72rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.04em; }

/* ── Tabs ── */
.fp-tabs { display: flex; gap: 4px; border-bottom: 2px solid var(--border-color); padding-bottom: 0; }
.fp-tab {
  padding: 10px 18px; font-size: 0.875rem; font-weight: 600;
  cursor: pointer; border: none; background: none;
  color: var(--text-muted); border-bottom: 2px solid transparent;
  margin-bottom: -2px; transition: all 0.2s;
  display: flex; align-items: center; gap: 6px;
}
.fp-tab:hover  { color: var(--primary); }
.fp-tab.active { color: var(--primary); border-bottom-color: var(--primary); }
.fp-tab-count  { background: var(--primary); color: white; border-radius: 10px; padding: 1px 7px; font-size: 0.7rem; }

/* ── Tab panels ── */
.fp-panel { display: none; animation: fadeSlide 0.2s ease; }
.fp-panel.active { display: block; }
@keyframes fadeSlide { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }

/* ── Subject cards ── */
.subject-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px; padding: 16px; }
.sub-card {
  border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;
  background: var(--card-bg); transition: box-shadow 0.2s, transform 0.2s;
}
.sub-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); transform: translateY(-2px); }
.sub-card-code  { font-size: 0.72rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.06em; }
.sub-card-name  { font-weight: 600; margin: 4px 0 10px; font-size: 0.9rem; }
.sub-card-meta  { display: flex; gap: 10px; font-size: 0.78rem; color: var(--text-muted); }
.sub-chip { display: flex; align-items: center; gap: 4px; }

/* ── Activity status dots ── */
.status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; flex-shrink: 0; }
.dot-success { background: #22c55e; }
.dot-warning { background: #f59e0b; }
.dot-muted   { background: #94a3b8; }

/* ── Progress ring ── */
.pub-summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 16px; }
.pub-stat-card {
  border-radius: 14px; padding: 20px; display: flex; align-items: center; gap: 14px;
}
.pub-stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
.pub-stat-val  { font-size: 2rem; font-weight: 800; line-height: 1; }
.pub-stat-lbl  { font-size: 0.78rem; color: var(--text-muted); margin-top: 2px; }

/* ── Perf table rows ── */
.perf-bar-wrap  { display: flex; align-items: center; gap: 10px; }
.perf-bar-track { flex: 1; height: 8px; background: var(--border-color); border-radius: 4px; overflow: hidden; min-width: 80px; }
.perf-bar-fill  { height: 100%; border-radius: 4px; transition: width 0.8s cubic-bezier(.4,0,.2,1); }
</style>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h1>Faculty Performance</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Faculty Performance</div>
  </div>
  <div style="display:flex;align-items:center;gap:10px;">
    <span style="font-size:0.85rem;color:var(--text-muted);"><?= count($facultyList) ?> faculty in your department</span>
  </div>
</div>

<div class="fp-layout">
  <!-- ══════════════ LEFT: Faculty List ══════════════ -->
  <div class="card" style="position:sticky; top:80px; border-radius:16px; overflow:hidden;">
    <div class="card-header" style="padding:14px 16px; border-bottom:1px solid var(--border-color);">
      <h3 style="margin:0; font-size:0.95rem;">👥 Faculty</h3>
    </div>
    <!-- Search -->
    <div style="padding:10px 12px; border-bottom:1px solid var(--border-color);">
      <input type="text" id="fac-search" placeholder="🔍  Search faculty…"
        class="form-control" style="font-size:0.85rem; padding:7px 12px;"
        oninput="filterFaculty(this.value)">
    </div>
    <div class="faculty-card-list" id="fac-list">
      <?php if (empty($facultyList)): ?>
        <div class="empty-state" style="padding:24px;">No faculty found.</div>
      <?php else: ?>
        <?php foreach ($facultyList as $fac):
          $isActive = ($selectedId == $fac['id']);
          [$clr, $bg, $lbl] = perfColor($fac['avg_performance']);
          $initials = getInitials($fac['name']);
        ?>
          <a href="?faculty_id=<?= $fac['id'] ?>"
             class="fac-card <?= $isActive ? 'active' : '' ?>"
             data-name="<?= strtolower(sanitize($fac['name'])) ?>">
            <div class="fac-avatar"><?= $initials ?></div>
            <div class="fac-card-info">
              <div class="fac-card-name"><?= sanitize($fac['name']) ?></div>
              <div class="fac-card-sub"><?= sanitize($fac['designation']) ?></div>
              <div class="fac-card-stats">
                <span class="fac-mini-badge">📚 <?= $fac['subjects_count'] ?> subj</span>
                <span class="fac-mini-badge">📝 <?= $fac['activities_count'] ?> acts</span>
              </div>
            </div>
            <div class="fac-perf-pill" style="background:<?= $bg ?>; color:<?= $clr ?>;">
              <?= $fac['avg_performance'] !== null ? $fac['avg_performance'].'%' : '—' ?>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- ══════════════ RIGHT: Detail Panel ══════════════ -->
  <div class="fp-right">
    <?php if ($facultyInfo): ?>
      <?php
        [$pClr] = perfColor($marksStats['total'] > 0 ? ($marksStats['total'] > 0 ? round($marksStats['published']/$marksStats['total']*100) : null) : null);
        $avgPctCol = array_column($performanceSummary, 'avg_pct');
        $overallAvg = !empty($avgPctCol) ? round(array_sum($avgPctCol)/count($avgPctCol), 1) : null;
      ?>

      <!-- Profile Strip -->
      <div class="faculty-profile-strip">
        <div class="profile-big-avatar"><?= getInitials($facultyInfo['name']) ?></div>
        <div class="profile-strip-info">
          <h2><?= sanitize($facultyInfo['name']) ?></h2>
          <p><?= sanitize($facultyInfo['designation']) ?> &nbsp;·&nbsp; <?= sanitize($facultyInfo['employee_id']) ?></p>
          <p style="margin-top:4px;"><?= sanitize($facultyInfo['email']) ?><?= $facultyInfo['phone'] ? ' &nbsp;·&nbsp; '.$facultyInfo['phone'] : '' ?></p>
        </div>
        <div class="profile-strip-stats">
          <div class="pss-item">
            <div class="pss-val"><?= count($assignedSubjects) ?></div>
            <div class="pss-lbl">Subjects</div>
          </div>
          <div class="pss-item">
            <div class="pss-val"><?= $marksStats['total'] ?></div>
            <div class="pss-lbl">Activities</div>
          </div>
          <div class="pss-item">
            <div class="pss-val"><?= $marksStats['published'] ?></div>
            <div class="pss-lbl">Published</div>
          </div>
          <div class="pss-item">
            <div class="pss-val"><?= $overallAvg !== null ? $overallAvg.'%' : '—' ?></div>
            <div class="pss-lbl">Avg Perf</div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="card" style="border-radius:16px; overflow:hidden;">
        <div style="padding:0 20px; border-bottom:2px solid var(--border-color);">
          <div class="fp-tabs">
            <button class="fp-tab active" onclick="showTab('subjects', this)">
              📚 Assigned Subjects <span class="fp-tab-count"><?= count($assignedSubjects) ?></span>
            </button>
            <button class="fp-tab" onclick="showTab('activities', this)">
              📝 Activities <span class="fp-tab-count"><?= $marksStats['total'] ?></span>
            </button>
            <button class="fp-tab" onclick="showTab('marks', this)">
              ✅ Marks Status
            </button>
            <button class="fp-tab" onclick="showTab('performance', this)">
              📊 Performance
            </button>
          </div>
        </div>

        <!-- ── Tab 1: Assigned Subjects ── -->
        <div id="panel-subjects" class="fp-panel active">
          <?php if (empty($assignedSubjects)): ?>
            <div class="empty-state" style="padding:40px;">
              <div class="icon">📚</div><h3>No Subjects Assigned</h3>
            </div>
          <?php else: ?>
            <div class="subject-grid">
              <?php foreach ($assignedSubjects as $sub): ?>
                <div class="sub-card">
                  <div class="sub-card-code"><?= sanitize($sub['code']) ?></div>
                  <div class="sub-card-name"><?= sanitize($sub['name']) ?></div>
                  <div style="height:2px; background:var(--border-color); border-radius:2px; margin-bottom:10px;"></div>
                  <div class="sub-card-meta">
                    <span class="sub-chip">👥 <?= $sub['student_count'] ?> students</span>
                    <span class="sub-chip">📝 <?= $sub['activity_count'] ?> activities</span>
                  </div>
                  <div style="margin-top:8px; font-size:0.75rem; color:var(--text-muted);">Semester <?= (int)$sub['semester'] ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- ── Tab 2: Activities Conducted ── -->
        <div id="panel-activities" class="fp-panel">
          <?php if (empty($activitiesConducted)): ?>
            <div class="empty-state" style="padding:40px;">
              <div class="icon">📝</div><h3>No Activities Conducted</h3>
            </div>
          <?php else: ?>
            <div class="table-container">
              <table style="margin:0; width:100%;">
                <thead>
                  <tr>
                    <th>Subject</th>
                    <th>Activity</th>
                    <th>Type</th>
                    <th style="text-align:center;">Max</th>
                    <th>Date</th>
                    <th style="text-align:center;">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($activitiesConducted as $act): ?>
                    <tr>
                      <td><span class="badge badge-info" style="font-size:0.72rem;"><?= sanitize($act['subject_code']) ?></span></td>
                      <td style="font-weight:600;"><?= sanitize($act['name']) ?></td>
                      <td style="color:var(--text-muted); font-size:0.85rem;"><?= ucfirst($act['type']) ?></td>
                      <td style="text-align:center; font-weight:700;"><?= $act['max_marks'] ?></td>
                      <td style="color:var(--text-muted); font-size:0.85rem;"><?= formatDate($act['activity_date']) ?></td>
                      <td style="text-align:center;">
                        <?php if ($act['entered_marks'] == 0): ?>
                          <span style="display:inline-flex;align-items:center;padding:3px 10px;background:#f1f5f9;border-radius:20px;font-size:0.75rem;font-weight:600;color:#64748b;">
                            <span class="status-dot dot-muted"></span>Pending
                          </span>
                        <?php elseif ($act['published_marks'] < $act['entered_marks']): ?>
                          <span style="display:inline-flex;align-items:center;padding:3px 10px;background:#fef3c7;border-radius:20px;font-size:0.75rem;font-weight:600;color:#92400e;">
                            <span class="status-dot dot-warning"></span>Draft <?= $act['entered_marks'] ?>/<?= $act['total_students'] ?>
                          </span>
                        <?php else: ?>
                          <span style="display:inline-flex;align-items:center;padding:3px 10px;background:#dcfce7;border-radius:20px;font-size:0.75rem;font-weight:600;color:#15803d;">
                            <span class="status-dot dot-success"></span>Published <?= $act['published_marks'] ?>/<?= $act['total_students'] ?>
                          </span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>

        <!-- ── Tab 3: Marks Published Summary ── -->
        <div id="panel-marks" class="fp-panel">
          <div class="pub-summary-grid">
            <div class="pub-stat-card" style="background:#eff6ff;">
              <div class="pub-stat-icon" style="background:#dbeafe;">📋</div>
              <div>
                <div class="pub-stat-val" style="color:#1d4ed8;"><?= $marksStats['total'] ?></div>
                <div class="pub-stat-lbl">Total Activities</div>
              </div>
            </div>
            <div class="pub-stat-card" style="background:#f0fdf4;">
              <div class="pub-stat-icon" style="background:#dcfce7;">✅</div>
              <div>
                <div class="pub-stat-val" style="color:#15803d;"><?= $marksStats['published'] ?></div>
                <div class="pub-stat-lbl">Marks Published</div>
              </div>
            </div>
            <div class="pub-stat-card" style="background:#fffbeb;">
              <div class="pub-stat-icon" style="background:#fef3c7;">📝</div>
              <div>
                <div class="pub-stat-val" style="color:#b45309;"><?= $marksStats['draft'] ?></div>
                <div class="pub-stat-lbl">Draft / Entered</div>
              </div>
            </div>
            <div class="pub-stat-card" style="background:#fef2f2;">
              <div class="pub-stat-icon" style="background:#fee2e2;">⏳</div>
              <div>
                <div class="pub-stat-val" style="color:#b91c1c;"><?= $marksStats['pending'] ?></div>
                <div class="pub-stat-lbl">Pending Entry</div>
              </div>
            </div>
          </div>
          <?php if ($marksStats['total'] > 0):
            $pubPct = round($marksStats['published'] / $marksStats['total'] * 100);
          ?>
            <div style="padding:0 20px 20px;">
              <div style="display:flex;justify-content:space-between;font-size:0.82rem;color:var(--text-muted);margin-bottom:8px;">
                <span>Publication Progress</span>
                <strong style="color:var(--text-dark);"><?= $pubPct ?>% complete</strong>
              </div>
              <div style="background:var(--border-color);border-radius:8px;height:12px;overflow:hidden;">
                <div style="width:<?= $pubPct ?>%;height:100%;border-radius:8px;background:linear-gradient(90deg,#22c55e,#16a34a);transition:width 1s ease;"></div>
              </div>
              <div style="display:flex;gap:16px;margin-top:14px;font-size:0.8rem;">
                <span style="display:flex;align-items:center;gap:5px;"><span class="status-dot dot-success" style="width:10px;height:10px;"></span>Published</span>
                <span style="display:flex;align-items:center;gap:5px;"><span class="status-dot dot-warning" style="width:10px;height:10px;"></span>Draft</span>
                <span style="display:flex;align-items:center;gap:5px;"><span class="status-dot dot-muted" style="width:10px;height:10px;"></span>Pending</span>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- ── Tab 4: Performance Summary ── -->
        <div id="panel-performance" class="fp-panel">
          <?php if (empty($performanceSummary)): ?>
            <div class="empty-state" style="padding:40px;">
              <div class="icon">📊</div>
              <h3>No Performance Data</h3>
              <p>No published marks available for this faculty yet.</p>
            </div>
          <?php else: ?>
            <div style="padding:16px;">
              <div class="table-container" style="border-radius:12px; overflow:hidden; margin-bottom:16px;">
                <table style="margin:0;width:100%;">
                  <thead>
                    <tr>
                      <th>Subject</th>
                      <th>Performance</th>
                      <th style="text-align:center;width:110px;">Avg %</th>
                      <th style="text-align:center;">Grade</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($performanceSummary as $perf):
                      $pct = (float)$perf['avg_pct'];
                      [$clr, $bg, $lbl] = perfColor($pct);
                    ?>
                      <tr>
                        <td>
                          <div style="font-weight:600; font-size:0.88rem;"><?= sanitize($perf['name']) ?></div>
                          <div style="font-size:0.75rem; color:var(--text-muted);"><?= sanitize($perf['code']) ?></div>
                        </td>
                        <td>
                          <div class="perf-bar-wrap">
                            <div class="perf-bar-track">
                              <div class="perf-bar-fill" style="width:<?= $pct ?>%; background:<?= $clr ?>;"></div>
                            </div>
                          </div>
                        </td>
                        <td style="text-align:center; font-weight:800; font-size:1.05rem; color:<?= $clr ?>;"><?= $pct ?>%</td>
                        <td style="text-align:center;">
                          <span style="padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:700; background:<?= $bg ?>; color:<?= $clr ?>;">
                            <?= $lbl ?>
                          </span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <!-- Chart -->
              <div class="chart-container" style="max-height:260px; padding:0 8px;">
                <canvas id="chart-faculty-perf"></canvas>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div><!-- /.card -->

    <?php elseif (!empty($facultyList)): ?>
      <!-- Empty state when no faculty is selected -->
      <div class="card" style="border-radius:16px;">
        <div class="card-body">
          <div class="empty-state" style="padding:60px 24px;">
            <div class="icon" style="font-size:3rem;">👈</div>
            <h3>Select a Faculty Member</h3>
            <p>Click on any faculty from the list on the left to view their detailed performance report.</p>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="card" style="border-radius:16px;">
        <div class="card-body">
          <div class="empty-state" style="padding:60px 24px;">
            <div class="icon">👨‍🏫</div>
            <h3>No Faculty Found</h3>
            <p>No faculty members are registered in your department.</p>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div><!-- /.fp-right -->
</div><!-- /.fp-layout -->

<script>
/* Tab switching */
function showTab(id, btn) {
  document.querySelectorAll('.fp-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.fp-tab').forEach(b => b.classList.remove('active'));
  document.getElementById('panel-' + id).classList.add('active');
  btn.classList.add('active');
  if (id === 'performance') initChart();
}

/* Faculty search filter */
function filterFaculty(q) {
  q = q.toLowerCase().trim();
  document.querySelectorAll('#fac-list .fac-card').forEach(card => {
    card.style.display = card.dataset.name.includes(q) ? '' : 'none';
  });
}

/* Chart init (lazy — only when tab active) */
let chartInitialised = false;
function initChart() {
  if (chartInitialised) return;
  chartInitialised = true;
  <?php if (!empty($performanceSummary)): ?>
  Charts.bar('chart-faculty-perf',
    <?= json_encode(array_column($performanceSummary, 'code')) ?>,
    [{
      label: 'Average Performance (%)',
      data: <?= json_encode(array_map('floatval', array_column($performanceSummary, 'avg_pct'))) ?>,
      backgroundColor: <?= json_encode(array_map(function($p) {
          $pct = (float)$p['avg_pct'];
          return $pct >= 75 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
      }, $performanceSummary)) ?>,
      borderRadius: 8,
      barThickness: 30
    }]
  );
  <?php endif; ?>
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
