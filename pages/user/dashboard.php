<?php
session_start();
require '../../config/db.php';
require 'sidebar.php';

$id_user = $_SESSION['id_user'];
$today   = date('Y-m-d');

$total_barang   = $pdo->query("SELECT COUNT(*) FROM barang WHERE stok > 0")->fetchColumn();

$pinjaman_aktif = $pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE id_user = ? AND status = 'Approved'");
$pinjaman_aktif->execute([$id_user]);
$pinjaman_aktif = $pinjaman_aktif->fetchColumn();

// Cari pinjaman yang sudah lewat tanggal kembali tapi belum dikonfirmasi
$terlambat = $pdo->prepare("
    SELECT p.id_pinjam, b.nama_barang, p.tanggal_kembali,
           DATEDIFF(CURDATE(), p.tanggal_kembali) AS hari_terlambat
    FROM peminjaman p
    JOIN detail_pinjam dp ON p.id_pinjam = dp.id_pinjam
    JOIN barang b ON dp.id_barang = b.id_barang
    WHERE p.id_user = ? AND p.status = 'Approved' AND p.tanggal_kembali < ?
    ORDER BY p.tanggal_kembali ASC
");
$terlambat->execute([$id_user, $today]);
$terlambat = $terlambat->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — CPP</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .warning-banner {
            background: #FEF2F2;
            border: 1.5px solid #FECACA;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 16px;
        }
        .warning-banner h4 {
            color: #B91C1C;
            font-size: 14px;
            margin: 0 0 8px;
        }
        .warning-banner ul {
            margin: 0; padding-left: 18px;
        }
        .warning-banner ul li {
            color: #7F1D1D;
            font-size: 13px;
            margin-bottom: 4px;
            line-height: 1.5;
        }
        .warning-banner .warn-footer {
            margin-top: 10px;
            font-size: 12px;
            color: #B91C1C;
        }
    </style>
</head>
<body>
<div class="app-layout">

    <div class="main-content">
        <div class="page-header">
            <h1>Dashboard User</h1>
            <p>Selamat Datang Kembali di CPP</p>
        </div>

        <!-- Peringatan terlambat -->
        <?php if (!empty($terlambat)): ?>
            <div class="warning-banner">
                <h4>⚠️ Perhatian! Kamu belum mengembalikan barang berikut:</h4>
                <ul>
                    <?php foreach ($terlambat as $t): ?>
                        <li>
                            <strong><?= htmlspecialchars($t['nama_barang']) ?></strong>
                            — seharusnya dikembalikan pada
                            <strong><?= date('d M Y', strtotime($t['tanggal_kembali'])) ?></strong>
                            (<?= $t['hari_terlambat'] ?> hari yang lalu)
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="warn-footer">Segera kembalikan barang dan konfirmasi ke admin ya! 🙏</p>
            </div>
        <?php endif; ?>

        <div class="welcome-banner">
            <h3>Halo, <?= htmlspecialchars($_SESSION['nama_user']) ?>!</h3>
            <p>
                <?php if ($pinjaman_aktif > 0): ?>
                    Kamu punya <?= $pinjaman_aktif ?> pinjaman aktif. Jangan lupa kembalikan tepat waktu ya!
                <?php else: ?>
                    Kamu belum punya pinjaman aktif. Mau pinjam barang? 😊
                <?php endif; ?>
            </p>
        </div>

        <div class="stats-grid-2">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-val"><?= $total_barang ?></div>
                <div class="stat-label">Total Barang</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-val"><?= $pinjaman_aktif ?></div>
                <div class="stat-label">Pinjaman Aktif</div>
            </div>
        </div>
    </div>
</div>
<script src="../../assets/script.js"></script>
</body>
</html>
