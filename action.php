<?php
require_once("./config/db.php");

if (isset($_GET['do']) && $_GET['do'] == 'get_rfid_code') {
    
    $rfid_code = '';

    $sql = "SELECT rfid_code FROM rfid_code WHERE used = 0 ORDER BY id DESC LIMIT 1";

    $result = $koneksi->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $rfid_code = $row['rfid_code'];

        $stmt_update = $koneksi->prepare("UPDATE rfid_code SET used = 1 WHERE rfid_code = ?");
        $stmt_update->bind_param("s", $rfid_code);
        $stmt_update->execute();
        $stmt_update->close();
    }
    
    echo $rfid_code;
}

$koneksi->close();
?>
