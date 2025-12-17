<?php
require_once __DIR__ . '/../config/koneksi.php';

class KendaraanCustomer {

    // Ambil daftar motor untuk halaman vehicles_user
    public function getMotorTersedia($search = "") {
        global $conn;
        // kept for backward compatibility: delegate to general method
        return $this->getKendaraanTersedia($search, 'motor');
    }

    /**
     * Ambil kendaraan tersedia. Jika $jenis diset (mis. 'motor' atau 'mobil'),
     * hasilnya akan difilter berdasarkan jenis tersebut. Jika kosong, kembalikan semua jenis.
     */
    public function getKendaraanTersedia($search = "", $jenis = '') {
        global $conn;

        $query = "SELECT k.*, m.nama_mitra 
                  FROM kendaraan k 
                  JOIN mitra m ON k.id_mitra = m.id_mitra
                  WHERE k.status_kendaraan = 'tersedia'";

        if (!empty($jenis)) {
            $query .= " AND k.jenis_kendaraan = ?";
        }

        if (!empty($search)) {
            $query .= " AND (k.merk LIKE ? OR k.model LIKE ? OR m.nama_mitra LIKE ?)";
        }

        $stmt = $conn->prepare($query);

        if (!empty($jenis) && !empty($search)) {
            $s = "%$search%";
            $stmt->bind_param("ssss", $jenis, $s, $s, $s);
        } elseif (!empty($jenis)) {
            $stmt->bind_param("s", $jenis);
        } elseif (!empty($search)) {
            $s = "%$search%";
            $stmt->bind_param("sss", $s, $s, $s);
        }

        $stmt->execute();
        return $stmt->get_result();
    }

    // Ambil detail kendaraan untuk halaman sewa.php
    public function getDetailKendaraan($id) {
        global $conn;

        $query = "SELECT k.*, m.nama_mitra 
                  FROM kendaraan k
                  JOIN mitra m ON k.id_mitra = m.id_mitra
                  WHERE k.kendaraan_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Buat transaksi (insert)
    // Buat transaksi (insert) + return rental_id
    public function buatTransaksi($customerId, $kendaraanId, $mulai, $selesai, $total) {
        global $conn;

        $query = "INSERT INTO transaksi (id_customer, kendaraan_id, tanggal_mulai, tanggal_selesai, total_harga, status_rental)
                VALUES (?, ?, ?, ?, ?, 'pending')";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("iissd", $customerId, $kendaraanId, $mulai, $selesai, $total);

        if ($stmt->execute()) {
            return $conn->insert_id; // ⬅️ kembalikan rental_id
        }

        return false;
    }
// ===============================
//  INSERT PEMBAYARAN
// ===============================
public function buatPembayaran($rentalId, $metode, $jumlah) {
    global $conn;

    // COD = pending, selain COD = sukses
    $status = ($metode === 'COD') ? 'pending' : 'sukses';

    $query = "INSERT INTO pembayaran (rental_id, metode_pembayaran, jumlah_bayar, status_pembayaran)
              VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isds", $rentalId, $metode, $jumlah, $status);

    return $stmt->execute();
}


// ===============================
//  DETAIL PEMBAYARAN + TRANSAKSI
// ===============================
public function getDetailPembayaran($rentalId)
{
    global $conn;

    $sql = "SELECT 
                t.*,
                p.pembayaran_id,
                p.metode_pembayaran,
                p.jumlah_bayar,
                p.status_pembayaran,
                k.merk,
                k.model,
                k.harga_sewa_per_hari
            FROM transaksi t
            LEFT JOIN pembayaran p ON t.rental_id = p.rental_id
            JOIN kendaraan k ON t.kendaraan_id = k.kendaraan_id
            WHERE t.rental_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rentalId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}
// ===============================
// GET TRANSAKSI BY CUSTOMER (My Rentals)
// ===============================
public function getTransaksiByCustomer($customer_id)
{
    global $conn;

    $sql = "SELECT t.*, 
                   k.merk, k.model, k.plat_nomor, k.foto
            FROM transaksi t
            JOIN kendaraan k ON t.kendaraan_id = k.kendaraan_id
            WHERE t.id_customer = ?
            ORDER BY t.rental_id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ===============================
// GET DETAIL TRANSAKSI (My Rental Detail) – VERSI BENAR & LENGKAP
// ===============================
public function getDetailTransaksi($id_transaksi)
{
    global $conn;

    $sql = "SELECT 
                t.*,
                k.merk, 
                k.model, 
                k.plat_nomor, 
                k.foto,
                k.harga_sewa_per_hari,
                m.nama_mitra, 
                m.no_telepon,
                c.nama AS nama_customer,
                c.email AS email_customer
            FROM transaksi t
            JOIN kendaraan k ON t.kendaraan_id = k.kendaraan_id
            JOIN mitra m ON k.id_mitra = m.id_mitra
            JOIN customer c ON t.id_customer = c.id_customer
            WHERE t.rental_id = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_transaksi);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc(); // return array atau null
}
// ===============================
// UPDATE STATUS TRANSAKSI
// ===============================
public function updateStatusTransaksi($rentalId, $status)
{
    global $conn;

    $sql = "UPDATE transaksi SET status_rental=? WHERE rental_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $rentalId);
    return $stmt->execute();
}

// ===============================
// UPDATE STATUS KENDARAAN
// ===============================
public function updateStatusKendaraan($kendaraanId, $status)
{
    global $conn;

    $sql = "UPDATE kendaraan SET status_kendaraan=? WHERE kendaraan_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $kendaraanId);
    return $stmt->execute();
}

// ===============================
// AUTO AKTIF JIKA TANGGAL MULAI
// ===============================
public function aktifkanJikaHariIni($trx)
{
    $today = date("Y-m-d");

    if ($trx['status_rental'] === 'dijadwalkan'
        && $today >= $trx['tanggal_mulai']) {

        $this->updateStatusTransaksi($trx['rental_id'], 'aktif');
        $this->updateStatusKendaraan($trx['kendaraan_id'], 'dipinjam');
    }
}

// ===============================
// AUTO SELESAI JIKA LEWAT TANGGAL
// ===============================
public function selesaikanJikaLewat($trx)
{
    $today = date("Y-m-d");

    if ($trx['status_rental'] === 'aktif'
        && $today > $trx['tanggal_selesai']) {

        $this->updateStatusTransaksi($trx['rental_id'], 'selesai');
        $this->updateStatusKendaraan($trx['kendaraan_id'], 'tersedia');
    }
}

}


