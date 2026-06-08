<?php
session_start();
require '../../config/db.php';
require 'sidebar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang — CPP</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="app-layout">

    <div class="main-content">
        <div class="page-header">
            <h1>Tambah Barang</h1>
            <p>Isi data barang baru</p>
        </div>

        <div class="card" style="max-width: 460px;">
            <form method="POST" action="../../actions/simpan_barang.php">
                <input type="hidden" name="id_lama" value="">

                <div class="form-group">
                    <label>Kode Barang</label>
                    <input type="text" name="id_barang" placeholder="Contoh: A001" required>
                </div>
                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" placeholder="Nama barang" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <input type="text" name="kategori" placeholder="ATK, Elektronik, Furniture, dll">
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" min="0" placeholder="0" required>
                </div>
                <div class="form-group">
                    <label>Kondisi</label>
                    <select name="kondisi">
                        <option value="Sangat Baik">Sangat Baik</option>
                        <option value="Baik">Baik</option>
                        <option value="Kurang Baik">Kurang Baik</option>
                        <option value="Tidak Baik">Tidak Baik</option>
                    </select>
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <a href="barang.php" class="btn-secondary" style="flex:1; text-align:center; padding:10px;">Batal</a>
                    <button type="submit" class="btn-primary" style="flex:1;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../assets/script.js"></script>
</body>
</html>