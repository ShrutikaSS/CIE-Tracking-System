<?php
$pageTitle = 'HOD Messages';
require_once __DIR__ . '/../includes/header.php';
requireRole(['coordinator']);
?>

<div class="page-header">
  <div>
    <h1>HOD Messages</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / HOD Messages</div>
  </div>
  <div class="actions" style="display:flex; gap:10px;">
    <button class="btn btn-primary" onclick="openGeneralSubmitDoc()">📤 Submit Document</button>
    <button class="btn btn-secondary" onclick="markAllMessagesAsRead()">Mark All Read</button>
  </div>
</div>

<div class="card">
  <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
    <h3>📩 Messages from HOD</h3>
    <div style="display:flex; gap:10px; align-items:center;">
      <select id="message-filter" class="form-control" style="width:auto; padding:4px 8px; font-size:0.85rem; height:auto;" onchange="loadMessages()">
        <option value="all">All Messages</option>
        <option value="unread">Unread Only</option>
        <option value="read">Read Only</option>
      </select>
      <span class="badge badge-unread" id="unread-messages-badge" style="font-size:0.85rem;">0 Unread</span>
    </div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div id="messages-container">
      <div style="padding: 40px; text-align: center;" class="text-muted">Loading messages...</div>
    </div>
  </div>
</div>

<!-- Message Detail Modal -->
<div class="modal-overlay" id="modal-message-detail">
  <div class="modal" style="max-width: 550px;">
    <div class="modal-header">
      <h3>Message Details</h3>
      <button class="modal-close" onclick="Modal.close('modal-message-detail')">✕</button>
    </div>
    <div class="modal-body" style="padding: 20px 25px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:8px;">
        <div>
          <span class="badge" id="msg-detail-priority">NORMAL</span>
          <span class="badge" id="msg-detail-status">UNREAD</span>
        </div>
        <span id="msg-detail-time" style="font-size:0.8rem; color:var(--text-muted);">Today</span>
      </div>
      <div style="margin-bottom: 12px; font-size: 0.85rem; color: var(--text-secondary);">
        <strong>Sender:</strong> <span id="msg-detail-sender">HOD</span>
      </div>
      <h4 id="msg-detail-subject" style="color:var(--primary); margin-bottom:15px; font-size:1.2rem; font-weight:600;">Subject Title</h4>
      <div id="msg-detail-body" style="font-size:0.95rem; line-height:1.6; color:var(--text-primary); white-space:pre-wrap; background:var(--bg-hover); padding:15px; border-radius:var(--radius-sm); border:1px solid var(--border-color);">
        Message body goes here...
      </div>
    </div>
    <div class="modal-footer" style="display:flex; justify-content:space-between; align-items:center;">
      <button class="btn btn-primary" onclick="openSubmitDocFromMsg()" style="background:var(--success);">📤 Submit Document</button>
      <button class="btn btn-secondary" onclick="Modal.close('modal-message-detail')">Close</button>
    </div>
  </div>
</div>

<!-- Submit Document Modal -->
<div class="modal-overlay" id="modal-submit-document">
  <div class="modal" style="max-width: 500px;">
    <div class="modal-header">
      <h3>Submit Document to HOD</h3>
      <button class="modal-close" onclick="Modal.close('modal-submit-document')">✕</button>
    </div>
    <div class="modal-body" style="padding: 20px 25px;">
      <form id="form-submit-document" onsubmit="event.preventDefault(); handleDocumentUpload();" enctype="multipart/form-data">
        <input type="hidden" id="submit-msg-id" value="">
        <div class="form-group mb-3" id="context-group" style="display:none;">
          <label class="form-label" style="font-weight:600;">Message Context</label>
          <input type="text" class="form-control" id="submit-msg-context" readonly style="background:var(--bg-hover);">
        </div>
        <div class="form-group mb-3">
          <label class="form-label" style="font-weight:600;">Document Title</label>
          <input type="text" class="form-control" id="submit-doc-title" placeholder="e.g., Attendance report TE-CSE-A June" required>
        </div>
        <div class="form-group mb-3">
          <label class="form-label" style="font-weight:600;">Note / Description</label>
          <textarea class="form-control" id="submit-doc-desc" rows="3" placeholder="Optional notes for HOD..."></textarea>
        </div>
        <div class="form-group mb-3">
          <label class="form-label" style="font-weight:600;">Choose File (Max 5MB)</label>
          <input type="file" class="form-control" id="submit-doc-file" required accept=".pdf,.png,.jpg,.jpeg,.webp,.doc,.docx,.xls,.xlsx">
          <small class="text-muted">Allowed formats: PDF, Images, Word, Excel</small>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-submit-document')">Cancel</button>
      <button class="btn btn-primary" onclick="handleDocumentUpload()">Submit Upload</button>
    </div>
  </div>
