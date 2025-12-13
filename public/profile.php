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

if ($role === 'customer') {
    $query = "SELECT id_customer AS id, nama, email, no_telepon, alamat, tanggal_daftar 
              FROM customer WHERE id_customer = ?";
} else {
    $query = "SELECT id_mitra AS id, nama_mitra AS nama, email, no_telepon, alamat 
              FROM mitra WHERE id_mitra = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
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

/* HEADER MELENGKUNG */
.profile-header {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-bottom-left-radius: 50% 40%;
    border-bottom-right-radius: 50% 40%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: -80px;
    position: relative;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

/* AVATAR */
.profile-avatar {
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, #dbe6ff 0%, #e5effe 100%);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 60px;
    color: #667eea;
    margin: 0 auto;
    font-weight: bold;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    border: 5px solid #ffffff;
    position: relative;
    z-index: 10;
}

/* WRAPPER */
.profile-wrapper {
    padding: 0 20px;
    margin-bottom: 40px;
}

/* CARD PROFIL */
.profile-container {
    width: 100%;
    max-width: 700px;
    margin: 120px auto 0;
    padding: 40px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
}

/* NAMA */
.profile-title {
    text-align: center;
    margin-bottom: 35px;
    font-size: 28px;
    font-weight: 700;
    color: #2d3748;
    letter-spacing: 0.5px;
}

/* DIVIDER */
.profile-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, #e2e8f0, transparent);
    margin-bottom: 30px;
}

/* ROW */
.profile-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid #f0f4f8;
    transition: all 0.3s ease;
}

.profile-row:last-child {
    border-bottom: none;
}

.profile-row:hover {
    background: rgba(102, 126, 234, 0.05);
    padding-left: 12px;
    padding-right: 12px;
    margin: 0 -12px;
    border-radius: 8px;
}

.profile-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.profile-value {
    text-align: right;
    color: #2d3748;
    font-size: 15px;
    font-weight: 500;
    word-break: break-word;
}

/* BUTTON EDIT */
.profile-button-group {
    display: flex;
    gap: 12px;
    margin-top: 35px;
    justify-content: center;
}

.profile-btn {
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

.btn-edit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.btn-back {
    background: #e2e8f0;
    color: #2d3748;
}

.btn-back:hover {
    background: #cbd5e0;
    transform: translateY(-2px);
}

/* RESPONSIVE */
@media (max-width: 600px) {
    .profile-header {
        height: 160px;
        margin-bottom: -60px;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        font-size: 48px;
    }

    .profile-container {
        max-width: 100%;
        margin: 90px 20px 0;
        padding: 30px 20px;
    }

    .profile-title {
        font-size: 22px;
        margin-bottom: 25px;
    }

    .profile-row {
        flex-direction: column;
        align-items: flex-start;
        padding: 12px 0;
    }

    .profile-value {
        text-align: left;
        margin-top: 6px;
    }

    .profile-button-group {
        flex-direction: column;
        gap: 10px;
    }

    .profile-btn {
        width: 100%;
        padding: 14px 20px;
    }
}
</style>

<!-- HEADER MELENGKUNG -->
<div class="profile-header">
</div>

<!-- AVATAR -->
<div class="profile-avatar">
    <?= strtoupper(substr($data['nama'], 0, 1)) ?>
</div>

<div class="profile-container">
    <div class="profile-title"><?= $data['nama'] ?></div>

    <div class="profile-row">
        <div class="profile-label">ID:</div>
        <div class="profile-value"><?= $data['id'] ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">Email:</div>
        <div class="profile-value"><?= $data['email'] ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">No Telepon:</div>
        <div class="profile-value"><?= $data['no_telepon'] ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">Alamat:</div>
        <div class="profile-value"><?= $data['alamat'] ?></div>
    </div>

    <?php if ($role === 'customer'): ?>
        <div class="profile-row">
            <div class="profile-label">Tanggal Daftar:</div>
            <div class="profile-value"><?= $data['tanggal_daftar'] ?></div>
        </div>
    <?php endif; ?>

    <div class="profile-button-group">
        <a href="edit_profile.php" class="profile-btn btn-edit">Edit Profil</a>
        <button class="profile-btn btn-back" onclick="history.back()">Kembali</button>
    </div>
</div>
