<?php
session_start(); // Start the session at the top of the file
error_reporting(E_ALL);
ini_set('display_errors', 1);
$errors = array(); // Define an array to store errors

if (isset($_POST["submit"])) {
    $prenom = $_POST["prenom"];
    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $data_naissance = $_POST["data_naissance"];
    $sexe = $_POST["sexe"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"]; // Capture confirmed password
    $nomrue = $_POST["nomrue"];
    $adresse = $_POST["adresse"];
    $ville = $_POST["ville"];
    $codepost = $_POST["codepost"];
    $metier = $_POST["metier"];
    $nom_commerce = $_POST["nom_commerce"];
    $str_jur = $_POST["str_jur"];
    $siret = $_POST["siret"];

    // Check if any required field is empty
    if (empty($prenom) || empty($nom) || empty($email) || empty($mobile) || empty($data_naissance) ||
        empty($sexe) || empty($password) || empty($confirm_password) || empty($nomrue) || empty($adresse) || empty($ville) || empty($codepost) ||
        empty($metier) || empty($nom_commerce) || empty($str_jur) || empty($siret)) {
        array_push($errors, "Veuillez vous assurer que tous les champs sont remplis.");
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Format d'email invalide.");
    }

    // Check password strength
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        array_push($errors, "Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial.");
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        array_push($errors, "Les mots de passe ne correspondent pas.");
    }

    if (empty($errors)) {
        require_once "database.php";
        $sql_check_email = "SELECT * FROM prousers WHERE email = ?";
        $stmt_check_email = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt_check_email, $sql_check_email)) {
            mysqli_stmt_bind_param($stmt_check_email, "s", $email);
            mysqli_stmt_execute($stmt_check_email);
            mysqli_stmt_store_result($stmt_check_email);
            if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
                array_push($errors, "L'email existe déjà.");
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO prousers (prenom, nom, email, mobile, data_naissance, sexe, password, nomrue, adresse, ville, codepost, metier, nom_commerce, str_jur, siret) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssssssssssssss", $prenom, $nom, $email, $mobile, $data_naissance, $sexe, $passwordHash, $nomrue, $adresse, $ville, $codepost, $metier, $nom_commerce, $str_jur, $siret);
                    mysqli_stmt_execute($stmt);
                    $_SESSION['success_message'] = "Inscription réussie ! Bienvenue sur votre tableau de bord.";
                    header("Location: dashboardpro.php");
                    exit();
                } else {
                    die("Erreur SQL : " . mysqli_error($conn));
                }
            }
        } else {
            die("Erreur SQL : " . mysqli_error($conn));
        }
    }

    // Display errors if any
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
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
    <link rel="stylesheet" href="css/normalize.css">
    <!-- FONT awesome library-->
    <link rel="stylesheet" href="css/all.min.css">
    <!-- main template css -->
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" href="./images/icon.png">
    <!-- google fonts-->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="login.js"></script>    
    <title>Inscription</title>
    <style>
        .subscribe-form input {
    width: 92%
}
.alert-success {
font-size: 50px;
z-index: 999;
background-color: green;
color: white;
padding: 20px;
margin: 5px;
text-align: center;
}
.alert-danger {
font-size: 20px;
background-color: red;
color: white;
margin: 0;

text-align: center;

}
.container-inscription {
    text-align: center;
}

.input-box {
   
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
                <a class="logo" href="index.html"><img src="./images/logo.png" alt="Hirafee"></a> 
                <div class="flinks">
                    <li>
                        <a id="connexion-link" class="connexion dropdown-toggle">
                            <i class="fas fa-check"></i>
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
        <header>Mes informations professionnelles</header>

        <form action="inscriptionpro.php" method="post" class="form">
            <div class="input-box">
                <label>Mon prénom *</label>
                <input type="text" name="prenom" placeholder="Précisez votre prénom" required />
            </div>
            <div class="input-box">
                <label>Mon nom *</label>
                <input type="text" name="nom" placeholder="Précisez votre nom de famille" required />
            </div>
            <div class="input-box">
                <label>Mon email *</label>
                <input type="email" name="email" placeholder="Enter email address" required />
            </div>
            <div class="column">
                <div class="input-box">
                    <label>Mon téléphone mobile *</label>
                    <input type="tel" name="mobile" placeholder="Votre téléphone" required />
                </div>
                <div class="input-box">
                    <label>Date de naissance *</label>
                    <input type="date" name="data_naissance" required />
                </div>
            </div>
            <div class="gender-box">
                <label for="sexe">Sexe *</label>
                <select name="sexe" id="sexe" required>
                    <option value="">Select your gender</option>
                    <option value="homme">Homme</option>
                    <option value="femme">Femme</option>
                </select>
            </div>
            <div class="input-box">
                <label>Métier principal *</label>
                <input type="text" name="metier" placeholder="Votre métier principal" required />
            </div>
            <div class="input-box">
                <label>Nom commercial *</label>
                <input type="text" name="nom_commerce" placeholder="Nom commercial" required />
            </div>
            <div class="input-box">
                <label>Structure juridique *</label>
                <select name="str_jur" required>
                    <option value="">Sélectionnez votre structure</option>
                    <option value="Entreprise individuelle">Entreprise individuelle</option>
                    <option value="Association">Association</option>
                    <option value="Auto-entrepreneur">Auto-entrepreneur</option>
                </select>
            </div>
            <div class="input-box">
                <label>Numéro de la carte d'identité nationale( Siret Pour les sociétés ):</label>
                <input type="text" name="siret" placeholder="Numéro d'identité" maxlength="18" minlength="14" />
            </div>
            <div class="input-box">
                <label for="password">Mot de passe *</label>
                <input type="password" name="password" id="password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial." required>
                     </div>
                     <div class="input-box">
                <label for="confirm_password">Confirmer le mot de passe *</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                   </div>
                  <input type="checkbox" id="showPassword" />
                 <label for="showPassword">Afficher le mot de passe</label>
            <div class="input-box address">
                <label>Mon adresse (n° et rue) *</label>
                <input type="text" name="nomrue" placeholder="Numéro et nom de votre rue" required />
                <input type="text" name="adresse" placeholder="Adresse (complément)" required />
                <div class="column">
                    <input type="text" name="ville" placeholder="Ville *" required />
                </div>
                <div class="column">
                    <input type="text" name="codepost" placeholder="Code Postal *" required />
                </div>
            </div>
            <button type="submit" name="submit">Enregistrer mes informations</button>
        </form>
    </section>
    <script>
    var password = document.getElementById("password"),
        confirm_password = document.getElementById("confirm_password");

    function validatePassword(){
        if(password.value !== confirm_password.value) {
            confirm_password.setCustomValidity("Les mots de passe ne correspondent pas.");
        } else {
            confirm_password.setCustomValidity('');
        }
    }

    password.onchange = validatePassword;
    confirm_password.onkeyup = validatePassword;

    document.getElementById('showPassword').onclick = function() {
        if (this.checked) {
            password.type = "text";
            confirm_password.type = "text";
        } else {
            password.type = "password";
            confirm_password.type = "password";
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
                                <a href="index.html"><img src="./images/hirafee-white.png" class="img-fluid" alt="logo"></a>
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
                            <li><a href="index.html#recherche">Recherche</a></li>
                             <li><a href="index.html#mecanisme">Mécanisme</a></li>
                            <li><a href="index.html#aboutus">Qui sommes-nous ?</a></li>
                            <li><a href="index.html#transparence">Transparence</a></li>
                            <li><a href="index.html#evaluations">Évaluations</a></li>
                             <li><a href="index.html#partners">Nos Partenaires</a></li>
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