<?php
// Start session to ensure user is authenticated
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');  // Redirect to login if not authenticated
    exit();
}

require_once 'database.php';  // Include your database connection

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_job'])) {
    // Extract data from POST
    $job_id = $_POST['job_id'];
    $job_title = $_POST['job_title'];
    $job_description = $_POST['job_description'];
    $location = $_POST['location'];
    $budget = $_POST['budget'];

    // Validate and sanitize inputs (basic example)
    $job_title = filter_var($job_title, FILTER_SANITIZE_STRING);
    $job_description = filter_var($job_description, FILTER_SANITIZE_STRING);
    $location = filter_var($location, FILTER_SANITIZE_STRING);
    $budget = filter_var($budget, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Prepare an update statement
    $sql = "UPDATE jobs SET job_title = ?, job_description = ?, location = ?, budget = ? WHERE id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ssssd", $job_title, $job_description, $location, $budget, $job_id);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            echo "Record updated successfully.";
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
    
    // Close connection
    mysqli_close($conn);
    
    // Redirect back to view jobs or dashboard page
    header("Location: view_jobs.php");
    exit();
} else {
    // If not a POST request, redirect to the form or error page
    header("Location: error.php"); // Consider creating an error handling page
    exit();
}
?>
