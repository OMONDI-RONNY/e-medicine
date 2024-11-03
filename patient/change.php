<?php
session_start();
include '../access/config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = $_SESSION['user_id'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $errorMsg = "New passwords do not match.";
    } else {
        
        $stmt = $conn->prepare("SELECT password FROM patients WHERE Email = ?");
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        $stmt->bind_result($existingPassword);
        $stmt->fetch();
        $stmt->close();

        
        if ($currentPassword === $existingPassword) {
            
            $stmt = $conn->prepare("UPDATE patients SET password = ? WHERE Email = ?");
            $stmt->bind_param('si', $newPassword, $patientId);

            if ($stmt->execute()) {
                $successMsg = "Password changed successfully.";
            } else {
                $errorMsg = "Error changing password: " . $conn->error;
            }
            $stmt->close();
        } else {
            $errorMsg = "Current password is incorrect.";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
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

        .change-password-form {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .change-password-form h2 {
            margin-bottom: 20px;
        }

        .change-password-form label {
            font-weight: bold;
        }

        .change-password-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .change-password-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .change-password-form button:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>


<?php include '../resources/includes/d_header.php'; ?>

<div class="dashboard"> 
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content"> 
        <h1>Change Password</h1>
        <div class="change-password-form">
            <form action="" method="POST"> 
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>

                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit">Change Password</button>
            </form>

            <?php if (isset($errorMsg)): ?>
                <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
            <?php elseif (isset($successMsg)): ?>
                <div class="alert alert-success"><?php echo $successMsg; ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../resources/includes/footer.php'; ?>

</body>
</html>
