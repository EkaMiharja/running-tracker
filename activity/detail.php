<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user = getCurrentUser();
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM activities WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user['id']]);
    redirect('history.php');
}

$stmt = $pdo->prepare("SELECT * FROM activities WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user['id']]);
$activity = $stmt->fetch();

if (!$activity) {
    redirect('history.php');
}

$route_path = json_decode($activity['route_path'], true);

$title = 'Detail - Run Tracker';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-6 pb-24 md:pb-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="history.php" class="text-[#9CA3AF] hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold">Detail Aktivitas</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card text-center">
            <p class="stat-label">Jarak</p>
            <p class="stat-value text-[#fc5200]"><?= number_format($activity['distance'], 2) ?></p>
            <p class="stat-label">km</p>
        </div>
        <div class="card text-center">
            <p class="stat-label">Durasi</p>
            <p class="stat-value text-sky-500"><?= formatDuration($activity['duration']) ?></p>
            <p class="stat-label"></p>
        </div>
        <div class="card text-center">
            <p class="stat-label">Pace Rata-rata</p>
            <p class="stat-value text-[#F59E0B]"><?= formatPace($activity['pace']) ?></p>
            <p class="stat-label"></p>
        </div>
    </div>

    <div class="card mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-[#9CA3AF]">Tanggal</p>
                <p class="font-semibold"><?= formatDate($activity['date']) ?></p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-medium <?= $activity['type'] === 'gps' ? 'bg-sky-500/10 text-sky-500 border border-sky-500/30' : 'bg-[#4B5563]/30 text-[#9CA3AF] border border-[#4B5563]' ?>">
                <?= $activity['type'] === 'gps' ? 'GPS Tracking' : 'Input Manual' ?>
            </span>
        </div>
        <?php if ($activity['notes']): ?>
            <div class="bg-gray-50 rounded-xl p-4 mt-3">
                <p class="text-sm text-[#9CA3AF]">Catatan</p>
                <p class="mt-1"><?= htmlspecialchars($activity['notes']) ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" onsubmit="return confirm('Yakin ingin menghapus aktivitas ini?')" class="mt-4 pt-4 border-t border-gray-200">
            <button type="submit" name="delete" class="w-full py-3 rounded-xl bg-[#EF4444]/10 text-[#EF4444] font-semibold text-sm hover:bg-[#EF4444]/20 transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Hapus Aktivitas
            </button>
        </form>
    </div>

    <?php if ($route_path && count($route_path) > 0): ?>
        <div class="card">
            <h2 class="text-lg font-semibold mb-4">Rute Lari</h2>
            <div id="routeMap" style="height: 350px;" class="rounded-xl"></div>
        </div>
    <?php endif; ?>
</div>

<?php if ($route_path && count($route_path) > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const routeCoords = <?= json_encode($route_path) ?>;
    const map = L.map('routeMap', {
        zoomControl: true,
        attributionControl: false,
        scrollWheelZoom: false
    }).setView(routeCoords[0], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.polyline(routeCoords, { color: '#fc5200', weight: 4, opacity: 0.8 }).addTo(map);
    L.marker(routeCoords[0], {
        icon: L.divIcon({
            className: 'start-marker',
            html: '<div style="background:#fc5200;width:12px;height:12px;border-radius:50%;border:2px solid white;"></div>',
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        })
    }).addTo(map).bindPopup('Mulai');

    L.marker(routeCoords[routeCoords.length - 1], {
        icon: L.divIcon({
            className: 'end-marker',
            html: '<div style="background:#EF4444;width:12px;height:12px;border-radius:50%;border:2px solid white;"></div>',
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        })
    }).addTo(map).bindPopup('Selesai');

    map.fitBounds(L.polyline(routeCoords).getBounds().pad(0.1));
    setTimeout(() => map.invalidateSize(), 300);
});
</script>
<?php endif; ?>

<?php include '../includes/bottom-nav.php'; ?>
<?php include '../includes/footer.php'; ?>
