<?php
// Start the session
session_start();

// Include the database configuration
include '../access/config.php'; // Your database connection settings

// Check if the user is logged in
if (!isset($_SESSION['doctor_id'])) {
    echo "Doctor ID not set. Redirecting...";
    header("Location: login.php");
    exit();
}

// Initialize variables
$doctorId = $_SESSION['doctor_id'];
$message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $specialty = $_POST['specialty'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname']; // New field for last name

    // Handle image upload
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $targetDirectory = "../resources/images/";
        $targetFile = $targetDirectory . basename($_FILES["profileImage"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profileImage"]["tmp_name"]);
        if ($check !== false) {
            // Move the uploaded file
            if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
                // Update profile with new image path
                $imagePath = basename($_FILES["profileImage"]["name"]);
                $updateQuery = $conn->prepare("UPDATE doctors SET Email = ?, firstname = ?, lastname = ?, Phone = ?, Specialty = ?, ProfileImage = ? WHERE DoctorID = ?");
                $updateQuery->bind_param("ssssssi", $email, $firstname, $lastname, $phone, $specialty, $imagePath, $doctorId);
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $message = "File is not an image.";
        }
    } else {
        // Update profile without changing the image
        $updateQuery = $conn->prepare("UPDATE doctors SET Email = ?, firstname = ?, lastname = ?, Phone = ?, Specialty = ? WHERE DoctorID = ?");
        $updateQuery->bind_param("sssssi", $email, $firstname, $lastname, $phone, $specialty, $doctorId);
    }

    if (isset($updateQuery) && $updateQuery->execute()) {
        $message = "Profile updated successfully.";
    } else {
        $message = "Error updating profile.";
    }
}

// Fetch doctor's profile information
$profileQuery = $conn->prepare("SELECT * FROM doctors WHERE DoctorID = ?");
$profileQuery->bind_param("i", $doctorId);
$profileQuery->execute();
$doctor = $profileQuery->get_result()->fetch_assoc();

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor's Profile - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            min-height: 100vh;
        }

        .container-fluid {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background-color: #f8f9fa;
            padding: 20px;
            min-height: 100%;
        }

        .profile-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            width: 100%;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid #007bff;
        }

        .profile-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-top: 15px;
            color: #333;
        }

        .profile-header p {
            font-size: 18px;
            color: #007bff;
        }

        .profile-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .profile-info div {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }

        .profile-info h5 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #007bff;
            display: flex;
            align-items: center;
        }

        .profile-info h5 i {
            margin-right: 10px;
        }

        .profile-info p {
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        .profile-edit {
            text-align: left;
            margin-top: 30px;
        }

        .profile-edit button {
            padding: 10px 30px;
            font-size: 16px;
            display: flex;
            align-items: center;
        }

        .profile-edit button i {
            margin-right: 10px;
        }

        @media (max-width: 767.98px) {
            .profile-info {
                grid-template-columns: 1fr;
            }
        }

        .image-preview {
            display: block;
            width: 100%;
            height: auto;
            margin-bottom: 10px;
            border: 2px solid #007bff;
        }
    </style>
</head>
<body>

<?php include '../resources/includes/d_header.php'; ?>

<div class="container-fluid">
    <div class="row flex-grow-1">
        <div class="col-md-3">
            <div class="sidebar">
                <?php include '../resources/includes/d_sidebar.php'; ?>
            </div>
        </div>

        <div class="col-md-9 d-flex align-items-start">
            <div class="profile-container">
                <div class="profile-header">
                    <img src="../resources/images/<?php echo htmlspecialchars($doctor['ProfileImage']); ?>" alt="Doctor Profile Picture" class="img-fluid rounded-circle">
                    <h2><?php echo htmlspecialchars($doctor['firstname'] . ' ' . $doctor['lastname']); ?></h2>
                    <p><?php echo htmlspecialchars($doctor['Specialty']); ?></p>
                </div>

                <div class="profile-info">
                    <div>
                        <h5><i class="fas fa-id-badge"></i> Doctor ID</h5>
                        <p><?php echo htmlspecialchars($doctor['DoctorID']); ?></p>
                    </div>
                    <div>
                        <h5><i class="fas fa-envelope"></i> Email</h5>
                        <p><?php echo htmlspecialchars($doctor['Email']); ?></p>
                    </div>
                    <div>
                        <h5><i class="fas fa-phone"></i> Phone</h5>
                        <p><?php echo htmlspecialchars($doctor['Phone']); ?></p>
                    </div>
                    <div>
                        <h5><i class="fas fa-user-md"></i> Specialty</h5>
                        <p><?php echo htmlspecialchars($doctor['Specialty']); ?></p>
                    </div>
                    <div>
                        <h5><i class="fas fa-calendar-alt"></i> Joined On</h5>
                        <p><?php echo htmlspecialchars(date('F j, Y', strtotime($doctor['CreatedAt']))); ?></p>
                    </div>
                </div>

                <div class="profile-edit">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info mt-3"><?php echo $message; ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" class="form-control" name="firstname" value="<?php echo htmlspecialchars($doctor['firstname']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($doctor['lastname']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($doctor['Email']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($doctor['Phone']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="specialty">Specialty</label>
                        <input type="text" class="form-control" name="specialty" value="<?php echo htmlspecialchars($doctor['Specialty']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="profileImage">Profile Image</label>
                        <input type="file" class="form-control" name="profileImage">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
