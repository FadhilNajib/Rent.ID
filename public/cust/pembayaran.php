<?php
include __DIR__ . '/../../app/auth.php';
requireLogin('customer');

include __DIR__ . '/../../app/kendaraan_cust_logic.php';
include __DIR__ . '/../navbar.php';

if (!isset($_GET['rental_id'])) {
    die("Transaksi tidak ditemukan.");
}

$rentalId = $_GET['rental_id'];
$kendaraan = new KendaraanCustomer();
$detail = $kendaraan->getDetailPembayaran($rentalId);

if (!$detail) {
    die("Data pembayaran tidak ditemukan.");
}

// PROSES SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $metode = $_POST['metode_pembayaran'];
    $jumlah = $detail['total_harga'];

    $buat = $kendaraan->buatPembayaran($rentalId, $metode, $jumlah);

    if ($buat) {
        echo "<script>alert('Pembayaran berhasil dibuat!'); window.location='my_rentals.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal membuat pembayaran.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pembayaran</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; }
        .box {
            width: 400px; margin: 40px auto; padding: 20px;
            background:white; border-radius:10px; box-shadow:0 0 5px rgba(0,0,0,0.1);
        }
        select, button {
            width:100%; padding:10px; border-radius:5px; margin-top:10px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Pembayaran untuk Rental #<?= $rentalId ?></h2>

    <p><strong>Kendaraan:</strong> <?= $detail['merk'] . " " . $detail['model'] ?></p>
    <p><strong>Total Harga:</strong> Rp<?= number_format($detail['total_harga']) ?></p>

    <form method="POST">
        <label>Metode Pembayaran</label><br>
        <select name="metode_pembayaran" required>
            <option value="">-- Pilih Metode --</option>
            <option value="transfer_bank">Transfer Bank</option>
            <option value="e_wallet">E-Wallet</option>
            <option value="COD">Bayar di Tempat (COD)</option>
        </select>

        <button type="submit" style="background:#28a745; color:white;">Konfirmasi Pembayaran</button>
    </form>
</div>

</body>
</html>
