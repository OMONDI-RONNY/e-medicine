<?php
session_start();
include '../../access/config.php'; 

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = trim($_POST['phone']);
    $token = trim($_POST['token']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!empty($phone) && !empty($token) && !empty($new_password) && !empty($confirm_password)) {
        
        $tables = ["patients", "admins", "doctors"];
        $found = false;
        $found_table = ""; 

        if ($new_password !== $confirm_password) {
            $error_message = "Passwords do not match.";
        } else {
            foreach ($tables as $table) {
                $stmt = $conn->prepare("SELECT * FROM $table WHERE phone = ? AND reset_token = ?");
                $stmt->bind_param("ss", $phone, $token);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    
                    $stmt = $conn->prepare("UPDATE $table SET password = ?, reset_token = NULL WHERE phone = ?");
                    $stmt->bind_param("ss", $new_password, $phone);
                    $stmt->execute();

                    $success_message = "Your password has been updated successfully!";
                    $found = true;
                    $found_table = $table; 
                    break;
                }
            }

            if (!$found) {
                $error_message = "Invalid phone number or token.";
            } else {
                
                switch ($found_table) {
                    case "patients":
                        header("Location: ../../patient/login.php");
                        break;
                    case "admins":
                        header("Location: ../../admin/login.php");
                        break;
                    case "doctors":
                        header("Location: ../../doctor/login.php");
                        break;
                }
                exit();
            }

            $stmt->close();
        }
    } else {
        $error_message = "Please fill in all fields.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - E-Medicine</title>
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
        .reset-password-card {
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
        .reset-password-card h2 {
            margin-bottom: 20px;
            color: #007bff;
        }
        .reset-password-card input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .reset-password-card input:focus {
            border-color: #007bff;
            outline: none;
        }
        .reset-password-card button {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        .reset-password-card button:hover {
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
    <script>
        function validatePassword() {
            const newPassword = document.querySelector('input[name="new_password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            const errorMessage = document.getElementById('password-mismatch-message');
            
            if (newPassword !== confirmPassword) {
                errorMessage.textContent = "Passwords do not match.";
            } else {
                errorMessage.textContent = "";
            }
        }
    </script>
</head>
<body>
    <div class="reset-password-card">
        <h2>Reset Password</h2>
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form action="reset.php" method="post">
            <input type="text" name="phone" placeholder="Enter your phone number" required>
            <input type="text" name="token" placeholder="Enter the reset token" required>
            <input type="password" name="new_password" placeholder="Enter new password" required oninput="validatePassword()">
            <input type="password" name="confirm_password" placeholder="Confirm new password" required oninput="validatePassword()">
            <div id="password-mismatch-message" class="error"></div>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
