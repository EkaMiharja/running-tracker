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

$distance = (float)$input['distance'];
$duration = (int)$input['duration'];
$pace = (float)$input['pace'];
$route_path = $input['route_path'] ?? null;
$pace_per_km = isset($input['pace_per_km']) ? json_encode($input['pace_per_km']) : null;
$distance_markers = isset($input['distance_markers']) ? json_encode($input['distance_markers']) : null;
$type = $input['type'] ?? 'gps';
$date = date('Y-m-d');

if ($distance <= 0 || $duration <= 0) {
    echo json_encode(['success' => false, 'error' => 'Data tidak valid']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO activities (user_id, date, distance, duration, pace, route_path, pace_per_km, distance_markers, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user['id'], $date, $distance, $duration, $pace, $route_path, $pace_per_km, $distance_markers, $type]);
    $id = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'id' => $id]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
