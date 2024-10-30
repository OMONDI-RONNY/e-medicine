<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Webpage</title>
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* Sidebar Styling */
        .sidebar {
            background-color: #007bff; /* Blue background */
            padding: 20px; /* Padding around the sidebar */
            margin-right: 20px; /* Space between sidebar and main content */
            flex: 0 0 250px; /* Increase Sidebar width to 300px */
            height: auto; /* Allow height to adjust based on content */
            transition: transform 0.3s ease; /* Smooth transition for sidebar */
            margin-top: 20px;
            margin-bottom: 10px;
        }

        /* Sidebar Title */
        .sidebar-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: white;
        }

        /* Remove background color from list items */
        .list-group-item {
            background-color: transparent;
            border: none;
            padding: 10px 0;
        }

        .list-group-item a {
            color: white;
            text-decoration: none;
            display: block;
        }

        /* Hover effect for links */
        .list-group-item a:hover {
            color: #cce7ff;
        }

        /* Sidebar Toggle Button (visible only on smaller screens) */
        .sidebar-toggle {
            display: none; /* Hidden on larger screens */
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 1.5rem;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Hide the sidebar on smaller screens */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 250px; /* Keep this as desired for mobile */
                height: 100vh;
                transform: translateX(-100%);
                z-index: 1000; /* Make sure the sidebar stays above the content */
            }

            /* Sidebar is visible when toggled */
            .sidebar.active {
                transform: translateX(0);
            }

            /* Show the toggle button */
            .sidebar-toggle {
                display: none; /* Show on mobile */
            }

            /* Adjust padding to avoid overlapping */
            .container {
                padding-top: 70px;
            }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <h2 class="sidebar-title">Dashboard</h2>
    <ul class="list-group">
        <li class="list-group-item"><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li class="list-group-item"><a href="patientdata.php"><i class="fas fa-user-injured"></i> Patients</a></li>
        <li class="list-group-item"><a href="appointment.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
        <li class="list-group-item"><a href="calender.php"><i class="fas fa-calendar"></i> Timetable</a></li>

        <li class="list-group-item"><a href="prescription.php"><i class="fas fa-file-prescription"></i> Prescription</a></li>
        <li class="list-group-item"><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>

        <li class="list-group-item"><a href="#"><i class="fas fa-headset"></i> Contact Support</a></li>
    </ul>
</div>

<!-- Toggle Button for Mobile -->
<button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

<script>
    // Toggle Sidebar visibility on smaller screens
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }
</script>

</body>
</html>
