<?php
session_start();
include '../db_connection.php';

// ====== Proses Simpan ======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bengkel = $_POST['id_bengkel'];
    $id_user    = $_POST['id_user'];
    $catatan    = $_POST['catatan'];

    $stmt = $conn->prepare("INSERT INTO tblpenggubal (IDBENGKEL, IDUSER, CATATAN) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_bengkel, $id_user, $catatan);

    if ($stmt->execute()) {
        $_SESSION['msg'] = "Ketua Penggubal berjaya ditetapkan!";
    } else {
        $_SESSION['msg'] = "Ralat: " . $stmt->error;
    }
    header("Location: penggubal.php?id_bengkel=".$id_bengkel);
    exit;
}

// ====== Ambil Data Bengkel ======
$bengkel = $conn->query("
  SELECT 
    IDBENGKEL, 
    CONCAT(KATEGORI, ' Bil.', LPAD(CAST(SIRIBENGKEL AS UNSIGNED),2,'0'), '/', TAHUN) AS NAMA_BENGKEL
  FROM tblbengkel
  ORDER BY TARIKHMULA ASC
");

// ====== Filter ikut bengkel kalau ada ======
$id_bengkel_filter = isset($_GET['id_bengkel']) ? intval($_GET['id_bengkel']) : 0;

$sql = "
SELECT k.IDKETUA, k.CATATAN, 
       u.NAMA, u.PHONE, u.EMAIL, j.NAMA_JAWATAN AS GRED,
       p.NAMAPROGRAM AS PROGRAM, i.NAMA_INSTITUSI AS INSTITUSI,
       CONCAT(b.KATEGORI, ' Bil.', LPAD(CAST(b.SIRIBENGKEL AS UNSIGNED),2,'0'), '/', b.TAHUN) AS NAMA_BENGKEL,
       b.IDBENGKEL
FROM tblpenggubal k
JOIN tbluser u ON k.IDUSER = u.ID
LEFT JOIN tbljawatan j ON u.IDJAWATAN = j.IDJAWATAN
LEFT JOIN tblprogram p ON u.PROGRAM = p.IDPROGRAM
LEFT JOIN tblinstitusi i ON u.INSTITUSI = i.IDINSTITUSI
JOIN tblbengkel b ON k.IDBENGKEL = b.IDBENGKEL
";
if ($id_bengkel_filter > 0) {
    $sql .= " WHERE k.IDBENGKEL = $id_bengkel_filter ";
}
$sql .= " ORDER BY b.TARIKHMULA DESC";
$ketua = $conn->query($sql);

// Ambil user untuk dropdown
$user = $conn->query("
  SELECT u.ID, u.NAMA, j.NAMA_JAWATAN AS GRED, 
         p.NAMAPROGRAM AS PROGRAM, i.NAMA_INSTITUSI AS INSTITUSI
  FROM tbluser u
  LEFT JOIN tbljawatan j ON u.IDJAWATAN = j.IDJAWATAN
  LEFT JOIN tblprogram p ON u.PROGRAM = p.IDPROGRAM
  LEFT JOIN tblinstitusi i ON u.INSTITUSI = i.IDINSTITUSI
  WHERE u.STATUS=1
");
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Lantik Ketua Penggubal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {font-family: Arial, sans-serif; background:#f4f6f9; margin:0; padding-bottom:200px;}
        h1, h2 {            
      text-align: center;
      font-size: 24px;
      font-weight: 700;
      color: #2c3e50;
      margin: 20px 0 15px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      letter-spacing: 1px;
      position: relative;
    }
    h1::after, h2::after {
      content: '';
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, #3498db, #9b59b6);
      display: block;
      margin: 8px auto 0;
      border-radius: 3px;
    }
    form {background:#fff;padding:30px;border-radius:12px;max-width:800px;margin:30px auto;
          display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    form h2 {grid-column: span 2;}
    label{font-weight:600;margin-bottom:5px;display:block;}
    select,textarea,button{width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;}
    textarea{grid-column:span 2;min-height:100px;}
    button{grid-column:span 2;background:#007bff;color:#fff;font-weight:600;cursor:pointer;}
    table{width:95%;margin:20px auto;border-collapse:collapse;background:#fff;
          box-shadow:0 3px 8px rgba(0,0,0,.1);border-radius:10px;overflow:hidden;}
    th,td{padding:10px;border-bottom:1px solid #eee;}
    th{background:#007bff;color:#fff;}
    tr:nth-child(even){background:#f9f9f9;}
    .btn-del{background:#dc3545;color:#fff;border:none;padding:6px 10px;border-radius:5px;cursor:pointer;}
    .btn-del:hover{background:#b02a37;}
    /* Samakan saiz Select2 dengan input lain */
.select2-container .select2-selection--single {
  height: 42px !important;        /* ikut tinggi input lain */
  border: 1px solid #ccc !important;
  border-radius: 6px !important;
  padding: 6px 10px;
  display: flex;
  align-items: center;
  box-sizing: border-box;
}

/* Teks dalam kotak select2 */
.select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 28px !important;
  font-size: 14px;
  color: #333;
}

/* Dropdown arrow */
.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 100% !important;
  right: 10px;
}

  </style>
</head>
<body>

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

<div class="navbar">
  <a href="index.php"><i class="fa fa-fw fa-home"></i> Dashboard</a> 
  <a href="bengkel.php"><i class="fa-solid fa-calendar"></i> Bengkel</a> 

  <!-- Dropdown Menu -->
  <div class="dropdown">
    <button class="dropbtn"><i class="fa fa-bars"></i> Senarai <i class="fa fa-caret-down"></i></button>
    <div class="dropdown-content">
      <a class="active" href="penggubal.php"><i class="fa fa-fw fa-user"></i> Ketua Penggubal</a>
      <a href="urusetia.php"><i class="fa fa-fw fa-user"></i> Urusetia</a>
      <a href="#"><i class="fa fa-fw fa-user"></i> Ahli JK3P2K</a>
    </div>
  </div> 
    <div class="dropdown">
        <button class="dropbtn"><i class="fa fa-bars"></i> program <i class="fa fa-caret-down"></i></button>
        <div class="dropdown-content">
            <a class="active" href="program.php"><i class="fa fa-fw fa-user"></i> Senarai Program</a>
            <a href="tambah_program.php"><i class="fa fa-fw fa-user"></i> Program Baru</a>
            <a href="sah_program"><i class="fa fa-fw fa-user"></i> Sah Program</a>
        </div>
  </div> 
    <a href="#"><i class="fa-solid fa-check-to-slot"></i> Permohonan</a>
    <a href="#"><i class="fa-solid fa-circle-exclamation"></i> Hebahan</a> 
    <a href="user-maintenance.php"><i class="fa fa-fw fa-user"></i> Akuan</a>
</div>

<!-- ====== Form tambah ketua ====== -->
<form method="POST">
  <h2>Lantikan Ketua Penggubal</h2>
  <div>
    <label>Pilih Bengkel:</label>
    <select name="id_bengkel" required onchange="location='penggubal.php?id_bengkel='+this.value">
      <option value="">-- Pilih Bengkel --</option>
      <?php 
      $bengkel->data_seek(0);
      while($b = $bengkel->fetch_assoc()): ?>
        <option value="<?= $b['IDBENGKEL'] ?>" <?= $id_bengkel_filter==$b['IDBENGKEL']?'selected':'' ?>>
          <?= $b['NAMA_BENGKEL'] ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

<div>
  <label>Pilih Ketua Penggubal:</label>
  <select name="id_user" id="id_user" class="select2" required style="width:100%;">
    <option value="">-- Pilih User --</option>
    <?php while($u = $user->fetch_assoc()): ?>
      <option value="<?= $u['ID'] ?>">
        <?= $u['NAMA'] ?> | <?= $u['PROGRAM'] ?> | <?= $u['INSTITUSI'] ?> | <?= $u['GRED'] ?>
      </option>
    <?php endwhile; ?>
  </select>
</div>

  <div>
    <label>Catatan:</label>
    <textarea name="catatan"></textarea>
  </div>
  <button type="submit">Simpan</button>
</form>

<!-- ====== Senarai ikut bengkel ====== -->
<h2>Senarai Ketua Penggubal</h2>
<table>
  <tr>
    <th>Bengkel</th><th>Nama</th><th>Program</th><th>Institusi</th>
    <th>Gred</th><th>Phone</th><th>Email</th><th>Catatan</th><th>Tindakan</th>
  </tr>
  <?php while($row = $ketua->fetch_assoc()): ?>
    <tr>
      <td><?= $row['NAMA_BENGKEL'] ?></td>
      <td><?= $row['NAMA'] ?></td>
      <td><?= $row['PROGRAM'] ?></td>
      <td><?= $row['INSTITUSI'] ?></td>
      <td><?= $row['GRED'] ?></td>
      <td><?= $row['PHONE'] ?></td>
      <td><?= $row['EMAIL'] ?></td>
      <td><?= $row['CATATAN'] ?></td>
      <td>
        <button class="btn-del" onclick="keluarkan(<?= $row['IDKETUA'] ?>, <?= $row['IDBENGKEL'] ?>)">
          <i class="fa fa-trash"></i> Keluarkan
        </button>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

<script>
function keluarkan(id,id_bengkel){
  Swal.fire({
    title: "Keluarkan Ketua Penggubal?",
    text: "Tindakan ini akan membuang rekod ketua penggubal.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya, keluarkan",
    cancelButtonText: "Batal"
  }).then((result) => {
    if(result.isConfirmed){
      window.location = "delete_penggubal.php?id=" + id + "&id_bengkel=" + id_bengkel;
    }
  });
}
</script>
<script>
$(document).ready(function() {
  $('.select2').select2({
    placeholder: "-- Pilih User --",
    allowClear: true
  });
});
</script>


<?php if (isset($_SESSION['msg'])): ?>
<script>
Swal.fire({icon:'info',title:'Makluman',text:'<?= $_SESSION['msg']; ?>'});
</script>
<?php unset($_SESSION['msg']); endif; ?>

</body>
</html>
