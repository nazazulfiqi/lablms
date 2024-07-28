<?php
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

// Initialize search and filter variables
$search = '';
$filter = 'all';

if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
}

if (isset($_GET['filter'])) {
    $filter = $conn->real_escape_string($_GET['filter']);
}

// Construct the SQL query
$sql = "SELECT 
            p.nama_praktikum, 
            p.deskripsi_praktikum,
            (SELECT COUNT(*) FROM videos v WHERE v.nama_praktikum = p.nama_praktikum) AS video_count
        FROM praktikum p
        WHERE p.nama_praktikum LIKE '%" . $search . "%'";

// Apply filter based on the selected option
if ($filter === 'with_videos') {
    $sql .= " HAVING video_count > 0";
} elseif ($filter === 'without_videos') {
    $sql .= " HAVING video_count = 0";
}

// Add sorting by the start of the name based on the search term
if ($search !== '') {
    $sql .= " ORDER BY CASE 
                        WHEN p.nama_praktikum LIKE '" . $search . "%' THEN 0 
                        ELSE 1 
                    END, p.nama_praktikum";
} else {
    $sql .= " ORDER BY p.nama_praktikum";
}

// Execute the query
$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->error);
}

// Fetch total number of courses
$totalCoursesSql = "SELECT COUNT(*) AS total FROM praktikum WHERE nama_praktikum LIKE '%" . $search . "%'";
if ($filter === 'with_videos') {
    $totalCoursesSql .= " HAVING video_count > 0";
} elseif ($filter === 'without_videos') {
    $totalCoursesSql .= " HAVING video_count = 0";
}
$totalCoursesResult = $conn->query($totalCoursesSql);
if (!$totalCoursesResult) {
    die("Error executing query: " . $conn->error);
}
$totalCoursesRow = $totalCoursesResult->fetch_assoc();
$totalCourses = $totalCoursesRow['total'];

// Prepare the data to return
$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode([
    'totalCourses' => $totalCourses,
    'courses' => $courses
]);

// Close the connection
$conn->close();
?>
