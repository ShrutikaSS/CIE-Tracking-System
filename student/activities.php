<?php
$pageTitle = 'My Activities';
require_once __DIR__ . '/../includes/header.php';
requireRole(['student']);
?>

<div class="page-header">
  <div>
    <h1>My Activities</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / My Activities</div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="form-row" style="grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; align-items: end;">
      <div class="form-group mb-0">
        <label>Search Activities</label>
        <input type="text" class="form-control" id="search-input" placeholder="Search by activity name...">
      </div>
      <div class="form-group mb-0">
        <label>Subject</label>
        <select class="form-control" id="subject-filter">
          <option value="">All Subjects</option>
        </select>
      </div>
      <div class="form-group mb-0">
        <label>Activity Type</label>
        <select class="form-control" id="type-filter">
          <option value="">All Types</option>
          <option value="assignment">Assignment</option>
          <option value="quiz">Quiz</option>
          <option value="test">Test</option>
          <option value="seminar">Seminar</option>
          <option value="viva">Viva</option>
          <option value="practical">Practical</option>
          <option value="project_review">Project Review</option>
          <option value="presentation">Presentation</option>
        </select>
      </div>
      <div class="form-group mb-0">
        <label>Status</label>
        <select class="form-control" id="status-filter">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="completed">Completed</option>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-container">
      <table id="activities-table">
        <thead>
          <tr>
            <th data-sortable="true">Activity Name</th>
            <th data-sortable="true">Subject</th>
            <th data-sortable="true">Type</th>
            <th data-sortable="true">Max Marks</th>
            <th data-sortable="true">Activity Date</th>
            <th data-sortable="true">Deadline</th>
            <th data-sortable="true">Status</th>
            <th data-sortable="true">Submission</th>
            <th data-sortable="true">Marks Obtained</th>
            <th data-sortable="false">Actions</th>
          </tr>
        </thead>
        <tbody id="activities-tbody">
          <tr><td colspan="10" class="text-center"><div class="spinner spinner-sm" style="margin:20px auto;"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Activity Detail Modal -->
<div class="modal-overlay" id="modal-activity-details">
  <div class="modal">
    <div class="modal-header">
      <h3 id="detail-title">Activity Details</h3>
      <button class="modal-close" onclick="Modal.close('modal-activity-details')">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
        <div>
          <label style="font-weight:600; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Subject</label>
          <div id="detail-subject" style="font-weight:500; margin-top:2px;">—</div>
        </div>
        <div>
          <label style="font-weight:600; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Type</label>
          <div id="detail-type" style="margin-top:2px;">—</div>
        </div>
        <div>
          <label style="font-weight:600; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Max Marks</label>
          <div id="detail-max-marks" style="font-weight:700; margin-top:2px;">—</div>
        </div>
        <div>
          <label style="font-weight:600; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Deadline</label>
          <div id="detail-deadline" style="margin-top:2px;">—</div>
        </div>
      </div>
      <div style="margin-bottom:20px;">
        <label style="font-weight:600; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Description</label>
        <p id="detail-description" style="margin-top:4px; font-size:0.875rem; white-space:pre-wrap; line-height:1.5;">—</p>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-activity-details')">Close</button>
    </div>
  </div>
</div>

