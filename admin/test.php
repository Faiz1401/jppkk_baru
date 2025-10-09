<?php
session_start();
include '../db_connection.php';

// 👉 Ambil semua bengkel dengan join program, bidang & kursus
$events = [];
$res = $conn->query("
  SELECT b.IDBENGKEL, b.KATEGORI, b.SIRIBENGKEL, b.TAHUN, b.TARIKHMULA, b.TARIKHTAMAT, 
         b.LOKASI, b.STATUS, b.JUSTIFIKASI,
         p.NAMAPROGRAM, bd.NAMABIDANGBK, k.NAMAKURSUS
  FROM tblbengkel b
  LEFT JOIN tblprogram p ON b.IDPROGRAM = p.IDPROGRAM
  LEFT JOIN tblbidang bd ON b.IDBIDANG = bd.IDBIDANGBK
  LEFT JOIN tblkursus k ON b.IDKURSUS = k.IDKURSUS
  ORDER BY b.TARIKHMULA ASC
");
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
    h1, h2 { text-align:center; font-size:24px; font-weight:700; color:#2c3e50; margin:20px 0 15px; }
    h1::after, h2::after { content:''; width:80px; height:3px; background:linear-gradient(90deg,#3498db,#9b59b6); display:block; margin:8px auto 0; border-radius:3px; }

    .layout { max-width:1200px; margin:20px auto; display:flex; gap:30px; align-items:flex-start; }
    .left-col { flex:2; }
    .calendar-box { flex:1; background:#fff; padding:20px; border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,.2); display:flex; flex-direction:column; align-items:center; }
    #calendar { display:inline-block; margin:0 auto; }

    .form-container, .bengkel-list { background:#fff; padding:20px; border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,.2); }

    .bengkel-list { max-width:100%; margin:20px auto 40px; }
    .bengkel-table { width:100%; border-collapse:collapse; font-size:14px; }
    .bengkel-table th, .bengkel-table td { padding:12px 14px; text-align:left; }
    .bengkel-table th { background:#2c3e50; color:white; font-weight:600; text-transform:uppercase; font-size:13px; }
    .bengkel-table tr:nth-child(even){ background:#fafafa; }
    .bengkel-table tr:hover{ background:#f1f1f1; }
    .status-badge { display:inline-block; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; color:#fff; }
    .status-planning { background:#f39c12; }
    .status-progress { background:#007bff; }
    .status-done { background:#28a745; }

    .event-card { border:1px solid #ddd; padding:12px; margin-bottom:12px; border-radius:8px; background:#fff; }
    .event-status { display:inline-block; padding:4px 10px; border-radius:20px; font-size:13px; font-weight:600; color:#fff; }
    .btn-update, .btn-delete { padding:8px 12px; border:none; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; }
    .btn-update { background:#007BFF; color:#fff; }
    .btn-delete { background:#dc3545; color:#fff; }

    .flatpickr-day.has-event { background-color:rgba(0,123,255,0.15); border-color:#007BFF; color:#007BFF; border-radius:50%; }
  </style>
</head>
<body>

<div class="navbar">
  <a href="index.php"><i class="fa fa-fw fa-home"></i> Dashboard</a> 
  <a class="active" href="bengkel.php"><i class="fa-solid fa-calendar"></i> Bengkel</a> 
</div>

<div class="layout">
  <!-- Column kiri -->
  <div class="left-col">
    <!-- Senarai Bengkel -->
    <div class="bengkel-list">
      <h2>Senarai Bengkel</h2>
      <?php if (count($events) > 0): ?>
        <table class="bengkel-table">
          <thead>
            <tr>
              <th>Siri / Tahun</th>
              <th>Program</th>
              <th>Bidang</th>
              <th>Kursus</th>
              <th>Lokasi</th>
              <th>Status</th>
              <th>Tempoh</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($events as $ev): 
              $status = $ev['STATUS'] ?? "-";
              $statusClass = "status-planning";
              if ($status === "Sedang Berjalan") $statusClass = "status-progress";
              else if ($status === "Selesai") $statusClass = "status-done";

              $siriDisplay = $ev['KATEGORI'] . " Bil." . str_pad($ev['SIRIBENGKEL'],2,"0",STR_PAD_LEFT);
            ?>
              <tr>
                <td><?= htmlspecialchars($siriDisplay . " / " . $ev['TAHUN']) ?></td>
                <td><?= htmlspecialchars($ev['NAMAPROGRAM'] ?? '-') ?></td>
                <td><?= htmlspecialchars($ev['NAMABIDANG'] ?? '-') ?></td>
                <td><?= htmlspecialchars($ev['NAMAKURSUS'] ?? '-') ?></td>
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
  <div class="calendar-box">
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
    let statusClass = "status-planning";
    if (ev.STATUS === "Sedang Berjalan") statusClass = "status-progress";
    else if (ev.STATUS === "Selesai") statusClass = "status-done";

    wrap.innerHTML += `
      <div class="event-card">
        <div class="event-header">
          <span class="event-status ${statusClass}" id="badge-${ev.IDBENGKEL}">${ev.STATUS}</span>
        </div>
        <div class="event-body">
          <p><b>Siri:</b> ${ev.KATEGORI} Bil.${ev.SIRIBENGKEL} / ${ev.TAHUN}</p>
          <p><b>Program:</b> ${ev.NAMAPROGRAM || "-"}</p>
          <p><b>Bidang:</b> ${ev.NAMABIDANG || "-"}</p>
          <p><b>Kursus:</b> ${ev.NAMAKURSUS || "-"}</p>
          <p><b>Lokasi:</b> ${ev.LOKASI || "-"}</p>
          <p><b>Justifikasi:</b> ${ev.JUSTIFIKASI || "-"}</p>
          <p><b>Tempoh:</b> ${ev.TARIKHMULA} → ${ev.TARIKHTAMAT || ev.TARIKHMULA}</p>
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
</script>

</body>
</html>
