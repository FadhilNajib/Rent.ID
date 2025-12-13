<?php
require_once __DIR__ . '/../../app/auth.php';
requireLogin('customer');

require_once __DIR__ . '/../../app/kendaraan_cust_logic.php';

$customer_id = $_SESSION['id'];        // ini yang bener!
$nama        = $_SESSION['nama'];

$kelola = new KendaraanCustomer();
$transaksi = $kelola->getTransaksiByCustomer($customer_id);

include_once __DIR__ . '/../navbar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Rentals</title>

    <style>
        table { width: 95%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #93BAFF; color: white; }
        img { width: 120px; border-radius: 6px; }
        .small { font-size: 0.9em; color:#555; }

        .status {
            padding: 5px 8px; border-radius: 4px; color: #fff;
            display: inline-block; min-width: 90px;
        }
        .pending { background: orange; }
        .dijadwalkan { background: #0069d9; }
        .aktif { background: green; }
        .selesai { background: gray; }
        .dibatalkan { background: red; }
    </style>
</head>

<body>

<h2 style="text-align:center;">Daftar Rental Saya — <?= htmlspecialchars($nama) ?></h2>

<table>
<tr>
    <th>ID</th>
    <th>Kendaraan</th>
    <th>Tanggal</th>
    <th>Total</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php if (empty($transaksi)): ?>
    <tr>
        <td colspan="6" style="text-align:center; color:gray;">Belum ada transaksi.</td>
    </tr>
<?php else: ?>
    <?php foreach ($transaksi as $t): ?>
        <tr>
            <td><?= $t['rental_id'] ?></td>

            <td>
                <?php
                // Determine image source: prefer file in `uploads/`, otherwise if the DB contains
                // raw binary image data (BLOB) render as data URI. Fallback to a placeholder.
                $fotoField = $t['foto'] ?? '';
                $imgSrc = '../../asset/logo.png';

                // path to uploads relative to this file
                $uploadPath = __DIR__ . '/../../uploads/' . $fotoField;
                if (!empty($fotoField) && file_exists($uploadPath)) {
                    $imgSrc = '../../uploads/' . rawurlencode($fotoField);
                } elseif (!empty($fotoField) && strlen($fotoField) > 64) {
                    // likely binary blob; try to detect mime type and emit data URI
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

                <img src="<?= $imgSrc ?>" alt="">
                <br>
                <?= htmlspecialchars($t['merk'] . ' ' . $t['model']) ?><br>
                <span class="small"><?= htmlspecialchars($t['plat_nomor']) ?></span>
            </td>

            <td>
                <?= $t['tanggal_mulai'] ?> → <?= $t['tanggal_selesai'] ?>
            </td>

            <td>
                Rp<?= number_format($t['total_harga'], 0, ',', '.') ?>
            </td>

            <td>
                <span class="status <?= $t['status_rental'] ?>">
                    <?= ucfirst($t['status_rental']) ?>
                </span>
            </td>

            <td>
                <a href="detail_rental.php?id=<?= $t['rental_id'] ?>">Detail</a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>

</table>

</body>
</html>
