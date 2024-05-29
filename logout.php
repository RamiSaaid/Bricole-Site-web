<?php
session_start(); // Start the session to access session variables

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the main index.html page
header("Location: index.html");
exit();
?>
