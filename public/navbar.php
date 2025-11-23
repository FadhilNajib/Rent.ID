<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan user sudah login (keamanan tambahan)
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$BASE_URL = "/Rent.ID/public/";
?>

<!-- ===== UNIVERSAL NAVBAR ===== -->


<nav>
  <ul>
    <?php if ($_SESSION['role'] === 'customer'): ?>
      <!-- ==== MENU CUSTOMER ==== -->
      <li><a href="<?= $BASE_URL ?>profile.php">Profile</a></li>
      <li><a href="<?= $BASE_URL ?>dashboard_Customer.php"
             class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard_Customer.php' ? 'active' : '' ?>">
          Dashboard
      </a></li>
      <li><a href="<?= $BASE_URL ?>cust/vechiles_user.php">Vehicles</a></li>
      <li><a href="<?= $BASE_URL ?>cust/my_rentals.php">My Rentals</a></li>
      <li><a href="#">Settings</a></li>

    <?php elseif ($_SESSION['role'] === 'mitra'): ?>
      <!-- ==== MENU MITRA ==== -->
      <li><a href="<?= $BASE_URL ?>profile.php">Profile</a></li>
      <li><a href="<?= $BASE_URL ?>Dashboard_Mitra.php"
             class="<?= basename($_SERVER['PHP_SELF']) === 'Dashboard_Mitra.php' ? 'active' : '' ?>">
          Dashboard
      </a></li>
      <li><a href="<?= $BASE_URL ?>mitra/rentals.php">Rentals</a></li>
      <li><a href="<?= $BASE_URL ?>mitra/kelola_kendaraan.php">Kendaraan</a></li>
      <li><a href="#">Payment</a></li>
      <li><a href="#">Reports</a></li>
      <li><a href="#">Settings</a></li>
    <?php endif; ?>
  </ul>

  <button class="logout-btn" onclick="window.location.href='/Rent.ID/app/auth.php?action=logout'">
    Logout
</button>
</nav>


<!-- ===== STYLING ===== -->
<style>
  nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #93BAFF;
    padding: 12px 25px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  }

  nav ul {
    list-style: none;
    display: flex;
    gap: 20px;
    margin: 0;
    padding: 0;
  }

  nav a {
    text-decoration: none;
    color: white;
    font-weight: 500;
    transition: color 0.2s ease, border-bottom 0.2s ease;
  }

  nav a:hover,
  nav a.active {
    color: #0c2c5d;
    border-bottom: 2px solid #0c2c5d;
    padding-bottom: 2px;
  }

  .logout-btn {
    background-color: white;
    color: #93BAFF;
    border: none;
    border-radius: 20px;
    padding: 8px 18px;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.3s ease, transform 0.2s ease;
  }

  .logout-btn:hover {
    background-color: #f3f3f3;
    transform: scale(1.05);
  }
</style>
