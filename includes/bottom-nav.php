<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);
$in_subdir = strpos($_SERVER['SCRIPT_NAME'], '/activity/') !== false;
?>
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 md:hidden">
    <div class="flex items-start justify-center h-16">
        <a href="<?= $in_subdir ? '../dashboard.php' : 'dashboard.php' ?>" class="flex flex-col items-center gap-0.5 pt-2 flex-1 h-full <?= $current_page === 'dashboard.php' ? 'text-[#fc5200]' : 'text-[#9CA3AF]' ?>">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px]">Beranda</span>
        </a>
        <a href="<?= $in_subdir ? 'history.php' : 'activity/history.php' ?>" class="flex flex-col items-center gap-0.5 pt-2 flex-1 h-full <?= $current_page === 'history.php' ? 'text-[#fc5200]' : 'text-[#9CA3AF]' ?>">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-[10px]">Riwayat</span>
        </a>
        <a href="<?= $in_subdir ? 'track.php' : 'activity/track.php' ?>" class="flex flex-col items-center gap-0.5 pt-2 flex-1 h-full">
            <div class="bg-[#fc5200] rounded-full p-3 shadow-lg shadow-[#fc5200]/30 -mt-7">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="text-[10px] text-[#fc5200] font-semibold">Lacak</span>
        </a>
        <a href="<?= $in_subdir ? 'create.php' : 'activity/create.php' ?>" class="flex flex-col items-center gap-0.5 pt-2 flex-1 h-full <?= $current_page === 'create.php' ? 'text-[#fc5200]' : 'text-[#9CA3AF]' ?>">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span class="text-[10px]">Catat</span>
        </a>
        <a href="<?= $in_subdir ? '../profile.php' : 'profile.php' ?>" class="flex flex-col items-center gap-0.5 pt-2 flex-1 h-full <?= $current_page === 'profile.php' ? 'text-[#fc5200]' : 'text-[#9CA3AF]' ?>">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[10px]">Profil</span>
        </a>
    </div>
</div>
