
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
    <title>Vos offres d'emploi</title>
    <style>
        .subscribe-form input {
    width: 92%
}
        .reteur-btn{
            text-align: center;
            margin: 10px 0 10px 0;
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
 /* Style for delete button */
 .delete-button {
        margin-top: 10px;
        width: 100%;
        display: block;
        text-align: center;
        background-color: #dc3545; /* Original background color */
        color: white;
        padding: 10px;
        border: none;
        cursor: pointer; /* Change cursor to hand on hover */
        transition: background-color 0.3s; /* Smooth transition for background color change */
    }

    /* Darken background color on hover */
    .delete-button:hover {
        background-color: #c82333; /* Darkened background color */
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
        <h1> Vos offres d'emploi </h1>
        <?php if (isset($_POST['post']) && $_SERVER['REQUEST_METHOD'] == 'POST' && empty($error_message)): ?>
    <p class="success-message" style="background-color: green; color: white; padding: 10px; margin-bottom: 20px;text-align:center;">L'annonce a été publiée avec succès!</p>
<?php endif; ?>

<?php if (isset($_POST['delete']) && $_SERVER['REQUEST_METHOD'] == 'POST'): ?>
    <p class="success-message" style="background-color: green; color: white; padding: 10px; margin-bottom: 20px;text-align:center;">L'annonce a été supprimée avec succès!</p>
<?php endif; ?>

        <div class="reteur-btn">
            <a href="dashboard.php" class="button" style="background-color: #ef7900; text-align:center; color: white; padding: 10px;">Retour au tableau de bord</a>
        </div>
        <p class="tarifs">Tous les tarifs sont en dinar algérien (DZD)</p>

        <div class="job-container">
        <?php
session_start();
require 'database.php';  // Ensure this file contains your database connection setup

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Handle job deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $job_id = $_POST['job_id'];

    // First, delete the reviews related to the job
    $delete_reviews_query = "DELETE FROM reviews WHERE job_id = ?";
    $delete_reviews_stmt = mysqli_prepare($conn, $delete_reviews_query);
    if ($delete_reviews_stmt) {
        mysqli_stmt_bind_param($delete_reviews_stmt, 'i', $job_id);
        mysqli_stmt_execute($delete_reviews_stmt);
        mysqli_stmt_close($delete_reviews_stmt);
    }

    // Then, delete the job confirmations related to the job
    $delete_confirmations_query = "DELETE FROM job_confirmations WHERE job_id = ?";
    $delete_confirmations_stmt = mysqli_prepare($conn, $delete_confirmations_query);
    if ($delete_confirmations_stmt) {
        mysqli_stmt_bind_param($delete_confirmations_stmt, 'i', $job_id);
        mysqli_stmt_execute($delete_confirmations_stmt);
        mysqli_stmt_close($delete_confirmations_stmt);
    }

    // Finally, delete the job itself
    $delete_job_query = "DELETE FROM jobs WHERE id = ? AND user_id = ?";
    $delete_job_stmt = mysqli_prepare($conn, $delete_job_query);
    if ($delete_job_stmt) {
        mysqli_stmt_bind_param($delete_job_stmt, 'ii', $job_id, $_SESSION['user']);
        mysqli_stmt_execute($delete_job_stmt);

        if (mysqli_stmt_affected_rows($delete_job_stmt) > 0) {
            echo "<p style='color: green;'></p>";
        } else {
            echo "<p style='color: red;'></p>";
        }
        mysqli_stmt_close($delete_job_stmt);
    } else {
        echo "<p style='color: red;'></p>";
    }
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status']) && isset($_POST['job_id'])) {
    $updated_status = $_POST['status'];
    $job_id = $_POST['job_id'];

    $update_query = "UPDATE jobs SET status = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    if ($update_stmt) {
        mysqli_stmt_bind_param($update_stmt, 'si', $updated_status, $job_id);
        if (!mysqli_stmt_execute($update_stmt)) {
            echo "Error: " . htmlspecialchars(mysqli_stmt_error($update_stmt));
        }
        mysqli_stmt_close($update_stmt);
    } else {
        echo "Error: " . htmlspecialchars(mysqli_error($conn));
    }
}

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['review']) && isset($_POST['job_id']) && isset($_POST['pro_user_id'])) {
    $review = $_POST['review'];
    $job_id = $_POST['job_id'];
    $pro_user_id = $_POST['pro_user_id'];

    // Check if the review already exists
    $review_check_query = "SELECT id FROM reviews WHERE job_id = ? AND pro_user_id = ? AND user_id = ?";
    $review_check_stmt = mysqli_prepare($conn, $review_check_query);
    if ($review_check_stmt) {
        mysqli_stmt_bind_param($review_check_stmt, 'iii', $job_id, $pro_user_id, $_SESSION['user']);
        mysqli_stmt_execute($review_check_stmt);
        mysqli_stmt_store_result($review_check_stmt);

        if (mysqli_stmt_num_rows($review_check_stmt) > 0) {
            // Update the existing review
            $update_review_query = "UPDATE reviews SET review = ? WHERE job_id = ? AND pro_user_id = ? AND user_id = ?";
            $update_review_stmt = mysqli_prepare($conn, $update_review_query);
            if ($update_review_stmt) {
                mysqli_stmt_bind_param($update_review_stmt, 'siii', $review, $job_id, $pro_user_id, $_SESSION['user']);
                if (mysqli_stmt_execute($update_review_stmt)) {
                    echo "<p style='color: green;'></p>";
                } else {
                    echo "<p style='color: red;'>Erreur lors de la mise à jour de l'avis. " . htmlspecialchars(mysqli_stmt_error($update_review_stmt)) . "</p>";
                }
                mysqli_stmt_close($update_review_stmt);
            } else {
                echo "<p style='color: red;'>SQL Error: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
            }
        } else {
            // Insert a new review
            $insert_review_query = "INSERT INTO reviews (job_id, pro_user_id, user_id, review) VALUES (?, ?, ?, ?)";
            $insert_review_stmt = mysqli_prepare($conn, $insert_review_query);
            if ($insert_review_stmt) {
                mysqli_stmt_bind_param($insert_review_stmt, 'iiis', $job_id, $pro_user_id, $_SESSION['user'], $review);
                if (mysqli_stmt_execute($insert_review_stmt)) {
                    echo "<p style='color: green;'></p>";
                } else {
                    echo "<p style='color: red;'>Erreur lors de la soumission de l'avis. " . htmlspecialchars(mysqli_stmt_error($insert_review_stmt)) . "</p>";
                }
                mysqli_stmt_close($insert_review_stmt);
            } else {
                echo "<p style='color: red;'>SQL Error: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
            }
        }

        mysqli_stmt_close($review_check_stmt);
    } else {
        echo "<p style='color: red;'>SQL Error: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
    }
}

