<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Jangan redirect di navbar — biarkan halaman itu yang memutuskan akses.
// Hanya baca role jika ada dan bangun base URL yang selalu mengarah ke folder `/public/`.
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$posPublic = strpos($scriptPath, '/public/');
if ($posPublic !== false) {
  $BASE_URL = substr($scriptPath, 0, $posPublic + strlen('/public/'));
} else {
  // fallback: gunakan dirname dari script
  $BASE_URL = rtrim(dirname($scriptPath), '/') . '/';
}
$role = $_SESSION['role'] ?? null;
$isDashboard = basename($_SERVER['SCRIPT_NAME']) === 'dashboard_Customer.php';
// derive a page-specific class like `page-profile` or `page-settings`
$scriptName = basename($_SERVER['SCRIPT_NAME']);
$pageName = pathinfo($scriptName, PATHINFO_FILENAME);
$pageClass = 'page-' . preg_replace('/[^a-zA-Z0-9_\-]/', '', $pageName);
?>

<!-- ===== UNIVERSAL NAVBAR ===== -->
<nav class="navbar navbar-expand-md <?= trim(($isDashboard ? 'dashboard-theme ' : '') . $pageClass) ?>">
  <div class="nav-left">
    <div class="brand">
      <img src="<?= $BASE_URL ?>asset/logo.png" alt="RENT.ID" />
      <span class="brand-name">RENT.ID</span>
      <?php if (isset($_SESSION['nama']) && trim($_SESSION['nama']) !== ''):
        $__nav_name = trim($_SESSION['nama']);
        $__nav_initial = mb_strtoupper(mb_substr($__nav_name, 0, 1));
      ?>
        <span class="nav-user" title="<?= htmlspecialchars($__nav_name) ?>">
          <span class="nav-avatar"><?= htmlspecialchars($__nav_initial) ?></span>
          <span class="nav-name"><?= htmlspecialchars($__nav_name) ?></span>
        </span>
      <?php endif; ?>
    </div>
  </div>
  <ul class="nav-menu" id="navMenu">
    <?php if (!$role): ?>
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'login.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>login.php">
          Login
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'register.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>register.php">
          Register
        </a>
      </li>
    <?php else: ?>
      <?php if ($role === 'customer'): ?>
      <!-- ==== MENU CUSTOMER ==== -->
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'profile.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>profile.php">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'dashboard_Customer.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>dashboard_Customer.php">Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'vechiles_user.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>cust/vechiles_user.php">Vehicles</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'my_rentals.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>cust/my_rentals.php">My Rentals</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'settings.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>settings.php">Settings</a>
      </li>

      <?php elseif ($role === 'mitra'): ?>
      <!-- ==== MENU MITRA ==== -->
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'profile.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>profile.php">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'dashboard_Mitra.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>dashboard_Mitra.php">Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'rentals.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>mitra/rentals.php">Rentals</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'kelola_kendaraan.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>mitra/kelola_kendaraan.php">Kendaraan</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'settings.php' ? 'active' : '' ?>" href="<?= $BASE_URL ?>settings.php">Settings</a>
      </li>
      <?php endif; ?>
    <?php endif; ?>
  </ul>

  <?php if ($role): ?>
    <button class="logout-btn btn btn-outline-light" onclick="window.location.href='<?= $BASE_URL ?>logout.php'">
      Logout
    </button>
  <?php endif; ?>
</nav>


<!-- Navbar styling moved to `public/style/navbar.css` to keep nav appearance consistent. -->
<script>
  (function(){
      try{
        // Inject Bootstrap 5 CSS first so site styles can build on it
        var bs = document.createElement('link');
        bs.rel = 'stylesheet';
        bs.href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css';
        bs.integrity = 'sha384-6m2bQ5fZQbXfDq1xO1H8Pq9p6mZQ2X1q9a8qv2q1r1p6s3k4b2m1j0p8z7y6x5w4';
        bs.crossOrigin = 'anonymous';
        document.head.appendChild(bs);

        // Add canonical navbar CSS
        var cssLink = document.createElement('link');
        cssLink.rel = 'stylesheet';
        cssLink.href = '<?= $BASE_URL ?>style/navbar.css';
        document.head.appendChild(cssLink);

        // Add a small site stylesheet (site-wide helpers / overrides)
        var s = document.createElement('link');
        s.rel = 'stylesheet';
        s.href = '<?= $BASE_URL ?>style/site.css';
        document.head.appendChild(s);
      } catch(e) { /* ignore */ }
  })();
</script>

<!-- Inject Bootstrap JS bundle (Popper included) at end of body via a small script -->
<script>
  (function(){
    try{
      var s = document.createElement('script');
      s.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js';
      s.integrity = 'sha384-3y5X1k8m2a4b6c7d8e9f0g1h2i3j4k5l6m7n8o9p0q1r2s3t4u5v6w7x8y9z0a1b';
      s.crossOrigin = 'anonymous';
      document.body.appendChild(s);
    }catch(e){/* ignore */}
  })();
</script>

<!-- PWA: dynamically add manifest and register service worker -->
<script>
  (function(){
    // inject manifest link into head
    try{
      var manifestLink = document.createElement('link');
      manifestLink.rel = 'manifest';
      manifestLink.href = '<?= $BASE_URL ?>manifest.json';
      document.head.appendChild(manifestLink);

      var meta = document.createElement('meta');
      meta.name = 'theme-color';
      meta.content = '#6c63ff';
      document.head.appendChild(meta);

      if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('<?= $BASE_URL ?>service-worker.js')
          .then(function(reg){
            // console.log('SW registered', reg);
          }).catch(function(err){
            // console.log('SW register failed', err);
          });
      }
    } catch(e) {
      // ignore in older browsers
    }
  })();
</script>

<script>
  // Ensure consistent page layout: add top padding to body equal to navbar height
  (function(){
    function applyBodyPadding(){
      var nav = document.querySelector('nav');
      if (!nav) return;
      var h = nav.offsetHeight;
      // For profile/settings pages we prefer the header to visually connect with the nav
      // so we don't add extra top padding (the page header will sit below the fixed nav).
        if (nav.classList.contains('page-profile') || nav.classList.contains('page-settings') || nav.classList.contains('page-edit_profile')) {
          // keep profile/settings visually connected to nav
          document.body.style.paddingTop = '0px';
        } else {
          // only apply padding to body (not to the html element) so the nav sits flush at the very top
          document.body.style.paddingTop = h + 'px';
        }
    }
    // run on load and resize (debounced)
    var resizeTimer;
    window.addEventListener('load', applyBodyPadding);
    window.addEventListener('resize', function(){ clearTimeout(resizeTimer); resizeTimer = setTimeout(applyBodyPadding, 120); });
  })();
</script>
