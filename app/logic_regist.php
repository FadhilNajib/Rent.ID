<?php
include __DIR__ . '../config/koneksi.php';

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
