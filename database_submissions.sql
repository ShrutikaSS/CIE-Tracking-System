-- ============================================
-- Coordinator Submissions Feature — Migration
-- ============================================

USE cie_tracking;

CREATE TABLE IF NOT EXISTS coordinator_submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  message_id INT DEFAULT NULL COMMENT 'Link to HOD message/request that prompted this',
  coordinator_id INT NOT NULL,
  department_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT DEFAULT NULL,
  file_path VARCHAR(255) NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_type VARCHAR(100) DEFAULT NULL,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (message_id) REFERENCES hod_messages(id) ON DELETE SET NULL,
  FOREIGN KEY (coordinator_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
  INDEX idx_coordinator (coordinator_id),
  INDEX idx_message (message_id),
  INDEX idx_submitted (submitted_at)
) ENGINE=InnoDB;

-- Seed: Sample submissions
INSERT INTO coordinator_submissions (message_id, coordinator_id, department_id, title, description, file_path, file_name, file_type) VALUES
  (3, 4, 1, 'TE-CSE-A Defaulters List (Attendance < 75%)', 'Attached is the PDF list of students having attendance below 75% in TE-CSE-A as requested.', '/uploads/submissions/defaulters_list.pdf', 'defaulters_list.pdf', 'application/pdf');
