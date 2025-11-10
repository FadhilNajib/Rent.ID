<?php
include __DIR__ . '/../config/koneksi.php';

// Mulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $password = trim($_POST['password']);

    if (empty($nama) || empty($password)) {
        $message = "Semua kolom wajib diisi.";
    } else {
        // === CEK DI TABEL MITRA ===
        $queryMitra = "SELECT id_mitra AS id, nama_mitra AS nama, password FROM mitra WHERE nama_mitra = ?";
        $stmt = $conn->prepare($queryMitra);
        $stmt->bind_param("s", $nama);
        $stmt->execute();
        $resultMitra = $stmt->get_result();

        if ($resultMitra->num_rows > 0) {
            $user = $resultMitra->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['id_mitra'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = 'mitra';
                header("Location: ../public/Dashboard_Mitra.php");
                exit;
            } else {
                $message = "Password atau username salah.";
            }
        } else {
            // === CEK DI TABEL CUSTOMER ===
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
                    header("Location: ../public/dashboard_Customer.php");
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
