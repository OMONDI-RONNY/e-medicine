<?php
session_start(); 


$_SESSION = [];


if (session_id() != '') {
    session_destroy(); 
}


$message = "You have successfully logged out of the admin module. Redirecting to the login page...";
header("refresh:3; url=../index.php"); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f7fa;
        }
        .logout-message {
            text-align: center;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="logout-message">
    <h1><?php echo $message; ?></h1>
    <p>You will be redirected in 3 seconds.</p>
</div>

</body>
</html>
