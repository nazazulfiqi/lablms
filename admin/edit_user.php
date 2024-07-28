<?php
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'ADMIN') {
    // Return error response if not logged in or not an admin
    $response = array(
        'status' => 'error',
        'message' => 'Unauthorized access.'
    );
    echo json_encode($response);
    exit();
}

// Check if user id is provided via GET or POST (depending on how you pass the id)
if (!isset($_GET['id']) && !isset($_POST['id'])) {
    // Return error response if user id is missing
    $response = array(
        'status' => 'error',
        'message' => 'User ID not provided.'
    );
    echo json_encode($response);
    exit();
}

// Sanitize and validate user id (you might need to adjust this based on your form submission method)
$id_user = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$dbname = "dblms"; // Database name for users (change as per your setup)

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Return error response if database connection fails
    $response = array(
        'status' => 'error',
        'message' => 'Connection failed: ' . $conn->connect_error
    );
    echo json_encode($response);
    exit();
}

// Fetch user details from the database
$sql_user = "SELECT id, fname, lname, email, role, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_user);

if (!$stmt) {
    // Return error response if SQL preparation fails
    $response = array(
        'status' => 'error',
        'message' => 'SQL preparation error: ' . $conn->error
    );
    echo json_encode($response);
    exit();
}

$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User found, fetch user details
    $user = $result->fetch_assoc();
} else {
    // No user found with the given id
    $response = array(
        'status' => 'error',
        'message' => 'User not found.'
    );
    echo json_encode($response);
    exit();
}

// Close statement and connection
$stmt->close();
$conn->close();

// Function to upload profile picture
function uploadProfilePicture($userId) {
    $targetDir = "uploads/"; // Directory where images will be stored
    $targetFile = $targetDir . basename($_FILES["profile_picture"]["name"]); // Path to store uploaded file

    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        return "File is not an image.";
    }

    // Check file size
    if ($_FILES["profile_picture"]["size"] > 500000) {
        return "Sorry, your file is too large.";
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }

    // If everything is ok, try to upload file
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
        // File uploaded successfully, update profile picture path in database
        $host = "localhost";
        $username = "root";
        $password = "";
        $dbname = "dblms"; // Database name for users (change as per your setup)

        // Create connection
        $conn = new mysqli($host, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            return "Connection failed: " . $conn->connect_error;
        }

        // Update profile picture path in database
        $sql_update = "UPDATE users SET profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);

        if (!$stmt) {
            return "SQL preparation error: " . $conn->error;
        }

        $updatedFilePath = $targetFile;
        $stmt->bind_param("si", $updatedFilePath, $userId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $uploadMessage = "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Profile picture updated successfully',
                                        showConfirmButton: true,
                                        confirmButtonText: 'OK'
                                    }).then((result) => {
                                        // Reload the page or redirect as needed
                                        location.reload();
                                    });
                                });
                              </script>";
            return $uploadMessage;
        } else {
            return "Failed to update profile picture.";
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    } else {
        return "Sorry, there was an error uploading your file.";
    }
}

// Handle form submission to update user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update user details
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Perform update operation in the database (you need to implement this part)
    // ...

    // Optionally, handle profile picture upload
    if (!empty($_FILES["profile_picture"]["name"])) {
        $uploadResult = uploadProfilePicture($id_user);
        if (strpos($uploadResult, "successfully") !== false) {
            // Profile picture uploaded successfully
            // You can handle success message or redirect here
        } else {
            // Error uploading profile picture
            // You can handle error message here
        }
    }

    // Redirect to user list or show success message
    // header("Location: users.php");
    // exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User - Lab SIMI</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="../assets/img/jakarta-logo.png" rel="icon">
    <link href="../assets/img/jakarta-logo.png" rel="apple-touch-icon">
    <link rel="stylesheet" href="assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<body>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        <?php include("sidebar.php"); ?>
        <!-- Sidebar End -->

        <!-- Main wrapper -->
        <div class="body-wrapper">
            <!-- Header Start -->
            <?php include("header.php"); ?>
            <!-- Header End -->

            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">Edit User</h5>
                        <form id="editUserForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                            <div class="mb-3">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="fname" name="fname"
                                    value="<?= htmlspecialchars($user['fname']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="lname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lname" name="lname"
                                    value="<?= htmlspecialchars($user['lname']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="ADMIN" <?= ($user['role'] === 'ADMIN') ? 'selected' : '' ?>>Admin</option>
                                    <option value="USER" <?= ($user['role'] === 'USER') ? 'selected' : '' ?>>User</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="current_profile_picture" class="form-label">Current Profile Picture</label><br>
                                <?php if (!empty($user['profile_picture'])): ?>
                                    <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Current Profile Picture" class="img-thumbnail" style="max-width: 200px;">
                                <?php else: ?>
                                    <p>No profile picture uploaded.</p>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">New Profile Picture</label>
                                <input class="form-control" type="file" id="profile_picture" name="profile_picture">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="index.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sidebarmenu.js"></script>
    <script src="assets/js/app.min.js"></script>

    <!-- Execute SweetAlert popup if upload message is set -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?= isset($uploadMessage) ? $uploadMessage : ''; ?>
        });
    </script>
</body>

</html>
