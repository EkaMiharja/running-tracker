<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            redirect('dashboard.php');
        } else {
            $error = 'Username atau password salah';
        }
    }
}

$title = 'Login - Run Tracker';
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
            <p class="text-[#9CA3AF] mt-2">Masuk ke akun Anda</p>
        </div>

        <div class="card">
            <?php if ($error): ?>
                <div class="bg-[#EF4444]/10 border border-[#EF4444]/30 text-[#EF4444] px-4 py-3 rounded-xl mb-4 text-sm"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Username / Email</label>
                    <input type="text" name="username" class="input-field" placeholder="Masukkan username atau email" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Password</label>
                    <input type="password" name="password" class="input-field" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn-primary w-full">Masuk</button>
            </form>

            <p class="text-center mt-6 text-sm text-[#9CA3AF]">
                Belum punya akun?
                <a href="register.php" class="text-sky-500 hover:text-sky-400 font-medium">Daftar</a>
            </p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
