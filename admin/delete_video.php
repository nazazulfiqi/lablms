<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    // Return error response if not logged in
    $response = array(
        'status' => 'error',
        'message' => 'User not logged in.'
    );
    echo json_encode($response);
    exit();
}

// Check if video id is provided
if (!isset($_POST['id'])) {
    // Return error response if video id is missing
    $response = array(
        'status' => 'error',
        'message' => 'Video ID not provided.'
    );
    echo json_encode($response);
    exit();
}

// Sanitize and validate video id
$id_video = $_POST['id']; // Assuming the video id is sent via POST

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$dbname = "dblms"; // Database name for videos (change as per your setup)

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Return error response if database connection fails
    $response = array(
        'status' => 'error',
        'message' => 'Connection failed: ' . $conn->connect_error
    );
    echo json_encode($response);
    exit();
}

// Prepare SQL statement to delete video
$sql_delete = "DELETE FROM videos WHERE id_video = ?";
$stmt = $conn->prepare($sql_delete);

if (!$stmt) {
    // Return error response if SQL preparation fails
    $response = array(
        'status' => 'error',
        'message' => 'SQL preparation error: ' . $conn->error
    );
    echo json_encode($response);
    exit();
}

// Bind parameters and execute the statement
$stmt->bind_param("i", $id_video); // Assuming id_video is an integer
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Video deleted successfully
    $response = array(
        'status' => 'success',
        'message' => 'Video deleted successfully.'
    );
} else {
    // No rows affected, likely the video with given id doesn't exist
    $response = array(
        'status' => 'error',
        'message' => 'Failed to delete video. Video may not exist or an error occurred.'
    );
}

// Close statement and connection
$stmt->close();
$conn->close();

// Return JSON response
echo json_encode($response);
?>
