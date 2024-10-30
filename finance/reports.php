<?php
session_start();
include '../access/config.php'; // Database connection
require_once('tcpdf/tcpdf.php'); // Include TCPDF library

// Check if the user is logged in
if (!isset($_SESSION['finance_id'])) {
    header("Location: login.php");
    exit();
}

// Handle report generation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get report type from POST request
    $reportType = isset($_POST['report_type']) ? $_POST['report_type'] : '';

    // Initialize PDF
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Company');
    $pdf->SetTitle('Financial Report');
    $pdf->SetHeaderData('', 0, 'Financial Report', 'Generated on: ' . date('Y-m-d'));
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();

    // Prepare report data based on type
    if ($reportType == 'monthly') {
        // Monthly report query
        $query = "SELECT f.PaymentDate, f.Amount, p.firstname, p.lastname FROM finance f 
                  JOIN patients p ON f.PatientID = p.PatientID 
                  WHERE MONTH(f.PaymentDate) = MONTH(CURRENT_DATE()) 
                  AND YEAR(f.PaymentDate) = YEAR(CURRENT_DATE())";
        $reportTitle = 'Monthly Revenue Report';
    } elseif ($reportType == 'annual') {
        // Annual report query
        $query = "SELECT f.PaymentDate, f.Amount, p.firstname, p.lastname FROM finance f 
                  JOIN patients p ON f.PatientID = p.PatientID 
                  WHERE YEAR(f.PaymentDate) = YEAR(CURRENT_DATE())";
        $reportTitle = 'Annual Expense Report';
    } elseif ($reportType == 'billing') {
        // Patient billing report query
        $query = "SELECT f.PaymentDate, f.Amount, p.firstname, p.lastname FROM finance f 
                  JOIN patients p ON f.PatientID = p.PatientID";
        $reportTitle = 'Patient Billing Summary';
    } else {
        // Invalid report type
        die('Invalid report type');
    }

    // Execute the query
    $result = $conn->query($query);

    // Check if there are results
    if ($result && $result->num_rows > 0) {
        $html = '<h2>' . $reportTitle . '</h2>';
        $html .= '<table border="1" cellpadding="4">
                    <tr>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Patient Name</th>
                    </tr>';
        
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($row['PaymentDate']) . '</td>
                        <td>' . htmlspecialchars($row['Amount']) . '</td>
                        <td>' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . '</td>
                      </tr>';
        }

        $html .= '</table>';
    } else {
        $html = '<h2>No records found for this report.</h2>';
    }

    // Write HTML content to PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('financial_report.pdf', 'I');
    exit; // Stop further execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Reports</title>
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
        <h1>Financial Reports</h1>
        
        <div class="mb-3">
            <h4>Generate Report</h4>
            <form action="reports.php" method="POST">
                <div class="form-group">
                    <label for="report_type">Select Report Type:</label>
                    <select name="report_type" id="report_type" class="form-control" required>
                        <option value="monthly">Monthly Revenue Report</option>
                        <option value="annual">Annual Expense Report</option>
                        <option value="billing">Patient Billing Summary</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Generate Report</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-alt"></i> Report Records</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Report ID</th>
                            <th>Report Title</th>
                            <th>Generated Date</th>
                            <th>Generated By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>REP001</td>
                            <td>Monthly Revenue Report</td>
                            <td>2024-10-01</td>
                            <td>Admin</td>
                            <td>
                                <a href="view_report.php?id=REP001" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a>
                                <a href="edit_report.php?id=REP001" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                <a href="download_report.php?id=REP001" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Download</a>
                            </td>
                        </tr>
                        <tr>
                            <td>REP002</td>
                            <td>Annual Expense Report</td>
                            <td>2024-10-05</td>
                            <td>Admin</td>
                            <td>
                                <a href="view_report.php?id=REP002" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a>
                                <a href="edit_report.php?id=REP002" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                <a href="download_report.php?id=REP002" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Download</a>
                            </td>
                        </tr>
                        <tr>
                            <td>REP003</td>
                            <td>Patient Billing Summary</td>
                            <td>2024-10-10</td>
                            <td>Admin</td>
                            <td>
                                <a href="view_report.php?id=REP003" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a>
                                <a href="edit_report.php?id=REP003" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                <a href="download_report.php?id=REP003" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Download</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-3">
            <a href="generate_report.php" class="btn btn-primary"><i class="fas fa-plus"></i> Generate New Report</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
