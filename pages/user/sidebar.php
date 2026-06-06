<?php
// Proteksi: hanya user biasa yang boleh akses
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'user') {
    header('Location: ../../index.php');
    exit;
}

$current = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-main"><span>CP </span>Pinjam2</div>
        <div class="logo-sub">User Panel</div>
    </div>

    <a href="dashboard.php"  class="nav-item <?= $current === 'dashboard'  ? 'active' : '' ?>">🏠 <span>Dashboard</span></a>
    <a href="barang.php"     class="nav-item <?= $current === 'barang'     ? 'active' : '' ?>">📦 <span>Daftar Barang</span></a>
    <a href="peminjaman.php" class="nav-item <?= $current === 'peminjaman' ? 'active' : '' ?>">📝 <span>Peminjaman</span></a>
    <a href="profil.php"     class="nav-item <?= $current === 'profil'     ? 'active' : '' ?>">👤 <span>Profil</span></a>

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
