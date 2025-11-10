<?php
session_start();
include __DIR__ . '/../navbar.php';

include '../../app/auth.php';
requireLogin('mitra');?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kendaraan | Rent.ID</title>
    <link rel="stylesheet" href="style/Dash_mitra.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        h1 { text-align: center; color: #333; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, select {
            width: 100%; padding: 8px; margin-top: 5px;
            border: 1px solid #aaa; border-radius: 5px;
        }
        button {
            display: block; width: 100%; margin-top: 20px;
            background: #007bff; color: white; border: none;
            padding: 10px; border-radius: 5px; cursor: pointer;
        }
        button:hover { background: #0056b3; }
        .msg { text-align: center; margin-bottom: 15px; }
        .back { text-align: center; margin-top: 15px; }
        .back a { text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
<div class="container">
    <h1>Tambah Kendaraan Baru</h1>

    <?php if (isset($_GET['pesan'])): ?>
        <p class="msg"><?= htmlspecialchars($_GET['pesan']) ?></p>
    <?php endif; ?>

    <form method="POST" action="../../app/tambah_kendaraan_logic.php" enctype="multipart/form-data">
        <label for="jenis_kendaraan">Jenis Kendaraan:</label>
        <select name="jenis_kendaraan" id="jenis_kendaraan" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="motor">Motor</option>
            <option value="mobil">Mobil</option>
            <option value="lainnya">Lainnya</option>
        </select>

        <label for="merk">Merk:</label>
        <input type="text" name="merk" id="merk" placeholder="Contoh: Honda" required>

        <label for="model">Model:</label>
        <input type="text" name="model" id="model" placeholder="Contoh: Vario 125" required>

        <label for="tahun">Tahun:</label>
        <input type="number" name="tahun" id="tahun" min="2000" max="2099" placeholder="Contoh: 2023">

        <label for="plat_nomor">Plat Nomor:</label>
        <input type="text" name="plat_nomor" id="plat_nomor" placeholder="Contoh: AB1234CD" required>

        <label for="harga_sewa_per_hari">Harga Sewa per Hari (Rp):</label>
        <input type="number" name="harga_sewa_per_hari" id="harga_sewa_per_hari" min="0" step="1000" required>

        <label for="foto">Upload Foto:</label>
        <input type="file" name="foto" id="foto" accept=".jpg,.jpeg,.png,.gif,.webp">

        <button type="submit">Simpan Kendaraan</button>
    </form>

    <div class="back">
        <a href="mitra.php">← Kembali ke Dashboard</a>
    </div>
</div>
</body>
</html>
