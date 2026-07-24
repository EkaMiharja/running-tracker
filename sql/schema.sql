CREATE DATABASE IF NOT EXISTS run_tracker;
USE run_tracker;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    time TIME DEFAULT NULL COMMENT 'waktu lari',
    distance DECIMAL(8,2) NOT NULL COMMENT 'km',
    duration INT NOT NULL COMMENT 'seconds',
    pace DECIMAL(5,2) NOT NULL COMMENT 'min/km',
    notes TEXT,
    route_path JSON,
    pace_per_km JSON COMMENT '[{"km":1,"pace":"5:12","time":"12:45"}]',
    distance_markers JSON COMMENT '[{"km":1,"lat":-6.2088,"lng":106.8456}]',
    type ENUM('manual', 'gps', 'interval') DEFAULT 'manual',
    workout_name VARCHAR(100) DEFAULT NULL,
    interval_data JSON DEFAULT NULL COMMENT '{"intervals":[{"type":"high","duration":120,"distance":0.8,"pace":"4:30"}],"current_interval":3,"total_intervals":8}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
