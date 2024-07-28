<?php
include("include/navbar.php");

// Check if the user is logged in
if (isset($_SESSION['id'])) {
    // Get user role from the session
    $role = $_SESSION['role'];

    // Redirect the user based on their role
    if ($role == 'ADMIN') {
        // Redirect to admin dashboard
        header("Location: admin/index.php");
        exit(); // Ensure script execution stops after redirection
    }
} else {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

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

// Fetch bookmarked videos for the logged-in user
$user_id = $_SESSION['id'];
$sql = "SELECT v.id_video, v.judul_video, v.deskripsi_video, v.url_video, v.thumbnail, p.nama_praktikum 
        FROM videos v 
        JOIN praktikum p ON v.nama_praktikum = p.nama_praktikum
        JOIN bookmarks b ON v.id_video = b.video_id
        WHERE b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query executed successfully
if (!$result) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Lab SIMI - Video Learning Website</title>
    <link href="./assets/img/favicon.png" rel="icon">
    <link href="./assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="./assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="./assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        .fixed-size-img {
            width: 100%;
            height: 200px; /* Set your desired height */
            object-fit: cover; /* Ensures the image covers the container while maintaining aspect ratio */
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            background-color: #fff;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .card-body {
            padding: 1rem;
        }
        .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .card-text {
            font-size: 1rem;
            color: #555;
        }
        .card-link {
            text-decoration: none;
            color: inherit;
        }
        .card-link:hover {
            color: #007bff;
        }
        .container {
            margin-top: 2rem;
        }
    </style>
</head>
<body class="index-page">

<main class="main">
    <section id="values" class="values section">
    <div class="container section-title mt-5" data-aos="fade-up">
            <p>Bookmarked videos<br></p>
        </div>

        <div class="container">
            <div class="row gy-4">
                <?php
                if ($result->num_rows > 0) {
                    // Output data for each row
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">';
                        echo '<a href="video.php?id=' . $row['id_video'] . '" class="card-link">';
                        echo '<div class="card">';
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($row['thumbnail']) . '" class="fixed-size-img img-fluid" alt="Thumbnail">';
                        echo '<div class="card-body">';
                        echo '<p class="card-text">' . htmlspecialchars($row['nama_praktikum']) . '</p>';
                        echo '<h3 class="card-title">' . htmlspecialchars($row['judul_video']) . '</h3>';
                        echo '<p class="card-text">' . htmlspecialchars($row['deskripsi_video']) . '</p>';
                        echo '</div>';
                        echo '</div>';
                        echo '</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No bookmarked videos found.</p>';
                }
                ?>
            </div>
        </div>
    </section>
</main>

<?php
include("include/footer.php");
?>

<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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

</body>
</html>
