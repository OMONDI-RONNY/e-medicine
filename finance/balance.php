<?php
session_start(); // Start the session
include '../access/config.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['finance_id'])) {
    // User is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Fetch patient balances and information
$patientBalances = [];
$query = "
    SELECT patients.PatientID, CONCAT(patients.firstname, ' ', patients.lastname) AS PatientName, 
           finance.Amount AS OutstandingBalance, patients.status AS PatientStatus
    FROM patients
    LEFT JOIN finance ON patients.PatientID = finance.PatientID
    WHERE finance.PaymentStatus = 'Unpaid'
";

$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $patientBalances[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Balances</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
            margin: 0;
        }
        .content {
            margin-left: 250px; 
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?> 

    <div class="content">
        <h1>Patient Balances</h1>
        
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-injured"></i> Patient Balance Records</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th>Outstanding Balance</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($patientBalances)) : ?>
                            <?php foreach ($patientBalances as $balance) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($balance['PatientID']); ?></td>
                                    <td><?php echo htmlspecialchars($balance['PatientName']); ?></td>
                                    <td>$<?php echo htmlspecialchars($balance['OutstandingBalance']); ?></td>
                                    <td><?php echo htmlspecialchars($balance['PatientStatus']); ?></td>
                                    <td>
                                        <a href="view_balance.php?id=<?php echo urlencode($balance['PatientID']); ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a>
                                        <a href="edit_balance.php?id=<?php echo urlencode($balance['PatientID']); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center">No records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
