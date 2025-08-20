<?php
// Langkah 1: Matikan semua laporan error untuk memastikan tidak ada output HTML
error_reporting(0);
ini_set('display_errors', 0);

// Langkah 2: Mulai output buffering untuk menangkap semua output
ob_start();

// Langkah 3: Mulai sesi
session_start();

// Fungsi untuk mengirim respon JSON yang bersih dan menghentikan script
function sendResponse($status, $message) {
    // Membersihkan semua output yang mungkin sudah ada di buffer
    ob_end_clean();
    
    // Mengatur header sebagai JSON
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    
    // Mengirim respon JSON dan menghentikan eksekusi
    echo json_encode(['status' => $status, 'message' => $message]);
    exit();
}

// Memasukkan file konfigurasi
require_once("./config/db.php");
require_once("./config/function.php");

// Cek Sesi Login
if (!isset($_SESSION['username'])) {
    sendResponse(false, 'Sesi Anda telah berakhir, silahkan login ulang.');
}

// Cek Kelengkapan Data POST dan FILES
if (
    !isset($_POST['pegawaiRfid'], $_POST['pegawaiNip'], $_POST['pegawaiNama'], $_POST['pegawaiJabatan'], $_POST['pegawaiJK'], $_POST['pegawaiTgl'], $_POST['pegawaiNohp'], $_POST['pegawaiAlamat']) ||
    !isset($_FILES['pegawaiFoto']) || $_FILES['pegawaiFoto']['error'] != 0
) {
    sendResponse(false, 'Semua kolom formulir, termasuk foto, wajib diisi.');
}

// Ambil dan Validasi Data
$pegawaiRFID = $_POST['pegawaiRfid'];
$pegawaiNIP = $_POST['pegawaiNip'];
$pegawaiNama = $_POST['pegawaiNama'];
$pegawaiJabatan = $_POST['pegawaiJabatan'];
$pegawaiJenisKelamin = $_POST['pegawaiJK'];
$pegawaiTanggalLahir = $_POST['pegawaiTgl'];
$pegawaiNohp = $_POST['pegawaiNohp'];
$pegawaiAlamat = $_POST['pegawaiAlamat'];
$pegawaiFoto = $_FILES['pegawaiFoto'];

// Validasi menggunakan fungsi Anda
if (validasi_rfid($pegawaiRFID) == FALSE) sendResponse(false, 'Format RFID Tidak Valid.');
if (validasi_nip($pegawaiNIP) == FALSE) sendResponse(false, 'NIP Tidak Valid! NIP harus terdiri dari 18 angka.');
if (validasi_nama($pegawaiNama) == FALSE) sendResponse(false, 'Nama minimal 2 karakter dan tidak boleh lebih dari 50 karakter.');
if (validasi_jk($pegawaiJenisKelamin) == FALSE) sendResponse(false, 'Jenis Kelamin tidak valid.');
if (validasi_tanggal($pegawaiTanggalLahir) == FALSE) sendResponse(false, 'Format Tanggal Lahir tidak valid.');
if (validasi_nohp($pegawaiNohp) == FALSE) sendResponse(false, 'Nomor HP tidak valid.');
if (validasi_alamat($pegawaiAlamat) == FALSE) sendResponse(false, 'Alamat tidak boleh lebih dari 500 karakter.');

// Cek Duplikasi Data (Menggunakan Prepared Statements)
$stmt = $koneksi->prepare("SELECT pegawai_rfid FROM pegawai WHERE pegawai_rfid = ?");
$stmt->bind_param("s", $pegawaiRFID);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    sendResponse(false, 'RFID sudah terdaftar.');
}
$stmt->close();

$stmt = $koneksi->prepare("SELECT pegawai_nip FROM pegawai WHERE pegawai_nip = ?");
$stmt->bind_param("s", $pegawaiNIP);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    sendResponse(false, 'NIP sudah terdaftar.');
}
$stmt->close();

// Proses Upload Foto
$targetDir = "./image/";
$namaFileFoto = validasi_foto($pegawaiFoto, $targetDir);
if ($namaFileFoto == FALSE) {
    sendResponse(false, 'Foto pegawai tidak valid! Pastikan ukuran di bawah 10 MB dan formatnya PNG/JPG/JPEG.');
}

// Simpan Data Pegawai Baru ke Database
$sql = "INSERT INTO `pegawai` (`jabatan_id`, `pegawai_rfid`, `pegawai_nama`, `pegawai_nip`, `pegawai_jeniskelamin`, `pegawai_lahir`, `pegawai_nomorhp`, `pegawai_alamat`, `pegawai_foto`, `pegawai_status`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, '1')";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("issssssss", $pegawaiJabatan, $pegawaiRFID, $pegawaiNama, $pegawaiNIP, $pegawaiJenisKelamin, $pegawaiTanggalLahir, $pegawaiNohp, $pegawaiAlamat, $namaFileFoto);

if ($stmt->execute()) {
    // Jika berhasil, update status RFID di tabel rfid_code
    $stmt_update = $koneksi->prepare("UPDATE rfid_code SET used = 1 WHERE rfid_code = ?");
    $stmt_update->bind_param("s", $pegawaiRFID);
    $stmt_update->execute();
    $stmt_update->close();

    sendResponse(true, 'Data pegawai berhasil disimpan.');
} else {
    sendResponse(false, 'Query Gagal: ' . $koneksi->error);
}

$stmt->close();
$koneksi->close();
?>
