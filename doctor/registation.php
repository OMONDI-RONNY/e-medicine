<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Medicine Registration Page</title>
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right, #74ebd5, #acb6e5); /* Gradient background */
        }

        .left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            background-image: url('../resources/images/doc.png'); /* Medical-themed background image */
            background-size: cover;
            background-position: center;
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .left h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .left h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .left ul {
            padding-left: 20px;
        }

        .left ul li {
            margin: 10px 0;
            font-size: 1.2rem;
        }

        .right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .registration-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            text-align: center;
            max-width: 400px;
            width: 100%;
            position: relative;
            overflow: hidden; /* To contain the pseudo-elements */
        }

        .registration-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 123, 255, 0.1); /* Light blue overlay */
            border-radius: 12px;
            z-index: -1; /* Set behind other content */
        }

        .registration-card h2 {
            margin-bottom: 20px;
            font-size: 1.8rem;
            color: #007bff; /* Primary theme color */
            position: relative; /* To overlap with pseudo-element */
            z-index: 1; /* Above the overlay */
        }

        .registration-card .icon {
            width: 70px;
            height: 70px;
            margin: 20px auto; /* Center icon */
            position: relative; /* To overlap with pseudo-element */
            z-index: 1; /* Above the overlay */
        }

        .registration-card input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            position: relative; /* Ensure inputs are above the overlay */
            z-index: 1; /* Above the overlay */
        }

        .registration-card input:focus {
            border-color: #007bff; /* Primary theme color on focus */
            outline: none; /* Remove outline */
        }

        .registration-card a {
            display: block;
            margin: 15px 0;
            color: #007bff; /* Primary theme color for links */
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .registration-card a:hover {
            color: #0056b3; /* Darker shade for hover */
        }

        .registration-card button {
            background-color: #007bff; /* Primary theme color */
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.3s ease; /* Added transform transition */
            position: relative;
            z-index: 1; /* Above the overlay */
        }

        .registration-card button:hover {
            background-color: #0056b3; /* Darker shade for hover */
            transform: translateY(-2px); /* Button lift effect */
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .left {
                padding: 20px;
            }

            .registration-card {
                padding: 30px;
            }

            .left h1 {
                font-size: 2rem;
            }

            .left h3 {
                font-size: 1.2rem;
            }

            .registration-card h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="left">
        <h1>Your Health, Our Priority</h1>
        <h3>Why Choose E-Medicine?</h3>
        <ul>
            <li>24/7 Access to Medical Experts</li>
            <li>Secure Online Consultations</li>
            <li>Personalized Health Plans</li>
        </ul>
    </div>
    <div class="right">
        <div class="registration-card">
            <div class="icon">
                <!-- Using the same custom SVG account icon from the login page -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#007bff"><path d="M12 12c2.7 0 5.2-.9 7.2-2.5-1.3-2.7-4.2-4.5-7.2-4.5S6.1 6.8 4.8 9.5C6.8 11.1 9.3 12 12 12zm0 2c-4 0-12 2-12 6v4h24v-4c0-4-8-6-12-6z"/></svg>
            </div>
            <h2>Register for E-Medicine</h2>
            <form action="register.php" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Register</button>
            </form>
            <a href="login.php">Already have an account? Login here</a>
        </div>
    </div>
</body>

</html>
