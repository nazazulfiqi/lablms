<?php
// Include the navbar which already starts the session
include("include/navbar.php");

// Start the session
session_start();

// Check if the user is logged in and handle redirection based on role
if (isset($_SESSION['id'])) {
    $role = $_SESSION['role'];
    if ($role == 'ADMIN') {
        header("Location: admin/index.php");
        exit(); // Ensure script execution stops after redirection
    }
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

// Fetch `praktikum` name from the URL
$praktikumName = isset($_GET['praktikum']) ? $_GET['praktikum'] : '';
$praktikumName = $conn->real_escape_string($praktikumName);

// Fetch praktikum details
$praktikumSql = "SELECT nama_praktikum, deskripsi_praktikum FROM praktikum WHERE nama_praktikum = '$praktikumName'";
$praktikumResult = $conn->query($praktikumSql);

if ($praktikumResult && $praktikumResult->num_rows > 0) {
    $praktikumRow = $praktikumResult->fetch_assoc();
    $praktikumDescription = $praktikumRow['deskripsi_praktikum'];
} else {
    $praktikumName = 'Unknown Praktikum'; // Fallback name
    $praktikumDescription = 'Description not available'; // Fallback description
}

// Fetch video data from the database
$sql = "SELECT v.id_video, v.judul_video, v.deskripsi_video, v.thumbnail, p.nama_praktikum 
        FROM videos v 
        JOIN praktikum p ON v.nama_praktikum = p.nama_praktikum
        WHERE p.nama_praktikum = '$praktikumName'";
$result = $conn->query($sql);

// Fetch modules data from the database
$modulesSql = "SELECT nama_pertemuan, deskripsi, file_name, file_upload, link FROM modules WHERE nama_praktikum = '$praktikumName'";
$modulesResult = $conn->query($modulesSql);

// Check if the queries executed successfully
if (!$result || !$modulesResult) {
    die("Error executing query: " . $conn->error);
}

// Close the connection
$conn->close();
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
    .accordion-button {
        background-color: #4154f1;
        color: white;
        font-size: 16px;
        font-weight: 600;
        border: none;
        border-radius: 0.5rem;
        transition: background-color 0.3s ease;
    }

    .accordion-button:not(.collapsed) {
        background-color: #4154f1;
        color: white;
    }

    .accordion-button:hover {
        background-color: #4154f1;
        color: white;
    }

    .accordion-body {
        padding: 1rem;
        background-color: #f9f9f9;
        border-top: 1px solid #ddd;
    }

    .accordion-body ul {
        list-style-type: none;
        padding: 0;
    }

    .accordion-body li {
        margin-bottom: 10px;
    }

    .accordion-body a {
        text-decoration: none;
        color: #4154f1;
        font-weight: 500;
    }

    .accordion-body a:hover {
        text-decoration: underline;
    }

    .fixed-size-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 0.5rem;
    }

    /* Set accordion width to be equal to the video card width */
    .accordion-container {
        max-width: 360px;
        /* Adjust this value to match the video card width */
        margin: 0;
        /* Align the accordion to the left */
    }

    .card-link {
        text-decoration: none;
        color: inherit;
    }

    .centered-heading {
        text-align: center;
        margin-bottom: 1rem;
        /* Add some spacing below the heading if needed */
    }
    </style>
</head>

<body class="index-page">

    <main class="main">
        <section id="values" class="values section">
            <div class="container section-title mt-4" data-aos="fade-up">
                <h2><?php echo htmlspecialchars($praktikumName); ?></h2>
                <p><?php echo htmlspecialchars($praktikumDescription); ?></p>
            </div>
            <div class="container" data-aos="fade-up">
                <h3 class="">Modules</h3>
                <div class="accordion-container">
                    <div class="accordion" id="modulesAccordion">
                        <div class="accordion-item ">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    Show Modules
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                data-bs-parent="#modulesAccordion">
                                <div class="accordion-body">
                                    <ul>
                                        <?php
                                        if ($modulesResult->num_rows > 0) {
                                            while ($module = $modulesResult->fetch_assoc()) {
                                                echo '<li><a href="#" class="meeting-link" data-bs-toggle="modal" data-bs-target="#meetingModal" data-praktikum="' . htmlspecialchars($praktikumName) . '" data-pertemuan="' . htmlspecialchars($module['nama_pertemuan']) . '" data-deskripsi="' . htmlspecialchars($module['deskripsi']) . '" data-file="' . htmlspecialchars($module['file_upload']) . '" data-link="' . htmlspecialchars($module['link']) . '">' . htmlspecialchars($module['nama_pertemuan']) . '</a></li>';
                                            }
                                        } else {
                                            echo '<p>No modules found.</p>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-4">
                <h3 class="">Videos</h3>
                <div class="row gy-4">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">';
                            echo '<a href="video.php?id=' . htmlspecialchars($row['id_video']) . '" class="card-link">';
                            echo '<div class="card">';
                            echo '<p>' . htmlspecialchars($row['nama_praktikum']) . '</p>';
                            echo '<img src="../admin/uploads/thumbnails/' . $row['thumbnail'] . '" class="fixed-size-img img-fluid" alt="">';

                            echo '<h3>' . htmlspecialchars($row['judul_video']) . '</h3>';
                            echo '<p>' . htmlspecialchars($row['deskripsi_video']) . '</p>';
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

    <!-- Modal -->
    <div class="modal fade" id="meetingModal" tabindex="-1" aria-labelledby="meetingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="meetingModalLabel">Module Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Praktikum:</strong> <span id="modalPraktikum"></span></p>
                    <p><strong>Pertemuan:</strong> <span id="modalPertemuan"></span></p>
                    <p><strong>Deskripsi:</strong> <span id="modalDeskripsi"></span></p>
                    <p><strong>File:</strong> <span id="modalFile"></span></p>
                    <p><strong>Link:</strong> <span id="modalLink"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    include("include/footer.php");
    ?>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="./assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/vendor/aos/aos.js"></script>
    <script src="./assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="./assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Template Main JS File -->
    <script src="./assets/js/main.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var meetingLinks = document.querySelectorAll('.meeting-link');
        var modalPraktikum = document.getElementById('modalPraktikum');
        var modalPertemuan = document.getElementById('modalPertemuan');
        var modalDeskripsi = document.getElementById('modalDeskripsi');
        var modalFile = document.getElementById('modalFile');
        var modalLink = document.getElementById('modalLink');

        meetingLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                var praktikum = link.getAttribute('data-praktikum');
                var pertemuan = link.getAttribute('data-pertemuan');
                var deskripsi = link.getAttribute('data-deskripsi');
                var fileName = link.getAttribute('data-file');
                var linkURL = link.getAttribute('data-link');

                modalPraktikum.textContent = praktikum;
                modalPertemuan.textContent = pertemuan;
                modalDeskripsi.textContent = deskripsi;
                modalFile.innerHTML = fileName ? '<a href="../uploads/modules/' + fileName +
                    '" target="_blank">' + fileName + '</a>' : 'Not available';
                modalLink.innerHTML = linkURL ? '<a href="' + linkURL + '" target="_blank">' +
                    linkURL + '</a>' : 'Not available';
            });
        });
    });
    </script>

</body>

</html>