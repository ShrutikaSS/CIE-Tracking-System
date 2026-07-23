/**
 * CIE Activity Marks Tracking System
 * Core JavaScript Engine
 */

// ── API Wrapper ──
const API = {
  async request(url, options = {}) {
    const defaultOpts = {
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    };

    const merged = { ...defaultOpts, ...options };
    if (options.headers) {
      merged.headers = { ...defaultOpts.headers, ...options.headers };
    }

    try {
      showLoading();
      const response = await fetch(url, merged);
      const data = await response.json();
      hideLoading();

      if (!response.ok && response.status === 401) {
        window.location.href = '/index.php';
        return null;
      }

      return data;
    } catch (error) {
      hideLoading();
      console.error('API Error:', error);
      Toast.error('Network error. Please try again.');
      return null;
    }
  },

  get(url) {
    return this.request(url);
  },

  post(url, data) {
    return this.request(url, {
      method: 'POST',
      body: JSON.stringify(data)
    });
  },

  put(url, data) {
    return this.request(url, {
      method: 'PUT',
      body: JSON.stringify(data)
    });
  },

  delete(url) {
    return this.request(url, { method: 'DELETE' });
  }
};


// ── Toast Notifications ──
const Toast = {
  container: null,

  init() {
    this.container = document.getElementById('toast-container');
    if (!this.container) {
      this.container = document.createElement('div');
      this.container.id = 'toast-container';
      this.container.className = 'toast-container';
      document.body.appendChild(this.container);
    }
  },

  show(message, type = 'info', title = '', duration = 4000) {
    if (!this.container) this.init();

    const icons = {
      success: '✓',
      error: '✕',
      warning: '⚠',
      info: 'ℹ'
    };

    const titles = {
      success: 'Success',
      error: 'Error',
      warning: 'Warning',
      info: 'Info'
    };

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.style.setProperty('--toast-duration', `${duration}ms`);
    toast.innerHTML = `
      <span class="toast-icon">${icons[type] || icons.info}</span>
      <div class="toast-content">
        <div class="toast-title">${title || titles[type] || titles.info}</div>
        <div class="toast-message">${message}</div>
      </div>
      <button class="toast-dismiss" onclick="Toast.dismiss(this.parentElement)">✕</button>
    `;

    this.container.appendChild(toast);

    setTimeout(() => this.dismiss(toast), duration);
  },

  dismiss(toast) {
    if (!toast || toast.classList.contains('removing')) return;
    toast.classList.add('removing');
    setTimeout(() => toast.remove(), 250);
  },

  success(msg, title) { this.show(msg, 'success', title); },
  error(msg, title) { this.show(msg, 'error', title); },
  warning(msg, title) { this.show(msg, 'warning', title); },
  info(msg, title) { this.show(msg, 'info', title); }
};


// ── Modal Manager ──
const Modal = {
  open(id) {
    const overlay = document.getElementById(id);
    if (!overlay) return;
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Focus first input
    setTimeout(() => {
      const firstInput = overlay.querySelector('input, select, textarea');
      if (firstInput) firstInput.focus();
    }, 300);
  },

  close(id) {
    const overlay = document.getElementById(id);
    if (!overlay) return;
    overlay.classList.remove('open');
    document.body.style.overflow = '';

    // Reset form
    const form = overlay.querySelector('form');
    if (form) form.reset();

    // Clear errors
    overlay.querySelectorAll('.form-error').forEach(el => el.classList.remove('show'));
    overlay.querySelectorAll('.form-control.error').forEach(el => el.classList.remove('error'));
  }
};

// Close modal on overlay click
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('open');
    document.body.style.overflow = '';
  }
});

// Close modal on Escape
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.open').forEach(m => {
      m.classList.remove('open');
      document.body.style.overflow = '';
    });
  }
});


// ── Sidebar Toggle ──
const Sidebar = {
  init() {
    const saved = localStorage.getItem('sidebar-collapsed');
    if (saved === 'true') {
      document.body.classList.add('sidebar-collapsed');
    }

    // Highlight active nav item
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-item').forEach(item => {
      const href = item.getAttribute('href');
      if (href && currentPath.includes(href.replace(/^\//, ''))) {
        item.classList.add('active');
      }
    });
  },

  toggle() {
    document.body.classList.toggle('sidebar-collapsed');
    const collapsed = document.body.classList.contains('sidebar-collapsed');
    localStorage.setItem('sidebar-collapsed', collapsed);
  }
};


