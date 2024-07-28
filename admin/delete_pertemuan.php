<?php
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'ADMIN') {
    // Return a JSON response indicating an error
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Check if the meeting name is provided
if (!isset($_POST['nama_pertemuan']) || empty(trim($_POST['nama_pertemuan']))) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid meeting name']);
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
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Prepare the SQL statement to delete the meeting
$nama_pertemuan = $_POST['nama_pertemuan'];
$sql_delete = "DELETE FROM pertemuan WHERE nama_pertemuan = ?";
$stmt = $conn->prepare($sql_delete);

if ($stmt) {
    $stmt->bind_param("s", $nama_pertemuan);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Meeting deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting meeting: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the SQL statement']);
}

// Close the connection
$conn->close();
?>
