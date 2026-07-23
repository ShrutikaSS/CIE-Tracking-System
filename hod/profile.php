<?php
/**
 * Profile Page for HOD
 */
$pageTitle = 'Profile';
require_once __DIR__ . '/../includes/header.php';
requireRole(['hod']);

// Fetch detailed HOD profile
$profile = dbFetchOne("
    SELECT u.name, u.email, u.role, f.employee_id, f.designation, f.phone, d.name as dept_name, d.code as dept_code
    FROM users u
    LEFT JOIN faculty f ON f.user_id = u.id
    LEFT JOIN departments d ON u.department_id = d.id
    WHERE u.id = ?
", 'i', [$user['id']]);

$initials = strtoupper(substr($profile['name'], 0, 1) . substr(strrchr($profile['name'], ' ') ?: $profile['name'], 1, 1));
?>

<div class="page-header">
  <div>
    <h1>My Profile</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Profile</div>
  </div>
</div>

<div class="grid-2" style="grid-template-columns: 1fr 2fr; align-items: start; gap: 24px;">
  <!-- Left Side: Profile Card & Actions -->
  <div class="card text-center">
    <div class="card-body" style="padding: 40px 24px; display:flex; flex-direction:column; align-items:center; gap:16px;">
      <div class="profile-avatar" style="width: 100px; height: 100px; border-radius: 50%; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; border: 4px solid var(--border-color); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <?=$initials?>
      </div>
      <div>
        <h2 style="font-family:var(--font-sans); font-weight:700; margin:0;"><?=sanitize($profile['name'])?></h2>
        <span class="badge badge-purple" style="margin-top:6px;"><?=roleLabel($profile['role'])?></span>
      </div>
      <div class="text-muted" style="font-size:0.875rem;">
        Member since: <strong><?=formatDate($user['created_at'] ?? '', 'M d, Y')?></strong>
      </div>
      <div class="divider" style="width: 100%; border-top: 1px solid var(--border-color);"></div>
      <div style="display:flex; flex-direction:column; gap:12px; width:100%;">
        <button class="btn btn-primary w-100" onclick="openEditProfileModal()">✏️ Edit Profile</button>
        <button class="btn btn-secondary w-100" onclick="openChangePasswordModal()">🔑 Change Password</button>
      </div>
    </div>
  </div>

  <!-- Right Side: Profile Details -->
  <div class="card">
    <div class="card-header">
      <h3>Profile Details</h3>
    </div>
    <div class="card-body" style="padding: 24px;">
<style>
@media (max-width: 600px) {
  .profile-detail-grid {
    grid-template-columns: 1fr !important;
    gap: 10px !important;
  }
}
</style>
      <div class="profile-detail-grid" style="display:grid; grid-template-columns: 1fr 2fr; gap: 20px; font-size: 0.95rem;">
        <div style="font-weight: 600; color: var(--text-muted);">Employee ID</div>
        <div><span class="badge badge-info"><?=$profile['employee_id'] ?: '—'?></span></div>
        
        <div style="font-weight: 600; color: var(--text-muted);">Full Name</div>
        <div style="font-weight: 600; color: var(--text-dark);"><?=sanitize($profile['name'])?></div>
        
        <div style="font-weight: 600; color: var(--text-muted);">Email Address</div>
        <div><?=sanitize($profile['email'])?></div>
        
        <div style="font-weight: 600; color: var(--text-muted);">Phone Number</div>
        <div><?=$profile['phone'] ? sanitize($profile['phone']) : '<span class="text-muted">—</span>'?></div>
        
        <div style="font-weight: 600; color: var(--text-muted);">Designation</div>
        <div><?=sanitize($profile['designation'] ?: '—')?></div>
        
        <div style="font-weight: 600; color: var(--text-muted);">Department</div>
        <div><?=sanitize($profile['dept_name'])?> (<?=sanitize($profile['dept_code'])?>)</div>
        
        <div style="font-weight: 600; color: var(--text-muted);">Role Permissions</div>
        <div><span class="badge badge-purple">Head of Department (HOD)</span></div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal-overlay" id="modal-edit-profile">
  <div class="modal" style="max-width: 500px;">
    <div class="modal-header">
      <h3>Edit Profile</h3>
      <button class="modal-close" onclick="Modal.close('modal-edit-profile')">✕</button>
    </div>
    <div class="modal-body">
      <form id="form-edit-profile" onsubmit="event.preventDefault(); submitEditProfile();">
        <div class="form-group">
          <label>Full Name *</label>
          <input type="text" class="form-control" id="ep-name" value="<?=sanitize($profile['name'])?>" required>
        </div>
        <div class="form-group">
          <label>Email Address *</label>
          <input type="email" class="form-control" id="ep-email" value="<?=sanitize($profile['email'])?>" required>
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="text" class="form-control" id="ep-phone" value="<?=sanitize($profile['phone'] ?? '')?>">
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-edit-profile')">Cancel</button>
      <button class="btn btn-primary" onclick="submitEditProfile()">Save Changes</button>
    </div>
  </div>
</div>

<script>
function openEditProfileModal() {
  Modal.open('modal-edit-profile');
}

async function submitEditProfile() {
  const name = document.getElementById('ep-name').value.trim();
  const email = document.getElementById('ep-email').value.trim();
  const phone = document.getElementById('ep-phone').value.trim();
  
  if (!name || !email) {
    return Toast.error('Name and Email are required.');
  }
  
  const res = await API.post('/api/auth.php', {
    action: 'update_profile',
    name: name,
    email: email,
    phone: phone
  });
  
  if (res && res.success) {
    Toast.success(res.message);
    Modal.close('modal-edit-profile');
    setTimeout(() => {
      window.location.reload();
    }, 1000);
  } else {
    Toast.error(res?.message || 'Failed to update profile.');
  }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
