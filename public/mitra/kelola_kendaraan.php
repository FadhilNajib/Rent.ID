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
        /* Page layout */
        .container { max-width: 1200px; margin: 0 auto; padding: 28px 20px; }
        h2 { margin: 6px 0 18px 0; font-size: 28px; color: #1f1140; }

        /* Table card */
        .table-card { background: #fff; padding: 18px; border-radius: 12px; box-shadow: 0 10px 30px rgba(15,15,15,0.04); }
        .table-responsive { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: auto; }
        thead th { padding: 10px 12px; text-align: left; background: linear-gradient(90deg,#8dbbff,#6fa8ff); color:#fff; font-weight:700; border-top-left-radius:8px; border-top-right-radius:8px; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #eef2f7; vertical-align: middle; }
        /* Thumbnail in table */
        .tbl-img { width: 110px; height: 72px; object-fit: cover; border-radius: 8px; box-shadow: 0 6px 14px rgba(15,15,15,0.06); }
        tbody tr:hover { background: #fbfdff; }
        tbody tr:last-child td { border-bottom: none; }

        /* Status pills */
        .status-pill { display: inline-block; padding: 6px 10px; border-radius: 999px; font-weight:700; font-size:0.9em; }
        .status-tersedia { background: rgba(72,187,120,0.12); color: #1b7a36; }
        .status-disewa { background: rgba(255,99,99,0.12); color: #b02a2a; }
        .status-maint { background: rgba(255,165,0,0.12); color: #b06a00; }

        /* Action buttons */
        .actions-cell { white-space: nowrap; }
        .actions { display: flex; gap:8px; align-items:center; flex-wrap:wrap; }
        .btn { padding: 6px 10px; background: #5aa0ff; color: white; text-decoration: none; border-radius: 8px; display: inline-block; border: none; font-size: 13px; }
        .btn:hover { opacity: 0.95; }
        .btn-red { background: #ff6b6b; }
        .btn-green { background: #3fc07a; }
        .btn-outline { background: transparent; color: #5b6b88; border: 1px solid #e6eef8; }

        /* Empty state */
        .empty-row { text-align: center; color: #6b7280; padding: 18px 0; }

        @media (max-width: 900px) {
            h2 { font-size: 22px; }
            .container { padding: 20px 12px; }
            .actions { gap:6px; }
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Kelola Kendaraan Saya</h2>

    <div style="margin-bottom:14px; display:flex; gap:12px; align-items:center;">
        <a href="tambah_kendaraan.php" class="btn btn-green">+ Tambah Kendaraan</a>
    </div>

    <div class="table-card">
      <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
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
        <td colspan="8">
            <div class="empty-row">Tidak ada kendaraan. Tambahkan kendaraan Anda terlebih dahulu.</div>
        </td>
    </tr>

<?php else: ?>
    <?php foreach ($dataKendaraan as $row): ?>
    <tr>
        <td><?= $row['kendaraan_id'] ?></td>
        <?php
            // Determine image source (prefer uploads file, else BLOB data URI, else fallback)
            $fotoField = $row['foto'] ?? '';
            $imgSrc = '../../asset/logo.png';
            $uploadsPath = __DIR__ . '/../../uploads/' . $fotoField;
            if (!empty($fotoField) && file_exists($uploadsPath)) {
                $imgSrc = '../../uploads/' . rawurlencode($fotoField);
            } elseif (!empty($fotoField) && strlen($fotoField) > 64) {
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
        <td><img src="<?= $imgSrc ?>" alt="Foto <?= htmlspecialchars($row['merk'] . ' ' . $row['model']) ?>" class="tbl-img"></td>
        <td><?= ucfirst($row['jenis_kendaraan']) ?></td>
        <td><?= $row['merk'] ?></td>
        <td><?= $row['model'] ?></td>
        <td><?= $row['plat_nomor'] ?></td>
        <td>Rp <?= number_format($row['harga_sewa_per_hari'], 0, ',', '.') ?></td>

        <!-- STATUS -->
        <td>
    <?php if ($row['status_kendaraan'] === 'tersedia'): ?>
        <span class="status-pill status-tersedia">Tersedia</span>

    <?php elseif ($row['status_kendaraan'] === 'disewa'): ?>
        <span class="status-pill status-disewa">Disewa</span>

    <?php else: ?>
        <span class="status-pill status-maint">Maintenance</span>

    <?php endif; ?>
</td>
<td class="actions-cell">
    <div class="actions">
        <a href="edit_kendaraan.php?id=<?= $row['kendaraan_id'] ?>" class="btn btn-outline">Edit</a>

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
    </div>
</td>
    </tr>
    <?php endforeach; ?>
<?php endif; ?>
                </tbody>
                </table>
            </div>
        </div>
</div>

</body>
</html>
