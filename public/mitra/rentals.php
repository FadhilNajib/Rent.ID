<?php
require_once __DIR__ . '/../../app/auth.php';
requireLogin('mitra');

require_once __DIR__ . '/../../app/kelola_logic.php';

$id_mitra = $_SESSION['id_mitra'];
$nama_mitra = $_SESSION['nama'];

$kelola = new KelolaKendaraan();
$dataTransaksi = $kelola->getTransaksiByMitra($id_mitra);

include_once __DIR__ . '/../navbar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rental Management - Mitra</title>
    <style>
        table { width: 95%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; vertical-align: middle; }
        th { background: #93BAFF; color: #fff; }
        .btn { padding: 6px 10px; border-radius: 4px; text-decoration: none; color: #fff; }
        .btn-acc { background: #007bff; }
        .btn-cancel { background: #dc3545; }
        .btn-done { background: #28a745; }
        .btn-cod { background: #ff9800; }

        .status-pending { background: orange; color: white; padding:5px 8px; border-radius:4px; }
        .status-dijadwalkan { background: #0069d9; color: white; padding:5px 8px; border-radius:4px; }
        .status-aktif { background: green; color: white; padding:5px 8px; border-radius:4px; }
        .status-selesai { background: gray; color: white; padding:5px 8px; border-radius:4px; }
        .status-dibatalkan { background: red; color: white; padding:5px 8px; border-radius:4px; }

        .small { font-size: 0.9em; color:#555; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Daftar Rental Pelanggan — <?= htmlspecialchars($nama_mitra) ?></h2>

<table>
<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Kendaraan</th>
    <th>Lama</th>
    <th>Total Harga</th>
    <th>Pembayaran</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php if (empty($dataTransaksi)): ?>
    <tr>
        <td colspan="8" style="text-align:center; color:gray;">Belum ada transaksi.</td>
    </tr>
<?php else: ?>
    <?php foreach ($dataTransaksi as $t): ?>
        <?php
            // label status pembayaran
            $pemb_status = $t['status_pembayaran'] ?? 'pending';
            $pemb_metode = $t['metode_pembayaran'] ?? '-';
        ?>
    <tr>
        <td><?= $t['rental_id'] ?></td>
        <td>
            <?= htmlspecialchars($t['nama_customer']) ?><br>
            <span class="small"><?= htmlspecialchars($t['email_customer'] ?? '') ?></span><br>
            <span class="small"><?= htmlspecialchars($t['no_telepon'] ?? '') ?></span>
        </td>
        <td>
            <?= htmlspecialchars($t['merk'] . ' ' . $t['model']) ?><br>
            <span class="small"><?= htmlspecialchars($t['plat_nomor']) ?></span>
        </td>
        <td>
            <?= htmlspecialchars($t['tanggal_mulai']) ?> → <?= htmlspecialchars($t['tanggal_selesai']) ?>
        </td>
        <td>Rp<?= number_format($t['total_harga'],0,',','.') ?></td>

        <td>
            <div><?= strtoupper($pemb_metode) ?></div>
            <div><strong><?= ucfirst($pemb_status) ?></strong></div>
        </td>

        <td>
            <?php
                $sr = $t['status_rental'];
                $cls = 'status-' . $sr;
            ?>
            <span class="<?= $cls ?>"><?= ucfirst($sr) ?></span>
        </td>

        <td>
            <!-- Aksi: ACC (jadwalkan) / Batalkan -->
            <?php if ($t['status_rental'] === 'pending'): ?>
                <!-- jika non-COD dan pembayaran belum sukses -> jangan bisa ACC -->
                <?php if ($pemb_metode !== 'COD' && $pemb_status !== 'sukses'): ?>
                    <div class="small" style="color:#b00;">Menunggu pembayaran</div>
                <?php else: ?>
                    <a class="btn btn-acc" href="../../app/kelola_logic.php?action=update_rental&id=<?= $t['rental_id'] ?>&status=dijadwalkan"
                        onclick="return confirm('ACC transaksi ini? (status menjadi dijadwalkan)');"> ACC
                    </a>

                <?php endif; ?>

                <a class="btn btn-cancel" href="../../app/kelola_logic.php?action=update_rental&id=<?= $t['rental_id'] ?>&status=dibatalkan"
                   onclick="return confirm('Batalkan transaksi ini?')">Batalkan</a>

                <!-- Jika metode COD dan pembayaran masih pending -> tampilkan tombol konfirmasi COD -->
                <?php if ($pemb_metode === 'COD' && $pemb_status === 'pending'): ?>
                    <a class="btn btn-cod" href="../../app/kelola_logic.php?action=konfirmasi_cod&id=<?= $t['rental_id'] ?>"
                       onclick="return confirm('Konfirmasi pembayaran COD (sudah dibayar cash oleh customer)?')">
                        Konfirmasi COD
                    </a>
                <?php endif; ?>

            <?php elseif ($t['status_rental'] === 'dijadwalkan'): ?>
                <div class="small">Dijadwalkan — akan aktif pada <?= htmlspecialchars($t['tanggal_mulai']) ?></div>
                <a class="btn btn-cancel" href="../../app/kelola_logic.php?action=update_rental&id=<?= $t['rental_id'] ?>&status=dibatalkan"
                   onclick="return confirm('Batalkan transaksi ini?')">Batalkan</a>

            <?php elseif ($t['status_rental'] === 'aktif'): ?>
                <a class="btn btn-done" href="../../app/kelola_logic.php?action=update_rental&id=<?= $t['rental_id'] ?>&status=selesai"
                   onclick="return confirm('Tandai rental ini selesai?')">Tandai Selesai</a>

            <?php elseif ($t['status_rental'] === 'selesai'): ?>
                <div class="small">Selesai</div>
            <?php else: ?>
                <div class="small"><?= htmlspecialchars($t['status_rental']) ?></div>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
<?php endif; ?>

</table>

</body>
</html>
