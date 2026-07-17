<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Username atau email sudah terdaftar';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed]);
            $success = 'Registrasi berhasil! Silakan login.';
        }
    }
}

$title = 'Daftar - Run Tracker';
$hide_nav = true;
?>
<?php include 'includes/header.php'; ?>
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold">
                <span class="text-[#fc5200]">Run</span>
                <span class="text-[#1F2937]">Tracker</span>
            </h1>
            <p class="text-[#9CA3AF] mt-2">Buat akun untuk mulai melacak lari</p>
        </div>

        <div class="card">
            <?php if ($error): ?>
                <div class="bg-[#EF4444]/10 border border-[#EF4444]/30 text-[#EF4444] px-4 py-3 rounded-xl mb-4 text-sm"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="bg-[#fc5200]/10 border border-[#fc5200]/30 text-[#fc5200] px-4 py-3 rounded-xl mb-4 text-sm"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Username</label>
                    <input type="text" name="username" class="input-field" placeholder="Masukkan username" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Email</label>
                    <input type="email" name="email" class="input-field" placeholder="Masukkan email" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Password</label>
                    <input type="password" name="password" class="input-field" placeholder="Minimal 6 karakter" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" class="input-field" placeholder="Ulangi password" required>
                </div>
                <button type="submit" class="btn-primary w-full">Daftar</button>
            </form>

            <p class="text-center mt-6 text-sm text-[#9CA3AF]">
                Sudah punya akun?
                <a href="login.php" class="text-sky-500 hover:text-sky-400 font-medium">Login</a>
            </p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
