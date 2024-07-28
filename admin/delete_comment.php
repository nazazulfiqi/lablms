<?php
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'ADMIN') {
    http_response_code(403); // Forbidden
    echo json_encode(['status' => 'error', 'message' => 'You are not authorized to perform this action.']);
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
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Check if the 'id' parameter is set and is numeric
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $comment_id = intval($_POST['id']);

    // Prepare the DELETE SQL statement
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $comment_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Comment deleted successfully.']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete comment.']);
    }

    $stmt->close();
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Invalid comment ID.']);
}

$conn->close();
