<?php
session_start();
require '../../config/db.php';
require 'sidebar.php';

$msg   = $_SESSION['msg']   ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);

$tab = $_GET['tab'] ?? 'pending';

// Pending requests
$pending = $pdo->query("
    SELECT p.id_pinjam, u.nama_user, u.email,
           b.nama_barang, b.id_barang,
           p.tanggal_pinjam, p.tanggal_kembali, p.status,
           dp.keperluan
    FROM peminjaman p
    JOIN users u          ON p.id_user    = u.id_user
    JOIN detail_pinjam dp ON p.id_pinjam  = dp.id_pinjam
    JOIN barang b         ON dp.id_barang = b.id_barang
    WHERE p.status = 'Pending'
    ORDER BY p.id_pinjam DESC
")->fetchAll();

// Approved (sedang dipinjam) — untuk konfirmasi kembali
$approved = $pdo->query("
    SELECT p.id_pinjam, u.nama_user, u.email,
           b.nama_barang, b.id_barang,
           p.tanggal_pinjam, p.tanggal_kembali, p.status,
           dp.keperluan
    FROM peminjaman p
    JOIN users u          ON p.id_user    = u.id_user
    JOIN detail_pinjam dp ON p.id_pinjam  = dp.id_pinjam
    JOIN barang b         ON dp.id_barang = b.id_barang
    WHERE p.status = 'Approved'
    ORDER BY p.tanggal_kembali ASC
")->fetchAll();

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval — CPP</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .tab-bar { display:flex; gap:8px; margin-bottom:20px; }
        .tab-btn {
            padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 600;
            cursor: pointer; border: 2px solid transparent; text-decoration: none;
            color: var(--muted); background: var(--surface);
        }
        .tab-btn.active { background: var(--yellow-200); color: var(--brown-text); }
        .tab-btn .badge-count {
            display:inline-block; background:var(--danger); color:white;
            border-radius:999px; padding:1px 7px; font-size:11px; margin-left:6px;
        }
        .tab-btn.active .badge-count { background: rgba(255,255,255,0.3); }
        .terlambat-tag {
            display:inline-block; background:#FEE2E2; color:#B91C1C;
            border-radius:6px; padding:2px 8px; font-size:11px; font-weight:700; margin-left:6px;
        }
    </style>
</head>
<body>
<div class="app-layout">

    <div class="main-content">
        <div class="page-header">
            <h1>Approval Admin</h1>
            <p>Kelola permintaan dan konfirmasi pengembalian</p>
        </div>

        <?php if ($msg):   ?><div class="alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <!-- Tab Bar -->
        <div class="tab-bar">
            <a href="?tab=pending" class="tab-btn <?= $tab === 'pending' ? 'active' : '' ?>">
                📥 Permintaan Masuk
                <?php if (count($pending) > 0): ?>
                    <span class="badge-count"><?= count($pending) ?></span>
                <?php endif; ?>
            </a>
            <a href="?tab=kembali" class="tab-btn <?= $tab === 'kembali' ? 'active' : '' ?>">
                🔄 Konfirmasi Kembali
                <?php if (count($approved) > 0): ?>
                    <span class="badge-count"><?= count($approved) ?></span>
                <?php endif; ?>
            </a>
        </div>

        <!-- TAB: Pending -->
        <?php if ($tab === 'pending'): ?>
            <?php if (empty($pending)): ?>
                <div class="card" style="text-align:center; color:var(--muted);">Tidak ada permintaan masuk saat ini.</div>
            <?php else: ?>
                <?php foreach ($pending as $r): ?>
                    <div class="approval-card">
                        <div class="approval-info">
                            <div class="user-name">👤 <?= htmlspecialchars($r['nama_user']) ?></div>
                            <div class="detail-row">
                                Barang &nbsp;&nbsp;: <strong><?= htmlspecialchars($r['nama_barang']) ?></strong><br>
                                Dipinjam : <?= date('d-m-Y', strtotime($r['tanggal_pinjam'])) ?><br>
                                Kembali &nbsp;: <?= date('d-m-Y', strtotime($r['tanggal_kembali'])) ?><br>
                                Keperluan : <?= htmlspecialchars($r['keperluan']) ?><br>
                                Status &nbsp;&nbsp;: <span class="badge badge-pending">Pending</span>
                            </div>
                        </div>
                        <div class="approval-actions">
                            <a href="../../actions/approve.php?id=<?= $r['id_pinjam'] ?>" class="btn-approve">✓ Setujui</a>
                            <a href="../../actions/reject.php?id=<?= $r['id_pinjam'] ?>"  class="btn-reject">✗ Tolak</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        <!-- TAB: Konfirmasi Kembali -->
        <?php else: ?>
            <?php if (empty($approved)): ?>
                <div class="card" style="text-align:center; color:var(--muted);">Tidak ada barang yang sedang dipinjam.</div>
            <?php else: ?>
                <?php foreach ($approved as $r): ?>
                    <?php $terlambat = $r['tanggal_kembali'] < $today; ?>
                    <div class="approval-card" style="<?= $terlambat ? 'border-left: 4px solid #E24B4A;' : '' ?>">
                        <div class="approval-info">
                            <div class="user-name">
                                👤 <?= htmlspecialchars($r['nama_user']) ?>
                                <?php if ($terlambat): ?>
                                    <span class="terlambat-tag">⚠️ Terlambat</span>
                                <?php endif; ?>
                            </div>
                            <div class="detail-row">
                                Barang &nbsp;&nbsp;: <strong><?= htmlspecialchars($r['nama_barang']) ?></strong><br>
                                Dipinjam : <?= date('d-m-Y', strtotime($r['tanggal_pinjam'])) ?><br>
                                Kembali &nbsp;: <?= date('d-m-Y', strtotime($r['tanggal_kembali'])) ?>
                                <?php if ($terlambat): ?>
                                    <span style="color:#E24B4A; font-size:12px; font-weight:700;">
                                        (<?= (int)((strtotime($today) - strtotime($r['tanggal_kembali'])) / 86400) ?> hari terlambat)
                                    </span>
                                <?php endif; ?><br>
                                Keperluan : <?= htmlspecialchars($r['keperluan']) ?>
                            </div>
                        </div>
                        <div class="approval-actions">
                            <a href="../../actions/konfirmasi_kembali.php?id=<?= $r['id_pinjam'] ?>" class="btn-approve">✓ Konfirmasi Kembali</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>
<script src="../../assets/script.js"></script>
</body>
</html>
