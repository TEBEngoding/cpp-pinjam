// ===== MODAL LOGOUT =====
function showLogoutModal() {
    document.getElementById('modal-logout').classList.add('show');
}

function closeLogoutModal() {
    document.getElementById('modal-logout').classList.remove('show');
}

// ===== MODAL TAMBAH/EDIT BARANG (Admin) =====
function showModalBarang(mode = 'tambah', data = {}) {
    const modal = document.getElementById('modal-barang');
    const title = document.getElementById('modal-barang-title');
    if (mode === 'tambah') {
        title.textContent = 'Tambah Barang';
        document.getElementById('form-barang').reset();
        document.getElementById('barang-id').value = '';
    } else {
        title.textContent = 'Edit Barang';
        document.getElementById('barang-id').value       = data.id_barang   || '';
        document.getElementById('barang-id-input').value = data.id_barang   || '';
        document.getElementById('barang-nama').value     = data.nama_barang || '';
        document.getElementById('barang-kategori').value = data.kategori    || '';
        document.getElementById('barang-stok').value     = data.stok        || '';
        document.getElementById('barang-kondisi').value  = data.kondisi     || '';
    }
    modal.classList.add('show');
}

function closeModalBarang() {
    document.getElementById('modal-barang').classList.remove('show');
}

// ===== KONFIRMASI HAPUS =====
function confirmDelete(id, nama) {
    if (confirm('Hapus barang "' + nama + '"? Tindakan ini tidak bisa dibatalkan.')) {
        window.location.href = '../actions/hapus_barang.php?id=' + id;
    }
}

// ===== TOGGLE PASSWORD =====
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// ===== AUTO HIDE ALERT =====
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert-error, .alert-success');
    alerts.forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        }, 3000);
    });
});
