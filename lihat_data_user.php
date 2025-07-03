<?php
// Memanggil template header
require_once 'template_header.php'; 
require_once "db.php";

// Ambil semua data pengguna dari database, diurutkan berdasarkan tanggal daftar
$sql = "SELECT id, username, email, nama_lengkap, status, created_at FROM users ORDER BY created_at DESC";
$result = mysqli_query($link, $sql);
?>

<div class="card">
    <h4><i class="fas fa-users"></i> Daftar Pengguna Sistem</h4>
    <p>Berikut adalah semua pengguna yang terdaftar di sistem. Anda dapat mengedit atau menghapus data mereka.</p>
    <br>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Nama Lengkap</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            // Loop melalui setiap baris data
            while ($user = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                // Cek jika nama lengkap null atau kosong
                echo "<td>" . (!empty($user['nama_lengkap']) ? htmlspecialchars($user['nama_lengkap']) : '-') . "</td>";
                echo "<td>" . htmlspecialchars($user['status']) . "</td>";
                echo "<td>" . date('d M Y, H:i', strtotime($user['created_at'])) . "</td>";
                echo "<td>";
                // Link ke halaman edit_user.php yang sudah kita buat sebelumnya
                echo "<a href='edit_user.php?id=" . $user['id'] . "' class='action-btn btn-edit'><i class='fas fa-edit'></i></a>";
                // Link ke halaman hapus_user.php, menggunakan fungsi konfirmasi
                echo "<a href='#' onclick='konfirmasiHapus(event, " . $user['id'] . ")' class='action-btn btn-delete'><i class='fas fa-trash'></i></a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center;'>Belum ada pengguna yang mendaftar.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
function konfirmasiHapus(event, id) {
    event.preventDefault(); 
    var konfirmasi = confirm("Apakah Anda yakin ingin menghapus pengguna ini?");
    if (konfirmasi) {
        window.location.href = 'hapus_user.php?id=' + id;
    }
}
</script>

<?php 
// Tutup koneksi dan panggil template footer
mysqli_close($link);
require_once 'template_footer.php'; 
?>