
<?php
session_start();
require '../../config/db.php';
require 'sidebar.php';

$barang_list = $pdo->query("SELECT * FROM barang ORDER BY id_barang ASC")->fetchAll();

// Emoji icon berdasarkan kategori
function getIcon($kategori) {
    $k = strtolower($kategori ?? '');
    if (str_contains($k, 'audio') || str_contains($k, 'speaker')) return '🔊';
    if (str_contains($k, 'foto') || str_contains($k, 'kamera'))   return '📷';
    if (str_contains($k, 'laptop') || str_contains($k, 'komputer')) return '💻';
    if (str_contains($k, 'proyektor'))  return '📽️';
    if (str_contains($k, 'mikrofon'))   return '🎙️';
    if (str_contains($k, 'lampu'))      return '💡';
    return '📦';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang — CPP</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="app-layout">

    <div class="main-content">
        <div class="page-header">
            <h1>Daftar Barang</h1>
            <p>Pilih barang yang ingin kamu pinjam</p>
        </div>

        <div class="card" style="margin-bottom:6px; padding:12px 16px;">
            <table style="font-size:13px;">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Kondisi</th>
                        <th>Ketersediaan</th>
                        <th>Ajukan Peminjaman</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barang_list as $b): ?>
                        <tr>
                            <td>
                                <?= getIcon($b['kategori']) ?>
                                <?= htmlspecialchars($b['nama_barang']) ?>
                                <?php if ($b['kategori']): ?>
                                    <br><small style="color:var(--muted)"><?= htmlspecialchars($b['kategori']) ?></small>
                                <?php endif; ?>
                            </td>
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
                                    <span class="badge <?= $kls ?>"><?= htmlspecialchars($b['kondisi']) ?></span>
                                </td>
                            <td>
                                <?php if ($b['stok'] > 0): ?>
                                    <span style="color:var(--success); font-weight:600;">Tersedia</span>
                                <?php else: ?>
                                    <span style="color:var(--danger); font-weight:600;">Tidak Ada</span>
                                <?php endif; ?>
                                <span style="color:var(--muted); font-size:12px;"> (stok: <?= $b['stok'] ?>)</span>
                            </td>
                            <td>
                                <?php if ($b['stok'] > 0): ?>
                                    <a href="peminjaman.php?barang=<?= urlencode($b['id_barang']) ?>" class="btn-pinjam" style="width:auto; padding:5px 14px;">Pinjam</a>
                                <?php else: ?>
                                    <span class="btn-pinjam disabled" style="width:auto; padding:5px 14px; opacity:0.4;">Habis</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="../../assets/script.js"></script>
</body>
</html>