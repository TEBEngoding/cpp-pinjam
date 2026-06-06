<?php
// Proteksi: hanya admin yang boleh akses
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

// Tentukan halaman aktif
$current = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-main"><span>CP </span>Pinjam2</div>
        <div class="logo-sub">Admin Panel</div>
    </div>

    <a href="dashboard.php"  class="nav-item <?= $current === 'dashboard'  ? 'active' : '' ?>">🏠 <span>Dashboard</span></a>
    <a href="barang.php"     class="nav-item <?= $current === 'barang'     ? 'active' : '' ?>">📦 <span>Kelola Barang</span></a>
    <a href="approval.php"   class="nav-item <?= $current === 'approval'   ? 'active' : '' ?>">📥 <span>Approval</span></a>
    <a href="data_user.php"  class="nav-item <?= $current === 'data_user'  ? 'active' : '' ?>">👥 <span>Data User</span></a>

    <div class="nav-logout">
        <a href="#" class="nav-item" onclick="showLogoutModal()">🚪 <span>LogOut</span></a>
    </div>
</div>

<!-- Modal Logout -->
<div class="modal-overlay" id="modal-logout">
    <div class="modal-box">
        <div class="modal-icon warn">🚪</div>
        <h3>Yakin ingin logout?</h3>
        <p>Kamu akan keluar dari sesi ini dan diarahkan kembali ke halaman login.</p>
        <div class="modal-btns">
            <button class="btn-secondary" onclick="closeLogoutModal()">Batal</button>
            <a href="../../actions/logout.php" class="btn-primary">Ya, Logout</a>
        </div>
    </div>
</div>
