<?php
// sambung ke database
$conn = new mysqli("localhost", "root", "", "jppkk_test");

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// jumlah semua user
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM tbluser")->fetch_assoc()['total'];

// user belum disahkan (STATUS = 0)
$pendingUsers = $conn->query("SELECT COUNT(*) AS total FROM tbluser WHERE STATUS = 0")->fetch_assoc()['total'];

// user telah disahkan (STATUS = 1)
$approvedUsers = $conn->query("SELECT COUNT(*) AS total FROM tbluser WHERE STATUS = 1")->fetch_assoc()['total'];

// user expired (STATUS = 2)
$expiredUsers = $conn->query("SELECT COUNT(*) AS total FROM tbluser WHERE STATUS = 2")->fetch_assoc()['total'];

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- CSS -->
<link rel="stylesheet" href="style.css">
<style>
/* Dashboard cards */
.dashboard {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  margin: 30px 20px;
}

.dashboard .card {
  background: #fff;
  padding: 20px;
  text-align: center;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.dashboard .card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

.dashboard .card i {
  font-size: 40px;
  color: #007BFF;
  margin-bottom: 10px;
}

.dashboard .card h3 {
  font-size: 28px;
  margin: 0;
  color: #333;
}

.dashboard .card p {
  color: #777;
  margin-top: 5px;
  font-size: 14px;
}
.dashboard a.card {
  background: #fff;
  padding: 20px;
  text-align: center;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  text-decoration: none; /* buang underline */
  color: inherit;        /* ikut warna asal */
  display: block;
}

.dashboard a.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.15);
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
        <a href="#logout"><i class="fa fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
  </div>
</div>

<div class="navbar">
    <a class="active" href="index.php"><i class="fa fa-fw fa-home"></i> Home</a> 
    <a href="#"><i class="fa fa-fw fa-search"></i> Search</a> 
    <a href="#"><i class="fa fa-fw fa-envelope"></i> Contact</a> 
    <a href="#"><i class="fa fa-fw fa-user"></i> Login</a>
    <a href="#"><i class="fa fa-fw fa-search"></i> Search</a> 
    <a href="#"><i class="fa fa-fw fa-envelope"></i> Contact</a> 
    <a href="#"><i class="fa fa-fw fa-envelope"></i> Contact</a> 
    <a href="#"><i class="fa fa-fw fa-user"></i> Login</a>
    <a href="#"><i class="fa fa-fw fa-search"></i> Search</a> 
    <a href="#"><i class="fa fa-fw fa-envelope"></i> Contact</a> 
    <a href="user-maintenance.php"><i class="fa fa-fw fa-user"></i> Akuan</a>
</div>

<!-- Dashboard Cards -->
<div class="dashboard">
  <a href="all_users.php" class="card">
    <i class="fa fa-users"></i>
    <h3><?php echo $totalUsers; ?></h3>
    <p>Total Users</p>
  </a>

  <a href="pending_users.php" class="card">
    <i class="fa fa-user-clock"></i>
    <h3><?php echo $pendingUsers; ?></h3>
    <p>Belum Disahkan</p>
  </a>

  <a href="approved_users.php" class="card">
    <i class="fa fa-user-check"></i>
    <h3><?php echo $approvedUsers; ?></h3>
    <p>Telah Disahkan</p>
  </a>

  <a href="expired_users.php" class="card">
    <i class="fa fa-user-times"></i>
    <h3><?php echo $expiredUsers; ?></h3>
    <p>Expired</p>
  </a>
</div>


</body>
</html>
