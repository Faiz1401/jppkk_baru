<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="styles.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <!-- Left side -->
        <div class="left-side">
            <h1 >Selamat Datang!</h1></br>
            <p style="font-size:1rem;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
                Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown 
                printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, 
                but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with 
                the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus 
                PageMaker including versions of Lorem Ipsum.</p>
            <p style="margin-top:20px; font-size:1rem;">
                Pengguna baru ? 
                <a href="register/register.php" style="color: #fff; font-weight:bold; text-decoration: underline;">
                    Daftar sini
                </a>
            </p>
        </div>

        <!-- Right side -->
        <div class="right-side">
            <h2>Log Masuk</h2>
                <?php
                    if (isset($_SESSION['error'])) {
                        echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
                        unset($_SESSION['error']);
                    }
                    if (isset($_SESSION['success'])) {
                        echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
                        unset($_SESSION['success']);
                    }
                ?>
            <!-- Login Form -->
            <form action="login.php" method="POST" onsubmit="return validateLoginForm()">
                <label for="username">Kad Pengenalan</label>
                <input type="text" id="username" name="username">

                <label for="password">Password</label>
                <input type="password" id="password" name="password">

                <a href="forgot_password.php">Lupa Password?</a>
                <button type="submit">Log Masuk</button>
            </form>
        </div>
    </div>

    <script>
        function validateLoginForm() {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!username && !password) {
                showErrorBox("Sila masukkan IC Number dan Password.");
                return false;
            } else if (!username) {
                showErrorBox("Sila masukkan IC Number.");
                return false;
            } else if (!password) {
                showErrorBox("Sila masukkan Password.");
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

    // Kalau mesej khas "menunggu pengesahan"
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
        // Default (error)
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
