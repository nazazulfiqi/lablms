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

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pertemuan_old = $_POST['nama_pertemuan_old'];
    $nama_pertemuan_new = $_POST['nama_pertemuan_new'];
    $created_at = $_POST['created_at']; // If you are updating created_at as well

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE pertemuan SET nama_pertemuan = ?, created_at = ? WHERE nama_pertemuan = ?");
    $stmt->bind_param("sss", $nama_pertemuan_new, $created_at, $nama_pertemuan_old);

    // Execute and check for errors
    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect to the pertemuan page
    header("Location: pertemuan.php");
    exit();
} else {
    // Fetch the current pertemuan details to pre-fill the form
    if (isset($_GET['nama_pertemuan'])) {
        $nama_pertemuan = $_GET['nama_pertemuan'];

        $stmt = $conn->prepare("SELECT nama_pertemuan, created_at FROM pertemuan WHERE nama_pertemuan = ?");
        $stmt->bind_param("s", $nama_pertemuan);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nama_pertemuan = $row['nama_pertemuan'];
            $created_at = $row['created_at'];
        } else {
            echo "No record found";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Pertemuan</title>
    <link rel="stylesheet" href="assets/css/styles.min.css" />
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
                        <h2>Edit Pertemuan</h2>
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="nama_pertemuan_old" class="form-label">Old Nama Pertemuan</label>
                                <input type="text" class="form-control" id="nama_pertemuan_old" name="nama_pertemuan_old" value="<?= htmlspecialchars($nama_pertemuan) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="nama_pertemuan_new" class="form-label">New Nama Pertemuan</label>
                                <input type="text" class="form-control" id="nama_pertemuan_new" name="nama_pertemuan_new" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
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
    <script src="assets/libs/simplebar/dist/simplebar.js"></script>
</body>

</html>
