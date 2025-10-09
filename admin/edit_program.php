<?php
include '../db_connection.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = "";

// Ambil data asal
$stmt = $conn->prepare("SELECT * FROM tblprogram WHERE IDPROGRAM = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$program = $result->fetch_assoc();

if (!$program) {
    die("❌ Program tidak dijumpai.");
}

// Proses update
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

    $update = $conn->prepare("UPDATE tblprogram SET 
        INSTITUSI=?, JABATAN=?, JENISPROGRAM=?, KODPROGRAM=?, NAMAPROGRAM=?, 
        BILKURSUS=?, NEC_CODE=?, AKREDITASI=?, VERSI=?, TEMPOH_PENGAJIAN=?, STATUS=? 
        WHERE IDPROGRAM=?");
    $update->bind_param("sssssisissii", 
        $institusi, $jabatan, $jenisprogram, $kodprogram, $namaprogram, 
        $bilkursus, $neccode, $akreditasi, $versi, $tempoh, $status, $id
    );

    if ($update->execute()) {
        $message = '<div class="alert alert-success">✅ Data program berjaya dikemaskini!</div>';
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM tblprogram WHERE IDPROGRAM = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $program = $result->fetch_assoc();
    } else {
        $message = '<div class="alert alert-danger">❌ Ralat: ' . $update->error . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Edit Program</title>
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
    <div class="card-header bg-warning text-dark">
      <h4 class="mb-0"><i class="fa fa-edit"></i> Edit Program</h4>
    </div>
    <div class="card-body">
      <?= $message ?>
      <form method="POST">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Institusi</label>
            <input type="text" name="INSTITUSI" class="form-control" value="<?= $program['INSTITUSI'] ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Jabatan</label>
            <input type="text" name="JABATAN" class="form-control" value="<?= $program['JABATAN'] ?>" required>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Jenis Program</label>
            <input type="text" name="JENISPROGRAM" class="form-control" value="<?= $program['JENISPROGRAM'] ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Kod Program</label>
            <input type="text" name="KODPROGRAM" class="form-control" value="<?= $program['KODPROGRAM'] ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Nama Program</label>
          <input type="text" name="NAMAPROGRAM" class="form-control" value="<?= $program['NAMAPROGRAM'] ?>" required>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Bilangan Kursus</label>
            <input type="number" name="BILKURSUS" class="form-control" value="<?= $program['BILKURSUS'] ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">NEC Code</label>
            <input type="number" name="NEC_CODE" class="form-control" value="<?= $program['NEC_CODE'] ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Akreditasi</label>
            <input type="text" name="AKREDITASI" class="form-control" value="<?= $program['AKREDITASI'] ?>">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Versi</label>
            <input type="text" name="VERSI" class="form-control" value="<?= $program['VERSI'] ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Tempoh (Tahun)</label>
            <input type="number" name="TEMPOH_PENGAJIAN" class="form-control" value="<?= $program['TEMPOH_PENGAJIAN'] ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="STATUS" class="form-select">
              <option value="1" <?= $program['STATUS']==1 ? 'selected' : '' ?>>Aktif</option>
              <option value="0" <?= $program['STATUS']==0 ? 'selected' : '' ?>>Tidak Aktif</option>
            </select>
          </div>
        </div>

        <div class="d-flex justify-content-between">
          <a href="program.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
          <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Kemaskini</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
