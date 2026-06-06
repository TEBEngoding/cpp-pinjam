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

try {
    $pdo->beginTransaction();

    // Cek status peminjaman saat ini
    $pinjam = $pdo->prepare("SELECT status FROM peminjaman WHERE id_pinjam = ?");
    $pinjam->execute([$id_pinjam]);
    $pinjam = $pinjam->fetch();

    if (!$pinjam || strtolower($pinjam['status']) !== 'pending') {
        $_SESSION['error'] = 'Peminjaman tidak ditemukan atau sudah diproses.';
        $pdo->rollBack();
        header('Location: ../pages/admin/approval.php'); exit;
    }

    // Ambil id_barang dari detail_pinjam
    $detail = $pdo->prepare("SELECT id_barang FROM detail_pinjam WHERE id_pinjam = ?");
    $detail->execute([$id_pinjam]);
    $detail = $detail->fetch();

    // Cek stok barang
    $barang = $pdo->prepare("SELECT stok FROM barang WHERE id_barang = ?");
    $barang->execute([$detail['id_barang']]);
    $barang = $barang->fetch();

    if ($barang['stok'] <= 0) {
        $_SESSION['error'] = 'Stok barang sudah habis, tidak bisa diapprove.';
        $pdo->rollBack();
        header('Location: ../pages/admin/approval.php'); exit;
    }

    // Update status peminjaman → Approved
    $pdo->prepare("UPDATE peminjaman SET status = 'Approved' WHERE id_pinjam = ?")
        ->execute([$id_pinjam]);

    // Kurangi stok barang sebanyak 1
    $pdo->prepare("UPDATE barang SET stok = stok - 1 WHERE id_barang = ?")
        ->execute([$detail['id_barang']]);

    $pdo->commit();
    $_SESSION['msg'] = 'Peminjaman berhasil disetujui dan stok barang dikurangi.';
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

header('Location: ../pages/admin/approval.php');
exit;
?>
