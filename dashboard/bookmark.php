<?php
session_start();
header('Content-Type: text/plain');

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$dbname = "dblms";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['video_id']) && isset($_SESSION['id'])) {
    $video_id = $_POST['video_id'];
    $user_id = $_SESSION['id'];

    // Check if the video is already bookmarked
    $stmt = $conn->prepare("SELECT * FROM bookmarks WHERE video_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $video_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Unbookmark the video
        $stmt = $conn->prepare("DELETE FROM bookmarks WHERE video_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $video_id, $user_id);
        $stmt->execute();
        echo 'unbookmarked';
    } else {
        // Bookmark the video
        $stmt = $conn->prepare("INSERT INTO bookmarks (video_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $video_id, $user_id);
        $stmt->execute();
        echo 'bookmarked';
    }

    $stmt->close();
} else {
    echo 'error';
}

$conn->close();
?>
