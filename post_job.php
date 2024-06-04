<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Database connection details
$hostname = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "login_register";

// Connect to the database
$conn = mysqli_connect($hostname, $dbUser, $dbPassword, $dbName);

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form for job details was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize job form data
    $data = [];
    foreach (['titre', 'description', 'nom_artisan', 'phone', 'email', 'wilaya', 'adresse', 'date_debut', 'date_fin', 'budget'] as $key) {
        $data[$key] = mysqli_real_escape_string($conn, $_POST[$key]);
    }

    // Adding user_id to the data array for insertion
    $data['user_id'] = $_SESSION['user'];  // Assuming $_SESSION['user'] stores the logged-in user's ID

    // Prepare and execute insertion of job details into jobs table
    $stmt = mysqli_prepare($conn, "INSERT INTO jobs (titre, description, nom_artisan, phone, email, wilaya, adresse, date_debut, date_fin, budget, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die('MySQL prepare error: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'ssssssssssi', ...array_values($data));
    mysqli_stmt_execute($stmt);
    $job_id = mysqli_stmt_insert_id($stmt);
    mysqli_stmt_close($stmt);

    // Handle image uploads
    if (isset($_FILES["img"])) {
        $num_files = count($_FILES['img']['name']);
        
        // Loop through each uploaded file
        for($i = 0; $i < $num_files; $i++) {
            $check = getimagesize($_FILES["img"]["tmp_name"][$i]);
    
            if($check !== false) {
                $uploadOk = 1;
            } else {
                echo "File is not an image.<br>";
                $uploadOk = 0;
            }
    
            if($uploadOk == 1){
                $image = $_FILES['img']['tmp_name'][$i];
                $imgContent = addslashes(file_get_contents($image));
                $query_img = "INSERT INTO tbl_image (image, job_id) VALUES ('$imgContent', '$job_id')";
                if(mysqli_query($conn, $query_img)){
                } else {
                    echo "Error uploading image: " . mysqli_error($conn) . "<br>";
                }
            }
        }
    }
}

// Close the database connection
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
    <title>Poster Une Annonce</title>
    <style>
       .subscribe-form input {
    width: 92%
}
        .container-inscription textarea  {
             padding: 20px;
             width: 100%;
        }
        .container-post-job {
            margin-top: 45px;
        }
