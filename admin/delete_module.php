<?php
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'ADMIN') {
    // Return a JSON response indicating an error
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Check if the module ID is provided
if (!isset($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid module ID']);
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

// Prepare the SQL statement to delete the module
$id = $conn->real_escape_string($_POST['id']);
$sql_delete = "DELETE FROM modules WHERE id = '$id'";

// Execute the query
if ($conn->query($sql_delete) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Module deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error deleting module: ' . $conn->error]);
}

// Close the connection
$conn->close();
