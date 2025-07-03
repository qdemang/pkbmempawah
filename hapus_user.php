<?php
session_start();

// Pastikan admin sudah login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "db.php";

// Cek apakah ID user ada di URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Siapkan statement DELETE
    $sql = "DELETE FROM users WHERE id = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variabel ke statement sebagai parameter
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameter
        $param_id = trim($_GET["id"]);
        
        // Coba eksekusi statement
        if (mysqli_stmt_execute($stmt)) {
            // Jika berhasil, arahkan kembali ke dashboard
            header("location: admin_dashboard.php");
            exit();
        } else {
            echo "Terjadi kesalahan. Silakan coba lagi nanti.";
        }
    }
     
    // Tutup statement
    mysqli_stmt_close($stmt);
    
    // Tutup koneksi
    mysqli_close($link);
} else {
    // Jika tidak ada ID, arahkan kembali ke dashboard
    header("location: admin_dashboard.php");
    exit();
}
?>