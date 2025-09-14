<?php
session_start();
require '../db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';
$user_role = $_SESSION['role'];

if ($user_role === 'admin') {
    header("Location: admin.php");
    exit;
}

// Fetch all events for selection
$events_result = $conn->query("SELECT id, event_category, bil, year FROM event ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Event Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="mb-4">
            <a href="../admin.php" class="btn btn-secondary">Home</a>
        </div>

        <h3 class="mb-4" id="formTitle">Register for an Event</h3>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"> <?= $_SESSION['success'];
                                                unset($_SESSION['success']); ?> </div>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"> <?= $_SESSION['error'];
                                                unset($_SESSION['error']); ?> </div>
        <?php endif; ?>

        <form action="do_regis_event.php" method="POST" class="row g-4">

            <div class="col-md-6">
                <label for="event_id" class="form-label">Select Event</label>
                <select name="event_id" id="event_id" class="form-select" required>
                    <option value="" disabled selected>-- Select Event --</option>
                    <?php while ($event = $events_result->fetch_assoc()): ?>
                        <option value="<?= $event['id'] ?>">
                            <?= htmlspecialchars($event['event_category'] . " Bil." . $event['bil'] . "/" . $event['year']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="w-100"></div>
            <div class="col-md-3">
                <label class="form-label">PROGRAM</label>
                <input type="text" name="program" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">KOD PROGRAM</label>
                <input type="text" name="kod_program" class="form-control" required>
            </div>
            <div class="w-100"></div>
            <div class="col-md-3">
                <label class="form-label">KOD KURSUS</label>
                <input type="text" name="kod_kursus" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">BIL KURSUS</label>
                <input type="text" name="bil_kursus" class="form-control" required>
            </div>
            <div class="w-100"></div>
            <div class="col-md-4">
                <label class="form-label">PERINGKAT KELULUSAN(MLK/JKK)</label>
                <select name="peringkat_kelulusan" class="form-select" required>
                    <option value="" disabled selected>-- Select --</option>
                    <option value="MLK">MLK</option>
                    <option value="JKIK">JKIK</option>
                </select>
            </div>

            <div class="col-md-5">
                <label class="form-label">JUSTIFIKASI</label>
                <select name="justifikasi" class="form-select" required>
                    <option value="" disabled selected>-- Select --</option>
                    <option value="FTR">FULL TERM REVIEW</option>
                    <option value="NP">NEW PROGRAM</option>
                    <option value="NC">NEW COURSE</option>
                    <option value="TR">THEMATIC REVIEW</option>
                </select>
            </div>
            <div class="w-100"></div>
            <div class="col-md-4">
                <label class="form-label">BIDANG</label>
                <select name="bidang" class="form-select" required>
                    <option value="" disabled selected>-- Select --</option>
                    <option value="HOTEL">HOTEL</option>
                    <option value="PERKHIDMATAN">PERKHIDMATAN</option>
                    <option value="KULINARI">KULINARI</option>
                    <option value="PELANCONGAN">PELANCONGAN</option>
                    <option value="HOSPITALITI">HOSPITALITI</option>
                    <option value="KEJURUTERAAN AWAM DAN ALAM BINA">KEJURUTERAAN AWAM DAN ALAM BINA</option>
                    <option value="AGRO & BIOINDUSTRI">AGRO & BIOINDUSTRI</option>
                    <option value="PERDAGANGAN">PERDAGANGAN</option>
                    <option value="KEJURUTERAAN ELEKTRIK">KEJURUTERAAN ELEKTRIK</option>
                    <option value="REKA BENTUK & KREATIF">REKA BENTUK & KREATIF</option>
                    <option value="TEKNOLOGI KEJURUTERAAN AWAM">TEKNOLOGI KEJURUTERAAN AWAM</option>
                    <option value="KOMPUTERAN">KOMPUTERAN</option>
                    <option value="ELEKTRIK">ELEKTRIK</option>
                    <option value="PENGKOMPUTERAN">PENGKOMPUTERAN</option>
                    <option value="KEJURUTERAAN AWAM">KEJURUTERAAN AWAM</option>
                    <option value="MEKANIKAL">MEKANIKAL</option>
                    <option value="KEJURUTERAAN MEKANIKAL">KEJURUTERAAN MEKANIKAL</option>
                    <option value="KEWANGAN ISLAM DAN MUAMALAT">KEWANGAN ISLAM DAN MUAMALAT</option>
                    <option value="PENYENGGARAAN PESAWAT">PENYENGGARAAN PESAWAT</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Kategori Permohonan</label>
                <select name="kategori_permohonan" class="form-select" required>
                    <option value="" disabled selected>-- Select --</option>
                    <option value="CQI">CQI</option>
                    <option value="BAHARU">BAHARU</option>
                    <option value="SPT">STRUKTUR PROGRAM TVET (ETAC)</option>

                </select>
            </div>
            <div class="w-100"></div>
            <div class="col-md-4">
                <label class="form-label">Kelulusan JK3P2K</label>
                <input type="text" name="kelulusan_jk3p2k" class="form-control" placeholder="e.g. BIL.1/2025">
            </div>
            <div class="col-md-4">
                <label class="form-label">Kelulusan MLK</label>
                <input type="text" name="kelulusan_mlk" class="form-control" placeholder="e.g. BIL.1/2025">
            </div>
            <div class="w-100"></div>
            <div class="col-md-4">
                <label class="form-label">Pegawai Unit Bidang</label>
                <input type="text" name="pegawai_bidang" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">SEMESTER PELAKSANAAN</label>
                <input type="text" name="semester" class="form-control" required placeholder="e.g. SESI 1 2025/2026">
            </div>

            <div class="col-12 mb-5">
                <button type="submit" class="btn btn-primary">Submit Registration</button>
            </div>

        </form>
    </div>
    <script>
        const eventDropdown = document.getElementById('event_id');
        const formTitle = document.getElementById('formTitle');

        eventDropdown.addEventListener('change', function() {
            const selectedOption = eventDropdown.options[eventDropdown.selectedIndex];
            if (selectedOption.value) {
                formTitle.textContent = `Register for ${selectedOption.text}`;
            } else {
                formTitle.textContent = 'Register for an Event';
            }
        });
    </script>

</body>

</html>