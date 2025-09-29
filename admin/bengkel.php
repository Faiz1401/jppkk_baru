<?php
include '../db_connection.php'; 

// Simpan bengkel baru
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['SIRI_KATEGORI'])) {
    $siriKategori = $_POST['SIRI_KATEGORI'] ?? '';
    $siriNo       = $_POST['SIRI_NO'] ?? '';
    $siribengkel  = (!empty($siriKategori) && !empty($siriNo)) 
                    ? $siriKategori . "-" . $siriNo 
                    : $siriNo;

    $tahun        = $_POST['TAHUN'] ?? '';
    $tarikhmula   = $_POST['TARIKHMULA'] ?? '';
    $tarikhtamat  = $_POST['TARIKHTAMAT'] ?? '';
    $lokasi       = $_POST['LOKASI'] ?? '';
    $status       = $_POST['STATUS'] ?? '';
    $justifikasi  = $_POST['JUSTIFIKASI'] ?? '';

    // simpan tahun dalam format YYYY-01-01
    $tahun = $tahun . "-01-01";

    $stmt = $conn->prepare("INSERT INTO tblbengkel 
        (SIRIBENGKEL, TAHUN, TARIKHMULA, TARIKHTAMAT, LOKASI, STATUS, JUSTIFIKASI)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $siribengkel, $tahun, $tarikhmula, $tarikhtamat, $lokasi, $status, $justifikasi);

    if ($stmt->execute()) {
        echo "<script>alert('Bengkel berjaya didaftarkan!'); window.location='create_bengkel.php';</script>";
        exit;
    } else {
        echo "Ralat: " . $stmt->error;
    }
    $stmt->close();
}

// Ambil semua bengkel
$events = [];
$res = $conn->query("SELECT SIRIBENGKEL, TAHUN, TARIKHMULA, TARIKHTAMAT, LOKASI, STATUS FROM tblbengkel");
while($row = $res->fetch_assoc()) {
    $events[] = $row;
}

