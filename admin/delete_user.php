<?php
// Ensure the request method is POST (or GET if that's what you're using)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize the user ID parameter
    $userId = $_POST['id'] ?? null;
    if (!$userId) {
        echo json_encode(['status' => 'error', 'message' => 'User ID not provided']);
        exit;
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
        echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
        exit;
    }

    // Prepare SQL statement
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting user: ' . $conn->error]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>