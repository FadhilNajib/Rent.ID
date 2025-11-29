<?php
require_once __DIR__ . '/../config/koneksi.php';

class KelolaKendaraan {

    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    /* ============================================================
       ================   FUNGSI KENDARAAN   ======================
       ============================================================ */

    public function getKendaraanByMitra($id_mitra)
    {
        $sql = "SELECT * FROM kendaraan WHERE id_mitra = ? ORDER BY kendaraan_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_mitra);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getKendaraanById($id)
    {
        $sql = "SELECT * FROM kendaraan WHERE kendaraan_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function tambahKendaraan($id_mitra, $jenis, $merk, $model, $tahun, $plat, $harga)
    {
        $sql = "INSERT INTO kendaraan (
                id_mitra, jenis_kendaraan, merk, model, tahun,
                plat_nomor, harga_sewa_per_hari, status_kendaraan
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'tersedia')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isssisd", $id_mitra, $jenis, $merk, $model, $tahun, $plat, $harga);
        return $stmt->execute();
    }

    public function updateKendaraan($id, $jenis, $merk, $model, $tahun,
                                    $plat, $harga, $status, $foto = null)
    {
        if ($foto) {
            $sql = "UPDATE kendaraan SET 
                jenis_kendaraan=?, merk=?, model=?, tahun=?, 
                plat_nomor=?, harga_sewa_per_hari=?, status_kendaraan=?, foto=?
                WHERE kendaraan_id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssssdssi", $jenis, $merk, $model, $tahun, $plat, 
                              $harga, $status, $foto, $id);
        } else {
            $sql = "UPDATE kendaraan SET 
                jenis_kendaraan=?, merk=?, model=?, tahun=?, 
                plat_nomor=?, harga_sewa_per_hari=?, status_kendaraan=?
                WHERE kendaraan_id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssssdsi", $jenis, $merk, $model, $tahun, 
                              $plat, $harga, $status, $id);
        }

        return $stmt->execute();
    }

    public function updateStatusKendaraan($id, $status)
    {
        $sql = "UPDATE kendaraan SET status_kendaraan=? WHERE kendaraan_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function hapusKendaraan($id)
    {
        $sql = "DELETE FROM kendaraan WHERE kendaraan_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }


    /* ============================================================
       ====================   TRANSAKSI   =========================
       ============================================================ */
public function getTransaksiByMitra($id_mitra)
{
    $sql = "SELECT t.*, 
                   c.nama AS nama_customer, 
                   c.email AS email_customer,
                   c.no_telepon,
                   k.merk, k.model, k.plat_nomor,
                   p.status_pembayaran,
                   p.metode_pembayaran
            FROM transaksi t
            JOIN customer c ON t.id_customer = c.id_customer
            JOIN kendaraan k ON t.kendaraan_id = k.kendaraan_id
            LEFT JOIN pembayaran p ON p.rental_id = t.rental_id
            WHERE k.id_mitra = ?
            ORDER BY t.rental_id DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $id_mitra);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    public function getDetailTransaksi($id)
    {
        $sql = "SELECT t.*, c.nama AS nama_customer, k.merk, k.model, k.plat_nomor
                FROM transaksi t
                JOIN customer c ON t.id_customer = c.id_customer
                JOIN kendaraan k ON t.kendaraan_id = k.kendaraan_id
                WHERE t.rental_id = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getPembayaranByRental($id)
    {
        $sql = "SELECT * FROM pembayaran WHERE rental_id=? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateStatusTransaksi($id, $status)
    {
        $sql = "UPDATE transaksi SET status_rental=? WHERE rental_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    // otomatis aktif jika tanggal_mulai tiba
    public function aktifkanJikaHariIni($trx)
    {
        $today = date("Y-m-d");

        if ($trx['status_rental'] == 'acc' && $today >= $trx['tanggal_mulai']) {

            $this->updateStatusTransaksi($trx['rental_id'], 'aktif');
            $this->updateStatusKendaraan($trx['kendaraan_id'], 'dipinjam');
        }
    }

    // otomatis selesai jika sudah lewat
    public function selesaikanJikaLewat($trx)
    {
        $today = date("Y-m-d");

        if ($trx['status_rental'] == 'aktif' && $today > $trx['tanggal_selesai']) {

            $this->updateStatusTransaksi($trx['rental_id'], 'selesai');
            $this->updateStatusKendaraan($trx['kendaraan_id'], 'tersedia');
        }
    }
}


/* ============================================================
   ==================== ROUTER ACTION ==========================
   ============================================================ */

$kendaraan = new KelolaKendaraan();

/* ------------ HAPUS KENDARAAN --------------- */
if (isset($_GET['action']) && $_GET['action'] === 'hapus') {
    $kendaraan->hapusKendaraan($_GET['id']);
    header("Location: ../public/mitra/kelola_kendaraan.php");
    exit;
}

/* ------------ UBAH STATUS KENDARAAN --------- */
if (isset($_GET['action']) && $_GET['action'] === 'ubah_status') {
    $kendaraan->updateStatusKendaraan($_GET['id'], $_GET['status']);
    header("Location: ../public/mitra/kelola_kendaraan.php");
    exit;
}

/* ------------ UPDATE DATA KENDARAAN --------- */
if (isset($_POST['update_kendaraan'])) {

    $foto = null;
    if (!empty($_FILES['foto']['tmp_name'])) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    }

    $kendaraan->updateKendaraan(
        $_POST['kendaraan_id'],
        $_POST['jenis_kendaraan'],
        $_POST['merk'],
        $_POST['model'],
        $_POST['tahun'],
        $_POST['plat_nomor'],
        $_POST['harga_sewa_per_hari'],
        $_POST['status_kendaraan'],
        $foto
    );

    header("Location: ../public/mitra/kelola_kendaraan.php");
    exit;
}
/* ------------ UPDATE TRANSAKSI (ACC / SELESAI / BATALKAN) --------------- */
if (isset($_GET['action']) && $_GET['action'] == 'update_rental') {

    $id = $_GET['id'];
    $status = $_GET['status'];

    // data transaksi
    $trx = $kendaraan->getDetailTransaksi($id);
    $pay = $kendaraan->getPembayaranByRental($id);

    // ACC → syarat pembayaran sukses kecuali COD
    if ($status == "dijadwalkan" && $pay && $pay['metode_pembayaran'] !== "COD" && $pay['status_pembayaran'] !== "sukses") {
        die("Pembayaran belum sukses. Tidak bisa ACC.");
    }

    // update status transaksi
    $kendaraan->updateStatusTransaksi($id, $status);

    // update status kendaraan
    if ($status == "dijadwalkan") {
        $kendaraan->updateStatusKendaraan($trx['kendaraan_id'], "booking");
    } elseif ($status == "aktif") {
        $kendaraan->updateStatusKendaraan($trx['kendaraan_id'], "dipinjam");
    } elseif ($status == "selesai" || $status == "dibatalkan") {
        $kendaraan->updateStatusKendaraan($trx['kendaraan_id'], "tersedia");
    }

    header("Location: ../public/mitra/rentals.php");
    exit;
}
/* ------------ KONFIRMASI COD --------------- */
if (isset($_GET['action']) && $_GET['action'] == 'konfirmasi_cod') {

    $id = $_GET['id'];

    // update pembayaran COD -> sukses
    $sql = "UPDATE pembayaran SET status_pembayaran='sukses' WHERE rental_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: ../public/mitra/rentals.php");
    exit;
}


?>
