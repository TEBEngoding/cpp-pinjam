<?php
session_start();
require '../../config/db.php';
require 'sidebar.php';

// Statistik
$total_barang  = $pdo->query("SELECT COUNT(*) FROM barang")->fetchColumn();
$total_dipinjam = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'Approved'")->fetchColumn();
$total_pending  = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'Pending'")->fetchColumn();
$total_user     = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();

// Request terbaru
$requests = $pdo->query("
    SELECT p.id_pinjam, u.nama_user, b.nama_barang, p.status, p.tanggal_pinjam
    FROM peminjaman p
    JOIN users u ON p.id_user = u.id_user
    JOIN detail_pinjam dp ON p.id_pinjam = dp.id_pinjam
    JOIN barang b ON dp.id_barang = b.id_barang
    ORDER BY p.id_pinjam DESC
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — CPP</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="app-layout">
    <?php /* sidebar sudah di-include via sidebar.php */ ?>

    <div class="main-content">
        <div class="page-header">
            <h1>Dashboard Admin</h1>
            <p>Ringkasan sistem inventaris CPP</p>
        </div>

        <!-- Stat Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-val"><?= $total_barang ?></div>
                <div class="stat-label">Total Barang</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🔄</div>
                <div class="stat-val"><?= $total_dipinjam ?></div>
                <div class="stat-label">Dipinjam</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-val"><?= $total_pending ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-val"><?= $total_user ?></div>
                <div class="stat-label">User Aktif</div>
            </div>
        </div>

        <!-- Request Terbaru -->
        <div class="card">
            <h4>Request Terbaru</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Barang</th>
                            <th>Tanggal Pinjam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr><td colspan="4" style="text-align:center; color:var(--muted);">Belum ada request</td></tr>
                        <?php else: ?>
                            <?php foreach ($requests as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['nama_user']) ?></td>
                                    <td><?= htmlspecialchars($r['nama_barang']) ?></td>
                                    <td><?= date('d M Y', strtotime($r['tanggal_pinjam'])) ?></td>
                                    <td>
                                        <?php
                                        $s = $r['status'];
                                        $cls = match(strtolower($s)) {
                                            'pending'  => 'badge-pending',
                                            'approved' => 'badge-approved',
                                            'rejected' => 'badge-rejected',
                                            'selesai'  => 'badge-selesai',
                                            default    => 'badge-pending'
                                        };
                                        ?>
                                        <span class="badge <?= $cls ?>"><?= $s ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="../../assets/script.js"></script>
</body>
</html>
