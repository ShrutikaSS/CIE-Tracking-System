<!-- Student Dashboard View -->
<style>
  /* Premium Vercel & Glassmorphism Dashboard Styling */
  :root {
    --card-bg-glass: rgba(255, 255, 255, 0.7);
    --card-border-glass: rgba(255, 255, 255, 0.45);
    --radius-premium: 16px;
    --shadow-premium: 0 10px 30px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.01);
    --shadow-premium-hover: 0 20px 40px rgba(13, 58, 113, 0.08), 0 2px 6px rgba(0, 0, 0, 0.02);
  }
  
  .dark-theme {
    --card-bg-glass: rgba(30, 41, 59, 0.7);
    --card-border-glass: rgba(255, 255, 255, 0.06);
    --shadow-premium: 0 10px 30px rgba(0, 0, 0, 0.2), 0 1px 3px rgba(0, 0, 0, 0.1);
    --shadow-premium-hover: 0 20px 40px rgba(0, 0, 0, 0.35), 0 2px 6px rgba(0, 0, 0, 0.15);
  }

  .premium-card {
    background: var(--card-bg-glass);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--card-border-glass);
    border-radius: var(--radius-premium);
    box-shadow: var(--shadow-premium);
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease, border-color 0.3s;
    margin-bottom: 24px;
    overflow: hidden;
  }
  
  .premium-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-premium-hover);
    border-color: rgba(13, 58, 113, 0.25);
  }

  /* Stats Grid & Ring Layout */
  .stats-ring-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
  }

  .stat-ring-card {
    padding: 24px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
  }

  /* Radial Progress Ring */
  .progress-ring-container {
    position: relative;
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  
  .progress-ring-circle-bg {
    fill: transparent;
    stroke: var(--border-light);
    stroke-width: 4.5;
  }
  
  .progress-ring-circle {
    fill: transparent;
    stroke-width: 4.5;
    stroke-linecap: round;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
    transition: stroke-dashoffset 0.65s cubic-bezier(0.16, 1, 0.3, 1);
  }
  
  .progress-ring-text {
    position: absolute;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-primary);
  }



  /* Attendance Progress Section */
  .attendance-subject-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-light);
  }
  .attendance-subject-row:last-child {
    border-bottom: none;
  }

  /* Attendance Split Layout */
  .attendance-split-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 32px;
    align-items: center;
  }

  @media (max-width: 768px) {
    .attendance-split-container {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    .attendance-left-sub {
      border-right: none !important;
      padding-right: 0 !important;
      margin-bottom: 16px;
    }
  }
</style>

<div class="page-header">
  <div>
    <h1 style="font-family:'Lora', serif; font-weight:700;">My Dashboard</h1>
    <div class="breadcrumb" style="font-weight: 500; color: var(--text-secondary);">Welcome, <?= sanitize($user['name']) ?></div>
  </div>
</div>



<!-- 2. STATS PROGRESS RINGS -->
<div class="stats-ring-grid stagger">
  <!-- Card 1: Total Activities -->
  <div class="premium-card stat-ring-card">
    <div>
      <h4 class="text-muted" style="font-size:0.8rem; text-transform:uppercase; font-weight:700; letter-spacing:0.5px; margin-bottom:8px;">Total Activities</h4>
      <div class="stat-value" id="stat-total" style="font-size:1.8rem; font-weight:800; color:var(--primary);">0</div>
    </div>
    <div class="progress-ring-container" style="width: 72px; height: 72px;">
      <svg class="progress-ring" width="72" height="72">
        <circle class="progress-ring-circle-bg" cx="36" cy="36" r="30" stroke-width="5"/>
        <circle class="progress-ring-circle" id="ring-total" cx="36" cy="36" r="30" stroke="#3b82f6" stroke-width="5" stroke-dasharray="188.49" stroke-dashoffset="188.49"/>
      </svg>
      <div class="progress-ring-text" id="ring-total-txt" style="font-size:0.85rem;">100%</div>
    </div>
  </div>

  <!-- Card 2: Completed Activities -->
  <div class="premium-card stat-ring-card">
    <div>
      <h4 class="text-muted" style="font-size:0.8rem; text-transform:uppercase; font-weight:700; letter-spacing:0.5px; margin-bottom:8px;">Completed</h4>
      <div class="stat-value" id="stat-completed" style="font-size:1.8rem; font-weight:800; color:var(--success);">0</div>
    </div>
    <div class="progress-ring-container" style="width: 72px; height: 72px;">
      <svg class="progress-ring" width="72" height="72">
        <circle class="progress-ring-circle-bg" cx="36" cy="36" r="30" stroke-width="5"/>
        <circle class="progress-ring-circle" id="ring-completed" cx="36" cy="36" r="30" stroke="#10b981" stroke-width="5" stroke-dasharray="188.49" stroke-dashoffset="188.49"/>
      </svg>
      <div class="progress-ring-text" id="ring-completed-txt" style="font-size:0.85rem;">0%</div>
    </div>
  </div>

  <!-- Card 3: Pending Activities -->
  <div class="premium-card stat-ring-card">
    <div>
      <h4 class="text-muted" style="font-size:0.8rem; text-transform:uppercase; font-weight:700; letter-spacing:0.5px; margin-bottom:8px;">Pending</h4>
      <div class="stat-value" id="stat-pending" style="font-size:1.8rem; font-weight:800; color:var(--warning);">0</div>
    </div>
    <div class="progress-ring-container" style="width: 72px; height: 72px;">
      <svg class="progress-ring" width="72" height="72">
        <circle class="progress-ring-circle-bg" cx="36" cy="36" r="30" stroke-width="5"/>
        <circle class="progress-ring-circle" id="ring-pending" cx="36" cy="36" r="30" stroke="#f59e0b" stroke-width="5" stroke-dasharray="188.49" stroke-dashoffset="188.49"/>
      </svg>
      <div class="progress-ring-text" id="ring-pending-txt" style="font-size:0.85rem;">0%</div>
    </div>
  </div>

  <!-- Card 4: Overall Marks -->
  <div class="premium-card stat-ring-card">
    <div>
      <h4 class="text-muted" style="font-size:0.8rem; text-transform:uppercase; font-weight:700; letter-spacing:0.5px; margin-bottom:8px;">Overall Marks</h4>
      <div class="stat-value" id="stat-avg" style="font-size:1.6rem; font-weight:800; color:var(--purple);">0%</div>
    </div>
    <div class="progress-ring-container" style="width: 72px; height: 72px;">
      <svg class="progress-ring" width="72" height="72">
        <circle class="progress-ring-circle-bg" cx="36" cy="36" r="30" stroke-width="5"/>
        <circle class="progress-ring-circle" id="ring-avg" cx="36" cy="36" r="30" stroke="#8b5cf6" stroke-width="5" stroke-dasharray="188.49" stroke-dashoffset="188.49"/>
      </svg>
      <div class="progress-ring-text" id="ring-avg-txt" style="font-size:0.85rem;">0%</div>
    </div>
  </div>
</div>

<!-- 3. SUBJECT PERFORMANCE ANALYTICS SECTION -->
<div class="premium-card" style="padding: 24px; margin-bottom: 24px;">
  <!-- Header Title -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
    <div>
      <h3 style="display:flex; align-items:center; gap:10px; font-family:'Lora', serif; font-size:1.35rem; font-weight:700; color:var(--text-primary); margin:0;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:10px; background:rgba(13, 58, 113, 0.08); color:var(--primary);">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
        </span>
        Subject Performance Analytics
      </h3>
      <div style="font-size:0.85rem; color:var(--text-secondary); margin-top:4px;">Subject-wise percentage evaluation & performance distribution</div>
    </div>
  </div>

  <!-- Interactive Filters Bar -->
  <div class="perf-filters-bar mb-3" style="display: flex; flex-wrap: wrap; gap: 10px; padding: 14px; background: rgba(0,0,0,0.025); border-radius: 12px; border: 1px solid var(--border-light); align-items: center;">
    <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg> Filters:
    </div>

    <!-- Academic Year -->
    <select id="perf-filter-year" class="form-control perf-filter-input" style="width: auto; min-width: 120px; font-size: 0.825rem; height: 34px; padding: 4px 10px; border-radius: 6px;">
      <option value="all">Year: All</option>
      <option value="2025-2026" selected>2025-2026</option>
      <option value="2024-2025">2024-2025</option>
    </select>

    <!-- Semester -->
    <select id="perf-filter-sem" class="form-control perf-filter-input" style="width: auto; min-width: 120px; font-size: 0.825rem; height: 34px; padding: 4px 10px; border-radius: 6px;">
      <option value="all">Semester: All</option>
      <option value="1">Sem 1</option>
      <option value="2">Sem 2</option>
      <option value="3">Sem 3</option>
      <option value="4">Sem 4</option>
      <option value="5" selected>Sem 5</option>
      <option value="6">Sem 6</option>
    </select>

    <!-- Department -->
    <select id="perf-filter-dept" class="form-control perf-filter-input" style="width: auto; min-width: 120px; font-size: 0.825rem; height: 34px; padding: 4px 10px; border-radius: 6px;">
      <option value="all">Dept: All</option>
      <option value="CSE">CSE</option>
      <option value="AIML">AIML</option>
      <option value="AIDS">AIDS</option>
      <option value="IT">IT</option>
      <option value="ENTC">ENTC</option>
      <option value="ME">ME</option>
      <option value="CE">CE</option>
      <option value="EE">EE</option>
    </select>

    <!-- Division -->
    <select id="perf-filter-div" class="form-control perf-filter-input" style="width: auto; min-width: 110px; font-size: 0.825rem; height: 34px; padding: 4px 10px; border-radius: 6px;">
      <option value="all">Division: All</option>
      <option value="A">Div A</option>
      <option value="B">Div B</option>
    </select>

    <!-- CIE Exam -->
    <select id="perf-filter-cie" class="form-control perf-filter-input" style="width: auto; min-width: 130px; font-size: 0.825rem; height: 34px; padding: 4px 10px; border-radius: 6px;">
      <option value="all">CIE Exam: All</option>
      <option value="assignment">CIE-1 (Assignment)</option>
      <option value="quiz">CIE-2 (Quiz)</option>
      <option value="test">CIE-3 (Unit Test)</option>
      <option value="viva">Viva / Practical</option>
    </select>

    <!-- Reset Button -->
    <button type="button" id="perf-filter-reset" class="btn btn-sm btn-secondary" style="height: 34px; font-size: 0.8rem; padding: 0 12px; margin-left: auto; border-radius: 6px;">
      Reset Filters
    </button>
  </div>

  <!-- Summary Statistics Header Bar -->
  <div class="perf-stats-summary mb-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px;">
    <div style="background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.15); padding: 12px 16px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #2563eb; letter-spacing: 0.5px;">Overall Average</div>
        <div id="summary-perf-avg" style="font-size: 1.3rem; font-weight: 800; color: #1e40af; margin-top: 2px;">0%</div>
      </div>
      <div style="font-size: 1.4rem;">📊</div>
    </div>

    <div style="background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.15); padding: 12px 16px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #059669; letter-spacing: 0.5px;">Highest Scoring</div>
        <div id="summary-perf-highest" style="font-size: 1.05rem; font-weight: 800; color: #065f46; margin-top: 2px;">—</div>
      </div>
      <div style="font-size: 1.4rem;">🏆</div>
    </div>

    <div style="background: rgba(245, 158, 11, 0.06); border: 1px solid rgba(245, 158, 11, 0.15); padding: 12px 16px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #d97706; letter-spacing: 0.5px;">Lowest Scoring</div>
        <div id="summary-perf-lowest" style="font-size: 1.05rem; font-weight: 800; color: #92400e; margin-top: 2px;">—</div>
      </div>
      <div style="font-size: 1.4rem;">⚠️</div>
    </div>

    <div style="background: rgba(139, 92, 246, 0.06); border: 1px solid rgba(139, 92, 246, 0.15); padding: 12px 16px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
      <div>
        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #7c3aed; letter-spacing: 0.5px;">Total Subjects</div>
        <div id="summary-perf-total" style="font-size: 1.3rem; font-weight: 800; color: #5b21b6; margin-top: 2px;">0</div>
      </div>
      <div style="font-size: 1.4rem;">📚</div>
    </div>
  </div>

  <!-- Chart Canvas Container -->
  <div style="position: relative; height: 350px; width: 100%;">
    <canvas id="chart-subject-perf"></canvas>
  </div>
</div>

<!-- 4. MARKS TREND SECTION -->
<div class="premium-card mb-3">
  <div class="card-header" style="border-bottom:none; padding-bottom:0;">
    <h3 style="display:flex; align-items:center; gap:8px; font-family:'Lora', serif;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--success);"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
      Marks Trend
    </h3>
  </div>
  <div class="card-body">
    <div class="chart-container"><canvas id="chart-marks-trend"></canvas></div>
  </div>
</div>

<!-- 4. SUBJECT-WISE MARKS SUMMARY -->
<div class="premium-card" style="padding: 24px;">
  <h3 style="display:flex; align-items:center; gap:8px; font-family:'Lora', serif; margin-bottom:16px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
    Subject-wise Marks Summary
  </h3>
  <div id="marks-breakdown">
    <div class="empty-state"><div class="spinner"></div></div>
  </div>
</div>



<!-- 6. RECENT ACTIVITIES & NOTIFICATIONS -->
<div class="grid-2 mb-3">
  <div class="premium-card">
    <div class="card-header">
      <h3 style="display:flex; align-items:center; gap:8px; font-family:'Lora', serif;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--info);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        Recent Activities
      </h3>
    </div>
    <div class="card-body p-0" id="recent-activities-list" style="max-height: 320px; overflow-y: auto;">
      <div class="empty-state"><div class="spinner"></div></div>
    </div>
  </div>
  <div class="premium-card">
    <div class="card-header">
      <h3 style="display:flex; align-items:center; gap:8px; font-family:'Lora', serif;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--warning);"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
        Recent Notifications
      </h3>
    </div>
    <div class="card-body p-0" id="recent-notifications-list" style="max-height: 320px; overflow-y: auto;">
      <div class="empty-state"><div class="spinner"></div></div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  };

  const updateProgressRing = (id, percent, circumference = 188.49) => {
    const circle = document.getElementById(id);
    const text = document.getElementById(id + '-txt');
    if (!circle) return;
    
    const offset = circumference - (Math.min(100, Math.max(0, percent)) / 100 * circumference);
    circle.style.strokeDashoffset = offset;
    
    if (text) {
      text.textContent = Math.round(percent) + '%';
    }
  };

  // Fetch Dashboard stats
  const res = await API.get('/api/dashboard.php?action=stats');
  if (res && res.success) {
    const s = res.stats;
    
    // Set text metrics
    animateCounter(document.getElementById('stat-total'), parseInt(s.total_activities));
    animateCounter(document.getElementById('stat-completed'), parseInt(s.completed));
    animateCounter(document.getElementById('stat-pending'), parseInt(s.pending));
    document.getElementById('stat-avg').textContent = s.avg_percentage + '%';

    // Update Academic Progress rings (r=30, circumference = 188.49)
    const completedPct = s.total_activities > 0 ? (s.completed / s.total_activities * 100) : 0;
    const pendingPct = s.total_activities > 0 ? (s.pending / s.total_activities * 100) : 0;

    updateProgressRing('ring-total', 100);
    updateProgressRing('ring-completed', completedPct);
    updateProgressRing('ring-pending', pendingPct);
    updateProgressRing('ring-avg', parseFloat(s.avg_percentage));





    // Recent Activities
    const actList = document.getElementById('recent-activities-list');
    if (res.recent_activities && res.recent_activities.length > 0) {
      actList.innerHTML = `
        <div class="table-container" style="border:none;">
          <table style="border:none;">
            <thead>
              <tr><th>Activity</th><th>Subject</th><th>Deadline</th></tr>
            </thead>
            <tbody>
              ${res.recent_activities.map(a => `
                <tr>
                  <td><strong>${a.name}</strong><br><small class="text-muted">${a.type}</small></td>
                  <td><span class="badge badge-secondary">${a.subject_code}</span></td>
                  <td>${formatDate(a.deadline)}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      `;
    } else {
      actList.innerHTML = '<div class="empty-state"><h3>No recent activities</h3></div>';
    }

    // Recent Notifications
    const notList = document.getElementById('recent-notifications-list');
    if (res.recent_notifications && res.recent_notifications.length > 0) {
      notList.innerHTML = res.recent_notifications.map(n => {
        let typeColor = 'blue';
        let svgIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
        
        if (n.type === 'success') {
          typeColor = 'green';
          svgIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        } else if (n.type === 'warning') {
          typeColor = 'orange';
          svgIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
        }
        
        return `
          <div style="display:flex; gap:12px; padding:12px 16px; border-bottom:1px solid var(--border-light); align-items:center;">
            <div class="stat-icon ${typeColor}" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              ${svgIcon}
            </div>
            <div style="flex:1;">
              <div style="font-weight:600; font-size:0.825rem; color:var(--text-primary);">${Notifications.escapeHtml(n.title)}</div>
              <div style="font-size:0.75rem; color:var(--text-secondary);">${Notifications.escapeHtml(n.message || '')}</div>
              <div style="font-size:0.65rem; color:var(--text-muted); margin-top:2px;">${n.time_ago}</div>
            </div>
          </div>
        `;
      }).join('');
    } else {
      notList.innerHTML = '<div class="empty-state"><h3>No recent notifications</h3></div>';
    }
  }

  // Subject Performance Chart Redesign Implementation
  let subjectChartInstance = null;

  async function loadSubjectPerformanceChart() {
    const year = document.getElementById('perf-filter-year')?.value || 'all';
    const sem = document.getElementById('perf-filter-sem')?.value || 'all';
    const dept = document.getElementById('perf-filter-dept')?.value || 'all';
    const div = document.getElementById('perf-filter-div')?.value || 'all';
    const cie = document.getElementById('perf-filter-cie')?.value || 'all';

    const params = new URLSearchParams({
      action: 'chart',
      type: 'subject_performance'
    });

    if (sem !== 'all') params.append('semester', sem);
    if (cie !== 'all') params.append('cie', cie);
    if (dept !== 'all') params.append('dept', dept);

    const res = await API.get('/api/dashboard.php?' + params.toString());
    
    const avgEl = document.getElementById('summary-perf-avg');
    const highEl = document.getElementById('summary-perf-highest');
    const lowEl = document.getElementById('summary-perf-lowest');
    const totEl = document.getElementById('summary-perf-total');

    if (!res || !res.success || !res.chart || !res.chart.length) {
      if (avgEl) avgEl.textContent = '0%';
      if (highEl) highEl.textContent = 'N/A';
      if (lowEl) lowEl.textContent = 'N/A';
      if (totEl) totEl.textContent = '0';

      if (subjectChartInstance) {
        subjectChartInstance.destroy();
        subjectChartInstance = null;
      }
      return;
    }

    let chartData = res.chart;

    // Summary Metrics
    let totalVal = 0;
    let highestItem = chartData[0];
    let lowestItem = chartData[0];

    chartData.forEach(item => {
      const val = parseFloat(item.value);
      totalVal += val;
      if (val > parseFloat(highestItem.value)) highestItem = item;
      if (val < parseFloat(lowestItem.value)) lowestItem = item;
    });

    const avgVal = (totalVal / chartData.length).toFixed(1);

    if (avgEl) avgEl.textContent = avgVal + '%';
    if (highEl) highEl.textContent = `${highestItem.label} (${highestItem.value}%)`;
    if (lowEl) lowEl.textContent = `${lowestItem.label} (${lowestItem.value}%)`;
    if (totEl) totEl.textContent = chartData.length;

    const canvas = document.getElementById('chart-subject-perf');
    if (!canvas) return;

    if (subjectChartInstance) {
      subjectChartInstance.destroy();
    }

    // Colors: Highlight Highest in vibrant Emerald Green (#10b981), others in palette
    const bgColors = chartData.map(d => {
      if (d.label === highestItem.label) return '#10b981';
      const val = parseFloat(d.value);
      if (val >= 85) return '#3b82f6';
      if (val >= 75) return '#6366f1';
      if (val >= 60) return '#8b5cf6';
      return '#f59e0b';
    });

    const borderColors = chartData.map(d => d.label === highestItem.label ? '#059669' : 'transparent');

    // Value on Top Plugin
    const valueOnTopPlugin = {
      id: 'valueOnTopPlugin',
      afterDatasetsDraw(chart) {
        const { ctx } = chart;
        chart.data.datasets.forEach((dataset, datasetIndex) => {
          const meta = chart.getDatasetMeta(datasetIndex);
          meta.data.forEach((bar, index) => {
            const val = dataset.data[index];
            if (val !== undefined && val !== null) {
              ctx.save();
              ctx.font = 'bold 11px Inter, sans-serif';
              ctx.fillStyle = chart.data.labels[index] === highestItem.label ? '#047857' : '#475569';
              ctx.textAlign = 'center';
              ctx.textBaseline = 'bottom';
              ctx.fillText(val + '%', bar.x, bar.y - 6);
              ctx.restore();
            }
          });
        });
      }
    };

    subjectChartInstance = new Chart(canvas, {
      type: 'bar',
      data: {
        labels: chartData.map(d => d.label),
        datasets: [{
          label: 'Performance %',
          data: chartData.map(d => parseFloat(d.value)),
          backgroundColor: bgColors,
          borderColor: borderColors,
          borderWidth: 2,
          borderRadius: 8,
          borderSkipped: false,
          maxBarThickness: 48
        }]
      },
      plugins: [valueOnTopPlugin],
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 800,
          easing: 'easeOutQuart'
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0f172a',
            titleFont: { family: 'Inter', size: 13, weight: '700' },
            bodyFont: { family: 'Inter', size: 12 },
            padding: 14,
            cornerRadius: 10,
            displayColors: true,
            boxPadding: 6,
            callbacks: {
              title: (items) => {
                const idx = items[0].dataIndex;
                const d = chartData[idx];
                return `${d.label} - ${d.subject_name || d.label}`;
              },
              label: (item) => {
                const idx = item.dataIndex;
                const d = chartData[idx];
                const score = item.raw;
                const isTop = d.label === highestItem.label;
                return [
                  `Score: ${score}%${isTop ? ' 🏆 Highest Performer!' : ''}`,
                  `Total Marks: ${d.total_obtained || '—'} / ${d.total_max || '—'}`,
                  `Semester: ${d.semester || '5'} (${d.dept_code || 'CSE'})`,
                  `Evaluation: CIE Assessment`
                ];
              }
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              font: { family: 'Inter', size: 12, weight: '600' },
              color: '#64748b'
            }
          },
          y: {
            min: 0,
            max: 100,
            ticks: {
              stepSize: 20,
              font: { family: 'Inter', size: 11 },
              color: '#94a3b8',
              callback: (v) => v + '%'
            },
            grid: {
              color: 'rgba(226, 232, 240, 0.6)'
            }
          }
        }
      }
    });
  }

  // Bind filter events
  document.querySelectorAll('.perf-filter-input').forEach(select => {
    select.addEventListener('change', loadSubjectPerformanceChart);
  });

  const resetBtn = document.getElementById('perf-filter-reset');
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      document.querySelectorAll('.perf-filter-input').forEach(s => s.value = 'all');
      loadSubjectPerformanceChart();
    });
  }

  // Initial load
  loadSubjectPerformanceChart();

  // Marks Trend
  const trendRes = await API.get('/api/dashboard.php?action=chart&type=marks_trend');
  if (trendRes && trendRes.success && trendRes.chart.length) {
    Charts.line('chart-marks-trend',
      trendRes.chart.map(d => d.label.substring(0, 15)),
      [{
        label: 'Performance %',
        data: trendRes.chart.map(d => parseFloat(d.value)),
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.05)',
        fill: true,
        pointBackgroundColor: '#10b981'
      }]
    );
  }

  // Marks Breakdown
  const marksRes = await API.get('/api/marks.php?action=by_student');
  if (marksRes && marksRes.success) {
    const container = document.getElementById('marks-breakdown');
    if (marksRes.marks.length === 0) {
      container.innerHTML = '<div class="empty-state"><h3>No marks published yet</h3></div>';
      return;
    }

    const subjects = {};
    marksRes.marks.forEach(m => {
      if (!subjects[m.subject_code]) {
        subjects[m.subject_code] = { name: m.subject_name, code: m.subject_code, items: [], total: 0, max: 0 };
      }
      subjects[m.subject_code].items.push(m);
      subjects[m.subject_code].total += parseFloat(m.marks_obtained);
      subjects[m.subject_code].max += parseFloat(m.max_marks);
    });

    let html = '';
    Object.values(subjects).forEach(sub => {
      const pct = sub.max > 0 ? ((sub.total / sub.max) * 100).toFixed(1) : 0;
      const colorClass = pct >= 75 ? 'success' : pct >= 50 ? '' : pct >= 35 ? 'warning' : 'danger';
      
      html += `
        <div style="margin-bottom:16px;">
          <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.8125rem;">
            <span><strong>${sub.code}</strong> — ${sub.name}</span>
            <span style="font-weight:600;">${sub.total.toFixed(1)}/${sub.max.toFixed(1)} (${pct}%)</span>
          </div>
          <div class="progress ${colorClass}" style="height:6px;">
            <div class="progress-fill" style="width:${pct}%"></div>
          </div>
        </div>
      `;
    });
    container.innerHTML = html;
  }
});
</script>
