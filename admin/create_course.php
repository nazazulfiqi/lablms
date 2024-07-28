<?php
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../login.php");
    exit();
}

// Database connection
include("../config/connection.php");

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_praktikum = $_POST['nama_praktikum'];
    $deskripsi_praktikum = $_POST['deskripsi_praktikum'];

    // Prepare and execute SQL statement to insert course
    $sql = "INSERT INTO praktikum (nama_praktikum, deskripsi_praktikum) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nama_praktikum, $deskripsi_praktikum);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $stmt->close();
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lab SIMI</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="../assets/img/jakarta-logo.png" rel="icon">
    <link href="../assets/img/jakarta-logo.png" rel="apple-touch-icon">
    <link rel="stylesheet" href="assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .card-img-top {
            width: 286px; /* Fixed width */
            height: 180px; /* Fixed height */
            object-fit: cover; /* Ensure the image covers the entire space */
        }
    </style>
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
                        <h5 class="card-title fw-semibold mb-4">Add New Course</h5>
                        <form id="add-course-form">
                            <div class="mb-3 text-end">
                                <button type="button" class="btn btn-secondary" onclick="window.history.back();">Back</button>
                            </div>
                            <div class="mb-3">
                                <label for="nama_praktikum" class="form-label">Course Name</label>
                                <input type="text" class="form-control" id="nama_praktikum" name="nama_praktikum" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi_praktikum" class="form-label">Course Description</label>
                                <textarea class="form-control" id="deskripsi_praktikum" name="deskripsi_praktikum" rows="3" required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" id="save-btn">Add Course</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sidebarmenu.js"></script>
    <script src="assets/js/app.min.js"></script>
    <script src="assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('add-course-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            $.ajax({
                type: 'POST',
                url: '', // Submit to the same PHP file
                data: $('#add-course-form').serialize(),
                success: function(response) {
                    let res = JSON.parse(response);
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Course added successfully!',
                        });

                        // Reset the form after successful save
                        document.getElementById('add-course-form').reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to add course: ' + res.message,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to add course.',
                    });
                }
            });
        });
    </script>
</body>

</html>
