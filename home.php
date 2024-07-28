<?php
session_start(); // Start the session

include("include/sidebar.php");

// Check if the user is logged in
if (isset($_SESSION['id'])) {
    // Get user role from the session
    $role = $_SESSION['role'];

    // Redirect the user based on their role
    if ($role == 'ADMIN') {
        // Redirect to admin dashboard
        header("Location: index.php");
        exit(); // Ensure script execution stops after redirection
    } elseif ($role == 'USER') {
    }
}
?>