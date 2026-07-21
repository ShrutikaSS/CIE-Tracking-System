<?php
$pageTitle = 'Profile';
require_once __DIR__ . '/../includes/header.php';
requireRole(['student']);
?>

<div class="page-header">
  <div>
    <h1>My Profile</h1>
    <div class="breadcrumb"><a href="/dashboard.php">Dashboard</a> / Profile</div>
  </div>
</div>

<div class="grid-3">
  <!-- Left Side: Profile Card -->
  <div class="card" style="grid-column: span 1;">
    <div class="card-body text-center">
      <!-- Interactive Avatar Container -->
      <div style="position:relative; width:120px; height:120px; margin:0 auto 16px; border-radius:50%; overflow:hidden; border:3px solid var(--primary); background:var(--primary-gradient); display:flex; align-items:center; justify-content:center; color:white; font-size:2.5rem; font-weight:700; cursor:pointer;" id="profile-avatar-container" onclick="document.getElementById('profile-avatar-file').click();" title="Click to change profile picture">
        <!-- Initial or Image will load here -->
      </div>
      <h3 id="profile-card-name" style="margin-bottom:4px; font-family:'Lora', serif; font-size:1.3rem;">—</h3>
      <p id="profile-card-role" class="text-muted" style="font-size:0.85rem; text-transform:uppercase; font-weight:600; letter-spacing:0.5px;">Student</p>
      <div class="divider" style="height:1px; background:var(--border-color); margin:16px 0;"></div>
      <div style="text-align:left; font-size:0.825rem;">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
          <span class="text-muted">USN:</span>
          <strong id="profile-card-usn">—</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
          <span class="text-muted">Department:</span>
          <strong id="profile-card-dept">—</strong>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span class="text-muted">Semester:</span>
          <strong id="profile-card-sem">—</strong>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Edit Tabs & Forms -->
  <div class="card" style="grid-column: span 2;">
    <div class="card-header" style="flex-direction:column; align-items:flex-start; gap:12px; padding-bottom:0;">
      <h3 style="margin-bottom:4px;">👤 Profile Management</h3>
      
      <!-- Tab Controls -->
      <div class="profile-tabs" style="display:flex; gap:8px; border-bottom:1px solid var(--border-color); width:100%;">
        <button class="profile-tab-btn active" onclick="switchTab('tab-details', this)" style="background:none; border:none; padding:12px 18px; font-weight:600; cursor:pointer; color:var(--primary); border-bottom:2px solid var(--primary); font-family:inherit; font-size:0.85rem;">Profile Details</button>
        <button class="profile-tab-btn" onclick="switchTab('tab-password', this)" style="background:none; border:none; padding:12px 18px; font-weight:600; cursor:pointer; color:var(--text-secondary); border-bottom:2px solid transparent; font-family:inherit; font-size:0.85rem;">Change Password</button>
      </div>
    </div>
    
    <div class="card-body">
      <!-- Hidden File Input for Avatar Upload -->
      <input type="file" id="profile-avatar-file" accept="image/png, image/jpeg, image/gif, image/webp" style="display:none;" onchange="handleAvatarFileChange(this)">

      <!-- Tab 1: Profile Details -->
      <div id="tab-details" class="profile-tab-content">
        <form id="form-profile" onsubmit="handleProfileSubmit(event)">
          <!-- Read-Only Academic Fields -->
          <div class="form-row">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" class="form-control" id="profile-name" readonly style="background:var(--border-light); cursor:not-allowed;">
            </div>
            <div class="form-group">
              <label>USN</label>
              <input type="text" class="form-control" id="profile-usn" readonly style="background:var(--border-light); cursor:not-allowed;">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>PRN Number</label>
              <input type="text" class="form-control" id="profile-prn" readonly style="background:var(--border-light); cursor:not-allowed;">
            </div>
            <div class="form-group">
              <label>Roll Number</label>
              <input type="text" class="form-control" id="profile-roll" readonly style="background:var(--border-light); cursor:not-allowed;">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Department</label>
              <input type="text" class="form-control" id="profile-dept" readonly style="background:var(--border-light); cursor:not-allowed;">
            </div>
            <div class="form-group">
              <label>Semester & Section</label>
              <input type="text" class="form-control" id="profile-sem-sec" readonly style="background:var(--border-light); cursor:not-allowed;">
            </div>
          </div>

          <div class="divider" style="height:1px; background:var(--border-color); margin:20px 0;"></div>
          
          <!-- Editable Contact Info -->
          <h4 style="font-family:'Inter', sans-serif; font-size:0.95rem; margin-bottom:16px; color:var(--primary); font-weight:600;">Editable Contact Details</h4>

          <div class="form-row">
            <div class="form-group">
              <label>Email Address <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="profile-email" required>
            </div>
            <div class="form-group">
              <label>Mobile Number</label>
              <input type="tel" class="form-control" id="profile-phone">
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>

      <!-- Tab 2: Change Password -->
      <div id="tab-password" class="profile-tab-content" style="display:none;">
        <form id="form-update-password" onsubmit="handlePasswordChangeSubmit(event)">
          <div class="form-group">
            <label>Current Password</label>
            <input type="password" class="form-control" id="current-password" required placeholder="Enter current password">
          </div>
          
          <div class="form-group">
            <label>New Password</label>
            <input type="password" class="form-control" id="new-password" required minlength="6" placeholder="Enter new password (min. 6 characters)">
          </div>
          
          <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" class="form-control" id="confirm-password" required minlength="6" placeholder="Confirm new password">
          </div>
          
          <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
            <button type="submit" class="btn btn-primary">Update Password</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
