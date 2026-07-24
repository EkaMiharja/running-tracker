<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

header('Content-Type: application/json');

$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$name = $input['name'] ?? '';
$warmup_type = $input['warmup_type'] ?? 'none';
$warmup_value = $input['warmup_value'] ?? null;
$warmup_unit = $input['warmup_unit'] ?? null;
$interval_count = (int)($input['interval_count'] ?? 8);
$high_type = $input['high_type'] ?? 'distance';
$high_value = (float)($input['high_value'] ?? 0);
$high_unit = $input['high_unit'] ?? 'km';
$target_pace = (float)($input['target_pace'] ?? 0);
$rec_type = $input['rec_type'] ?? 'distance';
$rec_value = (float)($input['rec_value'] ?? 0);
$rec_unit = $input['rec_unit'] ?? 'km';
$rec_mode = $input['rec_mode'] ?? 'jog';
$cool_type = $input['cool_type'] ?? 'none';
$cool_value = $input['cool_value'] ?? null;
$cool_unit = $input['cool_unit'] ?? null;

if (empty($name) || $interval_count <= 0 || $high_value <= 0 || $target_pace <= 0) {
    echo json_encode(['success' => false, 'error' => 'Data tidak lengkap']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO interval_templates (user_id, name, warmup_type, warmup_value, warmup_unit, interval_count, high_type, high_value, high_unit, target_pace, recovery_type, recovery_value, recovery_unit, recovery_mode, cooldown_type, cooldown_value, cooldown_unit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user['id'], $name, $warmup_type, $warmup_value, $warmup_unit, $interval_count, $high_type, $high_value, $high_unit, $target_pace, $rec_type, $rec_value, $rec_unit, $rec_mode, $cool_type, $cool_value, $cool_unit]);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}