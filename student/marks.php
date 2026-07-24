<?php
$pageTitle = 'My Marks';
require_once __DIR__ . '/../includes/header.php';
requireRole(['student']);
?>

<div class="page-header">
  <div>
    <h1>My Marks</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / My Marks</div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h3 style="display:flex; align-items:center; gap:8px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary);"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
      Subject-wise Marks Breakdown
    </h3>
  </div>
  <div class="card-body" id="marks-container">
    <div class="empty-state"><div class="spinner"></div></div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  const res = await API.get('/api/marks.php?action=by_student');
  if (!res || !res.success) return;
  
  const container = document.getElementById('marks-container');
  
  if (res.marks.length === 0) {
    container.innerHTML = '<div class="empty-state"><div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted);"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg></div><h3>No marks published yet</h3><p>Your marks will appear here once published by your instructors.</p></div>';
    return;
  }
  
  // Group by subject
  const subjects = {};
  res.marks.forEach(m => {
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
    const cieFinal = ((sub.total * 20) / 60).toFixed(2);
    
    html += `
      <div class="card mb-3" style="box-shadow:none;border:1px solid var(--border-color)">
        <div class="card-header">
          <div>
            <h3 style="font-size:1rem; font-family:\'Inter\', sans-serif;">${sub.code} — ${sub.name}</h3>
          </div>
          <div>
            <span class="badge badge-info" style="font-size:0.85rem; padding:6px 12px; margin-right:8px; font-weight:700;">
              Final CIE: ${cieFinal} / 20
            </span>
            <span class="badge ${pct >= 75 ? 'badge-success' : pct >= 50 ? 'badge-primary' : pct >= 35 ? 'badge-warning' : 'badge-danger'}" style="font-size:0.8125rem;padding:5px 12px">
              ${pct}%
            </span>
          </div>
        </div>
        <div class="card-body">
          <div class="progress-label">
            <span class="label">Overall: ${sub.total.toFixed(1)} / ${sub.max.toFixed(1)}</span>
            <span class="value">${pct}%</span>
          </div>
          <div class="progress ${colorClass}" style="margin-bottom:16px">
            <div class="progress-fill" style="width:${pct}%"></div>
          </div>
          <div class="table-container">
            <table>
              <thead><tr><th>Activity</th><th>Type</th><th>Marks Obtained</th><th>Max Marks</th><th>Percentage</th></tr></thead>
              <tbody>
                ${sub.items.map(m => {
                  const apct = ((parseFloat(m.marks_obtained) / parseFloat(m.max_marks)) * 100).toFixed(1);
                  return `<tr>
                    <td><strong>${m.activity_name}</strong></td>
                    <td><span class="badge badge-primary">${m.activity_type}</span></td>
                    <td><strong>${parseFloat(m.marks_obtained).toFixed(1)}</strong></td>
                    <td>${parseFloat(m.max_marks).toFixed(1)}</td>
                    <td>
                      <div class="d-flex align-center gap-1">
                        <div class="progress" style="width:80px;height:6px">
                          <div class="progress-fill" style="width:${apct}%"></div>
                        </div>
                        <span class="fs-sm fw-600">${apct}%</span>
                      </div>
                    </td>
                  </tr>`;
                }).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
  });
  
  container.innerHTML = html;
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
