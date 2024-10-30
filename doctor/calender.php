<?php
session_start(); // Start the session

// Include the database configuration
include '../access/config.php';

// Check if the user is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

// Prepare the SQL statement with error handling
$stmt = $conn->prepare("
    SELECT a.AppointmentDate, a.CreatedAt, a.Status, p.firstname 
    FROM appointments a 
    JOIN patients p ON a.patientID = p.patientID 
    WHERE a.doctorID = ?
");

// Check if prepare was successful
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

// Bind parameters and execute
$stmt->bind_param("s", $_SESSION['doctor_id']);

// Check for execution errors
if ($stmt->execute() === false) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Calendar - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .dashboard {
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #007bff;
            padding: 20px;
            height: 100vh;
            color: white;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        #calendar {
            width: 100%; /* Make the calendar take full width */
            max-height: 80vh; /* Limit height for better visibility */
            overflow: auto; /* Allow scrolling if necessary */
        }
        .fc-day.fc-day-today {
            background-color: #e1f5fe; /* Highlight today's date */
        }
        .fc-event {
            background-color: #007bff; /* Event background color */
            color: white; /* Event text color */
            border-radius: 5px; /* Rounded corners for events */
            padding: 5px; /* Space inside event */
        }
    </style>
</head>
<body>

    <?php include '../resources/includes/d_header.php'; ?>

    <div class="dashboard">
        <?php include '../resources/includes/d_sidebar.php'; ?>

        <div class="content">
            <h1 class="text-center my-4">Appointment Calendar</h1>
            <div id='calendar'></div>
        </div>
    </div>

    <?php include '../resources/includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    <?php foreach ($appointments as $appointment): ?>
                        {
                            title: '<?php echo htmlspecialchars($appointment['firstname']); ?>',
                            start: '<?php echo htmlspecialchars($appointment['AppointmentDate']); ?>',
                            description: 'Status: <?php echo htmlspecialchars($appointment['Status']); ?>'
                        },
                    <?php endforeach; ?>
                ],
                eventClick: function(info) {
                    alert('Event: ' + info.event.title + '\nDescription: ' + info.event.extendedProps.description);
                },
                dayRender: function(info) {
                    if (info.date.getTime() === new Date().setHours(0,0,0,0)) {
                        info.el.style.backgroundColor = '#e1f5fe'; // Highlight today's date
                    }
                }
            });

            calendar.render();
        });
    </script>

</body>
</html>
