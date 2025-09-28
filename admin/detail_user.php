<?php
// Sambung ke DB
$conn = new mysqli("localhost", "root", "", "jppkk_test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Dapatkan ID user dari URL
$id = $_GET['id'] ?? 0;

// Ambil detail user + gred sekali
$stmt = $conn->prepare("
    SELECT u.*, g.GRED AS NAMA_GRED, 
        p.KODPROGRAM, p.NAMAPROGRAM
    FROM tbluser u
    LEFT JOIN tblgred g ON u.GRED_ID = g.IDGRED
    LEFT JOIN tblprogram p ON u.PROGRAM = p.KODPROGRAM
    WHERE u.ID = ?

");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Jangan tutup connection lagi kalau nak guna $user banyak kali
if (!$user) {
    die("User tidak dijumpai!");
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Butiran Pengguna</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .detail-card {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
            position: relative;
        }
        h2::after {
            content: "";
            display: block;
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #3498db, #9b59b6);
            margin: 8px auto 0 auto;
            border-radius: 3px;
        }
        .section-title {
            margin: 25px 0 15px;
            font-size: 18px;
            font-weight: bold;
            color: #34495e;
            border-left: 5px solid #3498db;
            padding-left: 10px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 25px;
        }
        .detail-item label {
            font-weight: bold;
            font-size: 14px;
            color: #555;
            display: block;
            margin-bottom: 6px;
        }
        .detail-item input, .detail-item textarea {
            width: 95%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            font-size: 14px;
            color: #333;
        }
        .detail-item textarea {
            resize: vertical;
            min-height: 60px;
        }
        .btn-back {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 22px;
            background: #3498db;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 500;
        }
        .btn-back:hover {
            background: #2980b9;
            transform: translateY(-2px);
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


<div class="detail-card">
    <h2><i class="fa fa-id-card"></i> Butiran Pengguna</h2>

    <!-- Maklumat Peribadi -->
    <div class="section-title">Maklumat Peribadi</div>
    <div class="grid">
        <div class="detail-item"><label>No. IC</label>
            <input type="text" value="<?= htmlspecialchars($user['NO_IC'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>Nama</label>
            <input type="text" value="<?= htmlspecialchars($user['NAMA'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>Tarikh Lahir</label>
            <input type="text" value="<?= htmlspecialchars($user['TARIKH_LAHIR'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>Jantina</label>
            <input type="text" value="<?= htmlspecialchars($user['JANTINA'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>Agama</label>
            <input type="text" value="<?= htmlspecialchars($user['AGAMA'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>Email</label>
            <input type="text" value="<?= htmlspecialchars($user['EMAIL'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>No. Telefon</label>
            <input type="text" value="<?= htmlspecialchars($user['PHONE'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>Alamat</label>
            <textarea readonly><?= htmlspecialchars($user['ALAMAT'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Maklumat Pekerjaan -->
    <div class="section-title">Maklumat Pekerjaan</div>
    <div class="grid">
        <div class="detail-item"><label>Gred</label>
            <input type="text" value="<?= htmlspecialchars($user['NAMA_GRED'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>Bidang Pengajian</label>
            <input type="text" value="<?= htmlspecialchars($user['BIDANG_PENGAJIAN'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>Sub Bidang</label>
            <input type="text" value="<?= htmlspecialchars($user['SUB_BIDANG'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>Jabatan / Unit</label>
            <input type="text" value="<?= htmlspecialchars($user['JABATAN_UNIT'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>Program</label>
            <textarea readonly><?= htmlspecialchars(($user['KODPROGRAM'] ?? '') . ' - ' . ($user['NAMAPROGRAM'] ?? '')) ?></textarea>
        </div>
        <div class="detail-item"><label>Tarikh Pencen</label>
            <input type="text" value="<?= htmlspecialchars($user['TARIKH_PENCEN'] ?? '') ?>" readonly>
        </div>
    </div>

    <!-- Maklumat Hubungan -->
    <div class="section-title">Maklumat Institusi</div>
    <div class="grid">
        <div class="detail-item"><label>Institusi</label>
            <input type="text" value="<?= htmlspecialchars($user['INSTITUSI'] ?? '') ?>" readonly>
        </div>
        <div class="detail-item"><label>Alamat Institusi</label>
            <textarea readonly><?= htmlspecialchars($user['ALAMAT_INSTITUSI'] ?? '') ?></textarea>
        </div>
        <div class="detail-item"><label>Bukti Pengesahan</label>
            <?php if (!empty($user['BUKTI_PENGESAHAN'])): ?>
                <?php $base64 = base64_encode($user['BUKTI_PENGESAHAN']); ?>
                <iframe src="data:application/pdf;base64,<?= $base64 ?>"
                        width="100%" height="400px"
                        style="border:1px solid #ccc; border-radius:8px;">
                </iframe>
                <br><br>
                    <a href="download.php" class="btn btn-primary">⬇️ Muat Turun Bukti Saya</a>
            <?php else: ?>
                <input type="text" value="Tiada dokumen" readonly>
            <?php endif; ?>
        </div>
        <div class="detail-item"><label>Status</label>
            <input type="text" value="<?= ($user['STATUS'] ?? 0) == 1 ? 'Aktif' : 'Tidak Aktif' ?>" readonly>
        </div>
    </div>

    <!-- Butang Action -->
    <div style="margin-top: 30px; display:flex; justify-content:space-between; align-items:center;">
        
        <!-- Kiri: Update & Delete -->
        <div>
            <a href="update_user.php?id=<?= $user['ID'] ?>" 
               class="btn-back" 
               style="background:#27ae60; margin-right:10px;">
               <i class="fa fa-edit"></i> Kemaskini
            </a>
            
            <a href="delete_user.php?id=<?= $user['ID'] ?>" 
               class="btn-back" 
               style="background:#e74c3c;"
               onclick="return confirm('Anda pasti mahu padam user ini?');">
               <i class="fa fa-trash"></i> Padam
            </a>
        </div>
        
        <!-- Kanan: Kembali -->
        <div>
            <a href="user-maintenance.php" class="btn-back">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>

