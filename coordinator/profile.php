<?php
$pageTitle = 'My Profile';
require_once __DIR__ . '/../includes/header.php';
requireRole(['coordinator']);
?>

<div class="page-header">
  <div>
    <h1>My Profile</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Profile</div>
  </div>
</div>

<div class="grid-2">
  <!-- Left Side: Profile Information card -->
  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
      <h3>👤 Personal Details</h3>
      <button class="btn btn-primary btn-sm" onclick="editProfileMock()">Edit Profile</button>
    </div>
    
    <div class="card-body">
      <!-- Profile Header / Avatar -->
      <div style="display:flex; align-items:center; gap:20px; margin-bottom:30px; border-bottom:1px solid var(--border-color); padding-bottom:20px;">
        <div style="width:70px; height:70px; border-radius:50%; background-color:var(--primary); color:#ffffff; font-size:1.8rem; font-weight:700; display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow-md); position:relative; overflow:hidden;" id="profile-avatar-display">
          SP
        </div>
        <div>
          <h2 style="font-size:1.4rem; color:var(--text-primary); margin-bottom:5px; display:flex; align-items:center; gap:8px;">
            Sneha Patil 
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary);"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          </h2>
          <span class="badge badge-unread" style="font-size:0.8rem; padding: 4px 10px;">CLASS COORDINATOR</span>
        </div>
      </div>

      <!-- Profile Fields Grid -->
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px;">
        <div>
          <label style="font-size:0.8rem; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Employee ID</label>
          <div style="font-size:1rem; font-weight:600; margin-top:4px; color:var(--text-primary);">CC-2024-89</div>
        </div>
        
        <div>
          <label style="font-size:0.8rem; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Department</label>
          <div style="font-size:1rem; font-weight:600; margin-top:4px; color:var(--text-primary);">Computer Science & Engineering</div>
        </div>

        <div>
          <label style="font-size:0.8rem; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Assigned Class</label>
          <div style="font-size:1rem; font-weight:600; margin-top:4px; color:var(--text-primary);">TE-CSE-A (Third Year - Div A)</div>
        </div>

        <div>
          <label style="font-size:0.8rem; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Academic Role</label>
          <div style="font-size:1rem; font-weight:600; margin-top:4px; color:var(--text-primary);">Class Coordinator & Asst. Professor</div>
        </div>

        <div>
          <label style="font-size:0.8rem; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Email Address</label>
          <div style="font-size:1rem; font-weight:600; margin-top:4px; color:var(--text-primary);">sneha.patil@cie.edu</div>
        </div>

        <div>
          <label style="font-size:0.8rem; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Mobile Number</label>
          <div style="font-size:1rem; font-weight:600; margin-top:4px; color:var(--text-primary);">+91 98765 43210</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Change Password Form -->
  <div class="card">
    <div class="card-header">
      <h3>🔑 Change Password</h3>
    </div>
    
    <div class="card-body">
      <form id="profile-password-form" onsubmit="changePasswordMock(event)">
        <div class="form-group">
          <label style="font-weight:500; margin-bottom:5px;">Current Password</label>
          <input type="password" class="form-control" id="p-current" required placeholder="Enter current password">
        </div>
        
        <div class="form-group">
          <label style="font-weight:500; margin-bottom:5px;">New Password</label>
          <input type="password" class="form-control" id="p-new" required minlength="6" placeholder="Enter new password (min. 6 chars)">
        </div>

        <div class="form-group">
          <label style="font-weight:500; margin-bottom:5px;">Confirm New Password</label>
          <input type="password" class="form-control" id="p-confirm" required minlength="6" placeholder="Confirm new password">
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top:10px;">Update Password</button>
      </form>
    </div>
  </div>
</div>

<!-- Mock Edit Profile Modal -->
<div class="modal-overlay" id="modal-edit-profile-mock">
  <div class="modal" style="max-width: 500px;">
    <div class="modal-header">
      <h3>Edit Profile Info</h3>
      <button class="modal-close" onclick="Modal.close('modal-edit-profile-mock')">✕</button>
    </div>
    <div class="modal-body" style="padding: 20px 25px;">
      <form id="edit-profile-form" onsubmit="saveProfileMock(event)">
        <div class="form-group" style="text-align:center; margin-bottom:20px;">
          <div style="width:100px; height:100px; border-radius:50%; background-color:var(--bg-body); border:2px dashed var(--border-color); margin:0 auto; display:flex; align-items:center; justify-content:center; overflow:hidden; position:relative; cursor:pointer;" onclick="document.getElementById('profile-pic-input').click()">
            <img id="mock-preview-img" src="" style="width:100%; height:100%; object-fit:cover; display:none;">
            <div id="mock-upload-icon" style="color:var(--text-muted); display:flex; flex-direction:column; align-items:center; font-size:0.8rem;">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              <span style="margin-top:5px;">Add Photo</span>
            </div>
          </div>
          <input type="file" id="profile-pic-input" accept="image/*" style="display:none;" onchange="previewProfilePic(this)">
        </div>
        <div class="form-group">
          <label style="font-weight:500; margin-bottom:5px;">Mobile Number</label>
          <input type="text" class="form-control" id="mock-mobile" value="+91 98765 43210" required>
        </div>
        <div class="form-group">
          <label style="font-weight:500; margin-bottom:5px;">Email Address</label>
          <input type="email" class="form-control" id="mock-email" value="sneha.patil@cie.edu" required>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:10px; width:100%;">Save Changes (UI only)</button>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-edit-profile-mock')">Cancel</button>
    </div>
  </div>
</div>

<script>
function editProfileMock() {
  Modal.open('modal-edit-profile-mock');
}

function previewProfilePic(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('mock-preview-img').src = e.target.result;
      document.getElementById('mock-preview-img').style.display = 'block';
      document.getElementById('mock-upload-icon').style.display = 'none';
      
      // Update main profile picture
      const avatarDisplay = document.getElementById('profile-avatar-display');
      avatarDisplay.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
    }
    reader.readAsDataURL(input.files[0]);
  }
}

function saveProfileMock(e) {
  e.preventDefault();
  Modal.close('modal-edit-profile-mock');
  Toast.success('Profile details updated successfully! (UI Mock)');
}

function changePasswordMock(e) {
  e.preventDefault();
  const current = document.getElementById('p-current').value;
  const newPw = document.getElementById('p-new').value;
  const confirmPw = document.getElementById('p-confirm').value;

  if (newPw !== confirmPw) {
    Toast.error('New passwords do not match!');
    return;
  }

  // Clear form
  document.getElementById('profile-password-form').reset();
  Toast.success('Password changed successfully! (UI Mock)');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