</div>

<style>
.message-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 25px;
  border-bottom: 1px solid var(--border-color);
  transition: background-color var(--transition-fast);
  cursor: pointer;
}
.message-item:hover {
  background-color: var(--bg-hover);
}
.message-item.unread-item {
  background-color: rgba(15, 76, 129, 0.02);
  border-left: 4px solid var(--primary);
}
.msg-title-container {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 4px;
}
.msg-subject {
  font-weight: 500;
  color: var(--text-primary);
}
.unread-item .msg-subject {
  font-weight: 600;
}
.msg-snippet {
  font-size: 0.85rem;
  color: var(--text-secondary);
  max-width: 500px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.badge-priority-normal {
  background-color: var(--bg-hover);
  color: var(--text-secondary);
}
.badge-priority-important {
  background-color: var(--warning-light);
  color: var(--warning);
}
.badge-priority-urgent {
  background-color: var(--danger-light);
  color: var(--danger);
  animation: pulse-badge 2s infinite;
}
</style>

<script>
let messagesList = [];
let activeMessage = null;

document.addEventListener('DOMContentLoaded', () => {
  loadMessages();
});

async function loadMessages() {
  const filter = document.getElementById('message-filter').value;
  const container = document.getElementById('messages-container');
  
  try {
    const res = await API.get(`/api/messages.php?action=list&filter=${filter}`);
    if (res && res.success) {
      messagesList = res.messages;
      renderMessages();
      updateUnreadBadge();
    } else {
      container.innerHTML = `<div style="padding:40px; text-align:center;" class="text-danger">Failed to load messages.</div>`;
    }
  } catch (err) {
    container.innerHTML = `<div style="padding:40px; text-align:center;" class="text-danger">Error fetching messages.</div>`;
  }
}

function renderMessages() {
  const container = document.getElementById('messages-container');
  
  if (messagesList.length === 0) {
    container.innerHTML = `
      <div style="padding:60px; text-align:center;" class="text-muted">
        <div style="font-size:2.5rem; margin-bottom:15px;">📥</div>
        <h3>No messages found</h3>
        <p class="fs-sm">When your HOD sends you any communications or requests, they will show up here.</p>
      </div>`;
    return;
  }

  container.innerHTML = messagesList.map(m => {
    const isUnread = parseInt(m.is_read) === 0;
    const timeAgoStr = formatDateTimeAgo(m.created_at);
    
    let priorityBadge = '';
    if (m.priority === 'urgent') {
      priorityBadge = `<span class="badge badge-priority-urgent">URGENT</span>`;
    } else if (m.priority === 'important') {
      priorityBadge = `<span class="badge badge-priority-important">IMPORTANT</span>`;
    }
    
    return `
      <div class="message-item ${isUnread ? 'unread-item' : ''}" onclick="openMessageDetail(${m.id})">
        <div style="flex: 1; min-width: 0;">
          <div class="msg-title-container">
            ${priorityBadge}
            <span class="msg-subject">${escapeHtml(m.subject)}</span>
          </div>
          <div class="msg-snippet">${escapeHtml(m.message)}</div>
        </div>
        <div style="display:flex; align-items:center; gap:12px; margin-left: 15px; flex-shrink: 0;">
          <span class="badge ${isUnread ? 'badge-unread' : 'badge-read'}">${isUnread ? 'UNREAD' : 'READ'}</span>
          <span style="font-size:0.8rem; color:var(--text-muted); white-space:nowrap;">${timeAgoStr}</span>
        </div>
      </div>
    `;
  }).join('');
}

async function openMessageDetail(id) {
  const msg = messagesList.find(m => m.id === id);
  if (!msg) return;

  activeMessage = msg; // Track active message

  // Populate details
  const priorityEl = document.getElementById('msg-detail-priority');
  priorityEl.innerText = msg.priority.toUpperCase();
  priorityEl.className = 'badge badge-priority-' + msg.priority;

  const isUnread = parseInt(msg.is_read) === 0;
  const statusEl = document.getElementById('msg-detail-status');
  statusEl.innerText = isUnread ? 'UNREAD' : 'READ';
  statusEl.className = `badge ${isUnread ? 'badge-unread' : 'badge-read'}`;

  document.getElementById('msg-detail-time').innerText = new Date(msg.created_at).toLocaleString();
  document.getElementById('msg-detail-sender').innerText = msg.sender_name || 'HOD';
  document.getElementById('msg-detail-subject').innerText = msg.subject;
  document.getElementById('msg-detail-body').innerText = msg.message;

  // Open modal
  Modal.open('modal-message-detail');

  // Mark as read if unread
  if (isUnread) {
    try {
      const res = await API.post('/api/messages.php?action=mark_read', { message_id: msg.id });
      if (res && res.success) {
        msg.is_read = 1;
        renderMessages();
        updateUnreadBadge();
        // Update top header count if navbar has it
        if (typeof checkHODMessages === 'function') {
          checkHODMessages();
        }
      }
    } catch (e) {
      console.error(e);
    }
  }
}

function openSubmitDocFromMsg() {
  if (!activeMessage) return;
  
  // Close details modal
  Modal.close('modal-message-detail');
  
  // Reset form
  document.getElementById('form-submit-document').reset();
  
  // Set context
  document.getElementById('submit-msg-id').value = activeMessage.id;
  document.getElementById('submit-msg-context').value = `Subject: ${activeMessage.subject}`;
  document.getElementById('context-group').style.display = 'block';
  
  // Pre-populate document title based on subject
  document.getElementById('submit-doc-title').value = `Reply to: ${activeMessage.subject}`;
  
  // Open submission modal
  Modal.open('modal-submit-document');
}

function openGeneralSubmitDoc() {
  activeMessage = null;
  
  // Reset form
  document.getElementById('form-submit-document').reset();
  
  // Hide context
  document.getElementById('submit-msg-id').value = '';
  document.getElementById('context-group').style.display = 'none';
  document.getElementById('submit-doc-title').value = '';
  
  // Open submission modal
  Modal.open('modal-submit-document');
}

async function handleDocumentUpload() {
  const fileInput = document.getElementById('submit-doc-file');
  const title = document.getElementById('submit-doc-title').value.trim();
  const desc = document.getElementById('submit-doc-desc').value.trim();
  const msgId = document.getElementById('submit-msg-id').value;

  if (!title) {
    return Toast.error('Please enter a document title.');
  }

  if (fileInput.files.length === 0) {
    return Toast.error('Please choose a file to upload.');
  }

  const file = fileInput.files[0];
  if (file.size > 5 * 1024 * 1024) {
    return Toast.error('File size exceeds 5MB limit.');
  }

  const formData = new FormData();
  formData.append('action', 'submit');
  formData.append('title', title);
  formData.append('description', desc);
  formData.append('message_id', msgId);
  formData.append('document', file);

  showLoading();
  try {
    const response = await fetch('/api/submissions.php', {
      method: 'POST',
      body: formData
    });

    const res = await response.json();
    if (res && res.success) {
      Toast.success(res.message || 'Document uploaded successfully!');
      Modal.close('modal-submit-document');
      document.getElementById('form-submit-document').reset();
    } else {
      Toast.error(res?.message || 'Failed to upload document.');
    }
  } catch (err) {
    Toast.error('An error occurred during submission.');
    console.error(err);
  } finally {
    hideLoading();
  }
}

async function markAllMessagesAsRead() {
  try {
    const res = await API.post('/api/messages.php?action=mark_all_read');
    if (res && res.success) {
      messagesList.forEach(m => m.is_read = 1);
      renderMessages();
      updateUnreadBadge();
      Toast.success('All messages marked as read.');
      if (typeof checkHODMessages === 'function') {
        checkHODMessages();
      }
    }
  } catch (e) {
    Toast.error('Failed to mark all as read.');
  }
}

function updateUnreadBadge() {
  const unreadCount = messagesList.filter(m => parseInt(m.is_read) === 0).length;
  const badge = document.getElementById('unread-messages-badge');
  if (badge) {
    badge.innerText = `${unreadCount} Unread`;
    badge.style.display = unreadCount > 0 ? '' : 'none';
  }
}

function formatDateTimeAgo(dateStr) {
  const date = new Date(dateStr);
  const now = new Date();
  const diffMs = now - date;
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMins / 60);
  const diffDays = Math.floor(diffHours / 24);

  if (diffMins < 1) return 'Just now';
  if (diffMins < 60) return `${diffMins}m ago`;
  if (diffHours < 24) return `${diffHours}h ago`;
  if (diffDays === 1) return 'Yesterday';
  return date.toLocaleDateString();
}

function escapeHtml(text) {
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
