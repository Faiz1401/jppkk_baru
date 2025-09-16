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
            <h1>Welcome back!</h1>
            <p>You can sign in to access your existing account.</p>
        </div>

        <!-- Right side -->
        <div class="right-side">
            <h2>Sign In</h2>
            
            <!-- Login Form -->
            <form action="login.php" method="POST" onsubmit="return validateLoginForm()">
                <label for="username">IC Number</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <a href="#">Forgot password?</a>
                <button type="submit">Sign In</button>
            </form>

            <p>New here? <a href="register/register.php">Create an Account</a></p>
        </div>
    </div>

    <script>
        function validateLoginForm() {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!username || !password) {
                showErrorBox("Sila masukkan IC Number dan Password.");
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
        echo "<script>
            window.onload = function() {
                showErrorBox('$msg');
            };
        </script>";
        unset($_SESSION['login_error']);
    }
    ?>
</body>
</html>
