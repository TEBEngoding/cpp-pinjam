<?php
session_start();
require '../../config/db.php';
require 'sidebar.php';

$id   = urldecode($_GET['id']   ?? '');
$nama = urldecode($_GET['nama'] ?? '');

if (!$id) {
    header('Location: barang.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Barang — CPP</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="app-layout">

    <div class="main-content">
        <div style="display:flex; align-items:center; justify-content:center; min-height:60vh;">
            <div class="card" style="max-width:380px; text-align:center; padding:32px 28px;">

                <div style="width:60px; height:60px; border-radius:50%; background:#FEE; display:flex; align-items:center; justify-content:center; font-size:28px; margin:0 auto 16px;">
                    🗑️
                </div>

                <h3 style="font-size:17px; color:var(--brown-dark); margin-bottom:8px;">Hapus Barang?</h3>
                <p style="font-size:13px; color:var(--muted); margin-bottom:24px; line-height:1.6;">
                    Kamu yakin ingin menghapus barang<br>
                    <strong style="color:var(--brown-dark);">"<?= htmlspecialchars($nama) ?>"</strong>?<br>
                    Tindakan ini tidak bisa dibatalkan.
                </p>

                <div style="display:flex; gap:10px;">
                    <a href="barang.php" class="btn-secondary" style="flex:1; text-align:center; padding:10px;">Batal</a>
                    <a href="../../actions/hapus_barang.php?id=<?= urlencode($id) ?>" class="btn-primary" style="flex:1; text-align:center; padding:10px; background:#E24B4A; color:white;">Ya, Hapus</a>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="../../assets/script.js"></script>
</body>
</html>
