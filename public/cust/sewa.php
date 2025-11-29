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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'];
    $metodeBayar = $_POST['metode_pembayaran'];

    // VALIDASI TANGGAL
    if ($mulai > $selesai) {
        echo "<script>alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai');</script>";
    } else {

        // hitung hari pakai DateTime supaya pasti benar
        $start = new DateTime($mulai);
        $end   = new DateTime($selesai);

        $diff  = $start->diff($end)->days;
        $hari  = ($diff >= 1) ? $diff : 1; // minimal 1 hari

        $total = $hari * $data['harga_sewa_per_hari'];

        // Buat transaksi
        $rentalId = $kendaraan->buatTransaksi($_SESSION['id'], $kendaraanId, $mulai, $selesai, $total);

        if ($rentalId) {

            // LOGIC PEMBAYARAN
            if ($metodeBayar === "COD") {
                // Pending (mitra yang akan konfirmasi)
                $kendaraan->buatPembayaran($rentalId, "COD", $total, "pending");

            } else {
                // Transfer atau e-wallet → langsung sukses
                $kendaraan->buatPembayaran($rentalId, $metodeBayar, $total, "sukses");
            }

            echo "<script>
                    alert('Transaksi berhasil! Lanjut ke pembayaran.');
                    window.location='pembayaran.php?rental_id=$rentalId';
                 </script>";
            exit;

        } else {
            echo "<script>alert('Gagal membuat transaksi.');</script>";
        }
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

    <label>Metode Pembayaran</label><br>
    <select name="metode_pembayaran" required>
        <option value="transfer_bank">Transfer Bank</option>
        <option value="e_wallet">E-Wallet</option>
        <option value="COD">COD</option>
    </select>
    <br><br>

    <button type="submit">Konfirmasi Sewa</button>
</form>

</body>
</html>
