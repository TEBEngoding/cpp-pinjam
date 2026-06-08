<?php
session_start();
require '../../config/db.php';
require 'sidebar.php';

$users = $pdo->query("
    SELECT u.*,
        (SELECT COUNT(*) FROM peminjaman p
         WHERE p.id_user = u.id_user AND p.status = 'Approved') AS pinjaman_aktif
    FROM users u
    ORDER BY u.id_user ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data User — CPP</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="app-layout">

    <div class="main-content">
        <div class="page-header">
            <h1>Data User</h1>
            <p>Daftar akun anggota yang terdaftar</p>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Pinjaman Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= $u['id_user'] ?></td>
                                <td>
                                    <?= htmlspecialchars($u['nama_user']) ?>
                                </td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class="badge badge-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
                                <td><?= $u['role'] === 'user' ? $u['pinjaman_aktif'] : '—' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="../../assets/script.js"></script>
</body>
</html>
