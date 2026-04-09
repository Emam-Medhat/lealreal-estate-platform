import './bootstrap';
import './notifications';
import 'bootstrap';


window.togglePassword = function (fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');

    if (!field || !eye) return;

    if (field.type === 'password') {
        field.type = 'text';
        eye.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13.875 18.825A10.05 10.05 0 0112 19
            c-4.478 0-8.268-2.943-9.543-7
            a9.97 9.97 0 011.563-3.029
            m5.858.908a3 3 0 114.243 4.243
            M9.878 9.878l4.242 4.242
            M9.88 9.88l-3.29-3.29
            m7.532 7.532l3.29 3.29
            M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5
            c4.478 0 8.268 2.943 9.543 7
            a10.025 10.025 0 01-4.132 5.411
            m0 0L21 21" />
        `;
    } else {
        field.type = 'password';
        eye.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 12a3 3 0 11-6 0
            3 3 0 016 0zM2.458 12
            C3.732 7.943 7.523 5 12 5
            c4.478 0 8.268 2.943 9.542 7
            -1.274 4.057-5.064 7-9.542 7
            -4.477 0-8.268-2.943-9.542-7z" />
        `;
    }
};

// Auto-hide flash messages
document.addEventListener('DOMContentLoaded', function() {
    const flashMessages = document.querySelectorAll('.flash-message');
    if (flashMessages.length > 0) {
        setTimeout(() => {
            flashMessages.forEach(el => {
                el.style.transition = 'opacity 0.5s ease-out';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 4000);
    }
});
