<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
requireLogin();

$user = getCurrentUser();
$user_id = $user['id'];

$stats = $pdo->prepare("
    SELECT 
        COUNT(*) as total_activities,
        COALESCE(SUM(distance), 0) as total_distance,
        COALESCE(SUM(duration), 0) as total_duration,
        CASE WHEN SUM(distance) > 0 THEN SUM(duration) / SUM(distance) / 60 ELSE 0 END as avg_pace
    FROM activities WHERE user_id = ?
");
$stats->execute([$user_id]);
$stats = $stats->fetch();

$recent = $pdo->prepare("SELECT * FROM activities WHERE user_id = ? ORDER BY date DESC, created_at DESC LIMIT 5");
$recent->execute([$user_id]);
$recent_activities = $recent->fetchAll();

$chart_data = $pdo->prepare("
    SELECT DATE_FORMAT(date, '%Y-%m-%d') as day, SUM(distance) as total_km
    FROM activities WHERE user_id = ? 
    AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY day ORDER BY day ASC
");
$chart_data->execute([$user_id]);
$chart_rows = $chart_data->fetchAll();

$labels = [];
$values = [];
foreach ($chart_rows as $row) {
    $labels[] = $row['day'];
    $values[] = (float)$row['total_km'];
}

$title = 'Dashboard - Run Tracker';
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-6 pb-24 md:pb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Halo, <?= htmlspecialchars($user['username']) ?>!</h1>
            <p class="text-[#9CA3AF] text-sm mt-1">Semangat lari hari ini!</p>
        </div>
        <a href="activity/track.php" class="btn-primary text-sm flex items-center gap-2 hide-mobile">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Mulai Lari
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card">
            <p class="stat-label">Total Jarak</p>
            <p class="stat-value text-[#fc5200]"><?= number_format($stats['total_distance'], 2) ?></p>
            <p class="stat-label">km</p>
        </div>
        <div class="card">
            <p class="stat-label">Total Durasi</p>
            <p class="stat-value text-sky-500"><?= formatDuration($stats['total_duration']) ?></p>
            <p class="stat-label">jam</p>
        </div>
        <div class="card">
            <p class="stat-label">Rata-rata Pace</p>
            <p class="stat-value text-[#F59E0B]"><?= $stats['avg_pace'] > 0 ? number_format($stats['avg_pace'], 2) : '--' ?></p>
            <p class="stat-label">min/km</p>
        </div>
        <div class="card">
            <p class="stat-label">Aktivitas</p>
            <p class="stat-value text-[#1F2937]"><?= $stats['total_activities'] ?></p>
            <p class="stat-label">total lari</p>
        </div>
    </div>

    <div class="card mb-6">
        <h2 class="text-lg font-semibold mb-4">Tren 30 Hari</h2>
        <div class="relative" style="height: 250px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Aktivitas Terbaru</h2>
            <a href="activity/history.php" class="text-sm text-sky-500 hover:text-sky-400">Lihat Semua</a>
        </div>
        <?php if (count($recent_activities) > 0): ?>
            <div class="space-y-3">
                <?php foreach ($recent_activities as $act): ?>
                    <a href="activity/detail.php?id=<?= $act['id'] ?>" class="block bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold"><?= formatDate($act['date']) ?></p>
                                <div class="flex items-center gap-3 mt-1 text-sm text-[#9CA3AF]">
                                    <span><?= number_format($act['distance'], 2) ?> km</span>
                                    <span><?= formatDuration($act['duration']) ?></span>
                                    <span><?= formatPace($act['pace']) ?></span>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-[#4B5563] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <p class="text-[#9CA3AF] mb-2">Belum ada aktivitas lari</p>
                <p class="text-sm text-[#6B7280] mb-4">Mulai catat lari pertamamu!</p>
                <a href="activity/track.php" class="btn-primary inline-block">Mulai Lari</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/bottom-nav.php'; ?>
<?php include 'includes/footer.php'; ?>
<script>
const labels = <?= json_encode($labels) ?>;
const values = <?= json_encode($values) ?>;
</script>
<script src="assets/js/chart.js"></script>
