<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
requireLogin();

$user = getCurrentUser();
$message = '';
$error = '';

$stmt = $pdo->prepare("SELECT created_at FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$user_meta = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $email = sanitize($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $user['id']]);
            $message = 'Profil berhasil diperbarui';
            $user['email'] = $email;
        }
    }

    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
            $error = 'Semua field password harus diisi';
        } elseif (strlen($new) < 6) {
            $error = 'Password baru minimal 6 karakter';
        } elseif ($new !== $confirm) {
            $error = 'Konfirmasi password baru tidak cocok';
        } else {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $row = $stmt->fetch();

            if (password_verify($current, $row['password'])) {
                $hashed = password_hash($new, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed, $user['id']]);
                $message = 'Password berhasil diubah';
            } else {
                $error = 'Password saat ini salah';
            }
        }
    }
}

$title = 'Profil - Run Tracker';
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="max-w-2xl mx-auto px-4 py-6 pb-24 md:pb-6">
    <h1 class="text-2xl font-bold mb-6">Profil</h1>

    <?php if ($message): ?>
        <div class="bg-[#fc5200]/10 border border-[#fc5200]/30 text-[#fc5200] px-4 py-3 rounded-xl mb-4 text-sm"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-[#EF4444]/10 border border-[#EF4444]/30 text-[#EF4444] px-4 py-3 rounded-xl mb-4 text-sm"><?= $error ?></div>
    <?php endif; ?>

    <div class="card mb-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-full bg-[#fc5200]/20 flex items-center justify-center">
                <span class="text-2xl font-bold text-[#fc5200]"><?= strtoupper(substr($user['username'], 0, 1)) ?></span>
            </div>
            <div>
                <h2 class="text-xl font-bold"><?= htmlspecialchars($user['username']) ?></h2>
                <p class="text-sm text-[#9CA3AF]">Bergabung sejak <?= formatDate($user_meta['created_at'] ?? date('Y-m-d')) ?></p>
            </div>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Username</label>
                <input type="text" value="<?= htmlspecialchars($user['username']) ?>" class="input-field opacity-60" disabled>
                <p class="text-xs text-[#6B7280] mt-1">Username tidak dapat diubah</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Email</label>
                <input type="email" name="email" class="input-field" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <button type="submit" name="update_profile" class="btn-primary w-full">Simpan Profil</button>
        </form>
    </div>

    <div class="card">
        <h2 class="text-lg font-semibold mb-4">Ganti Password</h2>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Password Saat Ini</label>
                <input type="password" name="current_password" class="input-field" placeholder="Masukkan password saat ini" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Password Baru</label>
                <input type="password" name="new_password" class="input-field" placeholder="Minimal 6 karakter" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" class="input-field" placeholder="Ulangi password baru" required>
            </div>
            <button type="submit" name="change_password" class="btn-primary w-full">Ubah Password</button>
        </form>
    </div>
</div>

<?php include 'includes/bottom-nav.php'; ?>
<?php include 'includes/footer.php'; ?>
