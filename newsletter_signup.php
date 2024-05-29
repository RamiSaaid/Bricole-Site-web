<?php
require_once 'database.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email address
    $email = $_POST['email'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Display error message if email is invalid
        echo '<div style="background-color: #ffcccc; padding: 10px; border: 1px solid #ff0000; border-radius: 5px; font-size: 18px; text-align: center; margin-bottom: 20px;">Format d\'email invalide</div>';
    } else {
        // Insert email into the newsletters table
        $stmt = $conn->prepare("INSERT INTO newsletters (email) VALUES (?)");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                // Display success message if email is valid and inserted successfully
                echo '<div style="background-color: #ccffcc; padding: 10px; border: 1px solid #00ff00; border-radius: 5px; font-size: 18px; text-align: center; margin-bottom: 20px;">Merci de vous abonner! Votre email est : ' . htmlspecialchars($email) . '</div>';
            } else {
                // Display error message if insertion failed
                echo '<div style="background-color: #ffcccc; padding: 10px; border: 1px solid #ff0000; border-radius: 5px; font-size: 18px; text-align: center; margin-bottom: 20px;">Erreur lors de l\'abonnement. Veuillez réessayer plus tard.</div>';
            }
            $stmt->close();
        } else {
            echo '<div style="background-color: #ffcccc; padding: 10px; border: 1px solid #ff0000; border-radius: 5px; font-size: 18px; text-align: center; margin-bottom: 20px;">Erreur lors de la préparation de la requête. Veuillez réessayer plus tard.</div>';
        }
    }
}

mysqli_close($conn); // Close the database connection
?>

<div style="text-align: center;">
    <form method="POST" action="" style="display: inline-block; text-align: left;">
        <div style="margin-bottom: 10px;">
            <label for="email" style="display: block; text-align: center;">Email:</label>
            <input type="email" name="email" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
        </div>
        <button type="submit" style="background-color: #ff8000; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; display: block; margin: 0 auto;">S'abonner</button>
    </form>
    <button style="background-color: #ff8000; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; display: block; margin: 20px auto;">
        <a href="index.html" style="text-decoration: none; color: #fff; text-align: center;">Retourner au site web</a>
    </button>
</div>
