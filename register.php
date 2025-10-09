<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_telepon = trim($_POST['no_telepon']);
    $alamat = trim($_POST['alamat']);
    $password = trim($_POST['password']);

    if (empty($role) || empty($nama) || empty($email) || empty($no_telepon) || empty($alamat) || empty($password)) {
        echo "Semua kolom wajib diisi.";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($role === 'mitra') {
        $stmt = $conn->prepare("INSERT INTO mitra (nama_mitra, password, email, no_telepon, alamat) VALUES (?, ?, ?, ?, ?)");
    } elseif ($role === 'customer') {
        $stmt = $conn->prepare("INSERT INTO customer (nama, password, email, no_telepon, alamat, tanggal_daftar) VALUES (?, ?, ?, ?, ?, NOW())");
    } else {
        echo "Role tidak valid.";
        exit;
    }

    $stmt->bind_param("sssss", $nama, $hashed_password, $email, $no_telepon, $alamat);

    if ($stmt->execute()) {
        echo "Registrasi berhasil! <a href='login.php'>Login di sini</a>";
    } else {
        echo "Terjadi kesalahan: " . $conn->error;
    }

    $stmt->close();
}
?>

<form method="POST" action="">
    <label>Role:</label>
    <select name="role" required>
        <option value="">-- Pilih Role --</option>
        <option value="mitra">Mitra</option>
        <option value="customer">Customer</option>
    </select><br>

    <label>Nama:</label>
    <input type="text" name="nama" required><br>

    <label>Email:</label>
    <input type="email" name="email" required><br>

    <label>No. Telepon:</label>
    <input type="text" name="no_telepon" required><br>

    <label>Alamat:</label>
    <textarea name="alamat" required></textarea><br>

    <label>Password:</label>
    <input type="password" id="password" name="password" required>
    <input type="checkbox" id="showPassword" onclick="togglePassword()"> Tampilkan Password
    <br><br>

    <button type="submit">Daftar</button>

    <div class="register-link">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
</form>

<script>
function togglePassword() {
    const passwordField = document.getElementById("password");
    const checkbox = document.getElementById("showPassword");
    passwordField.type = checkbox.checked ? "text" : "password";
}
</script>
