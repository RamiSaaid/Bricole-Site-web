<?php

// Absolute path to the uploads directory
$targetDir = "/Applications/XAMPP/xamppfiles/htdocs/login/uploads/";

// Check if the uploads directory exists
if (!file_exists($targetDir)) {
    // Attempt to create the directory
    if (!mkdir($targetDir, 0755, true)) {
        die("Failed to create upload directory.");
    }
}

// Verify write permissions
if (!is_writable($targetDir)) {
    die("Upload directory is not writable.");
}

// Database connection details for jobs
$hostname = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "login_register";

// Connect to the jobs database
$conn_jobs = mysqli_connect($hostname, $dbUser, $dbPassword, $dbName);

// Check if the connection was successful
if (!$conn_jobs) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form for uploading images was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle image uploads
    if (isset($_FILES["img"])) {
        // Upload each image to the jobs database
        foreach ($_FILES['img']['name'] as $key => $value) {
            $fileName = basename($_FILES['img']['name'][$key]);
            $targetFilePath = $targetDir . $fileName;

            // Upload image to the server
            if (move_uploaded_file($_FILES['img']['tmp_name'][$key], $targetFilePath)) {
                // Insert image path into jobs table
                $query_img = "INSERT INTO jobs (image_path) VALUES ('$targetFilePath')";
                if (!mysqli_query($conn_jobs, $query_img)) {
                    echo "Error inserting image path into database.";
                }
            } else {
                echo "Error uploading file.";
            }
        }
    }
}

// Close the connection to the jobs database
mysqli_close($conn_jobs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload</title>
</head>
<body>
    <h2>Upload Images</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <input type="file" name="img[]" multiple accept="image/*">
        <button type="submit" name="submit">Upload</button>
    </form>
</body>
</html>
