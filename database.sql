-- ============================================
-- CIE Activity Marks Tracking System
-- Database Schema & Seed Data
-- ============================================

CREATE DATABASE IF NOT EXISTS cie_tracking
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE cie_tracking;

-- ============================================
-- 1. USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','hod','faculty','coordinator','student') NOT NULL DEFAULT 'student',
  department_id INT DEFAULT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_login DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_role (role),
  INDEX idx_email (email)
) ENGINE=InnoDB;

-- ============================================
-- 2. DEPARTMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  code VARCHAR(20) NOT NULL UNIQUE,
  hod_id INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (hod_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Add FK from users to departments (after departments exists)
ALTER TABLE users
  ADD CONSTRAINT fk_users_department
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL;

-- ============================================
-- 3. FACULTY TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS faculty (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  employee_id VARCHAR(30) NOT NULL UNIQUE,
  designation VARCHAR(100) DEFAULT 'Assistant Professor',
  department_id INT NOT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 4. STUDENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  usn VARCHAR(30) NOT NULL UNIQUE,
  prn_number VARCHAR(50) DEFAULT NULL,
  roll_number VARCHAR(20) DEFAULT NULL,
  semester TINYINT NOT NULL DEFAULT 1,
  section VARCHAR(5) DEFAULT 'A',
  department_id INT NOT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  admission_year YEAR DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
  INDEX idx_semester (semester),
  INDEX idx_department (department_id)
) ENGINE=InnoDB;

-- ============================================
-- 5. SUBJECTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS subjects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  code VARCHAR(20) NOT NULL UNIQUE,
  semester TINYINT NOT NULL,
  credits TINYINT DEFAULT 4,
  department_id INT NOT NULL,
  faculty_id INT DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
  FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE SET NULL,
  INDEX idx_semester (semester)
) ENGINE=InnoDB;

-- ============================================
-- 6. SUBJECT-STUDENT ENROLLMENT (M:N)
-- ============================================
CREATE TABLE IF NOT EXISTS subject_students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subject_id INT NOT NULL,
  student_id INT NOT NULL,
  enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  UNIQUE KEY uk_enrollment (subject_id, student_id)
) ENGINE=InnoDB;

-- ============================================
-- 7. ACTIVITIES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS activities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subject_id INT NOT NULL,
  name VARCHAR(200) NOT NULL,
  type ENUM('assignment','quiz','test','seminar','viva','practical','project_review','presentation') NOT NULL DEFAULT 'assignment',
  max_marks DECIMAL(5,2) NOT NULL DEFAULT 10.00,
  activity_date DATE DEFAULT NULL,
  deadline DATE DEFAULT NULL,
  description TEXT DEFAULT NULL,
  status ENUM('draft','active','completed','cancelled') NOT NULL DEFAULT 'active',
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_subject (subject_id),
  INDEX idx_type (type),
  INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================
-- 8. MARKS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS marks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  activity_id INT NOT NULL,
  student_id INT NOT NULL,
  marks_obtained DECIMAL(5,2) DEFAULT NULL,
  remarks VARCHAR(255) DEFAULT NULL,
  entered_by INT NOT NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  published_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (entered_by) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uk_marks (activity_id, student_id),
  INDEX idx_published (is_published)
) ENGINE=InnoDB;

-- ============================================
-- 9. NOTIFICATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  message TEXT DEFAULT NULL,
  type ENUM('info','success','warning','danger') NOT NULL DEFAULT 'info',
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  link VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_read (user_id, is_read),
  INDEX idx_created (created_at)
) ENGINE=InnoDB;


-- ============================================
-- SEED DATA
-- ============================================

-- Departments
INSERT INTO departments (name, code) VALUES
  ('Computer Science & Engineering', 'CSE'),
  ('Electronics & Communication', 'ECE'),
  ('Mechanical Engineering', 'ME'),
  ('Information Science & Engineering', 'ISE');

-- Users (password for ALL: "password123")
-- bcrypt hash of "password123"
SET @pwd = '$2y$10$UGDKMZ//Xvn9cZsCQR/4EOvMGB2d28tUR9u1ioYRz5.UBznJrPJbO';

