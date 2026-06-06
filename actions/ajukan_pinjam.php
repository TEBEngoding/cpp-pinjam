<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/user/peminjaman.php'); exit;
}

$id_user        = $_SESSION['id_user'];
$id_barang      = trim($_POST['id_barang']      ?? '');
$tanggal_pinjam = trim($_POST['tanggal_pinjam'] ?? '');
$tanggal_kembali= trim($_POST['tanggal_kembali']?? '');
$keperluan      = trim($_POST['keperluan']       ?? '');

// Validasi
if (!$id_barang || !$tanggal_pinjam || !$tanggal_kembali || !$keperluan) {
    $_SESSION['error'] = 'Semua kolom wajib diisi.';
    header('Location: ../pages/user/peminjaman.php'); exit;
}

if ($tanggal_kembali < $tanggal_pinjam) {
    $_SESSION['error'] = 'Tanggal kembali tidak boleh sebelum tanggal pinjam.';
    header('Location: ../pages/user/peminjaman.php'); exit;
}

// Cek stok barang
$barang = $pdo->prepare("SELECT * FROM barang WHERE id_barang = ?");
$barang->execute([$id_barang]);
$barang = $barang->fetch();

if (!$barang || $barang['stok'] <= 0) {
    $_SESSION['error'] = 'Barang tidak tersedia atau stok habis.';
    header('Location: ../pages/user/peminjaman.php'); exit;
}

try {
    $pdo->beginTransaction();

    // Insert ke tabel peminjaman
    $stmt = $pdo->prepare("
        INSERT INTO peminjaman (id_user, tanggal_pinjam, tanggal_kembali, status)
        VALUES (?, ?, ?, 'Pending')
    ");
    $stmt->execute([$id_user, $tanggal_pinjam, $tanggal_kembali]);
    $id_pinjam = $pdo->lastInsertId();

    // Insert ke detail_pinjam
    $stmt2 = $pdo->prepare("
        INSERT INTO detail_pinjam (id_pinjam, id_barang, keperluan)
        VALUES (?, ?, ?)
    ");
    $stmt2->execute([$id_pinjam, $id_barang, $keperluan]);

    $pdo->commit();
    $_SESSION['msg'] = 'Peminjaman berhasil diajukan! Tunggu konfirmasi admin ya 😊';
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

header('Location: ../pages/user/peminjaman.php');
exit;
?>
