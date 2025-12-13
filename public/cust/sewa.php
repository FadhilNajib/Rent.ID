<?php
include __DIR__ . '/../../app/auth.php';
requireLogin('customer');

include __DIR__ . '/../../app/kendaraan_cust_logic.php';

if (!isset($_GET['id'])) {
    die("Kendaraan tidak ditemukan.");
}

$kendaraanId = $_GET['id'];
$kendaraan = new KendaraanCustomer();
$data = $kendaraan->getDetailKendaraan($kendaraanId);

if (!$data) {
    die("Data kendaraan tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'];
    $metodeBayar = $_POST['metode_pembayaran'];

    // VALIDASI TANGGAL
    if ($mulai > $selesai) {
        echo "<script>alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai');</script>";
    } else {

        // hitung hari pakai DateTime supaya pasti benar
        $start = new DateTime($mulai);
        $end   = new DateTime($selesai);

        $diff  = $start->diff($end)->days;
        $hari  = ($diff >= 1) ? $diff : 1; // minimal 1 hari

        $total = $hari * $data['harga_sewa_per_hari'];

        // Buat transaksi
        $rentalId = $kendaraan->buatTransaksi($_SESSION['id'], $kendaraanId, $mulai, $selesai, $total);

        if ($rentalId) {

            // LOGIC PEMBAYARAN
            if ($metodeBayar === "COD") {
                // Pending (mitra yang akan konfirmasi) — fungsi akan set status sendiri
                $kendaraan->buatPembayaran($rentalId, "COD", $total);

            } else {
                // Transfer atau e-wallet → langsung sukses
                $kendaraan->buatPembayaran($rentalId, $metodeBayar, $total);
            }

                // Redirect user to their My Rentals page so they can see the booking immediately
                // Server-side redirect is more reliable than client JS (avoids race/JS-blocking issues)
                header('Location: my_rentals.php');
                exit;

        } else {
            echo "<script>alert('Gagal membuat transaksi.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sewa Kendaraan - Rent.id</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background:#fafafa; margin:0; padding:24px; }
        .container { max-width:1000px; margin:20px auto; background:#fff; border-radius:12px; box-shadow:0 8px 30px rgba(15,15,15,0.08); overflow:hidden; }
        .sewa-top { background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; padding:18px 24px; }
        .sewa-body { padding:20px 24px; display:flex; gap:24px; flex-wrap:wrap; }
        .vehicle-card { flex:1 1 320px; min-width:260px; }
        .vehicle-card h2 { margin:0 0 8px 0; font-size:20px; }
        .vehicle-meta { color:#333; margin-bottom:12px; }
        .price { font-weight:700; color:#2b2b2b; }
        form .field { margin-bottom:12px; }
        label { display:block; font-size:14px; margin-bottom:6px; color:#222; }
        input[type=date], select { width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:8px; }
        .actions { margin-top:12px; }
        .btn { display:inline-block; background:#6c63ff; color:#fff; padding:10px 16px; border-radius:8px; border:none; cursor:pointer; font-weight:600; }
        .btn.secondary { background:#fff; color:#333; border:1px solid #ddd; }
        @media (max-width:720px) { .sewa-body { flex-direction:column; } }
    </style>
</head>
<body>

<?php include __DIR__ . '/../navbar.php'; ?>

<div class="container">
    <div class="sewa-top">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <div style="opacity:0.95; font-size:13px;">Sewa</div>
                <h1 style="margin:6px 0 0; font-size:20px"><?= htmlspecialchars($data['merk'] . ' ' . $data['model']) ?></h1>
            </div>
            <div style="text-align:right">
                <div style="font-size:13px;opacity:0.9">Mitra</div>
                <div style="font-weight:600; margin-top:4px"><?= htmlspecialchars($data['nama_mitra']) ?></div>
            </div>
        </div>
    </div>

    <div class="sewa-body">
        <div class="vehicle-card">
            <p class="vehicle-meta"><span class="price">Rp<?= number_format($data['harga_sewa_per_hari']) ?></span> / hari</p>
            <?php if (!empty($data['gambar'])): ?>
                <img src="<?= htmlspecialchars($BASE_URL . 'uploads/' . $data['gambar']) ?>" alt="<?= htmlspecialchars($data['merk']) ?>" style="width:100%;border-radius:8px;margin-bottom:12px;">
            <?php endif; ?>
            <p style="color:#555;">Deskripsi singkat kendaraan jika tersedia.</p>
        </div>

        <div style="flex:0 0 320px;">
            <form method="POST">
                <div class="field">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" required>
                </div>

                <div class="field">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" required>
                </div>

                <div class="field">
                    <label for="metode_pembayaran">Metode Pembayaran</label>
                    <select id="metode_pembayaran" name="metode_pembayaran" required>
                        <option value="transfer_bank">Transfer Bank</option>
                        <option value="e_wallet">E-Wallet</option>
                        <option value="COD">COD</option>
                    </select>
                </div>

                <div class="actions">
                    <div style="margin-bottom:10px;color:#222">
                        <div>Durasi: <span id="rentalDays">-</span> hari</div>
                        <div style="font-weight:700">Total: <span id="totalPrice">-</span></div>
                    </div>
                    <button class="btn" type="submit">Konfirmasi Sewa</button>
                    <a class="btn secondary" href="<?= $BASE_URL ?>cust/vechiles_user.php" style="margin-left:8px;text-decoration:none;display:inline-block;">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function(){
        const startEl = document.getElementById('tanggal_mulai');
        const endEl = document.getElementById('tanggal_selesai');
        const daysEl = document.getElementById('rentalDays');
        const totalEl = document.getElementById('totalPrice');
        const pricePerDay = <?= json_encode((int)$data['harga_sewa_per_hari']) ?>;

        function fmt(num){
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function update(){
            const s = startEl.value;
            const e = endEl.value;
            if (!s || !e) {
                daysEl.textContent = '-';
                totalEl.textContent = '-';
                return;
            }
            const sd = new Date(s);
            const ed = new Date(e);
            let diff = Math.ceil((ed - sd) / (1000*60*60*24));
            diff = diff >= 1 ? diff : 1;
            const total = diff * pricePerDay;
            daysEl.textContent = diff;
            totalEl.textContent = 'Rp' + fmt(total);
        }

        startEl.addEventListener('change', update);
        endEl.addEventListener('change', update);
        // init
        update();
    })();
</script>

</body>
</html>
