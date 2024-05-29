<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Job</title>
</head>
<body>
    <h1>Edit Job</h1>
    <?php
    // Get job details based on job_id
    require_once 'database.php';
    $job_id = $_GET['job_id'];
    $query = "SELECT * FROM jobs WHERE id = $job_id";
    $result = mysqli_query($conn, $query);
    $job = mysqli_fetch_assoc($result);

    ?>
    <form action="update_job.php" method="post">
        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
        <input type="text" name="job_title" value="<?php echo $job['job_title']; ?>" required>
        <textarea name="job_description" required><?php echo $job['job_description']; ?></textarea>
        <input type="text" name="location" value="<?php echo $job['location']; ?>" required>
        <input type="text" name="budget" value="<?php echo $job['budget']; ?>">
        <button type="submit" name="update_job">Update Job</button>
    </form>
</body>
</html>
