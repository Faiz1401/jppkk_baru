<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        /* General Container Layout */
        .container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            min-height: 100vh;
            flex-wrap: wrap;
        }

        /* Left Side Section (Background Purple) */
        .left-side {
            background-color: #8e44ad;
            color: white;
            width: 40%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px;
        }

        .left-side h1 {
            font-size: 2.5rem;
        }

        .left-side p {
            font-size: 1.2rem;
        }

        /* Right Side Section (White Background) */
        .right-side {
            width: 60%;
            padding: 50px;
            background-color: white;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .right-side h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        /* Form Layout for Side by Side Inputs */
        .right-side form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        /* Label Styling */
        .right-side label {
            font-size: 1rem;
            margin-bottom: 5px;
            display: block;
        }

        /* General Input Styling */
        .right-side input,
        .right-side select {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
            width: 100%;
        }

        /* Grouping the input fields for side-by-side display */
        .right-side .input-group {
            display: flex;
            flex-direction: column;
            width: 48%; /* Adjust to 48% for better alignment */
        }

        /* Gender Section */
        .gender {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
            gap: 10px; /* Adds space between radio buttons */
        }

        .gender label {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .gender input[type="radio"] {
            display: none; /* Hide the default radio button */
        }

        .gender div {
            display: flex;
            gap: 15px;
        }

        .gender input[type="radio"] + label {
            padding: 10px 20px;
            background-color: #f0f0f0;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .gender input[type="radio"]:checked + label {
            background-color: #8e44ad;
            color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .gender input[type="radio"]:hover + label {
            background-color: #b16bde;
        }

        /* Buttons Styling */
        .right-side button {
            padding: 10px;
            background-color: #8e44ad;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .right-side button:hover {
            background-color: #732d91;
        }

        /* Submit Button Styling */
        .right-side button[type="submit"] {
            background-color: #3498db;  /* Bright blue color */
            padding: 18px 20px;  /* Increase padding for a larger button */
            font-size: 1.2rem;  /* Slightly larger font size */
            border-radius: 10px;  /* Rounded corners for a smooth look */
            margin-top: 20px;
            width: 100%;  /* Full width for a consistent design */
            cursor: pointer;
            transition: all 0.3s ease;  /* Smooth transition on hover */
        }

        .right-side button[type="submit"]:hover {
            background-color: #2980b9;
        }

        /* Media Queries for Responsive Design */
        @media screen and (max-width: 768px) {
            .container {
                flex-direction: column;
                height: auto;
            }

            .left-side, .right-side {
                width: 100%;
            }

            .right-side form {
                padding: 20px;
            }

            .right-side .input-group {
                width: 100%; /* Stack vertically on small screens */
            }

            .right-side button {
                width: 100%;
            }

            .gender {
                flex-direction: column; /* Stack gender options vertically */
                align-items: flex-start;
            }
        }

        /* Hidden Steps */
        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
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
            <form id="registrationForm">
                <!-- Step 1 -->
                <div class="form-step active">
                    <h2>Step 1: Personal Information</h2>

                    <!-- Step 1 Inputs -->
                    <div class="input-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" placeholder="Full Name" required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" placeholder="Email Address" required>
                    </div>
                    <div class="input-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" placeholder="Phone Number" required>
                    </div>
                    <div class="input-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" placeholder="Your Address" required>
                    </div>

                    <!-- Gender Section -->
                    <div class="gender">
                        <label>Gender</label>
                        <div>
                            <input type="radio" id="male" name="gender" value="Male" required>
                            <label for="male">Male</label>
                            <input type="radio" id="female" name="gender" value="Female">
                            <label for="female">Female</label>
                        </div>
                    </div>

                    <!-- Step Navigation -->
                    <button type="button" class="btn-next">Next Step →</button>
                </div>

                <!-- Step 2 -->
                <div class="form-step">
                    <h2>Step 2: Address Information</h2>

                    <div class="input-group">
                        <label for="street">Street</label>
                        <input type="text" id="street" placeholder="Street" required>
                    </div>
                    <div class="input-group">
                        <label for="city">City</label>
                        <input type="text" id="city" placeholder="City" required>
                    </div>
                    <div class="input-group">
                        <label for="postcode">Postcode</label>
                        <input type="text" id="postcode" placeholder="Postcode" required>
                    </div>
                    <button type="button" class="btn-prev">← Previous</button>
                    <button type="button" class="btn-next">Next Step →</button>
                </div>

                <!-- Step 3 -->
                <div class="form-step">
                    <h2>Step 3: Account Information</h2>

                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" placeholder="Password" required>
                    </div>
                    <div class="input-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <button type="button" class="btn-prev">← Previous</button>
                    <button type="submit" class="btn-submit">Submit</button>
                </div>

            </form>
        </div>
    </div>

    <script>
        // JavaScript to handle form navigation
        const nextBtns = document.querySelectorAll('.btn-next');
        const prevBtns = document.querySelectorAll('.btn-prev');
        const steps = document.querySelectorAll('.form-step');
        let currentStep = 0;

        // Function to show the current active step
        function updateStep() {
            steps.forEach((step, index) => {
                step.classList.toggle('active', index === currentStep); // Show the current step
            });
        }

        // Handle "Next Step" button click
        nextBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                if (currentStep < steps.length - 1) {
                    currentStep++; // Increment to next step
                    updateStep(); // Update the active step
                }
            });
        });

        // Handle "Previous Step" button click
        prevBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                if (currentStep > 0) {
                    currentStep--; // Decrement to previous step
                    updateStep(); // Update the active step
                }
            });
        });

        // Initialize with the first step visible
        updateStep();
    </script>

</body>
</html>
