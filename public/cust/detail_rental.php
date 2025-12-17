<?php
require_once __DIR__ . '/../../app/auth.php';
requireLogin('customer');

$require_once_path = __DIR__ . '/../../app/kendaraan_cust_logic.php';
require_once __DIR__ . '/../../app/kendaraan_cust_logic.php';
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

// Tentukan sumber gambar: cek apakah field foto berisi nama file di folder uploads
$fotoField = $detail['foto'] ?? '';
$imgSrc = '../../asset/logo.png';

// path to uploads on disk
$uploadPath = __DIR__ . '/../../uploads/' . $fotoField;
if (!empty($fotoField) && file_exists($uploadPath)) {
    $imgSrc = '../../uploads/' . rawurlencode($fotoField);
} elseif (!empty($fotoField) && strlen($fotoField) > 64) {
    // kemungkinan besar ini BLOB (binary) — deteksi MIME bila memungkinkan
    if (function_exists('finfo_buffer')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $fotoField) ?: 'image/jpeg';
        finfo_close($finfo);
    } else {
        $mime = 'image/jpeg';
    }
    $imgSrc = 'data:' . $mime . ';base64,' . base64_encode($fotoField);
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
        /* Tidy layout for transaction detail page */
        .status { padding: 6px 12px; border-radius: 999px; font-size: 0.95em; }
        .detail-container { margin-top: 20px; margin-bottom: 48px; }
        .card-header { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 20px; background: linear-gradient(90deg,#6d59e0,#8c59d4); color:#fff; border-bottom: none; }
        .card { border: none; border-radius: 12px; overflow: visible; }
        .card-body { padding: 20px; }
        .card .img-fluid { border-radius: 8px; box-shadow: 0 8px 20px rgba(15,15,15,0.06); }
        h5 { font-weight: 700; margin-top: 0; margin-bottom: 8px; }
        p { margin-bottom: 6px; color: #333; }
        .muted { color: #6b7280; }
        /* tighten the top spacing on small screens to avoid content moving too far down */
        @media (max-width: 768px) {
            .detail-container { margin-top: 12px; }
            .card-body { padding: 14px; }
        }
    </style>
</head>
<body class="bg-light">
<?php include_once __DIR__ . '/../navbar.php'; ?>

<div class="container detail-container">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow">
                <div class="card-header">
                    <h4 class="mb-0">Detail Transaksi #<?= $detail['rental_id'] ?></h4>
                    <div class="small muted">ID: <?= htmlspecialchars($detail['rental_id']) ?></div>
                </div>
                <div class="card-body">

                    <!-- Foto + Info Kendaraan -->
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <img src="<?= $imgSrc ?>" 
                                 class="img-fluid rounded shadow" style="max-height: 220px;" alt="Foto Kendaraan">
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