<?php

echo "<h1>Tes Diagnostik Login</h1>";

// 1. Tes Versi PHP
echo "<h2>1. Versi PHP</h2>";
echo "Versi PHP Anda: <strong>" . phpversion() . "</strong><hr>";

// 2. Tes Koneksi dan Pengambilan Data
echo "<h2>2. Tes Database</h2>";
require_once "db.php";

if ($link) {
    echo "<p style='color:green;'>✅ Koneksi ke database ('" . DB_NAME . "') berhasil.</p>";
    
    $sql = "SELECT password FROM admins WHERE username = 'admin'";
    $result = mysqli_query($link, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashed_password_from_db = $row['password'];
        
        echo "<p style='color:green;'>✅ User 'admin' ditemukan.</p>";
        echo "<p>Hash password dari database: <br><strong>" . htmlspecialchars($hashed_password_from_db) . "</strong></p>";
        echo "<p>Panjang hash: <strong>" . strlen($hashed_password_from_db) . "</strong> karakter.</p>";
        
    } else {
        echo "<p style='color:red;'>❌ GAGAL: User 'admin' tidak ditemukan di tabel 'admins'.</p>";
        $hashed_password_from_db = null;
    }
    
} else {
    echo "<p style='color:red;'>❌ GAGAL: Koneksi ke database tidak berhasil.</p>";
}

echo "<hr>";

// 3. Tes Verifikasi Password
echo "<h2>3. Tes Verifikasi Password</h2>";

if (!empty($hashed_password_from_db)) {
    $password_to_test = 'admin123';
    echo "<p>Mencoba memverifikasi password '<strong>" . $password_to_test . "</strong>' dengan hash di atas...</p>";
    
    $is_password_correct = password_verify($password_to_test, $hashed_password_from_db);
    
    if ($is_password_correct) {
        echo "<h3 style='color:green;'>✅ BERHASIL: Password cocok!</h3>";
    } else {
        echo "<h3 style='color:red;'>❌ GAGAL: Password TIDAK cocok.</h3>";
    }
    
    echo "<p>Hasil dari <code>password_verify()</code>: ";
    var_dump($is_password_correct);
    echo "</p>";

} else {
    echo "<p style='color:red;'>Tes verifikasi tidak bisa dijalankan karena hash tidak ditemukan.</p>";
}

mysqli_close($link);
?>