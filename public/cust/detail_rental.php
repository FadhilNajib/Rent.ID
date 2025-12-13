<?php
require_once __DIR__ . '/../../app/auth.php';
requireLogin('customer');

require_once __DIR__ . '/../../app/kendaraan_cust_logic.php';

include_once __DIR__ . '/../navbar.php'; // ata
$transaksiCtrl = new KendaraanCustomer();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID transaksi tidak valid!");
}

$detail = $transaksiCtrl->getDetailTransaksi($_GET['id']);

if (!$detail) {
    die("Transaksi tidak ditemukan atau bukan milik Anda.");
}

// Cek apakah benar milik customer yang login (keamanan ekstra)
if ($detail['id_customer'] != $_SESSION['id']) {
    die("Akses ditolak!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Rental #<?= $detail['rental_id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status { padding: 6px 12px; border-radius: 20px; font-size: 0.9em; }
    </style>
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Detail Transaksi #<?= $detail['rental_id'] ?></h4>
                </div>
                <div class="card-body">

                    <!-- Foto + Info Kendaraan -->
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <img src="../../uploads/<?= htmlspecialchars($detail['foto'] ?? 'default.jpg') ?>" 
                                 class="img-fluid rounded shadow" style="max-height: 220px;">
                        </div>
                        <div class="col-md-8">
                            <h5>Informasi Kendaraan</h5>
                            <p><strong><?= htmlspecialchars($detail['merk'] . ' ' . $detail['model']) ?></strong></p>
                            <p>Plat Nomor: <strong><?= htmlspecialchars($detail['plat_nomor']) ?></strong></p>
                            <p>Harga Sewa: <strong>Rp<?= number_format($detail['harga_sewa_per_hari'], 0, ',', '.') ?> / hari</strong></p>
                        </div>
                    </div>

                    <hr>

                    <!-- Mitra -->
                    <h5>Informasi Mitra</h5>
                    <p>Nama Mitra: <strong><?= htmlspecialchars($detail['nama_mitra']) ?></strong></p>
                    <p>No.No Telepon: <strong><?= htmlspecialchars($detail['no_telepon'] ?? '-') ?></strong></p>

                    <hr>

                    <!-- Penyewa (Customer) -->
                    <h5>Informasi Penyewa</h5>
                    <p>Nama: <strong><?= htmlspecialchars($detail['nama_customer']) ?></strong></p>
                    <p>Email: <strong><?= htmlspecialchars($detail['email_customer']) ?></strong></p>

                    <hr>

                    <!-- Detail Transaksi -->
                    <h5>Detail Transaksi</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tanggal Mulai:</strong> <?= date('d-m-Y', strtotime($detail['tanggal_mulai'])) ?></p>
                            <p><strong>Tanggal Selesai:</strong> <?= date('d-m-Y', strtotime($detail['tanggal_selesai'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Harga:</strong> 
                                <span class="text-success fs-5">Rp<?= number_format($detail['total_harga'], 0, ',', '.') ?></span>
                            </p>
                            <p><strong>Status Rental:</strong>
                                <?php
                                $status = $detail['status_rental'];
                                $badge = match($status) {
                                    'pending'       => 'bg-warning text-dark',
                                    'dijadwalkan'   => 'bg-info',
                                    'aktif'         => 'bg-success',
                                    'selesai'       => 'bg-secondary',
                                    'dibatalkan'    => 'bg-danger',
                                    default         => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badge ?> status">
                                    <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="my_rentals.php" class="btn btn-outline-secondary">Kembali ke Daftar Rental</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>