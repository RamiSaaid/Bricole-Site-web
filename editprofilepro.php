<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";

// Fetch user details from prousers table instead of users table
$userId = $_SESSION['user']; // Assume user's ID is stored in session
$stmt = mysqli_prepare($conn, "SELECT * FROM prousers WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "Données utilisateur non trouvées.";
    exit;
}

// Close the statement early to avoid open connection
mysqli_stmt_close($stmt);

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
    $metier = $_POST["metier"];
    $nom_commerce = $_POST["nom_commerce"];
    $str_jur = $_POST["str_jur"];
    $siret = $_POST["siret"];

    // Build the SQL query dynamically to include the password only if it is provided
    $sql = "UPDATE prousers SET prenom=?, nom=?, email=?, mobile=?, nomrue=?, adresse=?, ville=?, codepost=?, metier=?, nom_commerce=?, str_jur=?, siret=?"
           . (!empty($password) ? ", password=?" : "") . " WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        // Bind parameters dynamically
        $params = [$prenom, $nom, $email, $mobile, $nomrue, $adresse, $ville, $codepost, $metier, $nom_commerce, $str_jur, $siret];
        $types = 'ssssssssssss'; // 12 string parameters
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $params[] = $hashed_password;
            $types .= 's'; // add string parameter for password
        }
        $params[] = $userId;
        $types .= 'i'; // add integer parameter for userId

        mysqli_stmt_bind_param($stmt, $types, ...$params);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Vos informations ont été mises à jour avec succès.";
            header("Location: dashboardpro.php"); // Redirect to the professional dashboard
            exit();
        } else {
            $message = "Erreur lors de la mise à jour des informations : " . mysqli_error($conn);
        }
    } else {
        $message = "Erreur lors de la préparation de la mise à jour : " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

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
                <a class="logo" href="index.html"><img src="./images/logo.png" alt="Bricole"></a> 
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
            <h1>Modifier le profil professionnel</h1>
        </header>
        <?php if (!empty($message)) : ?>
            <p style="background-color: green; color: white; padding: 10px; margin-bottom: 20px; text-align: center;"><?php echo $message; ?></p>

            <style>
                form .input-box, .save-button { display: none; }
                .reteur-btn a { display: none; }
                .save-button { margin-top: 10px; }
                .footer { position: fixed; bottom: 0; width: 100%; }
                .container-inscription .container { margin-top: 20px; }
            </style>
            <div class="input-box">
                <a href="dashboardpro.php" class="button" style="background-color: orange; color: white; padding: 10px;">Retour au tableau de bord professionnel</a>
            </div>
        <?php endif; ?>
        <form action="editprofilepro.php" method="post" class="form">
    <section class="input-box">
        <label for="prenom">Prénom:</label>
        <input type="text" name="prenom" placeholder="Prénom" required value="<?php echo htmlspecialchars($user['prenom']); ?>" />
    </section>
    <section class="input-box">
        <label for="nom">Nom:</label>
        <input type="text" name="nom" placeholder="Nom" required value="<?php echo htmlspecialchars($user['nom']); ?>" />
    </section>
    <section class="input-box">
        <label for="email">Email:</label>
        <input type="email" name="email" placeholder="Adresse email" required value="<?php echo htmlspecialchars($user['email']); ?>" />
    </section>
    <section class="input-box">
        <label for="mobile">Numéro de téléphone:</label>
        <input type="text" name="mobile" placeholder="Numéro de téléphone" required value="<?php echo htmlspecialchars($user['mobile']); ?>" />
    </section>
    <section class="input-box">
        <label for="data_naissance">Date de naissance:</label>
        <input type="date" name="data_naissance" placeholder="Date de naissance" required value="<?php echo htmlspecialchars($user['data_naissance']); ?>" />
    </section>
    <section class="input-box">
        <label for="sexe">Sexe:</label>
        <select name="sexe" required>
            <option value="<?php echo htmlspecialchars($user['sexe']); ?>" selected><?php echo htmlspecialchars($user['sexe']); ?></option>
            <option value="Homme">Homme</option>
            <option value="Femme">Femme</option>
        </select>
    </section>
    <section class="input-box">
        <label for="metier">Métier:</label>
        <input type="text" name="metier" placeholder="Métier" required value="<?php echo htmlspecialchars($user['metier']); ?>" />
    </section>
    <section class="input-box">
        <label for="nom_commerce">Nom commercial:</label>
        <input type="text" name="nom_commerce" placeholder="Nom commercial" required value="<?php echo htmlspecialchars($user['nom_commerce']); ?>" />
    </section>
    <section class="input-box">
        <label for="str_jur">Structure juridique:</label>
        <input type="text" name="str_jur" placeholder="Structure juridique" required value="<?php echo htmlspecialchars($user['str_jur']); ?>" />
    </section>
    <section class="input-box">
        <label for="siret">Numéro de la carte d'identité nationale:</label>
        <input type="text" name="siret" placeholder="Numéro d'identité" required value="<?php echo htmlspecialchars($user['siret']); ?>" />
    </section>
    <section class="input-box">
        <label for="nomrue">Adresse (n° et rue):</label>
        <input type="text" name="nomrue" placeholder="Adresse (n° et rue)" required value="<?php echo htmlspecialchars($user['nomrue']); ?>" />
    </section>
    <section class="input-box">
        <label for="adresse">Complément d'adresse:</label>
        <input type="text" name="adresse" placeholder="Complément d'adresse" required value="<?php echo htmlspecialchars($user['adresse']); ?>" />
    </section>
    <section class="input-box">
        <label for="ville">Ville:</label>
        <input type="text" name="ville" placeholder="Ville" required value="<?php echo htmlspecialchars($user['ville']); ?>" />
    </section>
    <section class="input-box">
        <label for="codepost">Code postal:</label>
        <input type="text" name="codepost" placeholder="Code postal" required value="<?php echo htmlspecialchars($user['codepost']); ?>" />
    </section>
     <!-- Section mot de passe -->
     <section class="input-box">
        <label for="current_password">Mot de passe actuel :</label>
        <input type="password" name="current_password" placeholder="Mot de passe actuel" required />
        <label for="password">Nouveau mot de passe (laisser vide si non modifié) :</label>
        <input type="password" name="password" placeholder="Nouveau mot de passe" />
    </section>
    <button type="submit" name="update">Mettre à jour</button>
   
    <div class="reteur-btn">
                    <a href="dashboardpro.php" class="button" style="  text-align:center; color: whibluete; padding: 10px;">Retour au tableau de bord</a>
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
                                <a href="bricolepro.php"><img src="./images/hirafee-white.png" class="img-fluid" alt="logo"></a>
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
            window.location.href = "bricolepro.php";
        });
    });
</script> 
</footer>

</html>             