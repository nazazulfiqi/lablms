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

// Handle search query
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
}

// Calculate the limit clause for SQL query
$start_index = ($current_page - 1) * $results_per_page;

// Fetch data for modules table with pagination and search filter
$sql_modules = "SELECT * FROM modules";
if (!empty($search_query)) {
    $sql_modules .= " WHERE nama_praktikum LIKE '%$search_query%'";
}
$sql_modules .= " LIMIT $start_index, $results_per_page";
$result_modules = $conn->query($sql_modules);

if (!$result_modules) {
    die("Error fetching modules: " . $conn->error);
}

// Count total number of modules (for pagination)
$sql_count = "SELECT COUNT(*) AS total FROM modules";
if (!empty($search_query)) {
    $sql_count .= " WHERE nama_praktikum LIKE '%$search_query%'";
}
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_modules = $row_count['total'];

// Calculate total number of pages
$total_pages = ceil($total_modules / $results_per_page);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lab SIMI</title>
    <link href="../assets/img/jakarta-logo.png" rel="icon">
    <link href="../assets/img/jakarta-logo.png" rel="apple-touch-icon">
    <link rel="stylesheet" href="assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        .card-img-top {
            width: 286px;
            height: 180px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <?php include("sidebar.php"); ?>
        <div class="body-wrapper">
            <?php include("header.php"); ?>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">Welcome back,
                            <?= htmlspecialchars($_SESSION['fname']) ?>!</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <form method="get" action="">
                                <div class="input-group ">
                                    <input type="text" class="form-control " name="search" placeholder="Search by Course Name" value="<?= htmlspecialchars($search_query) ?>">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </form>
                            <a href="create_module.php" class="btn btn-primary m-0">Add Module</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Meeting Name</th>
                                        <th>Description</th>
                                        <th>File</th>
                                        <th>Link</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result_modules->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['nama_praktikum']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nama_pertemuan']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                                        // Display file link
                                        $file_path = '../uploads/modules/' . htmlspecialchars($row['file_upload']);
                                        if (file_exists($file_path) && strtolower(pathinfo($file_path, PATHINFO_EXTENSION)) === 'pdf') {
                                            echo "<td><a href='$file_path' target='_blank'>View PDF</a></td>";
                                        } else {
                                            echo "<td>No file uploaded</td>";
                                        }
                                        echo "<td>" . htmlspecialchars($row['link']) . "</td>";
                                        echo "<td>
                                                <a href='edit_module.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-info'>Edit</a>
                                                <a href='javascript:void(0);' onclick='confirmDeleteModule(\"" . urlencode($row['id']) . "\");' class='btn btn-sm btn-danger'>Delete</a>
                                            </td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-end">
                                <?php
                                $prev_page = $current_page - 1;
                                if ($prev_page >= 1) {
                                    echo "<li class='page-item'><a class='page-link' href='?page=$prev_page&search=" . urlencode($search_query) . "'>Previous</a></li>";
                                }
                                for ($page = 1; $page <= $total_pages; $page++) {
                                    echo "<li class='page-item " . ($page == $current_page ? 'active' : '') . "'><a class='page-link' href='?page=$page&search=" . urlencode($search_query) . "'>$page</a></li>";
                                }
                                $next_page = $current_page + 1;
                                if ($next_page <= $total_pages) {
                                    echo "<li class='page-item'><a class='page-link' href='?page=$next_page&search=" . urlencode($search_query) . "'>Next</a></li>";
                                }
                                ?>
                            </ul>
                        </nav>
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
        function confirmDeleteModule(moduleId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this module!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteModule(moduleId);
                }
            });
        }

        function deleteModule(moduleId) {
            $.ajax({
                type: 'POST',
                url: 'delete_module.php',
                data: {
                    id: moduleId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to delete module. Please try again later.',
                    });
                }
            });
        }
    </script>
</body>

</html>