// ── Loading Overlay ──
let loadingCount = 0;

function showLoading() {
  loadingCount++;
  const overlay = document.getElementById('loading-overlay');
  if (overlay) overlay.classList.add('show');
}

function hideLoading() {
  loadingCount--;
  if (loadingCount <= 0) {
    loadingCount = 0;
    const overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.classList.remove('show');
  }
}


// ── Table Sorting & Pagination ──
const DataTable = {
  instances: {},

  init(tableId, options = {}) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const config = {
      perPage: options.perPage || 10,
      currentPage: 1,
      sortCol: null,
      sortDir: 'asc',
      data: [],
      filteredData: [],
      ...options
    };

    // Parse table data from tbody
    const tbody = table.querySelector('tbody');
    const headers = table.querySelectorAll('thead th');

    // Make headers sortable
    headers.forEach((th, i) => {
      if (th.dataset.sortable === 'false') return;
      th.style.cursor = 'pointer';
      th.innerHTML += ' <span class="sort-icon">↕</span>';
      th.addEventListener('click', () => this.sort(tableId, i));
    });

    this.instances[tableId] = config;
    return this;
  },

  sort(tableId, colIndex) {
    const config = this.instances[tableId];
    if (!config) return;

    const table = document.getElementById(tableId);
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    // Toggle direction
    if (config.sortCol === colIndex) {
      config.sortDir = config.sortDir === 'asc' ? 'desc' : 'asc';
    } else {
      config.sortCol = colIndex;
      config.sortDir = 'asc';
    }

    rows.sort((a, b) => {
      const aVal = a.cells[colIndex]?.textContent.trim() || '';
      const bVal = b.cells[colIndex]?.textContent.trim() || '';

      // Try numeric sort
      const aNum = parseFloat(aVal);
      const bNum = parseFloat(bVal);

      if (!isNaN(aNum) && !isNaN(bNum)) {
        return config.sortDir === 'asc' ? aNum - bNum : bNum - aNum;
      }

      return config.sortDir === 'asc'
        ? aVal.localeCompare(bVal)
        : bVal.localeCompare(aVal);
    });

    // Update sort icons
    table.querySelectorAll('thead th').forEach((th, i) => {
      th.classList.toggle('sorted', i === colIndex);
      const icon = th.querySelector('.sort-icon');
      if (icon) {
        icon.textContent = i === colIndex
          ? (config.sortDir === 'asc' ? '↑' : '↓')
          : '↕';
      }
    });

    // Re-append rows
    rows.forEach(row => tbody.appendChild(row));
  },

  filter(tableId, searchTerm) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr');
    const term = searchTerm.toLowerCase();

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(term) ? '' : 'none';
    });
  }
};


// ── Search with Debounce ──
function debounce(func, wait = 300) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}


// ── Form Validation ──
const FormValidator = {
  validate(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    let isValid = true;
    const fields = form.querySelectorAll('[data-required], [data-min], [data-max], [data-email]');

    // Clear previous errors
    form.querySelectorAll('.form-error').forEach(el => el.classList.remove('show'));
    form.querySelectorAll('.form-control.error').forEach(el => el.classList.remove('error'));

    fields.forEach(field => {
      const value = field.value.trim();
      const errorEl = field.parentElement.querySelector('.form-error');

      // Required
      if (field.hasAttribute('data-required') && !value) {
        this.showError(field, errorEl, 'This field is required.');
        isValid = false;
        return;
      }

      // Email
      if (field.hasAttribute('data-email') && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
          this.showError(field, errorEl, 'Please enter a valid email address.');
          isValid = false;
          return;
        }
      }

      // Min
      if (field.hasAttribute('data-min') && value) {
        const min = parseFloat(field.dataset.min);
        const numVal = parseFloat(value);
        if (isNaN(numVal) || numVal < min) {
          this.showError(field, errorEl, `Minimum value is ${min}.`);
          isValid = false;
          return;
        }
      }

      // Max
      if (field.hasAttribute('data-max') && value) {
        const max = parseFloat(field.dataset.max);
        const numVal = parseFloat(value);
        if (isNaN(numVal) || numVal > max) {
          this.showError(field, errorEl, `Maximum value is ${max}.`);
          isValid = false;
          return;
        }
      }
    });

    return isValid;
  },

  showError(field, errorEl, message) {
    field.classList.add('error');
    if (errorEl) {
      errorEl.textContent = message;
      errorEl.classList.add('show');
    }
  }
};


