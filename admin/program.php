<?php
include '../db_connection.php';

// ====== Search ======
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// ====== Setup Pagination ======
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// Kira jumlah rekod (ikut search)
$countSql = "SELECT COUNT(*) AS total FROM tblprogram WHERE NAMAPROGRAM LIKE ?";
$stmt = $conn->prepare($countSql);
$like = "%$search%";
$stmt->bind_param("s", $like);
$stmt->execute();
$countResult = $stmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Ambil data ikut pagination + search
$sql = "SELECT IDPROGRAM, INSTITUSI, JABATAN, JENISPROGRAM, KODPROGRAM, NAMAPROGRAM, 
               BILKURSUS, NEC_CODE, AKREDITASI, VERSI, TEMPOH_PENGAJIAN, STATUS
        FROM tblprogram
        WHERE NAMAPROGRAM LIKE ?
        ORDER BY IDPROGRAM ASC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $like, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Senarai Program</title>
  <!-- Bootstrap 5 CSS -->
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
            <a href="aktiviti_program.php"><i class="fa fa-fw fa-user"></i> Aktiviti</a>
        </div>
  </div> 
    <a href="#"><i class="fa-solid fa-check-to-slot"></i> Permohonan</a>
    <a href="#"><i class="fa-solid fa-circle-exclamation"></i> Hebahan</a> 
    <a href="user-maintenance.php"><i class="fa fa-fw fa-user"></i> Akuan</a>
</div>

<div class="container my-5">
  <div class="card shadow-lg">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0">📋 Senarai Program</h4>
      <a href="tambah_program.php" class="btn btn-light btn-sm">
        <i class="fa fa-plus"></i> Tambah Program
      </a>
    </div>
    <div class="card-body">

      <!-- Search Form -->
      <form method="get" class="mb-3">
        <div class="input-group">
          <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Cari nama program...">
          <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Cari</button>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle shadow-sm rounded">
          <thead style="background: linear-gradient(45deg, #007bff, #00b4d8); color: white;" class="text-center">
            <tr>
              <th>ID</th>
              <th>Institusi</th>
              <th>Jabatan</th>
              <th>Jenis</th>
              <th>Kod</th>
              <th>Nama Program</th>
              <th>Bil. Kursus</th>
              <th>NEC Code</th>
              <th>Akreditasi</th>
              <th>Versi</th>
              <th>Tempoh (Tahun)</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td class="text-center fw-bold"><?= $row['IDPROGRAM'] ?></td>
                  <td><?= $row['INSTITUSI'] ?></td>
                  <td><?= $row['JABATAN'] ?></td>
                  <td><?= $row['JENISPROGRAM'] ?></td>
                  <td class="text-primary fw-semibold"><?= $row['KODPROGRAM'] ?></td>
                  <td>
                    <a href="edit_program.php?id=<?= $row['IDPROGRAM'] ?>" class="text-decoration-none text-dark fw-semibold">
                        <?= $row['NAMAPROGRAM'] ?>
                    </a>
                    </td>
                  <td class="text-center"><?= $row['BILKURSUS'] ?></td>
                  <td class="text-center"><?= $row['NEC_CODE'] ?></td>
                  <td><?= $row['AKREDITASI'] ?></td>
                  <td><?= $row['VERSI'] ?></td>
                  <td class="text-center"><?= $row['TEMPOH_PENGAJIAN'] ?></td>
                  <td class="text-center">
                    <?php if ($row['STATUS'] == 1): ?>
                      <span style="position:static;" class="badge bg-success px-3 py-2">Aktif</span>
                    <?php else: ?>
                      <span style="position:static;"  class="badge bg-secondary px-3 py-2">Tidak Aktif</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="12" class="text-center text-muted">Tiada rekod program.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <nav>
        <ul class="pagination justify-content-center">
          <?php if ($page > 1): ?>
            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">&laquo; Prev</a></li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next &raquo;</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
