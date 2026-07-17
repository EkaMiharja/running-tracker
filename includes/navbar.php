<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);
$user = getCurrentUser();
$in_subdir = strpos($_SERVER['SCRIPT_NAME'], '/activity/') !== false;
?>
<nav class="bg-white border-b border-gray-200 px-4 py-3">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <a href="<?= $in_subdir ? '../dashboard.php' : 'dashboard.php' ?>" class="flex items-center gap-2">
            <span class="text-[#fc5200] text-2xl font-bold">Run</span>
            <span class="text-[#1F2937] text-2xl font-bold">Tracker</span>
        </a>
        <div class="hidden md:flex items-center gap-1">
            <a href="<?= $in_subdir ? '../dashboard.php' : 'dashboard.php' ?>" class="px-3 py-2 text-sm rounded-lg transition-colors <?= $current_page === 'dashboard.php' ? 'text-[#fc5200] bg-[#fc5200]/10' : 'text-[#9CA3AF] hover:text-[#fc5200] hover:bg-gray-50' ?>">Beranda</a>
            <a href="<?= $in_subdir ? 'history.php' : 'activity/history.php' ?>" class="px-3 py-2 text-sm rounded-lg transition-colors <?= $current_page === 'history.php' ? 'text-[#fc5200] bg-[#fc5200]/10' : 'text-[#9CA3AF] hover:text-[#fc5200] hover:bg-gray-50' ?>">Riwayat</a>
            <a href="<?= $in_subdir ? 'track.php' : 'activity/track.php' ?>" class="px-3 py-2 text-sm rounded-lg transition-colors <?= $current_page === 'track.php' ? 'text-[#fc5200] bg-[#fc5200]/10' : 'text-[#9CA3AF] hover:text-[#fc5200] hover:bg-gray-50' ?>">Lacak</a>
            <a href="<?= $in_subdir ? 'create.php' : 'activity/create.php' ?>" class="px-3 py-2 text-sm rounded-lg transition-colors <?= $current_page === 'create.php' ? 'text-[#fc5200] bg-[#fc5200]/10' : 'text-[#9CA3AF] hover:text-[#fc5200] hover:bg-gray-50' ?>">Catat</a>
            <a href="<?= $in_subdir ? '../profile.php' : 'profile.php' ?>" class="px-3 py-2 text-sm rounded-lg transition-colors <?= $current_page === 'profile.php' ? 'text-[#fc5200] bg-[#fc5200]/10' : 'text-[#9CA3AF] hover:text-[#fc5200] hover:bg-gray-50' ?>">Profil</a>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-[#9CA3AF] text-sm hidden sm:block"><?= htmlspecialchars($user['username'] ?? '') ?></span>
            <a href="<?= $in_subdir ? '../logout.php' : 'logout.php' ?>" class="text-sm text-[#9CA3AF] hover:text-[#EF4444] transition-colors">Logout</a>
        </div>
    </div>
</nav>
