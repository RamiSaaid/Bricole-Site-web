<?php
session_start();

// Function to clear all session variables and reset session
function reset_session() {
    session_unset();  // remove all session variables
    session_destroy();  // destroy the session
    session_start();  // start a new session
    session_regenerate_id(true);  // regenerate the session ID
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "database.php";
    
    // Clear previous session to prevent session fixation
    reset_session();

    $email = $_POST["email"];
    $password = $_POST["password"];

    // Attempt to log in as a regular user
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user["password"])) {
            $_SESSION["user"] = $user['id'];
            $_SESSION["user_type"] = 'user';
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Mot de passe incorrect.";
            header("Location: login.php");
            exit();
        }
    }

    // If no regular user was found or password didn't match, check professional users
    $stmt = $conn->prepare("SELECT * FROM prousers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $proUser = $result->fetch_assoc();

    if ($proUser) {
        if (password_verify($password, $proUser["password"])) {
            $_SESSION["user"] = $proUser['id'];
            $_SESSION["user_type"] = 'pro';
            header("Location: dashboardpro.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Mot de passe incorrect.";
            header("Location: login.php");
            exit();
        }
    }

    // If no user was found at all or password did not match
    if (!$user && !$proUser) {
        $_SESSION['error_message'] = "Email invalide ou utilisateur non trouvé.";
        header("Location: login.php");
        exit();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- render all elements nomrally-->
    <link rel="stylesheet" href=".css/normalize.css">
    <!-- FONT awesome library-->
    <link rel="stylesheet" href=".css/all.min.css">
    <!-- main template css -->
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="./images/icon.png">
    <!-- google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="login.js"></script>    
    <title>Connexion</title>
<style>
    .subscribe-form input {
    width: 92%
}

</style>
</head>
<body>
    <div class="header">
        <div class="container">
            <nav id="home" class="navstyle">
                <a class="logo" href="index.html"><img src="./images/logo.png" alt="Hirafee"></a> 
                <div class="flinks">
                    <li>
                        <a id="connexion-link" class="connexion dropdown-toggle" href="inscription.php">
                            <i class="fas fa-check"  ></i>
                            Inscription gratuite  
                        </a>
                    </li>
                    <div class="phonenum">
                        <i class="fas fa-phone"></i>
                        <a href="#Chauffagiste">0554952290</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
<div class="inscription-bdy"> 
    <section class="container-inscription">
        <header>Connexion</header>
        <!-- Error Message Display -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class='alert alert-danger'>
                <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']); // Clear the message after displaying it
                ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="post" class="form">
            <div class="input-box">
                <label>Email *</label>
                <input type="email" name="email" placeholder="Entrez votre adresse email" required />
            </div>
            <div class="input-box">
                <label for="password">Mot de passe *</label>
                <input name="password" type="password" id="password" placeholder="Entrez votre mot de passe" required />
            </div>
            <input type="checkbox" id="showPassword" />
            <label for="showPassword">Afficher le mot de passe</label>
            <button type="submit" name="login">Connexion</button>
        </form>
        <div class="forgetpass"><a href="inscription.php">Vous n'avez pas de compte ?</a></div>
    </section>

    <script>
        document.getElementById('showPassword').onclick = function() {
            if (this.checked) {
                document.getElementById('password').type = "text";
            } else {
                document.getElementById('password').type = "password";
            }
        };
    </script>
</div>


</body>


<footer class="footer">
    <footer class="footer-section">
        <div class="container">
            <div class="footer-cta pt-5 pb-5">
                <div class="row">
                    <div class="col-xl-4 col-md-4 mb-30">
                        <div class="single-cta">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="cta-text">
                                <h4>Trouvez-nous</h4>
                                <span>17 hassen chaouche, Annaba 23000</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 mb-30">
                        <div class="single-cta">
                            <i class="fas fa-phone"></i>
                            <div class="cta-text">
                                <h4>Appelez-nous</h4>
                                <span>0554952290</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 mb-30">
                        <div class="single-cta">
                            <i class="far fa-envelope-open"></i>
                            <div class="cta-text">
                                <h4>Contactez-nous</h4>
                                <span>mail@bricole.com</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-content pt-5 pb-5">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 mb-50">
                        <div class="footer-widget">
                            <div class="footer-logo">
                                <a href="index.html"><img src="images/hirafee-white.png" class="img-fluid" alt="logo"></a>
                            </div>
                            <div class="footer-text">
                                <p> Bricole simplifie la recherche d'artisans de confiance en Algérie.
                                    Trouvez les meilleurs professionnels <br> du bâtiment évalués avec précision. </p>
                            </div>
                            <div class="footer-social-icon">
                                <span>Suivez-nous ! </span>
                                <a href="#"><i class="fab fa-facebook-f facebook-bg"></i></a>
                                <a href="#"><i class="fab fa-tiktok tiktok-bg"></i></a>
                                <a href="#"><i class="fab fa-instagram instagram-bg"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                        <div class="footer-widget">
                            <div class="footer-widget-heading">
                                <h3>Bricole</h3>
                            </div>
                            <ul>
                            <li><a href="index.html#recherche">Recherche</a></li>
                                <li><a href="index.html#mecanisme">Mécanisme</a></li>
                                <li><a href="index.html#aboutus">Qui sommes-nous ?</a></li>
                                <li><a href="index.html#transparence">Transparence</a></li>
                                 <!-- <li><a href="#">Recherche33</a></li>  -->
                                <li><a href="index.html#evaluations">Évaluations</a></li>
                               <!--  <li><a href="#">Expert Team</a></li>  -->
                                <li><a href="index.html#partners">Nos Partenaires</a></li>
                              <!--  <li><a href="#">Latest News</a></li> -->
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 mb-50">
                        <div class="footer-widget">
                            <div class="footer-widget-heading">
                                <h3>Newsletter</h3>
                            </div>
                            <div class="footer-text mb-25">
                                <p>N'oubliez pas de vous abonner à notre newsletter en remplissant le formulaire ci-dessous.</p>
                            </div>
                            <div class="subscribe-form">
                                <form action="newsletter_signup.php" method="POST">
                                    <input type="email" name="email" placeholder="Email Address">
                                    <button type="submit"><i class="fab fa-telegram-plane"></i></button>
                                </form>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
</footer>

</html>             