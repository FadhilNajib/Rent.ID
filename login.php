<?php
include 'koneksi.php';
//session_start();
$message= "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $password = trim($_POST['password']);

    if (empty($nama) || empty($password)) {
        echo "Semua kolom wajib diisi.";
        
    }

    // Coba cek di tabel mitra dulu
    $queryMitra = "SELECT id_mitra AS id, nama_mitra AS nama, password FROM mitra WHERE nama_mitra = ?";
    $stmt = $conn->prepare($queryMitra);
    $stmt->bind_param("s", $nama);
    $stmt->execute();
    $resultMitra = $stmt->get_result();

    if ($resultMitra->num_rows > 0) {
        $user = $resultMitra->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = 'mitra';
            header("Location: Dashboard_Mitra.php");
            exit;
        } else {
            $message = "Password atau username salah.";
            
        }
    }

    // Jika tidak ditemukan di mitra, cek di customer
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
            echo "Password Atau username salah.";
            
        }
    }

    $message = "Username tidak ditemukan.";
    $stmt->close();
}
?>

<form method="POST" action="">
    <label>Username :</label><br>
    <input type="text" name="nama" required><br><br>

    <label>Password:</label><br>
    <input type="password" id="password"name="password" required><br><br>
    <input type="checkbox" id="showPassword" onclick="togglePassword()"> Tampilkan Password
    <br><br>
    <button type="submit">Login</button><br><br>

    <div class="register-link">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
    </div>
</form>
<script>
function togglePassword() {
    const passwordField = document.getElementById("password");
    const checkbox = document.getElementById("showPassword");
    passwordField.type = checkbox.checked ? "text" : "password";
}
</script>

<!-- login customer pelanggan1 pass = cust123 -->
<!-- Login mitra motor pass = motor123 -->