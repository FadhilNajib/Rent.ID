<?php
require_once __DIR__ . '/../config/koneksi.php';

class KendaraanCustomer {

    // Ambil daftar motor untuk halaman vehicles_user
    public function getMotorTersedia($search = "") {
        global $conn;

        $query = "SELECT k.*, m.nama_mitra 
                  FROM kendaraan k 
                  JOIN mitra m ON k.id_mitra = m.id_mitra
                  WHERE k.jenis_kendaraan = 'motor' 
                  AND k.status_kendaraan = 'tersedia'";

        if (!empty($search)) {
            $query .= " AND (k.merk LIKE ? OR k.model LIKE ? OR m.nama_mitra LIKE ?)";
            $stmt = $conn->prepare($query);
            $s = "%$search%";
            $stmt->bind_param("sss", $s, $s, $s);
        } else {
            $stmt = $conn->prepare($query);
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
}