// ── Notification System ──
const Notifications = {
  async loadCount() {
    const badge = document.getElementById('notification-count');
    if (!badge) return;

    const data = await API.get('/api/notifications.php?action=count');
    if (data && data.success) {
      const count = data.count;
      badge.textContent = count;
      badge.style.display = count > 0 ? 'flex' : 'none';
    }
  },

  async loadList() {
    const list = document.getElementById('notification-list');
    if (!list) return;

    const data = await API.get('/api/notifications.php?action=list');
    if (data && data.success) {
      if (data.notifications.length === 0) {
        list.innerHTML = '<div class="notification-empty">🔔 No notifications</div>';
        return;
      }

      list.innerHTML = data.notifications.map(n => `
        <div class="notification-item ${n.is_read == 0 ? 'unread' : ''}" 
             data-id="${n.id}"
             onclick="Notifications.markRead(${n.id}, '${n.link ? n.link.replace(/'/g, "\\'") : ''}')">
          <div class="notification-item-icon stat-icon ${n.type === 'success' ? 'green' : n.type === 'warning' ? 'orange' : n.type === 'danger' ? 'red' : 'blue'}">
            ${n.type === 'success' ? '🟢' : n.type === 'warning' ? '🟡' : n.type === 'danger' ? '🔴' : '🔵'}
          </div>
          <div class="notification-item-content">
            <div class="title" style="display:flex; justify-content:space-between; align-items:center;">
              <span>${this.escapeHtml(n.title)}</span>
              ${n.event_type ? `<span class="badge ${n.type === 'danger' ? 'badge-danger' : n.type === 'warning' ? 'badge-warning' : n.type === 'success' ? 'badge-success' : 'badge-info'}" style="font-size:0.65rem; padding:1px 5px;">${this.escapeHtml(n.event_type.replace(/_/g, ' '))}</span>` : ''}
            </div>
            <div class="message">${this.escapeHtml(n.message || '')}</div>
            <div class="time">${n.time_ago}</div>
          </div>
        </div>
      `).join('');
    }
  },

  async markRead(id, link) {
    await API.post('/api/notifications.php?action=read', { id });
    this.loadCount();
    const item = document.querySelector(`.notification-item[data-id="${id}"]`);
    if (item) item.classList.remove('unread');
    if (link) window.location.href = link;
  },

  async markAllRead() {
    await API.post('/api/notifications.php?action=read_all', {});
    this.loadCount();
    document.querySelectorAll('.notification-item.unread').forEach(el => el.classList.remove('unread'));
  },

  toggle() {
    const dropdown = document.getElementById('notification-dropdown');
    if (dropdown) {
      const isOpen = dropdown.classList.contains('open');
      // Close all dropdowns first
      document.querySelectorAll('.notification-dropdown.open, .user-dropdown.open').forEach(d => d.classList.remove('open'));
      if (!isOpen) {
        dropdown.classList.add('open');
        this.loadList();
      }
    }
  },

  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
};


// ── User Menu ──
const UserMenu = {
  toggle() {
    const dropdown = document.getElementById('user-dropdown');
    if (dropdown) {
      const isOpen = dropdown.classList.contains('open');
      document.querySelectorAll('.notification-dropdown.open, .user-dropdown.open').forEach(d => d.classList.remove('open'));
      if (!isOpen) dropdown.classList.add('open');
    }
  }
};

// Close dropdowns when clicking outside
document.addEventListener('click', (e) => {
  if (!e.target.closest('.notification-bell') && !e.target.closest('.notification-dropdown')) {
    document.querySelectorAll('.notification-dropdown.open').forEach(d => d.classList.remove('open'));
  }
  if (!e.target.closest('.user-menu')) {
    document.querySelectorAll('.user-dropdown.open').forEach(d => d.classList.remove('open'));
  }
});


