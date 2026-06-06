<?php
session_start();
require 'config/db.php';

// Kalau sudah login, redirect langsung
if (isset($_SESSION['id_user'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: pages/admin/dashboard.php');
    } else {
        header('Location: pages/user/dashboard.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id_user']   = $user['id_user'];
            $_SESSION['nama_user'] = $user['nama_user'];
            $_SESSION['role']      = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: pages/admin/dashboard.php');
            } else {
                header('Location: pages/user/dashboard.php');
            }
            exit;
        } else {
            $error = 'Email atau password salah!';
        }
    } else {
        $error = 'Email dan password wajib diisi.';
    }
}

$logout_msg = isset($_GET['logout']) ? 'Kamu berhasil logout.' : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CP Pinjam Pinjam</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-page">

<div class="login-card">
    <div class="login-logo">📦</div>
    <h2><span>CP </span>Pinjam Pinjam</h2>
    <p class="subtitle">Sistem Peminjaman ADK</p>

    <?php if ($logout_msg): ?>
        <div class="alert-success"><?= htmlspecialchars($logout_msg) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="email@gmail.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-primary" style="width:100%; margin-top:6px;">Login</button>
    </form>
</div>

<script src="assets/script.js"></script>
</body>
</html>
