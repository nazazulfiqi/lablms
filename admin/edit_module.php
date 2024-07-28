<?php
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../login.php");
    exit();
}

// Database connection
include("../config/connection.php");

// Retrieve module data
$module_id = $_GET['id'];
$sql_module = "SELECT * FROM modules WHERE id = ?";
$stmt_module = $conn->prepare($sql_module);
$stmt_module->bind_param("i", $module_id);
$stmt_module->execute();
$result_module = $stmt_module->get_result();
$module = $result_module->fetch_assoc();

// Query Data
$data_courses = mysqli_query($conn, "SELECT nama_praktikum FROM praktikum");
$data_meetings = mysqli_query($conn, "SELECT nama_pertemuan FROM pertemuan");

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_praktikum = $_POST['nama_praktikum'];
    $nama_pertemuan = $_POST['nama_pertemuan'];
    $deskripsi = $_POST['deskripsi'];
    $link = $_POST['link'];
    $file_name = $_POST['file_name'];

    // Handle file upload
    $upload_dir = 'uploads/modules/'; // Fixed path
    $file_upload = $_FILES['file_upload'];
    $file_upload_name = basename($file_upload['name']);
    $file_upload_path = $upload_dir . $file_upload_name;

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
    }

    if ($file_upload['size'] > 0) {
        // Upload new file if a new file is provided
        if (move_uploaded_file($file_upload['tmp_name'], $file_upload_path)) {
            $sql = "UPDATE modules SET nama_praktikum=?, nama_pertemuan=?, deskripsi=?, file_upload=?, link=?, file_name=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $nama_praktikum, $nama_pertemuan, $deskripsi, $file_upload_name, $link, $file_name, $module_id);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
            exit();
        }
    } else {
        // Update without changing the file
        $sql = "UPDATE modules SET nama_praktikum=?, nama_pertemuan=?, deskripsi=?, link=?, file_name=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nama_praktikum, $nama_pertemuan, $deskripsi, $link, $file_name, $module_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update data in the database.']);
    }
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
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
    .card-img-top {
        width: 286px;
        /* Fixed width */
        height: 180px;
        /* Fixed height */
        object-fit: cover;
        /* Ensure the image covers the entire space */
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
                        <h5 class="card-title fw-semibold mb-4">Edit Module</h5>
                        <form id="edit-module-form" method="post" action="" enctype="multipart/form-data">
                            <div class="mb-3 text-end">
                                <button type="button" class="btn btn-secondary"
                                    onclick="window.history.back();">Back</button>
                            </div>
                            <div class="mb-3">
                                <label for="nama_praktikum" class="form-label">Course Name</label>
                                <select class="form-select" name="nama_praktikum" aria-label="Default select example">
                                    <?php
                                    while ($row = mysqli_fetch_assoc($data_courses)) {
                                        $selected = $row['nama_praktikum'] == $module['nama_praktikum'] ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($row['nama_praktikum']) . '" ' . $selected . '>' . htmlspecialchars($row['nama_praktikum']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nama_pertemuan" class="form-label">Meeting Name</label>
                                <select class="form-select" name="nama_pertemuan" aria-label="Default select example">
                                    <?php
                                    while ($row = mysqli_fetch_assoc($data_meetings)) {
                                        $selected = $row['nama_pertemuan'] == $module['nama_pertemuan'] ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($row['nama_pertemuan']) . '" ' . $selected . '>' . htmlspecialchars($row['nama_pertemuan']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Module Description</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                                    required><?php echo htmlspecialchars($module['deskripsi']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="file_name" class="form-label">File Name</label>
                                <input type="text" class="form-control" id="file_name" name="file_name"
                                    value="<?php echo htmlspecialchars($module['file_name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="file_upload" class="form-label">Upload File</label>
                                <input type="file" class="form-control" id="file_upload" name="file_upload">
                                <p>Current File: <?php echo htmlspecialchars($module['file_upload']); ?></p>
                            </div>
                            <div class="mb-3">
                                <label for="link" class="form-label">Link</label>
                                <input type="text" class="form-control" id="link" name="link"
                                    value="<?php echo htmlspecialchars($module['link']); ?>" required>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" id="save-btn">Update Module</button>
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
    document.getElementById('edit-module-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: '', // Submit to the same PHP file
            data: formData,
            processData: false, // Important for FormData
            contentType: false, // Important for FormData
            success: function(response) {
                let res = JSON.parse(response);
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Module updated successfully!',
                    });
                    window.location.href = 'modules.php';

                    // Reset the form after successful save
                    document.getElementById('edit-module-form').reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to update module: ' + res.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to update module.',
                });
            }
        });
    });
    </script>
</body>

</html>