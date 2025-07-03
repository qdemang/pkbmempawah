<?php
require_once 'template_header.php';
require_once "db.php";

// Cek apakah ID user ada di URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $user_id = trim($_GET["id"]);
    
    // 1. Ambil nama file dari database SEBELUM menghapus record
    $sql_select = "SELECT file_foto, file_kk FROM data_penduduk WHERE id = ?";
    if ($stmt_select = mysqli_prepare($link, $sql_select)) {
        mysqli_stmt_bind_param($stmt_select, "i", $user_id);
        mysqli_stmt_execute($stmt_select);
        $result = mysqli_stmt_get_result($stmt_select);
        $data = mysqli_fetch_assoc($result);
        $file_foto_hapus = $data['file_foto'];
        $file_kk_hapus = $data['file_kk'];
        mysqli_stmt_close($stmt_select);
    }

    // 2. Siapkan statement DELETE
    $sql_delete = "DELETE FROM data_penduduk WHERE id = ?";
    if ($stmt_delete = mysqli_prepare($link, $sql_delete)) {
        mysqli_stmt_bind_param($stmt_delete, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            // 3. Jika record berhasil dihapus, hapus file fisiknya
            $upload_dir = "uploads/";
            if (!empty($file_foto_hapus) && file_exists($upload_dir . $file_foto_hapus)) {
                unlink($upload_dir . $file_foto_hapus);
            }
            if (!empty($file_kk_hapus) && file_exists($upload_dir . $file_kk_hapus)) {
                unlink($upload_dir . $file_kk_hapus);
            }
            
            // Arahkan kembali ke dashboard
            header("location: lihat_data_ktp.php");
            exit();
        } else {
            echo "Terjadi kesalahan. Gagal menghapus data.";
        }
        mysqli_stmt_close($stmt_delete);
    }
    
    mysqli_close($link);
} else {
    // Jika tidak ada ID, arahkan kembali
    header("location: lihat_data_ktp.php");
    exit();
}
?>