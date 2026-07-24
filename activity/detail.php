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

$route_path = json_decode($activity['route_path'] ?? '', true) ?: [];
$pace_per_km = json_decode($activity['pace_per_km'] ?? '', true) ?: [];
$distance_markers = json_decode($activity['distance_markers'] ?? '', true) ?: [];

$title = 'Detail - Run Tracker';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-6 pb-24 md:pb-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="history.php" class="text-[#9CA3AF] hover:text-[#1F2937] transition-colors">
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
                <p class="font-semibold"><?= formatDate($activity['date']) ?> <?php if (formatTime($activity['time'] ?? '')): ?><span class="font-semibold text-[#9CA3AF]"> pukul <?= formatTime($activity['time']) ?></span><?php endif; ?></p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-medium <?php
                if ($activity['type'] === 'gps') echo 'bg-sky-500/10 text-sky-500 border border-sky-500/30';
                elseif ($activity['type'] === 'interval') echo 'bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/30';
                else echo 'bg-[#4B5563]/30 text-[#9CA3AF] border border-[#4B5563]';
            ?>">
                <?php
                if ($activity['type'] === 'gps') echo 'GPS Tracking';
                elseif ($activity['type'] === 'interval') echo 'Interval Training';
                else echo 'Input Manual';
                ?>
            </span>
        </div>
        <?php if ($activity['workout_name']): ?>
            <div class="bg-gray-50 rounded-xl p-4 mt-3">
                <p class="text-sm text-[#9CA3AF]">Workout</p>
                <p class="mt-1 font-semibold"><?= htmlspecialchars($activity['workout_name']) ?></p>
            </div>
        <?php endif; ?>
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

    <?php
    $interval_data = json_decode($activity['interval_data'] ?? '', true);
    if ($activity['type'] === 'interval' && $interval_data && !empty($interval_data['phases'])): ?>
        <div class="card mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Detail Interval</h2>
                <div class="flex bg-gray-100 rounded-lg p-0.5">
                    <button id="btnTime" onclick="switchIntervalView('time')" class="interval-toggle px-3 py-1 text-xs font-semibold rounded-md bg-[#fc5200] text-white transition-all">Waktu</button>
                    <button id="btnPace" onclick="switchIntervalView('pace')" class="interval-toggle px-3 py-1 text-xs font-semibold rounded-md text-[#9CA3AF] transition-all">Pace</button>
                </div>
            </div>
            <div class="relative" style="height: 220px;">
                <canvas id="intervalChart"></canvas>
            </div>
            <div id="intervalLegend" class="flex justify-center gap-4 mt-3">
                <span class="flex items-center gap-1.5 text-xs text-[#6B7280]"><span class="w-3 h-3 rounded-sm bg-[#fc5200]"></span> High Intensity</span>
                <span class="flex items-center gap-1.5 text-xs text-[#6B7280]"><span class="w-3 h-3 rounded-sm bg-[#10B981]"></span> Recovery</span>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($pace_per_km)): ?>
        <div class="card mb-6">
            <h2 class="text-lg font-semibold mb-4">Pace per Kilometer</h2>
            <div class="relative" style="height: 220px;">
                <canvas id="paceChart"></canvas>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($route_path && count($route_path) > 0): ?>
        <div class="card">
            <h2 class="text-lg font-semibold mb-4">Rute Lari</h2>
            <div id="routeMap" style="height: 350px;" class="rounded-xl"></div>
        </div>
    <?php endif; ?>
</div>

<?php if ($route_path && count($route_path) > 0): ?>
<script>
const pacePerKm = <?= json_encode($pace_per_km) ?>;
const distanceMarkers = <?= json_encode($distance_markers) ?>;
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

    if (distanceMarkers && distanceMarkers.length > 0) {
        distanceMarkers.forEach(function (m) {
            if (m.lat && m.lng) {
                L.marker([m.lat, m.lng], {
                    icon: L.divIcon({
                        className: 'km-marker',
                        html: '<div style="background:#fc5200;color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">' + m.km + '</div>',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    })
                }).addTo(map).bindPopup('KM ' + m.km);
            }
        });
    }

    map.fitBounds(L.polyline(routeCoords).getBounds().pad(0.1));
    setTimeout(() => map.invalidateSize(), 300);
});
</script>
<?php endif; ?>

