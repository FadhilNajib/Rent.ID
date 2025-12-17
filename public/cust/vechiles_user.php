<?php
include __DIR__ . '/../../app/auth.php';
requireLogin('customer');

include __DIR__ . '/../../app/kendaraan_cust_logic.php';

// Ambil pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Ambil data kendaraan dari BE
$kendaraan = new KendaraanCustomer();
// Ambil semua kendaraan yang tersedia (motor + mobil) — kosongkan filter jenis
$result = $kendaraan->getKendaraanTersedia($search);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Motor | Rent.ID</title>
    <style>
        /* account for fixed navbar: give page top padding so title is visible */
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0 0 0 0; padding-top: 80px; }
        .container { width: 90%; max-width: 1100px; margin: 30px auto; }
        h1 { text-align: center; color: #333; }
        form { text-align: center; margin-bottom: 20px; }
        input[type="text"] {
            width: 50%; padding: 8px; border-radius: 5px; border: 1px solid #aaa;
        }
        button { padding: 8px 15px; background: #007bff; border: none; color: white; border-radius: 5px; cursor: pointer; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .card { background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; transition: 0.2s; }
        .card:hover { transform: scale(1.03); }
        .card img { width: 100%; height: 180px; object-fit: cover; }
        .card-content { padding: 15px; }
        .btn-sewa { display: inline-block; padding: 7px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; }
        @media (max-width: 768px) {
            body { padding-top: 72px; }
            .container { margin-top: 10px; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../navbar.php'; ?>

<div class="container">
    <h1>🚗🛵 Daftar Kendaraan Tersedia</h1>

    <form method="GET">
        <input type="text" name="search" placeholder="Cari kendaraan berdasarkan merk, model, atau mitra..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Cari</button>
    </form>

    <div class="grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">

                    <?php
                        // Determine image source: prefer file in `uploads/`, otherwise if the DB contains
                        // raw binary image data (BLOB) render as data URI. Fallback to a placeholder.
                        $fotoField = $row['foto'] ?? '';
                        $imgSrc = 'https://via.placeholder.com/300x180?text=No+Image';
                        $uploadPath = __DIR__ . '/../../uploads/' . $fotoField;
                        if (!empty($fotoField) && file_exists($uploadPath)) {
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
                    <img src="<?= $imgSrc ?>" alt="Foto Kendaraan">

                    <div class="card-content">
                        <h3><?= htmlspecialchars($row['merk'] . ' ' . $row['model']) ?></h3>
                        <p><strong>Tahun:</strong> <?= htmlspecialchars($row['tahun']) ?></p>
                        <p><strong>Harga:</strong> Rp<?= number_format($row['harga_sewa_per_hari'], 0, ',', '.') ?>/hari</p>
                        <p><strong>Mitra:</strong> <?= htmlspecialchars($row['nama_mitra']) ?></p>
                        <a class="btn-sewa" href="sewa.php?id=<?= $row['kendaraan_id'] ?>">Sewa Sekarang</a>
                    </div>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">Tidak ada motor yang ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
