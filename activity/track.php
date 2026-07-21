<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user = getCurrentUser();

$title = 'Lacak Lari - Run Tracker';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="relative h-[calc(100vh-57px)] flex flex-col">
    <div id="map" class="flex-1 z-0"></div>

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
                    <p class="text-xs text-[#9CA3AF] uppercase tracking-wider">Waktu</p>
                    <p id="trackTime" class="text-2xl font-bold text-sky-500">00:00</p>
                    <p class="text-xs text-[#9CA3AF]">menit</p>
                </div>
                <div>
                    <p class="text-xs text-[#9CA3AF] uppercase tracking-wider">Pace</p>
                    <p id="trackPace" class="text-2xl font-bold text-[#F59E0B]">--:--</p>
                    <p class="text-xs text-[#9CA3AF]">/km</p>
                </div>
            </div>
            <div id="startContainer">
                <button id="trackStart" class="start-btn">
                    <span>START RUNNING</span>
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
<script src="../assets/js/tracker.js"></script>
