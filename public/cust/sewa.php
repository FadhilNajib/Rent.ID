<?php
include __DIR__ . '/../../app/auth.php';
requireLogin('customer');

include __DIR__ . '/../../app/kendaraan_cust_logic.php';
include __DIR__ . '/../navbar.php';

if (!isset($_GET['id'])) {
    die("Kendaraan tidak ditemukan.");
}

$kendaraanId = $_GET['id'];
$kendaraan = new KendaraanCustomer();
$data = $kendaraan->getDetailKendaraan($kendaraanId);

if (!$data) {
    die("Data kendaraan tidak ditemukan.");
}

// Jika submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'];

    // hitung hari
    $hari = (strtotime($selesai) - strtotime($mulai)) / 86400;
    if ($hari < 1) $hari = 1;

    $total = $hari * $data['harga_sewa_per_hari'];

    $buat = $kendaraan->buatTransaksi($_SESSION['id'], $kendaraanId, $mulai, $selesai, $total);

    if ($buat) {
        echo "<script>alert('Transaksi berhasil dibuat!'); window.location='my_rentals.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal membuat transaksi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sewa Kendaraan</title>
</head>
<body>

<h2>Sewa: <?= htmlspecialchars($data['merk'] . " " . $data['model']) ?></h2>

<p><strong>Mitra:</strong> <?= htmlspecialchars($data['nama_mitra']) ?></p>
<p><strong>Harga Per Hari:</strong> Rp<?= number_format($data['harga_sewa_per_hari']) ?></p>

<form method="POST">
    <label>Tanggal Mulai</label><br>
    <input type="date" name="tanggal_mulai" required><br><br>

    <label>Tanggal Selesai</label><br>
    <input type="date" name="tanggal_selesai" required><br><br>

    <button type="submit">Konfirmasi Sewa</button>
</form>

</body>
</html>
