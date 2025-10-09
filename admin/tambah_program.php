<?php
include '../db_connection.php';

$message = "";

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $institusi       = $_POST['INSTITUSI'];
    $jabatan         = $_POST['JABATAN'];
    $jenisprogram    = $_POST['JENISPROGRAM'];
    $kodprogram      = $_POST['KODPROGRAM'];
    $namaprogram     = $_POST['NAMAPROGRAM'];
    $bilkursus       = $_POST['BILKURSUS'];
    $neccode         = $_POST['NEC_CODE'];
    $akreditasi      = $_POST['AKREDITASI'];
    $versi           = $_POST['VERSI'];
    $tempoh          = $_POST['TEMPOH_PENGAJIAN'];
    $status          = $_POST['STATUS'];

    $stmt = $conn->prepare("INSERT INTO tblprogram 
        (INSTITUSI, JABATAN, JENISPROGRAM, KODPROGRAM, NAMAPROGRAM, BILKURSUS, NEC_CODE, AKREDITASI, VERSI, TEMPOH_PENGAJIAN, STATUS) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssisissi", 
        $institusi, $jabatan, $jenisprogram, $kodprogram, $namaprogram, 
        $bilkursus, $neccode, $akreditasi, $versi, $tempoh, $status
    );

    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">✅ Program berjaya ditambah!</div>';
    } else {
        $message = '<div class="alert alert-danger">❌ Ralat: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}
$conn->close();
?>
<?php
include '../db_connection.php';

$institusiOptions = [];
$sql = "SELECT IDINSTITUSI, NAMA_INSTITUSI FROM tblinstitusi";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $institusiOptions[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Tambah Program</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <div class="topnav">
      <a href="#" class="logo">LOGO</a>
      <div class="icons">
        <a href="#" class="icon-btn"><i class="fa fa-bell"></i><span class="badge">3</span></a>
        <a href="#" class="icon-btn"><i class="fa fa-envelope"></i><span class="badge">5</span></a>
        <div class="dropdown">
          <button class="dropbtn"><i class="fa fa-user-circle"></i></button>
          <div class="dropdown-content">
            <a href="#profile"><i class="fa fa-user"></i> Profile</a>
            <a href="#settings"><i class="fa fa-cog"></i> Settings</a>
            <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>
    </div>

<div class="container my-5">
  <div class="card shadow-lg">
    <div class="card-header bg-success text-white">
      <h4 class="mb-0"><i class="fa fa-plus"></i> Tambah Program</h4>
    </div>
    <div class="card-body">
      <?= $message ?>
      <form method="POST">
        <div class="row mb-3">
                <div class="col-md-6">
                    <label for="institusi" class="form-label">>Institusi</label>
                    <select id="institusi" name="institusi" required>
                        <option value="">-- Pilih Institusi --</option>
                        <?php foreach($institusiOptions as $i): ?>
                            <option value="<?= $i['IDINSTITUSI'] ?>"><?= $i['NAMA_INSTITUSI'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
          <div class="col-md-6">
            <label class="form-label">Jabatan</label>
            <input type="text" name="JABATAN" class="form-control" required>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Jenis Program</label>
            <input type="text" name="JENISPROGRAM" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Kod Program</label>
            <input type="text" name="KODPROGRAM" class="form-control">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Nama Program</label>
          <input type="text" name="NAMAPROGRAM" class="form-control" required>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Bilangan Kursus</label>
            <input type="number" name="BILKURSUS" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">NEC Code</label>
            <input type="number" name="NEC_CODE" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Akreditasi</label>
            <input type="text" name="AKREDITASI" class="form-control">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Versi</label>
            <input type="text" name="VERSI" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Tempoh (Tahun)</label>
            <input type="number" name="TEMPOH_PENGAJIAN" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="STATUS" class="form-select">
              <option value="1">Aktif</option>
              <option value="0">Tidak Aktif</option>
            </select>
          </div>
        </div>

        <div class="d-flex justify-content-between">
          <a href="program.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
          <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