// Query to retrieve jobs created by the logged-in user
$query = "SELECT * FROM jobs WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($job = mysqli_fetch_assoc($result)) {
    echo "<div class='job' id='job_" . $job['id'] . "'>";
    echo "<h2>" . htmlspecialchars($job['titre']) . "</h2>"; // Display job title

    // Fetch images for the current job
    $img_query = "SELECT image FROM tbl_image WHERE job_id = ?";
    $img_stmt = mysqli_prepare($conn, $img_query);
    mysqli_stmt_bind_param($img_stmt, 'i', $job['id']);
    mysqli_stmt_execute($img_stmt);
    $img_result = mysqli_stmt_get_result($img_stmt);
    $images = [];
    while ($img = mysqli_fetch_assoc($img_result)) {
        $images[] = $img['image'];
    }

    $current_status = $job['status'];  // Make sure this line correctly fetches the status from your job array
    $imageStyle = ($current_status == "Travail terminé") ? "filter: grayscale(100%);" : "";

    // Display slider if images are available
    if (!empty($images)) {
        echo "<div class='slider' id='slider_" . $job['id'] . "' style='position: relative; margin-top: 20px; margin-bottom: 20px;'>";
        foreach ($images as $index => $image) {
            $imageInfo = getimagesizefromstring($image);
            $imageType = image_type_to_mime_type($imageInfo[2]);
            echo "<div class='mySlides fade'>";
            echo "<img src='data:" . $imageType . ";charset=utf-8;base64," . base64_encode($image) . "' style='width:100%; $imageStyle'>";
            echo "</div>";
        }
        echo "<a class='prev' onclick='plusSlides(-1, " . $job['id'] . ")' style='position: absolute; top: 50%; left: 0; transform: translateY(-50%);'>&#10094;</a>";
        echo "<a class='next' onclick='plusSlides(1, " . $job['id'] . ")' style='position: absolute; top: 50%; right: 0; transform: translateY(-50%);'>&#10095;</a>";
        echo "</div>";
    } else {
        echo '<p>No images available</p>';
    }

    echo "<div style='margin-top: 10px; margin-bottom: 20px; text-align: center;'>";
    echo "<form method='post' action=''>";
    echo "<input type='hidden' name='job_id' value='" . htmlspecialchars($job['id']) . "'>";
    echo "<label for='status' style='color:white; margin-right: 20px; font-weight:bold;'>Status:</label>";
    $disabled = ($current_status == "Travail terminé") ? "disabled" : "";
    echo "<select name='status' id='status' style='margin-top: 4px; color: #ef7900; margin-right: 20px; padding:5px; font-weight:bold; border-radius: 30px' $disabled>";

    // Define all possible statuses
    $statuses = [
        "recherche artisan" => "À la recherche d'un artisan",
        "Négociation du budget" => "Négociation du budget",
        "Négociation des dates" => "Négociation des dates",
        "En attente de réponse" => "En attente de réponse",
        "Travail en cours" => "Travail en cours",
        "Travail terminé" => "Travail terminé"
    ];

    // Loop through the statuses and mark the current one as selected
    foreach ($statuses as $key => $value) {
        echo "<option value='" . htmlspecialchars($key) . "'" . ($key == $current_status ? " selected" : "") . ">" . htmlspecialchars($value) . "</option>";
    }

    echo "</select>";
    $buttonStyle = ($current_status == "Travail terminé") ? "display: none;" : "display: inline;";
    echo "<button class='update-btn' type='submit' style='color: white; text-decoration: none; border: none; background-color: #ef7900; padding: 5px 10px;$buttonStyle'>Update Status</button>";
    echo "</form>";
    echo "</div>";

    // Additional job details
    echo '<div style="max-height: 200px; overflow: auto; margin-bottom: 20px;">';
    echo '<p>' . htmlspecialchars($job['description']) . '</p>';
    echo '</div>';
    echo "<p><strong>Artisan:</strong> " . htmlspecialchars($job['nom_artisan']) . "</p>";
    echo "<p><strong>Phone:</strong> " . htmlspecialchars($job['phone']) . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($job['email']) . "</p>";
    echo "<p><strong>Location:</strong> " . htmlspecialchars($job['wilaya']) . ", " . htmlspecialchars($job['adresse']) . "</p>";
    echo "<p><strong>Date:</strong> " . htmlspecialchars($job['date_debut']) . " to " . htmlspecialchars($job['date_fin']) . "</p>";
    echo "<p><strong>Budget:</strong> " . htmlspecialchars($job['budget']) . "</p>";
    echo "<a href='edit_offres.php?job_id=" . $job['id'] . "' style='margin-top: 25px; display: block; text-align: center;'>Edit</a>";

    // Review section if the status is "Travail terminé"
    if ($current_status == "Travail terminé") {
        // Fetch pro user ID who confirmed this job
        $confirm_query = "SELECT pro_user_id FROM job_confirmations WHERE job_id = ?";
        $confirm_stmt = mysqli_prepare($conn, $confirm_query);
        if ($confirm_stmt) {
            mysqli_stmt_bind_param($confirm_stmt, 'i', $job['id']);
            mysqli_stmt_execute($confirm_stmt);
            mysqli_stmt_bind_result($confirm_stmt, $pro_user_id);
            mysqli_stmt_fetch($confirm_stmt);
            mysqli_stmt_close($confirm_stmt);

            if ($pro_user_id) {
                // Fetch existing review if any
                $existing_review = '';
                $review_query = "SELECT review FROM reviews WHERE job_id = ? AND pro_user_id = ? AND user_id = ?";
                $review_stmt = mysqli_prepare($conn, $review_query);
                if ($review_stmt) {
                    mysqli_stmt_bind_param($review_stmt, 'iii', $job['id'], $pro_user_id, $_SESSION['user']);
                    mysqli_stmt_execute($review_stmt);
                    mysqli_stmt_bind_result($review_stmt, $existing_review);
                    mysqli_stmt_fetch($review_stmt);
                    mysqli_stmt_close($review_stmt);

                    if ($existing_review) {
                        echo "<hr style='border: 1px solid #ef7900; margin-top: 20px;'>";
                        echo "<div style='margin-top: 20px; text-align: center;'>";
                        echo "<h3 style='color: #ef7900; margin-bottom:10px;'>Avis sur l'artisan</h3>";
                        echo "<p style='color: #ef7900;'>" . htmlspecialchars($existing_review) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p style='color: red;'>" . htmlspecialchars(mysqli_error($conn)) . "</p>";
                }

                echo "<hr style='border: 1px solid #ef7900; margin-top: 20px;'>";
                echo "<div style='margin-top: 20px; text-align: center;'>";
                echo "<h3 style='color: #ef7900; margin-bottom:10px;'>Écrire un avis</h3>";
                echo "<form method='POST' action=''>";
                echo "<input type='hidden' name='job_id' value='" . htmlspecialchars($job['id']) . "'>";
                echo "<input type='hidden' name='pro_user_id' value='" . htmlspecialchars($pro_user_id) . "'>";
                echo "<textarea name='review' rows='4' style='width: 100%; padding: 10px; border-radius: 5px;' placeholder='Écrivez votre avis ici...'>" . htmlspecialchars($existing_review) . "</textarea><br>";
                echo "<button type='submit' style='color: white; text-decoration: none; border: none; background-color: #ef7900; padding: 10px 20px; margin-top: 10px;'>Soumettre</button>";
                echo "</form>";
                echo "</div>";
            }
        }
    }

    // Move the delete button here, under the review section
    echo "<form method='post' style='text-align: center; margin-top: 20px;'>";
    echo "<input type='hidden' name='job_id' value='" . $job['id'] . "' />";
    echo "<button type='submit' name='delete' class='delete-button'>Delete</button>";
    echo "</form>";

    echo "</div>"; // Close job div
}

