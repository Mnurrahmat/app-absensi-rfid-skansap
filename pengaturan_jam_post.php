<?php
require_once("./config/db.php");

header('Content-Type: application/json');
$response = ['status' => false, 'message' => 'Data tidak lengkap.'];

if (isset($_POST['jam_masuk']) && isset($_POST['batas_terlambat']) && isset($_POST['jam_pulang'])) {
    $jam_masuk = $_POST['jam_masuk'];
    $batas_terlambat = $_POST['batas_terlambat'];
    $jam_pulang = $_POST['jam_pulang'];

    // Selalu update baris dengan id = 1
    $stmt = $koneksi->prepare("UPDATE pengaturan_jam SET jam_masuk = ?, batas_terlambat = ?, jam_pulang = ? WHERE id = 1");
    $stmt->bind_param("sss", $jam_masuk, $batas_terlambat, $jam_pulang);

    if ($stmt->execute()) {
        $response = ['status' => true, 'message' => 'Pengaturan waktu berhasil diperbarui.'];
    } else {
        $response['message'] = 'Gagal memperbarui pengaturan: ' . $stmt->error;
    }
    $stmt->close();
}

echo json_encode($response);
$koneksi->close();
?>
