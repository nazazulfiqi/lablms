<?php
session_start(); // Start the session
session_destroy(); // Destroy the session
echo "<script>
alert('You have been logged out.');
window.location = 'login.php';
</script>";
?>
