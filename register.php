<?php
require_once "db.php";

$username_err = $email_err = $password_err = $nama_lengkap_err = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Mohon masukkan username.";
    } else {
        $sql_check = "SELECT id FROM users WHERE username = ?";
        if ($stmt_check = mysqli_prepare($link, $sql_check)) {
            mysqli_stmt_bind_param($stmt_check, "s", $param_username_check);
            $param_username_check = trim($_POST["username"]);
            if (mysqli_stmt_execute($stmt_check)) {
                mysqli_stmt_store_result($stmt_check);
                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    $username_err = "Username ini sudah digunakan.";
                }
            }
            mysqli_stmt_close($stmt_check);
        }
    }

    // Validasi Nama Lengkap
    if(empty(trim($_POST["nama_lengkap"]))){
        $nama_lengkap_err = "Mohon masukkan nama lengkap.";
    }

    // Validasi email
    if(empty(trim($_POST["email"]))){
        $email_err = "Mohon masukkan email.";
    }

    // Validasi password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Mohon masukkan password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password minimal harus 6 karakter.";
    }

    // Jika tidak ada error, masukkan ke database
    if (empty($username_err) && empty($nama_lengkap_err) && empty($email_err) && empty($password_err)) {
        $sql = "INSERT INTO users (username, nama_lengkap, email, password) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $param_username, $param_nama, $param_email, $param_password);
            
            $param_username = trim($_POST["username"]);
            $param_nama = trim($_POST["nama_lengkap"]);
            $param_email = trim($_POST["email"]);
            $param_password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Registrasi berhasil! Silakan login.";
                $_POST = array();
            } else {
                $message = "Terjadi kesalahan. Gagal mendaftar.";
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
    <title>Registrasi User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <div class="form-container">
        <h2>Registrasi User Baru</h2>
        
        <?php 
        if(!empty($message)){
            $alert_class = (strpos($message, 'berhasil') !== false) ? 'alert-success' : 'alert-danger';
            echo '<div class="alert ' . $alert_class . '">' . $message . '</div>';
        }
        if(!empty($username_err)){ echo '<div class="alert alert-danger">' . $username_err . '</div>'; }
        if(!empty($nama_lengkap_err)){ echo '<div class="alert alert-danger">' . $nama_lengkap_err . '</div>'; }
        if(!empty($email_err)){ echo '<div class="alert alert-danger">' . $email_err . '</div>'; }
        if(!empty($password_err)){ echo '<div class="alert alert-danger">' . $password_err . '</div>'; }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>    
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn-submit" value="Daftar">
            </div>
            <p class="form-footer">Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </form>
    </div>
</body>
</html>