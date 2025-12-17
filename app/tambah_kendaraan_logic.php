<?php
include __DIR__ . '/../config/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mitra') {
    header("Location: ../login.php");
    exit;
}

$id_mitra = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis = $_POST['jenis_kendaraan'];
    $merk = trim($_POST['merk']);
    $model = trim($_POST['model']);
    $tahun = $_POST['tahun'];
    $plat = strtoupper(trim($_POST['plat_nomor']));
    $harga = $_POST['harga_sewa_per_hari'];

    if (empty($jenis) || empty($merk) || empty($model) || empty($plat) || empty($harga)) {
        header("Location: ../public/mitra/tambah_kendaraan.php?pesan=❌ Semua kolom wajib diisi.");
        exit;
    }

    $fotoBlob = null;
    if (!empty($_FILES['foto']['tmp_name'])) {
        $fotoBlob = file_get_contents($_FILES['foto']['tmp_name']);
    }

    $query = "INSERT INTO kendaraan (
        id_mitra, jenis_kendaraan, merk, model, tahun,
        plat_nomor, harga_sewa_per_hari, status_kendaraan, foto
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'tersedia', ?)";

    $stmt = $conn->prepare($query);

    $null = null;
    $stmt->bind_param(
        "isssissb",
        $id_mitra,
        $jenis,
        $merk,
        $model,
        $tahun,
        $plat,
        $harga,
        $null
    );

    if ($fotoBlob !== null) {
        $stmt->send_long_data(7, $fotoBlob);
    }

    try {
        $stmt->execute();
        header("Location: ../public/mitra/tambah_kendaraan.php?pesan=✅ Kendaraan berhasil ditambahkan!");
        exit;

    } catch (mysqli_sql_exception $e) {

        // Duplicate plat nomor
        if ($e->getCode() == 1062) {
            header("Location: ../public/mitra/tambah_kendaraan.php?pesan=❌ Plat nomor sudah terdaftar!");
            exit;
        }

        // Error lain
        header("Location: ../public/mitra/tambah_kendaraan.php?pesan=❌ Terjadi kesalahan sistem.");
        exit;
    }
}
?>
