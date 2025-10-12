<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi input kosong
    if (empty($role) || empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "Semua kolom wajib diisi.";
        exit;
    }

    // Cek apakah password dan konfirmasi sama
    if ($password !== $confirm_password) {
        echo "Password dan Konfirmasi Password tidak cocok.";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($role === 'mitra') {
        $stmt = $conn->prepare("INSERT INTO mitra (nama_mitra, password, email) VALUES (?, ?, ?)");
    } elseif ($role === 'customer') {
        $stmt = $conn->prepare("INSERT INTO customer (nama, password, email, tanggal_daftar) VALUES (?, ?, ?, NOW())");
    } else {
        echo "Role tidak valid.";
        exit;
    }

    $stmt->bind_param("sss", $nama, $hashed_password, $email);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        echo "Terjadi kesalahan: " . $conn->error;
    }

    $stmt->close();
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

        <form method="POST" action="">
            <input type="text" name="nama" placeholder="Nama" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
            <input type="checkbox" id="showPassword" onclick="togglePassword()"> Tampilkan Password

            <div class="role-selection">
    <label class="role-label">Buat akun sebagai:</label>
    <div class="role-options">
    <label><input type="radio" name="role" value="customer"> Penyewa</label>
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
        function togglePassword() {
            const password = document.getElementById("password");
            const confirm = document.getElementById("confirm_password");
            const checkbox = document.getElementById("showPassword");
            const type = checkbox.checked ? "text" : "password";
            password.type = type;
            confirm.type = type;
        }
    </script>
</body>
</html>
