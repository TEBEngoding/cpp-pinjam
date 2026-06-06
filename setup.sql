-- =============================================
-- SETUP AWAL: Jalankan di phpMyAdmin tab SQL
-- Database: db_cpp
-- =============================================

-- Tambah akun ADMIN
INSERT INTO users (nama_user, email, password, role) VALUES
('Admin CPP', 'admin@cpp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Tambah akun USER contoh
INSERT INTO users (nama_user, email, password, role) VALUES
('PO', 'po.himati@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Wirus', 'wirus.romati@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Hash di atas = password: "password"
-- Setelah login, bisa ganti password via halaman Profil

-- Tambah contoh barang
INSERT INTO barang (id_barang, nama_barang, kategori, stok, kondisi) VALUES
('BRG001', 'Speaker', 'Audio', 3, 'Baik'),
('BRG002', 'Tripod', 'Foto', 2, 'Baik'),
('BRG003', 'Proyektor', 'Elektronik', 1, 'Baik'),
('BRG004', 'Mikrofon', 'Audio', 4, 'Baik'),
('BRG005', 'Laptop', 'Komputer', 2, 'Baik');
