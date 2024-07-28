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
    } elseif ($role == 'USER') {
    }
}
?>

<main class="main">

    <section id="hero" class="hero section">

        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
                    <h1 data-aos="fade-up">Laboratory of Information Systems and Management Informatics</h1>
                    <p data-aos="fade-up" data-aos-delay="100">Virtual Laboratory, Real Knowledge</p>
                    <div class="d-flex flex-column flex-md-row" data-aos="fade-up" data-aos-delay="200">
                        <a href="register.php" class="btn-get-started">Register</a>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out">
                    <img src="assets/img/hero-img.png" class="img-fluid animated" alt="">
                </div>
            </div>
        </div>

    </section>

    <section id="feature" class="values section">

        <div class="container section-title" data-aos="fade-up">
            <h2>Features</h2>
            <p>What we value most<br></p>
        </div>

        <div class="container">

            <div class="row gy-4">

                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card">
                        <img src="assets/img/values-1.png" class="img-fluid" alt="">
                        <h3>Video Learning Catalog</h3>
                        <p>Browse through a well-organized catalog featuring videos categorized by courses.</p>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card">
                        <img src="assets/img/values-2.png" class="img-fluid" alt="">
                        <h3>Search Feature</h3>
                        <p>Filter through titles, designed to help you find exactly what you're looking for in just a few clicks.</p>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card">
                        <img src="assets/img/values-3.png" class="img-fluid" alt="">
                        <h3>Note Taking</h3>
                        <p>Easily create, view, and edit notes associated with specific videos. All your notes are organized by video, so you can quickly find relevant information.</p>
                    </div>
                </div>

            </div>

        </div>

    </section>

    <!-- Faq Section -->
    <section id="faq" class="faq section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>F.A.Q</h2>
            <p>Frequently Asked Questions</p>
        </div><!-- End Section Title -->

        <div class="container">

            <div class="row">

                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">

                    <div class="faq-container">

                        <div class="faq-item faq-active">
                            <h3>What can I use this website for?</h3>
                            <div class="faq-content">
                                <p>This Video Learning Website is an online platform that provides various educational videos for students. These videos are categorized by courses, making it easy for students to access learning materials flexibly and interactively.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div><!-- End Faq item-->

                        <div class="faq-item">
                            <h3>How do I register on the website?</h3>
                            <div class="faq-content">
                                <p>To register, visit the <a href="register.php">Register</a> page and fill out the registration form with the required information.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div><!-- End Faq item-->

                        <div class="faq-item">
                            <h3>Can I take notes on the videos?</h3>
                            <div class="faq-content">
                                <p>Yes, you can take notes on the videos you watch. These notes will be saved and can be accessed later on your notes page.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div><!-- End Faq item-->

                    </div>

                </div><!-- End Faq Column-->

                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">

                    <div class="faq-container">

                        <div class="faq-item">
                            <h3>How do I delete notes that I've made?</h3>
                            <div class="faq-content">
                                <p>To delete notes, go to your notes page and select the note you wish to delete. There will be an option to remove the note in the note settings.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div><!-- End Faq item-->

                        <div class="faq-item">
                            <h3>Is there a cost to access videos on this website?</h3>
                            <div class="faq-content">
                                <p>All videos on the website are completely free to access. Enjoy all the educational content without any cost.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div><!-- End Faq item-->

                        <div class="faq-item">
                            <h3>Can I bookmark videos?</h3>
                            <div class="faq-content">
                                <p>Yes, you can bookmark videos for easy access later. Simply click the bookmark icon on the video page to save it to your list of bookmarked videos.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div><!-- End Faq item-->

                    </div>

                </div><!-- End Faq Column-->

            </div>

        </div>

    </section><!-- /Faq Section -->

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
