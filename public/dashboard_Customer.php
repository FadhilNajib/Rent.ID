<?php session_start();
include '../app/auth.php';
requireLogin('customer'); // hanya bisa diakses mitra
include __DIR__ . '/../config/koneksi.php';
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | RENT.ID</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style/Dash_cust.css">
  <link rel="icon" type="image/png" sizes="32x32" href="asset/icon.png">
</head>
<body>


  <!-- Content -->
  <div class="content">
    <h1>Hey there!</h1>
    <h2>Glad to have you back <span class="emoji">👋</span></h2>
    <p>Ready to rent, manage, or explore? Let’s make your rental experience easier and faster today!</p>
  </div>

  <!-- Footer -->
  <footer>
    <img src="asset/logo.png" alt="RENT.ID Logo" />
  </footer>

</body>
</html>
