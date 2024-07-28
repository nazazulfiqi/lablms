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

// Pagination settings
$results_per_page = 10; // Number of results per page

// Determine current page number
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $current_page = intval($_GET['page']);
} else {
    $current_page = 1; // Default to page 1
}

// Calculate the limit clause for SQL query
$start_index = ($current_page - 1) * $results_per_page;

// Fetch data for meetings table with pagination
$sql_meetings = "SELECT nama_pertemuan, created_at FROM pertemuan LIMIT $start_index, $results_per_page";
$result_meetings = $conn->query($sql_meetings);

if (!$result_meetings) {
    die("Error fetching meetings: " . $conn->error);
}

// Count total number of meetings (for pagination)
$sql_count = "SELECT COUNT(*) AS total FROM pertemuan";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_meetings = $row_count['total'];

// Calculate total number of pages
$total_pages = ceil($total_meetings / $results_per_page);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">

    <style>
        .card-img-top {
            width: 286px; /* Fixed width */
            height: 180px; /* Fixed height */
            object-fit: cover; /* Ensure the image covers the entire space */
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
                        <h5 class="card-title fw-semibold mb-4">Welcome back,
                            <?= htmlspecialchars($_SESSION['fname']) ?>!
                        </h5>
                        <div class="text-end mb-3">
                            <a href="create_pertemuan.php" class="btn btn-primary">Add Meeting</a>
                        </div>

                        <!-- Table of Meetings -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama Pertemuan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result_meetings->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['nama_pertemuan']) . "</td>";
                                        echo "<td>
                                                <a href='edit_pertemuan.php?nama_pertemuan=" . urlencode($row['nama_pertemuan']) . "' class='btn btn-sm btn-info'>Edit</a>
                                                <a href='javascript:void(0);' onclick='confirmDeleteMeeting(\"" . urlencode($row['nama_pertemuan']) . "\");' class='btn btn-sm btn-danger'>Delete</a>
                                            </td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- End Table of Meeting -->

                        <!-- Pagination Links -->
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-end">
                                <?php
                                // Previous page link
                                $prev_page = $current_page - 1;
                                if ($prev_page >= 1) {
                                    echo "<li class='page-item'><a class='page-link' href='?page=$prev_page'>Previous</a></li>";
                                }

                                // Page links
                                for ($page = 1; $page <= $total_pages; $page++) {
                                    echo "<li class='page-item " . ($page == $current_page ? 'active' : '') . "'><a class='page-link' href='?page=$page'>$page</a></li>";
                                }

                                // Next page link
                                $next_page = $current_page + 1;
                                if ($next_page <= $total_pages) {
                                    echo "<li class='page-item'><a class='page-link' href='?page=$next_page'>Next</a></li>";
                                }
                                ?>
                            </ul>
                        </nav>
                        <!-- End Pagination Links -->

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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Function to confirm deletion and then call deleteMeeting function
        function confirmDeleteMeeting(meetingName) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this meeting!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Call deleteMeeting function if user confirms
                    deleteMeeting(meetingName);
                }
            });
        }

        // AJAX request to delete meeting
        function deleteMeeting(meetingName) {
            $.ajax({
                type: 'POST',
                url: 'delete_pertemuan.php',
                data: { nama_pertemuan: meetingName }, // Pass meetingName as data parameter
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                        }).then((result) => {
                            location.reload(); // Reload the page after successful deletion
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to delete meeting. Please try again later.',
                    });
                }
            });
        }
    </script>
</body>

</html>
