<?php
require_once __DIR__ . '/../../app/auth.php';
requireLogin('mitra');

require_once __DIR__ . '/../../app/kelola_logic.php';

$id_mitra = $_SESSION['id_mitra'];
$nama_mitra = $_SESSION['nama'];

$kendaraan = new KelolaKendaraan();
$dataTransaksi = $kendaraan->getTransaksiByMitra($id_mitra);

include_once __DIR__ . '/../navbar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rental Management</title>
    <style>
        table { width: 95%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #93BAFF; color: #fff; }
        .btn { padding: 6px 10px; border-radius: 4px; text-decoration: none; }
        .pending { background: orange; color: white; }
        .aktif { background: green; color: white; }
        .selesai { background: gray; color: white; }
        .dibatalkan { background: red; color: white; }
    </style>
</head>

<body>
<h2 style="text-align:center;">Daftar Rental Pelanggan</h2>

<table>
<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Kendaraan</th>
    <th>Tanggal</th>
    <th>Total Harga</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php foreach ($dataTransaksi as $t): ?>
<tr>
    <td><?= $t['rental_id'] ?></td>
    <td><?= $t['nama_customer'] ?></td>
    <td><?= $t['merk'] ?> <?= $t['model'] ?> <br> (<?= $t['plat_nomor'] ?>)</td>
    
    <td><?= $t['tanggal_mulai'] ?> → <?= $t['tanggal_selesai'] ?></td>

    <td>Rp<?= number_format($t['total_harga'], 0, ',', '.') ?></td>

    <td>
        <span class="<?= $t['status_rental'] ?>">
            <?= ucfirst($t['status_rental']) ?>
        </span>
    </td>

    <td>
        <?php if ($t['status_rental'] == 'pending'): ?>
            <a class="btn aktif" 
               href="../../app/kelola_logic.php?action=update_rental&id=<?= $t['rental_id'] ?>&status=aktif">
               ACC
            </a>

            <a class="btn dibatalkan" 
               href="../../app/kelola_logic.php?action=update_rental&id=<?= $t['rental_id'] ?>&status=dibatalkan">
               Batalkan
            </a>
        <?php endif; ?>

        <?php if ($t['status_rental'] == 'aktif'): ?>
            <a class="btn selesai" 
               href="../../app/kelola_logic.php?action=update_rental&id=<?= $t['rental_id'] ?>&status=selesai">
               Selesaikan
            </a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
