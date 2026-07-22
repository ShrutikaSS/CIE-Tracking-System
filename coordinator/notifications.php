<?php
$pageTitle = 'Notifications';
require_once __DIR__ . '/../includes/header.php';
requireRole(['coordinator']);
?>

<div class="page-header">
  <div>
    <h1>Notifications</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Notifications</div>
  </div>
  <div class="actions">
    <button class="btn btn-secondary" onclick="markAllAsRead()">Mark All Read</button>
  </div>
</div>

<div class="card">
  <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
    <h3>🔔 Class Activity Alerts</h3>
    <span class="badge badge-unread" id="unread-total-badge" style="font-size:0.85rem;">3 Unread</span>
  </div>
  <div class="card-body" style="padding: 0;">
    <div id="notifications-container">
      <!-- Mock notifications loaded here -->
    </div>
  </div>
</div>

<!-- Notification Detail Modal -->
<div class="modal-overlay" id="modal-notification-detail">
  <div class="modal" style="max-width: 500px;">
    <div class="modal-header">
      <h3>Notification Details</h3>
      <button class="modal-close" onclick="Modal.close('modal-notification-detail')">✕</button>
    </div>
    <div class="modal-body" style="padding: 20px 25px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <span class="badge badge-unread" id="notif-detail-badge">UNREAD</span>
        <span id="notif-detail-time" style="font-size:0.8rem; color:var(--text-muted);">Today, 10:15 AM</span>
      </div>
      <h4 id="notif-detail-title" style="color:var(--primary); margin-bottom:15px; font-size:1.15rem; font-weight:600;">Subject Title</h4>
      <p id="notif-detail-desc" style="font-size:0.95rem; line-height:1.6; color:var(--text-primary);">Notification body goes here...</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" onclick="Modal.close('modal-notification-detail')">Close</button>
    </div>
  </div>
</div>

<style>
.notification-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 25px;
  border-bottom: 1px solid var(--border-color);
  transition: background-color var(--transition-fast);
  cursor: pointer;
}
.notification-item:hover {
  background-color: var(--bg-hover);
}
.notification-item.unread-item {
  background-color: rgba(15, 76, 129, 0.02);
  border-left: 4px solid var(--primary);
}
.notif-title {
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 4px;
}
.unread-item .notif-title {
  font-weight: 600;
}
.notif-snippet {
  font-size: 0.85rem;
  color: var(--text-secondary);
}
.badge-unread {
  background-color: var(--primary-lighter);
  color: var(--primary);
}
.badge-read {
  background-color: var(--bg-hover);
  color: var(--text-muted);
}
</style>

<script>
// Mock notifications array
let notifications = [
  {
    id: 1,
    title: 'Urgent: Mid-Term marks submission deadline approaching',
    snippet: 'All mid-term evaluation marks for Semester 5 subjects must be uploaded and published...',
    body: 'Dear Coordinators, please note that the portal submission deadline for Continuous Internal Evaluation (CIE) Mid-Term Test marks has been scheduled for 28-Jul-2026. Ensure all faculty members assigned to subjects in your division enter the student marks and verify before the cut-off. Late entries will require HOD approval.',
    status: 'unread',
    time: 'Today, 10:15 AM'
  },
  {
    id: 2,
    title: 'Class attendance reports for June 2026 published',
    snippet: 'The aggregate attendance details for Third Year CSE students have been updated...',
    body: 'The consolidated attendance register for all students of TE-CSE-A has been uploaded. Kindly review students with less than 75% attendance and issue notices to clear defaults before the final evaluations.',
    status: 'unread',
    time: 'Yesterday, 04:30 PM'
  },
  {
    id: 3,
    title: 'Curriculum update: Lab schedule modified for Term II',
    snippet: 'The practical schedules for Web Technology Lab will be interchanged with Network Lab...',
    body: 'Please note the web development lab sessions on Wednesday will now be replaced with Computer Networks practicals. This change is effective starting next week.',
    status: 'read',
    time: '20-Jul-2026, 11:00 AM'
  },
  {
    id: 4,
    title: 'Notification: HOD meeting scheduled for Friday',
    snippet: 'All class coordinators and department faculty members are requested to attend...',
    body: 'An academic audit meeting has been called by the HOD of Artificial Intelligence & Machine Learning on Friday at 2:00 PM in the department conference hall to review CIE progress.',
    status: 'read',
    time: '18-Jul-2026, 09:15 AM'
  },
  {
    id: 5,
    title: 'Quiz 2 Marks Published: Database Management Systems',
    snippet: 'Marks for DB Systems Quiz 2 are now visible to students on their dashboards...',
    body: 'Quiz 2 evaluations for Database Management Systems have been completed by Prof. Anil Mehta. The grades have been locked and published. If students raise queries, coordinates can view individual score cards under Student Progress.',
    status: 'unread',
    time: '17-Jul-2026, 02:20 PM'
  }
];

document.addEventListener('DOMContentLoaded', () => {
  renderNotifications();
});

function renderNotifications() {
  const container = document.getElementById('notifications-container');
  const unreadCount = notifications.filter(n => n.status === 'unread').length;
  
  // Update badge count
  document.getElementById('unread-total-badge').innerText = `${unreadCount} Unread`;
  
  if (notifications.length === 0) {
    container.innerHTML = `<div class="empty-state" style="padding:40px;"><div class="icon" style="font-size:2rem; margin-bottom:10px;">🔔</div><h3>No notifications found</h3></div>`;
    return;
  }

  container.innerHTML = notifications.map(n => `
    <div class="notification-item ${n.status === 'unread' ? 'unread-item' : ''}" onclick="openNotifDetail(${n.id})">
      <div>
        <div class="notif-title">${n.title}</div>
        <div class="notif-snippet">${n.snippet}</div>
      </div>
      <div style="display:flex; align-items:center; gap:12px;">
        <span class="badge ${n.status === 'unread' ? 'badge-unread' : 'badge-read'}">${n.status.toUpperCase()}</span>
        <span style="font-size:0.8rem; color:var(--text-muted); white-space:nowrap;">${n.time}</span>
      </div>
    </div>
  `).join('');
}

function openNotifDetail(id) {
  const notif = notifications.find(n => n.id === id);
  if (!notif) return;

  // Mark as read in UI state
  notif.status = 'read';
  renderNotifications();

  // Populate details
  document.getElementById('notif-detail-badge').innerText = 'READ';
  document.getElementById('notif-detail-badge').className = 'badge badge-read';
  document.getElementById('notif-detail-time').innerText = notif.time;
  document.getElementById('notif-detail-title').innerText = notif.title;
  document.getElementById('notif-detail-desc').innerText = notif.body;

  // Open modal
  Modal.open('modal-notification-detail');
}

function markAllAsRead() {
  notifications.forEach(n => n.status = 'read');
  renderNotifications();
  Toast.success('All notifications marked as read.');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
