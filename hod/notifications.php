<?php
/**
 * Notifications Center Page for HOD
 */
$pageTitle = 'Notifications';
require_once __DIR__ . '/../includes/header.php';
requireRole(['hod']);

// Fetch notifications
$notifications = dbFetchAll("
    SELECT * FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC
", 'i', [$user['id']]);
?>

<div class="page-header">
  <div>
    <h1>Notifications Center</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Notifications</div>
  </div>
  <div class="actions">
    <button class="btn btn-primary" onclick="markAllNotificationsRead()">✓ Mark All Read</button>
  </div>
</div>

<div class="card">
  <div class="card-header" style="flex-wrap:wrap; gap:12px; align-items:center;">
    <h3>All Notifications</h3>
    <div class="filter-bar" style="margin:0;">
      <select class="form-control" id="filter-status" onchange="filterNotifications()">
        <option value="all">All Notifications</option>
        <option value="unread">Unread Only</option>
        <option value="read">Read Only</option>
      </select>
    </div>
  </div>
  
  <div class="card-body p-0">
    <div class="notification-full-list" id="notifications-container">
      <?php if (empty($notifications)): ?>
        <div class="empty-state" style="padding:40px;">
          <div class="icon">🔔</div>
          <h3>No notifications found</h3>
          <p>You are all caught up!</p>
        </div>
      <?php else: ?>
        <?php foreach ($notifications as $n): ?>
          <div class="notification-item-row <?= $n['is_read'] == 0 ? 'unread' : '' ?>" 
               data-id="<?=$n['id']?>" 
               data-status="<?=$n['is_read'] == 0 ? 'unread' : 'read'?>"
               style="display:flex; justify-content:space-between; align-items:center; padding:16px 24px; border-bottom:1px solid var(--border-color); cursor:pointer; transition: background 0.2s;"
               onclick="openNotificationDetails(<?=$n['id']?>, '<?=sanitize(addslashes($n['title']))?>', '<?=sanitize(addslashes($n['message']))?>', '<?=formatDate($n['created_at'], 'M d, Y H:i')?>')">
            <div style="display:flex; gap:16px; align-items:center;">
              <div class="stat-icon <?= $n['type'] === 'success' ? 'green' : ($n['type'] === 'warning' ? 'orange' : ($n['type'] === 'danger' ? 'red' : 'blue')) ?>" style="width:36px; height:36px; border-radius:50%; font-size:1.1rem; display:flex; align-items:center; justify-content:center;">
                <?= $n['type'] === 'success' ? '✓' : ($n['type'] === 'warning' ? '⚠' : ($n['type'] === 'danger' ? '✕' : 'ℹ')) ?>
              </div>
              <div>
                <h4 style="margin:0; font-family:var(--font-sans); font-size:0.95rem; font-weight: 600;"><?=sanitize($n['title'])?></h4>
                <p style="margin:2px 0 0 0; font-size:0.85rem; color:var(--text-muted);"><?=sanitize($n['message'])?></p>
                <small style="font-size:0.75rem; color:var(--text-light);"><?=timeAgo($n['created_at'])?> (<?=formatDate($n['created_at'], 'M d, Y H:i')?>)</small>
              </div>
            </div>
            <div style="display:flex; align-items:center; gap:12px;">
              <span class="status-badge badge <?= $n['is_read'] == 0 ? 'badge-warning' : 'badge-secondary' ?>">
                <?= $n['is_read'] == 0 ? 'Unread' : 'Read' ?>
              </span>
              <button class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); openNotificationDetails(<?=$n['id']?>, '<?=sanitize(addslashes($n['title']))?>', '<?=sanitize(addslashes($n['message']))?>', '<?=formatDate($n['created_at'], 'M d, Y H:i')?>')">View</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Details Modal -->
<div class="modal-overlay" id="modal-notification-detail">
  <div class="modal" style="max-width:500px;">
    <div class="modal-header">
      <h3 id="modal-notif-title">Notification Details</h3>
      <button class="modal-close" onclick="Modal.close('modal-notification-detail')">✕</button>
    </div>
    <div class="modal-body" style="padding: 20px 24px;">
      <div style="display:flex; flex-direction:column; gap:12px;">
        <div>
          <small class="text-muted" id="modal-notif-date"></small>
        </div>
        <p id="modal-notif-msg" style="font-size:1rem; line-height:1.6; color:var(--text-dark); margin:0;"></p>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" onclick="Modal.close('modal-notification-detail')">Close</button>
    </div>
  </div>
</div>

<script>
function filterNotifications() {
  const filter = document.getElementById('filter-status').value;
  const items = document.querySelectorAll('.notification-item-row');
  
  items.forEach(item => {
    const status = item.getAttribute('data-status');
    if (filter === 'all' || status === filter) {
      item.style.display = 'flex';
    } else {
      item.style.display = 'none';
    }
  });
}

async function openNotificationDetails(id, title, message, date) {
  document.getElementById('modal-notif-title').textContent = title;
  document.getElementById('modal-notif-msg').textContent = message;
  document.getElementById('modal-notif-date').textContent = date;
  
  Modal.open('modal-notification-detail');
  
  // Call API to mark as read
  const row = document.querySelector(`.notification-item-row[data-id="${id}"]`);
  if (row && row.classList.contains('unread')) {
    await API.post('/api/notifications.php?action=read', { id });
    Notifications.loadCount();
    
    // Update local UI
    row.classList.remove('unread');
    row.setAttribute('data-status', 'read');
    
    const badge = row.querySelector('.status-badge');
    if (badge) {
      badge.className = 'status-badge badge badge-secondary';
      badge.textContent = 'Read';
    }
  }
}

async function markAllNotificationsRead() {
  await API.post('/api/notifications.php?action=read_all', {});
  Notifications.loadCount();
  
  // Update local UI
  document.querySelectorAll('.notification-item-row.unread').forEach(row => {
    row.classList.remove('unread');
    row.setAttribute('data-status', 'read');
    
    const badge = row.querySelector('.status-badge');
    if (badge) {
      badge.className = 'status-badge badge badge-secondary';
      badge.textContent = 'Read';
    }
  });
  
  Toast.success('All notifications marked as read.');
}
</script>

<style>
.notification-item-row:hover {
  background-color: var(--light-hover);
}
.notification-item-row.unread {
  background-color: rgba(99, 102, 241, 0.05);
}
.notification-item-row.unread:hover {
  background-color: rgba(99, 102, 241, 0.08);
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
