<?php
session_start(); // Start the session

$isLogin = false;

if (isset($_SESSION['id'])) {
    $isLogin = true;
}
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$dbname = "dblms"; // Update with your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Comment
$id = $_GET['id'];
$comments = mysqli_query($conn, "SELECT * FROM comments WHERE id_video = '$id' ");




// Check if id_video is set in the URL
if (isset($_GET['id'])) {
    $id_video = $_GET['id'];

    // Fetch video details from the database
    $sql = "SELECT v.id_video, v.judul_video, v.created_at, v.deskripsi_video, v.url_video, v.thumbnail, p.nama_praktikum 
            FROM videos v 
            JOIN praktikum p ON v.nama_praktikum = p.nama_praktikum
            WHERE v.id_video = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error . " SQL: " . $sql);
    }
    $stmt->bind_param("i", $id_video);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the video exists
    if ($result->num_rows > 0) {
        $video = $result->fetch_assoc();

        // Extract YouTube Video ID and construct embed URL
        if (preg_match('/(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $video['url_video'], $matches)) {
            $youtube_id = $matches[1];
            $embed_url = "https://www.youtube.com/embed/" . $youtube_id;
        } else {
            die("Invalid YouTube URL.");
        }
    } else {
        die("Video not found.");
    }
} else {
    die("No video ID specified.");
}

// Fetch user's existing notes for this video
$user_notes = "";
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT notes FROM notes WHERE video_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id_video, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user_notes = $result->fetch_assoc()['notes'];
    }
    $stmt->close();
}

// Check if the video is bookmarked by the user
$bookmarked = false;
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT * FROM bookmarks WHERE video_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id_video, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $bookmarked = true;
    }
    $stmt->close();
}


if (isset($_POST['submit'])) {
    $name = $_SESSION['fname'];
    $comment = $_POST['comment'];


    $query = "INSERT INTO comments (id,id_video,name,comment,created_at) VALUES ('','$id','$name','$comment', current_timestamp())";
    $tambah = mysqli_query($conn, $query);
    if (!$tambah) {
        echo "<script>
        alert('Gagal');
        window.location = '';
        </script>";
    } else {
        echo "<script>
        alert('Sukses');
        window.location = '';
        </script>";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($video['judul_video']); ?></title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    .video-container {
        position: relative;
        padding-bottom: 56.25%;
        /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        max-width: 100%;
        background: #000;
        border-radius: 8px;
    }

    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .video-description {
        background-color: #f8f9fa;
        /* Light gray background */
        padding: 15px;
        /* Add padding around the text */
        border-radius: 8px;
        /* Optional: rounded corners */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /* Optional: subtle shadow */
        margin-top: 15px;
        /* Optional: spacing from the top */
    }

    .notes-container {
        padding: 15px;
        /* Add padding around the text */
        border-radius: 8px;
        /* Optional: rounded corners */
        margin-top: 15px;
        /* Optional: spacing from the top */
    }

    .notes-container textarea {
        width: 100%;
        height: 320px;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 8px;
        resize: none;
    }

    .text-success {
        color: #28a745;
    }

    .text-danger {
        color: #dc3545;
    }

    .text-warning {
        color: #ffc107;
    }

    .button-container {
        text-align: right;
    }

    .back-button {
        margin-bottom: 15px;
        /* Space between button and title */
    }
    </style>
</head>

<body>
    <?php include("include/navbar.php"); ?>

    <main class="main">
        <div class="container pt-4 mt-5">
            <div class="row">
                <div class="col-md-8 ">
                    <div class="d-flex justify-content-between mb-2">

                        <a href="javascript:history.back()" class="btn btn-secondary back-button m-0">Back</a>
                        <!-- Bookmark Button -->
                        <button id="bookmark-btn"
                            class="btn  <?php echo $bookmarked ? 'btn-primary' : 'btn-outline-primary'; ?>"
                            data-video-id="<?php echo htmlspecialchars($id_video); ?>">
                            <i class="bi <?php echo $bookmarked ? 'bi-bookmark-check' : 'bi-bookmark'; ?>"></i>
                            <?php echo $bookmarked ? 'Bookmarked' : 'Bookmark'; ?>
                        </button>
                    </div>
                    <div class="video-container">
                        <iframe src="<?php echo htmlspecialchars($embed_url); ?>" frameborder="0"
                            allowfullscreen></iframe>
                    </div>
                    <h2><?php echo htmlspecialchars($video['judul_video']); ?></h2>
                    <p><strong>Praktikum:</strong> <?php echo htmlspecialchars($video['nama_praktikum']); ?></p>
                    <p><strong>Uploaded At:</strong>
                        <?php echo htmlspecialchars(date('d M Y', strtotime($video['created_at']))); ?></p>
                    <p class="video-description"><?php echo nl2br(htmlspecialchars($video['deskripsi_video'])); ?></p>
                </div>
                <div class="col-md-4 mt-4">
                    <div class="notes-container">
                        <h3>Notes</h3>
                        <form action="save_notes.php" method="post">
                            <textarea name="notes"
                                placeholder="Write your notes here..."><?php echo htmlspecialchars($user_notes); ?></textarea>
                            <input type="hidden" name="video_id" value="<?php echo htmlspecialchars($id_video); ?>">
                            <input type="hidden" name="save_note" value="1">
                            <div class="button-container">
                                <button type="submit" class="btn btn-primary mt-2"
                                    style="background-color: #012970">Save Notes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mt-5 row">
                <h1>Komentar</h1>

                <div class="col-8">
                    <?php foreach ($comments as $comment) { ?>
                    <div class="card p-3 mb-2">

                        <div class="d-flex  justify-content-between">
                            <p class="m-0 p-0 fw-bold" style="font-size: 20px;"><?= $comment['name'] ?></p>
                            <p class="m-0 p-0"><?= $comment['created_at'] ?></p>
                        </div>
                        <p class="mt-2 p-0"><?= $comment['comment'] ?></p>
                    </div>
                    <?php } ?>
                </div>


            </div>

            <?php if ($isLogin) { ?>
            <div class="row">
                <div class="col-8">
                    <form class="mt-4" method="post">
                        <h5 class="mb-1">Tinggalkan Komentar</p>
                            <textarea name="comment" id="comment" class="form-control" rows="5"></textarea>

                            <button type="submit" name="submit" class="btn btn-primary mt-3 w-100"
                                style="background-color: #012970">Kirim</button>
                    </form>
                </div>
            </div>
            <?php } else { ?>

            <div>
                <p class="mt-4">Kamu harus login terlebih dahulu</p>
                <a href="login.php" class="btn btn-primary">Login</a>
            </div>

            <?php } ?>
        </div>


    </main>

    <?php include("include/footer.php"); ?>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const bookmarkButton = document.getElementById('bookmark-btn');

        bookmarkButton.addEventListener('click', function() {
            const videoId = bookmarkButton.getAttribute('data-video-id');

            fetch('bookmark.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'video_id': videoId,
                    }),
                })
                .then(response => response.text())
                .then(status => {
                    if (status === 'bookmarked') {
                        bookmarkButton.innerHTML =
                            '<i class="bi bi-bookmark-check"></i> Bookmarked';
                        bookmarkButton.classList.remove('btn-outline-primary');
                        bookmarkButton.classList.add('btn-primary');
                    } else if (status === 'unbookmarked') {
                        bookmarkButton.innerHTML = '<i class="bi bi-bookmark"></i> Bookmark';
                        bookmarkButton.classList.remove('btn-primary');
                        bookmarkButton.classList.add('btn-outline-primary');
                    }
                });
        });
    });
    </script>
</body>

</html>