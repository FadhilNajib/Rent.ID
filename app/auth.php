<?php
include_once __DIR__ . '/../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * LOGIN USER / MITRA
 */
function loginUser($nama, $password)
{
    global $conn;

    if (empty($nama) || empty($password)) {
        return "Semua kolom wajib diisi.";
    }

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
            header("Location: ../public/dashboard_Mitra.php");
            exit;
        } else {
            return "Password atau username salah.";
        }
    }

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
            return "Password atau username salah.";
        }
    }

    return "Username tidak ditemukan.";
}


/**
 * REGISTER USER / MITRA
 */
function registerUser($role, $nama, $email, $password, $confirm_password)
{
    global $conn;

    if (empty($role) || empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        return "Semua kolom wajib diisi.";
    }

    if ($password !== $confirm_password) {
        return "Password dan Konfirmasi Password tidak cocok.";
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($role === 'mitra') {
        $stmt = $conn->prepare("INSERT INTO mitra (nama_mitra, password, email) VALUES (?, ?, ?)");
    } elseif ($role === 'customer') {
        $stmt = $conn->prepare("INSERT INTO customer (nama, password, email, tanggal_daftar) VALUES (?, ?, ?, NOW())");
    } else {
        return "Role tidak valid.";
    }

    $stmt->bind_param("sss", $nama, $hashed_password, $email);

    if ($stmt->execute()) {
        header("Location: ../public/login.php");
        exit;
    } else {
        return "Terjadi kesalahan: " . $conn->error;
    }
}


/**
 * LOGOUT
 */
function logoutUser()
{
    session_unset();
    session_destroy();
    header("Location: ../public/login.php");
    exit;
}


/**
 * CEK LOGIN & BATAS AKSES
 */
function requireLogin($role = null)
{
    if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
        header("Location: ../public/login.php");
        exit;
    }

    if ($role && $_SESSION['role'] !== $role) {
        header("Location: ../public/login.php");
        exit;
    }
}
?>
