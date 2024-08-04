<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../login.php");
    exit();
}

include("../config/connection.php");

$praktikumOptions = [];
$sql = "SELECT nama_praktikum FROM praktikum";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $praktikumOptions[] = $row['nama_praktikum'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul_video = $_POST['judul_video'];
    $deskripsi_video = $_POST['deskripsi_video'];
    $url_video = $_POST['url_video'];
    $nama_praktikum = $_POST['nama_praktikum'];

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['thumbnail']['tmp_name'];
        $fileName = $_FILES['thumbnail']['name'];
        $fileSize = $_FILES['thumbnail']['size'];
        $fileType = $_FILES['thumbnail']['type'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
        if (in_array(strtolower($fileExtension), $allowedfileExtensions)) {
            $uploadDirectory = "uploads/thumbnails/";
            if (!file_exists($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }
            $targetFilePath = $uploadDirectory . $fileName;

            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                $sql = "INSERT INTO videos (judul_video, deskripsi_video, url_video, thumbnail, nama_praktikum, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("sssss", $judul_video, $deskripsi_video, $url_video, $fileName, $nama_praktikum);
                    if ($stmt->execute()) {
                        echo json_encode(['status' => 'success']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => $conn->error]);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement.']);
                }
                $stmt->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded or there was an upload error.']);
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
    <link href="../assets/img/jakarta-logo.png" rel="icon">
    <link href="../assets/img/jakarta-logo.png" rel="apple-touch-icon">
    <link rel="stylesheet" href="assets/css/styles.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?php include("sidebar.php"); ?>
        <div class="body-wrapper">
            <?php include("header.php"); ?>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">Add New Video</h5>
                        <form id="add-video-form" enctype="multipart/form-data">
                            <div class="mb-3 text-end">
                                <a href="videos.php" class="btn btn-secondary">Back</a>
                            </div>
                            <div class="mb-3">
                                <label for="judul_video" class="form-label">Video Title</label>
                                <input type="text" class="form-control" id="judul_video" name="judul_video" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi_video" class="form-label">Video Description</label>
                                <textarea class="form-control" id="deskripsi_video" name="deskripsi_video"
                                    required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="url_video" class="form-label">Video URL</label>
                                <input type="url" class="form-control" id="url_video" name="url_video" required>
                            </div>
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*"
                                    required>
                            </div>
                            <div class="mb-3 d-flex align-items-center">
                                <label for="nama_praktikum" class="form-label me-2">Praktikum</label>
                                <select class="form-select" id="nama_praktikum" name="nama_praktikum" required>
                                    <?php foreach ($praktikumOptions as $option) : ?>
                                    <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" id="save-btn">Add Video</button>
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
    document.getElementById('add-video-form').addEventListener('submit', function(event) {
        event.preventDefault();
        let formData = new FormData(document.getElementById('add-video-form'));

        $.ajax({
            type: 'POST',
            url: '',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                let res = JSON.parse(response);
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Video added successfully!',
                    });
                    document.getElementById('add-video-form').reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to add video: ' + res.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to add video.',
                });
            }
        });
    });
    </script>
</body>

</html>