<?php
$pageTitle = 'Attendance';
require_once __DIR__ . '/../includes/header.php';
requireRole(['student']);
?>

<div class="page-header">
  <div>
    <h1>My Attendance</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Attendance</div>
  </div>
</div>

<style>
  /* Premium Glassmorphic Cards */
  .attendance-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 30px;
  }
  .attendance-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.45);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    padding: 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
  }
  .attendance-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(13, 58, 113, 0.08);
  }
  .dark-theme .attendance-card {
    background: rgba(30, 41, 59, 0.7);
    border-color: rgba(255, 255, 255, 0.06);
  }

  /* Large Circular Ring */
  .large-ring-container {
    position: relative;
    width: 120px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .large-ring-circle-bg {
    fill: transparent;
    stroke: var(--border-light);
    stroke-width: 7;
  }
  
  .large-ring-circle {
    fill: transparent;
    stroke-width: 7;
    stroke-linecap: round;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
    transition: stroke-dashoffset 0.65s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .large-ring-text {
    position: absolute;
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--text-primary);
  }

  /* Subject Circular Progress Ring */
  .subject-ring-container {
    position: relative;
    width: 84px;
    height: 84px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
  }
  
  .subject-ring-circle-bg {
    fill: transparent;
    stroke: var(--border-light);
    stroke-width: 5.5;
  }
  
  .subject-ring-circle {
    fill: transparent;
    stroke-width: 5.5;
    stroke-linecap: round;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
    transition: stroke-dashoffset 0.65s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .subject-ring-text {
    position: absolute;
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
  }
</style>

<!-- Overall Attendance Overview -->
<div class="attendance-card mb-3" style="flex-direction:row; align-items:center; justify-content:space-between; gap:24px; padding: 28px;">
  <div>
    <h2 style="font-family:'Lora', serif; margin-bottom:6px;">Overall Attendance Record</h2>
    <p class="text-muted" style="font-size:0.9rem; line-height: 1.5; max-width: 500px;" id="attendance-status">Loading overall status...</p>
    <div style="font-size:0.9rem; display:flex; gap:24px; margin-top:16px;">
      <div>Total Attended: <strong id="att-attended" style="color:var(--success);">0</strong> classes</div>
      <div>Total Missed: <strong id="att-missed" style="color:var(--danger);">0</strong> classes</div>
    </div>
  </div>
  <div class="large-ring-container">
    <svg class="large-ring" width="120" height="120">
      <circle class="large-ring-circle-bg" cx="60" cy="60" r="52"/>
      <circle class="large-ring-circle" id="overall-ring-circle" cx="60" cy="60" r="52" stroke="#10b981" stroke-dasharray="326.72" stroke-dashoffset="326.72"/>
    </svg>
    <div class="large-ring-text" id="overall-ring-val">0%</div>
  </div>
</div>

<!-- Subject-wise Grid -->
<h2 style="font-family:'Lora', serif; font-size:1.4rem; margin-top:30px; margin-bottom:16px;">Subject-wise Attendance</h2>
<div class="attendance-grid" id="subjects-attendance-grid">
  <div class="empty-state"><div class="spinner"></div></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  const res = await API.get('/api/dashboard.php?action=stats');
  if (res && res.success && res.attendance) {
    const att = res.attendance;
    
    // Overall Stats
    document.getElementById('att-attended').textContent = att.attended;
    document.getElementById('att-missed').textContent = att.missed;
    document.getElementById('attendance-status').textContent = att.status_message;
    
    // Overall Ring (r=52, Circumference = 326.72)
    const overallCircle = document.getElementById('overall-ring-circle');
    const overallCircumference = 326.72;
    overallCircle.style.strokeDashoffset = overallCircumference - (att.percentage / 100 * overallCircumference);
    document.getElementById('overall-ring-val').textContent = att.percentage + '%';

    // Subjects Grid
    const grid = document.getElementById('subjects-attendance-grid');
    grid.innerHTML = '';
    
    att.subjects.forEach((s, index) => {
      const total = s.attended + s.missed;
      const pct = total > 0 ? parseFloat(((s.attended / total) * 100).toFixed(1)) : 0;
      const isWarning = pct < 75;
      
      const card = document.createElement('div');
      card.className = 'attendance-card';
      
      // Radius = 36, Circumference = 226.19
      const circleCircumference = 226.19;
      const circleOffset = circleCircumference - (pct / 100 * circleCircumference);
      
      card.innerHTML = `
        <div style="margin-bottom:20px; text-align:center;">
          <h3 style="font-size:1.1rem; font-family:'Lora', serif; margin-bottom:4px;">${s.code}</h3>
          <p class="text-muted" style="font-size:0.8rem; height: 36px; overflow:hidden;">${s.name}</p>
        </div>
        
        <div style="margin-bottom:20px;">
          <div class="subject-ring-container">
            <svg class="subject-ring" width="84" height="84">
              <circle class="subject-ring-circle-bg" cx="42" cy="42" r="36"/>
              <circle class="subject-ring-circle" id="subject-ring-${index}" cx="42" cy="42" r="36" 
                stroke="${isWarning ? '#ef4444' : '#10b981'}" 
                stroke-dasharray="${circleCircumference}" 
                stroke-dashoffset="${circleCircumference}"/>
            </svg>
            <div class="subject-ring-text">${pct}%</div>
          </div>
        </div>
        
        <div style="border-top:1px solid var(--border-light); padding-top:14px; text-align:center; font-size:0.85rem;">
          <div><strong style="color:var(--success);">${s.attended}</strong> classes attended</div>
          <div style="margin-top:2px;"><strong style="color:var(--danger);">${s.missed}</strong> classes missed</div>
          <div class="text-muted" style="font-size:0.75rem; margin-top:4px;">Total classes: ${total}</div>
        </div>
      `;
      grid.appendChild(card);
      
      // Trigger ring offset draw with small delay
      setTimeout(() => {
        const ring = document.getElementById(`subject-ring-${index}`);
        if (ring) ring.style.strokeDashoffset = circleOffset;
      }, 50);
    });
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
