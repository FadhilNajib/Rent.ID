<?php 
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mitra') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';
$mitra_id = $_SESSION['id_mitra'];

// jumlah penyewa 
$sq1 = $conn->query("
  SELECT COUNT(DISTINCT t.id_customer) AS total
  FROM Transaksi t
  JOIN Kendaraan k ON t.kendaraan_id = k.kendaraan_id
  WHERE k.id_mitra = $mitra_id
");
$jumlah_penyewa = $sq1->fetch_assoc()['total'];

// Kendaraan disewa
$q2 = $conn->query("
  SELECT COUNT(*) AS total
  FROM Kendaraan
  WHERE id_mitra = $mitra_id
  AND status_kendaraan = 'disewa'
");
$kendaraan_diseewa = $q2->fetch_assoc()['total'];

// Transaksi aktif
$q3 = $conn->query("
  SELECT COUNT(*) AS total
  FROM Transaksi t
  JOIN Kendaraan k ON t.kendaraan_id = k.kendaraan_id
  WHERE k.id_mitra = $mitra_id
  AND t.status_rental = 'aktif'
");
$transaksi_aktif = $q3->fetch_assoc()['total'];
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | RENT.ID</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style/Dash_mitra.css">
  <link rel="icon" type="image/png" sizes="32x32" href="asset/icon.png">
</head>
<body>

  <!-- Navbar -->
  <nav>
    <ul>
      <li><a href="#">Profile</a></li>
      <li><a href="#" class="active">Dashboard</a></li>
      <li><a href="#">Rentals</a></li>
      <li><a href="#">Customer</a></li>
      <li><a href="#">Payment</a></li>
      <li><a href="#">Reports</a></li>
      <li><a href="#">Settings</a></li>
    </ul>
    <button class="logout-btn" onclick="window.location.href='logout.php'">
      Logout
    </button>

  </nav>

  <!-- Content -->
  <div class="container">
    <div class="welcome">
      <h1>Welcome to your<br><span>Dashboard!</span></h1>
      <p>
        <strong>Manage all your rentals, track payments, and stay on top of</strong>
        <span><strong>every transaction — all in one place.</strong></span>
      </p>
    </div>

    <div class="stats">
      <div class="stat-item">
        <p class="label">Jumlah Penyewa</p>
        <div class="oval">
          <h3><?=$jumlah_penyewa?></h3>
        </div>
      </div>

      <div class="stat-item">
        <p class="label">Kendaraan Disewakan</p>
        <div class="oval">
          <h3><?=$kendaraan_diseewa?></h3>
        </div>
      </div>

      <div class="stat-item">
        <p class="label">Transaksi Aktif</p>
        <div class="oval">
          <h3><?=$transaksi_aktif?></h3>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer -->
  <footer>
    <img src="asset/logo.png" alt="Logo RENT.ID" />
  </footer>
</body>
</html>
