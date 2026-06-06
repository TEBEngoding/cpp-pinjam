<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php'); exit;
}

$id = trim($_GET['id'] ?? '');
if (!$id) {
    header('Location: ../pages/admin/barang.php'); exit;
}

try {
    $pdo->prepare("DELETE FROM barang WHERE id_barang = ?")->execute([$id]);
    $_SESSION['msg'] = 'Barang berhasil dihapus.';
} catch (Exception $e) {
    $_SESSION['error'] = 'Gagal menghapus: mungkin barang sedang dipinjam.';
}

header('Location: ../pages/admin/barang.php');
exit;
?>
