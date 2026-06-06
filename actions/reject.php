<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php'); exit;
}

$id_pinjam = intval($_GET['id'] ?? 0);
if (!$id_pinjam) {
    header('Location: ../pages/admin/approval.php'); exit;
}

$stmt = $pdo->prepare("UPDATE peminjaman SET status = 'Rejected' WHERE id_pinjam = ? AND status = 'Pending'");
$stmt->execute([$id_pinjam]);

if ($stmt->rowCount() > 0) {
    $_SESSION['msg'] = 'Peminjaman berhasil ditolak.';
} else {
    $_SESSION['error'] = 'Peminjaman tidak ditemukan atau sudah diproses.';
}

header('Location: ../pages/admin/approval.php');
exit;
?>
