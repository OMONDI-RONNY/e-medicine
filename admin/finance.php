<?php
session_start();
include '../access/config.php'; // Include the database configuration
if (!isset($_SESSION['username'])) {
    // Admin is not logged in, redirect to login page
    header("Location: login.php");
    exit; // Ensure no further code is executed
}

// Fetch finance data
function fetchFinanceData($conn) {
    $query = "SELECT FinanceID, PatientID, Amount, Description, PaymentStatus, PaymentDate FROM finance";
    $result = mysqli_query($conn, $query);

    $financeData = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $financeData[] = $row;
        }
    }
    return $financeData;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['FinanceID'];
    $amount = $_POST['Amount'];
    $status = $_POST['PaymentStatus'];
    
    $query = "UPDATE finance SET Amount = ?, PaymentStatus = ? WHERE FinanceID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "dsi", $amount, $status, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    exit;
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['FinanceID'];
    
    $query = "DELETE FROM finance WHERE FinanceID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    exit;
}

$financeData = fetchFinanceData($conn);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Management - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f5f7fa; color: #333; }
        .navbar { background-color: #007bff; }
        .navbar-brand, .nav-link { color: white !important; }
        .dashboard { display: flex; }
        
        .container {
    padding: 20px;
    flex-grow: 1;
    margin-left: -20px; /* Adjust this to move the container closer to the sidebar */
}
        .page-header h1 { font-size: 2rem; color: #007bff; }
        .page-header p { font-size: 1.1rem; color: #666; }
        .finance-table table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .finance-table th, .finance-table td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        .finance-table th { background-color: #007bff; color: white; }
        .btn-edit, .btn-delete { padding: 6px 12px; border-radius: 4px; font-size: 0.9rem; margin-right: 5px; cursor: pointer; transition: background 0.3s; }
        .btn-edit { background-color: #ffc107; color: white; }
        .btn-edit:hover { background-color: #e0a800; }
        .btn-delete { background-color: #dc3545; color: white; }
        .btn-delete:hover { background-color: #c82333; }
        @media (max-width: 768px) { .finance-table table { font-size: 0.9rem; } }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="dashboard">
        <?php include 'sidebar.php'; ?>
        
        <div class="container">
            <div class="page-header">
                <h1>Finance Management</h1>
                <p>Handle billing and invoicing processes efficiently.</p>
            </div>

            <div class="finance-table">
                <table>
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Payment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($financeData as $row): ?>
                        <tr data-id="<?php echo $row['FinanceID']; ?>">
                            <td><?php echo htmlspecialchars($row['PatientID']); ?></td>
                            <td><?php echo htmlspecialchars($row['Amount']); ?></td>
                            <td><?php echo htmlspecialchars($row['Description']); ?></td>
                            <td><span class="badge <?php echo ($row['PaymentStatus'] == 'Paid') ? 'badge-success' : 'badge-danger'; ?>"><?php echo htmlspecialchars($row['PaymentStatus']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['PaymentDate']); ?></td>
                            <td>
                                <button class="btn-edit" onclick="editEntry(<?php echo $row['FinanceID']; ?>)">Edit</button>
                                <button class="btn-delete" onclick="deleteEntry(<?php echo $row['FinanceID']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include '../resources/includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function editEntry(id) {
            const amount = prompt("Enter new amount:");
            const status = prompt("Enter new status (Paid or Unpaid):");
            if (amount && status) {
                $.post('finance.php', { action: 'update', FinanceID: id, Amount: amount, PaymentStatus: status }, function() {
                    location.reload();
                });
            }
        }

        function deleteEntry(id) {
            if (confirm("Are you sure you want to delete this record?")) {
                $.post('finance.php', { action: 'delete', FinanceID: id }, function() {
                    location.reload();
                });
            }
        }
    </script>
</body>

</html>
