<?php
include '../app/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $message = loginUser($nama, $password);
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Rent.id</title>
    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/login.css">
    <link rel="icon" type="image/png" sizes="32x32" href="asset/icon.png">

</head>

<body>
    <div class="login-box">
        <h2>Selamat Datang<br>di Rent.id</h2>

        <?php if (!empty($message)) echo "<p class='error-message'>$message</p>"; ?>

        <form method="POST" action="">
            <input type="text" name="nama" placeholder="Username" required>

            <div class="password-wrapper" aria-hidden="false">
                <input type="password" id="password" name="password" placeholder="Password" required aria-label="Password">
                <button type="button" class="toggle-password" id="togglePassword" aria-label="Tampilkan atau sembunyikan password" title="Tampilkan/ Sembunyikan password">
                    <!-- two-state SVG: eye (visible) and eye-off (hidden) handled by swapping paths -->
                    <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <!-- visible eye (default: closed state uses line through, but we'll toggle by data-visible) -->
                        <path id="eyeVisible" d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zM12 16.5a4.5 4.5 0 100-9 4.5 4.5 0 000 9zM12 14a2 2 0 110-4 2 2 0 010 4z" fill="#333" />
                        <g id="eyeHidden" style="display:none;">
                            <path d="M2 2l20 20" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                    </svg>
                </button>
            </div>

            <button type="submit" class="submit">Login</button>

            <div class="register-link">
                Belum punya akun? <a href="register.php">Daftar di sini.</a>
            </div>
        </form>
    </div>

    <script>
        (function() {
            const passwordField = document.getElementById('password');
            const toggleBtn = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeVisible = document.getElementById('eyeVisible');
            const eyeHidden = document.getElementById('eyeHidden');

            // initial state: password hidden
            let visible = false;

            function updateIcon() {
                if (visible) {
                    // show "eye with slash" — for simplicity: show the hidden group
                    eyeVisible.style.display = 'none';
                    eyeHidden.style.display = 'block';
                } else {
                    eyeVisible.style.display = 'block';
                    eyeHidden.style.display = 'none';
                }
            }

            toggleBtn.addEventListener('click', function() {
                visible = !visible;
                passwordField.type = visible ? 'text' : 'password';
                updateIcon();
                // keep focus on password field for accessibility
                passwordField.focus();
            });

            // allow toggle by pressing Enter/Space when toggle button focused
            toggleBtn.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleBtn.click();
                }
            });

            updateIcon();
        })();
    </script>
</body>
</html>

<!-- login customer pelanggan1 pass = cust123 --> 
 <!-- Login mitra motor pass = motor123 -->