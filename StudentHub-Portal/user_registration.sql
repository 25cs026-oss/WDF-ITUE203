CREATE DATABASE IF NOT EXISTS studenthub_practical8
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE studenthub_practical8;

CREATE TABLE IF NOT EXISTS student_accounts (
  account_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  username VARCHAR(30) NOT NULL UNIQUE,
  email VARCHAR(254) NOT NULL UNIQUE,
  mobile VARCHAR(20) NOT NULL,
  course VARCHAR(100) NOT NULL,
  study_year VARCHAR(10) NOT NULL,
  gender VARCHAR(30) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('Admin', 'Student') NOT NULL DEFAULT 'Student',
  status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE student_accounts
  ADD COLUMN IF NOT EXISTS role ENUM('Admin', 'Student') NOT NULL DEFAULT 'Student';

ALTER TABLE student_accounts
  ADD COLUMN IF NOT EXISTS status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active';

INSERT IGNORE INTO student_accounts
  (full_name, username, email, mobile, course, study_year, gender, password_hash, role)
VALUES
  ('Demo Administrator', 'demo_admin', 'admin.demo@example.test', '0000000000',
   'B.Tech Computer Engineering', 'Year 4', 'Prefer not to say',
   '$2y$10$3XlEl8tXAlFiPpg9Gv9NQ.w3UlqD7iuG/j83WWT5hpcCEZo.UNDO6', 'Admin'),
  ('Demo Student', 'demo_student', 'student.demo@example.test', '0000000000',
   'B.Tech Computer Engineering', 'Year 1', 'Prefer not to say',
   '$2y$10$QrgBq4FuMW/sgnNanZ.2g.i1WhoxF.kUFEVWkB5gHrh6PeJ.NIO96', 'Student');
