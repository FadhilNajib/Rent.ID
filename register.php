<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi input kosong
    if (empty($role) || empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "Semua kolom wajib diisi.";
        exit;
    }

    // Cek apakah password dan konfirmasi sama
    if ($password !== $confirm_password) {
        echo "Password dan Ulangi Password tidak cocok.";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($role === 'mitra') {
        $stmt = $conn->prepare("INSERT INTO mitra (nama_mitra, password, email) VALUES (?, ?, ?)");
    } elseif ($role === 'customer') {
        $stmt = $conn->prepare("INSERT INTO customer (nama, password, email, tanggal_daftar) VALUES (?, ?, ?, NOW())");
    } else {
        echo "Role tidak valid.";
        exit;
    }

    $stmt->bind_param("sss", $nama, $hashed_password, $email);

    if ($stmt->execute()) {
        header("Location: login.php");
        // echo "Registrasi berhasil! <a href='login.php'>Login di sini</a>";
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

    <label>Password:</label>
    <input type="password" id="password" name="password" required><br>

    <label>Ulangi Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" required><br>

    <input type="checkbox" id="showPassword" onclick="togglePassword()"> Tampilkan Password
    <br><br>

    <button type="submit">Daftar</button>

    <div class="register-link">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
</form>

<script>
function togglePassword() {
    const password = document.getElementById("password");
    const confirm = document.getElementById("confirm_password");
    const checkbox = document.getElementById("showPassword");
    const type = checkbox.checked ? "text" : "password";
    password.type = type;
    confirm.type = type;
}
</script>
