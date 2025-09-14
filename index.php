<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Error Message Box Styling (Pop-up Modal) */
        .error-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            z-index: 1000; /* Ensure the overlay is on top */
            justify-content: center;
            align-items: center;
        }

        .error-box {
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 5px;
            font-size: 1.2rem;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

        .error-box button {
            margin-top: 15px;
            padding: 10px;
            background-color: #721c24;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .error-box button:hover {
            background-color: #5a1d1d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-side">
            <h1>Welcome back!</h1>
            <p>You can sign in to access your existing account.</p>
        </div>
        <div class="right-side">
            <h2>Sign In</h2>

            <!-- Error Modal Overlay -->
            <div class="error-overlay" id="errorOverlay">
                <div class="error-box">
                    <p>Incorrect IC number or password. Please try again.</p>
                    <button onclick="closeErrorBox()">Close</button>
                </div>
            </div>
            
            <?php
                session_start();
                if (isset($_SESSION['login_error'])) {
                    echo "<script>showErrorBox('".$_SESSION['login_error']."');</script>";
                    unset($_SESSION['login_error']);
                }
            ?>

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
        // Function to validate Sign In form
        function validateLoginForm() {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!username || !password) {
                showErrorBox("Sila masukkan IC Number dan Password.");
                return false;
            }

            return true; // Hanya pastikan field diisi, credential sebenar di check di PHP
        }

        // Show the error box modal
        function showErrorBox(message) {
            document.getElementById("errorOverlay").style.display = "flex";
            document.querySelector(".error-box p").textContent = message;  // Set custom error message
        }

        // Close the error box modal
        function closeErrorBox() {
            document.getElementById("errorOverlay").style.display = "none";
        }
    </script>
</body>
</html>
