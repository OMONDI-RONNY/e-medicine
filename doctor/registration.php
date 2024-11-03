
<?php
session_start(); 
include '../access/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $specialty = $_POST['specialty'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("INSERT INTO doctors (firstname, lastname, Specialty, Email, Phone, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssis', $firstname, $lastname, $specialty, $email, $phone, $password);

    if ($stmt->execute()) {
        // Get the last inserted doctor ID
        $doctorId = $conn->insert_id;

        // SMS notification setup
        $endpoint = 'https://api.tiaraconnect.io/api/messaging/sendsms';
        $apiKey = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiIzNTEiLCJvaWQiOjM1MSwidWlkIjoiN2Y5ZGQ1ZmMtM2QwMi00ZGZiLTg1YjItY2FjMDBlYjU0NDhkIiwiYXBpZCI6MjQxLCJpYXQiOjE3MTExOTQyMTAsImV4cCI6MjA1MTE5NDIxMH0._BW3-yd5JJmAnRsL_trguFXmTLKFmz_a4EAJVmoIk7H66Lpccj3uKiwuTJjgYoxKLU6ZH0EhAC3pkDU2wQcPXQ';
        $from = 'TIARACONECT';
        $message = 'Welcome to E-Medicine, ' . $firstname . '! Your registration was successful. Your login ID is: ' . $doctorId . ' and your password is: ' . $password;

        sendSMS($endpoint, $apiKey, $phone, $from, $message);

        header("Location: registration.php?sms_sent=1");
        exit(); 
    } else {
        $errorMsg = "Error submitting details: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();

function sendSMS($endpoint, $apiKey, $to, $from, $message) {
    $request = [
        'to' => $to,
        'from' => $from,
        'message' => $message
    ];
    $requestBody = json_encode($request);

    error_log("Sending SMS to $to with message: $message");

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $requestBody,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
    ]);

    $response_body = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response_body === false) {
        error_log('cURL Error: ' . curl_error($curl));
    } elseif ($http_status !== 200) {
        error_log('HTTP Error ' . $http_status . ': ' . $response_body);
    } else {
        error_log("SMS sent successfully to $to: " . $response_body);
    }

    curl_close($curl);
}
?>

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
            background: linear-gradient(to right, #74ebd5, #acb6e5); 
        }

        .left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            background-image: url('../resources/images/doc.png'); 
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
            overflow: hidden; 
        }

        .registration-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 123, 255, 0.1); 
            border-radius: 12px;
            z-index: -1; 
        }

        .registration-card h2 {
            margin-bottom: 20px;
            font-size: 1.8rem;
            color: #007bff; 
            position: relative; 
            z-index: 1; 
        }

        .registration-card .icon {
            width: 70px;
            height: 70px;
            margin: 20px auto; 
            position: relative; 
            z-index: 1;
        }

        .registration-card input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            position: relative; 
            z-index: 1; 
        }

        .registration-card input:focus {
            border-color: #007bff; 
            outline: none; 
        }

        .registration-card a {
            display: block;
            margin: 15px 0;
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .registration-card a:hover {
            color: #0056b3; 
        }

        .registration-card button {
            background-color: #007bff; 
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.3s ease; 
            position: relative;
            z-index: 1; 
        }

        .registration-card button:hover {
            background-color: #0056b3; 
            transform: translateY(-2px); 
        }

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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#007bff"><path d="M12 12c2.7 0 5.2-.9 7.2-2.5-1.3-2.7-4.2-4.5-7.2-4.5S6.1 6.8 4.8 9.5C6.8 11.1 9.3 12 12 12zm0 2c-4 0-12 2-12 6v4h24v-4c0-4-8-6-12-6z"/></svg>
            </div>
            <h2>Register for E-Medicine</h2>
            <form action="registration.php" method="post">
                <input type="text" name="fname" placeholder="First Name" required>
                <input type="text" name="lname" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="text" name="specialty" placeholder="Specialty" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>

            <?php 
                if (isset($_GET['sms_sent']) && $_GET['sms_sent'] == '1') {
                    echo "<script>
                        alert('Registration successful! An SMS has been sent to your phone with your login credentials.');
                        window.location.href = 'login.php';
                    </script>";
                } elseif (isset($errorMsg)) {
                    echo "<p style='color: red;'>$errorMsg</p>";
                }
            ?>
            <a href="login.php">Already have an account? Log in</a>
        </div>
    </div>
</body>
</html>
