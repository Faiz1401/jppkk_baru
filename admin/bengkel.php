<?php
include '../db_connection.php'; 

// 👉 Ambil semua bengkel
$events = [];
$res = $conn->query("SELECT IDBENGKEL, SIRIBENGKEL, TAHUN, TARIKHMULA, TARIKHTAMAT, LOKASI, STATUS 
                     FROM tblbengkel
                     ORDER BY TARIKHMULA ASC");
while($row = $res->fetch_assoc()) {
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
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: Arial, sans-serif; background:#f4f6f9; }
    h2 { text-align:center; margin:20px 0; }

    .layout {
      max-width: 1200px;
      margin: 20px auto;
      display: flex;
      gap: 30px;
      align-items: flex-start;
    }

    .form-container, .calendar-box {
      background:#fff;
      padding:20px;
      border-radius:10px;
      box-shadow:0 3px 6px rgba(0,0,0,.2);
    }

    .form-container { flex: 2; }
    .calendar-box { flex: 1; }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px 20px;
    }

    .form-item { display:flex; flex-direction:column; }
    .form-item label { font-weight:600; margin-bottom:5px; }
    .form-item input, .form-item select, .form-item textarea {
      padding:10px;
      border:1px solid #ccc;
      border-radius:8px;
      font-size:14px;
    }
    .form-item textarea { grid-column: span 2; resize: vertical; }

    button {
      background:#007BFF;
      color:#fff;
      padding:10px 16px;
      border:none;
      border-radius:8px;
      cursor:pointer;
      font-weight:600;
      margin-top: 10px;
    }
    button:hover { background:#0056b3; }

    .event-card {
      border:1px solid #ddd;
      padding:12px;
      margin-bottom:12px;
      border-radius:8px;
      background:#fff;
    }
    .btn-delete {
      background:#dc3545;
      color:#fff;
      border:none;
      padding:6px 12px;
      border-radius:6px;
      cursor:pointer;
    }
    .btn-delete:hover { background:#b02a37; }

    @media(max-width: 900px) {
      .layout { flex-direction: column; }
      .form-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<div class="layout">
  <!-- FORM -->
  <div class="form-container">
    <h2>Daftar Bengkel Baru</h2>
    <form method="POST" action="create_bengkel.php">
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
            <?php for($i=1;$i<=10;$i++): $num=str_pad($i,2,"0",STR_PAD_LEFT); ?>
              <option value="<?= $num ?>"><?= $num ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="form-item">
          <label>Tahun</label>
          <select name="TAHUN" required>
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
      <button type="submit">Simpan Bengkel</button>
    </form>
  </div>

  <!-- CALENDAR -->
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
  list.forEach(ev=>{
    const year=ev.TAHUN?ev.TAHUN.substring(0,4):"", [siriKategori="",siriNo=""]=(ev.SIRIBENGKEL||"").split("-");
    wrap.innerHTML+=`
      <div class="event-card">
        <p><b>Siri:</b> ${siriKategori} Bil.${siriNo} / ${year}</p>
        <p><b>Lokasi:</b> ${ev.LOKASI||"-"}</p>
        <p><b>Status:</b> ${ev.STATUS}</p>
        <p><b>Tempoh:</b> ${ev.TARIKHMULA} → ${ev.TARIKHTAMAT||ev.TARIKHMULA}</p>
        <button class="btn-delete" onclick="deleteEvent(${ev.IDBENGKEL})">Padam</button>
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
</script>
</body>
</html>
