<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: ../index.php'); exit;
}

$id_user    = $_SESSION['id_user'];
$nama_user  = trim($_POST['nama_user'] ?? '');
$email      = trim($_POST['email']     ?? '');
$password   = $_POST['password']       ?? '';

if (!$nama_user || !$email) {
    $_SESSION['error'] = 'Nama dan email wajib diisi.';
    header('Location: ../pages/user/profil.php'); exit;
}

try {
    if ($password) {
        // Update dengan password baru
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET nama_user=?, email=?, password=? WHERE id_user=?")
            ->execute([$nama_user, $email, $hash, $id_user]);
    } else {
        // Update tanpa password
        $pdo->prepare("UPDATE users SET nama_user=?, email=? WHERE id_user=?")
            ->execute([$nama_user, $email, $id_user]);
    }

    $_SESSION['nama_user'] = $nama_user;
    $_SESSION['msg'] = 'Profil berhasil diperbarui!';
} catch (Exception $e) {
    $_SESSION['error'] = 'Gagal memperbarui profil: ' . $e->getMessage();
}

header('Location: ../pages/user/profil.php');
exit;
?>
