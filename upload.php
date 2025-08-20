<?php
require_once("./config/db.php");
require_once("./config/function.php");

date_default_timezone_set('Asia/Makassar');
$target_dir = "image/";

function sendSuccessResponse() {
    if (ob_get_level()) ob_end_clean();
    header("Connection: close");
    header("Content-Length: 2");
    http_response_code(200);
    echo "OK";
    flush();
    exit();
}

if (isset($_POST["uid"]) && isset($_FILES["imageFile"])) {
    $uid = $_POST["uid"];
    if ($_FILES["imageFile"]["error"] != 0) {
        http_response_code(400);
        die("Error pada file upload: " . $_FILES["imageFile"]["error"]);
    }

    $image_name = uniqid() . "_" . basename($_FILES["imageFile"]["name"]);
    $target_file = $target_dir . $image_name;
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $target_file)) {
        $stmt_pegawai = $koneksi->prepare("SELECT pegawai_id FROM pegawai WHERE pegawai_rfid = ?");
        $stmt_pegawai->bind_param("s", $uid);
        $stmt_pegawai->execute();
        $result_pegawai = $stmt_pegawai->get_result();

        if ($result_pegawai->num_rows > 0) {
            $row_pegawai = $result_pegawai->fetch_assoc();
            $pegawai_id = $row_pegawai['pegawai_id'];
            $stmt_pegawai->close();

            // REVISI: Mengambil pengaturan waktu global dari tabel `pengaturan_jam`
            $sql_pengaturan = "SELECT * FROM pengaturan_jam WHERE id = 1 LIMIT 1";
            $result_pengaturan = $koneksi->query($sql_pengaturan);

            if ($result_pengaturan->num_rows > 0) {
                $pengaturan = $result_pengaturan->fetch_assoc();
                $batas_terlambat = $pengaturan['batas_terlambat'];
                $jadwal_pulang = $pengaturan['jam_pulang'];

                $tanggal_sekarang = date("Y-m-d");
                $jam_sekarang = date("H:i:s");

                $stmt_rekap = $koneksi->prepare("SELECT rekap_id, rekap_keluar FROM rekap WHERE pegawai_id = ? AND rekap_tanggal = ?");
                $stmt_rekap->bind_param("is", $pegawai_id, $tanggal_sekarang);
                $stmt_rekap->execute();
                $result_rekap = $stmt_rekap->get_result();

                if ($result_rekap->num_rows > 0) {
                    // --- PROSES ABSEN PULANG ---
                    $row_rekap = $result_rekap->fetch_assoc();
                    $stmt_rekap->close();
                    if ($row_rekap['rekap_keluar'] != NULL) {
                        http_response_code(400);
                        die("Error: Anda sudah melakukan absen pulang hari ini.");
                    }
                    if (strtotime($jam_sekarang) < strtotime($jadwal_pulang)) {
                        http_response_code(400);
                        die("Error: Belum waktunya untuk absen pulang.");
                    }
                    
                    $keterangan_pulang = "Hadir Pulang";
                    $rekap_id = $row_rekap['rekap_id'];
                    $stmt_update = $koneksi->prepare("UPDATE rekap SET rekap_keluar = ?, rekap_photokeluar = ?, rekap_keterangan = ? WHERE rekap_id = ?");
                    $stmt_update->bind_param("sssi", $jam_sekarang, $image_name, $keterangan_pulang, $rekap_id);
                    
                    if ($stmt_update->execute()) {
                        sendSuccessResponse();
                    } else {
                        http_response_code(500);
                        die("Error: Gagal update database: " . $stmt_update->error);
                    }
                } else {
                    // --- PROSES ABSEN MASUK ---
                    $stmt_rekap->close();
                    // REVISI: Logika terlambat sekarang berdasarkan kolom `batas_terlambat` dari database
                    $keterangan_masuk = (strtotime($jam_sekarang) <= strtotime($batas_terlambat)) ? "Hadir Masuk" : "Hadir Terlambat";

                    // REVISI: Menghapus `jadwal_id` dari query INSERT karena sudah tidak ada di tabel `rekap`
                    $stmt_insert = $koneksi->prepare("INSERT INTO rekap (pegawai_id, rekap_tanggal, rekap_masuk, rekap_photomasuk, rekap_keterangan) VALUES (?, ?, ?, ?, ?)");
                    $stmt_insert->bind_param("issss", $pegawai_id, $tanggal_sekarang, $jam_sekarang, $image_name, $keterangan_masuk);

                    if ($stmt_insert->execute()) {
                        sendSuccessResponse();
                    } else {
                        http_response_code(500);
                        die("Error: Gagal insert ke database: " . $stmt_insert->error);
                    }
                }
            } else {
                http_response_code(500);
                die("Error: Pengaturan waktu global tidak ditemukan di database.");
            }
        } else {
            http_response_code(404);
            die("Error: Kartu RFID tidak terdaftar.");
        }
    } else {
        http_response_code(500);
        die("Error: Gagal menyimpan file gambar.");
    }
} else {
    http_response_code(400);
    die("Error: Data tidak lengkap. Pastikan UID dan gambar terkirim.");
}
$koneksi->close();
?>