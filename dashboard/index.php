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
    // Add any additional logic for 'USER' role if needed
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

// Fetch the number of courses and videos
$courseCountSql = "SELECT COUNT(DISTINCT nama_praktikum) AS course_count FROM praktikum";
$videoCountSql = "SELECT COUNT(id_video) AS video_count FROM videos";

$courseCountResult = $conn->query($courseCountSql);
$videoCountResult = $conn->query($videoCountSql);

$courseCount = $courseCountResult->fetch_assoc()['course_count'];
$videoCount = $videoCountResult->fetch_assoc()['video_count'];

// Fetch video data for the gallery
$sql = "SELECT v.id_video, v.judul_video, v.deskripsi_video, v.url_video, v.thumbnail, p.nama_praktikum 
        FROM videos v 
        JOIN praktikum p ON v.nama_praktikum = p.nama_praktikum";
$result = $conn->query($sql);

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
</head>

<body class="index-page">

    <main class="main">
        <section id="hero" class="hero section">

            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
                        <h1 data-aos="fade-up"><?php echo htmlspecialchars($courseCount); ?> courses and
                            <?php echo htmlspecialchars($videoCount); ?> videos available for you to explore!</h1>
                        <!-- <p data-aos="fade-up" data-aos-delay="100">Laboratorium Virtual, Pengetahuan Nyata</p> -->
                        <div class="d-flex flex-column flex-md-row" data-aos="fade-up" data-aos-delay="200">
                            <a href="courses.php" class="btn-get-started mt-4">Explore Courses</a>
                        </div>
                    </div>
                    <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out">
                        <img src="assets/img/hero-img.png" class="img-fluid animated" alt="">
                    </div>
                </div>
            </div>

        </section>
        <section id="values" class="values section">
            <div class="container section-title mt-5" data-aos="fade-up">
                <p>Recently uploaded videos<br></p>
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
                            echo '<p>' . $row['nama_praktikum'] . '</p>';
                            echo '<img src="../admin/uploads/thumbnails/' . $row['thumbnail'] . '" class="fixed-size-img img-fluid" alt="">';

                            echo '<h3>' . $row['judul_video'] . '</h3>';
                            echo '<p>' . $row['deskripsi_video'] . '</p>';
                            echo '</div>';
                            echo '</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No videos found.</p>';
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

    <!-- Custom CSS for fixed-size images -->
    <style>
        .fixed-size-img {
            width: 100%;
            height: 200px;
            /* Set your desired height */
            object-fit: cover;
            /* This ensures the image covers the container while maintaining aspect ratio */
        }

        .card-link {
            text-decoration: none;
            /* Remove underline from links */
            color: inherit;
            /* Inherit color from parent elements */
        }
    </style>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>