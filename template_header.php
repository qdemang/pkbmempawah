<?php
// Selalu mulai session dan cek login di template header
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKB KABUPATEN MEMPAWAH</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <?php
$logo_path = "images/pkb.png";
echo "<img src='$logo_path' alt='Logo' width='180'>";
?>
            <h2>PKB MEMPAWAH</h2>
            <p></p>
<div style="height: 50px;"></div>
<p></p>

            <ul>
    <li><a href="admin_dashboard.php"><i class="fas fa-home"></i>Dashboard</a></li>
    <li><a href="lihat_data_user.php"><i class="fas fa-users"></i>Data Pengguna</a></li>
    <li><a href="input_data_ktp.php"><i class="fas fa-plus"></i>Input Data KTP</a></li>
    <li><a href="lihat_data_ktp.php"><i class="fas fa-id-card"></i>Lihat Data KTP</a></li>
    <li><a href="rekap_dapil.php"><i class="fas fa-map-marked-alt"></i>Rekap per Dapil</a></li>
    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
</ul>
        </div>
        <div class="main-content">
            <div class="header">
                Selamat Datang, <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>
            </div>
            <div class="content">