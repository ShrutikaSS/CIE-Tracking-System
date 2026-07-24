<?php
$pageTitle = 'Coordinators Submissions';
require_once __DIR__ . '/../includes/header.php';
requireRole(['hod']);
?>

<div class="page-header">
  <div>
    <h1>Coordinators Submissions</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Submissions</div>
  </div>
</div>

<div class="card">
  <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
    <h3>📥 Submitted Documents & Reports</h3>
    <button class="btn btn-secondary btn-sm" onclick="loadSubmissions()">🔄 Refresh</button>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-responsive">
      <table class="table" style="width:100%; border-collapse:collapse; margin:0;">
        <thead>
          <tr style="border-bottom:2px solid var(--border-color); text-align:left; background:var(--bg-hover);">
            <th style="padding:15px 20px;">Submitted By</th>
            <th style="padding:15px 20px;">Document Title</th>
            <th style="padding:15px 20px;">Linked Request / Message</th>
            <th style="padding:15px 20px;">Submitted At</th>
            <th style="padding:15px 20px; text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody id="submissions-list">
          <tr>
            <td colspan="5" style="padding:40px; text-align:center;" class="text-muted">Loading submissions...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Submission Details Modal -->
<div class="modal-overlay" id="modal-submission-detail">
  <div class="modal" style="max-width: 550px;">
    <div class="modal-header">
      <h3>Submission Details</h3>
      <button class="modal-close" onclick="Modal.close('modal-submission-detail')">✕</button>
    </div>
    <div class="modal-body" style="padding: 20px 25px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:8px;">
        <span class="badge badge-success">SUBMITTED</span>
        <span id="sub-detail-time" style="font-size:0.8rem; color:var(--text-muted);">Today</span>
      </div>
      <div style="margin-bottom: 12px; font-size: 0.85rem; color: var(--text-secondary);">
        <strong>Coordinator:</strong> <span id="sub-detail-coordinator">Name</span>
      </div>
      <div style="margin-bottom: 12px; font-size: 0.85rem; color: var(--text-secondary);" id="sub-detail-context-container">
        <strong>In Response To:</strong> <span id="sub-detail-context">None</span>
      </div>
      <h4 id="sub-detail-title" style="color:var(--primary); margin-bottom:15px; font-size:1.2rem; font-weight:600;">Title</h4>
      
      <div style="margin-bottom:20px;">
        <strong>Coordinator Notes:</strong>
        <div id="sub-detail-desc" style="font-size:0.95rem; line-height:1.6; color:var(--text-primary); white-space:pre-wrap; background:var(--bg-hover); padding:12px; border-radius:var(--radius-sm); border:1px solid var(--border-color); margin-top:5px;">
          No notes provided.
        </div>
      </div>

      <div style="background:var(--primary-lighter); padding:15px; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:space-between; gap:15px;">
        <div style="display:flex; align-items:center; gap:10px; min-width:0;">
          <span style="font-size:1.8rem; flex-shrink:0;">📁</span>
          <div style="min-width:0;">
            <div id="sub-detail-filename" style="font-weight:600; font-size:0.9rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--primary);">document.pdf</div>
            <div id="sub-detail-filetype" style="font-size:0.75rem; color:var(--text-muted);">application/pdf</div>
          </div>
        </div>
        <a href="#" id="sub-detail-download-btn" class="btn btn-sm btn-primary" download style="background:var(--primary); border-radius:4px; text-decoration:none; color:white; white-space:nowrap;">📥 Download</a>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-submission-detail')">Close</button>
    </div>
  </div>
</div>

<style>
.sub-row {
  border-bottom: 1px solid var(--border-color);
  transition: background-color var(--transition-fast);
}
.sub-row:hover {
  background-color: var(--bg-hover);
}
table th, table td {
  font-size: 0.9rem;
}
</style>

<script>
let submissionsList = [];

document.addEventListener('DOMContentLoaded', () => {
  loadSubmissions();
});

async function loadSubmissions() {
  const container = document.getElementById('submissions-list');
  try {
    const res = await API.get('/api/submissions.php?action=list');
    if (res && res.success) {
      submissionsList = res.submissions;
      renderSubmissions();
    } else {
      container.innerHTML = `<tr><td colspan="5" style="padding:40px; text-align:center;" class="text-danger">Failed to load submissions.</td></tr>`;
    }
  } catch (err) {
    container.innerHTML = `<tr><td colspan="5" style="padding:40px; text-align:center;" class="text-danger">Error loading submissions.</td></tr>`;
    console.error(err);
  }
}

function renderSubmissions() {
  const container = document.getElementById('submissions-list');
  if (submissionsList.length === 0) {
    container.innerHTML = `
      <tr>
        <td colspan="5" style="padding:60px; text-align:center;" class="text-muted">
          <div style="font-size:2.5rem; margin-bottom:15px;">📥</div>
          <h3>No submissions yet</h3>
          <p class="fs-sm">Documents submitted by class coordinators will show up here.</p>
        </td>
      </tr>`;
    return;
  }

  container.innerHTML = submissionsList.map(s => {
    const date = new Date(s.submitted_at).toLocaleString();
    const context = s.message_subject ? escapeHtml(s.message_subject) : '<span class="text-muted">General Submission</span>';
    
    return `
      <tr class="sub-row">
        <td style="padding:15px 20px;">
          <strong>${escapeHtml(s.coordinator_name)}</strong>
          <div style="font-size:0.75rem; color:var(--text-muted);">${escapeHtml(s.coordinator_email)}</div>
        </td>
        <td style="padding:15px 20px; font-weight:500; color:var(--primary);">${escapeHtml(s.title)}</td>
        <td style="padding:15px 20px; max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${context}</td>
        <td style="padding:15px 20px; color:var(--text-secondary);">${date}</td>
        <td style="padding:15px 20px; text-align:right;">
          <div style="display:flex; justify-content:flex-end; gap:8px;">
            <button class="btn btn-sm btn-secondary" onclick="viewSubmissionDetail(${s.id})">👁️ View</button>
            <a href="${s.file_path}" class="btn btn-sm btn-primary" download="${escapeHtml(s.file_name)}" style="background:var(--success); border-radius:4px; text-decoration:none; color:white; display:inline-flex; align-items:center; justify-content:center;">📥 Download</a>
          </div>
        </td>
      </tr>
    `;
  }).join('');
}

function viewSubmissionDetail(id) {
  const sub = submissionsList.find(s => s.id === id);
  if (!sub) return;

  document.getElementById('sub-detail-time').innerText = new Date(sub.submitted_at).toLocaleString();
  document.getElementById('sub-detail-coordinator').innerText = `${sub.coordinator_name} (${sub.coordinator_email})`;
  
  const contextContainer = document.getElementById('sub-detail-context-container');
  if (sub.message_subject) {
    contextContainer.style.display = 'block';
    document.getElementById('sub-detail-context').innerText = sub.message_subject;
  } else {
    contextContainer.style.display = 'none';
  }

  document.getElementById('sub-detail-title').innerText = sub.title;
  document.getElementById('sub-detail-desc').innerText = sub.description ? sub.description : 'No notes provided by coordinator.';
  
  document.getElementById('sub-detail-filename').innerText = sub.file_name;
  document.getElementById('sub-detail-filetype').innerText = sub.file_type || 'Unknown';
  
  const dlBtn = document.getElementById('sub-detail-download-btn');
  dlBtn.href = sub.file_path;
  dlBtn.download = sub.file_name;

  Modal.open('modal-submission-detail');
}

function escapeHtml(text) {
  if (!text) return '';
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
