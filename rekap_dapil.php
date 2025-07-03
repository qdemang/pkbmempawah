<?php 
require_once 'template_header.php'; 
require_once "db.php";

// Cek apakah ada filter dapil yang dipilih dari URL
$selected_dapil = null;
if (isset($_GET['dapil']) && !empty($_GET['dapil'])) {
    $selected_dapil = trim($_GET['dapil']);
}

// =================================================================
// PHP BARU: Ambil daftar Dapil yang unik dari database untuk membuat tombol filter
// =================================================================
$dapil_list = [];
$sql_dapil_unik = "SELECT DISTINCT dapil FROM data_penduduk WHERE dapil IS NOT NULL AND dapil != '' ORDER BY dapil ASC";
$result_dapil_unik = mysqli_query($link, $sql_dapil_unik);
if ($result_dapil_unik) {
    while ($row = mysqli_fetch_assoc($result_dapil_unik)) {
        $dapil_list[] = $row['dapil'];
    }
}
// =================================================================

// Logika PHP: Jika ada dapil yang dipilih, ambil data detail. Jika tidak, ambil data rekap.
if ($selected_dapil) {
    // Ambil data detail penduduk untuk dapil yang dipilih
    $sql = "SELECT * FROM data_penduduk WHERE dapil = ? ORDER BY nama_lengkap ASC";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $selected_dapil);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Ambil data rekapitulasi semua dapil
    $sql_rekap = "
        SELECT 
            dapil,
            COUNT(id) AS jumlah_penduduk,
            COUNT(DISTINCT nama_koordinator) AS jumlah_koordinator,
            COUNT(DISTINCT tps) AS jumlah_tps
        FROM 
            data_penduduk
        WHERE 
            dapil IS NOT NULL AND dapil != ''
        GROUP BY 
            dapil
        ORDER BY 
            dapil ASC
    ";
    $result_rekap = mysqli_query($link, $sql_rekap);
}
?>

<div class="card">
    <h4><i class="fas fa-map-marked-alt"></i> Rekapitulasi Data per Daerah Pemilihan (Dapil)</h4>
    <p>Silakan pilih Dapil untuk melihat data yang lebih detail.</p>
    
    <div class="filter-container">
        <a href="rekap_dapil.php" class="filter-btn <?php echo !$selected_dapil ? 'active' : ''; ?>">Semua Dapil</a>
        
        <?php foreach ($dapil_list as $dapil_item): ?>
            <a href="rekap_dapil.php?dapil=<?php echo urlencode($dapil_item); ?>" 
               class="filter-btn <?php echo ($selected_dapil == $dapil_item) ? 'active' : ''; ?>">
               <?php echo htmlspecialchars($dapil_item); ?>
            </a>
        <?php endforeach; ?>
        </div>
</div>

<div class="card">
    <?php if ($selected_dapil): ?>
        <h4>Detail Data untuk: <?php echo htmlspecialchars($selected_dapil); ?></h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Lengkap</th>
                    <th>NIK</th>
                    <th>Alamat</th>
                    <th>TPS</th>
                    <th>Koordinator</th>
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
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>Tidak ada data untuk " . htmlspecialchars($selected_dapil) . ".</td></tr>";
            }
            ?>
            </tbody>
        </table>

    <?php else: ?>
        <h4>Ringkasan Semua Dapil</h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Dapil</th>
                    <th style="text-align: center;">Total Penduduk</th>
                    <th style="text-align: center;">Total Koordinator</th>
                    <th style="text-align: center;">Total TPS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_rekap && mysqli_num_rows($result_rekap) > 0) {
                    while ($row = mysqli_fetch_assoc($result_rekap)) {
                        echo "<tr>";
                        echo "<td><strong>" . htmlspecialchars($row['dapil']) . "</strong></td>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row['jumlah_penduduk']) . "</td>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row['jumlah_koordinator']) . "</td>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row['jumlah_tps']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center;'>Belum ada data Dapil untuk direkap.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php 
if (isset($stmt)) mysqli_stmt_close($stmt);
mysqli_close($link);
require_once 'template_footer.php'; 
?>