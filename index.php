<?php
include 'config/koneksi.php';

// Query ambil data kendaraan tersedia
$sql = "SELECT k.*, m.nama_mitra 
        FROM kendaraan k
        JOIN mitra m ON k.id_mitra = m.id_mitra
        WHERE k.status_kendaraan = 'tersedia'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENT.ID | Rental Kendaraan</title>
    <link rel="icon" type="image/png" sizes="32x32" href="asset/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f8ff;
            color: #333;
        }

        header {
            background: url('public/asset/BG_PROJEK.jpg') no-repeat center center/cover;
            color: white;
            text-align: center;
            padding: 40px 0;
        }

        header h1 {
            font-size: 72px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        header p {
            font-size: 20px;
            margin-top: 10px;
        }

        /* Tombol login */
        .btn-login {
            background-color: #fff;
            color: #93BAFF;
            border: none;
            border-radius: 30px;
            padding: 12px 50px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s ease;
            margin-top: 20px;
        }

        .btn-login:hover {
            background-color: #f3f3f3;
            transform: scale(1.05);
        }

        /* Daftar kendaraan */
        .vehicles {
            padding: 60px 10%;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .vehicles h2 {
            text-align: center;
            color: #2d4fa1;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .vehicle-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            align-items: center;
        }

        .vehicle-card {
            background: #fff9f9ff;
            border-radius: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
            width: 250px;
            transition: all 0.3s ease;
        }

        .vehicle-card:hover {
            transform: translateY(-5px);
        }

        .vehicle-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 10px;
            background: #f0f0f0;
        }

        .vehicle-card h4 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .vehicle-card p {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .price {
            font-weight: 700;
            color: #2d4fa1;
        }

        .status {
            font-weight: 600;
            margin-top: 5px;
        }

        .status.available {
            color: green;
        }

        .status.unavailable {
            color: red;
        }

        .no-vehicle {
            text-align: center;
            font-size: 18px;
            color: #555;
            font-weight: 500;
            padding: 80px 0;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        /* Tentang Kami */
        .about {
            padding: 60px 10%;
            background: #e9f0ff;
            text-align: center;
        }

        .about h2 {
            color: #2d4fa1;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .about p {
            font-size: 17px;
            line-height: 1.7;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Kontak */
        .contact {
            padding: 60px 10%;
            background: white;
            text-align: center;
        }

        .contact h2 {
            color: #2d4fa1;
            margin-bottom: 20px;
        }

        .contact p {
            font-size: 16px;
            line-height: 1.6;
        }

        /* Footer */
        footer {
            background: #93BAFF;
            color: white;
            text-align: center;
            padding: 20px 0;
            font-size: 15px;
        }

        footer a {
            color: white;
            font-weight: 600;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header>
        <h1>RENT.<span style="color:#fff;">ID</span></h1>
        <p>Solusi transportasi praktis, cepat, dan terpercaya sesuai kebutuhan Anda.</p>
        <a href="public/login.php" class="btn-login">LOGIN</a>
    </header>

    <!-- Daftar Kendaraan -->
    <section class="vehicles">
        <h2>Kendaraan Tersedia</h2>
        <div class="vehicle-grid">
            <?php
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    // 🔹 Konversi foto LONGBLOB ke Base64
                    if (!empty($row['foto'])) {
                        $fotoData = base64_encode($row['foto']);
                        $mimeType = 'image/jpeg';
                        $fotoSrc = "data:$mimeType;base64,$fotoData";
                    } else {
                        $fotoSrc = 'asset/default.jpg';
                    }
            ?>
                <div class="vehicle-card">
                    <img src="<?= $fotoSrc ?>" alt="<?= htmlspecialchars($row['merk']) ?>">
                    <h4><?= htmlspecialchars(strtoupper($row['nama_mitra'])) ?></h4>
                    <p><?= htmlspecialchars($row['merk'] . " " . $row['model']) ?></p>
                    <p class="price">Rp<?= number_format($row['harga_sewa_per_hari'], 0, ',', '.') ?> / day</p>
                    <p class="status available"><?= ucfirst($row['status_kendaraan']) ?></p>
                </div>
            <?php
                endwhile;
            else:
            ?>
                <div class="no-vehicle">
                    <p>Belum ada kendaraan tersedia<br>untuk saat ini.</p>
                </div>
            <?php
            endif;
            $conn->close();
            ?>
        </div>
    </section>

    <!-- Tentang Kami -->
    <section class="about" id="about">
        <h2>Tentang Kami</h2>
        <p>
            Rent.id adalah platform penyewaan kendaraan digital yang menghubungkan pelanggan dengan berbagai mitra rental terpercaya di seluruh Indonesia.
            Kami berkomitmen untuk memberikan pengalaman rental yang cepat, aman, dan efisien dengan sistem pembayaran yang fleksibel dan layanan pelanggan 24 jam.
        </p>
    </section>

    <!-- Kontak -->
    <section class="contact" id="contact">
        <h2>Kontak Kami</h2>
        <p>
            📍 Alamat: Jl. Teknologi No. 7, Sleman, Yogyakarta <br>
            📞 Telepon: +62 812-3456-7890 <br>
            📧 Email: support@rent.id
        </p>
    </section>

    <!-- Footer -->
    <footer>
        <p>© 2025 Rent.id — Semua hak cipta dilindungi.</p>
    </footer>

</body>
</html>
