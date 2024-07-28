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

// Check if file ID is set
if (isset($_GET['file_id'])) {
    $file_id = intval($_GET['file_id']);
    $sql = "SELECT file_upload, file_name FROM modules WHERE id = $file_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fileContent = $row['file_upload'];
        $fileName = $row['file_name'];

        if ($fileContent) {
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"$fileName\"");
            echo $fileContent;
            exit();
        } else {
            echo "File content not available.";
        }
    } else {
        echo "File not found.";
    }
} else {
    echo "No file ID specified.";
}

// Close the connection
$conn->close();
?>
