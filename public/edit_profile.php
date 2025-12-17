<?php
session_start();
include_once __DIR__ . '/../config/koneksi.php';

// Cek login
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$id   = (int) ($_SESSION['id'] ?? 0);
$role = $_SESSION['role'] ?? null;
$message = '';
$message_type = '';

// Ambil data user
if ($role === 'customer') {
    $query = "SELECT id_customer AS id, nama, email, no_telepon, alamat FROM customer WHERE id_customer = ?";
} else {
    $query = "SELECT id_mitra AS id, nama_mitra AS nama, email, no_telepon, alamat FROM mitra WHERE id_mitra = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    // normalize phone: remove spaces and non-digit/+ characters, keep a single leading + if present
    $raw_no = trim($_POST['no_telepon'] ?? '');
    // remove spaces, parentheses and dashes but keep digits and plus
    $no_telepon = preg_replace('/[^0-9+]/', '', $raw_no);
    // ensure only a single leading plus (remove any '+' not at start)
    $no_telepon = preg_replace('/(?!^)\+/', '', $no_telepon);
    $alamat = trim($_POST['alamat'] ?? '');

    // Validasi
    if (empty($nama) || empty($email) || empty($no_telepon) || empty($alamat)) {
        $message = 'Semua field wajib diisi!';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Format email tidak valid!';
        $message_type = 'error';
    } elseif (!preg_match('/^(\+62|62|0)[0-9]{9,12}$/', $no_telepon)) {
        $message = 'Format nomor telepon tidak valid! (contoh: 08123456789 atau +628123456789)';
        $message_type = 'error';
    } else {
        // Update database
        if ($role === 'customer') {
            $update_query = "UPDATE customer SET nama = ?, email = ?, no_telepon = ?, alamat = ? WHERE id_customer = ?";
        } else {
            $update_query = "UPDATE mitra SET nama_mitra = ?, email = ?, no_telepon = ?, alamat = ? WHERE id_mitra = ?";
        }

        $update_stmt = $conn->prepare($update_query);
        if (!$update_stmt) {
            $message = 'Gagal menyiapkan query: ' . $conn->error;
            $message_type = 'error';
        } else {
            $update_stmt->bind_param("ssssi", $nama, $email, $no_telepon, $alamat, $id);
            if ($update_stmt->execute()) {
                // check affected rows to ensure update happened
                if ($update_stmt->affected_rows > 0) {
                    $message = 'Profil berhasil diperbarui!';
                    $message_type = 'success';
                    // Refresh data from DB
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $data = $stmt->get_result()->fetch_assoc();
                } else {
                    // No rows changed: could be because submitted values equal existing values
                    $message = 'Tidak ada perubahan yang disimpan (mungkin nilai baru sama dengan yang lama). Jika kolom `no_telepon` berjenis INT, nomor panjang dapat dipotong — pertimbangkan mengubah tipe kolom menjadi VARCHAR(20).';
                    $message_type = 'error';
                }
            } else {
                $message = 'Gagal memperbarui profil: ' . $update_stmt->error;
                $message_type = 'error';
            }
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
.edit-profile-header {
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

.edit-profile-title-header {
    color: white;
    font-size: 32px;
    font-weight: 700;
    text-align: center;
}

/* MAIN CONTAINER */
.edit-profile-wrapper {
    width: 100%;
    max-width: 700px;
    margin: 100px auto 0;
    padding: 0 20px 40px;
}

/* CARD */
.edit-profile-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
    padding: 40px;
}

.edit-profile-card-title {
    font-size: 22px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f4f8;
}

/* MESSAGE */
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

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

/* BUTTON GROUP */
.button-group {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    justify-content: center;
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

/* HELPER TEXT */
.helper-text {
    font-size: 12px;
    color: #718096;
    margin-top: 4px;
}

/* RESPONSIVE */
@media (max-width: 600px) {
    .edit-profile-header {
        height: 160px;
        margin-bottom: -30px;
    }

    .edit-profile-title-header {
        font-size: 24px;
    }

    .edit-profile-wrapper {
        margin-top: 80px;
        padding: 0 15px 30px;
    }

    .edit-profile-card {
        padding: 24px 20px;
    }

    .edit-profile-card-title {
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
<div class="edit-profile-header">
    <h1 class="edit-profile-title-header">Edit Profil</h1>
</div>

<div class="edit-profile-wrapper">
    <div class="edit-profile-card">
        <h2 class="edit-profile-card-title">Perbarui Informasi Profil Anda</h2>

        <?php if ($message): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input 
                    type="text" 
                    id="nama" 
                    name="nama" 
                    value="<?= htmlspecialchars($data['nama']) ?>"
                    placeholder="Masukkan nama lengkap Anda"
                    required
                    maxlength="100"
                >
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($data['email']) ?>"
                    placeholder="Masukkan email Anda"
                    required
                    maxlength="100"
                >
                <div class="helper-text">Gunakan email yang valid untuk keamanan akun</div>
            </div>

            <div class="form-group">
                <label for="no_telepon">Nomor Telepon</label>
                <input 
                    type="tel" 
                    id="no_telepon" 
                    name="no_telepon" 
                    value="<?= htmlspecialchars($data['no_telepon']) ?>"
                    placeholder="Contoh: 08123456789 atau +628123456789"
                    required
                    maxlength="15"
                >
                <div class="helper-text">Format: 08123456789 atau +628123456789 (tanpa spasi atau tanda '-')</div>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea 
                    id="alamat" 
                    name="alamat" 
                    placeholder="Masukkan alamat lengkap Anda"
                    required
                    maxlength="255"
                ><?= htmlspecialchars($data['alamat']) ?></textarea>
                <div class="helper-text">Sertakan jalan, nomor, kelurahan, kecamatan, kota, dan kode pos</div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="profile.php" class="btn btn-secondary" style="text-align: center;">Batal</a>
            </div>
        </form>
    </div>
</div>
