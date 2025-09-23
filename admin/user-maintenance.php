<?php
// Sambung ke DB
$conn = new mysqli("localhost", "root", "", "jppkk_test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil semua user
$result = $conn->query("SELECT ID, NAMA, EMAIL, NO_IC, PHONE, STATUS FROM tbluser ORDER BY ID DESC");
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>User Maintenance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 14px;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

th, td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #eee; /* garisan halus */
}

th {
    background: #3498db;
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

tr:last-child td { border-bottom: none; }
tr:nth-child(even) { background: #fafafa; }
tr:hover { background: #f1f7ff; }

/* Butang */
a.btn {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    margin: 2px;
    display: inline-block;
    font-size: 13px;
    transition: 0.2s;
}
a.btn:hover { opacity: 0.85; }
.detail { background: #8e44ad; color: #fff; }

.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
    color: #fff;
}

.status-badge.aktif {
    background: #2ecc71; /* Hijau */
}

.status-badge.tidak {
    background: #e74c3c; /* Merah */
}

.aktif { background: #2ecc71; }
.tidak { background: #e74c3c; }
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
            <a href="#logout"><i class="fa fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>
    </div>

<div class="navbar">
    <a  href="index.php"><i class="fa fa-fw fa-home"></i> Home</a> 
    <a href="#"><i class="fa fa-fw fa-search"></i> Search</a> 
    <a href="#"><i class="fa fa-fw fa-envelope"></i> Contact</a> 
    <a href="#"><i class="fa fa-fw fa-user"></i> Login</a>
    <a href="#"><i class="fa fa-fw fa-search"></i> Search</a> 
    <a href="#"><i class="fa fa-fw fa-envelope"></i> Contact</a> 
    <a href="#"><i class="fa fa-fw fa-envelope"></i> Contact</a> 
    <a href="#"><i class="fa fa-fw fa-user"></i> Login</a>
    <a href="#"><i class="fa fa-fw fa-search"></i> Search</a> 
    <a href="#"><i class="fa fa-fw fa-envelope"></i> Contact</a> 
    <a class="active"href="user-maintenance.php"><i class="fa fa-fw fa-user"></i> Akuan</a>
</div>

    <h1>Senarai User (User Maintenance)</h1>

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
</body>
</html>
<?php $conn->close(); ?>
