<?php
// Sambung ke DB
$conn = new mysqli("localhost", "root", "", "jppkk_test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Carian
$search = "";
$result = null;
$noUser = false;

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT u.ID, u.NAMA, u.EMAIL, u.NO_IC, u.PHONE, u.STATUS, 
                                    u.IDJAWATAN, j.NAMA_JAWATAN
                             FROM tbluser u
                             LEFT JOIN tbljawatan j ON u.IDJAWATAN = j.IDJAWATAN
                             WHERE u.NAMA LIKE ? OR u.NO_IC LIKE ?
                             ORDER BY u.ID DESC");
    $like = "%".$search."%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) $noUser = true;
} else {
    $result = $conn->query("SELECT u.ID, u.NAMA, u.EMAIL, u.NO_IC, u.PHONE, u.STATUS, 
                                   u.IDJAWATAN, j.NAMA_JAWATAN
                            FROM tbluser u
                            LEFT JOIN tbljawatan j ON u.IDJAWATAN = j.IDJAWATAN
                            ORDER BY u.ID DESC");
}

// Senarai jawatan untuk dropdown
$jawatanRes = $conn->query("SELECT IDJAWATAN, NAMA_JAWATAN FROM tbljawatan");
$jawatanList = [];
while($j = $jawatanRes->fetch_assoc()){ $jawatanList[]=$j; }
?>
<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<title>User Maintenance</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
h1{text-align:center;font-size:28px;font-weight:700;color:#2c3e50;margin:30px 0;}
.table-container{margin:20px auto;max-width:100%;background:#fff;box-shadow:0 6px 16px rgba(0,0,0,.08);padding:15px;border-radius:12px;}
table{width:100%;border-collapse:collapse;}
th,td{padding:12px;text-align:center;border-bottom:1px solid #eee;}
th{background:#3498db;color:#fff;text-transform:uppercase;font-size:13px;}
tr:hover{background:#f9f9f9;cursor:pointer;}
.status-badge{padding:5px 10px;border-radius:15px;color:#fff;font-size:12px;}
.status-badge.aktif{background:#27ae60;}
.status-badge.tidak{background:#e74c3c;}
.details{display:none;background:#f4f6f9;}
.details td{text-align:left;}
.details form{padding:10px;}
.details label{display:block;margin-top:8px;font-weight:bold;}
.details select,.details button{padding:6px 10px;margin-top:4px;}
.details button{background:#8e44ad;color:#fff;border:none;border-radius:6px;cursor:pointer;}
.details button:hover{background:#732d91;}
.search-box{text-align:center;margin:20px 0;}
.search-box input{padding:8px;border:1px solid #ccc;border-radius:6px;width:250px;}
.search-box button{padding:8px 14px;border:none;border-radius:6px;background:#3498db;color:#fff;cursor:pointer;}
.search-box button:hover{background:#2980b9;}
</style>
<script>
function toggleDetails(id){
  var row=document.getElementById("detail-"+id);
  row.style.display=(row.style.display==="table-row")?"none":"table-row";
}
</script>
</head>
<body>

<h1>Senarai Pengguna</h1>

<div class="search-box">
  <form method="get">
    <input type="text" name="search" placeholder="Cari Nama atau IC..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit"><i class="fa fa-search"></i> Cari</button>
  </form>
</div>

<?php if($result && $result->num_rows>0): ?>
<div class="table-container">
<table>
<tr>
  <th>Nama</th><th>Email</th><th>No. IC</th><th>No. Phone</th><th>Status</th><th>Jawatan</th>
</tr>
<?php while($row=$result->fetch_assoc()): ?>
<tr onclick="toggleDetails(<?= $row['ID'] ?>)">
  <td><strong><?= htmlspecialchars($row['NAMA']) ?></strong></td>
  <td><?= htmlspecialchars($row['EMAIL']) ?></td>
  <td><?= htmlspecialchars($row['NO_IC']) ?></td>
  <td><?= htmlspecialchars($row['PHONE']) ?></td>
  <td><?= $row['STATUS']==1?'<span class="status-badge aktif">Aktif</span>':'<span class="status-badge tidak">Tidak Aktif</span>' ?></td>
  <td><?= $row['NAMA_JAWATAN']??'-' ?></td>
</tr>
<tr id="detail-<?= $row['ID'] ?>" class="details">
  <td colspan="6">
    <form method="post" action="update_user.php">
      <input type="hidden" name="id" value="<?= $row['ID'] ?>">

      <label>Status</label>
      <select name="status">
        <option value="1" <?= $row['STATUS']==1?'selected':'' ?>>Aktif</option>
        <option value="0" <?= $row['STATUS']==0?'selected':'' ?>>Tidak Aktif</option>
      </select>

      <label>Jawatan</label>
      <select name="idjawatan">
        <?php foreach($jawatanList as $j): ?>
          <option value="<?= $j['IDJAWATAN'] ?>" <?= ($row['IDJAWATAN']==$j['IDJAWATAN'])?'selected':'' ?>>
            <?= htmlspecialchars($j['NAMA_JAWATAN']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <br><br>
      <button type="submit">Simpan Perubahan</button>
    </form>
  </td>
</tr>
<?php endwhile; ?>
</table>
</div>
<?php endif; ?>

<?php if($noUser): ?>
<script>
Swal.fire({icon:'error',title:'Tidak Jumpa!',text:'Pengguna tidak dijumpai dalam sistem.',confirmButtonColor:'#3498db'})
</script>
<?php endif; ?>

</body>
</html>
<?php $conn->close(); ?>
