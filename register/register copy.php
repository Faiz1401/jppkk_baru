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
    .left-side {background-color:#8e44ad;color:white;width:40%;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:50px;}
    .left-side h1 {font-size:2.5rem;}
    .left-side p {font-size:1.2rem;}
    .right-side {width:60%;padding:50px;background:white;display:flex;flex-direction:column;}

    .right-side h2 {font-size:2rem;margin-bottom:20px;grid-column:span 2;}

    form {display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .input-group {display:flex;flex-direction:column;width:100%;}
    .input-group.full-width {grid-column:span 2;}
    label {font-size:1rem;margin-bottom:5px;}
    input,select {padding:12px;border-radius:5px;border:1px solid #ccc;font-size:1rem;width:100%;}

    .gender {display:flex;flex-direction:column;gap:10px;}
    .gender label {font-weight:bold;margin-bottom:10px;}
    .gender input[type="radio"] {display:none;}
    .gender div {display:flex;gap:15px;}
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
  </style>
</head>
<body>
  <div class="container">
    <!-- Left Panel -->
    <div class="left-side">
      <h1>Join Us Today</h1>
      <p>Fill out this form to get started with your account.</p>
    </div>

    <!-- Right Panel -->
    <div class="right-side">
      <form id="registrationForm" action="process_register.php" method="POST">
        <!-- Error Box -->
        <div class="error-box" id="errorBox">⚠ Please fill in all required fields before continuing.</div>

        <!-- Step 1 -->
        <div class="form-step active">
          <h2>Step 1: Personal Information</h2>
          <div class="input-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Full Name" required>
          </div>
          <div class="input-group">
            <label for="gred">Gred</label>
            <input type="text" id="gred" name="gred" placeholder="Enter your grade" required>
          </div>
          <div class="input-group">
            <label for="noIC">IC Number</label>
            <input type="text" id="noIC" name="noIC" placeholder="Enter your IC number" required>
          </div>
          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
          </div>
          <div class="input-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
          </div>
          <div class="input-group">
            <label for="religion">Religion</label>
            <select id="religion" name="religion" required>
              <option value="">--Select Religion--</option>
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
              <label>Gender</label>
              <div>
                <input type="radio" id="male" name="gender" value="Male" required>
                <label for="male">Male</label>
                <input type="radio" id="female" name="gender" value="Female" required>
                <label for="female">Female</label>
              </div>
            </div>
          </div>
          <div class="form-navigation">
            <button type="button" class="btn-next">Next Step →</button>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="form-step">
          <h2>Step 2: Academic Information</h2>
          <div class="input-group">
            <label for="institusi">Institution</label>
            <input type="text" id="institusi" name="institusi" placeholder="Enter your institution" required>
          </div>
          <div class="input-group">
            <label for="field">Field of Study</label>
            <select id="field" name="field" required>
              <option value="">-- Pilih Bidang --</option>
              <?php foreach ($bidangOptions as $opt): ?>
                <option value="<?= $opt['IDBIDANGBK'] ?>"><?= $opt['NAMABIDANGBK'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="input-group">
            <label for="subField">Sub Field</label>
            <input type="text" id="subField" name="subField" placeholder="Enter your sub field" required>
          </div>
          <div class="input-group">
            <label for="department">Department</label>
            <input type="text" id="department" name="department" placeholder="Enter your department" required>
          </div>
            <div class="input-group">
            <label for="program">Program</label>
              <select id="program" name="program" required onchange="toggleProgramForm(this)">
                  <option value="">-- Pilih Program --</option>
                  <?php
                  include 'db_connection.php';
                  $sql = "SELECT KODPROGRAM, NAMAPROGRAM FROM tblprogram";
                  $res = $conn->query($sql);
                  if ($res && $res->num_rows > 0) {
                      while ($row = $res->fetch_assoc()) {
                          // Display "KODPROGRAM - NAMAPROGRAM", value tetap KODPROGRAM
                          echo "<option value='" . $row['KODPROGRAM'] . "'>" . $row['KODPROGRAM'] . " - " . $row['NAMAPROGRAM'] . "</option>";
                      }
                  }
                  ?>
                  <option value="new">+ Tambah Program Baru</option>
              </select>
            </div>
            <!-- Borang Tambah Program Baru (hidden by default) -->
            <div id="newProgramForm" style="display:none; grid-column:span 2; border:1px solid #ccc; padding:15px; margin-top:10px; border-radius:5px;">
              <h3>Tambah Program Baru</h3>
              <div class="input-group">
                <label for="jenisProgram">Jenis Program</label>
                <input type="text" id="jenisProgram" name="jenisProgram">
              </div>
              <div class="input-group">
                <label for="kodProgram">Kod Program</label>
                <input type="text" id="kodProgram" name="kodProgram">
              </div>
              <div class="input-group">
                <label for="namaProgram">Nama Program</label>
                <input type="text" id="namaProgram" name="namaProgram">
              </div>
              <div class="input-group">
                <label for="bilKursus">Bilangan Kursus</label>
                <input type="number" id="bilKursus" name="bilKursus">
              </div>
              <div class="input-group">
                <label for="necCode">NEC Code</label>
                <input type="number" id="necCode" name="necCode">
              </div>
              <div class="input-group">
                <label for="akreditasi">Akreditasi</label>
                <input type="text" id="akreditasi" name="akreditasi">
              </div>
              <div class="input-group">
                <label for="versi">Versi</label>
                <input type="text" id="versi" name="versi">
              </div>
              <div class="input-group">
                <label for="tempoh">Tempoh Pengajian</label>
                <input type="number" id="tempoh" name="tempoh">
              </div>
            </div>

          <div class="input-group">
            <label for="appointmentDate">Appointment Date</label>
            <input type="date" id="appointmentDate" name="appointmentDate" required>
          </div>
          <div class="input-group">
            <label for="retirementDate">Retirement Date</label>
            <input type="date" id="retirementDate" name="retirementDate" required>
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
      const inputs = steps[stepIndex].querySelectorAll("input, select");
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

    updateStep();
  </script>
</body>
</html>
