<?php
require_once __DIR__ . '/../../app/auth.php';
requireLogin('mitra');

require_once __DIR__ . '/../../app/kelola_logic.php';

$kendaraan = new KelolaKendaraan();
$id_mitra = $_SESSION['id_mitra'];
$dataKendaraan = $kendaraan->getKendaraanByMitra($id_mitra);

// === AUTO UPDATE STATUS BERDASARKAN TANGGAL ===
$transaksiMitra = $kendaraan->getTransaksiByMitra($id_mitra);

foreach ($transaksiMitra as $trx) {
    $kendaraan->aktifkanJikaHariIni($trx);
    $kendaraan->selesaikanJikaLewat($trx);
}

?>

<?php include_once __DIR__ . '/../navbar.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kendaraan</title>

    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #93BAFF; color: white; }

        .btn {
            padding: 8px 12px;
            background: #93BAFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-red { background: #ff6565; }
        .btn-green { background: #4CAF50; }
    </style>
</head>
<body>

<div class="container" style="padding:20px;">

    <h2>Kelola Kendaraan Saya</h2>

    <a href="tambah_kendaraan.php" class="btn btn-green">+ Tambah Kendaraan</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Jenis</th>
                <th>Merk</th>
                <th>Model</th>
                <th>Plat</th>
                <th>Harga/Hari</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
<?php if (empty($dataKendaraan)): ?>
    <tr>
        <td colspan="8" style="text-align:center; color:gray;">
            Tidak ada kendaraan. Tambahkan kendaraan Anda terlebih dahulu.
        </td>
    </tr>

<?php else: ?>
    <?php foreach ($dataKendaraan as $row): ?>
    <tr>
        <td><?= $row['kendaraan_id'] ?></td>
        <td><?= ucfirst($row['jenis_kendaraan']) ?></td>
        <td><?= $row['merk'] ?></td>
        <td><?= $row['model'] ?></td>
        <td><?= $row['plat_nomor'] ?></td>
        <td>Rp <?= number_format($row['harga_sewa_per_hari'], 0, ',', '.') ?></td>

        <!-- STATUS -->
        <td>
    <?php if ($row['status_kendaraan'] === 'tersedia'): ?>
        <span style="color:green; font-weight:bold;">Tersedia</span>

    <?php elseif ($row['status_kendaraan'] === 'disewa'): ?>
        <span style="color:red; font-weight:bold;">Disewa</span>

    <?php else: ?>
        <span style="color:orange; font-weight:bold;">Maintenance</span>

    <?php endif; ?>
</td>
<td>
    <a href="edit_kendaraan.php?id=<?= $row['kendaraan_id'] ?>" class="btn">Edit</a>

    <!-- Ubah status -->
    <?php if ($row['status_kendaraan'] == 'tersedia'): ?>
        <a href="../../app/kelola_logic.php?action=ubah_status&id=<?= $row['kendaraan_id'] ?>&status=disewa"
           class="btn btn-red">Tandai Disewa</a>

        <a href="../../app/kelola_logic.php?action=ubah_status&id=<?= $row['kendaraan_id'] ?>&status=maintenance"
           class="btn btn-red">Maintenance</a>

    <?php elseif ($row['status_kendaraan'] == 'disewa'): ?>
        <a href="../../app/kelola_logic.php?action=ubah_status&id=<?= $row['kendaraan_id'] ?>&status=tersedia"
           class="btn btn-green">Tersedia</a>

    <?php else: ?>  <!-- maintenance -->
        <a href="../../app/kelola_logic.php?action=ubah_status&id=<?= $row['kendaraan_id'] ?>&status=tersedia"
           class="btn btn-green">Tersedia</a>
    <?php endif; ?>

    <a href="../../app/kelola_logic.php?action=hapus&id=<?= $row['kendaraan_id'] ?>"
       class="btn btn-red"
       onclick="return confirm('Yakin ingin menghapus kendaraan ini?')">
        Hapus
    </a>
</td>
    </tr>
    <?php endforeach; ?>
<?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
