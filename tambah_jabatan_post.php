<?php
session_start();
require_once("./config/db.php");
require_once("./config/function.php");

// Fungsi untuk mengirim respon JSON yang bersih
function sendResponse($status, $message) {
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode(['status' => $status, 'message' => $message]);
    exit();
}

// 1. Cek Sesi Login
if (!isset($_SESSION['username'])) {
    sendResponse(false, 'Sesi Anda telah berakhir, silahkan login ulang.');
}

// 2. REVISI: Cek apakah ada data nama jabatan yang dikirim dengan nama 'jabatanNama'
if (!isset($_POST['jabatanNama']) || empty(trim($_POST['jabatanNama']))) {
    sendResponse(false, 'Nama jabatan tidak boleh kosong.');
}

// REVISI: Mengambil data dari 'jabatanNama'
$jabatan_nama = trim($_POST['jabatanNama']);

// 3. Validasi nama jabatan
if (validasi_jabatan($jabatan_nama) == FALSE) {
    sendResponse(false, 'Nama jabatan tidak boleh lebih dari 50 karakter.');
}

// 4. Cek apakah jabatan sudah ada (menggunakan prepared statements)
$stmt_check = $koneksi->prepare("SELECT jabatan_nama FROM jabatan WHERE jabatan_nama = ?");
$stmt_check->bind_param("s", $jabatan_nama);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows > 0) {
    sendResponse(false, 'Nama jabatan sudah ada.');
}
$stmt_check->close();

// 5. Jika semua validasi lolos, masukkan data baru ke tabel `jabatan`
$stmt_insert = $koneksi->prepare("INSERT INTO jabatan (jabatan_nama) VALUES (?)");
$stmt_insert->bind_param("s", $jabatan_nama);

if ($stmt_insert->execute()) {
    sendResponse(true, 'Jabatan baru berhasil ditambahkan.');
} else {
    sendResponse(false, 'Gagal menyimpan data ke database: ' . $koneksi->error);
}

$stmt_insert->close();
$koneksi->close();
?>
