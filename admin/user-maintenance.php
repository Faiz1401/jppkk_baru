<?php
session_start();

// Kalau user belum login → tendang ke index/login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Kawalan cache supaya page tak boleh back lepas logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Sambung ke DB
$conn = new mysqli("localhost", "root", "", "jppkk_test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Semak kalau ada carian
$search = "";
$result = null;
$noUser = false; // Flag utk detect tiada user

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
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

    <title>User Maintenance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        h1 {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin: 30px 0;
            position: relative;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 1px;
        }
        h1::after {
            content: '';
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #3498db, #9b59b6);
            display: block;
            margin: 8px auto 0 auto;
            border-radius: 3px;
        }

        /* Container untuk responsive */
        .table-container {
            margin: 20px auto;
            overflow-x: auto;   /* scroll horizontal */
            max-width: 100%;    /* responsive ikut skrin */
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
            padding: 15px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 15px;
            border-radius: 12px;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Header */
        th {
            background: #3498db; /* warna solid biru */
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            padding: 14px 16px;
            letter-spacing: 0.5px;
            text-align: center;
            font-size: 13px;
        }

        /* Sel biasa */
        td {
            padding: 14px 16px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
            color: #2c3e50;
        }

        /* Hover row */
        tr:hover td {
            background: #f4f9ff;
            transition: 0.25s;
        }

        /* Striping baris */
        tr:nth-child(even) td {
            background: #fafafa;
        }

        /* Butang detail */
        a.btn.detail {
            background: #8e44ad;
            color: #fff;
            padding: 8px 14px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: 0.3s;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
        }
        a.btn.detail:hover {
            background: #732d91;
            transform: translateY(-2px);
        }

        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: bold;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .status-badge.aktif { background: #27ae60; }
        .status-badge.tidak { background: #e74c3c; }

        /* Search */
.search-box {
    margin: 20px 0;
    text-align: left; /* ikut kiri asal */
}

.search-box form {
    margin-left: 70%; /* tolak ke kanan sikit (adjust ikut citarasa) */
}

.search-box input[type="text"] {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    width: 250px; /* lebar tetap */
}

.search-box button {
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    background: #3498db;
    color: #fff;
    font-size: 14px;
    cursor: pointer;
    margin-left: 5px;
}
.search-box button:hover {
    background: #2980b9;
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

     <h1>Senarai Pengguna</h1>

    <!-- Kotak Search -->
    <div class="search-box">
        <form method="get" action="">
            <input type="text" name="search" placeholder="Cari Nama atau IC..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fa fa-search"></i> Cari</button>
        </form>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-container">
            <table>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. IC</th>
                    <th>No. Phone</th>
                    <th>Status</th>
                    <th>Butiran</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['NAMA']) ?></td>
                    <td><?= htmlspecialchars($row['EMAIL']) ?></td>
                    <td><?= htmlspecialchars($row['NO_IC']) ?></td>
                    <td><?= htmlspecialchars($row['PHONE']) ?></td>
                    <td>
                        <?php if ($row['STATUS'] == 1): ?>
                            <span class="status-badge aktif">Aktif</span>
                        <?php else: ?>
                            <span class="status-badge tidak">Tidak Aktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="btn detail" href="detail_user.php?id=<?= $row['ID'] ?>">
                            <i class="fa fa-eye"></i> Lihat
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    <?php endif; ?>

    <!-- SweetAlert bila user tak jumpa -->
    <?php if ($noUser): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Tidak Jumpa!',
            text: 'Pengguna tidak dijumpai dalam sistem.',
            confirmButtonColor: '#3498db'
        })
    </script>
    <?php endif; ?>

</body>
</html>
<?php $conn->close(); ?>
