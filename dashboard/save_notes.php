<?php
session_start();

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

// Check if note is being saved
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_note'])) {
    $videoId = $_POST['video_id'];
    $userId = $_SESSION['id'];
    $notes = $_POST['notes'];

    // Check if a note already exists for this video and user
    $checkSql = "SELECT id FROM notes WHERE video_id = ? AND user_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $videoId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Note exists, update it
        $noteId = $result->fetch_assoc()['id'];
        $updateSql = "UPDATE notes SET notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("si", $notes, $noteId);
        $stmt->execute();
    } else {
        // Note does not exist, insert a new one
        $insertSql = "INSERT INTO notes (video_id, user_id, notes, created_at, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("iis", $videoId, $userId, $notes);
        $stmt->execute();
    }

    if ($stmt->affected_rows > 0) {
        header("Location: video.php?id=" . $videoId . "&success=note_saved");
    } else {
        header("Location: video.php?id=" . $videoId . "&error=save_failed");
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
