<?php
session_start();
require_once("./config/db.php");
require_once("./config/function.php");

if (!$_SESSION['username']) {
    die(json_encode(array(
        'status' => false, 
        'message' => 'Session kamu berakhir, Silahkan login ulang.'
    )));
}

if (isset($_POST)) {
    
    /// VALIDASI FOTO
    $updatepegawaiFoto = false;
    $pegawaiFoto = $_FILES['pegawaiFoto'];
    if($pegawaiFoto['size'] != 0)
    {
        $updatepegawaiFoto = true;
    }

    /// Validasi post name yang kosong
    $postNames = array(
        "pegawaiID", 
        "pegawaiRfid", 
        "pegawaiNip", 
        "pegawaiNama", 
        "pegawaiJabatan", 
        "pegawaiJK", 
        "pegawaiTgl", 
        "pegawaiNohp", 
        "pegawaiAlamat"
    );

    foreach ($postNames as $postname) {
        /// JIka ada postname yang panjang lenghtnya 0
        if (!strlen($_POST[$postname])) {
            die(json_encode(array(
                'status' => false, 
                'message' => 'Silahkan isi semua form dengan baik dan benar'
            )));
        }
    }

    $pegawaiID = $_POST['pegawaiID'];
    /// Validasi RFID
    $pegawaiRFID = $_POST['pegawaiRfid'];
    if (validasi_rfid($pegawaiRFID) == FALSE) {
        die(json_encode(array(
            'status' => false, 
            'message' => 'RFID Tidak Valid! RFID terdiri dari 10 angka.'
        )));
    }

    $sql = "SELECT `pegawai_rfid` FROM `pegawai` WHERE `pegawai_rfid` = '$pegawaiRFID' AND `pegawai_id` <> '$pegawaiID'";
    $result = $koneksi->query($sql);
    if ($result->num_rows > 0) {
        die(json_encode(array(
            'status' => false, 
            'message' => 'RFID sudah terdaftar.'
        )));
    }

    /// Validasi NIP
    $pegawaiNIP = $_POST['pegawaiNip'];
    if (validasi_nip($pegawaiNIP) == FALSE) {
        die(json_encode(array(
            'status' => false, 
            'message' => 'NIP Tidak Valid! NIP terdiri dari 18 angka.'
        )));
    }

    $sql = "SELECT `pegawai_nip` FROM `pegawai` WHERE `pegawai_nip` = '$pegawaiNIP' AND `pegawai_id` <> '$pegawaiID'";
    $result = $koneksi->query($sql);
    if ($result->num_rows > 0) {
        die(json_encode(array(
            'status' => false, 
            'message' => 'NIP sudah terdaftar.'
        )));
    }

    /// Validasi NAMA
    $pegawaiNama = $_POST['pegawaiNama'];
    if (validasi_nama($pegawaiNama) == FALSE) {
        die(json_encode(array(
            'status' => false, 
            'message' => 'Nama minimal 2 karakter dan tidak boleh lebih dari 50 karakter.'
        )));
    }

    /// Validasi Jabatan
    $pegawaiJabatan = $_POST['pegawaiJabatan'];
    $sql = "SELECT * FROM `jabatan` WHERE `jabatan_id` = '$pegawaiJabatan'";
    $result = $koneksi->query($sql);
    if ($result->num_rows <= 0) {
        die(json_encode(array(
            'status' => false, 
            'message' => 'Form Jabatan tidak valid.'
        )));
    }

    /// Validasi Jenis Kelamin
    $pegawaiJenisKelamin = $_POST['pegawaiJK'];
    if (validasi_jk($pegawaiJenisKelamin) == FALSE) {
        die(json_encode(array(
            'status' => false, 
            'message' => 'Form Jenis Kelamin tidak valid.'
        )));
    }

    /// Validasi Tanggal Lahir
    $pegawaiTanggalLahir = $_POST['pegawaiTgl'];
    if (validasi_tanggal($pegawaiTanggalLahir) == FALSE) {
        die(json_encode(array(
            'status' => false, 
            'message' => 'Form Tanggal Lahir tidak valid.'
        )));
    }

    /// Validasi No HP
    $pegawaiNohp = $_POST['pegawaiNohp'];
    if (validasi_nohp($pegawaiNohp) == FALSE) {
        die(json_encode(array(
            'status' => false, 
            'message' => 'Nomor HP tidak valid.'
        )));
    }

    /// Validasi Alamat
    $pegawaiAlamat = $_POST['pegawaiAlamat'];
    if (validasi_alamat($pegawaiAlamat) == FALSE) {
        die(json_encode(array(
            'status' => false, 
            'message' => 'Alamat tidak boleh lebih dari 500 karakter.'
        )));
    }

    /// VALIDASI FOTO
    if ($updatepegawaiFoto) {
        $targetDir = "./image/";
        $response = validasi_foto($pegawaiFoto, $targetDir);
        if ($response == FALSE) {
            die(json_encode(array(
                'status' => false, 
                'message' => 'Foto pegawai tidak valid! Maksimal ukuran 10 MB dan ekstensi PNG/JPG/JPEG.'
            )));
        }
        $sql = "UPDATE `pegawai` 
                SET `jabatan_id` = '$pegawaiJabatan', `pegawai_rfid` = '$pegawaiRFID', `pegawai_nama` = '$pegawaiNama', `pegawai_nip` = '$pegawaiNip', `pegawai_jeniskelamin` = '$pegawaiJenisKelamin', `pegawai_lahir` = '$pegawaiTanggalLahir', `pegawai_nomorhp` = '$pegawaiNohp', `pegawai_alamat` = '$pegawaiAlamat', `pegawai_foto` = '$response'
                WHERE `pegawai`.`pegawai_id` = '$pegawaiID';";
    }else{
        $sql = "UPDATE `pegawai` 
                SET `jabatan_id` = '$pegawaiJabatan', `pegawai_rfid` = '$pegawaiRFID', `pegawai_nama` = '$pegawaiNama', `pegawai_nip` = '$pegawaiNIP', `pegawai_jeniskelamin` = '$pegawaiJenisKelamin', `pegawai_lahir` = '$pegawaiTanggalLahir', `pegawai_nomorhp` = '$pegawaiNohp', `pegawai_alamat` = '$pegawaiAlamat'
                WHERE `pegawai`.`pegawai_id` = '$pegawaiID';";
    }
    if($koneksi->query($sql) === TRUE)
    {
        /// Sukses
        die(json_encode(array(
            'status' => true, 
            'message' => 'Data pegawai berhasil di Update.'
        )));
    }else{
        /// Terjadi Kesalahan MySQL
        die(json_encode(array(
            'status' => false, 
            'message' => 'Query Gagal : '.$koneksi->error.''
        )));
    }
}