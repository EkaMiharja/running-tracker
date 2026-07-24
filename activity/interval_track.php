<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user = getCurrentUser();

$workout_name = sanitize($_GET['name'] ?? 'Interval');
$warmup_type = sanitize($_GET['warmup_type'] ?? 'none');
$warmup_value = (float)($_GET['warmup_value'] ?? 0);
$warmup_unit = sanitize($_GET['warmup_unit'] ?? 'minutes');
$interval_count = (int)($_GET['interval_count'] ?? 8);
$high_type = sanitize($_GET['high_type'] ?? 'distance');
$high_value = (float)($_GET['high_value'] ?? 0);
$high_unit = sanitize($_GET['high_unit'] ?? 'km');
$target_pace = (float)($_GET['target_pace'] ?? 0);
$rec_type = sanitize($_GET['rec_type'] ?? 'distance');
$rec_value = (float)($_GET['rec_value'] ?? 0);
$rec_unit = sanitize($_GET['rec_unit'] ?? 'km');
$rec_mode = sanitize($_GET['rec_mode'] ?? 'jog');
$cool_type = sanitize($_GET['cool_type'] ?? 'none');
$cool_value = (float)($_GET['cool_value'] ?? 0);
$cool_unit = sanitize($_GET['cool_unit'] ?? 'minutes');

$has_warmup = $warmup_type !== 'none' && $warmup_value > 0;
$has_cooldown = $cool_type !== 'none' && $cool_value > 0;

$config = [
    'workout_name' => $workout_name,
    'warmup' => ['type' => $warmup_type, 'value' => $warmup_value, 'unit' => $warmup_unit],
    'interval_count' => $interval_count,
    'high' => ['type' => $high_type, 'value' => $high_value, 'unit' => $high_unit],
    'target_pace' => $target_pace,
    'recovery' => ['type' => $rec_type, 'value' => $rec_value, 'unit' => $rec_unit, 'mode' => $rec_mode],
    'cooldown' => ['type' => $cool_type, 'value' => $cool_value, 'unit' => $cool_unit]
];

$title = 'Interval - Run Tracker';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="relative h-[calc(100vh-57px)] flex flex-col">
    <div id="headerCard" class="absolute top-4 left-0 right-0 z-20 flex justify-center px-4 pointer-events-none">
        <div class="bg-white/90 backdrop-blur-md rounded-2xl px-5 py-3 border border-gray-200/50 inline-flex flex-col items-center gap-0.5 pointer-events-auto">
            <p id="intervalLabel" class="text-xs text-[#9CA3AF] font-semibold tracking-wide">Interval 1 / <?= $interval_count ?></p>
            <p id="intervalStatusText" class="text-lg font-extrabold tracking-wide">HIGH INTENSITY</p>
            <p id="intervalTimer" class="text-3xl font-black text-[#1F2937] tabular-nums">00:00</p>
        </div>
    </div>

    <div id="viewTabs" class="absolute top-[88px] left-0 right-0 z-20 flex justify-center gap-1 pointer-events-none">
        <div class="bg-white/80 backdrop-blur-sm rounded-full border border-gray-200/50 inline-flex overflow-hidden pointer-events-auto">
            <button id="tabMap" class="view-tab px-4 py-1.5 text-xs font-semibold transition-colors bg-[#fc5200] text-white" data-view="map">MAP</button>
            <button id="tabTimer" class="view-tab px-4 py-1.5 text-xs font-semibold transition-colors text-[#9CA3AF]" data-view="timer">TIMER</button>
        </div>
    </div>

    <div id="viewContainer" class="flex-1 relative overflow-hidden">
        <div id="viewMap" class="absolute inset-0 z-0">
            <div id="map" class="w-full h-full"></div>
        </div>

        <div id="viewTimer" class="absolute inset-0 z-10 hidden flex flex-col items-center justify-center bg-gray-50 pt-8 pb-40">
            <div class="circular-timer-wrapper">
                <svg class="circular-timer-svg" viewBox="0 0 240 240">
                    <circle class="circular-timer-bg" cx="120" cy="120" r="108" fill="none" stroke="#E5E7EB" stroke-width="10"/>
                    <circle id="progressRing" class="circular-timer-ring" cx="120" cy="120" r="108" fill="none" stroke="#fc5200" stroke-width="10" stroke-linecap="round" stroke-dasharray="678.58" stroke-dashoffset="0" transform="rotate(-90 120 120)"/>
                </svg>
                <div class="circular-timer-inner">
                    <p id="circleTimerNum" class="text-6xl font-black tabular-nums text-[#1F2937]">00:00</p>
                    <p id="circleTimerStatus" class="text-sm font-extrabold tracking-wide mt-1">HIGH INTENSITY</p>
                    <p class="text-xs text-[#9CA3AF] mt-2">Sisa waktu interval ini</p>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute top-4 left-4 z-20 flex items-center gap-2 bg-white/90 backdrop-blur-sm px-3 py-2 rounded-xl border border-gray-200">
        <div id="gpsStatus" class="w-3 h-3 rounded-full bg-[#EF4444] animate-pulse"></div>
        <span id="gpsStatusText" class="text-xs text-[#EF4444]">Mencari lokasi...</span>
    </div>

    <div class="absolute bottom-0 left-0 right-0 bg-white/95 backdrop-blur-sm rounded-t-3xl p-6 pb-24 md:pb-6 z-10 border-t border-gray-200">
        <div class="max-w-md mx-auto">
            <div class="grid grid-cols-3 gap-4 mb-6 text-center">
                <div>
                    <p class="text-xs text-[#9CA3AF] uppercase tracking-wider">Jarak</p>
                    <p id="trackDistance" class="text-2xl font-bold text-[#fc5200]">0.00</p>
                    <p class="text-xs text-[#9CA3AF]">km</p>
                </div>
                <div>
                    <p class="text-xs text-[#9CA3AF] uppercase tracking-wider">Pace</p>
                    <p id="trackPace" class="text-2xl font-bold text-[#F59E0B]">--:--</p>
                    <p class="text-xs text-[#9CA3AF]">/km</p>
                </div>
                <div>
                    <p class="text-xs text-[#9CA3AF] uppercase tracking-wider">Total Waktu</p>
                    <p id="trackTime" class="text-2xl font-bold text-sky-500">00:00</p>
                    <p class="text-xs text-[#9CA3AF]">menit</p>
                </div>
            </div>
            <div id="startContainer">
                <button id="trackStart" class="start-btn">
                    <span>START INTERVAL</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <div id="controlBar" class="hidden control-bar">
                <button id="trackPause" class="ctrl-btn ctrl-btn-pause">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                    </svg>
                    <span>PAUSE</span>
                </button>
                <button id="trackLock" class="ctrl-btn ctrl-btn-lock">
                    <svg id="lockIcon" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/>
                    </svg>
                </button>
                <button id="trackFinish" class="ctrl-btn ctrl-btn-finish" disabled>
                    FINISH
                </button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/bottom-nav.php'; ?>
<?php include '../includes/footer.php'; ?>
<script>
var INTERVAL_CONFIG = <?= json_encode($config) ?>;
INTERVAL_CONFIG.target_pace_min = Math.floor(<?= $target_pace ?>);
INTERVAL_CONFIG.target_pace_sec = Math.round((<?= $target_pace ?> - INTERVAL_CONFIG.target_pace_min) * 60);
</script>
<script src="../assets/js/interval_tracker.js"></script>