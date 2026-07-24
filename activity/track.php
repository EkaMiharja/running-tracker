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
            <div id="startContainer" class="flex items-center gap-3">
                <button id="menuToggleBtn" class="w-14 h-14 flex-shrink-0 bg-white rounded-2xl border-2 border-gray-200 flex items-center justify-center shadow-lg hover:bg-gray-50 transition-colors text-[#1F2937] text-2xl font-bold">
                    &#9776;
                </button>
                <button id="trackStart" class="start-btn flex-1">
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

<div id="menuOverlay" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity duration-300" style="opacity:0;"></div>

<div id="menuSheet" class="fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-3xl shadow-2xl transform translate-y-full transition-transform duration-300 ease-out">
    <div class="w-10 h-1 bg-gray-300 rounded-full mx-auto mt-3 mb-2"></div>
    <div class="px-6 pb-8 pt-2">
        <h2 class="text-lg font-bold mb-4 text-[#1F2937]">Mode Lari</h2>
        <a href="track.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 transition-colors border border-gray-200 mb-3">
            <div class="w-12 h-12 rounded-full bg-[#fc5200]/10 flex items-center justify-center">
                <svg class="w-6 h-6 text-[#fc5200]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <p class="font-semibold text-[#1F2937]">Free Run</p>
                <p class="text-sm text-[#9CA3AF]">Lari bebas tanpa aturan interval</p>
            </div>
        </a>
        <a href="interval_builder.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 transition-colors border border-gray-200">
            <div class="w-12 h-12 rounded-full bg-[#10B981]/10 flex items-center justify-center">
                <svg class="w-6 h-6 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <p class="font-semibold text-[#1F2937]">Interval Training</p>
                <p class="text-sm text-[#9CA3AF]">Latihan interval dengan HIGH & Recovery</p>
            </div>
        </a>
    </div>
</div>

<?php include '../includes/bottom-nav.php'; ?>
<?php include '../includes/footer.php'; ?>
<script src="../assets/js/tracker.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const menuBtn = document.getElementById('menuToggleBtn');
    const menuOverlay = document.getElementById('menuOverlay');
    const menuSheet = document.getElementById('menuSheet');
    let menuOpen = false;

    function openMenu() {
        menuOpen = true;
        menuOverlay.classList.remove('hidden');
        setTimeout(function () {
            menuOverlay.style.opacity = '1';
            menuSheet.classList.remove('translate-y-full');
        }, 10);
    }

    function closeMenu() {
        menuOpen = false;
        menuOverlay.style.opacity = '0';
        menuSheet.classList.add('translate-y-full');
        setTimeout(function () {
            menuOverlay.classList.add('hidden');
        }, 300);
    }

    menuBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        openMenu();
    });

    menuOverlay.addEventListener('click', closeMenu);
});
</script>