<?php
include 'koneksi.php';
//session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $password = trim($_POST['password']);

    if (empty($nama) || empty($password)) {
        echo "Semua kolom wajib diisi.";
        exit;
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
            echo "Password atau username salah.";
            exit;
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
            header("Location: Dashboard_Customer.php");
            exit;
        } else {
            echo "Password Atau username salah.";
            exit;
        }
    }

    echo "Nama pengguna tidak ditemukan.";
    $stmt->close();
}
?>

<form method="POST" action="">
    <label>Nama:</label><br>
    <input type="text" name="nama" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button><br><br>

    <div class="register-link">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
    </div>
</form>
