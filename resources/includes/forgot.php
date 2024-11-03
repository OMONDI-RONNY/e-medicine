<?php
session_start();

$error_message = "";
$success_message = "";
$phone = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../../access/config.php';

    $phone = $_POST['phone']; 

    
    function generateToken() {
        return str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    
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

    if (!empty($phone)) {
        
        $reset_token = generateToken();

        
        $tables = ["patients", "admins", "doctors"];
        $found = false;

        foreach ($tables as $table) {
            $stmt = $conn->prepare("SELECT * FROM $table WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
              
                $stmt = $conn->prepare("UPDATE $table SET reset_token = ? WHERE phone = ?");
                $stmt->bind_param("ss", $reset_token, $phone);
                if ($stmt->execute()) {
                    
                    $endpoint = 'https://api.tiaraconnect.io/api/messaging/sendsms';
                    $apiKey = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiIzNTEiLCJvaWQiOjM1MSwidWlkIjoiN2Y5ZGQ1ZmMtM2QwMi00ZGZiLTg1YjItY2FjMDBlYjU0NDhkIiwiYXBpZCI6MjQxLCJpYXQiOjE3MTExOTQyMTAsImV4cCI6MjA1MTE5NDIxMH0._BW3-yd5JJmAnRsL_trguFXmTLKFmz_a4EAJVmoIk7H66Lpccj3uKiwuTJjgYoxKLU6ZH0EhAC3pkDU2wQcPXQ';
                    $from = 'TIARACONECT';
                    $message = "Your password reset token is: $reset_token";

                    sendSMS($endpoint, $apiKey, $phone, $from, $message); 

                    $success_message = "A reset token has been sent to your phone.";
                    $found = true;
                }
                break;
            }
        }

        if (!$found) {
            $error_message = "User not registered with this phone number.";
        } else {
           
            echo "<script>
                    alert('A reset token has been sent to your phone.');
                    window.location.href = 'reset.php'; // Redirect without query parameters
                  </script>";
            exit();
        }

        $stmt->close();
    } else {
        $error_message = "Please enter your phone number.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - E-Medicine</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
       
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            justify-content: center;
            align-items: center;
        }
        .forgot-password-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            text-align: center;
            max-width: 400px;
            width: 100%;
            position: relative;
            overflow: hidden;
            animation: slideIn 0.5s ease-in-out;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .forgot-password-card h2 {
            margin-bottom: 20px;
            color: #007bff;
        }
        .forgot-password-card input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .forgot-password-card input:focus {
            border-color: #007bff;
            outline: none;
        }
        .forgot-password-card button {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        .forgot-password-card button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
        .success {
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="forgot-password-card">
        <h2>Forgot Password</h2>
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form action="forgot.php" method="post">
            <input type="text" name="phone" placeholder="Enter your phone number" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <a href="../../index.php">Back to Login</a>
    </div>
</body>
</html>
