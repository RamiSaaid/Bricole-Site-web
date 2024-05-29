<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Image Upload</title>
</head>
<body>
    <h1>Upload Images</h1>
    
    <!-- Image upload form -->
    <form action="upload.php" method="post" enctype="multipart/form-data">
        Select image to upload:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload Image" name="submit">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
        require_once 'database.php';  // Ensure this file correctly sets up a connection to your database
        $image = $_FILES['fileToUpload']['tmp_name'];
        $imgContent = addslashes(file_get_contents($image));

        // Insert image content into database
        $insert = $conn->query("INSERT INTO tbl_image (image) VALUES ('$imgContent')");
        if($insert){
            echo "File uploaded successfully.<br>";
        } else {
            echo "File upload failed, please try again.<br>";
        }
        $conn->close();
    }
    ?>
</body>
</html>
