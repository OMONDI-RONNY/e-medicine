<!-- d_notifications.php -->
<div class="notification-sidebar">
    <h5>Notifications</h5>
    <div class="notification success">
        <i class="fas fa-check-circle notification-icon"></i>
        <div class="notification-content">
            <h6>Prescription for John Doe issued successfully.</h6>
            <p class="notification-time">Just now</p>
        </div>
    </div>
    <div class="notification warning">
        <i class="fas fa-exclamation-circle notification-icon"></i>
        <div class="notification-content">
            <h6>New prescription for Jane Smith is pending review.</h6>
            <p class="notification-time">5 minutes ago</p>
        </div>
    </div>
    <div class="notification error">
        <i class="fas fa-times-circle notification-icon"></i>
        <div class="notification-content">
            <h6>Failed to update the patient record.</h6>
            <p class="notification-time">10 minutes ago</p>
        </div>
    </div>
    <div class="notification info">
        <i class="fas fa-info-circle notification-icon"></i>
        <div class="notification-content">
            <h6>Your account settings have been updated.</h6>
            <p class="notification-time">15 minutes ago</p>
        </div>
    </div>
</div>

<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    body {
        font-family: 'Roboto', sans-serif;
    }

    .notification-sidebar {
        width: 400px; /* Fixed width for the sidebar */
        padding: 20px;
        border-left: 1px solid #e9ecef; /* Border to separate sidebar */
        background: #ffffff; /* White background for the sidebar */
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1); /* Shadow for sidebar */
        margin-top: 30px;
        border-radius: 8px; /* Rounded corners */
        position: relative; /* For positioning child elements */
    }

    h5 {
        margin-bottom: 20px; /* Space below header */
        font-size: 20px; /* Font size for header */
        color: #007bff; /* Blue color */
        border-bottom: 2px solid #007bff; /* Underline effect */
        padding-bottom: 10px; /* Space below header text */
    }

    .notification {
        display: flex; /* Flexbox for icon and content */
        align-items: center; /* Center items vertically */
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: background-color 0.3s ease, transform 0.3s ease; /* Smooth transition */
        position: relative; /* For pseudo-elements */
        overflow: hidden; /* Ensures the border radius applies */
    }

    .notification-icon {
        font-size: 28px; /* Increased icon size */
        margin-right: 15px; /* Space between icon and text */
        flex-shrink: 0; /* Prevents icon from shrinking */
    }

    .notification-content {
        flex: 1; /* Take up remaining space */
    }

    .notification-time {
        font-size: 12px; /* Smaller font for time */
        color: #888; /* Gray color */
        margin-top: 5px; /* Space above time */
    }

    /* Specific styles for each notification type */
    .success {
        background-color: rgba(40, 167, 69, 0.1); /* Light green background */
        border-left: 5px solid #28a745; /* Green border */
    }

    .warning {
        background-color: rgba(255, 193, 7, 0.1); /* Light yellow background */
        border-left: 5px solid #ffc107; /* Yellow border */
    }

    .error {
        background-color: rgba(220, 53, 69, 0.1); /* Light red background */
        border-left: 5px solid #dc3545; /* Red border */
    }

    .info {
        background-color: rgba(23, 162, 184, 0.1); /* Light blue background */
        border-left: 5px solid #17a2b8; /* Blue border */
    }

    @media (max-width: 768px) {
        .notification-sidebar {
            display: none; /* Hide sidebar on smaller screens */
        }
    }
</style>
