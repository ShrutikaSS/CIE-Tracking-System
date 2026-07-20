# CIE Activity Marks Tracking System

A modern, full-stack web application for tracking Continuous Internal Evaluation (CIE) activity marks across departments. Built with **Core PHP, MySQL, and Vanilla JavaScript** — no frameworks.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=black)

---

## ✨ Features

- **5 User Roles**: Admin, HOD, Faculty, Coordinator, Student
- **Role-based Dashboards** with animated stat cards & Chart.js graphs
- **CIE Activity Management**: Assignment, Quiz, Test, Seminar, Viva
- **Marks Entry System** with max-marks validation, duplicate prevention, and publish workflow
- **Student Panel**: View marks, performance charts, progress bars
- **Admin Module**: CRUD for Departments, Faculty, Students, Subjects
- **Reports**: Student-wise & Subject-wise with PDF/Excel export
- **Notification System**: Real-time alerts for marks published, new activities, deadlines
- **Premium UI**: Light theme, animations, modals, toasts, collapsible sidebar

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.0+ (with MySQLi extension)
- MySQL 8.0+ (or MariaDB 10.5+)
- A local server (XAMPP, WAMP, Laragon, or PHP built-in server)

### Installation

1. **Clone / Copy** the project to your web server directory:
   ```
   # For XAMPP:
   Copy to C:\xampp\htdocs\cie-tracker\
   
   # Or use PHP built-in server:
   cd "ZEAL MARKS TRACKING"
   php -S localhost:8000
   ```

2. **Create the database**:
   - Open phpMyAdmin or MySQL CLI
   - Run the `database.sql` file:
   ```sql
   source database.sql;
   ```

3. **Configure database** (if needed):
   - Edit `includes/db.php` and update credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Your MySQL password
   define('DB_NAME', 'cie_tracking');
   ```

4. **Open in browser**:
   ```
   http://localhost:8000
   ```

---

## 🔑 Demo Accounts

| Role        | Email                  | Password      |
|-------------|------------------------|---------------|
| Admin       | admin@cie.edu          | password123   |
| HOD         | hod.cse@cie.edu        | password123   |
| Faculty     | anil.mehta@cie.edu     | password123   |
| Coordinator | sneha.patil@cie.edu    | password123   |
| Student     | rahul.verma@cie.edu    | password123   |

---

## 📁 Project Structure

```
├── database.sql              # MySQL schema + seed data
├── index.php                 # Entry point (redirect)
├── login.php                 # Login page
├── logout.php                # Logout handler
├── dashboard.php             # Dashboard router
├── includes/
│   ├── db.php                # Database connection
│   ├── auth.php              # Authentication & session
│   ├── functions.php         # Utility functions
│   ├── header.php            # HTML head + header bar
│   ├── sidebar.php           # Sidebar navigation
│   └── footer.php            # Footer + JS includes
├── assets/
│   ├── css/style.css         # Design system (1200+ lines)
│   └── js/app.js             # Core JS engine
├── views/                    # Dashboard views per role
├── admin/                    # Admin CRUD pages
├── faculty/                  # Activity & marks pages
├── student/                  # Student marks & performance
├── reports/                  # Report pages with export
├── api/                      # REST API endpoints
└── README.md
```

---

## 🛡️ Security

- Passwords hashed with `password_hash()` (bcrypt)
- All SQL queries use prepared statements
- Input sanitized with `htmlspecialchars()`
- Session security: `httponly`, `samesite` cookie flags
- Role-based access control on all pages & APIs
- Session regeneration on login

---

## 📊 Tech Stack

| Layer    | Technology |
|----------|-----------|
| Frontend | HTML5, CSS3, Vanilla JS (ES6+) |
| Backend  | Core PHP 8.0+ |
| Database | MySQL 8.0+ |
| Charts   | Chart.js 4.x |
| Export   | jsPDF + jspdf-autotable, SheetJS |
| Fonts    | Google Fonts (Inter) |
