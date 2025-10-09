<?php
session_start();
include '../db_connection.php';

// 👉 Ambil semua bengkel
$events = [];
$res = $conn->query("SELECT IDBENGKEL, KATEGORI, SIRIBENGKEL, TAHUN, TARIKHMULA, TARIKHTAMAT, LOKASI, STATUS, JUSTIFIKASI 
                     FROM tblbengkel
                     ORDER BY TARIKHMULA ASC");
while ($row = $res->fetch_assoc()) {
    $events[] = $row;
}

// 👉 Ambil bengkel terakhir untuk fokus calendar
$last = $conn->query("SELECT TARIKHMULA FROM tblbengkel ORDER BY IDBENGKEL DESC LIMIT 1");
$lastEvent = $last->fetch_assoc();
$lastMula = $lastEvent['TARIKHMULA'] ?? '';

$conn->close();
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Bengkel</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: Arial, sans-serif; background:#f4f6f9; margin:0; }
    h1, h2 {            
      text-align: center;
      font-size: 24px;
      font-weight: 700;
      color: #2c3e50;
      margin: 20px 0 15px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      letter-spacing: 1px;
      position: relative;
    }
    h1::after, h2::after {
      content: '';
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, #3498db, #9b59b6);
      display: block;
      margin: 8px auto 0;
      border-radius: 3px;
    }

    .layout {
      max-width: 1200px;
      margin: 20px auto;
      display: flex;
      gap: 30px;
      align-items: flex-start;
    }

    .left-col { flex: 2; }
    .calendar-box {
      flex: 1;
      background:#fff;
      padding:20px;
      border-radius:10px;
      box-shadow:0 3px 6px rgba(0,0,0,.2);

      display: flex;
      flex-direction: column;
      align-items: center;   /* center horizontal */
    }

    #calendar {
      display: inline-block; /* ikut size sebenar */
      margin: 0 auto;
    }

    .form-container, .bengkel-list {
      background:#fff;
      padding:20px;
      border-radius:10px;
      box-shadow:0 3px 6px rgba(0,0,0,.2);
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px 20px;
    }
    .form-item { display:flex; flex-direction:column; }
    .form-item label { font-weight:600; margin-bottom:5px; }
    .form-item input, .form-item select, .form-item textarea {
      padding:10px; border:1px solid #ccc; border-radius:8px; width:100%; box-sizing:border-box;
    }
    button { background:#007BFF; color:#fff; padding:10px 16px; border:none; border-radius:8px; cursor:pointer; font-weight:600; }
    button:hover { background:#0056b3; }

.bengkel-list {
  max-width: 780px;
  margin: 20px auto 40px;
  border-radius: 10px;
  padding: 20px;
  background: #fff;
  border: 1px solid #ddd;
  box-shadow: 0 3px 6px rgba(0,0,0,.08);
}

.bengkel-list h2 {
  font-size: 22px;
  font-weight: 700;
  margin-bottom: 20px;
  text-align: center;
  color: #2c3e50;
}

.bengkel-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  border-radius: 8px;
  overflow: hidden;
}

.bengkel-table th, 
.bengkel-table td {
  padding: 12px 14px;
  text-align: left;
}

.bengkel-table th {
  background: #2c3e50;   /* warna flat dark */
  color: white;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 13px;
  letter-spacing: .5px;
}

.bengkel-table tr {
  border-bottom: 1px solid #eee;
  transition: background 0.2s ease;
}

.bengkel-table tr:nth-child(even) {
  background: #fafafa;
}

.bengkel-table tr:hover {
  background: #f1f1f1;
}

.bengkel-table td {
  color: #333;
  font-size: 14px;
}

/* Badge status */
.status-badge {
  display: inline-block;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  color: #fff;
}
.status-planning { background: #f39c12; }   /* kuning oren */
.status-progress { background: #007bff; }   /* biru */
.status-done { background: #28a745; }      /* hijau */


    /* Event List (calendar klik) */
    .event-card { border:1px solid #ddd; padding:12px; margin-bottom:12px; border-radius:8px; background:#fff; text-align:left; }
    .event-status { display:inline-block; padding:4px 10px; border-radius:20px; font-size:13px; font-weight:600; color:#fff; }
    .status-planning { background:#f39c12; }
    .status-progress { background:#007bff; }
    .status-done { background:#28a745; }
    .event-actions { margin-top:10px; display:flex; gap:10px; }

    .btn-update, .btn-delete { padding:8px 12px; border:none; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; }
    .btn-update { background:#007BFF; color:#fff; }
    .btn-update:hover { background:#0056b3; }
    .btn-delete { background:#dc3545; color:#fff; }
    .btn-delete:hover { background:#b02a37; }

    /* Highlight tarikh */
    .flatpickr-day.has-event { background-color:rgba(0,123,255,0.15); border-color:#007BFF; color:#007BFF; border-radius:50%; }
    .flatpickr-day.has-event:hover { background-color:rgba(0,123,255,0.25); }
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
  <a class="active" href="bengkel.php"><i class="fa-solid fa-calendar"></i> Bengkel</a> 

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

<div class="layout">
  <!-- Column kiri -->
  <div class="left-col">
    <div class="form-container">
      <h1>Daftar Bengkel Baru</h1>
      <form method="POST" action="create_bengkel.php">
        <div class="form-grid">
          <div class="form-item">
            <label>Kategori</label>
            <select name="KATEGORI" required>
              <option value="">-- Pilih Kategori --</option>
              <option value="MLK">MLK</option>
              <option value="JKIK">JKIK</option>
            </select>
          </div>

          <div class="form-item">
            <label>Siri</label>
            <select name="SIRIBENGKEL" required>
              <option value="">-- Pilih Siri --</option>
              <?php for($i=1;$i<=10;$i++): $num=str_pad($i,2,"0",STR_PAD_LEFT); ?>
                <option value="<?= $num ?>"><?= $num ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="form-item">
            <label>Tahun</label>
            <select name="TAHUN" required>
              <option value="">-- Pilih Tahun --</option>
              <?php $tahunSemasa=date("Y"); for($i=0;$i<=5;$i++): $tahun=$tahunSemasa+$i; ?>
                <option value="<?= $tahun ?>"><?= $tahun ?></option>
              <?php endfor; ?>
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
            <textarea name="JUSTIFIKASI" rows="3" required></textarea>
          </div>
        </div>
        <button type="submit" style="margin-top:15px;">Simpan Bengkel</button>
      </form>
    </div>

    <!-- Senarai Bengkel -->
<div class="bengkel-list">
  <h2>Senarai Bengkel</h2>
  <?php if (count($events) > 0): ?>
    <table class="bengkel-table">
<thead>
  <tr>
    <th>Siri / Tahun</th>
    <th>Lokasi</th>
    <th>Status</th>
    <th>Tempoh</th>
  </tr>
</thead>

<tbody>
  <?php foreach ($events as $ev): 
    $year = $ev['TAHUN'] ? substr($ev['TAHUN'],0,4) : "";
    $siri = $ev['SIRIBENGKEL'] ?? "";
    $status = $ev['STATUS'] ?? "-";

    // Tentukan warna badge
    $statusClass = "status-planning";
    if ($status === "Sedang Berjalan") $statusClass = "status-progress";
    else if ($status === "Selesai") $statusClass = "status-done";

    // Pecahkan siri -> MLK-01 jadi MLK Bil.01
    $siriDisplay = $ev['KATEGORI'] . " Bil." . str_pad($ev['SIRIBENGKEL'], 2, "0", STR_PAD_LEFT);

  ?>
    <tr>
      <td><?= htmlspecialchars($siriDisplay . " / " . $year) ?></td>
      <td><?= htmlspecialchars($ev['LOKASI'] ?? '-') ?></td>
      <td><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($status) ?></span></td>
      <td><?= $ev['TARIKHMULA'] ?> → <?= $ev['TARIKHTAMAT'] ?: $ev['TARIKHMULA'] ?></td>
    </tr>
  <?php endforeach; ?>
</tbody>

    </table>
  <?php else: ?>
    <p style="text-align:center; color:#888;">Tiada bengkel direkodkan.</p>
  <?php endif; ?>
</div>


  </div>

  <!-- Column kanan -->
  <div class="calendar-box" style="padding-left:10px; padding-right:10px;">
    <h2>Kalendar Bengkel</h2>
    <div id="calendar"></div>
    <div id="eventList" style="margin-top:15px;"></div>
  </div>
</div>

<script>
const events = <?= json_encode($events) ?>;
function fmt(d){ return d.toISOString().split("T")[0]; }
function parseYMD(str){ if(!str) return null; const [y,m,d]=str.split("-").map(Number); return new Date(y,(m||1)-1,d||1); }

const eventDateSet=new Set();
events.forEach(ev=>{ if(!ev.TARIKHMULA) return;
  const start=parseYMD(ev.TARIKHMULA), end=ev.TARIKHTAMAT?parseYMD(ev.TARIKHTAMAT):start;
  for(let d=new Date(start); d<=end; d.setDate(d.getDate()+1)){ eventDateSet.add(fmt(new Date(d))); }
});

function renderEventList(dateStr){
  const wrap=document.getElementById("eventList"); wrap.innerHTML="";
  const list=events.filter(ev=> dateStr>=ev.TARIKHMULA && dateStr<=(ev.TARIKHTAMAT||ev.TARIKHMULA));
  if(list.length===0){ wrap.innerHTML="<p>Tiada bengkel pada tarikh ini</p>"; return; }
  list.forEach(ev => {
    const year = ev.TAHUN ? ev.TAHUN.substring(0,4) : "";
    const [siriKategori="", siriNo=""] = (ev.SIRIBENGKEL || "").split("-");
    let statusClass = "status-planning";
    if (ev.STATUS === "Sedang Berjalan") statusClass = "status-progress";
    else if (ev.STATUS === "Selesai") statusClass = "status-done";

    wrap.innerHTML += `
      <div class="event-card">
        <div class="event-header">
          <span class="event-status ${statusClass}" id="badge-${ev.IDBENGKEL}">${ev.STATUS}</span>
        </div>
        <div class="event-body">
          <p><b>Siri:</b> ${ev.KATEGORI} Bil.${ev.SIRIBENGKEL} / ${year}</p>
          <p><b>Lokasi:</b> ${ev.LOKASI || "-"}</p>
          <p><b>Justifikasi:</b> ${ev.JUSTIFIKASI || "-"}</p>
          <p><b>Tempoh:</b> ${ev.TARIKHMULA} → ${ev.TARIKHTAMAT || ev.TARIKHMULA}</p>
          <p><b>Kemaskini Status:</b>
            <select id="status-${ev.IDBENGKEL}">
              <option ${ev.STATUS==="Dalam Perancangan"?"selected":""}>Dalam Perancangan</option>
              <option ${ev.STATUS==="Sedang Berjalan"?"selected":""}>Sedang Berjalan</option>
              <option ${ev.STATUS==="Selesai"?"selected":""}>Selesai</option>
            </select>
          </p>
        </div>
        <div class="event-actions">
          <button class="btn-update" onclick="kemaskiniStatus(${ev.IDBENGKEL})">Kemaskini</button>
          <button class="btn-delete" onclick="deleteEvent(${ev.IDBENGKEL})">Padam</button>
        </div>
      </div>`;
  });
}

flatpickr("#calendar",{
  inline:true,dateFormat:"Y-m-d",clickOpens:false,
  defaultDate: <?= $lastMula ? json_encode($lastMula) : "null" ?>,
  onDayCreate:(dObj,dStr,instance,dayElem)=>{ if(eventDateSet.has(fmt(dayElem.dateObj))){ dayElem.classList.add("has-event"); }},
  onReady:(sel,dateStr,instance)=>{ const f=dateStr||<?= $lastMula?json_encode($lastMula):"null" ?>; if(f){instance.jumpToDate(f); renderEventList(f);} },
  onChange:(sel,dateStr)=>{ renderEventList(dateStr); }
});

function deleteEvent(id){
  Swal.fire({title:"Padam Bengkel?",text:"Anda pasti mahu padam?",icon:"warning",showCancelButton:true,confirmButtonText:"Ya, padam"}).then(res=>{
    if(res.isConfirmed){ window.location="delete_bengkel.php?delete="+id; }
  });
}

function kemaskiniStatus(id) {
  const newStatus = document.getElementById("status-" + id).value;
  fetch("update_bengkel_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + encodeURIComponent(id) + "&status=" + encodeURIComponent(newStatus)
  })
  .then(r => r.text())
  .then(txt => {
    Swal.fire("Berjaya!", "Status berjaya dikemaskini", "success");
    const badge = document.getElementById("badge-" + id);
    if (badge) {
      badge.textContent = newStatus;
      badge.className = "event-status";
      if (newStatus === "Dalam Perancangan") badge.classList.add("status-planning");
      else if (newStatus === "Sedang Berjalan") badge.classList.add("status-progress");
      else if (newStatus === "Selesai") badge.classList.add("status-done");
    }
    const select = document.getElementById("status-" + id);
    if (select) select.value = newStatus;
  })
  .catch(err => Swal.fire("Ralat", "Tidak berjaya: " + err, "error"));
}
</script>

<?php if (isset($_SESSION['msg'])): ?>
<script>
Swal.fire({ icon: 'info', title: 'Makluman', text: '<?= $_SESSION['msg']; ?>' });
</script>
<?php unset($_SESSION['msg']); endif; ?>

</body>
</html>
