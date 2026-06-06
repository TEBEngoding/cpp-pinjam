
<?php
session_start();
require '../../config/db.php';
require 'sidebar.php';

$msg   = $_SESSION['msg']   ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);

$barang_list = $pdo->query("SELECT * FROM barang ORDER BY id_barang ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Barang — CPP</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="app-layout">

    <div class="main-content">
        <?php if ($msg):   ?><div class="alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="section-row">
            <div class="page-header" style="margin:0">
                <h1>Kelola Barang</h1>
            </div>
            <a href="tambah_barang.php" class="btn-add">+ Barang</a>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Stok</th>
                            <th>Kondisi</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($barang_list)): ?>
                            <tr><td colspan="5" style="text-align:center; color:var(--muted);">Belum ada barang</td></tr>
                        <?php else: ?>
                            <?php foreach ($barang_list as $b): ?>
                                <tr>
                                    <td><?= htmlspecialchars($b['id_barang']) ?></td>
                                    <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                                    <td><?= $b['stok'] ?></td>
                                    <td>
                                        <?php

                                            $kondisi = strtolower($b['kondisi']);

                                            if ($kondisi == 'sangat baik') {

                                                $kls = 'badge-sangat-baik';

                                            } elseif ($kondisi == 'baik') {

                                                $kls = 'badge-baik';

                                            } elseif ($kondisi == 'kurang baik') {

                                                $kls = 'badge-kurang-baik';

                                            } elseif ($kondisi == 'tidak baik') {

                                                $kls = 'badge-tidak-baik';

                                            } else {

                                                $kls = 'badge';

                                            }

                                            ?>

                                            <span class="badge <?= $kls ?>">
                                                <?= htmlspecialchars($b['kondisi']) ?>
                                            </span>
                                    </td>
                                    <td>
                                        <a href="edit_barang.php?id=<?= urlencode($b['id_barang']) ?>" class="btn-edit">Edit</a>
                                        <a href="hapus_konfirm.php?id=<?= urlencode($b['id_barang']) ?>&nama=<?= urlencode($b['nama_barang']) ?>" class="btn-del">Hapus</a>
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