// confirm.php
$token = $_GET['token'];

// Database lookup to find a matching token
$sql = "SELECT * FROM users WHERE token = ? AND is_active = 0";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        // Token matches, activate the account
        $sql_update = "UPDATE users SET is_active = 1 WHERE token = ?";
        $stmt_update = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt_update, $sql_update)) {
            mysqli_stmt_bind_param($stmt_update, "s", $token);
            mysqli_stmt_execute($stmt_update);
            echo "Your account has been activated!";
        }
    } else {
        echo "Invalid or expired activation link";
    }
}
