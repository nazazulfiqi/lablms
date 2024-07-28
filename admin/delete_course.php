<?php
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'ADMIN') {
    // Return a JSON response indicating an error
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Check if the course name is provided
if (!isset($_POST['nama_praktikum'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid course name']);
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
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

// Prepare the SQL statement to delete the course
$nama_praktikum = $conn->real_escape_string($_POST['nama_praktikum']);
$sql_delete = "DELETE FROM praktikum WHERE nama_praktikum = '$nama_praktikum'";

// Execute the query
if ($conn->query($sql_delete) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Course deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error deleting course: ' . $conn->error]);
}

// Close the connection
$conn->close();
?>