.container-inscription {
    text-align: center;
 margin-bottom: 10px;
 margin-top: -35px;
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
.reteur-btn{
            text-align: center;
            margin: 20px 0 10px 0;
           
        }
        .reteur-btn .button a {
            width: 100%;
      
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
</head>
<body>
<div class="header">
        <div class="container">
            <nav id="home" class="navstyle">
                <a class="logo" href="index.html"><img src="./images/logo.png" alt="Hirafee"></a> 
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


    <div class="container-post-job">
    <section class="container-inscription">
        <header>
            <h1>Poster une annonce</h1>
            
        </header>
        <?php if(isset($_POST['post']) && $_SERVER['REQUEST_METHOD'] == 'POST' && empty($error_message)): ?>
            <p class="success-message" style="background-color: green; color: white; padding: 10px; margin-bottom: 20px;">L'annonce a été publiée avec succès!</p>
            <style>.container-inscription .form .input-box, .container-inscription .form button { display: none; }
        .footer {

  bottom: 0;
  width: 100%;
}
.reteur-btn {
    display: none;
}
        </style>
        <div class="input-box">
                    <a href="dashboard.php" class="button" style="background-color: #ef7900; color: white; padding: 10px;">Retour au tableau de bord</a>
</div>
        <?php endif; ?>
        <?php if(isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="post_job.php" method="post" class="form" enctype="multipart/form-data">
            <section class="input-box">
                <label>Titre pour l'annonce *</label>
                <input type="text" name="titre" placeholder="Titre de l'annonce" required />
            </section>
            <section class="input-box">
                <label>Description *</label>
                <textarea name="description" rows="10" cols="100%" placeholder="Description de l'annonce" required></textarea>
            </section>
            <section class="input-box">
    <label>Métier de l'artisan recherché *</label>
    <select name="nom_artisan" required>
        <option value="Chauffagiste">Chauffagiste</option>
        <option value="Electricien">Electricien</option>
        <option value="Peintre">Peintre</option>
        <option value="Plombier">Plombier</option>
        <option value="Autre">Autre</option>
    </select>
</section>

            <section class="input-box">
                <label>Numéro de téléphone *</label>
                <input type="tel" name="phone" placeholder="Numéro de téléphone" required />
            </section>
            <section class="input-box">
                <label>Email *</label>
                <input type="email" name="email" placeholder="Email" required />
            </section>
            <section class="input-box"> 
            <label>Wilaya *</label>
<select name="wilaya" required>
    <option value="">Select Wilaya</option>
    <option value="Adrar">Adrar</option>
    <option value="Chlef">Chlef</option>
    <option value="Laghouat">Laghouat</option>
    <option value="Oum El Bouaghi">Oum El Bouaghi</option>
    <option value="Batna">Batna</option>
    <option value="Béjaïa">Béjaïa</option>
    <option value="Biskra">Biskra</option>
    <option value="Béchar">Béchar</option>
    <option value="Blida">Blida</option>
    <option value="Bouira">Bouira</option>
    <option value="Tamanrasset">Tamanrasset</option>
    <option value="Tébessa">Tébessa</option>
    <option value="Tlemcen">Tlemcen</option>
    <option value="Tiaret">Tiaret</option>
    <option value="Tizi Ouzou">Tizi Ouzou</option>
    <option value="Algiers">Algiers</option>
    <option value="Djelfa">Djelfa</option>
    <option value="Jijel">Jijel</option>
    <option value="Sétif">Sétif</option>
    <option value="Saïda">Saïda</option>
    <option value="Skikda">Skikda</option>
    <option value="Sidi Bel Abbès">Sidi Bel Abbès</option>
    <option value="Annaba">Annaba</option>
    <option value="Guelma">Guelma</option>
    <option value="Constantine">Constantine</option>
    <option value="Médéa">Médéa</option>
    <option value="Mostaganem">Mostaganem</option>
    <option value="M'Sila">M'Sila</option>
    <option value="Mascara">Mascara</option>
    <option value="Ouargla">Ouargla</option>
    <option value="Oran">Oran</option>
    <option value="El Bayadh">El Bayadh</option>
    <option value="Illizi">Illizi</option>
    <option value="Bordj Bou Arréridj">Bordj Bou Arréridj</option>
    <option value="Boumerdès">Boumerdès</option>
    <option value="El Tarf">El Tarf</option>
    <option value="Tindouf">Tindouf</option>
    <option value="Tissemsilt">Tissemsilt</option>
    <option value="El Oued">El Oued</option>
    <option value="Khenchela">Khenchela</option>
    <option value="Souk Ahras">Souk Ahras</option>
    <option value="Tipaza">Tipaza</option>
    <option value="Mila">Mila</option>
    <option value="Aïn Defla">Aïn Defla</option>
    <option value="Naâma">Naâma</option>
    <option value="Aïn Témouchent">Aïn Témouchent</option>
    <option value="Ghardaïa">Ghardaïa</option>
    <option value="Relizane">Relizane</option>
    <option value="Timimoun">Timimoun</option>
    <option value="Bordj Badji Mokhtar">Bordj Badji Mokhtar</option>
    <option value="Ouled Djellal">Ouled Djellal</option>
    <option value="Béni Abbès">Béni Abbès</option>
    <option value="In Salah">In Salah</option>
    <option value="In Guezzam">In Guezzam</option>
    <option value="Touggourt">Touggourt</option>
    <option value="Djanet">Djanet</option>
    <option value="El M'Ghair">El M'Ghair</option>
    <option value="El Meniaa">El Meniaa</option>
</select>
</section>

            <section class="input-box">
                <label>Adresse *</label>
                <input type="text" name="adresse" placeholder="Adresse" required />
            </section>
            <section class="input-box">
                <label>Date début *</label>
                <input type="date" name="date_debut" required />
            </section>
            <section class="input-box">
                <label>Date fin *</label>
                <input type="date" name="date_fin" required />
            </section>
            <section class="input-box">
                <label>Budget *</label>
                <input type="number" name="budget" placeholder="Budget" required />
            </section>
           
            <section class="input-box">
    <label>Images *</label>
    <input type="file" name="img[]" id="img" multiple accept="image/*" required>
</section>
 

            <!-- Other input fields for phone number, email, wilaya, adresse, images upload, date début, date fin, budget -->
            <button type="submit" name="post">Poster</button>
            <div class="reteur-btn">
                    <a href="dashboard.php" class="button" style="  text-align:center; color: whibluete; padding: 10px;">Retour au tableau de bord</a>
</div>
        </form>
    </section>
   

</div>






</body>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var connexionLink = document.getElementById("connexion-link");

        connexionLink.addEventListener("click", function() {
            // Redirect to main page
            window.location.href = "index.html";
        });
    });
</script>


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
                                <a href="index.html"><img src="./images/hirafee-white.png" class="img-fluid" alt="logo"></a>
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
    
</footer>

</html>             