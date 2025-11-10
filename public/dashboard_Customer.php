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
  <link rel="icon" type="image/png" sizes="32x32" href="asset/icon.png">
</head>
<body>

  <!-- Navbar -->
  <nav>
    <ul>
      <li><a href="#">Profile</a></li>
      <li><a href="#" class="active">Dashboard</a></li>
      <li><a href="cust/vechiles_user.php">Vehicles</a></li>
      <li><a href="#">My Rentals</a></li>
      <li><a href="#">Settings</a></li>
    </ul>
    <button class="logout-btn" onclick="window.location.href='logout.php'">
      Logout
    </button>
    </nav>

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
