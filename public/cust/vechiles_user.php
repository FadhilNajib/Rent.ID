<?php
include __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../../app/auth.php';

// session_start();

// Pastikan hanya customer yang bisa akses halaman ini
requireLogin('customer');

// Ambil input pencarian (kalau ada)
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Query dasar untuk kendaraan motor yang tersedia
$query = "SELECT k.*, m.nama_mitra 
          FROM kendaraan k 
          JOIN mitra m ON k.id_mitra = m.id_mitra
          WHERE k.jenis_kendaraan = 'motor' 
          AND k.status_kendaraan = 'tersedia'";

// Kalau user mengetik di kolom pencarian
if (!empty($search)) {
    $query .= " AND (k.merk LIKE ? OR k.model LIKE ? OR m.nama_mitra LIKE ?)";
    $stmt = $conn->prepare($query);
    $searchParam = "%$search%";
    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
} else {
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Motor | Rent.ID</title>
    <!-- <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1100px;
            margin: 30px auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 50%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #aaa;
        }
        button {
            padding: 8px 15px;
            background: #007bff;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: 0.2s;
        }
        .card:hover {
            transform: scale(1.03);
        }
        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .card-content {
            padding: 15px;
        }
        .card-content h3 {
            margin: 0 0 5px;
        }
        .card-content p {
            margin: 5px 0;
            color: #555;
        }
        .btn-sewa {
            display: inline-block;
            padding: 7px 12px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-sewa:hover {
            background: #218838;
        }
    </style> -->
</head>
<body>
<div class="container">
    <h1>🛵 Daftar Motor Tersedia</h1>
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Cari motor berdasarkan merk, model, atau mitra..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Cari</button>
    </form>

    <div class="grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($row['foto_url'] ?? 'https://via.placeholder.com/300x180?text=No+Image') ?>" alt="Foto Motor">
                    <div class="card-content">
                        <h3><?= htmlspecialchars($row['merk'] . ' ' . $row['model']) ?></h3>
                        <p><strong>Tahun:</strong> <?= htmlspecialchars($row['tahun']) ?></p>
                        <p><strong>Harga Sewa:</strong> Rp<?= number_format($row['harga_sewa_per_hari'], 0, ',', '.') ?>/hari</p>
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
