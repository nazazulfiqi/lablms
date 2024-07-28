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

// Fetch summary data
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($sql_users);
$row_users = $result_users->fetch_assoc();
$total_users = $row_users['total_users'];

$sql_videos = "SELECT COUNT(*) AS total_videos FROM videos";
$result_videos = $conn->query($sql_videos);
$row_videos = $result_videos->fetch_assoc();
$total_videos = $row_videos['total_videos'];

$sql_praktikum = "SELECT COUNT(*) AS total_praktikum FROM praktikum";
$result_praktikum = $conn->query($sql_praktikum);
$row_praktikum = $result_praktikum->fetch_assoc();
$total_praktikum = $row_praktikum['total_praktikum'];

// Close connection
$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Lab SIMI</title>
    <link href="../assets/img/jakarta-logo.png" rel="icon">
    <link href="../assets/img/jakarta-logo.png" rel="apple-touch-icon">
    <link rel="stylesheet" href="assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
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
                        <h5 class="card-title fw-semibold mb-4">Admin Dashboard</h5>

                        <div class="row row-cols-1 row-cols-md-2 g-4">
                            <!-- Card - Total Users -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Users</h5>
                                        <p class="card-text"><?= htmlspecialchars($total_users) ?></p>
                                        <a href="users.php" class="btn btn-primary">View Users</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Card - Total Videos -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Videos</h5>
                                        <p class="card-text"><?= htmlspecialchars($total_videos) ?></p>
                                        <a href="videos.php" class="btn btn-primary">View Videos</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Card - Total Praktikum -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Praktikum</h5>
                                        <p class="card-text"><?= htmlspecialchars($total_praktikum) ?></p>
                                        <a href="praktikum.php" class="btn btn-primary">View Praktikum</a>
                                    </div>
                                </div>
                            </div>
                        </div>
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
