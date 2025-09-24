<?php
include 'db_connection.php';

$jawatanOptions = [];
$sql = "SELECT IDJAWATAN, NAMA_JAWATAN FROM tbljawatan";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jawatanOptions[] = $row;
    }
}
$conn->close();
?>

<?php
include 'db_connection.php';

// ambil data bidang
$bidangOptions = [];
$sql = "SELECT IDBIDANGBK, NAMABIDANGBK FROM tblbidang";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bidangOptions[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
  <style>
    * {margin:0;padding:0;box-sizing:border-box;}
    body {font-family: Arial, sans-serif;background-color:#f4f4f4;}

    .container {display:flex;justify-content:space-between;padding:20px;min-height:100vh;flex-wrap:wrap;}
    .left-side {
      width: 40%;
      display: flex;
      flex-direction: column;
      justify-content: center;   /* center vertical */
      align-items: center;       /* center horizontal */
      padding: 50px;
      color: black;
      text-align: center;

      background: url("../images/Background2.jpg") no-repeat center center;
      background-size: cover;
      position: relative;
    }

    /* Buang overlay */
    .left-side::before {
      display: none;
    }

    /* Logo naik atas */
    .left-side .logos {
      position: absolute;
      top: 20px;        /* jarak sikit dari atas */
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 20px;
    }

    .left-side .logos img {
      height: 60px;    /* boleh adjust */
      width: auto;
    }


    .right-side {width:60%;padding:50px;background:white;display:flex;flex-direction:column;}

    .right-side h2 {font-size:2rem;margin-bottom:20px;grid-column:span 2;}

    form {display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .input-group {display:flex;flex-direction:column;width:100%;}
    .input-group.full-width {grid-column:span 2;}
    label {font-size:1rem;margin-bottom:5px;}
    input,select {padding:12px;border-radius:5px;border:1px solid #ccc;font-size:1rem;width:100%;}

    .gender {display:flex;flex-direction:column;gap:10px;}
    .gender label {margin-bottom:10px;}
    .gender input[type="radio"] {display:none;}
    .gender div {font-weight:bold;display:flex;gap:15px;}
    .gender input[type="radio"]+label {padding:10px 20px;background:#f0f0f0;border-radius:50px;cursor:pointer;transition:0.3s;}
    .gender input[type="radio"]:checked+label {background:#8e44ad;color:white;}
    .gender input[type="radio"]:hover+label {background:#b16bde;}

    button {padding:12px 20px;background:#8e44ad;color:white;font-size:1rem;border:none;border-radius:5px;cursor:pointer;}
    button:hover {background:#732d91;}
    button[type="submit"] {padding:18px 20px;font-size:1.2rem;border-radius:10px;grid-column:span 2;}
    .btn-submit {background:#3498db;}
    .btn-submit:hover {background:#2980b9;}

    /* Disabled state */
    .btn-disabled {
      background:#ccc !important;
      color:#666 !important;
      cursor:not-allowed !important;
    }

    .form-navigation {display:flex;justify-content:space-between;grid-column:span 2;}

    @media screen and (max-width:768px){
      .container{flex-direction:column;}
      .left-side,.right-side{width:100%;} 
      form{grid-template-columns:1fr;}
      .form-navigation{flex-direction:column;gap:10px;}
      button{width:100%;}
    }

    .form-step {display:none;grid-column:span 2;}
    .form-step.active {display:contents;}

    .confirmation {display:flex;align-items:center;gap:10px;font-size:1rem;}

    /* Error Box */
    .error-box {
      grid-column: span 2;
      background:#ffdddd;
      color:#a94442;
      border:1px solid #a94442;
      padding:10px;
      border-radius:5px;
      display:none;
    }

    input, select, textarea {
        border: 1px solid #ccc; /* normal */
        border-radius: 5px;
        padding: 12px;
    }

    /* Progressbar (Rail Kereta Api) */
    .progressbar {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 30px;
      gap: 10px;
    }

    .progress-step {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      background: #ccc;
      color: white;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: 0.3s;
    }

    .progress-step.active {
      background: #8e44ad; /* purple */
    }

    .progress-line {
      flex: 1;
      height: 4px;
      background: #ccc;
      transition: 0.3s;
    }

    .progress-line.active {
      background: #8e44ad;
    }
  </style>
</head>
<body>
    <div class="container">
        <div class="left-side">
          <div class="logos">
              <img src="../logo/POLITEKNIK.png" alt="Logo 1">
              <img src="../logo/JPPKK.png" alt="Logo 2">
              <img src="../logo/KOLEJ_KOMUNITI.png" alt="Logo 3">
          </div>
        <h1>Join Us Today</h1>
        <p>Fill out this form to get started with your account.</p>
        <p style="margin-top:20px; font-size:1rem;">
            Already have an account? 
            <a href="../index.php" style="color: #2289dc; font-weight:bold; text-decoration: underline;">
                Sign In
            </a>
        </p>
    </div>

    <div class="right-side">
          <div class="progressbar">
            <div class="progress-step active">1</div>
            <div class="progress-line"></div>
            <div class="progress-step">2</div>
            <div class="progress-line"></div>
            <div class="progress-step">3</div>
          </div>
        <form id="registrationForm" action="process_register.php" method="POST">
            <div class="error-box" id="errorBox">⚠ Sila isi semua butiran dibawah.</div>

            <!-- Step 1 -->
            <div class="form-step active">
                <h2>Langkah 1: Maklumat Diri</h2>
                <div class="input-group">
                    <label for="name">Nama Penuh</label>
                    <input type="text" id="name" name="name" placeholder="Full Name" required>
                </div>
                <div class="input-group">
                    <label for="noIC">Kad Pengenalan</label>
                    <input type="text" id="noIC" name="noIC" placeholder="Nombor kad pengenalan (tanpa -)" required>
                </div>
                <div class="input-group">
                    <label for="dob">Tarikh Lahir</label>
                    <input type="date" id="dob" name="dob" readonly required>
                </div>
                <div class="input-group">
                    <label for="retirementDate">Tarikh Bersara</label>
                    <input type="date" id="retirementDate" name="retirementDate" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan Email" required>
                </div>
                <div class="input-group">
                    <label for="phone">Nombor Telefon</label>
                    <input type="tel" id="phone" name="phone" placeholder="Nombor Telefon (0123456789)" required>
                </div>
                <div class="input-group full-width">
                    <label for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat" placeholder="Masukkan Alamat" required rows="5" style="font-family: Arial, sans-serif; resize:vertical; font-size:1.1rem; padding:12px;"></textarea>
                </div>
                <div class="input-group">
                    <label for="religion">Agama</label>
                    <select id="religion" name="religion" required>
                        <option value="">--Sila Pilih Agama--</option>
                        <option value="Islam">Islam</option>
                        <option value="Christianity">Christianity</option>
                        <option value="Hinduism">Hinduism</option>
                        <option value="Buddhism">Buddhism</option>
                        <option value="Sikhism">Sikhism</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="input-group full-width">
                    <div class="gender">
                        <label>Jantina</label>
                        <div>
                            <input type="radio" id="lelaki" name="gender" value="Lelaki" required>
                            <label for="lelaki">Lelaki</label>
                            <input type="radio" id="perempuan" name="gender" value="Perempuan" required>
                            <label for="perempuan">Perempuan</label>
                        </div>
                    </div>
                </div>
                <div class="form-navigation">
                    <button type="button" class="btn-next">Seterusnya →</button>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="form-step">
                <h2>Step 2: Academic / Institution Information</h2>
                <div class="input-group">
                    <label for="institusi">Institution</label>
                    <input type="text" id="institusi" name="institusi" placeholder="Enter your institution" required>
                </div>
                <div class="input-group full-width">
                    <label for="alamatInstitusi">Alamat Institusi</label>
                    <textarea id="alamatInstitusi" name="alamatInstitusi" placeholder="Masukkan Alamat Institusi" required rows="5" style="font-family: Arial, sans-serif; resize:vertical; font-size:1.1rem; padding:12px;"></textarea>
                </div>
                <div class="input-group">
                    <label for="jawatan">Jawatan</label>
                    <select id="jawatan" name="jawatan" required>
                        <option value="">-- Pilih Jawatan --</option>
                        <?php foreach($jawatanOptions as $j): ?>
                            <option value="<?= $j['IDJAWATAN'] ?>"><?= $j['NAMA_JAWATAN'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-group">
                    <label for="gred">Gred</label>
                    <select id="gred" name="gred" required>
                        <option value="">-- Pilih Gred --</option>
                    </select>
                </div>
                <div class="form-navigation">
                    <button type="button" class="btn-prev">← Previous</button>
                    <button type="button" class="btn-next">Next Step →</button>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="form-step">
                <h2>Step 3: Confirmation</h2>
                <div class="input-group full-width confirmation">
                    <input type="checkbox" id="confirmation">
                    <label for="confirmation">Saya mengesahkan segala maklumat yang diisikan adalah benar.</label>
                </div>
                <div class="form-navigation">
                    <button type="button" class="btn-prev">← Previous</button>
                    <button type="submit" id="submitBtn" class="btn-submit btn-disabled" disabled>Submit</button>
                </div>
            </div>

        </form>
    </div>
</div>

  <script>
      // Auto-populate DOB dari No IC
      document.getElementById('noIC').addEventListener('input', function() {
          const ic = this.value;
          const dobInput = document.getElementById('dob');

          // Pastikan ada 6 angka
          if (/^\d{6}/.test(ic)) {
              let yy = ic.substr(0,2);
              let mm = ic.substr(2,2);
              let dd = ic.substr(4,2);

              // Tentukan abad (19xx atau 20xx)
              let yearPrefix = (parseInt(yy) > 50) ? '19' : '20';
              let year = yearPrefix + yy;

              // Format YYYY-MM-DD untuk input type=date
              dobInput.value = `${year}-${mm}-${dd}`;
          } else {
              dobInput.value = '';
          }
      });

        function toggleProgramForm(select) {
      const form = document.getElementById("newProgramForm");
      if (select.value === "new") {
        form.style.display = "grid";
        // Make required
        document.querySelectorAll("#newProgramForm input").forEach(inp => inp.setAttribute("required","true"));
      } else {
        form.style.display = "none";
        // Remove required
        document.querySelectorAll("#newProgramForm input").forEach(inp => inp.removeAttribute("required"));
      }
    }
    const steps = document.querySelectorAll(".form-step");
    const nextBtns = document.querySelectorAll(".btn-next");
    const prevBtns = document.querySelectorAll(".btn-prev");
    const errorBox = document.getElementById("errorBox");
    const confirmation = document.getElementById("confirmation");
    const submitBtn = document.getElementById("submitBtn");
    let currentStep = 0;

    function updateStep() {
      steps.forEach((step, index) => {
        step.classList.toggle("active", index === currentStep);
      });
    }

    function validateStep(stepIndex) {
      const inputs = steps[stepIndex].querySelectorAll("input, select, textarea");
      let valid = true;
      inputs.forEach(input => {
        input.style.border = "1px solid #ccc"; // reset
        if (input.hasAttribute("required")) {
          if (input.type === "radio") {
            const radios = steps[stepIndex].querySelectorAll(`input[name="${input.name}"]`);
            if (![...radios].some(r => r.checked)) {
              valid = false;
            }
          } else if (!input.value.trim()) {
            valid = false;
            input.style.border = "1px solid red";
          }
        }
      });
      return valid;
    }

    nextBtns.forEach(btn => {
      btn.addEventListener("click", () => {
        if (!validateStep(currentStep)) {
          errorBox.style.display = "block";
        } else {
          errorBox.style.display = "none";
          currentStep++;
          updateStep();
        }
      });
    });

    prevBtns.forEach(btn => {
      btn.addEventListener("click", () => {
        errorBox.style.display = "none";
        currentStep--;
        updateStep();
      });
    });

    // Enable/disable submit based on checkbox
    confirmation.addEventListener("change", () => {
      if (confirmation.checked) {
        submitBtn.disabled = false;
        submitBtn.classList.remove("btn-disabled");
      } else {
        submitBtn.disabled = true;
        submitBtn.classList.add("btn-disabled");
      }
    });

    document.getElementById("registrationForm").addEventListener("submit", (e) => {
      if (!validateStep(currentStep)) {
        e.preventDefault();
        errorBox.style.display = "block";
      } else {
        errorBox.style.display = "none";
      }
    });

    
      document.getElementById('jawatan').addEventListener('change', function() {
          const jawatanId = this.value;
          const gredSelect = document.getElementById('gred');

          // reset dropdown
          gredSelect.innerHTML = '<option value="">-- Pilih Gred --</option>';

          if(jawatanId) {
              fetch('process_register.php?jawatan_id=' + jawatanId)
              .then(res => res.json())
              .then(data => {
                  data.forEach(g => {
                      const option = document.createElement('option');
                      option.value = g.IDGRED;
                      option.textContent = g.GRED;
                      gredSelect.appendChild(option);
                  });
              });
          }
      });
      function updateStep() {
  steps.forEach((step, index) => {
    step.classList.toggle("active", index === currentStep);
  });

  // Update progressbar
  const progressSteps = document.querySelectorAll(".progress-step");
  const progressLines = document.querySelectorAll(".progress-line");

  progressSteps.forEach((ps, idx) => {
    if (idx <= currentStep) {
      ps.classList.add("active");
    } else {
      ps.classList.remove("active");
    }
  });

  progressLines.forEach((line, idx) => {
    if (idx < currentStep) {
      line.classList.add("active");
    } else {
      line.classList.remove("active");
    }
  });
}

    updateStep();
  </script>
</body>
</html>
