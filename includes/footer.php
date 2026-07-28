      </main>

      <!-- App Footer -->
      <footer class="app-footer" style="padding: 20px 30px; margin-top: auto; border-top: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.85rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
          &copy; <?= date('Y') ?> <strong>Department of Artificial Intelligence and Machine Learning</strong>.<br>
          CIE Marks Tracking System
        </div>
        <div style="display: flex; gap: 15px;">
          <a href="#" style="color: var(--primary); text-decoration: none;">Privacy Policy</a>
          <a href="#" style="color: var(--primary); text-decoration: none;">Terms & Conditions</a>
          <a href="mailto:support@cie.edu" style="color: var(--text-muted); text-decoration: none;">support@cie.edu</a>
          <span>+91 98765 43210</span>
        </div>
      </footer>

    </div>
  </div>
  
  <!-- Loading Overlay -->
  <div class="loading-overlay" id="loading-overlay">
    <div class="spinner"></div>
    <div class="loading-text">Loading...</div>
  </div>
  
  <!-- Toast Container -->
  <div class="toast-container" id="toast-container"></div>
  
  <!-- Change Password Modal -->
  <div class="modal-overlay" id="modal-change-password">
    <div class="modal">
      <div class="modal-header">
        <h3>Change Password</h3>
        <button class="modal-close" onclick="Modal.close('modal-change-password')">✕</button>
      </div>
      <div class="modal-body">
        <form id="form-change-password" onsubmit="event.preventDefault(); submitChangePassword();">
          <div class="form-group">
            <label>Current Password</label>
            <input type="password" class="form-control" id="cp-current" required>
          </div>
          <div class="form-group">
            <label>New Password</label>
            <input type="password" class="form-control" id="cp-new" required minlength="6">
          </div>
          <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" class="form-control" id="cp-confirm" required minlength="6">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="Modal.close('modal-change-password')">Cancel</button>
        <button class="btn btn-primary" onclick="submitChangePassword()">Update Password</button>
      </div>
    </div>
  </div>

  <!-- Logout Confirmation Modal -->
  <div class="modal-overlay" id="modal-logout-confirm">
    <div class="modal" style="max-width: 400px;">
      <div class="modal-header">
        <h3>Confirm Logout</h3>
        <button class="modal-close" onclick="Modal.close('modal-logout-confirm')">✕</button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to log out of the CIE Marks Tracking System?</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="Modal.close('modal-logout-confirm')">Cancel</button>
        <button class="btn btn-danger" onclick="window.location.href='/logout.php'">Logout</button>
      </div>
    </div>
  </div>

  <script>
    function openLogoutModal(e) {
      if (e) e.preventDefault();
      Modal.open('modal-logout-confirm');
    }

    function openChangePasswordModal() {
      document.getElementById('form-change-password').reset();
      Modal.open('modal-change-password');
      UserMenu.close();
    }

    async function submitChangePassword() {
      const current = document.getElementById('cp-current').value;
      const newPw = document.getElementById('cp-new').value;
      const confirmPw = document.getElementById('cp-confirm').value;

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
        Modal.close('modal-change-password');
      } else {
        Toast.error(res?.message || 'Failed to change password.');
      }
    }

    // Notification Polling (Every 30s)
    setInterval(async () => {
      if (typeof API !== 'undefined' && typeof NotificationPanel !== 'undefined') {
        const res = await API.get('/api/notifications.php?action=unread_count');
        if (res && res.success && typeof res.count !== 'undefined') {
          const badge = document.getElementById('notification-badge');
          if (badge) {
            badge.textContent = res.count;
            badge.style.display = res.count > 0 ? 'inline-block' : 'none';
          }
        }
      }
    }, 30000);
  </script>
  
  <!-- App JS -->
  <script src="<?= url('/assets/js/app.js') ?>"></script>
  <script src="<?= url('/assets/js/star-border.js') ?>"></script>
</body>
</html>
