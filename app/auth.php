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
        // cek duplikat nama atau email pada tabel mitra
        $chk = $conn->prepare("SELECT id_mitra FROM mitra WHERE nama_mitra = ? OR email = ?");
        $chk->bind_param("ss", $nama, $email);
        $chk->execute();
        $res = $chk->get_result();
        if ($res && $res->num_rows > 0) {
            return "Nama atau email sudah terdaftar (mitra).";
        }

        $stmt = $conn->prepare("INSERT INTO mitra (nama_mitra, password, email) VALUES (?, ?, ?)");
    } elseif ($role === 'customer') {
        // cek duplikat nama atau email pada tabel customer
        $chk = $conn->prepare("SELECT id_customer FROM customer WHERE nama = ? OR email = ?");
        $chk->bind_param("ss", $nama, $email);
        $chk->execute();
        $res = $chk->get_result();
        if ($res && $res->num_rows > 0) {
            return "Nama atau email sudah terdaftar (customer).";
        }

        $stmt = $conn->prepare("INSERT INTO customer (nama, password, email, tanggal_daftar) VALUES (?, ?, ?, NOW())");
    } else {
        return "Role tidak valid.";
    }

    if (!$stmt) {
        return "Terjadi kesalahan pada query: " . $conn->error;
    }

    $stmt->bind_param("sss", $nama, $hashed_password, $email);

    if ($stmt->execute()) {
        header("Location: ../public/login.php");
        exit;
    } else {
        return "Terjadi kesalahan saat menyimpan: " . $stmt->error;
    }
}


/**
 * LOGOUT
 */
function logoutUser()
{
    // Pastikan session sudah dimulai
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Kosongkan semua variabel session
    $_SESSION = [];

    // 2. Hapus session cookie di browser (INI YANG PALING PENTING!)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // 3. Baru destroy session di server
    session_destroy();

    // 4. Hapus cookie tambahan kalau kalian pakai remember me atau simpan role di cookie
    // setcookie('username', '', time() - 3600, "/");
    // setcookie('role', '', time() - 3600, "/");

    // 5. Redirect ke index/login
    header("Location: ../index.php");
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

/**
 * HANDLER UNTUK ACTION DI URL
 * (tambahkan di paling bawah)
 */
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logoutUser();
}
?>