mysqli_close($conn);
?>






            </div>
        </div>
    </section>





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
<script>
    function deleteJob(jobId) {
    if (confirm('Are you sure you want to delete this job offer?')) {
        // Perform AJAX request to delete the job offer from the database
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_offer.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    // Check if deletion was successful
                    if (xhr.responseText.trim() === 'success') {
                        // Remove the job offer from the page if deletion is successful
                        var jobElement = document.getElementById('job_' + jobId);
                        if (jobElement) {
                            jobElement.remove();
                        }
                    } else {
                        console.error('Failed to delete job offer:', xhr.responseText);
                        alert('Failed to delete job offer. Please try again later.');
                    }
                } else {
                    console.error('Error:', xhr.status, xhr.statusText);
                    alert('An error occurred while deleting the job offer. Please try again later.');
                }
            }
        };
        xhr.send('job_id=' + jobId);
    }
}

    document.addEventListener("DOMContentLoaded", function() {
        var connexionLink = document.getElementById("connexion-link");

        connexionLink.addEventListener("click", function() {
            // Redirect to main page
            window.location.href = "index.html";
        });
    });




    // display all images 
    var slideIndex = {};

function plusSlides(n, jobId) {
    showSlides(slideIndex[jobId] += n, jobId);
}

function showSlides(n, jobId) {
    var i;
    var slides = document.querySelectorAll('#slider_' + jobId + ' .mySlides');
    if (n > slides.length) {slideIndex[jobId] = 1}    
    if (n < 1) {slideIndex[jobId] = slides.length}
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";  
    }
    slides[slideIndex[jobId]-1].style.display = "block";  
}

// Initialize sliders
document.querySelectorAll('.slider').forEach(slider => {
    var jobId = slider.id.split('_')[1];
    slideIndex[jobId] = 1;
    showSlides(1, jobId); // Show the first image
});
</script>

</body>
</html>