<?php if (!empty($pace_per_km)): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('paceChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const labels = pacePerKm.map(function (p) { return 'KM ' + p.km; });
    const data = pacePerKm.map(function (p) {
        var parts = p.pace.split(':');
        return parseInt(parts[0]) + parseInt(parts[1]) / 60;
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pace (min/km)',
                data: data,
                backgroundColor: '#fc5200',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            const m = Math.floor(ctx.raw);
                            const s = Math.round((ctx.raw - m) * 60);
                            return 'Pace: ' + m + ':' + String(s).padStart(2, '0') + ' /km';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                },
                y: {
                    reverse: true,
                    grid: { color: 'rgba(0,0,0,0.06)' },
                    ticks: {
                        color: '#6B7280',
                        font: { size: 11 },
                        callback: function (v) {
                            const m = Math.floor(v);
                            const s = Math.round((v - m) * 60);
                            return m + ':' + String(s).padStart(2, '0');
                        }
                    }
                }
            }
        }
    });
});
</script>
<?php endif; ?>

<?php
$interval_data = json_decode($activity['interval_data'] ?? '', true);
if ($activity['type'] === 'interval' && $interval_data && !empty($interval_data['phases'])):
    $phases = $interval_data['phases'];
    $chartLabels = [];
    $chartColors = [];
    $chartTime = [];
    $chartPace = [];
    foreach ($phases as $i => $p) {
        $num = $i + 1;
        $chartLabels[] = ($p['type'] === 'high' ? 'H' : 'R') . $p['interval'];
        $chartColors[] = $p['type'] === 'high' ? '#fc5200' : '#10B981';
        $chartTime[] = round($p['time']);
        $dist = $p['distance'] ?? 0;
        $t = $p['time'] ?? 0;
        $chartPace[] = ($t > 0 && $dist > 0) ? round($t / 60 / $dist, 2) : 0;
    }
?>
<script>
var intervalPhases = <?= json_encode($phases) ?>;
var intervalLabels = <?= json_encode($chartLabels) ?>;
var intervalColors = <?= json_encode($chartColors) ?>;
var intervalTimeData = <?= json_encode($chartTime) ?>;
var intervalPaceData = <?= json_encode($chartPace) ?>;
var intervalChart = null;
var currentView = 'time';

function formatSec(secs) {
    var m = Math.floor(secs / 60);
    var s = Math.round(secs % 60);
    return m + ':' + String(s).padStart(2, '0');
}

function formatPaceVal(minPerKm) {
    if (minPerKm <= 0) return '-';
    var m = Math.floor(minPerKm);
    var s = Math.round((minPerKm - m) * 60);
    return m + ':' + String(s).padStart(2, '0') + ' /km';
}

function switchIntervalView(view) {
    currentView = view;
    document.getElementById('btnTime').className = 'interval-toggle px-3 py-1 text-xs font-semibold rounded-md transition-all ' + (view === 'time' ? 'bg-[#fc5200] text-white' : 'text-[#9CA3AF]');
    document.getElementById('btnPace').className = 'interval-toggle px-3 py-1 text-xs font-semibold rounded-md transition-all ' + (view === 'pace' ? 'bg-[#fc5200] text-white' : 'text-[#9CA3AF]');
    if (!intervalChart) return;
    if (view === 'time') {
        intervalChart.data.datasets[0].data = intervalTimeData;
        intervalChart.options.scales.y.reverse = false;
        intervalChart.options.scales.y.ticks.callback = function(v) { return formatSec(v); };
        intervalChart.options.plugins.tooltip.callbacks.label = function(ctx) { return 'Waktu: ' + formatSec(ctx.raw); };
    } else {
        intervalChart.data.datasets[0].data = intervalPaceData;
        intervalChart.options.scales.y.reverse = true;
        intervalChart.options.scales.y.ticks.callback = function(v) { return formatPaceVal(v); };
        intervalChart.options.plugins.tooltip.callbacks.label = function(ctx) { return 'Pace: ' + formatPaceVal(ctx.raw); };
    }
    intervalChart.update();
}

document.addEventListener('DOMContentLoaded', function () {
    var canvas = document.getElementById('intervalChart');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    intervalChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: intervalLabels,
            datasets: [{
                data: intervalTimeData,
                backgroundColor: intervalColors,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) { return 'Waktu: ' + formatSec(ctx.raw); }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                },
                y: {
                    reverse: false,
                    grid: { color: 'rgba(0,0,0,0.06)' },
                    ticks: {
                        color: '#6B7280',
                        font: { size: 11 },
                        callback: function (v) { return formatSec(v); }
                    }
                }
            }
        }
    });
});
</script>
<?php endif; ?>

<?php include '../includes/bottom-nav.php'; ?>
<?php include '../includes/footer.php'; ?>
