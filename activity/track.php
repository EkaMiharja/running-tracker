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
            <div class="flex justify-center gap-6">
                <button id="trackStart" class="tracking-btn bg-[#fc5200] text-white hover:bg-[#e04700] shadow-lg shadow-[#fc5200]/30">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </button>
                <button id="trackPause" class="tracking-btn bg-[#F59E0B] text-white hover:bg-[#D97706] shadow-lg shadow-[#F59E0B]/30 hidden">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                </button>
                <button id="trackResume" class="tracking-btn bg-[#fc5200] text-white hover:bg-[#e04700] shadow-lg shadow-[#fc5200]/30 hidden">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </button>
                <button id="trackStop" class="tracking-btn bg-[#EF4444] text-white hover:bg-[#DC2626] shadow-lg shadow-[#EF4444]/30 hidden">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/bottom-nav.php'; ?>
<?php include '../includes/footer.php'; ?>
<script src="../assets/js/tracker.js"></script>
