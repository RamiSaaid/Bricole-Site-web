<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

require 'database.php';

// Job ID from GET request
$job_id = $_GET['job_id'] ?? null; // Using null coalescing operator for PHP 7+

if (!$job_id) {
    die("Job ID not specified.");
}

// Fetch job details
$stmt = mysqli_prepare($conn, "SELECT * FROM jobs WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $job_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$job = mysqli_fetch_assoc($result);

if (!$job) {
    die("No job found with ID: $job_id");
}

// Check for POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $titre = mysqli_real_escape_string($conn, $_POST['titre']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    // Add other fields as necessary



    $update_sql = "UPDATE jobs SET titre = ?, description = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, 'ssi', $titre, $description, $job_id);
    mysqli_stmt_execute($update_stmt);

    if (mysqli_stmt_affected_rows($update_stmt) > 0) {
        $_SESSION['message'] = "Job updated successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        echo "No changes were made.";
    }
}

mysqli_close($conn);
?>
<?php
session_start();
require 'database.php'; // Ensure this file correctly sets up a connection to your database

// Check if the job ID is passed and is valid
if (isset($_GET['job_id']) && is_numeric($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    // Fetch job details along with images
    $stmt = mysqli_prepare($conn, "SELECT * FROM jobs WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $job_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $job = mysqli_fetch_assoc($result);
    if (!$job) {
        echo "No job found with ID: $job_id";
        exit;
    }

    // Fetch images for the job
    $img_query = "SELECT id, image FROM tbl_image WHERE job_id = ?";
    $img_stmt = mysqli_prepare($conn, $img_query);
    mysqli_stmt_bind_param($img_stmt, 'i', $job_id);
    mysqli_stmt_execute($img_stmt);
    $img_result = mysqli_stmt_get_result($img_stmt);
} else {
    echo "Job ID not specified or invalid.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Update job details directly without extra sanitization
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $nom_artisan = $_POST['nom_artisan'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $wilaya = $_POST['wilaya'];
    $adresse = $_POST['adresse'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $budget = $_POST['budget'];

    // Prepare and bind parameters to the SQL statement
    $update_stmt = mysqli_prepare($conn, "UPDATE jobs SET titre=?, description=?, nom_artisan=?, phone=?, email=?, wilaya=?, adresse=?, date_debut=?, date_fin=?, budget=? WHERE id=?");
    mysqli_stmt_bind_param($update_stmt, 'ssssssssssi', $titre, $description, $nom_artisan, $phone, $email, $wilaya, $adresse, $date_debut, $date_fin, $budget, $job_id);
    if (mysqli_stmt_execute($update_stmt)) {
        // Successfully updated
        $_SESSION['message'] = "Vos informations sur l'offre ont été mises à jour avec succès.";
        header("Location: dashboard.php");  // Optional: Redirect to dashboard after update
        exit();
    } else {
        $_SESSION['message'] = "Erreur lors de la mise à jour de l'offre: " . mysqli_error($conn);
    }


    // Delete selected images
    if (!empty($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $delete_id) {
            $delete_query = "DELETE FROM tbl_image WHERE id = ?";
            $delete_stmt = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($delete_stmt, 'i', $delete_id);
            mysqli_stmt_execute($delete_stmt);
        }
    }

    // Handle new image uploads
    if (!empty($_FILES['new_images']['name'][0])) {
        foreach ($_FILES['new_images']['tmp_name'] as $key => $image_tmp) {
            if ($_FILES['new_images']['error'][$key] === UPLOAD_ERR_OK) {
                $check = getimagesize($image_tmp);
                if ($check !== false) {
                    $imageContent = file_get_contents($image_tmp);  // Get binary data of the image
                    $insert_image = mysqli_prepare($conn, "INSERT INTO tbl_image (job_id, image) VALUES (?, ?)");
                    mysqli_stmt_bind_param($insert_image, 'is', $job_id, $imageContent);
                    mysqli_stmt_execute($insert_image);
                } else {
                    echo "File is not an image.<br>";
                }
            } else {
                echo "Error uploading file: " . $_FILES['new_images']['error'][$key] . "<br>";
            }
        }
    }

    mysqli_stmt_close($update_stmt);
    header("Location: dashboard.php"); // Redirect to dashboard after update
    exit();
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
    <link rel="stylesheet" href="css/view_jobs.css">

    <link rel="icon" href="./images/icon.png">
    <!-- google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="login.js"></script>    
    <title>Edit job offres</title>
    <style>
        .delete-images {
    display: flex;
    align-items: center;
    margin-bottom: 10px; /* Adds space between image rows */
    border: 1px solid #ddd; /* Optional: adds a subtle border around each item */
    padding: 10px;
    border-radius: 5px; /* Rounded corners for a softer look */
    background-color: #f9f9f9; /* Light background for each item */
}

.delete-images img {
    margin-right: 15px; /* Space between the image and the checkbox */
    width: 100px; /* Fixed width */
    height: 100px; /* Fixed height */
    object-fit: cover; /* Ensures the image covers the area without stretching */
    border-radius: 5px; /* Rounded corners for the image */
}

.delete-images label {
    margin-left: 10px; /* Space between checkbox and label text */
    user-select: none; /* Prevent text selection */
}

.delete-images input[type="checkbox"] {
    cursor: pointer; /* Pointer cursor on hover */
}

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
            <h1>Edit Job Details</h1>
        </header>
        <form action="edit_offres.php?job_id=<?php echo $job_id; ?>" method="post" enctype="multipart/form-data" class="form">
        
            <section class="input-box">
                <label for="titre">Title:</label>
                <input type="text" id="titre" name="titre" required value="<?php echo htmlspecialchars($job['titre']); ?>">
            </section>

            <section class="input-box">
            <h3>Images actuelles :</h3>

        <?php while ($img = mysqli_fetch_assoc($img_result)): ?>
            <div class="delete-images">
    <img src="data:image/jpeg;base64,<?php echo base64_encode($img['image']); ?>" alt="Image">
    <input type="checkbox" id="delete_image_<?php echo $img['id']; ?>" name="delete_images[]" value="<?php echo $img['id']; ?>">
    <label for="delete_image_<?php echo $img['id']; ?>">Cocher pour supprimer</label>
</div>

        <?php endwhile; ?>
    </section>

            <!-- Section to add new images 
            <section class="input-box">
                <label for="new_images">Add New Images:</label>
                <input type="file" id="new_images" name="new_images[]" multiple accept="image/*">
            </section> -->

            <section class="input-box">
                <label>Description *</label>
                <textarea name="description" rows="10" cols="100%" placeholder="Description de l'annonce" required><?php echo htmlspecialchars($job['description']); ?></textarea>
            </section>

            <section class="input-box">
                <label for="nom_artisan">Artisan's Name:</label>
                <select id="nom_artisan" name="nom_artisan" required>
                    <option value="">Please select</option>
                    <option value="Chauffagiste" <?php echo ($job['nom_artisan'] == 'Chauffagiste') ? 'selected' : ''; ?>>Chauffagiste</option>
                    <option value="Electricien" <?php echo ($job['nom_artisan'] == 'Electricien') ? 'selected' : ''; ?>>Electricien</option>
                    <option value="Peintre" <?php echo ($job['nom_artisan'] == 'Peintre') ? 'selected' : ''; ?>>Peintre</option>
                    <option value="Plombier" <?php echo ($job['nom_artisan'] == 'Plombier') ? 'selected' : ''; ?>>Plombier</option>
                    <option value="Autre" <?php echo ($job['nom_artisan'] == 'Autre') ? 'selected' : ''; ?>>Autre</option>
                </select>
            </section>

            <section class="input-box">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required value="<?php echo htmlspecialchars($job['phone']); ?>">
            </section>

            <section class="input-box">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($job['email']); ?>">
            </section>

            <section class="input-box">
                <label for="wilaya">Region (Wilaya):</label>
                <input type="text" id="wilaya" name="wilaya" required value="<?php echo htmlspecialchars($job['wilaya']); ?>">
            </section>

            <section class="input-box">
                <label for="adresse">Address:</label>
                <input type="text" id="adresse" name="adresse" required value="<?php echo htmlspecialchars($job['adresse']); ?>">
            </section>

            <section class="input-box">
                <label for="date_debut">Start Date:</label>
                <input type="date" id="date_debut" name="date_debut" required value="<?php echo $job['date_debut']; ?>">
            </section>

            <section class="input-box">
                <label for="date_fin">End Date:</label>
                <input type="date" id="date_fin" name="date_fin" required value="<?php echo $job['date_fin']; ?>">
            </section>

            <section class="input-box">
                <label for="budget">Budget:</label>
                <input type="number" id="budget" name="budget" required value="<?php echo $job['budget']; ?>">
            </section>

            <button type="submit" name="update" class="save-button">Update Job</button>
        </form>
    </div>
</section>

<div class="reteur-btn">
                    <a href="dashboard.php" class="button" style="  text-align:center; color: whibluete; padding: 10px;">Retour au tableau de bord</a>
</div>






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
    
</footer>

</body>
</html>

