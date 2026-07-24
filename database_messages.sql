-- ============================================
-- HOD Messages Feature — Migration
-- Run this in phpMyAdmin after the main database.sql
-- ============================================

USE cie_tracking;

-- HOD Messages Table
CREATE TABLE IF NOT EXISTS hod_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  recipient_id INT DEFAULT NULL COMMENT 'NULL = broadcast to all coordinators in dept',
  department_id INT NOT NULL,
  subject VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  priority ENUM('normal','important','urgent') NOT NULL DEFAULT 'normal',
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  read_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
  INDEX idx_recipient (recipient_id, is_read),
  INDEX idx_department (department_id),
  INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Seed: Sample messages from HOD (user_id=2) to Coordinator (user_id=4)
INSERT INTO hod_messages (sender_id, recipient_id, department_id, subject, message, priority, is_read, created_at) VALUES
  (2, 4, 1, 'Submit CIE marks by Friday', 'Dear Coordinator,\n\nPlease ensure all faculty members submit their CIE marks for Semester 5 subjects by this Friday (25-Jul-2026). The academic audit is scheduled for next week and we need complete data.\n\nRegards,\nDr. Priya Sharma\nHOD - CSE', 'urgent', 0, '2026-07-22 10:30:00'),
  (2, 4, 1, 'Faculty meeting rescheduled', 'The department faculty meeting originally scheduled for Thursday has been moved to Friday at 3:00 PM in Room 301. Please inform all faculty members in your section.\n\nThanks,\nDr. Priya Sharma', 'important', 0, '2026-07-21 14:15:00'),
  (2, 4, 1, 'Student attendance concern', 'I have noticed that several students in TE-CSE-A have attendance below 75%. Please prepare a list of defaulters and share it with me before the next review meeting.\n\nDr. Priya Sharma', 'normal', 1, '2026-07-18 09:00:00'),
  (2, NULL, 1, 'Department circular: New evaluation guidelines', 'All Class Coordinators,\n\nPlease note the revised CIE evaluation guidelines effective from this semester. The weightage for assignments has been increased to 20% from 15%. Refer to the attached circular for details.\n\nHOD - CSE Department', 'important', 0, '2026-07-20 11:00:00');