INSERT INTO users (name, email, password, role, department_id) VALUES
  ('Dr. Rajesh Kumar',     'admin@cie.edu',       @pwd, 'admin',       1),
  ('Dr. Priya Sharma',     'hod.cse@cie.edu',     @pwd, 'hod',         1),
  ('Prof. Anil Mehta',     'anil.mehta@cie.edu',   @pwd, 'faculty',     1),
  ('Prof. Sneha Patil',    'sneha.patil@cie.edu',  @pwd, 'coordinator', 1),
  ('Rahul Verma',          'rahul.verma@cie.edu',  @pwd, 'student',     1),
  ('Priya Nair',           'priya.nair@cie.edu',   @pwd, 'student',     1),
  ('Amit Singh',           'amit.singh@cie.edu',   @pwd, 'student',     1),
  ('Kavya Reddy',          'kavya.reddy@cie.edu',  @pwd, 'student',     1),
  ('Prof. Deepak Joshi',   'deepak.joshi@cie.edu', @pwd, 'faculty',     2),
  ('Dr. Meena Rao',        'hod.ece@cie.edu',      @pwd, 'hod',         2),
  ('Tanish Chavir',        'tanish@cie.edu',       @pwd, 'student',     1),
  ('Vikram Desai',         'vikram.desai@cie.edu',  @pwd, 'student',     2);

-- Set HODs
UPDATE departments SET hod_id = 2 WHERE code = 'CSE';
UPDATE departments SET hod_id = 10 WHERE code = 'ECE';

-- Faculty
INSERT INTO faculty (user_id, employee_id, designation, department_id, phone) VALUES
  (2, 'FAC001', 'Professor & HOD',       1, '9876543210'),
  (3, 'FAC002', 'Associate Professor',   1, '9876543211'),
  (4, 'FAC003', 'Assistant Professor',   1, '9876543212'),
  (9, 'FAC004', 'Associate Professor',   2, '9876543213'),
  (10,'FAC005', 'Professor & HOD',       2, '9876543214');

-- Students
INSERT INTO students (user_id, usn, semester, section, department_id, admission_year) VALUES
  (5,  '1ZL21CS001', 5, 'A', 1, 2021),
  (6,  '1ZL21CS002', 5, 'A', 1, 2021),
  (7,  '1ZL21CS003', 5, 'B', 1, 2021),
  (8,  '1ZL21CS004', 5, 'B', 1, 2021),
  (11, '1ZL21CS005', 5, 'A', 1, 2021),
  (12, '1ZL21EC001', 5, 'A', 2, 2021);

-- Subjects
INSERT INTO subjects (name, code, semester, credits, department_id, faculty_id) VALUES
  ('Data Structures & Algorithms',   'CS301', 3, 4, 1, 2),
  ('Database Management Systems',    'CS302', 3, 4, 1, 3),
  ('Operating Systems',              'CS401', 4, 4, 1, 2),
  ('Computer Networks',              'CS402', 4, 3, 1, 3),
  ('Software Engineering',           'CS501', 5, 4, 1, 2),
  ('Machine Learning',               'CS502', 5, 4, 1, 3),
  ('Digital Signal Processing',      'EC301', 3, 4, 2, 4),
  ('VLSI Design',                    'EC401', 4, 4, 2, 5);

-- Enroll students in semester-5 CSE subjects
INSERT INTO subject_students (subject_id, student_id) VALUES
  (5, 1), (5, 2), (5, 3), (5, 4), (5, 5),
  (6, 1), (6, 2), (6, 3), (6, 4), (6, 5),
  (7, 6), (8, 6);

-- Activities
INSERT INTO activities (subject_id, name, type, max_marks, activity_date, deadline, description, status, created_by) VALUES
  (5, 'Assignment 1: UML Diagrams',      'assignment', 20.00, '2026-07-01', '2026-07-15', 'Draw class, sequence, and use case diagrams for a library system.', 'completed', 3),
  (5, 'Quiz 1: SDLC Models',             'quiz',       10.00, '2026-07-05', '2026-07-05', 'MCQ quiz on Waterfall, Agile, Spiral models.',                      'completed', 3),
  (5, 'Test 1: Mid Semester',             'test',       30.00, '2026-07-10', '2026-07-10', 'Mid-sem exam covering Units 1-3.',                                  'completed', 3),
  (5, 'Seminar: Agile Methodologies',     'seminar',    15.00, '2026-07-20', '2026-07-25', 'Present on any Agile methodology (Scrum, Kanban, XP).',             'active',    3),
  (6, 'Assignment 1: Linear Regression',  'assignment', 20.00, '2026-07-03', '2026-07-17', 'Implement linear regression from scratch in Python.',               'completed', 3),
  (6, 'Quiz 1: Supervised Learning',      'quiz',       10.00, '2026-07-08', '2026-07-08', 'Quiz on classification vs regression.',                             'active',    3),
  (6, 'Viva: ML Project Review',          'viva',       25.00, '2026-08-01', '2026-08-01', 'Viva on semester ML project.',                                      'draft',     3),
  (7, 'Assignment 1: Filter Design',      'assignment', 20.00, '2026-07-05', '2026-07-19', 'Design FIR filter using windowing technique.',                       'active',    9);

