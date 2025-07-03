<?php 
require_once 'template_header.php'; 
require_once 'db.php';

// --- Ambil data untuk statistik di bagian atas ---
$total_users = mysqli_query($link, "SELECT COUNT(id) as total FROM users")->fetch_assoc()['total'];
$total_penduduk = mysqli_query($link, "SELECT COUNT(id) as total FROM data_penduduk")->fetch_assoc()['total'];

$total_lakilaki = 0;
$total_perempuan = 0;
$sql_gender = "SELECT jenis_kelamin, COUNT(id) as total FROM data_penduduk GROUP BY jenis_kelamin";
$result_gender = mysqli_query($link, $sql_gender);
if ($result_gender) {
    while ($row = mysqli_fetch_assoc($result_gender)) {
        if ($row['jenis_kelamin'] == 'Laki-laki') {
            $total_lakilaki = $row['total'];
        } elseif ($row['jenis_kelamin'] == 'Perempuan') {
            $total_perempuan = $row['total'];
        }
    }
}

// --- Ambil data untuk grafik batang (Jumlah Penduduk per TPS) ---
$tps_labels = [];
$tps_data = [];
$sql_chart = "SELECT tps, COUNT(id) as jumlah FROM data_penduduk GROUP BY tps ORDER BY tps ASC";
$result_chart = mysqli_query($link, $sql_chart);
if ($result_chart) {
    while ($row = mysqli_fetch_assoc($result_chart)) {
        $tps_labels[] = "TPS " . $row['tps'];
        $tps_data[] = $row['jumlah'];
    }
}

// =================================================================
// PHP BARU: Ambil data untuk Rekapitulasi Dapil
// =================================================================
$sql_dapil = "SELECT dapil, COUNT(id) as jumlah_penduduk FROM data_penduduk WHERE dapil IS NOT NULL AND dapil != '' GROUP BY dapil ORDER BY dapil ASC";
$result_dapil = mysqli_query($link, $sql_dapil);
// =================================================================

?>

<div class="card">
    <h4><i class="fas fa-chart-bar"></i> Ringkasan Data</h4>
    <div class="form-row" style="gap: 15px;">
        <div class="card" style="flex: 1; text-align: center; background: rgba(0,0,0,0.2);">
            <h5><i class="fas fa-users"></i> Total Data Penduduk</h5>
            <h2 style="font-size: 36px; margin-top: 10px; color: #fff;"><?php echo $total_penduduk; ?></h2>
        </div>
        <div class="card" style="flex: 1; text-align: center; background: rgba(23, 162, 184, 0.3);">
            <h5><i class="fas fa-mars"></i> Total Laki-Laki</h5>
            <h2 style="font-size: 36px; margin-top: 10px; color: #fff;"><?php echo $total_lakilaki; ?></h2>
        </div>
        <div class="card" style="flex: 1; text-align: center; background: rgba(233, 69, 96, 0.3);">
            <h5><i class="fas fa-venus"></i> Total Perempuan</h5>
            <h2 style="font-size: 36px; margin-top: 10px; color: #fff;"><?php echo $total_perempuan; ?></h2>
        </div>
        <div class="card" style="flex: 1; text-align: center; background: rgba(0,0,0,0.2);">
            <h5><i class="fas fa-user-shield"></i> Total User Sistem</h5>
            <h2 style="font-size: 36px; margin-top: 10px; color: #fff;"><?php echo $total_users; ?></h2>
        </div>
    </div>
</div>

<div class="form-row">
    <div class="card" style="flex: 2;"> <h4><i class="fas fa-chart-area"></i> Grafik Jumlah Penduduk per TPS</h4>
        <canvas id="tpsChart"></canvas>
    </div>

    <div class="card" style="flex: 1;"> <h4><i class="fas fa-map-marker-alt"></i> Rekap per Dapil</h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Dapil</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_dapil && mysqli_num_rows($result_dapil) > 0) {
                    while ($row = mysqli_fetch_assoc($result_dapil)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['dapil']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jumlah_penduduk']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2' style='text-align:center;'>Belum ada data Dapil.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
mysqli_close($link);
require_once 'template_footer.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('tpsChart');
    const tpsLabels = <?php echo json_encode($tps_labels); ?>;
    const tpsData = <?php echo json_encode($tps_data); ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: tpsLabels,
            datasets: [{
                label: 'Jumlah Penduduk',
                data: tpsData,
                backgroundColor: 'rgba(233, 69, 96, 0.5)',
                borderColor: 'rgba(233, 69, 96, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#e0e0e0' }
                },
                x: {
                    ticks: { color: '#e0e0e0' }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>