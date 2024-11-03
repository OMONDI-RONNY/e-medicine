<?php
include '../access/config.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

$query = "SELECT `Description`, `Timestamp` FROM `notification` WHERE `DoctorID` = ? ORDER BY `Timestamp` DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

$stmt->close();
$conn->close();
?>

<div class="notification-sidebar">
    <h5>Notifications</h5>
    <?php if (empty($notifications)): ?>
        <div class="notification info">
            <i class="fas fa-info-circle notification-icon"></i>
            <div class="notification-content">
                <h6>No new notifications.</h6>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($notifications as $index => $notification): ?>
            <div class="notification <?php echo $index === 0 ? 'dance' : ''; ?>">
                <i class="fas fa-bell notification-icon"></i>
                <div class="notification-content">
                    <h6><?php echo htmlspecialchars($notification['Description']); ?></h6>
                    <p class="notification-time"><?php echo date('h:i A', strtotime($notification['Timestamp'])); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f8f9fa; /* Light background for contrast */
        margin: 0;
        padding: 0;
    }

    .notification-sidebar {
        width: 400px;
        padding: 20px;
        border-left: 1px solid #e9ecef; 
        background: #ffffff; 
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
        border-radius: 10px;
        position: relative;
        transition: transform 0.3s ease; /* Smooth animation */
    }

    h5 {
        margin-bottom: 20px;
        font-size: 22px; 
        color: #007bff; 
        border-bottom: 2px solid #007bff; 
        padding-bottom: 10px; 
        text-align: center; /* Centered title */
    }

    .notification {
        display: flex; 
        align-items: center; 
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: background-color 0.3s ease, transform 0.3s ease; 
        position: relative;
        overflow: hidden; 
        background-color: #f9f9f9; /* Light background for notifications */
        cursor: pointer; /* Change cursor on hover */
    }

    .notification:hover {
        background-color: #e9ecef; /* Slightly darker on hover */
        transform: scale(1.02); /* Scale effect on hover */
    }

    .notification-icon {
        font-size: 28px; 
        margin-right: 15px;
        flex-shrink: 0; 
        color: #007bff; /* Icon color */
    }

    .notification-content {
        flex: 1;
    }

    .notification-time {
        font-size: 12px; 
        color: #888; 
        margin-top: 5px; 
    }

    /* Dancing effect for the latest notification */
    @keyframes dance {
        0% { transform: translateY(0); }
        25% { transform: translateY(-5px); }
        50% { transform: translateY(5px); }
        75% { transform: translateY(-3px); }
        100% { transform: translateY(0); }
    }

    .dance {
        animation: dance 0.8s ease-in-out infinite; /* Infinite animation */
    }

    .success {
        background-color: rgba(40, 167, 69, 0.1); 
        border-left: 5px solid #28a745; 
    }

    .warning {
        background-color: rgba(255, 193, 7, 0.1);
        border-left: 5px solid #ffc107; 
    }

    .error {
        background-color: rgba(220, 53, 69, 0.1); 
        border-left: 5px solid #dc3545; 
    }

    .info {
        background-color: rgba(23, 162, 184, 0.1); 
        border-left: 5px solid #17a2b8; 
    }

    @media (max-width: 768px) {
        .notification-sidebar {
            display: none; 
        }
    }
</style>
