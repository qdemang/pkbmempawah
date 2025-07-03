<?php 
require_once 'template_header.php';
require_once "db.php";

$pesan = "";

// Proses form saat data dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $nik = trim($_POST['nik']);
    $nomor_kk = trim($_POST['nomor_kk']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
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

    // --- Proses Upload File ---
    $upload_dir = "uploads/";
    $file_foto_nama = "";
    $file_kk_nama = "";
    $upload_ok = 1;

    // Proses Foto
    if (isset($_FILES['file_foto']) && $_FILES['file_foto']['error'] == 0) {
        $foto_nama_asli = basename($_FILES["file_foto"]["name"]);
        $file_foto_nama = "foto_" . $nik . "_" . time() . "_" . $foto_nama_asli;
        $target_file_foto = $upload_dir . $file_foto_nama;
        if (!move_uploaded_file($_FILES["file_foto"]["tmp_name"], $target_file_foto)) {
            $pesan = "Error saat mengunggah file foto.";
            $upload_ok = 0;
        }
    }

    // Proses KK
    if (isset($_FILES['file_kk']) && $_FILES['file_kk']['error'] == 0) {
        $kk_nama_asli = basename($_FILES["file_kk"]["name"]);
        $file_kk_nama = "kk_" . $nik . "_" . time() . "_" . $kk_nama_asli;
        $target_file_kk = $upload_dir . $file_kk_nama;
        if (!move_uploaded_file($_FILES["file_kk"]["tmp_name"], $target_file_kk)) {
            $pesan = "Error saat mengunggah file KK.";
            $upload_ok = 0;
        }
    }

    // Jika upload berhasil, masukkan data ke database
    if ($upload_ok == 1) {
        $sql = "INSERT INTO data_penduduk (nik, nomor_kk, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, rt, rw, agama, status_perkawinan, pekerjaan, kewarganegaraan, tps, dapil, nama_koordinator, file_foto, file_kk) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssssssssssssss", $nik, $nomor_kk, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $alamat, $rt, $rw, $agama, $status_perkawinan, $pekerjaan, $kewarganegaraan, $tps, $dapil, $nama_koordinator, $file_foto_nama, $file_kk_nama);

            if (mysqli_stmt_execute($stmt)) {
                $pesan = "Data penduduk baru berhasil ditambahkan.";
            } else {
                $pesan = "Error: " . mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<?php if(!empty($pesan)): ?>
    <div class="card" style="background: #28a745;">
        <p><?php echo $pesan; ?></p>
    </div>
<?php endif; ?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <div class="card">
        <h4><i class="fas fa-id-card"></i> Data Diri & Keluarga</h4>
        <div class="form-row">
            <div class="form-group">
                <label>NIK</label>
                <input type="text" name="nik" class="form-control" required>
            </div>
             <div class="form-group">
                <label>Nomor KK</label>
                <input type="text" name="nomor_kk" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-control" required>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>
    </div>

    <div class="card">
        <h4><i class="fas fa-home"></i> Data Alamat & Lainnya</h4>
        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" rows="2" required></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>RT</label>
                <input type="text" name="rt" class="form-control" required>
            </div>
            <div class="form-group">
                <label>RW</label>
                <input type="text" name="rw" class="form-control" required>
            </div>
        </div>
         <div class="form-row">
            <div class="form-group">
                <label>Agama</label>
                <select name="agama" class="form-control" required>
                    <option value="">-- Pilih Agama --</option>
                    <option value="Islam">Islam</option>
                    <option value="Kristen Protestan">Kristen Protestan</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Khonghucu">Khonghucu</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status Perkawinan</label>
                <select name="status_perkawinan" class="form-control" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Belum Kawin">Belum Kawin</option>
                    <option value="Kawin">Kawin</option>
                    <option value="Cerai Hidup">Cerai Hidup</option>
                    <option value="Cerai Mati">Cerai Mati</option>
                </select>
            </div>
            </div>
         <div class="form-row">
            <div class="form-group">
                <label>Pekerjaan</label>
                <input type="text" name="pekerjaan" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Kewarganegaraan</label>
                <input type="text" name="kewarganegaraan" class="form-control" value="WNI" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>TPS</label>
                <input type="text" name="tps" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Dapil</label>
                <input type="text" name="dapil" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label>Nama Koordinator</label>
            <input type="text" name="nama_koordinator" class="form-control">
        </div>
    </div>

    <div class="card">
        <h4><i class="fas fa-upload"></i> Upload Berkas</h4>
        <div class="form-group">
            <label>Upload Foto Diri (jpg, png)</label>
            <input type="file" name="file_foto" class="form-control" required accept="image/jpeg, image/png">
        </div>
        <div class="form-group">
            <label>Upload Scan KK (jpg, png, pdf)</label>
            <input type="file" name="file_kk" class="form-control" required accept="image/jpeg, image/png, application/pdf">
        </div>
    </div>
    
    <div class="form-group">
        <input type="submit" class="btn-submit" value="Simpan Data Penduduk">
    </div>
</form>

<?php require_once 'template_footer.php'; ?>