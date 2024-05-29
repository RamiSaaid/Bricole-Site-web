<?php
session_start();

// Check if the user just registered, using a session variable set during registration
if (!isset($_SESSION['registered']) || $_SESSION['registered'] !== true) {
    header('Location: register.php'); // Redirect to registration page if accessed inappropriately
    exit();
}

// Clear the registration session variable
unset($_SESSION['registered']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px auto;
            max-width: 700px;
            padding: 20px;
            text-align: center;
        }
        .message {
            background-color: #D4EDDA;
            border: 1px solid #C3E6CB;
            color: #155724;
            margin: 20px 0;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="message">
        <h1>Inscription Réussie!</h1>
        <p>Bienvenue sur notre site! Votre inscription a été complétée avec succès.</p>
        <p><a href="login.php">Cliquez ici</a> pour vous connecter à votre compte.</p>
    </div>
</body>
</html>
