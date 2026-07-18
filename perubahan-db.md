-- Update Skema Database - Versi Terbaru
USE run_tracker;

-- Tambahkan kolom baru
ALTER TABLE `activities` 
ADD COLUMN `pace_per_km` JSON NULL 
    COMMENT 'Contoh: [{"km":1,"pace":"5:12","time": "12:45"}, {"km":2,"pace":"5:08","time": "25:10"}]'
AFTER `route_path`;

ALTER TABLE `activities` 
ADD COLUMN `distance_markers` JSON NULL 
    COMMENT 'Contoh: [{"km":1,"lat":-6.2088,"lng":106.8456}, {"km":2,"lat":-6.2100,"lng":106.8470}]'
AFTER `pace_per_km`;

-- Buat index untuk performa (opsional tapi direkomendasikan)
CREATE INDEX idx_activity_date_user ON activities(activity_date, user_id);