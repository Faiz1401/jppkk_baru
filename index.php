<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="style.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container">
    <!-- Left Side -->
    <div class="left-side">
        <h2>Log Masuk</h2>
        <p><strong>Selamat kembali !</strong> Sila masukkan butiran anda</p>

        <!-- Form ditolak ke bawah sikit -->
        <div class="form-wrapper">
            <form action="login.php" method="POST" onsubmit="return validateLoginForm()">
                <label for="username">Kad Pengenalan</label>
                <input type="text" id="username" name="username" placeholder="Kad Pengenalan (tanpa -)">

                <label for="password">Kata Laluan</label>
                <div class="password-box">
                    <input type="password" id="password" name="password" placeholder="Masukkan Kata Laluan">
                </div>

                <p class="forgot"><a href="forgot_password.php">Lupa Kata Laluan ?</a>
                <button type="submit" class="btn">Log Masuk</button>
            </form>

            <p class="signup">Pengguna baru ? <a href="register/register.php">Daftar sini</a></p>
        </div>
    </div>

    <!-- Right Side -->
    <div class="right-side">
        <div class="logos">
            <img src="logo/POLITEKNIK.png" alt="Logo 1">
            <img src="logo/JPPKK.png" alt="Logo 2">
            <img src="logo/KOLEJ_KOMUNITI.png" alt="Logo 3">
        </div>
        <p class="tagline">Sistem Ahli Jawatankuasa Kecil Program
Pengajian Politknik Dan Kolej Komuniti <br> (JK3P2K) <br><br> Bersama Kita Lebih Baik</p>
    </div>
</div>

<script>
    function validateLoginForm() {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!username && !password) {
            showErrorBox("Sila masukkan IC Number dan Kata Laluan.");
            return false;
        } else if (!username) {
            showErrorBox("Sila masukkan IC Number.");
            return false;
        } else if (!password) {
            showErrorBox("Sila masukkan Kata Laluan.");
            return false;
        }
        return true;
    }

    function showErrorBox(message) {
        Swal.fire({
            icon: 'error',
            title: 'Login Error',
            text: message,
            confirmButtonColor: '#d33'
        });
    }
</script>

<?php
if (isset($_SESSION['login_error'])) {
    $msg = $_SESSION['login_error'];

    if ($msg === "Akaun anda sedang menunggu pengesahan admin.") {
        echo "<script>
            Swal.fire({
                icon: 'info',
                title: 'Tunggu Pengesahan',
                text: '$msg',
                confirmButtonColor: '#3085d6'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Login Error',
                text: '$msg',
                confirmButtonColor: '#d33'
            });
        </script>";
    }
    unset($_SESSION['login_error']);
}
?>
</body>
</html>
