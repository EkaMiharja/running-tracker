-- =============================================
-- Update Database untuk Fitur Interval Training
-- Run Tracker
-- =============================================

-- 1. Tambah kolom baru di tabel activities
ALTER TABLE activities
    MODIFY COLUMN type ENUM('manual', 'gps', 'interval') DEFAULT 'manual',
    ADD COLUMN workout_name VARCHAR(100) DEFAULT NULL AFTER type,
    ADD COLUMN interval_data JSON DEFAULT NULL COMMENT '{"phases":[{"type":"high","time":120,"distance":0.8}],"config":{...}}' AFTER workout_name;

-- 2. Buat tabel interval_templates
CREATE TABLE IF NOT EXISTS interval_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    warmup_type ENUM('duration','distance','none') DEFAULT 'none',
    warmup_value DECIMAL(8,2) DEFAULT NULL,
    warmup_unit VARCHAR(10) DEFAULT NULL,
    interval_count INT NOT NULL DEFAULT 8,
    high_type ENUM('duration','distance') NOT NULL DEFAULT 'distance',
    high_value DECIMAL(8,2) NOT NULL,
    high_unit VARCHAR(10) DEFAULT 'km',
    target_pace DECIMAL(5,2) NOT NULL COMMENT 'min/km',
    recovery_type ENUM('duration','distance') NOT NULL DEFAULT 'distance',
    recovery_value DECIMAL(8,2) NOT NULL,
    recovery_unit VARCHAR(10) DEFAULT 'km',
    recovery_mode ENUM('jog','walk','stand') DEFAULT 'jog',
    cooldown_type ENUM('duration','distance','none') DEFAULT 'none',
    cooldown_value DECIMAL(8,2) DEFAULT NULL,
    cooldown_unit VARCHAR(10) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;