<?php
session_start(); 

include '../access/config.php'; 




$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $inputUsername = $conn->real_escape_string($_POST['username']);
    $inputPassword = $_POST['password'];

    
    $sql = "SELECT AdminID, Password FROM admins WHERE Username='$inputUsername'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        
        $row = $result->fetch_assoc();

       
        if (password_verify($inputPassword, $row['Password'])) {
           
           
          $_SESSION['username'] = $inputUsername;

         
            header("Location: index.php");
            exit();
        } else {
            
            $error = "Invalid username or password.";
        }
    } else {
        
        $error = "Invalid username or password.";
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url('../resources/images/hospital.jpg');
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .login-container {
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            padding: 30px;
            width: 400px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffffff;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 10px;
            margin-bottom: 15px;
            color: #fff;
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.3);
            color: #fff;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.6);
        }
        .btn-primary {
            background-color: #6c5ce7;
            border: none;
            border-radius: 10px;
            padding: 10px;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #5a54d1;
        }
        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }
        .forgot-password a {
            color: #ffffff;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2><i class="fas fa-user-shield"></i> Admin Login</h2>
        
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login <i class="fas fa-sign-in-alt"></i></button>
        </form>
        <div class="forgot-password">
            <a href="../resources/includes/forgot.php">Forgot Password?</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