// Ambil bengkel terakhir untuk highlight default
$last = $conn->query("SELECT TARIKHMULA, TARIKHTAMAT 
                      FROM tblbengkel 
                      ORDER BY IDBENGKEL DESC 
                      LIMIT 1");
$lastEvent = $last->fetch_assoc();
$lastMula = $lastEvent['TARIKHMULA'] ?? '';
$lastTamat = $lastEvent['TARIKHTAMAT'] ?? '';

$conn->close();
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Daftar Bengkel Baru</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <style>
    h1, h2 {
      text-align: center;
      font-size: 28px;
      font-weight: 700;
      color: #2c3e50;
      margin: 30px 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      letter-spacing: 1px;
    }
    h1::after, h2::after {
      content: '';
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, #3498db, #9b59b6);
      display: block;
      margin: 8px auto 0 auto;
      border-radius: 3px;
    }
    .form-container {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    }
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px 25px;
    }
    .form-item { display: flex; flex-direction: column; }
    .form-item label { margin-bottom: 6px; font-weight: bold; }
    .form-item input, .form-item select, .form-item textarea {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      width: 100%;
      box-sizing: border-box;
    }
    button {
      margin-top: 15px;
      background: #007BFF;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover { background: #0056b3; }
    .layout {
      display:flex;
      gap:30px;
      align-items:flex-start;
      max-width:1200px;
      margin:auto;
    }
    .form-container { flex:2; }
    .calendar-box {
      flex:1;
      max-width:350px;
      background:#fff;
      padding:20px;
      border-radius:10px;
      box-shadow:0 3px 6px rgba(0,0,0,0.2);
      position: sticky;
      top: 80px;
      height: fit-content;
    }

    /* Card styling */
    .event-card {
      border: 1px solid #ddd;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 8px;
      background: #ffffff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .event-header {
      font-size: 16px;
      font-weight: bold;
      margin-bottom: 10px;
      color: #2c3e50;
      border-bottom: 1px solid #eee;
      padding-bottom: 5px;
    }
    .event-body p {
      margin: 6px 0;
      font-size: 14px;
      color: #444;
    }
    .status-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 5px;
      font-size: 12px;
      font-weight: bold;
      color: white;
    }
    .status-planning { background: #3498db; }
    .status-progress { background: #f39c12; }
    .status-done { background: #27ae60; }

    .btn-delete {
      background: #dc3545;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 13px;
    }
    .btn-delete:hover { background: #b02a37; }

    .btn-update {
      background: #007BFF;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 13px;
      margin-top: 5px;
    }
    .btn-update:hover { background: #0056b3; }

    .highlight-day {
      background: #007BFF !important;
      color: white !important;
      border-radius: 50%;
    }

    @media screen and (max-width: 900px) {
      .layout { flex-direction: column; }
      .calendar-box { max-width:100%; position: static; }
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
    <a href="#"><i class="fa fa-fw fa-envelope"></i> Contact</a> 
    <a href="#"><i class="fa fa-fw fa-user"></i> Login</a>
    <a href="#"><i class="fa fa-fw fa-search"></i> Search</a> 
    <a href="#"><i class="fa fa-fw fa-search"></i> Search</a> 
    <a href="#"><i class="fa fa-fw fa-envelope"></i> Contact</a> 
    <a class="active" href="user-maintenance.php"><i class="fa fa-fw fa-user"></i> Akuan</a>
</div>

<div class="layout">

  <!-- FORM -->
  <div class="form-container">
    <h2>Daftar Bengkel Baru</h2>
    <form method="POST" action="">
      <div class="form-grid">
        <div class="form-item">
          <label>Siri Kategori</label>
          <select name="SIRI_KATEGORI" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="MLK">MLK</option>
            <option value="JKIK">JKIK</option>
          </select>
        </div>
        <div class="form-item">
          <label>Siri Bengkel</label>
          <select name="SIRI_NO" required>
            <option value="">-- Pilih Siri --</option>
            <?php for ($i=1; $i<=10; $i++): 
              $num = str_pad($i, 2, "0", STR_PAD_LEFT); ?>
              <option value="<?= $num ?>"><?= $num ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="form-item">
          <label>Tahun</label>
          <select name="TAHUN" required>
            <option value="">-- Pilih Tahun --</option>
            <?php
              $tahunSemasa = date("Y");
              for ($i = 0; $i <= 5; $i++) {
                  $tahun = $tahunSemasa + $i;
                  echo "<option value='$tahun'>$tahun</option>";
              }
            ?>
          </select>
        </div>
        <div class="form-item">
          <label>Tarikh Mula</label>
          <input type="date" name="TARIKHMULA" required>
        </div>
        <div class="form-item">
          <label>Tarikh Tamat</label>
          <input type="date" name="TARIKHTAMAT">
        </div>
        <div class="form-item">
          <label>Lokasi</label>
          <input type="text" name="LOKASI">
        </div>
        <div class="form-item">
          <label>Status</label>
          <select name="STATUS" required>
            <option value="">-- Pilih Status --</option>
            <option value="Dalam Perancangan">Dalam Perancangan</option>
            <option value="Sedang Berjalan">Sedang Berjalan</option>
            <option value="Selesai">Selesai</option>
          </select>
        </div>
        <div class="form-item" style="grid-column: span 2;">
          <label>Justifikasi</label>
          <textarea name="JUSTIFIKASI" rows="4" required></textarea>
        </div>
      </div>
      <button type="submit">Simpan Bengkel</button>
    </form>
  </div>

  <!-- SIDEBAR CALENDAR -->
  <div class="calendar-box">
    <h2>Kalendar Bengkel</h2>
    <div id="calendar"></div>
    <div id="eventInfo" style="margin-top:15px;">
      <label><b>Maklumat Bengkel:</b></label>
      <div id="eventList"></div>
    </div>
  </div>

</div>

<script>
  const events = <?= json_encode($events) ?>;

  // senarai tarikh highlight
  let highlightedDates = [];
  events.forEach(ev => {
    let start = new Date(ev.TARIKHMULA);
    let end = new Date(ev.TARIKHTAMAT);
    for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
      highlightedDates.push(d.toISOString().split("T")[0]);
    }
  });

  // default highlight last event
  let defaultDates = [];
  <?php if (!empty($lastMula) && !empty($lastTamat)): ?>
    defaultDates = ["<?= $lastMula ?>", "<?= $lastTamat ?>"];
  <?php endif; ?>

  const calendar = flatpickr("#calendar", {
    inline: true,
    mode: "single",
    dateFormat: "Y-m-d",
    clickOpens: false,
    defaultDate: defaultDates,
    onDayCreate: function(dObj, dStr, fp, dayElem) {
      let dateStr = dayElem.dateObj.toISOString().split("T")[0];
      if (highlightedDates.includes(dateStr)) {
        dayElem.classList.add("highlight-day");
      }
    },
    onChange: function(selectedDates, dateStr) {
      const eventList = document.getElementById("eventList");
      eventList.innerHTML = "";

      let found = events.filter(ev => dateStr >= ev.TARIKHMULA && dateStr <= ev.TARIKHTAMAT);

      if (found.length > 0) {
        found.forEach(ev => {
          let year = ev.TAHUN ? ev.TAHUN.substring(0,4) : "";
          let siriKategori = ev.SIRIBENGKEL.split("-")[0] || "";
          let siriNo = ev.SIRIBENGKEL.split("-")[1] || "";

          let statusClass = "";
          if (ev.STATUS === "Dalam Perancangan") statusClass = "status-planning";
          else if (ev.STATUS === "Sedang Berjalan") statusClass = "status-progress";
          else if (ev.STATUS === "Selesai") statusClass = "status-done";

          let div = document.createElement("div");
          div.className = "event-card";
          div.innerHTML = `
            <div class="event-header">${siriKategori} - ${siriNo} (${year})</div>
            <div class="event-body">
              <p><b>Lokasi:</b> ${ev.LOKASI}</p>
              <p>
                <b>Status:</b> 
                <span class="status-badge ${statusClass}" id="badge-${ev.SIRIBENGKEL}">${ev.STATUS}</span>
                <br>
                <select id="status-${ev.SIRIBENGKEL}">
                  <option ${ev.STATUS==="Dalam Perancangan"?"selected":""}>Dalam Perancangan</option>
                  <option ${ev.STATUS==="Sedang Berjalan"?"selected":""}>Sedang Berjalan</option>
                  <option ${ev.STATUS==="Selesai"?"selected":""}>Selesai</option>
                </select>
                <button class="btn-update" onclick="kemaskiniStatus('${ev.SIRIBENGKEL}')">Kemaskini</button>
              </p>
              <p><b>Tempoh:</b> ${ev.TARIKHMULA} → ${ev.TARIKHTAMAT}</p>
            </div>
            <div class="event-actions">
              <button class="btn-delete" onclick="deleteEvent('${ev.SIRIBENGKEL}')">Padam</button>
            </div>
          `;
          eventList.appendChild(div);
        });
      } else {
        eventList.innerHTML = "<p>Tiada bengkel pada tarikh ini</p>";
      }
    }
  });

  function kemaskiniStatus(siri) {
    let newStatus = document.getElementById("status-" + siri).value;

    fetch("update_bengkel_status.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "siri=" + encodeURIComponent(siri) + "&status=" + encodeURIComponent(newStatus)
    })
    .then(res => res.text())
    .then(data => {
      alert(data);
      let badge = document.getElementById("badge-" + siri);
      badge.textContent = newStatus;
      badge.className = "status-badge";
      if (newStatus === "Dalam Perancangan") badge.classList.add("status-planning");
      else if (newStatus === "Sedang Berjalan") badge.classList.add("status-progress");
      else if (newStatus === "Selesai") badge.classList.add("status-done");
    })
    .catch(err => alert("Ralat: " + err));
  }

  function deleteEvent(siri) {
    if (confirm("Padam bengkel " + siri + "?")) {
      window.location.href = "delete_bengkel.php?siri=" + encodeURIComponent(siri);
    }
  }
</script>

</body>
</html>
