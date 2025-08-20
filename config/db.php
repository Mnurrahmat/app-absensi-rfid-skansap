<?php
date_default_timezone_set("Asia/Makassar");
$host     = "localhost";
$username = "root";
$password = "";
$database = "app-rfid-absensi-smea";

$koneksi = new mysqli($host, $username, $password, $database);

if ($koneksi->connect_error) {
    die("Koneksi Gagal : " . $koneksi->connect_error);
}
?>