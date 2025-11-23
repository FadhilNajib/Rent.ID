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
    public function buatTransaksi($customerId, $kendaraanId, $mulai, $selesai, $total) {
        global $conn;

        $query = "INSERT INTO transaksi (id_customer, kendaraan_id, tanggal_mulai, tanggal_selesai, total_harga, status_rental)
                  VALUES (?, ?, ?, ?, ?, 'pending')";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("iissd", $customerId, $kendaraanId, $mulai, $selesai, $total);

        return $stmt->execute();
    }
}
