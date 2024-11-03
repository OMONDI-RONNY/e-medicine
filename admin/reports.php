<?php
session_start();
include '../access/config.php';
if (!isset($_SESSION['username'])) {
   
    header("Location: login.php");
    exit; 
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_report'])) {
    $reportType = $_POST['report_type'];

    
    require_once 'tcpdf/tcpdf.php'; 

   
    $pdf = new TCPDF();

   
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('E-Medicine System');
    $pdf->SetTitle($reportType);
    $pdf->SetHeaderData('', 0, 'Report: ' . $reportType, 'Generated on: ' . date('Y-m-d H:i:s'));

   
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->AddPage();

  
    if ($reportType === 'Patient Summary Report') {
       
        $result = $conn->query("SELECT p.PatientID, p.firstname, COUNT(hr.RecordID) AS RecordCount 
                                 FROM patients p 
                                 LEFT JOIN healthrecords hr ON p.PatientID = hr.PatientID 
                                 GROUP BY p.PatientID");
        $html = '<h1>Patient Summary Report</h1>
                 <table border="1" cellpadding="5">
                     <tr>
                         <th>Patient ID</th>
                         <th>Name</th>
                         <th>Number of Health Records</th>
                     </tr>';
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>
                          <td>' . htmlspecialchars($row['PatientID']) . '</td>
                          <td>' . htmlspecialchars($row['firstname']) . '</td>
                          <td>' . htmlspecialchars($row['RecordCount']) . '</td>
                      </tr>';
        }
        $html .= '</table>';
    } elseif ($reportType === 'Appointment Report') {
        
        $result = $conn->query("SELECT * FROM appointments");
        $html = '<h1>Appointment Report</h1>
                 <table border="1" cellpadding="5">
                     <tr>
                         <th>Appointment ID</th>
                         <th>Patient ID</th>
                         <th>Doctor ID</th>
                         <th>Appointment Date</th>
                         <th>Status</th>
                     </tr>';
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>
                          <td>' . htmlspecialchars($row['AppointmentID']) . '</td>
                          <td>' . htmlspecialchars($row['PatientID']) . '</td>
                          <td>' . htmlspecialchars($row['DoctorID']) . '</td>
                          <td>' . date('Y-m-d H:i:s', strtotime($row['AppointmentDate'])) . '</td>
                          <td>' . htmlspecialchars($row['Status']) . '</td>
                      </tr>';
        }
        $html .= '</table>';
    } elseif ($reportType === 'Prescription Report') {
       
        $query = "SELECT pr.PrescriptionID, p.firstname, pr.Instructions, pr.CreatedAt 
                  FROM prescriptions pr 
                  JOIN patients p ON pr.PatientID = p.PatientID";
        
        $result = $conn->query($query);
    
        if (!$result) {
           
            die("Query Error: " . $conn->error);
        }
    
        $html = '<h1>Prescription Report</h1>
                 <table border="1" cellpadding="5">
                     <tr>
                         <th>Prescription ID</th>
                         <th>Patient Name</th>
                         <th>Description</th>
                         <th>Date Created</th>
                     </tr>';
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>
                          <td>' . htmlspecialchars($row['PrescriptionID']) . '</td>
                          <td>' . htmlspecialchars($row['firstname']) . '</td>
                          <td>' . htmlspecialchars($row['Instructions']) . '</td>
                          <td>' . date('Y-m-d H:i:s', strtotime($row['CreatedAt'])) . '</td>
                      </tr>';
        }
        $html .= '</table>';
    }
    

    $pdf->writeHTML($html, true, false, true, false, '');

 
    $fileName = $reportType . '_' . date('YmdHis') . '.pdf';
    $pdf->Output($fileName, 'D'); 
    exit; 
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .navbar {
            background-color: #007bff;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .dashboard {
            display: flex;
        }

        .container {
            padding: 20px;
            margin: 0;
            max-width: 100%;
            flex-grow: 1;
        }

        .page-header h1 {
            font-size: 2rem;
            color: #007bff;
        }

        .reports-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .reports-table th,
        .reports-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .reports-table th {
            background-color: #007bff;
            color: white;
        }

        .btn-generate {
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-right: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-generate:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>
    <div class="dashboard">
        <?php include 'sidebar.php'; ?>

        <div class="container">
            <div class="page-header">
                <h1>Reports</h1>
                <p>Generate and view various reports related to the administration module.</p>
            </div>

            <button class="btn-generate" data-toggle="modal" data-target="#generateReportModal">Generate Report</button>

          

          
            <div class="modal fade" id="generateReportModal" tabindex="-1" role="dialog" aria-labelledby="generateReportModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="generateReportModalLabel">Generate Report</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" action="">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="report_type">Select Report Type</label>
                                    <select class="form-control" id="report_type" name="report_type" required>
                                        <option value="Patient Summary Report">Patient Summary Report</option>
                                        <option value="Appointment Report">Appointment Report</option>
                                        <option value="Prescription Report">Prescription Report</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="generate_report">Generate Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include '../resources/includes/footer.php'; ?> 


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
