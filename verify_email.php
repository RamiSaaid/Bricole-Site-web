<?php
session_start();
require_once "database.php";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = mysqli_prepare($conn, "UPDATE users SET is_verified = 1 WHERE verification_token = ?");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['success_message'] = "Your email has been verified successfully. You can now login.";
        header("Location: login.php");
    } else {
        echo "Failed to verify email. Invalid link or email already verified.";
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo "No token provided.";
}
?>
