<?php

include '../access/config.php';

session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

 
    $endpoint = 'https://api.tiaraconnect.io/api/messaging/sendsms';
    $apiKey = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiIzNTEiLCJvaWQiOjM1MSwidWlkIjoiN2Y5ZGQ1ZmMtM2QwMi00ZGZiLTg1YjItY2FjMDBlYjU0NDhkIiwiYXBpZCI6MjQxLCJpYXQiOjE3MTExOTQyMTAsImV4cCI6MjA1MTE5NDIxMH0._BW3-yd5JJmAnRsL_trguFXmTLKFmz_a4EAJVmoIk7H66Lpccj3uKiwuTJjgYoxKLU6ZH0EhAC3pkDU2wQcPXQ';
    $from = 'TIARACONECT';
    $smsMessage = "From: $name\nMessage: $message";

    
    $smsResponse = sendSMS($endpoint, $apiKey, $phone, $from, $smsMessage);

    if ($smsResponse) {
        echo "<script>alert('Message sent successfully!');</script>";
    } else {
        echo "<script>alert('Failed to send message. Please try again.');</script>";
    }
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
        return false;
    } elseif ($http_status !== 200) {
        error_log('HTTP Error ' . $http_status . ': ' . $response_body);
        return false; 
    } else {
        error_log("SMS sent successfully to $to: " . $response_body);
        return true; 
    }

    curl_close($curl);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .dashboard {
            display: flex; 
        }

        .main-content {
            flex: 1; 
            padding: 20px;
        }

        .contact-form {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .contact-form h2 {
            margin-bottom: 20px;
        }

        .contact-form label {
            font-weight: bold;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .contact-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .contact-form button:hover {
            background-color: #0056b3;
        }

        .contact-details {
            margin-top: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .contact-details h3 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="dashboard">
    <?php include '../resources/includes/d_sidebar.php'; ?> 
    
    <div class="main-content"> 
        <h1>Contact Support</h1>
        <div class="contact-form">
            <h2>Get in Touch</h2>
            <form action="" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required>

                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" required></textarea>

                <button type="submit">Send Message</button>
            </form>
        </div>

        
        <div class="contact-details">
            <h3>Contact Details</h3>
            <p><strong>Email:</strong> <a href="mailto:omoron37@mail.com">omoron37@mail.com</a></p>
            <p><strong>Phone:</strong> <a href="tel:+254796471436">+254 796 471 436</a></p>
            <p><strong>Address:</strong> 232, Kombew, Kisumu, Kenya</p>
        </div>
    </div>
</div>

</body>
</html>
