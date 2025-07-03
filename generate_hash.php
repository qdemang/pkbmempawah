<?php
// Script untuk membuat hash password baru menggunakan Argon2id

$password_baru = 'admin123';
$hash_argon2id = password_hash($password_baru, PASSWORD_ARGON2ID);

echo "<h1>Hash Password Baru (Argon2id)</h1>";
echo "<p>Gunakan hash di bawah ini untuk memperbarui database Anda.</p>";
echo "<hr>";
echo "<strong>" . htmlspecialchars($hash_argon2id) . "</strong>";
?>
