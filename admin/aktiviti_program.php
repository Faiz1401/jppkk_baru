<?php
session_start();
include '../db_connection.php';

// ====== Proses Simpan / Update ======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_program   = $_POST['id_program'] ?? 0;
    $institusi    = $_POST['INSTITUSI'];
    $jabatan      = $_POST['JABATAN'];
    $jenisprogram = $_POST['JENISPROGRAM'];
    $kodprogram   = $_POST['KODPROGRAM'];
    $namaprogram  = $_POST['NAMAPROGRAM'];
    $bilkursus    = $_POST['BILKURSUS'];
    $neccode      = $_POST['NEC_CODE'];
    $akreditasi   = $_POST['AKREDITASI'];
    $versi        = $_POST['VERSI'];
    $tempoh       = $_POST['TEMPOH_PENGAJIAN'];
    $status       = $_POST['STATUS'];

    if ($id_program > 0) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE tblprogram SET 
            INSTITUSI=?, JABATAN=?, JENISPROGRAM=?, KODPROGRAM=?, NAMAPROGRAM=?, 
            BILKURSUS=?, NEC_CODE=?, AKREDITASI=?, VERSI=?, TEMPOH_PENGAJIAN=?, STATUS=? 
            WHERE IDPROGRAM=?");
        $stmt->bind_param("sssssisissii", $institusi, $jabatan, $jenisprogram, $kodprogram, $namaprogram,
                          $bilkursus, $neccode, $akreditasi, $versi, $tempoh, $status, $id_program);
        $ok = $stmt->execute();
        $_SESSION['msg'] = $ok ? "✅ Program berjaya dikemaskini!" : "❌ Ralat: ".$stmt->error;
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO tblprogram 
            (INSTITUSI,JABATAN,JENISPROGRAM,KODPROGRAM,NAMAPROGRAM,
             BILKURSUS,NEC_CODE,AKREDITASI,VERSI,TEMPOH_PENGAJIAN,STATUS)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssisissi", $institusi, $jabatan, $jenisprogram, $kodprogram, $namaprogram,
                          $bilkursus, $neccode, $akreditasi, $versi, $tempoh, $status);
        $ok = $stmt->execute();
        $_SESSION['msg'] = $ok ? "✅ Program baru berjaya ditambah!" : "❌ Ralat: ".$stmt->error;
    }
    header("Location: program_manage.php");
    exit;
}

