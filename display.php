<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Display Images</title>
</head>
<body>
    <h1>Display Images</h1>

    <?php
    require_once 'database.php';  // Ensure this file correctly sets up a connection to your database

    // Display images from the database
    $query = "SELECT image FROM tbl_image";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image']) . '" style="width: 200px; height: auto; margin: 10px;"/><br>';
        }
    } else {
        echo 'No images found in the database.';
    }

    $conn->close();
    ?>
</body>
</html>
