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

    // Cek status harus Approved
    $pinjam = $pdo->prepare("SELECT status FROM peminjaman WHERE id_pinjam = ?");
    $pinjam->execute([$id_pinjam]);
    $pinjam = $pinjam->fetch();

    if (!$pinjam || strtolower($pinjam['status']) !== 'approved') {
        $_SESSION['error'] = 'Peminjaman tidak ditemukan atau statusnya bukan Approved.';
        $pdo->rollBack();
        header('Location: ../pages/admin/approval.php'); exit;
    }

    // Ambil id_barang
    $detail = $pdo->prepare("SELECT id_barang FROM detail_pinjam WHERE id_pinjam = ?");
    $detail->execute([$id_pinjam]);
    $detail = $detail->fetch();

    // Kembalikan stok barang
    $pdo->prepare("UPDATE barang SET stok = stok + 1 WHERE id_barang = ?")
        ->execute([$detail['id_barang']]);

    // Hapus dari detail_pinjam dan peminjaman
    $pdo->prepare("DELETE FROM detail_pinjam WHERE id_pinjam = ?")->execute([$id_pinjam]);
    $pdo->prepare("DELETE FROM peminjaman WHERE id_pinjam = ?")->execute([$id_pinjam]);

    $pdo->commit();
    $_SESSION['msg'] = 'Barang berhasil dikonfirmasi kembali dan stok sudah dipulihkan.';
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

header('Location: ../pages/admin/approval.php');
exit;
