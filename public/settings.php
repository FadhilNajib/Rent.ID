<?php
session_start();
include_once __DIR__ . '/../config/koneksi.php';

// Cek login
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$id   = $_SESSION['id'];
$role = $_SESSION['role'];

// Ambil data user
if ($role === 'customer') {
    $query = "SELECT id_customer AS id, nama, email, no_telepon FROM customer WHERE id_customer = ?";
} else {
    $query = "SELECT id_mitra AS id, nama_mitra AS nama, email, no_telepon FROM mitra WHERE id_mitra = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

// Handle form submission untuk update password
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $message = 'Semua field wajib diisi!';
        $message_type = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = 'Password baru tidak cocok!';
        $message_type = 'error';
    } elseif (strlen($new_password) < 6) {
        $message = 'Password minimal 6 karakter!';
        $message_type = 'error';
    } else {
        // Cek password lama
        $table = ($role === 'customer') ? 'customer' : 'mitra';
        $id_col = ($role === 'customer') ? 'id_customer' : 'id_mitra';
        
        $check_query = "SELECT password FROM $table WHERE $id_col = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result()->fetch_assoc();

        if ($result && password_verify($old_password, $result['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_query = "UPDATE $table SET password = ? WHERE $id_col = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $hashed_password, $id);
            
            if ($update_stmt->execute()) {
                $message = 'Password berhasil diperbarui!';
                $message_type = 'success';
            } else {
                $message = 'Gagal memperbarui password!';
                $message_type = 'error';
            }
        } else {
            $message = 'Password lama tidak sesuai!';
            $message_type = 'error';
        }
    }
}
?>

<?php include __DIR__ . '/navbar.php'; ?>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* HEADER */
.settings-header {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-bottom-left-radius: 50% 40%;
    border-bottom-right-radius: 50% 40%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: -40px;
    position: relative;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

.settings-title-header {
    color: white;
    font-size: 32px;
    font-weight: 700;
    text-align: center;
}

/* MAIN CONTAINER */
.settings-wrapper {
    width: 100%;
    max-width: 900px;
    margin: 100px auto 0;
    padding: 0 20px 40px;
}

/* CARD CONTAINER */
.settings-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
    padding: 40px;
    margin-bottom: 30px;
}

.settings-card-title {
    font-size: 22px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f4f8;
}

/* MESSAGE ALERT */
.message {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.message.success {
    background: #c6f6d5;
    color: #22543d;
    border-left: 4px solid #48bb78;
}

.message.error {
    background: #fed7d7;
    color: #742a2a;
    border-left: 4px solid #f56565;
}

/* FORM GROUP */
.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-group input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* BUTTON GROUP */
.button-group {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    justify-content: flex-start;
}

.btn {
    padding: 12px 32px;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #e2e8f0;
    color: #2d3748;
}

.btn-secondary:hover {
    background: #cbd5e0;
    transform: translateY(-2px);
}

/* INFO SECTION */
.settings-info {
    background: #f7fafc;
    border-left: 4px solid #667eea;
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
}

.settings-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
}

.settings-info-item:last-child {
    border-bottom: none;
}

.settings-info-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 14px;
}

.settings-info-value {
    color: #2d3748;
    font-size: 14px;
}

/* RESPONSIVE */
@media (max-width: 600px) {
    .settings-header {
        height: 160px;
        margin-bottom: -30px;
    }

    .settings-title-header {
        font-size: 24px;
    }

    .settings-wrapper {
        margin-top: 80px;
        padding: 0 15px 30px;
    }

    .settings-card {
        padding: 24px 20px;
    }

    .settings-card-title {
        font-size: 18px;
        margin-bottom: 20px;
    }

    .button-group {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}
</style>

<!-- HEADER -->
<div class="settings-header">
    <h1 class="settings-title-header">Pengaturan</h1>
</div>

<div class="settings-wrapper">
    <!-- INFORMASI AKUN -->
    <div class="settings-card">
        <h2 class="settings-card-title">Informasi Akun</h2>
        
        <div class="settings-info">
            <div class="settings-info-item">
                <span class="settings-info-label">Nama</span>
                <span class="settings-info-value"><?= htmlspecialchars($data['nama']) ?></span>
            </div>
            <div class="settings-info-item">
                <span class="settings-info-label">Email</span>
                <span class="settings-info-value"><?= htmlspecialchars($data['email']) ?></span>
            </div>
            <div class="settings-info-item">
                <span class="settings-info-label">No Telepon</span>
                <span class="settings-info-value"><?= htmlspecialchars($data['no_telepon']) ?></span>
            </div>
            <div class="settings-info-item">
                <span class="settings-info-label">Tipe Akun</span>
                <span class="settings-info-value"><?= ucfirst($role) ?></span>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-primary" onclick="alert('Fitur edit profil akan segera hadir')">Edit Informasi</button>
            <button class="btn btn-secondary" onclick="history.back()">Kembali</button>
        </div>
    </div>

    <!-- UBAH PASSWORD -->
    <div class="settings-card">
        <h2 class="settings-card-title">Keamanan - Ubah Password</h2>

        <?php if ($message): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="old_password">Password Lama</label>
                <input 
                    type="password" 
                    id="old_password" 
                    name="old_password" 
                    placeholder="Masukkan password lama"
                    required
                >
            </div>

            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input 
                    type="password" 
                    id="new_password" 
                    name="new_password" 
                    placeholder="Masukkan password baru (minimal 6 karakter)"
                    required
                >
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    placeholder="Konfirmasi password baru"
                    required
                >
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">Ubah Password</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>

    <!-- PREFERENSI -->
    <div class="settings-card">
        <h2 class="settings-card-title">Preferensi</h2>
        
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
                <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                <span>Terima notifikasi email</span>
            </label>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer;">
                <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                <span>Terima pembaruan produk</span>
            </label>
        </div>

      

        <div class="button-group">
            <button class="btn btn-primary" onclick="alert('Preferensi akan disimpan')">Simpan Preferensi</button>
        </div>
    </div>
</div>
