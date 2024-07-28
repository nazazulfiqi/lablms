<?php
// Include the navbar which already starts the session
include("include/navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Courses</title>
    <link href="./assets/img/favicon.png" rel="icon">
    <link href="./assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="./assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="./assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="index-page">

<main class="main">
    <section id="values" class="values section">
        <div class="container section-title mt-5" data-aos="fade-up">
            <p id="total-courses">Courses Available</p>
        </div>

        <div class="container mb-4" data-aos="fade-up">
            <form id="filter-form" class="d-flex align-items-center">
                <div class="input-group">
                    <input type="text" class="form-control" id="search" placeholder="Search by course name">
                    <button class="btn btn-primary" type="button" id="search-button">Search</button>
                </div>
            </form>
        </div>

        <div class="container">
            <div class="row gy-4" id="courses-container">
                <!-- Courses will be dynamically loaded here -->
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

<!-- Custom CSS for card styling and search button -->
<style>
    .card {
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .card-link {
        text-decoration: none; /* Remove underline from links */
        color: inherit; /* Inherit color from parent elements */
    }
    .btn-primary {
        background-color: #4154f1;
        border-color: #4154f1;
    }
    .btn-primary:hover {
        background-color: #324cdd;
        border-color: #324cdd;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchButton = document.getElementById('search-button');
    const searchInput = document.getElementById('search');
    const coursesContainer = document.getElementById('courses-container');
    const totalCoursesElement = document.getElementById('total-courses');

    function fetchCourses() {
        const search = encodeURIComponent(searchInput.value);

        fetch(`fetch_courses.php?search=${search}`)
            .then(response => response.json())
            .then(data => {
                totalCoursesElement.textContent = `${data.totalCourses} Courses Available`;

                if (data.courses.length > 0) {
                    coursesContainer.innerHTML = data.courses.map(course => `
                        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                            <a href="modules.php?praktikum=${encodeURIComponent(course.nama_praktikum)}" class="card-link">
                                <div class="card">
                                    <h3>${course.nama_praktikum}</h3>
                                    <p>${course.deskripsi_praktikum.replace(/\n/g, '<br>')}</p>
                                    <p class="text-muted"><strong>Videos:</strong> ${course.video_count}</p>
                                </div>
                            </a>
                        </div>
                    `).join('');
                } else {
                    coursesContainer.innerHTML = '<p>No courses found.</p>';
                }
            });
    }

    // Fetch all courses when the page loads
    fetchCourses();

    searchButton.addEventListener('click', fetchCourses);
    searchInput.addEventListener('input', fetchCourses);
});
</script>

</body>
</html>
