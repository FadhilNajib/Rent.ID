<?php session_start();
include '../app/auth.php';
requireLogin('customer'); // hanya bisa diakses mitra
include __DIR__ . '/../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | RENT.ID</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style/Dash_cust.css">
  <style>
    /* Extra dashboard content styles */
    .features { max-width:1100px; margin:36px auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:20px; padding:0 20px; }
    .feature { background: #fff; border-radius:12px; padding:20px; box-shadow:0 8px 24px rgba(15,15,15,0.06); }
    .feature h3{ margin:0 0 8px 0; color:#1f1140; }
    .feature p{ margin:0; color:#444; font-size:14px; }
    .cta-row{ display:flex; gap:12px; margin-top:18px; }
    .cta{ background:linear-gradient(90deg,#6c63ff,#764ba2); color:#fff; padding:10px 14px; border-radius:10px; text-decoration:none; font-weight:600; }
    /* Tidy watermark and dashboard logo (rendered in-flow to avoid overlap) */
    #wpa { display: none; }
    /* reduce bottom padding now that logo is in the flow */
    body.dashboard-page { padding-bottom: 48px; }

    /* Logo container placed in normal document flow below the features */
    .dashboard-logo { display:flex; justify-content:center; margin: 22px 0 48px; }
    .dashboard-logo img { width: 140px; max-width: 36vw; height: auto; display:block; }

    @media (max-width: 1024px) {
      .dashboard-logo img { width: 120px; }
    }
    @media (max-width: 600px) {
      .dashboard-logo img { width: 96px; max-width: 28vw; }
    }
  </style>
  <link rel="icon" type="image/png" sizes="32x32" href="asset/icon.png">
</head>
<body class="dashboard-page">
<?php include 'navbar.php'; ?>


  <!-- Content -->
  <div class="content">
    <h1>Hey there!</h1>
    <h2>Glad to have you back <span class="emoji">👋</span></h2>
    <p>Ready to rent, manage, or explore? Let’s make your rental experience easier and faster today!</p>
  </div>

  <!-- New: benefits / features section -->
  <section class="features" aria-label="Keuntungan menggunakan Rent.ID">
    <div class="feature">
      <h3>1. Pilihan Kendaraan Beragam</h3>
      <p>Nikmati koleksi kendaraan lengkap — motor, mobil, dan alat transportasi lainnya dari mitra terpercaya.</p>
      <div class="cta-row">
        <a class="cta" href="cust/vechiles_user.php">Jelajahi Kendaraan</a>
      </div>
    </div>

    <div class="feature">
      <h3>2. Pemesanan & Pembayaran Mudah</h3>
      <p>Pilih tanggal, metode pembayaran, dan konfirmasi cepat. Lacak status pembayaran langsung di dashboardmu.</p>
      <div class="cta-row">
        <a class="cta" href="cust/my_rentals.php">Lihat Sewa Saya</a>
      </div>
    </div>

    <div class="feature">
      <h3>3. Perlindungan & Kepercayaan</h3>
      <p>Mitras terverifikasi dan sistem konfirmasi memastikan transaksi aman untuk penyewa dan pemilik.</p>
      <div class="cta-row">
        <a class="cta" href="settings.php">Kelola Akun</a>
      </div>
    </div>
  </section>

  <!-- watermark removed to avoid showing session info on the page -->

  <!-- Dashboard logo (in-flow) -->
  <div class="dashboard-logo" aria-hidden="true">
    <img src="asset/logo.png" alt="RENT.ID Logo" />
  </div>

</body>
</html>
