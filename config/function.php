<?php
function format_hari_tanggal($waktu, $mode = false)
{
    $hari_array = array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );
    $hr = date('w', strtotime($waktu));
    $hari = $hari_array[$hr];
    $tanggal = date('j', strtotime($waktu));
    $bulan_array = array(
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    );
    $bl = date('n', strtotime($waktu));
    $bulan = $bulan_array[$bl];
    $tahun = date('Y', strtotime($waktu));

    if($mode == TRUE)
    {
        return "$tanggal $bulan $tahun";
    }else{
        return "$hari, $tanggal $bulan $tahun";
    }
}

function hari_indonesia($hari)
{
    switch ($hari) {
        case 'Sun':
            $hari_ini = "Minggu";
            break;

        case 'Mon':
            $hari_ini = "Senin";
            break;

        case 'Tue':
            $hari_ini = "Selasa";
            break;

        case 'Wed':
            $hari_ini = "Rabu";
            break;

        case 'Thu':
            $hari_ini = "Kamis";
            break;

        case 'Fri':
            $hari_ini = "Jumat";
            break;

        case 'Sat':
            $hari_ini = "Sabtu";
            break;

        default:
            $hari_ini = "Tidak di ketahui";
            break;
    }
    return $hari_ini;
}

function jenis_kelamin($jenis_kelamin)
{
    switch ($jenis_kelamin) {
        case 'M':
            $jenis_kelamin = "Pria";
            break;

        case 'F':
            $jenis_kelamin = "Wanita";
            break;

        default:
            $jenis_kelamin = "UnKnown";
            break;
    }
    return $jenis_kelamin;
}

/// Cek apakah panjangnya 8 karakter dan hanya berisi karakter hexadecimal (A-F, 0-9)
/// Jika true = valid
/// Jika false = tidak valid
function validasi_rfid($rfid)
{
    // Cek apakah panjangnya 8 karakter dan hanya berisi karakter hexadecimal (A-F, 0-9)
    if (preg_match('/^[a-fA-F0-9]{8,20}$/', $rfid)) {
        return true;
    }
    return false;
}

/// Jika nip bukan angka
/// Jika panjang nip bukang 18
/// Jika true = valid
/// Jika false = tidak valid
function validasi_nip($nip)
{
    if (!is_numeric($nip)) {
        return false;
    }

    if (strlen($nip) != 18) {
        return false;
    }

    return true;
}

/// Jika nama lebih dari 50
/// Jika true = valid
/// Jika false = tidak valid
function validasi_nama($nama)
{
    if (strlen($nama) < 2 || strlen($nama) > 50) {
        return false;
    }

    return true;
}

/// Jika jenis kelamin bukan M (Male) / F (Female)
/// Jika true = valid
/// Jika false = tidak valid
function validasi_jk($jenis_kelamin)
{
    if ($jenis_kelamin == "M" || $jenis_kelamin == "F") {
        return true;
    }

    return false;
}

/// validasi tanggal dengan function php yaitu checkdate
/// Format parameter Tahun-Bulan-Tanggal
/// Jika true = valid
/// Jika false = tidak valid
function validasi_tanggal($tanggal)
{
    $data = explode('-', $tanggal);
    $tanggal = $data[2];
    $bulan = $data[1];
    $tahun = $data[0];  
    // checkdate ( int $month , int $day , int $year ) : bool
    if (checkdate($bulan, $tanggal, $tahun)) {
        return true;
    }

    return false;
}


/// Jika nohp bukan angka
/// Jika panjang nomor hp kurang dari 5 dan lebih dari 20
/// Jika true = valid
/// Jika false = tidak valid
function validasi_nohp($nohp)
{
    if (!is_numeric($nohp)) {
        return false;
    }

    if (strlen($nohp) <= 5 || strlen($nohp) >= 20) {
        return false;
    }

    return true;
}

/// Jika alamat lebih dari 500
/// Jika true = valid
/// Jika false = tidak valid
function validasi_alamat($alamat)
{
    if (strlen($alamat) > 500) {
        return false;
    }

    return true;
}

function validasi_foto($pegawaiFoto, $targetDir)
{
    $check = getimagesize($pegawaiFoto["tmp_name"]);
    if($check == false) {
        return false;
    }

    /// Jika ukuran file lebih dari 10 MB
    /// 10000000 = 10 MB
    if ($pegawaiFoto["size"] > 10000000) {
        return false;
    }

    if ($pegawaiFoto['type'] != "image/jpg" && $pegawaiFoto['type'] != "image/png" && $pegawaiFoto['type'] != "image/jpeg") {
        return false;
    }

    $extension = explode("/", $pegawaiFoto['type']);
    $randomName = uniqid(rand());
    $finalName = $randomName.'.'.$extension[1];
    $fullPath = $targetDir.$finalName;
    if (file_exists($fullPath)) {
        return false;
    }

    if (move_uploaded_file($pegawaiFoto["tmp_name"], $fullPath)) {
        return $finalName;
    }
    return false;
}

/// Jika jabatan_nama lebih dari 50
/// Jika true = valid
/// Jika false = tidak valid
function validasi_jabatan($jabatan_nama)
{
    if (strlen($jabatan_nama) > 50) {
        return false;
    }

    return true;
}