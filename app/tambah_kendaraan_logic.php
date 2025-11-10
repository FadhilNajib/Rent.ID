<?php
include __DIR__ . '/../config/koneksi.php';

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mitra') {
    header("Location: ../login.php");
    exit;
}

$id_mitra = $_SESSION['id'];
$uploadDir = __DIR__ . '../public/uploads/'; // path absolut

// Pastikan folder uploads ada
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis = $_POST['jenis_kendaraan'];
    $merk = trim($_POST['merk']);
    $model = trim($_POST['model']);
    $tahun = $_POST['tahun'];
    $plat = strtoupper(trim($_POST['plat_nomor']));
    $harga = $_POST['harga_sewa_per_hari'];
    $fotoPath = "";

    // === Upload foto ===
    if (!empty($_FILES['foto']['name'])) {
        $fileName = basename($_FILES['foto']['name']);
        $fileTmp = $_FILES['foto']['tmp_name'];
        $fileSize = $_FILES['foto']['size'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed)) {
            if ($fileSize <= 2 * 1024 * 1024) {
                $newFileName = uniqid('kendaraan_') . '.' . $ext;
                $targetPath = $uploadDir . $newFileName;
                if (move_uploaded_file($fileTmp, $targetPath)) {
                    // Simpan path relatif
                    $fotoPath = 'app/uploads/' . $newFileName;
                } else {
                    header("Location: ../public/mitra/tambah_kendaraan.php?pesan=❌ Gagal upload foto.");
                    exit;
                }
            } else {
                header("Location: ../public/mitra//tambah_kendaraan.php?pesan=❌ File terlalu besar (maks 2MB).");
                exit;
            }
        } else {
            header("Location: ../public/mitra/tambah_kendaraan.php?pesan=❌ Format file tidak didukung.");
            exit;
        }
    }

    if (empty($jenis) || empty($merk) || empty($model) || empty($plat) || empty($harga)) {
        header("Location: ../tambah_kendaraan.php?pesan=❌ Semua kolom wajib diisi.");
        exit;
    }

    $query = "INSERT INTO kendaraan (id_mitra, jenis_kendaraan, merk, model, tahun, plat_nomor, harga_sewa_per_hari, status_kendaraan, foto_url)
              VALUES (?, ?, ?, ?, ?, ?, ?, 'tersedia', ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssssss", $id_mitra, $jenis, $merk, $model, $tahun, $plat, $harga, $fotoPath);

    if ($stmt->execute()) {
        header("Location: ../tambah_kendaraan.php?pesan=✅ Kendaraan berhasil ditambahkan!");
        exit;
    } else {
        header("Location: ../tambah_kendaraan.php?pesan=❌ Gagal menambahkan kendaraan.");
        exit;
    }
}
?>
