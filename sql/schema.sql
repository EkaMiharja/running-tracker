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
    type ENUM('manual', 'gps') DEFAULT 'manual',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