-- Marks (for completed activities)
INSERT INTO marks (activity_id, student_id, marks_obtained, remarks, entered_by, is_published, published_at) VALUES
  -- Activity 1: SE Assignment 1
  (1, 1, 18.00, 'Excellent work',        3, 1, '2026-07-16 10:00:00'),
  (1, 2, 15.50, 'Good effort',           3, 1, '2026-07-16 10:00:00'),
  (1, 3, 12.00, 'Needs improvement',     3, 1, '2026-07-16 10:00:00'),
  (1, 4, 19.00, 'Outstanding',           3, 1, '2026-07-16 10:00:00'),
  (1, 5, 16.50, 'Well done',             3, 1, '2026-07-16 10:00:00'),
  -- Activity 2: SE Quiz 1
  (2, 1,  9.00, NULL,                    3, 1, '2026-07-06 09:00:00'),
  (2, 2,  7.50, NULL,                    3, 1, '2026-07-06 09:00:00'),
  (2, 3,  6.00, NULL,                    3, 1, '2026-07-06 09:00:00'),
  (2, 4,  8.50, NULL,                    3, 1, '2026-07-06 09:00:00'),
  (2, 5,  8.00, NULL,                    3, 1, '2026-07-06 09:00:00'),
  -- Activity 3: SE Test 1
  (3, 1, 27.00, 'Very good',             3, 1, '2026-07-11 14:00:00'),
  (3, 2, 22.00, NULL,                    3, 1, '2026-07-11 14:00:00'),
  (3, 3, 18.50, 'Can do better',         3, 1, '2026-07-11 14:00:00'),
  (3, 4, 28.00, 'Excellent',             3, 1, '2026-07-11 14:00:00'),
  (3, 5, 24.50, NULL,                    3, 1, '2026-07-11 14:00:00'),
  -- Activity 5: ML Assignment 1
  (5, 1, 17.00, 'Good implementation',   3, 1, '2026-07-18 11:00:00'),
  (5, 2, 14.00, NULL,                    3, 1, '2026-07-18 11:00:00'),
  (5, 3, 11.50, NULL,                    3, 1, '2026-07-18 11:00:00'),
  (5, 4, 19.50, 'Perfect',              3, 1, '2026-07-18 11:00:00'),
  (5, 5, 15.00, NULL,                    3, 1, '2026-07-18 11:00:00');

-- Notifications
INSERT INTO notifications (user_id, title, message, type, is_read, created_at) VALUES
  (5,  'Marks Published',       'Marks for "Assignment 1: UML Diagrams" have been published.',    'success', 0, '2026-07-16 10:05:00'),
  (5,  'New Activity',          'New seminar activity "Agile Methodologies" has been created.',    'info',    0, '2026-07-20 09:00:00'),
  (5,  'Deadline Reminder',     'Assignment "Linear Regression" deadline is approaching.',         'warning', 1, '2026-07-15 08:00:00'),
  (6,  'Marks Published',       'Marks for "Quiz 1: SDLC Models" have been published.',           'success', 0, '2026-07-06 09:05:00'),
  (11, 'Welcome',               'Welcome to the CIE Marks Tracking System!',                      'info',    0, '2026-07-01 08:00:00'),
  (3,  'Activity Created',      'Activity "Seminar: Agile Methodologies" created successfully.',   'success', 1, '2026-07-20 09:00:00'),
  (3,  'Marks Entry Pending',   'Marks for "Quiz 1: Supervised Learning" are pending entry.',      'warning', 0, '2026-07-10 10:00:00');
