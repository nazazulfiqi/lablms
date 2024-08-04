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

// Function to log activities
function log_activity($conn, $user_id, $activity)
{
    $sql = "INSERT INTO activities (user_id, activity) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("is", $user_id, $activity);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch video data to be edited
$id_video = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_video = intval($_GET['id']);
} elseif (isset($_GET['id_video']) && is_numeric($_GET['id_video'])) {
    $id_video = intval($_GET['id_video']);
}

if ($id_video) {
    // Fetch video data
    $sql = "SELECT v.id_video, v.judul_video, v.deskripsi_video, v.url_video, v.thumbnail, p.nama_praktikum 
            FROM videos v 
            JOIN praktikum p ON v.nama_praktikum = p.nama_praktikum
            WHERE v.id_video=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in SQL query: " . $conn->error);
    }

    $stmt->bind_param("i", $id_video);
    $stmt->execute();
    $result = $stmt->get_result();
    $video = $result->fetch_assoc();

    if (!$video) {
        echo "Invalid video ID";
        exit();
    }
} else {
    echo "Invalid video ID";
    exit();
}

// Fetch praktikum options for the dropdown
$praktikumOptions = [];
$sql_praktikum = "SELECT nama_praktikum FROM praktikum";
$result_praktikum = $conn->query($sql_praktikum);

if ($result_praktikum->num_rows > 0) {
    while ($row_praktikum = $result_praktikum->fetch_assoc()) {
        $praktikumOptions[] = $row_praktikum['nama_praktikum'];
    }
}

// Process form submission
$update_success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul_video = $_POST['judul_video'];
    $deskripsi_video = $_POST['deskripsi_video'];
    $url_video = $_POST['url_video'];
    $nama_praktikum = $_POST['nama_praktikum']; // Assuming this is fetched from the praktikum table

    // Handle thumbnail update if a new file is uploaded
    if ($_FILES['thumbnail']['name']) {
        $thumbnail = $_FILES['thumbnail'];

        // Check file type and size (adjust as per your needs)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (in_array($thumbnail['type'], $allowed_types) && $thumbnail['size'] <= $max_size) {
            // Process file upload
            $upload_dir = 'uploads/thumbnails/';
            $file_name = uniqid('thumbnail_') . '_' . basename($thumbnail['name']);
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($thumbnail['tmp_name'], $target_path)) {
                // Update video data with new thumbnail filename
                $thumbnail_filename = $file_name;

                $sql_update = "UPDATE videos 
                               SET judul_video=?, deskripsi_video=?, url_video=?, thumbnail=?, nama_praktikum=?, updated_at=NOW()
                               WHERE id_video=?";
                $stmt_update = $conn->prepare($sql_update);

                if (!$stmt_update) {
                    die("Error in SQL update query: " . $conn->error);
                }

                $stmt_update->bind_param("sssssi", $judul_video, $deskripsi_video, $url_video, $thumbnail_filename, $nama_praktikum, $id_video);

                if ($stmt_update->execute()) {
                    $update_success = true;
                    // Update $video array with new thumbnail filename
                    $video['thumbnail'] = $thumbnail_filename;
                } else {
                    echo "Error updating video: " . $conn->error;
                }

                $stmt_update->close();
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Invalid file type or exceeds maximum size.";
        }
    } else {
        // Update video data without changing the thumbnail
        $sql_update = "UPDATE videos 
                       SET judul_video=?, deskripsi_video=?, url_video=?, nama_praktikum=?, updated_at=NOW()
                       WHERE id_video=?";
        $stmt_update = $conn->prepare($sql_update);

        if (!$stmt_update) {
            die("Error in SQL update query: " . $conn->error);
        }

        $stmt_update->bind_param("ssssi", $judul_video, $deskripsi_video, $url_video, $nama_praktikum, $id_video);

        if ($stmt_update->execute()) {
            $update_success = true;
        } else {
            echo "Error updating video: " . $conn->error;
        }

        $stmt_update->close();
    }

    // Log the activity if the update was successful
    if ($update_success) {
        $user_id = $_SESSION['id']; // Assuming the user's ID is stored in the session
        $activity = "Updated video with ID " . $id_video;
        log_activity($conn, $user_id, $activity);
    }
}

$stmt->close();
$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Video</title>
    <link href="../assets/img/jakarta-logo.png" rel="icon">
    <link href="../assets/img/jakarta-logo.png" rel="apple-touch-icon">
    <link rel="stylesheet" href="assets/css/styles.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    .img-thumbnail {
        max-width: 200px;
        /* Adjust the max width as per your design */
        max-height: 200px;
        /* Adjust the max height as per your design */
        width: auto;
        height: auto;
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
                        <h5 class="card-title fw-semibold mb-4">Edit Video</h5>

                        <?php if ($update_success) : ?>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Video updated successfully',
                                showConfirmButton: true,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                // Optional: You can reload the page here if needed
                                // location.reload();
                            });
                        });
                        </script>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="judul_video" class="form-label">Judul Video</label>
                                <input type="text" class="form-control" id="judul_video" name="judul_video"
                                    value="<?= htmlspecialchars($video['judul_video']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi_video" class="form-label">Deskripsi Video</label>
                                <textarea class="form-control" id="deskripsi_video" name="deskripsi_video" rows="3"
                                    required><?= htmlspecialchars($video['deskripsi_video']) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="url_video" class="form-label">URL Video</label>
                                <input type="url" class="form-control" id="url_video" name="url_video"
                                    value="<?= htmlspecialchars($video['url_video']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="nama_praktikum" class="form-label">Nama Praktikum</label>
                                <select class="form-select" id="nama_praktikum" name="nama_praktikum" required>
                                    <?php foreach ($praktikumOptions as $option) : ?>
                                    <option value="<?= htmlspecialchars($option) ?>"
                                        <?= $video['nama_praktikum'] === $option ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($option) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail</label>
                                <?php if (!empty($video['thumbnail'])): ?>
                                <div class="mb-3">
                                    <img src="uploads/thumbnails/<?= htmlspecialchars($video['thumbnail']) ?>"
                                        alt="Current Thumbnail" class="img-thumbnail">
                                </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Video</button>
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