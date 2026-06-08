// ===== MODAL LOGOUT =====
function showLogoutModal() {
    document.getElementById('modal-logout').classList.add('show');
}

function closeLogoutModal() {
    document.getElementById('modal-logout').classList.remove('show');
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