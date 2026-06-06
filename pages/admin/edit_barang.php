<?php
session_start();
require '../../config/db.php';
require 'sidebar.php';

$id = urldecode($_GET['id'] ?? '');
if (!$id) {
    header('Location: barang.php'); exit;
}

$stmt = $pdo->prepare("SELECT * FROM barang WHERE id_barang = ?");
$stmt->execute([$id]);
$b = $stmt->fetch();

if (!$b) {
    $_SESSION['error'] = 'Barang tidak ditemukan.';
    header('Location: barang.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang — CPP</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="app-layout">

    <div class="main-content">
        <div class="page-header">
            <h1>Edit Barang</h1>
            <p>Ubah data barang yang sudah ada</p>
        </div>

        <div class="card" style="max-width: 460px;">
            <form method="POST" action="../../actions/simpan_barang.php">
                <!-- id_lama untuk tahu ini mode edit -->
                <input type="hidden" name="id_lama" value="<?= htmlspecialchars($b['id_barang']) ?>">

                <div class="form-group">
                    <label>Kode Barang</label>
                    <input type="text" name="id_barang" value="<?= htmlspecialchars($b['id_barang']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" value="<?= htmlspecialchars($b['nama_barang']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <input type="text" name="kategori" value="<?= htmlspecialchars($b['kategori'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" min="0" value="<?= $b['stok'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Kondisi</label>
                    <select name="kondisi">
                        <option value="Baik"  <?= $b['kondisi'] === 'Baik'  ? 'selected' : '' ?>>Baik</option>
                        <option value="Cukup" <?= $b['kondisi'] === 'Cukup' ? 'selected' : '' ?>>Cukup</option>
                        <option value="Rusak" <?= $b['kondisi'] === 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                    </select>
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <a href="barang.php" class="btn-secondary" style="flex:1; text-align:center; padding:10px;">Batal</a>
                    <button type="submit" class="btn-primary" style="flex:1;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../assets/script.js"></script>
</body>
</html>