// ====== Ambil senarai program ======
$sql = "SELECT * FROM tblprogram ORDER BY IDPROGRAM DESC";
$programs = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Urus Program</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {font-family: Arial, sans-serif; background:#f4f6f9; margin:0; padding-bottom:200px;}
    h2 {text-align:center;font-size:22px;font-weight:700;color:#2c3e50;margin:20px 0;}
    form {background:#fff;padding:20px;border-radius:12px;max-width:900px;margin:30px auto;
          display:grid;grid-template-columns:1fr 1fr;gap:15px;}
    form h2 {grid-column: span 2;}
    label{font-weight:600;margin-bottom:5px;display:block;}
    input,select,button{width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;}
    button{grid-column: span 2;background:#007bff;color:#fff;font-weight:600;cursor:pointer;}
    table{width:95%;margin:20px auto;border-collapse:collapse;background:#fff;
          box-shadow:0 3px 8px rgba(0,0,0,.1);border-radius:10px;overflow:hidden;}
    th,td{padding:10px;border-bottom:1px solid #eee;}
    th{background:#007bff;color:#fff;}
    tr:nth-child(even){background:#f9f9f9;}
    .btn-del{background:#dc3545;color:#fff;border:none;padding:6px 10px;border-radius:5px;cursor:pointer;}
    .btn-del:hover{background:#b02a37;}
    .btn-edit{background:#ffc107;color:#000;border:none;padding:6px 10px;border-radius:5px;cursor:pointer;}
    .btn-edit:hover{background:#e0a800;}
  </style>
</head>
<body>

<!-- ====== Form tambah/update program ====== -->
<form method="POST">
  <h2>Tambah / Kemaskini Program</h2>
  <input type="hidden" name="id_program" id="id_program">

  <div>
    <label>Institusi:</label>
    <input type="text" name="INSTITUSI" id="INSTITUSI" required>
  </div>
  <div>
    <label>Jabatan:</label>
    <input type="text" name="JABATAN" id="JABATAN" required>
  </div>
  <div>
    <label>Jenis Program:</label>
    <input type="text" name="JENISPROGRAM" id="JENISPROGRAM">
  </div>
  <div>
    <label>Kod Program:</label>
    <input type="text" name="KODPROGRAM" id="KODPROGRAM">
  </div>
  <div style="grid-column: span 2;">
    <label>Nama Program:</label>
    <input type="text" name="NAMAPROGRAM" id="NAMAPROGRAM" required>
  </div>
  <div>
    <label>Bilangan Kursus:</label>
    <input type="number" name="BILKURSUS" id="BILKURSUS">
  </div>
  <div>
    <label>NEC Code:</label>
    <input type="number" name="NEC_CODE" id="NEC_CODE">
  </div>
  <div>
    <label>Akreditasi:</label>
    <input type="text" name="AKREDITASI" id="AKREDITASI">
  </div>
  <div>
    <label>Versi:</label>
    <input type="text" name="VERSI" id="VERSI">
  </div>
  <div>
    <label>Tempoh (Tahun):</label>
    <input type="number" name="TEMPOH_PENGAJIAN" id="TEMPOH_PENGAJIAN">
  </div>
  <div>
    <label>Status:</label>
    <select name="STATUS" id="STATUS">
      <option value="1">Aktif</option>
      <option value="0">Tidak Aktif</option>
    </select>
  </div>

  <button type="submit">Simpan</button>
</form>

<!-- ====== Senarai program ====== -->
<h2>Senarai Program</h2>
<table>
  <tr>
    <th>ID</th><th>Nama Program</th><th>Institusi</th><th>Jabatan</th>
    <th>Kod</th><th>Tempoh</th><th>Status</th><th>Tindakan</th>
  </tr>
  <?php while($row = $programs->fetch_assoc()): ?>
    <tr>
      <td><?= $row['IDPROGRAM'] ?></td>
      <td><?= $row['NAMAPROGRAM'] ?></td>
      <td><?= $row['INSTITUSI'] ?></td>
      <td><?= $row['JABATAN'] ?></td>
      <td><?= $row['KODPROGRAM'] ?></td>
      <td><?= $row['TEMPOH_PENGAJIAN'] ?> thn</td>
      <td><?= $row['STATUS']==1?'Aktif':'Tidak Aktif' ?></td>
      <td>
        <button class="btn-edit" 
          onclick='editProgram(<?= json_encode($row) ?>)'>
          <i class="fa fa-edit"></i> Edit
        </button>
        <button class="btn-del" onclick="padamProgram(<?= $row['IDPROGRAM'] ?>)">
          <i class="fa fa-trash"></i> Padam
        </button>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

<script>
function editProgram(data){
  // isi form dengan data untuk update
  $("#id_program").val(data.IDPROGRAM);
  $("#INSTITUSI").val(data.INSTITUSI);
  $("#JABATAN").val(data.JABATAN);
  $("#JENISPROGRAM").val(data.JENISPROGRAM);
  $("#KODPROGRAM").val(data.KODPROGRAM);
  $("#NAMAPROGRAM").val(data.NAMAPROGRAM);
  $("#BILKURSUS").val(data.BILKURSUS);
  $("#NEC_CODE").val(data.NEC_CODE);
  $("#AKREDITASI").val(data.AKREDITASI);
  $("#VERSI").val(data.VERSI);
  $("#TEMPOH_PENGAJIAN").val(data.TEMPOH_PENGAJIAN);
  $("#STATUS").val(data.STATUS);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function padamProgram(id){
  Swal.fire({
    title: "Padam Program?",
    text: "Tindakan ini tidak boleh diundur.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya, padam",
    cancelButtonText: "Batal"
  }).then((result) => {
    if(result.isConfirmed){
      window.location = "program_delete.php?id=" + id;
    }
  });
}
</script>

<?php if (isset($_SESSION['msg'])): ?>
<script>
Swal.fire({icon:'info',title:'Makluman',text:'<?= $_SESSION['msg']; ?>'});
</script>
<?php unset($_SESSION['msg']); endif; ?>

</body>
</html>
