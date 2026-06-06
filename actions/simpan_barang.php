<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php'); exit;
}

$id_lama     = trim($_POST['id_lama']     ?? '');
$id_barang   = trim($_POST['id_barang']   ?? '');
$nama_barang = trim($_POST['nama_barang'] ?? '');
$kategori    = trim($_POST['kategori']    ?? '');
$stok        = intval($_POST['stok']      ?? 0);
$kondisi     = trim($_POST['kondisi']     ?? 'Baik');

if (!$id_barang || !$nama_barang) {
    $_SESSION['error'] = 'Kode dan nama barang wajib diisi.';
    header('Location: ../pages/admin/barang.php'); exit;
}

try {
    if ($id_lama) {
        // MODE EDIT
        $stmt = $pdo->prepare("
            UPDATE barang SET id_barang=?, nama_barang=?, kategori=?, stok=?, kondisi=?
            WHERE id_barang=?
        ");
        $stmt->execute([$id_barang, $nama_barang, $kategori, $stok, $kondisi, $id_lama]);
        $_SESSION['msg'] = 'Barang berhasil diperbarui!';
    } else {
        // MODE TAMBAH
        $cek = $pdo->prepare("SELECT id_barang FROM barang WHERE id_barang = ?");
        $cek->execute([$id_barang]);
        if ($cek->fetch()) {
            $_SESSION['error'] = 'Kode barang sudah digunakan.';
            header('Location: ../pages/admin/barang.php'); exit;
        }
        $stmt = $pdo->prepare("
            INSERT INTO barang (id_barang, nama_barang, kategori, stok, kondisi)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id_barang, $nama_barang, $kategori, $stok, $kondisi]);
        $_SESSION['msg'] = 'Barang berhasil ditambahkan!';
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Gagal menyimpan: ' . $e->getMessage();
}

header('Location: ../pages/admin/barang.php');
exit;
?>
