<?php
include '../app/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = registerUser($_POST['role'], $_POST['nama'], $_POST['email'], $_POST['password'], $_POST['confirm_password']);
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Rent.id</title>

    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/regist.css">
    <link rel="icon" type="image/png" sizes="32x32" href="asset/icon.png">

</head>

<body>
    <div class="register-box">
        <h2>Selamat Datang<br>di Rent.id</h2>

        <?php if (!empty($message)): ?>
            <p class="error-message" style="color:#b91c1c;margin-bottom:12px;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="nama" placeholder="Nama" required>
            <input type="email" name="email" placeholder="E-mail" required>

            <!-- Password -->
            <div class="password-wrapper">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="button" onclick="togglePassword('password','eye1')">
                    <svg id="eye1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zM12 16.5a4.5 4.5 0 100-9 4.5 4.5 0 000 9zM12 14a2 2 0 110-4 2 2 0 010 4z" fill="#333"/>
                    </svg>
                </button>
            </div>

            <!-- Konfirmasi Password -->
            <div class="password-wrapper">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                <button type="button" onclick="togglePassword('confirm_password','eye2')">
                    <svg id="eye2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zM12 16.5a4.5 4.5 0 100-9 4.5 4.5 0 000 9zM12 14a2 2 0 110-4 2 2 0 010 4z" fill="#333"/>
                    </svg>
                </button>
            </div>

            <div class="role-selection">
                <label class="role-label">Buat akun sebagai:</label>
                <div class="role-options">
                        <label><input type="radio" name="role" value="customer" required> Penyewa</label>
                        <label><input type="radio" name="role" value="mitra"> Pemilik rental</label>
                </div>
            </div>

            <button type="submit">Register</button>

            <div class="register-link">
                Sudah punya akun? <a href="login.php">Login di sini.</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(fieldId, eyeId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(eyeId);
            const isHidden = input.type === "password";

            input.type = isHidden ? "text" : "password";

            // Ganti warna ikon biar kelihatan aktif/pasif
            icon.style.fill = isHidden ? "#93BAFF" : "#333";
        }
    </script>
</body>
</html>
