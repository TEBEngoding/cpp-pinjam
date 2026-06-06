<?php
session_start();
require '../../config/db.php';
require 'sidebar.php';

$id_user     = $_SESSION['id_user'];
$msg         = $_SESSION['msg']   ?? '';
$error       = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);

// Barang yang stoknya > 0
$barang_list = $pdo->query("SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC")->fetchAll();

// Pre-select kalau datang dari tombol Pinjam di halaman barang
$preselect = $_GET['barang'] ?? '';

// Cek apakah sedang mode edit
$edit_id   = intval($_GET['edit'] ?? 0);
$edit_data = null;
if ($edit_id) {
    $stmt = $pdo->prepare("
        SELECT p.*, dp.id_barang, dp.keperluan
        FROM peminjaman p
        JOIN detail_pinjam dp ON p.id_pinjam = dp.id_pinjam
        WHERE p.id_pinjam = ? AND p.id_user = ? AND p.status = 'Pending'
    ");
    $stmt->execute([$edit_id, $id_user]);
    $edit_data = $stmt->fetch();
}

// Riwayat peminjaman user ini
$riwayat = $pdo->prepare("
    SELECT p.id_pinjam, b.nama_barang, b.id_barang, p.tanggal_pinjam, p.tanggal_kembali, p.status, dp.keperluan
    FROM peminjaman p
    JOIN detail_pinjam dp ON p.id_pinjam = dp.id_pinjam
    JOIN barang b ON dp.id_barang = b.id_barang
    WHERE p.id_user = ?
    ORDER BY p.id_pinjam DESC
");
$riwayat->execute([$id_user]);
$riwayat = $riwayat->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman — CPP</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="app-layout">

    <div class="main-content">
        <div class="page-header">
            <h1><?= $edit_data ? 'Edit Permintaan' : 'Form Peminjaman' ?></h1>
            <p><?= $edit_data ? 'Ubah data permintaan yang masih pending' : 'Isi data peminjaman dengan lengkap ya!' ?></p>
        </div>

        <?php if ($msg):   ?><div class="alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <!-- Form Ajukan / Edit -->
        <div class="card" style="max-width: 460px;">
            <?php if ($edit_data): ?>
                <!-- MODE EDIT -->
                <form method="POST" action="../../actions/edit_pinjam.php">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_pinjam" value="<?= $edit_data['id_pinjam'] ?>">
                    <div class="form-group">
                        <label>Barang</label>
                        <input type="text" value="<?= htmlspecialchars($edit_data['nama_barang'] ?? '') ?>" disabled style="background:var(--surface); color:var(--muted);">
                        <small style="color:var(--muted);">Barang tidak bisa diubah. Hapus dan buat permintaan baru jika ingin ganti barang.</small>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Peminjaman</label>
                        <input type="date" name="tanggal_pinjam" value="<?= $edit_data['tanggal_pinjam'] ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Kembali</label>
                        <input type="date" name="tanggal_kembali" value="<?= $edit_data['tanggal_kembali'] ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Keperluan</label>
                        <input type="text" name="keperluan" value="<?= htmlspecialchars($edit_data['keperluan']) ?>" required>
                    </div>
                    <div style="display:flex; gap:10px; margin-top:8px;">
                        <a href="peminjaman.php" class="btn-secondary" style="flex:1; text-align:center; padding:10px;">Batal</a>
                        <button type="submit" class="btn-primary" style="flex:1;">Simpan Perubahan</button>
                    </div>
                </form>
            <?php else: ?>
                <!-- MODE TAMBAH -->
                <form method="POST" action="../../actions/ajukan_pinjam.php">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <select name="id_barang" required>
                            <option value="">— Pilih Barang —</option>
                            <?php foreach ($barang_list as $b): ?>
                                <option value="<?= $b['id_barang'] ?>"
                                    <?= $preselect === $b['id_barang'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['nama_barang']) ?> (stok: <?= $b['stok'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Peminjaman</label>
                        <input type="date" name="tanggal_pinjam" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Kembali</label>
                        <input type="date" name="tanggal_kembali" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Keperluan</label>
                        <input type="text" name="keperluan" placeholder="Acara / kegiatan..." required>
                    </div>
                    <button type="submit" class="btn-primary" style="width:100%; margin-top:6px;">Ajukan Peminjaman</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Riwayat -->
        <div class="page-header" style="margin-top:24px;">
            <h1 style="font-size:17px;">Riwayat Peminjaman</h1>
        </div>
        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat)): ?>
                            <tr><td colspan="6" style="text-align:center; color:var(--muted);">Belum ada riwayat peminjaman</td></tr>
                        <?php else: ?>
                            <?php foreach ($riwayat as $r): ?>
                                <?php
                                $s   = $r['status'];
                                $cls = match(strtolower($s)) {
                                    'pending'  => 'badge-pending',
                                    'approved' => 'badge-approved',
                                    'rejected' => 'badge-rejected',
                                    'selesai'  => 'badge-selesai',
                                    default    => 'badge-pending'
                                };
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['nama_barang']) ?></td>
                                    <td><?= date('d M Y', strtotime($r['tanggal_pinjam'])) ?></td>
                                    <td><?= date('d M Y', strtotime($r['tanggal_kembali'])) ?></td>
                                    <td><?= htmlspecialchars($r['keperluan']) ?></td>
                                    <td><span class="badge <?= $cls ?>"><?= $s ?></span></td>
                                    <td>
                                        <?php if (strtolower($s) === 'pending'): ?>
                                            <a href="peminjaman.php?edit=<?= $r['id_pinjam'] ?>" class="btn-edit" style="font-size:12px;">✏️ Edit</a>
                                            <a href="#" onclick="konfirmasiHapusPinjam(<?= $r['id_pinjam'] ?>, '<?= htmlspecialchars(addslashes($r['nama_barang'])) ?>')" class="btn-del" style="font-size:12px;">🗑️ Hapus</a>
                                        <?php else: ?>
                                            <span style="color:var(--muted); font-size:12px;">—</span>
                                        <?php endif; ?>
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

<!-- Modal Konfirmasi Hapus Permintaan -->
<div class="modal-overlay" id="modal-hapus-pinjam">
    <div class="modal-box">
        <div class="modal-icon warn">🗑️</div>
        <h3>Batalkan Permintaan?</h3>
        <p id="modal-hapus-pinjam-text">Kamu yakin ingin membatalkan permintaan ini?</p>
        <div class="modal-btns">
            <button class="btn-secondary" onclick="document.getElementById('modal-hapus-pinjam').classList.remove('show')">Batal</button>
            <a id="modal-hapus-pinjam-link" href="#" class="btn-primary" style="background:#E24B4A; color:white;">Ya, Batalkan</a>
        </div>
    </div>
</div>

<script src="../../assets/script.js"></script>
<script>
function konfirmasiHapusPinjam(id, nama) {
    document.getElementById('modal-hapus-pinjam-text').textContent =
        'Kamu yakin ingin membatalkan permintaan pinjam "' + nama + '"?';
    document.getElementById('modal-hapus-pinjam-link').href =
        '../../actions/edit_pinjam.php?action=hapus&id=' + id;
    document.getElementById('modal-hapus-pinjam').classList.add('show');
}
</script>
</body>
</html>
