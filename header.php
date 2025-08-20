<?php
session_start();
require_once("./config/db.php");
require_once("./config/function.php");
if (!$_SESSION['username']) {
    header('Location: ./auth/login.php');
    exit();
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $page; ?> - Absensi</title>
    <link href="./assets/css/bootstrap.css" rel="stylesheet">
    <link href="./assets/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="./assets/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-primary">
        <a class="navbar-brand" href="./"><img src="https://smkn1pangkep.sch.id/assets/media/logos/logo.png" alt="logo" style="width:40px;height:40px;">&nbsp; SKANSAP</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i
                class="fas fa-bars"></i></button>
    </nav>
    
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark bg-primary" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link <?php echo ($page == 'Dashboard') ? "active" : "" ?>" href="./">
                            <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                            Dashboard
                        </a>
                        <a class="nav-link <?php echo ($page == 'Data Pegawai') ? "active" : "" ?>"
                            href="./data_pegawai.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Data Pegawai
                        </a>
                        <a class="nav-link <?php echo ($page == 'Data Rekap') ? "active" : "" ?>"
                            href="./data_rekap.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-file"></i></div>
                            Data Rekap
                        </a>
                            <a class="nav-link <?php echo ($page == 'Data Jabatan') ? "active" : "" ?>"
                            href="./data_jabatan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-address-book"></i></div>
                            Data Jabatan
                        </a>
                        <a class="nav-link <?php echo ($page == 'Pengaturan Jam') ? "active" : "" ?>"
                            href="./pengaturan_jam.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-clock"></i></div>
                            Pengaturan Jam
                        </a>
                        <a class="nav-link" href="./auth/logout.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                            Log out
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer bg-primary">
                    <div class="small">Masuk sebagai:</div>
                    <?php echo $username; ?>
                </div>
            </nav>
        </div>