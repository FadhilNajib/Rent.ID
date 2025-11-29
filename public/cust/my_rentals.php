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
                <img src="../../uploads/<?= $t['foto'] ?>" alt="">
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
                <a href="rental_detail.php?id=<?= $t['rental_id'] ?>">Detail</a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>

</table>

</body>
</html>
