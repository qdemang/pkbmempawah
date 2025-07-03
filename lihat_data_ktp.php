<?php
require_once 'template_header.php'; 
require_once "db.php";

// Logika untuk Pencarian
$search_term = "";
$sql = "SELECT * FROM data_penduduk";
$where_clauses = [];
$params = [];
$types = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = trim($_GET['search']);
    // Cari di beberapa kolom: nama, nik, tps, atau koordinator
    $where_clauses[] = "(nama_lengkap LIKE ? OR nik LIKE ? OR tps LIKE ? OR nama_koordinator LIKE ?)";
    $search_param = "%" . $search_term . "%";
    array_push($params, $search_param, $search_param, $search_param, $search_param);
    $types .= "ssss";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY tanggal_input DESC";

// Eksekusi query
$stmt = mysqli_prepare($link, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<div class="card">
    <h4><i class="fas fa-search"></i> Cari & Lihat Data Penduduk</h4>
    
    <form action="lihat_data_ktp.php" method="get" class="search-form" style="margin-bottom: 20px;">
        <div class="form-row">
            <div class="form-group" style="flex-grow: 3;">
                <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan Nama, NIK, TPS, atau Koordinator..." value="<?php echo htmlspecialchars($search_term); ?>">
            </div>
            <div class="form-group">
                <button type="submit" class="btn-submit" style="padding: 12px 20px;">Cari</button>
            </div>
        </div>
    </form>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th>Alamat</th>
                <th>TPS</th>
                <th>Koordinator</th>
                <th class="kolom-aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td><a href='uploads/" . htmlspecialchars($row['file_foto']) . "' target='_blank'><img src='uploads/" . htmlspecialchars($row['file_foto']) . "' alt='Foto'></a></td>";
                echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nik']) . "</td>";
                echo "<td>" . htmlspecialchars($row['alamat']) . " RT " . htmlspecialchars($row['rt']) . "/RW " . htmlspecialchars($row['rw']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tps']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_koordinator']) . "</td>";
                echo "<td class='kolom-aksi'>";
                echo "<a href='edit_data_penduduk.php?id=" . $row['id'] . "' class='action-btn btn-edit'><i class='fas fa-edit'></i></a>";
                echo "<a href='#' onclick='konfirmasiHapusPenduduk(event, " . $row['id'] . ")' class='action-btn btn-delete'><i class='fas fa-trash'></i></a>";
                echo "</td>";
                echo "</tr>";
            } // Penutup untuk 'while'
        } else {
            echo "<tr><td colspan='7' style='text-align:center;'>Data tidak ditemukan.</td></tr>";
        } // Penutup untuk 'if'
        ?>
        </tbody>
    </table>

    <div style="text-align: right; margin-top: 20px;">
        <button onclick="window.print();" class="btn-submit btn-print" style="width: auto; padding: 12px 25px;">
            <i class="fas fa-print"></i> Cetak Halaman
        </button>
    </div>

</div> <script>
function konfirmasiHapusPenduduk(event, id) {
    event.preventDefault(); 
    var konfirmasi = confirm("Apakah Anda yakin ingin menghapus data penduduk ini? Aksi ini tidak dapat dibatalkan.");
    if (konfirmasi) {
        window.location.href = 'hapus_data_penduduk.php?id=' + id;
    }
}
</script>

<?php 
mysqli_stmt_close($stmt);
mysqli_close($link);
require_once 'template_footer.php'; 
?>