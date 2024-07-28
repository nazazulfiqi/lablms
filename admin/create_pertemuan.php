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
    $nama_pertemuan = $_POST['nama_pertemuan'];

    // Prepare and execute SQL statement to insert meeting
    $sql = "INSERT INTO pertemuan (nama_pertemuan) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nama_pertemuan);

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
                        <h5 class="card-title fw-semibold mb-4">Add New Meeting</h5>
                        <form id="add-meeting-form">
                            <div class="mb-3 text-end">
                                <button type="button" class="btn btn-secondary" onclick="window.history.back();">Back</button>
                            </div>
                            <div class="mb-3">
                                <label for="nama_pertemuan" class="form-label">Meeting Name</label>
                                <input type="text" class="form-control" id="nama_pertemuan" name="nama_pertemuan" required>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" id="save-btn">Add Meeting</button>
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
        document.getElementById('add-meeting-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            $.ajax({
                type: 'POST',
                url: '', // Submit to the same PHP file
                data: $('#add-meeting-form').serialize(),
                success: function(response) {
                    let res = JSON.parse(response);
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Meeting added successfully!',
                        });

                        // Reset the form after successful save
                        document.getElementById('add-meeting-form').reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to add meeting: ' + res.message,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to add meeting.',
                    });
                }
            });
        });
    </script>
</body>

</html>