// ── Chart Helpers (Chart.js Wrappers) ──
const Charts = {
  defaultOptions: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: 'bottom',
        labels: {
          font: { family: 'Inter', size: 12 },
          padding: 16,
          usePointStyle: true,
          pointStyleWidth: 8
        }
      },
      tooltip: {
        backgroundColor: '#0f172a',
        titleFont: { family: 'Inter', size: 13, weight: '600' },
        bodyFont: { family: 'Inter', size: 12 },
        padding: 12,
        cornerRadius: 8,
        displayColors: true,
        boxPadding: 4
      }
    },
    scales: {
      x: {
        grid: { display: false },
        ticks: { font: { family: 'Inter', size: 11 }, color: '#94a3b8' }
      },
      y: {
        grid: { color: '#f1f5f9' },
        ticks: { font: { family: 'Inter', size: 11 }, color: '#94a3b8' },
        beginAtZero: true
      }
    }
  },

  bar(canvasId, labels, datasets, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    return new Chart(ctx, {
      type: 'bar',
      data: { labels, datasets },
      options: { ...this.defaultOptions, ...options }
    });
  },

  line(canvasId, labels, datasets, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    return new Chart(ctx, {
      type: 'line',
      data: { labels, datasets },
      options: {
        ...this.defaultOptions,
        elements: {
          line: { tension: 0.4, borderWidth: 2 },
          point: { radius: 4, hoverRadius: 6, borderWidth: 2 }
        },
        ...options
      }
    });
  },

  doughnut(canvasId, labels, data, colors, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    return new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{
          data,
          backgroundColor: colors,
          borderWidth: 0,
          hoverOffset: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              font: { family: 'Inter', size: 12 },
              padding: 16,
              usePointStyle: true
            }
          },
          tooltip: {
            backgroundColor: '#0f172a',
            titleFont: { family: 'Inter', size: 13 },
            bodyFont: { family: 'Inter', size: 12 },
            padding: 12,
            cornerRadius: 8
          }
        },
        ...options
      }
    });
  },

  radar(canvasId, labels, datasets, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    return new Chart(ctx, {
      type: 'radar',
      data: { labels, datasets },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          r: {
            beginAtZero: true,
            max: 100,
            ticks: { font: { family: 'Inter', size: 10 }, backdropColor: 'transparent' },
            grid: { color: '#e2e8f0' },
            pointLabels: { font: { family: 'Inter', size: 11 }, color: '#475569' }
          }
        },
        plugins: {
          legend: {
            position: 'bottom',
            labels: { font: { family: 'Inter', size: 12 }, padding: 16, usePointStyle: true }
          }
        },
        ...options
      }
    });
  }
};


// ── Animated Counter ──
function animateCounter(element, target, duration = 1500) {
  const start = 0;
  const startTime = performance.now();

  function update(currentTime) {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);

    // Ease out cubic
    const eased = 1 - Math.pow(1 - progress, 3);
    const current = Math.round(start + (target - start) * eased);

    element.textContent = current.toLocaleString();

    if (progress < 1) {
      requestAnimationFrame(update);
    }
  }

  requestAnimationFrame(update);
}


// ── Initialize App ──
document.addEventListener('DOMContentLoaded', () => {
  // Init sidebar
  Sidebar.init();

  // Init toast
  Toast.init();

  // Load notification count
  if (document.getElementById('notification-count')) {
    Notifications.loadCount();
  }

  // Animate stat counters
  document.querySelectorAll('[data-count]').forEach(el => {
    const target = parseInt(el.dataset.count, 10);
    if (!isNaN(target)) {
      animateCounter(el, target);
    }
  });

  // Live search binding for specific tables
  document.querySelectorAll('[data-search-table]').forEach(input => {
    const tableId = input.dataset.searchTable;
    input.addEventListener('input', debounce((e) => {
      DataTable.filter(tableId, e.target.value);
    }, 300));
  });

  // Global Search functionality
  const globalSearch = document.getElementById('global-search');
  if (globalSearch) {
    globalSearch.addEventListener('input', function (e) {
      const query = e.target.value.toLowerCase().trim();
      const searchTargets = document.querySelectorAll('tbody tr, .subject-card, .dept-card, .stat-card');

      searchTargets.forEach(el => {
        const text = el.textContent.toLowerCase();
        if (text.includes(query)) {
          el.style.display = '';
        } else {
          el.style.display = 'none';
        }
      });
    });
  }

});
