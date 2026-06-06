<?php
session_start();
require '../../config/db.php';
require 'sidebar.php';

$id_user = $_SESSION['id_user'];
$msg     = $_SESSION['msg']   ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);

$user = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
$user->execute([$id_user]);
$user = $user->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — CPP</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="app-layout">

    <div class="main-content">
        <div class="page-header">
            <h1>Profil</h1>
            <p>Lihat informasi akunmu</p>
        </div>

        <?php if ($msg):   ?><div class="alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="profile-card">
            <div class="avatar-circle">
                <?= strtoupper(substr($user['nama_user'], 0, 2)) ?>
            </div>
            <div style="font-size:16px; font-weight:700; color:var(--brown-dark);">
                <?= htmlspecialchars($user['nama_user']) ?>
            </div>
            <div style="font-size:12px; color:var(--muted); margin-bottom:20px;">Anggota Aktif</div>

            <form method="POST" action="../../actions/update_profil.php" style="text-align:left;">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama_user" value="<?= htmlspecialchars($user['nama_user']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Password Baru <small style="color:var(--muted)">(kosongkan jika tidak diubah)</small></label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="pass-input" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('pass-input')"
                                style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--muted);">
                            👁️
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="width:100%; margin-top:6px;">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
<script src="../../assets/script.js"></script>
</body>
</html>
