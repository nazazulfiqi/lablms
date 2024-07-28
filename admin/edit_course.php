<?php
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'ADMIN') {
    // Redirect to login page if not logged in or not an admin
    header("Location: ../login.php");
    exit();
}

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$dbname = "dblms"; // Update this with your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch course data to be edited
$nama_praktikum = null;
if (isset($_GET['nama_praktikum'])) {
    $nama_praktikum = $_GET['nama_praktikum'];
}

if ($nama_praktikum) {
    // Fetch course data
    $sql = "SELECT nama_praktikum, deskripsi_praktikum FROM praktikum WHERE nama_praktikum=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in SQL query: " . $conn->error);
    }

    $stmt->bind_param("s", $nama_praktikum);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();

    if (!$course) {
        echo "Invalid course name";
        exit();
    }
} else {
    echo "Invalid course name";
    exit();
}

// Process form submission
$update_success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_praktikum = $_POST['nama_praktikum'];
    $deskripsi_praktikum = $_POST['deskripsi_praktikum'];

    // Update course data
    $sql_update = "UPDATE praktikum SET nama_praktikum=?, deskripsi_praktikum=? WHERE nama_praktikum=?";
    $stmt_update = $conn->prepare($sql_update);

    if (!$stmt_update) {
        die("Error in SQL update query: " . $conn->error);
    }

    $stmt_update->bind_param("sss", $nama_praktikum, $deskripsi_praktikum, $_GET['nama_praktikum']);

    if ($stmt_update->execute()) {
        $update_success = true;
    } else {
        echo "Error updating course: " . $conn->error;
    }

    $stmt_update->close();
}

$stmt->close();
$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Course</title>
    <link href="../assets/img/jakarta-logo.png" rel="icon">
    <link href="../assets/img/jakarta-logo.png" rel="apple-touch-icon">
    <link rel="stylesheet" href="assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script untuk tombol back -->
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
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
                        <h5 class="card-title fw-semibold mb-4">Edit Course</h5>

                        <?php if ($update_success): ?>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Course updated successfully',
                                        showConfirmButton: true,
                                        confirmButtonText: 'OK'
                                    }).then(function() {
                                        // Optional: You can reload the page here if needed
                                        // location.reload();
                                    });
                                });
                            </script>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="nama_praktikum" class="form-label">Course Name</label>
                                <input type="text" class="form-control" id="nama_praktikum" name="nama_praktikum" value="<?= htmlspecialchars($course['nama_praktikum']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi_praktikum" class="form-label">Course Description</label>
                                <textarea class="form-control" id="deskripsi_praktikum" name="deskripsi_praktikum" rows="3" required><?= htmlspecialchars($course['deskripsi_praktikum']) ?></textarea>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="goBack();">Back</button>
                                <button type="submit" class="btn btn-primary">Update Course</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
