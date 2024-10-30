<?php
session_start();
include '../access/config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$patientEmail = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    // Removed DateOfBirth since we are using PatientID
    $gender = $_POST['gender'];

    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $targetDirectory = "../resources/images/";
        $targetFile = $targetDirectory . basename($_FILES["profileImage"]["name"]);
        $check = getimagesize($_FILES["profileImage"]["tmp_name"]);

        if ($check !== false && move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
            $imagePath = basename($_FILES["profileImage"]["name"]);
            $updateQuery = $conn->prepare(
                "UPDATE patients SET  Phone = ?, Address = ?, Gender = ?, ProfileImage = ? WHERE Email = ?"
            );
            $updateQuery->bind_param("sssss", $phone, $address, $gender, $imagePath, $patientEmail);
        } else {
            $message = "Error uploading your file.";
        }
    } else {
        $updateQuery = $conn->prepare(
            "UPDATE patients SET Email = ?, Phone = ?, Address = ?, Gender = ? WHERE Email = ?"
        );
        $updateQuery->bind_param("sssss", $email, $phone, $address, $gender, $patientEmail);
    }

    if (isset($updateQuery) && $updateQuery->execute()) {
        $message = "Profile updated successfully.";
    } else {
        $message = "Error updating profile.";
    }
}

$profileQuery = $conn->prepare("SELECT * FROM patients WHERE Email = ?");
$profileQuery->bind_param("s", $patientEmail);
$profileQuery->execute();
$patient = $profileQuery->get_result()->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .container-fluid {
            display: flex;
            flex: 1;
        }

        .sidebar {
            flex: 0 0 280px;
        }

        .profile-container {
            flex: 1;
            margin: 50px;
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .image-box {
            width: 280px;
            height: 280px;
            overflow: hidden;
            margin: 0 auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-header h2 {
            margin-top: 15px;
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
        }

        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .profile-info div {
            background-color: #f7f9fc;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .profile-info h5 {
            color: #3498db;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-edit {
            margin-top: 30px;
            text-align: center;
        }

        .profile-edit button {
            background-color: #3498db;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }

        .profile-edit button:hover {
            background-color: #2980b9;
        }

        .alert-info {
            background-color: #eaf4fd;
            color: #2980b9;
            border: 1px solid #3498db;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            background-color: #3498db;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .modal-footer button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .modal-footer button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<?php include '../resources/includes/p_header.php'; ?>
<div class="container-fluid">
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="profile-container">
        <div class="profile-header">
            <div class="image-box">
                <img src="../resources/images/<?php echo htmlspecialchars($patient['ProfileImage']); ?>" alt="Patient Image">
            </div>
            <h2><?php echo htmlspecialchars($patient['firstname'] . ' ' . $patient['lastname']); ?></h2>
        </div>

        <div class="profile-info">
            <div>
                <h5><i class="fas fa-user-id-badge"></i> Patient ID</h5>
                <p><?php echo htmlspecialchars($patient['PatientID']); ?></p>
            </div>
            <div>
                <h5><i class="fas fa-envelope"></i> Email</h5>
                <p><?php echo htmlspecialchars($patient['Email']); ?></p>
            </div>
            <div>
                <h5><i class="fas fa-phone"></i> Phone</h5>
                <p><?php echo htmlspecialchars($patient['Phone']); ?></p>
            </div>
            <div>
                <h5><i class="fas fa-map-marker-alt"></i> Address</h5>
                <p><?php echo htmlspecialchars($patient['Address']); ?></p>
            </div>
            <div>
                <h5><i class="fas fa-venus-mars"></i> Gender</h5>
                <p><?php echo htmlspecialchars($patient['Gender']); ?></p>
            </div>
        </div>
        <div class="profile-edit">
            <button data-toggle="modal" data-target="#editProfileModal">
                <i class="fas fa-edit"></i> Edit Profile
            </button>
        </div>
        <?php if ($message): ?>
            <div class="alert alert-info mt-3"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="profileImage">Profile Image</label>
                        <input type="file" class="form-control" name="profileImage" id="profileImage">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($patient['Email']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($patient['Phone']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($patient['Address']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" name="gender">
                            <option value="Male" <?php echo $patient['Gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $patient['Gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $patient['Gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