#profile-avatar-container:hover .avatar-hover-overlay {
  opacity: 1 !important;
}
</style>

<script>
let currentAvatarSrc = '';
let currentInitials = '';

document.addEventListener('DOMContentLoaded', async () => {
  await loadProfileData();
});

function switchTab(tabId, btn) {
  document.querySelectorAll('.profile-tab-content').forEach(content => {
    content.style.display = 'none';
  });
  
  document.getElementById(tabId).style.display = 'block';
  
  document.querySelectorAll('.profile-tab-btn').forEach(button => {
    button.classList.remove('active');
    button.style.color = 'var(--text-secondary)';
    button.style.borderBottomColor = 'transparent';
  });
  
  btn.classList.add('active');
  btn.style.color = 'var(--primary)';
  btn.style.borderBottomColor = 'var(--primary)';
}

async function loadProfileData() {
  const res = await API.get('/api/profile.php');
  if (res && res.success) {
    const p = res.profile;
    
    // Set text on card
    document.getElementById('profile-card-name').textContent = p.name;
    document.getElementById('profile-card-usn').textContent = p.usn;
    document.getElementById('profile-card-dept').textContent = p.department_name;
    document.getElementById('profile-card-sem').textContent = `Semester ${p.semester} (${p.section})`;
    
    // Render avatar
    currentInitials = p.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    currentAvatarSrc = p.avatar || '';
    renderAvatarPreview(currentAvatarSrc);
    
    // Set form fields
    document.getElementById('profile-name').value = p.name;
    document.getElementById('profile-usn').value = p.usn;
    document.getElementById('profile-prn').value = p.prn_number || '—';
    document.getElementById('profile-roll').value = p.roll_number || '—';
    document.getElementById('profile-dept').value = p.department_name;
    document.getElementById('profile-sem-sec').value = `Semester ${p.semester} - Section ${p.section}`;
    
    document.getElementById('profile-email').value = p.email;
    document.getElementById('profile-phone').value = p.phone || '';
  } else {
    Toast.error('Failed to load profile data.');
  }
}

function renderAvatarPreview(src) {
  const avatarContainer = document.getElementById('profile-avatar-container');
  
  // Camera SVG icon overlay
  const overlayHtml = `
    <div class="avatar-hover-overlay" style="position:absolute; inset:0; background:rgba(13,58,113,0.65); color:white; font-size:0.75rem; display:flex; flex-direction:column; gap:4px; align-items:center; justify-content:center; opacity:0; transition:opacity 0.2s; border-radius:50%;">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
      <span style="font-weight:600; font-size:0.65rem; text-transform:uppercase; letter-spacing:0.5px;">Change</span>
    </div>
  `;
  
  if (src) {
    avatarContainer.innerHTML = `<img src="${src}" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">${overlayHtml}`;
  } else {
    avatarContainer.innerHTML = `${currentInitials}${overlayHtml}`;
  }
}

function handleAvatarFileChange(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const maxSize = 2 * 1024 * 1024;
    
    if (!allowedTypes.includes(file.type)) {
      Toast.error('Only JPG, PNG, GIF, and WEBP images are allowed.');
      input.value = '';
      return;
    }
    if (file.size > maxSize) {
      Toast.error('Image size must be less than 2MB.');
      input.value = '';
      return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
      renderAvatarPreview(e.target.result);
      Toast.info('New picture selected. Click "Save Changes" to save.');
    };
    reader.readAsDataURL(file);
  }
}

async function handleProfileSubmit(event) {
  event.preventDefault();
  
  const email = document.getElementById('profile-email').value.trim();
  const phone = document.getElementById('profile-phone').value.trim();
  const fileInput = document.getElementById('profile-avatar-file');
  
  if (!email) {
    return Toast.error('Email is required.');
  }
  
  const formData = new FormData();
  formData.append('email', email);
  formData.append('phone', phone);
  
  if (fileInput.files.length > 0) {
    formData.append('avatar', fileInput.files[0]);
  }
  
  showLoading();
  try {
    const response = await fetch('/api/profile.php', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    
    const res = await response.json();
    hideLoading();
    
    if (res && res.success) {
      Toast.success(res.message);
      fileInput.value = ''; // Reset file input
      await loadProfileData();
      
      // Update header avatar
      const headerAvatar = document.querySelector('.user-avatar');
      if (headerAvatar && res.avatar) {
        headerAvatar.innerHTML = `<img src="${res.avatar}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
      }
    } else {
      Toast.error(res?.message || 'Failed to update profile.');
    }
  } catch (error) {
    hideLoading();
    console.error(error);
    Toast.error('An error occurred during submission.');
  }
}

async function handlePasswordChangeSubmit(event) {
  event.preventDefault();
  
  const current = document.getElementById('current-password').value;
  const newPw = document.getElementById('new-password').value;
  const confirmPw = document.getElementById('confirm-password').value;

  if (!current || !newPw || !confirmPw) {
    return Toast.error('All fields are required.');
  }
  if (newPw !== confirmPw) {
    return Toast.error('New passwords do not match.');
  }

  const res = await API.post('/api/auth.php', {
    action: 'change_password',
    current_password: current,
    new_password: newPw
  });

  if (res && res.success) {
    Toast.success(res.message);
    document.getElementById('form-update-password').reset();
  } else {
    Toast.error(res?.message || 'Failed to update password.');
  }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
