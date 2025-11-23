<?php
require_once __DIR__ . '/../../app/auth.php';
requireLogin('mitra');

require_once __DIR__ . '/../../app/kelola_logic.php';

if (!isset($_GET['id'])) {
    die("ID kendaraan tidak ditemukan.");
}

$kendaraan = new KelolaKendaraan();
$data = $kendaraan->getKendaraanById($_GET['id']);

if (!$data) {
    die("Data kendaraan tidak ditemukan.");
}

?>

<?php include_once __DIR__ . '/../navbar.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kendaraan</title>

    <style>
        form { max-width: 500px; margin: 30px auto; padding: 20px; background: #f7f7f7; border-radius: 8px; }
        label { font-weight: bold; margin-top: 10px; display: block; }
        input, select { width: 100%; padding: 8px; margin-top: 4px; }
        img { width: 150px; margin-top: 10px; border-radius: 6px; }
        .btn { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-primary { background: #93BAFF; color: white; }
        .btn-secondary { background: #666; color: white; text-decoration: none; padding: 10px 15px; }
    </style>
</head>

<body>

<h2 style="text-align:center;">Edit Kendaraan</h2>

<form action="../../app/kelola_logic.php" 
      method="POST" enctype="multipart/form-data">

    <input type="hidden" name="kendaraan_id" 
           value="<?= $data['kendaraan_id'] ?>">

    <!-- JENIS KENDARAAN -->
    <label>Jenis Kendaraan:</label>
    <select name="jenis_kendaraan">
        <option value="motor" <?= $data['jenis_kendaraan']=='motor' ? 'selected' : '' ?>>Motor</option>
        <option value="mobil" <?= $data['jenis_kendaraan']=='mobil' ? 'selected' : '' ?>>Mobil</option>
        <option value="lainnya" <?= $data['jenis_kendaraan']=='lainnya' ? 'selected' : '' ?>>Lainnya</option>
    </select>

    <!-- MERK -->
    <label>Merk:</label>
    <input type="text" name="merk"
           value="<?= $data['merk'] ?>">

    <!-- MODEL -->
    <label>Model:</label>
    <input type="text" name="model"
           value="<?= $data['model'] ?>">

    <!-- TAHUN -->
    <label>Tahun:</label>
    <input type="number" name="tahun"
           value="<?= $data['tahun'] ?>">

    <!-- PLAT NOMOR -->
    <label>Plat Nomor:</label>
    <input type="text" name="plat_nomor"
           value="<?= $data['plat_nomor'] ?>">

    <!-- HARGA -->
    <label>Harga Sewa / Hari:</label>
    <input type="number" name="harga_sewa_per_hari" step="0.01"
           value="<?= $data['harga_sewa_per_hari'] ?>">

    <!-- STATUS -->
    <label>Status Kendaraan:</label>
    <select name="status_kendaraan">
        <option value="tersedia" <?= $data['status_kendaraan']=='tersedia'?'selected':'' ?>>Tersedia</option>
        <option value="disewa" <?= $data['status_kendaraan']=='disewa'?'selected':'' ?>>Disewa</option>
        <option value="maintenance" <?= $data['status_kendaraan']=='maintenance'?'selected':'' ?>>Maintenance</option>
    </select>

    <!-- FOTO -->
    <label>Foto (opsional):</label>
    <input type="file" name="foto">

    <?php if (!empty($data['foto'])): ?>
        <img src="data:image/jpeg;base64,<?= base64_encode($data['foto']) ?>">
    <?php endif; ?>

    <button type="submit" name="update_kendaraan" class="btn-primary">Update</button>
</form>

</body>
</html>
