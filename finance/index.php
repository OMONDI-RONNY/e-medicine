<?php
session_start(); // Start the session
include '../access/config.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['finance_id'])) {
    // User is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Fetch finance statistics
$totalRevenueQuery = "SELECT SUM(Amount) as totalRevenue FROM finance WHERE PaymentStatus = 'Paid'";
$totalExpensesQuery = "SELECT SUM(Amount) as totalExpenses FROM finance WHERE Description = 'Expenses'";
$recentTransactionsQuery = "SELECT PaymentDate, Description, Amount, PaymentStatus FROM finance ORDER BY PaymentDate DESC LIMIT 10";

$totalRevenueResult = $conn->query($totalRevenueQuery);
$totalExpensesResult = $conn->query($totalExpensesQuery);
$recentTransactionsResult = $conn->query($recentTransactionsQuery);

// Calculate total profit
$totalRevenue = $totalRevenueResult->fetch_assoc()['totalRevenue'] ?? 0;
$totalExpenses = $totalExpensesResult->fetch_assoc()['totalExpenses'] ?? 0;
$totalProfit = $totalRevenue - $totalExpenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
            margin: 0;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            width: 220px;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            margin: 10px 0;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057;
            padding-left: 10px;
        }
        .content {
            margin-left: 240px;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .card-header h5 {
            margin: 0;
        }
        .card-body {
            font-size: 1.5em;
        }
        .text-muted {
            font-size: 0.8em;
        }
        .table {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>Finance Dashboard</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-dollar-sign"></i> Total Revenue</h5>
                    </div>
                    <div class="card-body">
                        Kshs.<?php echo number_format($totalRevenue, 2); ?>
                        <div class="text-muted">This Month</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-exclamation-circle"></i> Total Expenses</h5>
                    </div>
                    <div class="card-body">
                        Kshs.<?php echo number_format($totalExpenses, 2); ?>
                        <div class="text-muted">This Month</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line"></i> Total Profit</h5>
                    </div>
                    <div class="card-body">
                        Kshs.<?php echo number_format($totalProfit, 2); ?>
                        <div class="text-muted">This Month</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-table"></i> Recent Transactions</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($transaction = $recentTransactionsResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo date('Y-m-d', strtotime($transaction['PaymentDate'])); ?></td>
                            <td><?php echo htmlspecialchars($transaction['Description']); ?></td>
                            <td>Kshs.<?php echo number_format($transaction['Amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($transaction['PaymentStatus']); ?></td>
                        </tr>
                        <?php } ?>
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

<?php
// Close the database connection
$conn->close();
?>
