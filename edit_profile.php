<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";
$message = ""; // Initialize message variable

// Fetch user details
$userId = $_SESSION['user']; // Assume user's ID is stored in session
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "User data not found.";
    exit;
}

// Handle profile update
if (isset($_POST["update"])) {
    $prenom = $_POST["prenom"];
    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $password = $_POST["password"];
    $nomrue = $_POST["nomrue"];
    $adresse = $_POST["adresse"];
    $ville = $_POST["ville"];
    $codepost = $_POST["codepost"];

    // Only update password if a new one is provided
    $sql = "UPDATE users SET prenom=?, nom=?, email=?, mobile=?, nomrue=?, adresse=?, ville=?, codepost=?". (!empty($password) ? ", password=?" : "") . " WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        $bind_params = [$prenom, $nom, $email, $mobile, $nomrue, $adresse, $ville, $codepost];
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $bind_params[] = $hashed_password;
        }
        $bind_params[] = $userId;
        mysqli_stmt_bind_param($stmt, str_repeat('s', count($bind_params)), ...$bind_params);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Vos informations ont été mises à jour avec succès.";
            header("Location: dashboard.php"); // Redirect to dashboard or stay on the same page
            exit();
        } else {
            $message = "Error updating record: " . mysqli_error($conn);
        }
    } else {
        $message = "Error preparing statement: " . mysqli_error($conn);
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
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
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" href="./images/icon.png">
    <!-- google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="login.js"></script>    
    <title>Edit profile</title>
    <style>

.subscribe-form input {
    width: 92%
}
.container-inscription {
    text-align: center;
}

.input-box {
    margin-bottom: 20px;
    display: inline-block;
    text-align: left;
}

.icon-container {
    display: inline-block;
    vertical-align: middle;
    margin-right: 10px;
}

.input-box h2 {
    display: inline-block;
    margin: 0;
    vertical-align: middle;
}

.input-fields {
    margin-top: 5px;
}

.save-button {
    margin-top: 20px; /* Adjust the margin-top value to add space between the green message and the button */
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
.reteur-btn{
            text-align: center;
            margin: 10px 0 10px 0;
        }
    </style>
</head>
<body>
<div class="header">
        <div class="container">
            <nav id="home" class="navstyle">
                <a class="logo" href="bricole.php"><img src="./images/logo.png" alt="Hirafee"></a> 
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
            <h1>Edit Profile</h1>
        </header>
        <?php if (isset($_SESSION['message'])): ?>
    <p class="success-message" style="background-color: green; color: white; padding: 10px; margin-bottom: 20px;">
        <?php 
        echo $_SESSION['message']; // Display the session message
        unset($_SESSION['message']); // Unset the session message after displaying it
        ?>
    </p>
                <style>form .input-box, .save-button { display: none; } 
                .reteur-btn a {
                    display:none;
                }
            .save-button {margin-top: 10px;}
            .footer {
  position: fixed;
  bottom: 0;
  width: 100%;
}
.container-inscription .container {
    margin-top: 20px;
}
            </style>
                <div class="input-box">
                    <a href="dashboard.php" class="button" style="background-color: orange; color: white; padding: 10px;">Retour au tableau de bord</a>
</div>
            <?php endif; ?>
            <form action="edit_profile.php" method="post" class="form">
            <form action="edit_profile.php" method="post" class="form">
            <!-- Name section -->
            <section class="input-box">
        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" placeholder="Prénom" required value="<?php echo htmlspecialchars($user['prenom']); ?>" />
    </section>
    <section class="input-box">
        <label for="nom">Nom de famille :</label>
        <input type="text" name="nom" placeholder="Nom de famille" required value="<?php echo htmlspecialchars($user['nom']); ?>" />
    </section>

    <!-- Section email -->
    <section class="input-box">
        <label for="email">Email :</label>
        <input type="email" name="email" placeholder="Adresse email" required value="<?php echo htmlspecialchars($user['email']); ?>" />
    </section>

    <!-- Section téléphone -->
    <section class="input-box">
        <label for="mobile">Numéro de téléphone :</label>
        <input type="text" name="mobile" placeholder="Numéro de téléphone" required value="<?php echo htmlspecialchars($user['mobile']); ?>" />
    </section>

    <!-- Section mot de passe -->
    <section class="input-box">
        <label for="current_password">Mot de passe actuel :</label>
        <input type="password" name="current_password" placeholder="Mot de passe actuel" required />
        <label for="password">Nouveau mot de passe (laisser vide si non modifié) :</label>
        <input type="password" name="password" placeholder="Nouveau mot de passe" />
    </section>

    <!-- Section adresse -->
    <section class="input-box">
        <label for="nomrue">Adresse (rue) :</label>
        <input type="text" name="nomrue" placeholder="Adresse (rue)" required value="<?php echo htmlspecialchars($user['nomrue']); ?>" />
        <input type="text" name="adresse" placeholder="Complément d'adresse" required value="<?php echo htmlspecialchars($user['adresse']); ?>" />
        <input type="text" name="ville" placeholder="Ville" required value="<?php echo htmlspecialchars($user['ville']); ?>" />
        <input type="text" name="codepost" placeholder="Code postal" required value="<?php echo htmlspecialchars($user['codepost']); ?>" />
    </section>

            <button type="submit" name="update" class="save-button">Save Changes</button>
            <div class="reteur-btn">
                    <a href="dashboard.php" class="button" style="  text-align:center; color: whibluete; padding: 10px;">Retour au tableau de bord</a>
</div>
        </form>
    </div>
</section>




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
                                <a href="bricole.php"><img src="./images/hirafee-white.png" class="img-fluid" alt="logo"></a>
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
                              
                                <li><a href="#recherche">Recherche</a></li>
                                <li><a href="#mecanisme">Mécanisme</a></li>
                                <li><a href="#">Qui sommes-nous ?</a></li>
                                <li><a href="#transparence">Transparence</a></li>
                                 <!-- <li><a href="#">Recherche33</a></li>  -->
                                <li><a href="#evaluations">Évaluations</a></li>
                               <!--  <li><a href="#">Expert Team</a></li>  -->
                                <li><a href="#partners">Nos Partenaires</a></li>
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
        <script>
    document.addEventListener("DOMContentLoaded", function() {
        var connexionLink = document.getElementById("connexion-link");

        connexionLink.addEventListener("click", function() {
            // Redirect to main page
            window.location.href = "index.html";
        });
    });
</script> 
</footer>

</html>             