<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user = getCurrentUser();
$user_id = $user['id'];

$filter_date = sanitize($_GET['date'] ?? '');

$sql = "SELECT * FROM activities WHERE user_id = ?";
$params = [$user_id];

if (!empty($filter_date)) {
    $sql .= " AND date = ?";
    $params[] = $filter_date;
}

$sql .= " ORDER BY date DESC, created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$activities = $stmt->fetchAll();

$title = 'Riwayat - Run Tracker';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-6 pb-24 md:pb-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Riwayat Aktivitas</h1>
        <a href="create.php" class="btn-primary text-sm flex items-center gap-2 hide-mobile">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Manual
        </a>
    </div>

    <div class="card mb-6">
        <form method="GET" class="flex items-center gap-3">
            <div class="flex-1">
                <input type="date" name="date" class="input-field" value="<?= htmlspecialchars($filter_date) ?>">
            </div>
            <button type="submit" class="btn-primary text-sm px-4">Filter</button>
            <?php if (!empty($filter_date)): ?>
                <a href="history.php" class="btn-secondary text-sm px-4">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (count($activities) > 0): ?>
        <div class="space-y-3">
            <?php foreach ($activities as $act): ?>
                <a href="detail.php?id=<?= $act['id'] ?>" class="card block hover:bg-gray-50 transition-colors cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-[#fc5200]/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#fc5200]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold"><?= formatDate($act['date']) ?></p>
                                <div class="flex items-center gap-3 mt-1 text-sm text-[#9CA3AF]">
                                    <span><?= number_format($act['distance'], 2) ?> km</span>
                                    <span><?= formatDuration($act['duration']) ?></span>
                                    <span><?= formatPace($act['pace']) ?></span>
                                    <?php if ($act['type'] === 'gps'): ?>
                                        <span class="text-sky-500 text-xs border border-sky-500/30 px-2 py-0.5 rounded-full">GPS</span>
                                    <?php else: ?>
                                        <span class="text-[#9CA3AF] text-xs border border-[#4B5563] px-2 py-0.5 rounded-full">Manual</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card text-center py-12">
            <svg class="w-16 h-16 mx-auto text-[#4B5563] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-[#9CA3AF] mb-2">Tidak ada aktivitas</p>
            <p class="text-sm text-[#6B7280] mb-4">
                <?= empty($filter_date) ? 'Belum ada aktivitas lari. Mulai lacak lari sekarang!' : 'Tidak ada aktivitas di tanggal tersebut' ?>
            </p>
            <a href="track.php" class="btn-primary inline-block">Mulai Lari</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/bottom-nav.php'; ?>
<?php include '../includes/footer.php'; ?>
