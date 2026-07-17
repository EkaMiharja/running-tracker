<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user = getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = sanitize($_POST['date'] ?? '');
    $distance = str_replace(',', '.', sanitize($_POST['distance'] ?? ''));
    $hours = (int)($_POST['hours'] ?? 0);
    $minutes = (int)($_POST['minutes'] ?? 0);
    $seconds = (int)($_POST['seconds'] ?? 0);
    $notes = sanitize($_POST['notes'] ?? '');

    $duration = $hours * 3600 + $minutes * 60 + $seconds;

    if (empty($date) || empty($distance) || $duration <= 0) {
        $error = 'Tanggal, jarak, dan durasi harus diisi dengan benar';
    } elseif (!is_numeric($distance) || $distance <= 0) {
        $error = 'Jarak harus berupa angka positif';
    } else {
        $pace = $duration / 60 / $distance;
        $stmt = $pdo->prepare("INSERT INTO activities (user_id, date, distance, duration, pace, notes, type) VALUES (?, ?, ?, ?, ?, ?, 'manual')");
        $stmt->execute([$user['id'], $date, $distance, $duration, $pace, $notes]);
        $success = 'Aktivitas berhasil dicatat!';
    }
}

$title = 'Catat Manual - Run Tracker';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="max-w-2xl mx-auto px-4 py-6 pb-24 md:pb-6">
    <h1 class="text-2xl font-bold mb-6">Catat Aktivitas Manual</h1>

    <?php if ($error): ?>
        <div class="bg-[#EF4444]/10 border border-[#EF4444]/30 text-[#EF4444] px-4 py-3 rounded-xl mb-4 text-sm"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="bg-[#fc5200]/10 border border-[#fc5200]/30 text-[#fc5200] px-4 py-3 rounded-xl mb-4 text-sm"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold mb-4">Detail Lari</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Tanggal</label>
                    <input type="date" name="date" class="input-field" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Jarak (km)</label>
                    <input type="number" step="0.01" min="0.01" name="distance" class="input-field" placeholder="Contoh: 5.2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Durasi</label>
                    <div class="grid grid-cols-3 gap-3">
                        <input type="number" name="hours" class="input-field text-center" placeholder="Jam" min="0" value="0">
                        <input type="number" name="minutes" class="input-field text-center" placeholder="Menit" min="0" max="59" value="0" required>
                        <input type="number" name="seconds" class="input-field text-center" placeholder="Detik" min="0" max="59" value="0">
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-6">
            <h2 class="text-lg font-semibold mb-4">Catatan</h2>
            <div class="space-y-4">
                <textarea name="notes" class="input-field" rows="3" placeholder="Contoh: Lari pagi di taman"></textarea>
            </div>
        </div>
        <button type="submit" class="btn-primary w-full">Simpan Aktivitas</button>
    </form>
</div>

<?php include '../includes/bottom-nav.php'; ?>
<?php include '../includes/footer.php'; ?>