<!-- Submit Activity Modal -->
<div class="modal-overlay" id="modal-submit-activity">
  <div class="modal" style="max-width:540px; border-radius:12px; overflow:hidden;">
    <div class="modal-header" style="padding:18px 24px; border-bottom:1px solid var(--border-color); display:flex; align-items:center; justify-content:space-between;">
      <h3 id="submit-modal-title" style="margin:0; font-size:1.1rem; font-weight:700;">Submit Activity</h3>
      <button class="modal-close" onclick="Modal.close('modal-submit-activity')" style="background:none; border:none; cursor:pointer; color:var(--text-muted); font-size:1.2rem; padding:4px; border-radius:4px; line-height:1;">✕</button>
    </div>
    <form id="submit-activity-form" onsubmit="handleActivitySubmit(event)">
      <input type="hidden" id="submit-activity-id" name="activity_id">
      <div class="modal-body" style="padding:20px 24px; max-height:65vh; overflow-y:auto;">

        <!-- Activity Info Card -->
        <div style="background:var(--bg-light); border:1px solid var(--border-color); border-radius:8px; padding:14px 16px; margin-bottom:18px;">
          <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700; letter-spacing:0.05em; margin-bottom:4px;">ACTIVITY</div>
          <div id="submit-info-name" style="font-weight:700; font-size:1.05rem; color:var(--text-primary); margin-bottom:8px;">—</div>
          <div style="display:flex; gap:20px; font-size:0.83rem; color:var(--text-secondary);">
            <div><span style="font-weight:600; color:var(--text-muted);">Type:</span> <span id="submit-info-type" style="font-weight:600; color:var(--text-primary);">—</span></div>
            <div><span style="font-weight:600; color:var(--text-muted);">Max Marks:</span> <span id="submit-info-max-marks" style="font-weight:600; color:var(--primary);">—</span></div>
          </div>
        </div>

        <!-- Already submitted view -->
        <div id="already-submitted-view" style="display:none; margin-bottom:18px; border-left:3px solid var(--success); padding:10px 14px; background:rgba(46,204,113,0.06); border-radius:0 6px 6px 0;">
          <div style="font-weight:700; color:var(--success); font-size:0.85rem; margin-bottom:6px;">✓ Previous Submission</div>
          <div style="font-size:0.83rem; color:var(--text-secondary); margin-bottom:4px;">
            <span style="font-weight:600;">Submitted on:</span> <span id="prev-submitted-at">—</span>
          </div>
          <div id="prev-file-container" style="display:none; font-size:0.83rem; color:var(--text-secondary); margin-bottom:4px;">
            <span style="font-weight:600;">File:</span>
            <a id="prev-file-link" href="#" target="_blank" style="color:var(--primary); font-weight:600; text-decoration:none; margin-left:4px;">📄 View Submitted File</a>
          </div>
          <div id="prev-text-container" style="display:none; font-size:0.83rem; margin-top:8px;">
            <div style="font-weight:600; color:var(--text-secondary); margin-bottom:4px;">Your notes:</div>
            <p id="prev-text-content" style="background:var(--bg-input,#f5f5f5); padding:8px 10px; border-radius:5px; margin:0; white-space:pre-wrap; border:1px solid var(--border-color); font-size:0.83rem; line-height:1.5;"></p>
          </div>
          <button type="button" class="btn btn-sm btn-outline" id="btn-show-submit-form" onclick="toggleSubmissionForm(true)" style="margin-top:12px; font-size:0.8rem;">
            ✏️ Update / Resubmit
          </button>
        </div>

        <!-- Form Fields -->
        <div id="submission-form-fields">
          <!-- Comments / Text -->
          <div class="form-group" style="margin-bottom:16px;">
            <label id="text-submission-label" style="display:block; font-weight:600; font-size:0.85rem; color:var(--text-primary); margin-bottom:6px;">Comments / Notes <span style="font-weight:400; color:var(--text-muted);">(Optional)</span></label>
            <textarea
              class="form-control"
              id="submission-text-input"
              name="submission_text"
              rows="4"
              placeholder="Add any notes or comments for the faculty..."
              style="resize:vertical; font-size:0.875rem; line-height:1.5; border-radius:7px; padding:10px 12px;"
            ></textarea>
          </div>

          <!-- File Upload -->
          <div class="form-group" style="margin-bottom:4px;">
            <label id="file-submission-label" style="display:block; font-weight:600; font-size:0.85rem; color:var(--text-primary); margin-bottom:8px;">Upload Submission File <span id="file-required-badge" style="font-size:0.75rem; color:var(--danger); font-weight:500;">(Required)</span></label>

            <!-- Custom file upload zone -->
            <label for="submission-file-input" id="file-drop-zone" style="
              display:flex; flex-direction:column; align-items:center; justify-content:center;
              border:2px dashed var(--border-color); border-radius:8px; padding:20px 16px;
              cursor:pointer; transition:border-color 0.2s, background 0.2s;
              background:var(--bg-light,#f9f9f9); text-align:center; gap:6px;
            "
              onmouseover="this.style.borderColor='var(--primary)'; this.style.background='rgba(26,115,232,0.04)'"
              onmouseout="this.style.borderColor='var(--border-color)'; this.style.background='var(--bg-light,#f9f9f9)'"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
              <div id="file-drop-label" style="font-size:0.85rem; color:var(--text-secondary); font-weight:500;">
                <span style="color:var(--primary); font-weight:600;">Click to upload</span> or drag & drop
              </div>
              <div style="font-size:0.75rem; color:var(--text-muted);">PDF, JPG, JPEG, PNG — max 5MB</div>
            </label>
            <input
              type="file"
              id="submission-file-input"
              name="submission_file"
              accept=".pdf,.jpg,.jpeg,.png"
              style="display:none;"
              onchange="updateFileLabel(this)"
            >
          </div>
        </div>

      </div>
      <div class="modal-footer" style="padding:14px 24px; border-top:1px solid var(--border-color); display:flex; align-items:center; justify-content:flex-end; gap:10px;">
        <button type="button" class="btn btn-secondary" onclick="Modal.close('modal-submit-activity')" style="min-width:90px;">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-action" style="min-width:110px; display:flex; align-items:center; gap:6px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
          Submit
        </button>
      </div>
    </form>
  </div>
</div>


<script>
// Update file drop zone label when a file is selected
window.updateFileLabel = (input) => {
  const zone = document.getElementById('file-drop-zone');
  const label = document.getElementById('file-drop-label');
  if (input.files && input.files[0]) {
    const f = input.files[0];
    const sizeMB = (f.size / (1024 * 1024)).toFixed(2);
    label.innerHTML = `<span style="color:var(--success); font-weight:700;">✓ ${f.name}</span> <span style="color:var(--text-muted); font-weight:400;">(${sizeMB} MB)</span>`;
    zone.style.borderColor = 'var(--success)';
    zone.style.background = 'rgba(46,204,113,0.05)';
  } else {
    label.innerHTML = `<span style="color:var(--primary); font-weight:600;">Click to upload</span> or drag & drop`;
    zone.style.borderColor = 'var(--border-color)';
    zone.style.background = 'var(--bg-light,#f9f9f9)';
  }
};

document.addEventListener('DOMContentLoaded', async () => {
  let allActivities = [];

  // Drag-and-drop support for file upload zone
  const dropZone = document.getElementById('file-drop-zone');
  const fileInput = document.getElementById('submission-file-input');
  if (dropZone && fileInput) {
    dropZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      dropZone.style.borderColor = 'var(--primary)';
      dropZone.style.background = 'rgba(26,115,232,0.07)';
    });
    dropZone.addEventListener('dragleave', () => {
      dropZone.style.borderColor = 'var(--border-color)';
      dropZone.style.background = 'var(--bg-light,#f9f9f9)';
    });
    dropZone.addEventListener('drop', (e) => {
      e.preventDefault();
      if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        updateFileLabel(fileInput);
      }
    });
  }


  const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  };

  // Load filter options
  const subRes = await API.get('/api/subjects.php');
  if (subRes && subRes.success) {
    const select = document.getElementById('subject-filter');
    subRes.subjects.forEach(s => {
      const opt = document.createElement('option');
      opt.value = s.id;
      opt.textContent = `${s.code} - ${s.name}`;
      select.appendChild(opt);
    });
  }

  // Load activities
  const loadActivities = async () => {
    const subjectId = document.getElementById('subject-filter').value;
    const type = document.getElementById('type-filter').value;
    const status = document.getElementById('status-filter').value;
    const search = document.getElementById('search-input').value;

    let url = `/api/activities.php?action=list`;
    if (subjectId) url += `&subject_id=${subjectId}`;
    if (type) url += `&type=${type}`;
    if (status) url += `&status=${status}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    const res = await API.get(url);
    const tbody = document.getElementById('activities-tbody');
    
    if (res && res.success) {
      allActivities = res.activities;
      if (allActivities.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center" style="padding:40px 0;"><div class="icon" style="margin-bottom:8px; display:flex; justify-content:center;"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted);"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg></div><h3>No activities found</h3><p>No activities match your filters.</p></td></tr>';
        return;
      }

      tbody.innerHTML = allActivities.map(a => {
        let marksDisplay = '<span class="text-muted" style="font-size:0.85rem;">—</span>';
        if (a.is_marks_published == 1 && a.marks_obtained !== null) {
          const score = parseFloat(a.marks_obtained).toFixed(1);
          const max = parseFloat(a.max_marks).toFixed(1);
          const pct = max > 0 ? ((score / max) * 100).toFixed(1) : 0;
          const badgeClass = pct >= 75 ? 'badge-success' : pct >= 40 ? 'badge-primary' : 'badge-danger';
          marksDisplay = `<div style="display:flex; flex-direction:column; gap:2px;">
            <span class="badge ${badgeClass}" style="font-size:0.85rem; font-weight:700;">${score} / ${max}</span>
            <small style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">${pct}%</small>
          </div>`;
        } else if (a.submission_id) {
          marksDisplay = `<span class="badge badge-warning" style="font-size:0.75rem; padding:4px 8px;">⏳ Under Review</span>`;
        }

        return `
        <tr>
          <td><strong>${a.name}</strong></td>
          <td>${a.subject_code} - ${a.subject_name}</td>
          <td><span class="badge badge-primary">${a.type}</span></td>
          <td><strong>${parseFloat(a.max_marks).toFixed(1)}</strong></td>
          <td>${formatDate(a.activity_date)}</td>
          <td>${formatDate(a.deadline)}</td>
          <td><span class="badge ${a.status === 'completed' ? 'badge-success' : 'badge-primary'}">${a.status}</span></td>
          <td>
            ${a.submission_id 
              ? `<span class="badge badge-success" style="display:inline-flex; align-items:center; gap:4px;">
                   <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                   Submitted
                 </span>` 
              : `<span class="badge badge-secondary">Not Submitted</span>`}
          </td>
          <td>${marksDisplay}</td>
          <td>
            <div style="display:flex; gap:6px; flex-wrap:wrap;">
              <button class="btn btn-sm btn-secondary" style="display:inline-flex; align-items:center; gap:6px;" onclick="viewDetails(${a.id})">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                View Details
              </button>
              ${a.submission_id 
                ? `<button class="btn btn-sm btn-outline" style="display:inline-flex; align-items:center; gap:6px;" onclick="openSubmitModal(${a.id}, true)">
                     <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                     View Submission
                   </button>` 
                : `<button class="btn btn-sm btn-primary" style="display:inline-flex; align-items:center; gap:6px;" onclick="openSubmitModal(${a.id}, false)">
                     <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
                     Submit
                   </button>`
              }
            </div>
          </td>
        </tr>`;
      }).join('');
    } else {
      tbody.innerHTML = '<tr><td colspan="10" class="text-center text-danger" style="padding:20px;">Failed to load activities.</td></tr>';
    }
  };

  // Event Listeners for Filters
  document.getElementById('subject-filter').addEventListener('change', loadActivities);
  document.getElementById('type-filter').addEventListener('change', loadActivities);
  document.getElementById('status-filter').addEventListener('change', loadActivities);
  document.getElementById('search-input').addEventListener('input', debounce(loadActivities, 300));

  // Initial Load
  await loadActivities();

  // Detail Modal view function
  window.viewDetails = (id) => {
    const a = allActivities.find(item => item.id === id);
    if (!a) return;

    document.getElementById('detail-title').textContent = a.name;
    document.getElementById('detail-subject').textContent = `${a.subject_code} - ${a.subject_name}`;
    document.getElementById('detail-type').innerHTML = `<span class="badge badge-primary">${a.type}</span>`;
    document.getElementById('detail-max-marks').textContent = parseFloat(a.max_marks).toFixed(1);
    document.getElementById('detail-deadline').textContent = formatDate(a.deadline);
    document.getElementById('detail-description').textContent = a.description || 'No description provided.';

    Modal.open('modal-activity-details');
  };

  // Open Submit Activity Modal
  window.openSubmitModal = (id, viewOnly) => {
    const a = allActivities.find(item => item.id === id);
    if (!a) return;

    // Reset form
    document.getElementById('submit-activity-form').reset();
    // Reset custom file drop zone
    const dz = document.getElementById('file-drop-zone');
    const dzLabel = document.getElementById('file-drop-label');
    if (dz && dzLabel) {
      dzLabel.innerHTML = `<span style="color:var(--primary); font-weight:600;">Click to upload</span> or drag & drop`;
      dz.style.borderColor = 'var(--border-color)';
      dz.style.background = 'var(--bg-light,#f9f9f9)';
    }
    document.getElementById('submit-activity-id').value = a.id;
    document.getElementById('submit-info-name').textContent = a.name;
    document.getElementById('submit-info-type').textContent = a.type.toUpperCase();
    document.getElementById('submit-info-max-marks').textContent = parseFloat(a.max_marks).toFixed(1);

    const prevView = document.getElementById('already-submitted-view');
    const formFields = document.getElementById('submission-form-fields');
    const submitBtn = document.getElementById('btn-submit-action');

    // Dynamic field labels and requirements
    const fileLabel = document.getElementById('file-submission-label');
    const fileInput = document.getElementById('submission-file-input');
    const textLabel = document.getElementById('text-submission-label');
    const textInput = document.getElementById('submission-text-input');

    if (a.type === 'quiz') {
      textLabel.textContent = 'Quiz Answers / Response (Required)';
      textInput.required = true;
      textInput.placeholder = 'Type your answers/response to the quiz questions here...';
      
      fileLabel.textContent = 'Supporting File (Optional - PDF/Image)';
      fileInput.required = false;
    } else {
      textLabel.textContent = 'Comments / Notes (Optional)';
      textInput.required = false;
      textInput.placeholder = 'Add any additional notes for the faculty...';
      
      fileLabel.textContent = 'Upload Submission File (Required - PDF/Image)';
      fileInput.required = !a.submission_id; 
    }

    if (a.submission_id) {
      prevView.style.display = 'block';
      document.getElementById('prev-submitted-at').textContent = formatDate(a.submitted_at);
      
      if (a.file_path) {
        document.getElementById('prev-file-container').style.display = 'block';
        document.getElementById('prev-file-link').href = a.file_path;
        document.getElementById('prev-file-link').textContent = `View Submitted File (${a.file_path.split('/').pop()})`;
      } else {
        document.getElementById('prev-file-container').style.display = 'none';
      }

      if (a.submission_text) {
        document.getElementById('prev-text-container').style.display = 'block';
        document.getElementById('prev-text-content').textContent = a.submission_text;
      } else {
        document.getElementById('prev-text-container').style.display = 'none';
      }

      if (viewOnly) {
        formFields.style.display = 'none';
        submitBtn.style.display = 'none';
        document.getElementById('btn-show-submit-form').style.display = 'inline-block';
      } else {
        formFields.style.display = 'block';
        submitBtn.style.display = 'inline-block';
        document.getElementById('btn-show-submit-form').style.display = 'none';
      }
    } else {
      prevView.style.display = 'none';
      formFields.style.display = 'block';
      submitBtn.style.display = 'inline-block';
    }

    Modal.open('modal-submit-activity');
  };

  // Toggle Submission Form in Modal
  window.toggleSubmissionForm = (show) => {
    const formFields = document.getElementById('submission-form-fields');
    const submitBtn = document.getElementById('btn-submit-action');
    const btnShowSubmitForm = document.getElementById('btn-show-submit-form');
    
    if (show) {
      formFields.style.display = 'block';
      submitBtn.style.display = 'inline-block';
      btnShowSubmitForm.style.display = 'none';
    } else {
      formFields.style.display = 'none';
      submitBtn.style.display = 'none';
      btnShowSubmitForm.style.display = 'inline-block';
    }
  };

  // Submit Activity Form Action
  window.handleActivitySubmit = async (event) => {
    event.preventDefault();

    const form = event.target;
    const fileInput = document.getElementById('submission-file-input');
    const activityId = document.getElementById('submit-activity-id').value;

    if (fileInput.files.length > 0) {
      const file = fileInput.files[0];
      const maxSize = 5 * 1024 * 1024; // 5MB
      const allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
      const ext = file.name.split('.').pop().toLowerCase();

      if (!allowedExts.includes(ext)) {
        Toast.error('Invalid file type. Only PDF, JPG, JPEG, and PNG are allowed.');
        return;
      }

      if (file.size > maxSize) {
        Toast.error('File size exceeds 5MB limit.');
        return;
      }
    }

    const formData = new FormData(form);

    try {
      showLoading();
      const response = await fetch('/api/submit_activity.php', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      
      const result = await response.json();
      hideLoading();

      if (result && result.success) {
        Toast.success(result.message);
        Modal.close('modal-submit-activity');
        await loadActivities();
      } else {
        Toast.error(result ? result.message : 'Submission failed.');
      }
    } catch (error) {
      hideLoading();
      console.error('Submission error:', error);
      Toast.error('An error occurred during submission.');
    }
  };
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
