<!-- HOD Dashboard View -->
<div class="page-header">
  <div>
    <h1>Department Dashboard</h1>
    <div class="breadcrumb">Head of Department — <?= sanitize($user['name']) ?></div>
  </div>
</div>

<div class="stats-grid stagger">
  <div class="stat-card">
    <div class="stat-info">
      <h4>Dept. Students</h4>
      <div class="stat-value" id="stat-students">0</div>
    </div>
    <div class="stat-icon blue">🎓</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Dept. Faculty</h4>
      <div class="stat-value" id="stat-faculty">0</div>
    </div>
    <div class="stat-icon green">👨‍🏫</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Subjects</h4>
      <div class="stat-value" id="stat-subjects">0</div>
    </div>
    <div class="stat-icon purple">📚</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h4>Activities</h4>
      <div class="stat-value" id="stat-activities">0</div>
    </div>
    <div class="stat-icon orange">📝</div>
  </div>
  <div class="stat-card" onclick="window.location.href='/hod/submissions.php'" style="cursor:pointer;">
    <div class="stat-info">
      <h4>Submitted Docs</h4>
      <div class="stat-value" id="stat-submissions">0</div>
    </div>
    <div class="stat-icon green">📥</div>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <div class="card-header"><h3>📊 Marks Trend</h3></div>
    <div class="card-body">
      <div class="chart-container"><canvas id="chart-marks-trend"></canvas></div>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><h3>⚡ Quick Actions</h3></div>
    <div class="card-body">
      <div style="display:flex;flex-direction:column;gap:12px;">
        <a href="/admin/faculty.php" class="btn btn-secondary w-100" style="justify-content:flex-start">👨‍🏫 Department Faculty</a>
        <a href="/admin/students.php" class="btn btn-secondary w-100" style="justify-content:flex-start">🎓 Department Students</a>
        <a href="/admin/subjects.php" class="btn btn-secondary w-100" style="justify-content:flex-start">📚 Subjects</a>
        <a href="/reports/subject_report.php" class="btn btn-secondary w-100" style="justify-content:flex-start">📈 Subject Reports</a>
        <a href="/hod/submissions.php" class="btn btn-secondary w-100" style="justify-content:flex-start">📥 Coordinators Submissions</a>
        <button onclick="openSendMessageModal()" class="btn btn-primary w-100" style="justify-content:flex-start">📩 Send Coordinator Message</button>
      </div>
    </div>
  </div>
</div>

<!-- Send Message to Coordinator Modal -->
<div class="modal-overlay" id="modal-send-message">
  <div class="modal" style="max-width: 500px;">
    <div class="modal-header">
      <h3>Send Message to Coordinators</h3>
      <button class="modal-close" onclick="Modal.close('modal-send-message')">✕</button>
    </div>
    <div class="modal-body" style="padding: 20px 25px;">
      <form id="form-send-message" onsubmit="event.preventDefault(); submitCoordinatorMessage();">
        <div class="form-group mb-3">
          <label class="form-label" style="font-weight:600;">Recipient Coordinator</label>
          <select class="form-control" id="msg-recipient" required>
            <option value="">All Coordinators (Broadcast)</option>
          </select>
        </div>
        <div class="form-group mb-3">
          <label class="form-label" style="font-weight:600;">Priority Level</label>
          <select class="form-control" id="msg-priority" required>
            <option value="normal">Normal</option>
            <option value="important">Important</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
        <div class="form-group mb-3">
          <label class="form-label" style="font-weight:600;">Subject</label>
          <input type="text" class="form-control" id="msg-subject" placeholder="e.g., Submit Mid-Term marks" required>
        </div>
        <div class="form-group mb-3">
          <label class="form-label" style="font-weight:600;">Message Content</label>
          <textarea class="form-control" id="msg-body" rows="5" placeholder="Type your message here..." style="resize:vertical;" required></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('modal-send-message')">Cancel</button>
      <button class="btn btn-primary" onclick="submitCoordinatorMessage()">Send Message</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  const res = await API.get('/api/dashboard.php?action=stats');
  if (res && res.success) {
    const s = res.stats;
    animateCounter(document.getElementById('stat-students'), parseInt(s.students));
    animateCounter(document.getElementById('stat-faculty'), parseInt(s.faculty));
    animateCounter(document.getElementById('stat-subjects'), parseInt(s.subjects));
    animateCounter(document.getElementById('stat-activities'), parseInt(s.activities));
    animateCounter(document.getElementById('stat-submissions'), parseInt(s.submissions));
  }

  const trendRes = await API.get('/api/dashboard.php?action=chart&type=marks_trend');
  if (trendRes && trendRes.success && trendRes.chart.length) {
    Charts.line('chart-marks-trend',
      trendRes.chart.map(d => d.label),
      [{
        label: 'Avg Performance %',
        data: trendRes.chart.map(d => parseFloat(d.value)),
        borderColor: '#4f46e5',
        backgroundColor: 'rgba(79, 70, 229, 0.1)',
        fill: true,
        pointBackgroundColor: '#4f46e5'
      }]
    );
  }
});

async function openSendMessageModal() {
  const select = document.getElementById('msg-recipient');
  
  // Clear any existing options except the first broadcast option
  select.innerHTML = '<option value="">All Coordinators (Broadcast)</option>';
  
  // Reset form
  document.getElementById('form-send-message').reset();

  showLoading();
  try {
    const res = await API.get('/api/messages.php?action=coordinators');
    if (res && res.success) {
      res.coordinators.forEach(c => {
        const option = document.createElement('option');
        option.value = c.id;
        option.textContent = `${c.name} (${c.email})`;
        select.appendChild(option);
      });
      Modal.open('modal-send-message');
    } else {
      Toast.error(res?.message || 'Failed to load coordinators.');
    }
  } catch (err) {
    Toast.error('Error fetching coordinators list.');
  } finally {
    hideLoading();
  }
}

async function submitCoordinatorMessage() {
  const recipientId = document.getElementById('msg-recipient').value;
  const priority = document.getElementById('msg-priority').value;
  const subject = document.getElementById('msg-subject').value.trim();
  const message = document.getElementById('msg-body').value.trim();

  if (!subject || !message) {
    return Toast.error('Please enter both subject and message body.');
  }

  showLoading();
  try {
    const res = await API.post('/api/messages.php?action=send', {
      recipient_id: recipientId,
      priority: priority,
      subject: subject,
      message: message
    });

    if (res && res.success) {
      Toast.success('Message sent to coordinator(s) successfully.');
      Modal.close('modal-send-message');
    } else {
      Toast.error(res?.message || 'Failed to send message.');
    }
  } catch (err) {
    Toast.error('An error occurred while sending the message.');
  } finally {
    hideLoading();
  }
}
</script>
