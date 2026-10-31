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

CREATE TABLE IF NOT EXISTS students (
  student_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(254) NOT NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS api_students (
  student_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(254) NOT NULL UNIQUE,
  course VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
  event_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  event_date DATETIME NOT NULL,
  location VARCHAR(150) NOT NULL,
  poster_path VARCHAR(255) NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE events
  ADD COLUMN IF NOT EXISTS poster_path VARCHAR(255) NULL;

ALTER TABLE events
  ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

CREATE TABLE IF NOT EXISTS registrations (
  registration_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  event_id INT UNSIGNED NOT NULL,
  registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT unique_student_event UNIQUE (student_id, event_id),
  CONSTRAINT fk_registrations_student
    FOREIGN KEY (student_id) REFERENCES students (student_id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_registrations_event
    FOREIGN KEY (event_id) REFERENCES events (event_id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
  audit_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  actor_account_id INT UNSIGNED NULL,
  actor_name VARCHAR(100) NOT NULL,
  actor_role VARCHAR(30) NOT NULL,
  action VARCHAR(100) NOT NULL,
  entity_type VARCHAR(64) NOT NULL,
  entity_id INT UNSIGNED NULL,
  details VARCHAR(500) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_audit_logs_created_at (created_at),
  INDEX idx_audit_logs_actor (actor_account_id),
  CONSTRAINT fk_audit_logs_actor
    FOREIGN KEY (actor_account_id) REFERENCES student_accounts (account_id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO students (student_id, full_name, email) VALUES
  (1, 'Aarav Patel', 'aarav@example.com'),
  (2, 'Diya Shah', 'diya@example.com'),
  (3, 'Rohan Mehta', 'rohan@example.com')
ON DUPLICATE KEY UPDATE
  full_name = VALUES(full_name),
  email = VALUES(email);

INSERT INTO events (event_id, title, description, event_date, location) VALUES
  (1, 'Web Development Workshop', 'A hands-on introduction to building web pages.', '2026-11-15 10:00:00', 'Computer Lab 1'),
  (2, 'Open Source Meetup', 'An introduction to collaborative open-source projects.', '2026-11-20 14:00:00', 'Seminar Hall'),
  (3, 'UI Design Showcase', 'A showcase of student interface design projects.', '2026-11-25 11:00:00', 'Main Auditorium')
ON DUPLICATE KEY UPDATE
  title = VALUES(title),
  description = VALUES(description),
  event_date = VALUES(event_date),
  location = VALUES(location);

INSERT INTO registrations (student_id, event_id) VALUES
  (1, 1),
  (2, 1),
  (3, 2),
  (1, 3)
ON DUPLICATE KEY UPDATE
  registered_at = registered_at;
