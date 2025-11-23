<?php
require_once __DIR__ . '/../config/koneksi.php';

class KelolaKendaraan {

    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // GET semua kendaraan milik mitra
    public function getKendaraanByMitra($id_mitra)
    {
        $sql = "SELECT * FROM kendaraan WHERE id_mitra = ? ORDER BY kendaraan_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_mitra);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // GET detail kendaraan (untuk halaman edit)
    public function getKendaraanById($kendaraan_id)
    {
        $sql = "SELECT * FROM kendaraan WHERE kendaraan_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $kendaraan_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // INSERT kendaraan baru
    public function tambahKendaraan(
        $id_mitra,
        $jenis_kendaraan,
        $merk,
        $model,
        $tahun,
        $plat_nomor,
        $harga_sewa_per_hari
    ) {
        $sql = "INSERT INTO kendaraan (
                    id_mitra, jenis_kendaraan, merk, model, tahun,
                    plat_nomor, harga_sewa_per_hari, status_kendaraan
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'tersedia')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "isssisd",
            $id_mitra,
            $jenis_kendaraan,
            $merk,
            $model,
            $tahun,
            $plat_nomor,
            $harga_sewa_per_hari
        );

        return $stmt->execute();
    }

public function updateKendaraan(
    $kendaraan_id,
    $jenis_kendaraan,
    $merk,
    $model,
    $tahun,
    $plat_nomor,
    $harga_sewa_per_hari,
    $status_kendaraan,
    $foto = null
) {
    if ($foto) {
        $sql = "UPDATE kendaraan SET 
                jenis_kendaraan=?, merk=?, model=?, tahun=?, 
                plat_nomor=?, harga_sewa_per_hari=?, status_kendaraan=?, foto=?
                WHERE kendaraan_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssdssi",
            $jenis_kendaraan,
            $merk,
            $model,
            $tahun,
            $plat_nomor,
            $harga_sewa_per_hari,
            $status_kendaraan,
            $foto,
            $kendaraan_id
        );
    } else {
        $sql = "UPDATE kendaraan SET 
                jenis_kendaraan=?, merk=?, model=?, tahun=?, 
                plat_nomor=?, harga_sewa_per_hari=?, status_kendaraan=?
                WHERE kendaraan_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssdsi",
            $jenis_kendaraan,
            $merk,
            $model,
            $tahun,
            $plat_nomor,
            $harga_sewa_per_hari,
            $status_kendaraan,
            $kendaraan_id
        );
    }

    return $stmt->execute();
}

    // UPDATE STATUS kendaraan
    public function updateStatusKendaraan($kendaraan_id, $status)
    {
        $sql = "UPDATE kendaraan SET status_kendaraan = ? WHERE kendaraan_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $kendaraan_id);
        return $stmt->execute();
    }

    // DELETE kendaraan
    public function hapusKendaraan($kendaraan_id)
    {
        $sql = "DELETE FROM kendaraan WHERE kendaraan_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $kendaraan_id);
        return $stmt->execute();
    }
    // GET transaksi berdasarkan mitra
public function getTransaksiByMitra($id_mitra)
{
    $sql = "SELECT t.*, c.nama AS nama_customer, k.merk, k.model, k.plat_nomor
            FROM transaksi t
            JOIN customer c ON t.id_customer = c.id_customer
            JOIN kendaraan k ON t.kendaraan_id = k.kendaraan_id
            WHERE k.id_mitra = ?
            ORDER BY t.rental_id DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $id_mitra);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// UPDATE status transaksi
public function updateStatusTransaksi($rental_id, $status)
{
    $sql = "UPDATE transaksi SET status_rental=? WHERE rental_id=?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("si", $status, $rental_id);
    return $stmt->execute();
}

}

// ------------------ ROUTER ACTION ------------------- //

$kendaraan = new KelolaKendaraan();

// ===================== HAPUS ===================== //
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $kendaraan->hapusKendaraan($_GET['id']);
    header("Location: ../public/mitra/kelola_kendaraan.php");
    exit;
}

// ===================== UBAH STATUS ===================== //
if (isset($_GET['action']) && $_GET['action'] == 'ubah_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $kendaraan->updateStatusKendaraan($_GET['id'], $_GET['status']);
    header("Location: ../public/mitra/kelola_kendaraan.php");
    exit;
}

// ===================== UPDATE DATA KENDARAAN ===================== //
if (isset($_POST['update_kendaraan'])) {

    $kendaraan_id = $_POST['kendaraan_id'];
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $merk = $_POST['merk'];
    $model = $_POST['model'];
    $tahun = $_POST['tahun'];
    $plat_nomor = $_POST['plat_nomor'];
    $harga = $_POST['harga_sewa_per_hari'];
    $status = $_POST['status_kendaraan'];

    // FOTO
    $foto = null;
    if (!empty($_FILES['foto']['tmp_name'])) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    }

    $kendaraan->updateKendaraan(
        $kendaraan_id,
        $jenis_kendaraan,
        $merk,
        $model,
        $tahun,
        $plat_nomor,
        $harga,
        $status,
        $foto
    );

    header("Location: ../public/mitra/kelola_kendaraan.php");
    exit;
}
// UPDATE STATUS TRANSAKSI (ACC / SELESAI / BATALKAN)
if (isset($_GET['action']) && $_GET['action'] == 'update_rental' 
    && isset($_GET['id']) && isset($_GET['status'])) {

    $kendaraan = new KelolaKendaraan();
    $kendaraan->updateStatusTransaksi($_GET['id'], $_GET['status']);

    header("Location: ../public/mitra/rentals.php");
    exit;
}

?>