<?php
session_start(); // Start the session
include '../access/config.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['finance_id'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Update payment status if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['invoice'])) {
    $invoice = $_POST['invoice'];
    $status = 'Paid'; // Set status to Paid when the Pay button is clicked

    // Update the finance table
    $updateQuery = "UPDATE finance SET PaymentStatus = '$status' WHERE FinanceID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $invoice);

    if ($stmt->execute()) {
        $message = "Payment status updated successfully!";
    } else {
        $message = "Error updating payment status: " . $conn->error;
    }

    $stmt->close();
}

// Fetch billing records by joining finance and patients tables
$query = "
    SELECT f.FinanceID, f.Amount, f.PaymentStatus, f.CreatedAt, p.firstname, p.lastname 
    FROM finance f
    JOIN patients p ON f.PatientID = p.PatientID
";
$result = $conn->query($query); // Execute the query

// Check for errors
if (!$result) {
    die("Error fetching billing records: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Management</title>
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
        <h1>Billing Management</h1>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-invoice-dollar"></i> Billing Records</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Invoice No.</th>
                            <th>Patient Name</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through each billing record and display it in the table
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $invoiceNo = $row['FinanceID'];
                                $patientName = $row['firstname'] . ' ' . $row['lastname'];
                                $date = date('Y-m-d', strtotime($row['CreatedAt']));
                                $amount = $row['Amount'];
                                $status = $row['PaymentStatus'];

                                echo "<tr>
                                        <td>#00$invoiceNo</td>
                                        <td>$patientName</td>
                                        <td>$date</td>
                                        <td>Kshs.$amount</td>
                                        <td>$status</td>
                                        <td>
                                            <button class='btn btn-info btn-sm view-btn' data-toggle='modal' data-target='#viewModal' data-invoice='$invoiceNo' data-patient='$patientName' data-date='$date' data-amount='$amount' data-status='$status'><i class='fas fa-eye'></i> View</button>";

                                if ($status !== 'Paid') {
                                    echo "<button class='btn btn-success btn-sm pay-btn' data-toggle='modal' data-target='#payModal' data-invoice='$invoiceNo'>Kshs. Pay</button>";
                                }

                                echo "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No billing records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">Billing Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Invoice No:</strong> <span id="viewInvoice"></span></p>
                    <p><strong>Patient Name:</strong> <span id="viewPatient"></span></p>
                    <p><strong>Date:</strong> <span id="viewDate"></span></p>
                    <p><strong>Amount:</strong> $<span id="viewAmount"></span></p>
                    <p><strong>Status:</strong> <span id="viewStatus"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pay Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="payModalLabel">Confirm Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="payInvoice" name="invoice">
                        <p>Are you sure you want to mark this invoice as paid?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Confirm</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Handle view modal population
        $('.view-btn').on('click', function() {
            var invoice = $(this).data('invoice');
            var patient = $(this).data('patient');
            var date = $(this).data('date');
            var amount = $(this).data('amount');
            var status = $(this).data('status');

            $('#viewInvoice').text(invoice);
            $('#viewPatient').text(patient);
            $('#viewDate').text(date);
            $('#viewAmount').text(amount);
            $('#viewStatus').text(status);
        });

        // Handle pay modal population
        $('.pay-btn').on('click', function() {
            var invoice = $(this).data('invoice');
            $('#payInvoice').val(invoice);
        });
    </script>
</body>
</html>
