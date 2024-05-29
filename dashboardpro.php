<?php
session_start(); // Always start with session_start()

if (!isset($_SESSION["user"])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}

require_once "database.php"; // Your database connection file

// Display success message if it exists
if (isset($_SESSION['success_message'])): ?>
    <div style="background-color: green; color: white; padding: 10px; margin-bottom: 20px;">
        <?php 
        echo $_SESSION['success_message'];
        unset($_SESSION['success_message']); // Clear the message after displaying it
        ?>
    </div>
<?php endif; ?>

<!-- Rest of your HTML for the dashboard goes here -->

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
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" href="./images/icon.png">
    <!-- google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="login.js"></script>    
    <title>Tableau de bord Pro</title>
    <style>
         
    .subscribe-form input {
    width: 92%
}
    
        .alert-success {
font-size: 20px;
z-index: 999;
background-color: green;
color: white;
padding: 20px;
margin: 5px;
text-align: center;
}

    .fa-sign-out-alt {
  color: #ef7900;
 
}
#connexion-link {
    cursor: pointer; /* Set default cursor to pointer */
}
#connexion-link:hover {
    cursor: pointer; /* Change cursor to pointer on hover */
}
    </style>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var connexionLink = document.getElementById("connexion-link");

        connexionLink.addEventListener("click", function() {
            // Redirect to main page
            window.location.href = "index.html";
        });
    });
</script>
</head>
<body>
<?php
    session_start();
    

    if (isset($_SESSION['success_message'])) {
        echo "<div id='successMessage' style='background-color: green; text-align:center;  color: white; padding: 10px; margin-bottom: 20px;'>" . $_SESSION['success_message'] . "</div>";
        unset($_SESSION['success_message']); // Clear the message after displaying it
    }
    ?>

<div class="header">
        <div class="container">
            <nav id="home" class="navstyle">
                <a class="logo" href="bricolepro.php"><img src="./images/logo.png" alt="Hirafee"></a> 
                <div class="flinks">
                    <li>
                        <a id="connexion-link" class="connexion dropdown-toggle" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion 
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

    <section class="container-inscription">
        <div class="container">
            <header>
                <h1>Tableau de bord de l'artisan</h1>
                <?php
if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success'>{$_SESSION['success_message']}</div>";
    unset($_SESSION['success_message']); // Clear the message after displaying it
}
?>

            </header>
            <div class="section-container">
                <h2><i class="fas fa-user"></i>Gérez votre profil</h2>
                <a href="editprofilepro.php" class="button">Modifier le profil</a>
            </div>
            <div class="section-container">
    <h2><i class="fas fa-briefcase"></i>Offres sur lesquelles vous travaillez actuellement</h2>
    <a href="offres_travail.php" class="button">Voir les offres</a>
</div>
            <div class="section-container">
                <h2><i class="fas fa-eye"></i>Consulter les offres publiées</h2>
                <a href="tous_les_offres.php" class="button">
Voir les offres</a>
            </div>
        </div>
    </section>

    <script>
        window.onload = function() {
            setTimeout(function() {
                var messageDiv = document.getElementById('successMessage');
                if (messageDiv) {
                    messageDiv.style.display = 'none';
                }
            }, 5000); // 5000 milliseconds = 5 seconds
        };
        document.addEventListener('DOMContentLoaded', function() {
            var successMessage = document.getElementById('successMessage');
            // Show the success message
            successMessage.style.display = 'block';
            // Hide the success message after 6 seconds
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 6000);
    </script>


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
                                <span>mail@hirafee.com</span>
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
                                <a href="bricolepro.php"><img src="./images/hirafee-white.png" class="img-fluid" alt="logo"></a>
                            </div>
                            <div class="footer-text">
                                <p> Hiraf-ee simplifie la recherche d'artisans de confiance en Algérie.
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
                                <h3>HIRAF-EE</h3>
                            </div>
                            <ul>
                              
                                <li><a href="bricolepro.php#faq">Recherche</a></li>
                                <li><a href="bricolepro.php#mecanisme">Mécanisme</a></li>
                                <li><a href="bricolepro.php#recherche">Qui sommes-nous ?</a></li>
                                <li><a href="bricolepro.php#transparence">Transparence</a></li>
                                 <!-- <li><a href="#">Recherche33</a></li>  -->
                                <li><a href="bricolepro#evaluations">Évaluations</a></li>
                               <!--  <li><a href="#">Expert Team</a></li>  -->
                                <li><a href="bricolepro#partners">Nos Partenaires</a></li>
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
                                <form action="#">
                                    <input type="text" placeholder="Email Address">
                                    <button><i class="fab fa-telegram-plane"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
</footer>

</html>             