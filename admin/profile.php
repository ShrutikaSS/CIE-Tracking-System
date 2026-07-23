<?php
$pageTitle = 'Admin Profile';
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin']);
?>

<div class="page-header">
  <div>
    <h1>Administrator Profile</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Admin Profile</div>
  </div>
</div>

<div class="grid-2 profile-grid mb-4">
  <!-- Left Column: Admin Info & Quick Actions -->
  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <h3>⚙️ Administrator Account</h3>
      <span class="badge badge-primary">SYSTEM ADMIN</span>
    </div>
    
    <div class="card-body">
      <!-- Profile Header / Avatar -->
      <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px; border-bottom:1px solid var(--border-color); padding-bottom:20px; flex-wrap:wrap;">
        <div style="width:76px; height:76px; border-radius:50%; background:var(--primary-gradient); color:#ffffff; font-size:2rem; font-weight:700; display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow-md); position:relative; overflow:hidden;" id="admin-avatar-display">
          <?= htmlspecialchars($initials) ?>
        </div>
        <div>
          <h2 style="font-size:1.35rem; color:var(--text-primary); margin-bottom:4px;" id="admin-disp-name">
            <?= htmlspecialchars($user['name'] ?? 'System Administrator') ?>
          </h2>
          <div style="font-size:0.85rem; color:var(--text-secondary); display:flex; align-items:center; gap:6px;">
            <span>📧 <?= htmlspecialchars($user['email'] ?? 'admin@system.local') ?></span>
          </div>
        </div>
      </div>

      <!-- Account Metadata -->
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:18px; margin-bottom:24px;">
        <div style="background:var(--bg-input); padding:14px; border-radius:var(--radius-sm); border:1px solid var(--border-color);">
          <div style="font-size:0.75rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">Role Access</div>
          <div style="font-size:0.95rem; font-weight:700; color:var(--primary); margin-top:2px;">Full Administrator</div>
        </div>
        
        <div style="background:var(--bg-input); padding:14px; border-radius:var(--radius-sm); border:1px solid var(--border-color);">
          <div style="font-size:0.75rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">User ID</div>
          <div style="font-size:0.95rem; font-weight:700; color:var(--text-primary); margin-top:2px;">#<?= htmlspecialchars($user['id'] ?? '1') ?></div>
        </div>

        <div style="background:var(--bg-input); padding:14px; border-radius:var(--radius-sm); border:1px solid var(--border-color);">
          <div style="font-size:0.75rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">System Status</div>
          <div style="font-size:0.95rem; font-weight:700; color:var(--success); margin-top:2px;">🟢 Active Session</div>
        </div>
      </div>

      <!-- Profile Form -->
      <form id="form-admin-profile" onsubmit="handleAdminProfileSubmit(event)">
        <h4 style="margin-bottom:14px;">Edit Profile Information</h4>
        <div class="form-group mb-3">
          <label>Full Name *</label>
          <input type="text" class="form-control" id="admin-name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
        </div>
        <div class="form-group mb-3">
          <label>Email Address *</label>
          <input type="email" class="form-control" id="admin-email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">💾 Save Profile Details</button>
      </form>
    </div>
  </div>

  <!-- Right Column: Password & Security -->
  <div class="card">
    <div class="card-header">
      <h3>🔒 Security & Password</h3>
    </div>
    
    <div class="card-body">
      <form id="form-admin-password" onsubmit="handleAdminPasswordSubmit(event)">
        <div class="form-group mb-3">
          <label>Current Password *</label>
          <input type="password" class="form-control" id="admin-curr-pass" required placeholder="Enter current password">
        </div>
        <div class="form-group mb-3">
          <label>New Password *</label>
          <input type="password" class="form-control" id="admin-new-pass" required minlength="6" placeholder="Enter new password (min 6 chars)">
        </div>
        <div class="form-group mb-3">
          <label>Confirm New Password *</label>
          <input type="password" class="form-control" id="admin-confirm-pass" required minlength="6" placeholder="Confirm new password">
        </div>
        <button type="submit" class="btn btn-secondary w-100">🔑 Update Password</button>
      </form>

      <hr style="margin:24px 0; border:0; border-top:1px solid var(--border-color);">

      <div style="background:var(--primary-lighter); padding:16px; border-radius:var(--radius-sm); border:1px solid var(--primary-light);">
        <h4 style="font-size:0.95rem; color:var(--primary); margin-bottom:6px;">⚡ Administrative Permissions</h4>
        <p style="font-size:0.82rem; color:var(--text-secondary); line-height:1.5;">
          You possess full system privileges to manage Departments, Faculty, Students, Subjects, Activities, and view institution-wide CIE Reports.
        </p>
      </div>
    </div>
  </div>
</div>

<script>
async function handleAdminProfileSubmit(e) {
  e.preventDefault();
  const name = document.getElementById('admin-name').value.trim();
  const email = document.getElementById('admin-email').value.trim();
  
  if (!name || !email) {
    Toast.show('Please fill in all required fields.', 'warning');
    return;
  }

  const res = await API.post('/api/profile.php', { name, email });
  if (res && res.success) {
    Toast.show('Profile updated successfully!', 'success');
    document.getElementById('admin-disp-name').textContent = name;
  } else {
    Toast.show(res?.error || 'Failed to update profile details', 'error');
  }
}

async function handleAdminPasswordSubmit(e) {
  e.preventDefault();
  const current_password = document.getElementById('admin-curr-pass').value;
  const new_password = document.getElementById('admin-new-pass').value;
  const confirm_password = document.getElementById('admin-confirm-pass').value;

  if (new_password !== confirm_password) {
    Toast.show('New passwords do not match!', 'error');
    return;
  }

  const res = await API.post('/api/profile.php?action=change_password', {
    current_password,
    new_password
  });

  if (res && res.success) {
    Toast.show('Password changed successfully!', 'success');
    document.getElementById('form-admin-password').reset();
  } else {
    Toast.show(res?.error || 'Failed to change password.', 'error');
  }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
