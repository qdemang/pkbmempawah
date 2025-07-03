<?php
session_start();

// Pastikan admin sudah login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "db.php";

// Inisialisasi variabel
$username = $email = $nama_lengkap = $status = "";
$username_err = $email_err = $nama_lengkap_err = "";
$update_success = "";

// Cek apakah ID ada di URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $user_id = trim($_GET["id"]);

    // Ambil data user dari database berdasarkan ID
    $sql = "SELECT username, email, nama_lengkap, status FROM users WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $username = $row['username'];
                $email = $row['email'];
                $nama_lengkap = $row['nama_lengkap'];
                $status = $row['status'];
            } else {
                // Jika ID tidak ditemukan, arahkan ke dashboard
                header("location: admin_dashboard.php");
                exit();
            }
        } else {
            echo "Terjadi kesalahan. Silakan coba lagi.";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // Jika tidak ada ID, arahkan ke dashboard
    header("location: admin_dashboard.php");
    exit();
}

// Proses data form saat dikirim (method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil ID dari hidden input
    $user_id = $_POST['id'];

    // Validasi input (bisa ditambahkan validasi lain jika perlu)
    $email = trim($_POST['email']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $status = trim($_POST['status']);

    // Jika tidak ada error, update data ke database
    if (empty($email_err) && empty($nama_lengkap_err)) {
        $sql = "UPDATE users SET email = ?, nama_lengkap = ?, status = ? WHERE id = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssi", $email, $nama_lengkap, $status, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $update_success = "Data pengguna berhasil diperbarui.";
                // Arahkan kembali ke dashboard setelah 2 detik
                header("refresh:2;url=admin_dashboard.php");
            } else {
                echo "Terjadi kesalahan. Gagal memperbarui data.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengguna</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Pengguna: <?php echo htmlspecialchars($username); ?></h2>

        <?php 
        if (!empty($update_success)) {
            echo '<div class="alert alert-success">' . $update_success . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $user_id; ?>">

            <div class="form-group">
                <label>Username (tidak bisa diubah)</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($username); ?>" disabled>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" value="<?php echo htmlspecialchars($nama_lengkap); ?>">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="aktif" <?php if ($status == 'aktif') echo 'selected'; ?>>Aktif</option>
                    <option value="nonaktif" <?php if ($status == 'nonaktif') echo 'selected'; ?>>Nonaktif</option>
                    <option value="diblokir" <?php if ($status == 'diblokir') echo 'selected'; ?>>Diblokir</option>
                </select>
            </div>

            <div class="form-group">
                <input type="submit" class="btn-submit" value="Simpan Perubahan">
            </div>
            <p class="form-footer"><a href="admin_dashboard.php">Kembali ke Dashboard</a></p>
        </form>
    </div>
</body>
</html>