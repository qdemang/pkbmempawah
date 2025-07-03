<?php
require_once 'template_header.php';
require_once "db.php";

$pesan = "";
$data = null;
$user_id = null;

// Ambil ID dari URL dan data lama dari DB
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $user_id = trim($_GET["id"]);
    $sql = "SELECT * FROM data_penduduk WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $data = mysqli_fetch_assoc($result);
            } else {
                header("location: lihat_data_ktp.php");
                exit();
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Proses update saat form di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil semua data dari form
    $user_id = $_POST['id'];
    $nik = trim($_POST['nik']);
    $nomor_kk = trim($_POST['nomor_kk']);
    $nama_lengkap = trim($_POST['nama_lengkap']);

    // =================================================================
    // BAGIAN YANG DIPERBAIKI ADA DI SINI
    // =================================================================
    // (Kode di bawah ini sebelumnya hilang)
    $tempat_lahir = trim($_POST['tempat_lahir']);
    $tanggal_lahir = trim($_POST['tanggal_lahir']);
    $jenis_kelamin = trim($_POST['jenis_kelamin']);
    $alamat = trim($_POST['alamat']);
    $rt = trim($_POST['rt']);
    $rw = trim($_POST['rw']);
    $agama = trim($_POST['agama']);
    $status_perkawinan = trim($_POST['status_perkawinan']);
    $pekerjaan = trim($_POST['pekerjaan']);
    $kewarganegaraan = trim($_POST['kewarganegaraan']);
    $tps = trim($_POST['tps']);
    $dapil = trim($_POST['dapil']);
    $nama_koordinator = trim($_POST['nama_koordinator']);
    // =================================================================

    // Ambil nama file lama
    $file_foto_lama = $_POST['file_foto_lama'];
    $file_kk_lama = $_POST['file_kk_lama'];

    $file_foto_nama = $file_foto_lama;
    $file_kk_nama = $file_kk_lama;

    // Proses jika ada file foto baru yang diunggah
    if (isset($_FILES['file_foto']) && $_FILES['file_foto']['error'] == 0) {
        $upload_dir = "uploads/";
        if (!empty($file_foto_lama) && file_exists($upload_dir . $file_foto_lama)) {
            unlink($upload_dir . $file_foto_lama);
        }
        $foto_nama_asli = basename($_FILES["file_foto"]["name"]);
        $file_foto_nama = "foto_" . $nik . "_" . time() . "_" . $foto_nama_asli;
        move_uploaded_file($_FILES["file_foto"]["tmp_name"], $upload_dir . $file_foto_nama);
    }
    
    // Proses jika ada file KK baru yang diunggah
    if (isset($_FILES['file_kk']) && $_FILES['file_kk']['error'] == 0) {
         $upload_dir = "uploads/";
        if (!empty($file_kk_lama) && file_exists($upload_dir . $file_kk_lama)) {
            unlink($upload_dir . $file_kk_lama);
        }
        $kk_nama_asli = basename($_FILES["file_kk"]["name"]);
        $file_kk_nama = "kk_" . $nik . "_" . time() . "_" . $kk_nama_asli;
        move_uploaded_file($_FILES["file_kk"]["tmp_name"], $upload_dir . $file_kk_nama);
    }

    // Query UPDATE (juga diperbaiki agar mencakup semua kolom)
    $sql_update = "UPDATE data_penduduk SET nik=?, nomor_kk=?, nama_lengkap=?, tempat_lahir=?, tanggal_lahir=?, jenis_kelamin=?, alamat=?, rt=?, rw=?, agama=?, status_perkawinan=?, pekerjaan=?, kewarganegaraan=?, tps=?, dapil=?, nama_koordinator=?, file_foto=?, file_kk=? WHERE id=?";
    
    if ($stmt_update = mysqli_prepare($link, $sql_update)) {
        // bind_param diperbarui dengan jumlah variabel yang benar
        mysqli_stmt_bind_param($stmt_update, "ssssssssssssssssssi", $nik, $nomor_kk, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $alamat, $rt, $rw, $agama, $status_perkawinan, $pekerjaan, $kewarganegaraan, $tps, $dapil, $nama_koordinator, $file_foto_nama, $file_kk_nama, $user_id);
        
        if (mysqli_stmt_execute($stmt_update)) {
            $pesan = "Data berhasil diperbarui.";
            header("refresh:2;url=lihat_data_ktp.php");
        } else {
            $pesan = "Gagal memperbarui data: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt_update);
    }
    mysqli_close($link);
}

?>

<?php if(!empty($pesan)): ?>
    <div class="card" style="background: #28a745;"><p><?php echo $pesan; ?></p></div>
<?php endif; ?>

<?php if($data): ?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $user_id; ?>">
    <input type="hidden" name="file_foto_lama" value="<?php echo htmlspecialchars($data['file_foto']); ?>">
    <input type="hidden" name="file_kk_lama" value="<?php echo htmlspecialchars($data['file_kk']); ?>">

    <div class="card">
        <h4><i class="fas fa-edit"></i> Edit Data: <?php echo htmlspecialchars($data['nama_lengkap']); ?></h4>
        
        <div class="form-row">
            <div class="form-group">
                <label>NIK</label>
                <input type="text" name="nik" class="form-control" value="<?php echo htmlspecialchars($data['nik']); ?>" required>
            </div>
             <div class="form-group">
                <label>Nomor KK</label>
                <input type="text" name="nomor_kk" class="form-control" value="<?php echo htmlspecialchars($data['nomor_kk']); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" value="<?php echo htmlspecialchars($data['nama_lengkap']); ?>" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control" value="<?php echo htmlspecialchars($data['tempat_lahir']); ?>" required>
            </div>
            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_lahir']); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-control" required>
                <option value="Laki-laki" <?php if($data['jenis_kelamin'] == 'Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                <option value="Perempuan" <?php if($data['jenis_kelamin'] == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
            </select>
        </div>
        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" rows="2" required><?php echo htmlspecialchars($data['alamat']); ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>RT</label>
                <input type="text" name="rt" class="form-control" value="<?php echo htmlspecialchars($data['rt']); ?>" required>
            </div>
            <div class="form-group">
                <label>RW</label>
                <input type="text" name="rw" class="form-control" value="<?php echo htmlspecialchars($data['rw']); ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Agama</label>
                <input type="text" name="agama" class="form-control" value="<?php echo htmlspecialchars($data['agama']); ?>" required>
            </div>
            <div class="form-group">
                <label>Status Perkawinan</label>
                <input type="text" name="status_perkawinan" class="form-control" value="<?php echo htmlspecialchars($data['status_perkawinan']); ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Pekerjaan</label>
                <input type="text" name="pekerjaan" class="form-control" value="<?php echo htmlspecialchars($data['pekerjaan']); ?>" required>
            </div>
            <div class="form-group">
                <label>Kewarganegaraan</label>
                <input type="text" name="kewarganegaraan" class="form-control" value="<?php echo htmlspecialchars($data['kewarganegaraan']); ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>TPS</label>
                <input type="text" name="tps" class="form-control" value="<?php echo htmlspecialchars($data['tps']); ?>" required>
            </div>
            <div class="form-group">
                <label>Dapil</label>
                <input type="text" name="dapil" class="form-control" value="<?php echo htmlspecialchars($data['dapil']); ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Nama Koordinator</label>
            <input type="text" name="nama_koordinator" class="form-control" value="<?php echo htmlspecialchars($data['nama_koordinator']); ?>">
        </div>
    </div>

    <div class="card">
        <h4><i class="fas fa-upload"></i> Ganti Berkas (Opsional)</h4>
        <div class="form-group">
            <label>Ganti Foto Diri</label>
            <p>Foto saat ini: <a href="uploads/<?php echo htmlspecialchars($data['file_foto']); ?>" target="_blank"><?php echo htmlspecialchars($data['file_foto']); ?></a></p>
            <input type="file" name="file_foto" class="form-control" accept="image/jpeg, image/png">
        </div>
        <div class="form-group">
            <label>Ganti Scan KK</label>
            <p>KK saat ini: <a href="uploads/<?php echo htmlspecialchars($data['file_kk']); ?>" target="_blank"><?php echo htmlspecialchars($data['file_kk']); ?></a></p>
            <input type="file" name="file_kk" class="form-control" accept="image/jpeg, image/png, application/pdf">
        </div>
    </div>
    
    <div class="form-group">
        <input type="submit" class="btn-submit" value="Simpan Perubahan">
    </div>
</form>
<?php endif; ?>

<?php require_once 'template_footer.php'; ?>