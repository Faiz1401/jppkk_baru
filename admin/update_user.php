<?php
// Sambung DB
$conn = new mysqli("localhost", "root", "", "jppkk_test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil ID dari URL
$id = $_GET['id'] ?? 0;

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = $_POST['NAMA'] ?? '';
    $no_ic   = $_POST['NO_IC'] ?? '';
    $tarikh_lahir = $_POST['TARIKH_LAHIR'] ?? '';
    $jantina = $_POST['JANTINA'] ?? '';
    $agama   = $_POST['AGAMA'] ?? '';
    $email   = $_POST['EMAIL'] ?? '';
    $phone   = $_POST['PHONE'] ?? '';
    $alamat  = $_POST['ALAMAT'] ?? '';
    $gred_id = $_POST['GRED_ID'] ?? null;
    $bidang  = $_POST['BIDANG_PENGAJIAN'] ?? '';
    $sub_bidang = $_POST['SUB_BIDANG'] ?? '';
    $jabatan = $_POST['JABATAN_UNIT'] ?? '';
    $idjawatan = $_POST['idjawatan'] ?? null;
        $stmt = $conn->prepare("UPDATE tbluser SET STATUS = ?, IDJAWATAN = ? WHERE ID = ?");
        $stmt->bind_param("iii", $status, $idjawatan, $id);

    $program = $_POST['PROGRAM'] ?? '';
        $newProgramName = $_POST['newProgramName'] ?? '';

        if ($program === 'new' && !empty($newProgramName)) {
            // Insert ke tblprogram (KODPROGRAM auto increment atau generate manual)
            $stmtProg = $conn->prepare("INSERT INTO tblprogram (NAMAPROGRAM) VALUES (?)");
            $stmtProg->bind_param("s", $newProgramName);
            $stmtProg->execute();

            // Dapatkan KODPROGRAM baru
            $newProgramId = $stmtProg->insert_id;
            $stmtProg->close();

            // Simpan rekod user dengan program baru
            $program = $newProgramId;
        }

    $tarikh_pencen = $_POST['TARIKH_PENCEN'] ?? '';
    $institusi = $_POST['INSTITUSI'] ?? '';
    $alamat_institusi = $_POST['ALAMAT_INSTITUSI'] ?? '';
    $status  = $_POST['STATUS'] ?? 0;

    $stmt = $conn->prepare("
        UPDATE tbluser 
        SET NAMA=?, NO_IC=?, TARIKH_LAHIR=?, JANTINA=?, AGAMA=?, EMAIL=?, PHONE=?, ALAMAT=?, 
            GRED_ID=?, BIDANG_PENGAJIAN=?, SUB_BIDANG=?, JABATAN_UNIT=?, PROGRAM=?, TARIKH_PENCEN=?, 
            INSTITUSI=?, ALAMAT_INSTITUSI=?, STATUS=? 
        WHERE ID=?");
    $stmt->bind_param("ssssssssissssssiii", 
        $nama, $no_ic, $tarikh_lahir, $jantina, $agama, $email, $phone, $alamat,
        $gred_id, $bidang, $sub_bidang, $jabatan, $program, $tarikh_pencen,
        $institusi, $alamat_institusi, $status, $id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Data berjaya dikemaskini');window.location='user-maintenance.php';</script>";
    } else {
        echo "<script>alert('Ralat: ".$stmt->error."');</script>";
    }
}


// Dapatkan data user sedia ada
$stmt = $conn->prepare("SELECT * FROM tbluser WHERE ID=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Ambil senarai gred
$greds = $conn->query("SELECT IDGRED, GRED FROM tblgred ORDER BY GRED ASC");
// Ambil senarai bidang
$bidangs = $conn->query("SELECT IDBIDANGBK, NAMABIDANGBK FROM tblbidang ORDER BY NAMABIDANGBK ASC");
// Ambil senarai program
$programs = $conn->query("SELECT KODPROGRAM, NAMAPROGRAM FROM tblprogram ORDER BY KODPROGRAM ASC");
// Ambil senarai institusi
$institusi = $conn->query("SELECT NAMA_INSTITUSI, NEGERI FROM tblinstitusi ORDER BY NAMA_INSTITUSI ASC");

?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kemaskini Pengguna</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
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
            background: linear-gradient(90deg, #27ae60, #2ecc71);
            margin: 8px auto 0 auto;
            border-radius: 3px;
        }
        .section-title {
            margin: 25px 0 15px;
            font-size: 18px;
            font-weight: bold;
            color: #34495e;
            border-left: 5px solid #27ae60;
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
        .detail-item input, .detail-item textarea, .detail-item select {
            width: 95%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background: #fff;
            font-size: 14px;
            color: #333;
        }
        .detail-item textarea { resize: vertical; min-height: 60px; }
        .btn-save {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 22px;
            background: #27ae60;
            color: #fff;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: 0.3s;
        }
        .btn-save:hover { background: #219150; transform: translateY(-2px); }
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
<div class="detail-card">
    <h2><i class="fa fa-edit"></i> Kemaskini Pengguna</h2>
    <form method="post">
        <!-- Maklumat Peribadi -->
        <div class="section-title">Maklumat Peribadi</div>
        <div class="grid">
            <div class="detail-item"><label>No. IC</label>
                <input type="text" name="NO_IC" value="<?= htmlspecialchars($user['NO_IC'] ?? '') ?>">
            </div>
            <div class="detail-item"><label>Nama</label>
                <input type="text" name="NAMA" value="<?= htmlspecialchars($user['NAMA'] ?? '') ?>">
            </div>
            <div class="detail-item"><label>Tarikh Lahir</label>
                <input type="date" name="TARIKH_LAHIR" value="<?= htmlspecialchars($user['TARIKH_LAHIR'] ?? '') ?>">
            </div>
            <div class="detail-item"><label>Jantina</label>
                <input type="text" name="JANTINA" value="<?= htmlspecialchars($user['JANTINA'] ?? '') ?>">
            </div>
            <div class="detail-item"><label>Agama</label>
                <input type="text" name="AGAMA" value="<?= htmlspecialchars($user['AGAMA'] ?? '') ?>">
            </div>
            <div class="detail-item"><label>Email</label>
                <input type="email" name="EMAIL" value="<?= htmlspecialchars($user['EMAIL'] ?? '') ?>">
            </div>
            <div class="detail-item"><label>No. Telefon</label>
                <input type="text" name="PHONE" value="<?= htmlspecialchars($user['PHONE'] ?? '') ?>">
            </div>
            <div class="detail-item"><label>Alamat</label>
                <textarea name="ALAMAT"><?= htmlspecialchars($user['ALAMAT'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Maklumat Pekerjaan -->
        <div class="section-title">Maklumat Pekerjaan</div>
        <div class="grid">
            <div class="detail-item"><label>Gred</label>
                <select name="GRED_ID">
                    <option value="">--Pilih Gred--</option>
                    <?php while($g = $greds->fetch_assoc()): ?>
                        <option value="<?= $g['IDGRED'] ?>" <?= ($user['GRED_ID']==$g['IDGRED'])?'selected':'' ?>>
                            <?= $g['GRED'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="detail-item"><label>Bidang Pengajian</label>
                <select name="BIDANG_PENGAJIAN" required>
                    <option value="">-- Pilih Bidang --</option>
                    <?php while($b = $bidangs->fetch_assoc()): ?>
                        <option value="<?= $b['IDBIDANGBK'] ?>" 
                            <?= ($user['BIDANG_PENGAJIAN'] == $b['IDBIDANGBK']) ? 'selected' : '' ?>>
                            <?= $b['NAMABIDANGBK'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="detail-item"><label>Sub Bidang</label>
                <input type="text" name="SUB_BIDANG" value="<?= htmlspecialchars($user['SUB_BIDANG'] ?? '') ?>">
            </div>
            <div class="detail-item"><label>Jabatan / Unit</label>
                <input type="text" name="JABATAN_UNIT" value="<?= htmlspecialchars($user['JABATAN_UNIT'] ?? '') ?>">
            </div>
            <div class="detail-item">
                <label for="program">Program</label>
                <select id="program" name="PROGRAM" onchange="toggleProgramForm(this)" required>
                    <option value="">-- Pilih Program --</option>
                    <?php while($p = $programs->fetch_assoc()): ?>
                        <option value="<?= $p['KODPROGRAM'] ?>" 
                            <?= ($user['PROGRAM'] == $p['KODPROGRAM']) ? 'selected' : '' ?>>
                            <?= $p['KODPROGRAM'] ?> - <?= $p['NAMAPROGRAM'] ?>
                        </option>
                    <?php endwhile; ?>
                    <option value="new">+ Tambah Program Baru</option>
                </select>

                <!-- input nama program baru -->
                <input type="text" id="newProgramName" name="newProgramName" 
                    placeholder="Masukkan nama program baru" 
                    style="margin-top:10px; display:none; width:95%; padding:10px; border:1px solid #ddd; border-radius:8px;">
            </div>
            <div class="detail-item"><label>Tarikh Pencen</label>
                <input type="date" name="TARIKH_PENCEN" value="<?= htmlspecialchars($user['TARIKH_PENCEN'] ?? '') ?>">
            </div>
        </div>

        <!-- Maklumat Institusi -->
        <div class="section-title">Maklumat Institusi</div>
        <div class="grid">
            <div class="detail-item">
                <label for="institusi">Institusi</label>
                <select id="institusi" name="INSTITUSI" required>
                    <option value="">-- Pilih Institusi --</option>
                    <?php while($i = $institusi->fetch_assoc()): ?>
                        <option value="<?= $i['NAMA_INSTITUSI'] ?>" 
                            <?= ($user['INSTITUSI'] == $i['NAMA_INSTITUSI']) ? 'selected' : '' ?>>
                            <?= $i['NAMA_INSTITUSI'] ?> - <?= $i['NEGERI'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="detail-item"><label>Alamat Institusi</label>
                <textarea name="ALAMAT_INSTITUSI"><?= htmlspecialchars($user['ALAMAT_INSTITUSI'] ?? '') ?></textarea>
            </div>
            <div class="detail-item"><label>Status</label>
                <select name="STATUS">
                    <option value="1" <?= ($user['STATUS']==1)?'selected':'' ?>>Aktif</option>
                    <option value="0" <?= ($user['STATUS']==0)?'selected':'' ?>>Tidak Aktif</option>
                </select>
            </div>
        </div>

        <div style="text-align:center;">
            <button type="submit" class="btn-save">
                <i class="fa fa-save"></i> Simpan
            </button>
            <a href="detail_user.php?id=<?= $user['ID'] ?>" class="btn-back">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
    <script>
        function toggleProgramForm(select) {
            const newProgramInput = document.getElementById('newProgramName');
            if (select.value === 'new') {
                newProgramInput.style.display = 'block';
                newProgramInput.required = true;
            } else {
                newProgramInput.style.display = 'none';
                newProgramInput.required = false;
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>