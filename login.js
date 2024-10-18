document.getElementById('loginForm').addEventListener('submit', function(e) {
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    var error = false;

    if (email.trim() === '') {
        alert('Por favor, ingresa tu correo electrónico.');
        error = true;
    } else if (!/\S+@\S+\.\S+/.test(email)) {
        alert('Por favor, ingresa un correo electrónico válido.');
        error = true;
    }

    if (password.trim() === '') {
        alert('Por favor, ingresa tu contraseña.');
        error = true;
    }

    if (error) {
        e.preventDefault();
    }
});