<?php
session_start();
include_once __DIR__ . '/../config/koneksi.php';

// Cek login
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$id   = $_SESSION['id'];
$role = $_SESSION['role'];

// Query berdasarkan role
if ($role === 'customer') {
    $query = "SELECT id_customer AS id, nama, email, no_telepon, alamat, tanggal_daftar 
              FROM customer WHERE id_customer = ?";
} else {
    $query = "SELECT id_mitra AS id, nama_mitra AS nama, email, no_telepon, alamat 
              FROM mitra WHERE id_mitra = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>

<?php include __DIR__ . '/navbar.php'; ?>

<h2 style="text-align:center; margin-top:25px;">Profile <?= ucfirst($role) ?></h2>

<div style="
    width: 400px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
">
    <p><strong>ID:</strong> <?= $data['id'] ?></p>
    <p><strong>Nama:</strong> <?= $data['nama'] ?></p>
    <p><strong>Email:</strong> <?= $data['email'] ?></p>
    <p><strong>No Telepon:</strong> <?= $data['no_telepon'] ?></p>
    <p><strong>Alamat:</strong> <?= $data['alamat'] ?></p>

    <?php if ($role === 'customer'): ?>
        <p><strong>Tanggal Daftar:</strong> <?= $data['tanggal_daftar'] ?></p>
    <?php endif; ?>
</div>
