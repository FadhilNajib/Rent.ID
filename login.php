<?php
include 'koneksi.php';
//session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $password = trim($_POST['password']);

    if (empty($nama) || empty($password)) {
        $message = "Semua kolom wajib diisi.";
    } else {
        // Cek di tabel mitra
        $queryMitra = "SELECT id_mitra AS id, nama_mitra AS nama, password FROM mitra WHERE nama_mitra = ?";
        $stmt = $conn->prepare($queryMitra);
        $stmt->bind_param("s", $nama);
        $stmt->execute();
        $resultMitra = $stmt->get_result();

        if ($resultMitra->num_rows > 0) {
            $user = $resultMitra->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['id_mitra'] = $user['id']; // tambahkan ini
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = 'mitra';
                header("Location: Dashboard_Mitra.php");
                exit;
            } else {
                $message = "Password atau username salah.";
            }
        } else {
            // Cek di customer
            $queryCustomer = "SELECT id_customer AS id, nama, password FROM customer WHERE nama = ?";
            $stmt = $conn->prepare($queryCustomer);
            $stmt->bind_param("s", $nama);
            $stmt->execute();
            $resultCustomer = $stmt->get_result();

            if ($resultCustomer->num_rows > 0) {
                $user = $resultCustomer->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['nama'] = $user['nama'];
                    $_SESSION['role'] = 'customer';
                    header("Location: dashboard_Customer.php");
                    exit;
                } else {
                    $message = "Password atau username salah.";
                }
            } else {
                $message = "Username tidak ditemukan.";
            }
        }
        $stmt->close();
    }
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
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="checkbox" id="showPassword" onclick="togglePassword()"> Tampilkan Password
            <button type="submit">Login</button>

            <div class="register-link">
                Belum punya akun? <a href="register.php">Daftar di sini.</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById("password");
            const checkbox = document.getElementById("showPassword");
            passwordField.type = checkbox.checked ? "text" : "password";
        }
    </script>
</body>
</html>

<!-- login customer pelanggan1 pass = cust123 --> 
 <!-- Login mitra motor pass = motor123 -->