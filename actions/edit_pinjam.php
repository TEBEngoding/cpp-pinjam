<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php'); exit;
}

$id_user   = $_SESSION['id_user'];
$id_pinjam = intval($_POST['id_pinjam'] ?? $_GET['id'] ?? 0);
$action    = $_POST['action'] ?? $_GET['action'] ?? '';

if (!$id_pinjam) {
    header('Location: ../pages/user/peminjaman.php'); exit;
}

// Pastikan peminjaman milik user ini dan masih Pending
$stmt = $pdo->prepare("
    SELECT p.*, dp.id_barang, dp.keperluan
    FROM peminjaman p
    JOIN detail_pinjam dp ON p.id_pinjam = dp.id_pinjam
    WHERE p.id_pinjam = ? AND p.id_user = ? AND p.status = 'Pending'
");
$stmt->execute([$id_pinjam, $id_user]);
$pinjam = $stmt->fetch();

if (!$pinjam) {
    $_SESSION['error'] = 'Permintaan tidak ditemukan atau sudah diproses.';
    header('Location: ../pages/user/peminjaman.php'); exit;
}

// HAPUS permintaan
if ($action === 'hapus') {
    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM detail_pinjam WHERE id_pinjam = ?")->execute([$id_pinjam]);
        $pdo->prepare("DELETE FROM peminjaman WHERE id_pinjam = ?")->execute([$id_pinjam]);
        $pdo->commit();
        $_SESSION['msg'] = 'Permintaan peminjaman berhasil dibatalkan.';
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Gagal membatalkan: ' . $e->getMessage();
    }
    header('Location: ../pages/user/peminjaman.php'); exit;
}

// EDIT permintaan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit') {
    $tanggal_pinjam  = trim($_POST['tanggal_pinjam']  ?? '');
    $tanggal_kembali = trim($_POST['tanggal_kembali'] ?? '');
    $keperluan       = trim($_POST['keperluan']        ?? '');

    if (!$tanggal_pinjam || !$tanggal_kembali || !$keperluan) {
        $_SESSION['error'] = 'Semua kolom wajib diisi.';
        header('Location: ../pages/user/peminjaman.php'); exit;
    }

    if ($tanggal_kembali < $tanggal_pinjam) {
        $_SESSION['error'] = 'Tanggal kembali tidak boleh sebelum tanggal pinjam.';
        header('Location: ../pages/user/peminjaman.php'); exit;
    }

    try {
        $pdo->prepare("UPDATE peminjaman SET tanggal_pinjam=?, tanggal_kembali=? WHERE id_pinjam=?")
            ->execute([$tanggal_pinjam, $tanggal_kembali, $id_pinjam]);
        $pdo->prepare("UPDATE detail_pinjam SET keperluan=? WHERE id_pinjam=?")
            ->execute([$keperluan, $id_pinjam]);
        $_SESSION['msg'] = 'Permintaan peminjaman berhasil diperbarui!';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Gagal memperbarui: ' . $e->getMessage();
    }
    header('Location: ../pages/user/peminjaman.php'); exit;
}

header('Location: ../pages/user/peminjaman.php');
exit;
