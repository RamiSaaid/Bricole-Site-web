
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
    <title>Offres en cours</title>
    <style>
        #filter-form label {
            margin-right: 5px;
        }
        #filter-form select {
            margin-right: 5px;
        }
        .update-btn {
            display: inline;

        }
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
#filter-form {
    text-align: center;
    margin-top: 5px;
    margin-bottom: 5px;
}
#filter-form button {
background-color: #ef7900;
color: white;
border-radius: 8px;
border: none;
margin: 0;
padding: 5px;
cursor: pointer;
}
    </style>
</head>
<body>
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
        <h1> Offres sur lesquelles vous travaillez actuellement </h1>
        <div class="reteur-btn">
            <a href="dashboardpro.php" class="button" style="background-color: #ef7900; text-align:center; color: white; padding: 10px; margin-bottom:20px">Retour au tableau de bord pro</a>
        </div>
        <div class="job-container" id="job-listings"> 
        <?php
session_start();

// Check if the user is logged in as a pro user
if (!isset($_SESSION['user']) || $_SESSION['user_type'] != 'pro') {
    header("Location: login.php"); // Redirect to login if not logged in or not a pro user
    exit();
}

require_once 'database.php'; // Include the database connection

// Get the pro user ID from the session
$pro_user_id = $_SESSION['user'];

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

echo "<h2></h2>";

// Query to get jobs the pro user is currently working on
$working_query = "
    SELECT jobs.*
    FROM jobs
    JOIN job_confirmations ON jobs.id = job_confirmations.job_id
    WHERE job_confirmations.pro_user_id = ?
";
$working_stmt = $conn->prepare($working_query);
if ($working_stmt) {
    $working_stmt->bind_param("i", $pro_user_id);
    $working_stmt->execute();
    $working_result = $working_stmt->get_result();

    while ($job = $working_result->fetch_assoc()) {
        $current_status = $job['status'];
        $imageStyle = ($current_status == "Travail terminé") ? "filter: grayscale(100%);" : "";

        echo "<div class='job'>";
        echo "<h2>" . htmlspecialchars($job['titre']) . "</h2>"; // Display job title

        // Fetch all images for the current job
        $img_query = "SELECT image FROM tbl_image WHERE job_id = ?";
        $img_stmt = $conn->prepare($img_query);
        if (!$img_stmt) {
            echo 'SQL prepare failed: ' . htmlspecialchars($conn->error);
            continue;
        }
        $img_stmt->bind_param("i", $job['id']);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();

        $images = [];
        while ($img = $img_result->fetch_assoc()) {
            $images[] = $img['image'];
        }

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

                // Display status section for pro users only
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
                
        // Display other details
        echo '<div style="max-height: 200px; overflow: auto; margin-bottom: 20px;">';
        echo '<p>' . htmlspecialchars($job['description']) . '</p>';
        echo '</div>';

        

        // Display job details
        echo "<p><strong>Artisan:</strong> " . htmlspecialchars($job['nom_artisan']) . "</p>";
        echo "<p><strong>Phone:</strong> " . htmlspecialchars($job['phone']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($job['email']) . "</p>";
        echo "<p><strong>Location:</strong> " . htmlspecialchars($job['wilaya']) . ", " . htmlspecialchars($job['adresse']) . "</p>";
        echo "<p><strong>Date:</strong> " . htmlspecialchars($job['date_debut']) . " to " . htmlspecialchars($job['date_fin']) . "</p>";
        echo "<p><strong>Budget:</strong> " . htmlspecialchars($job['budget']) . "</p>";



        echo "<div style='margin-top: 20px;'>";
        echo "<a href='tel:" . htmlspecialchars($job['phone']) . "' style='margin-right: 20px;'><i class='fas fa-phone'></i> Call</a>";
        echo "<a href='mailto:" . htmlspecialchars($job['email']) . "'><i class='fas fa-envelope'></i> Send Message</a>";
        echo "</div>";
        echo "</div>"; // Close job div
    }
    $working_stmt->close(); // Close statement
} else {
    echo "<p>Error: " . htmlspecialchars($conn->error) . "</p>";
}

mysqli_close($conn); // Close the database connection
?>


</div>




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
                              
                            <li><a href="bricolepro.php#faq">Recherche</a></li>
                                <li><a href="bricolepro.php#mecanisme">Mécanisme</a></li>
                                <li><a href="bricolepro.php#recherche">Qui sommes-nous ?</a></li>
                                <li><a href="bricolepro.php#transparence">Transparence</a></li>
                                 <!-- <li><a href="#">Recherche33</a></li>  -->
                                <li><a href="bricolepro#evaluations">Évaluations</a></li>
                               <!--  <li><a href="#">Expert Team</a></li>  -->
                                <li><a href="bricolepro#partners">Nos Partenaires</a></li>
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
    
 document.addEventListener("DOMContentLoaded", function() {
    var statusDropdowns = document.querySelectorAll("select[name='status']");
    var updateButtons = document.querySelectorAll("button[type='submit']");

    statusDropdowns.forEach(function(dropdown, index) {
        function checkStatus() {
            if (dropdown.value === "Travail terminé") {
                updateButtons[index].style.display = "none";
            } else {
                updateButtons[index].style.display = "inline-block";
            }
        }

        // Check status on page load
        checkStatus();

        // Check status on change
        dropdown.addEventListener("change", checkStatus);
    });
});
        function navigateToArtisan(artisanType) {
            window.location.href = 'tous_les_offres.php?artisan=' + artisanType;
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

// mn hnaaa
let slideIndex = 1;

function plusSlides(n, jobId) {
    showSlides(slideIndex += n, jobId);
}

function showSlides(n, jobId) {
    const slides = $(`#job-${jobId} .mySlides`);
    if (n > slides.length) { slideIndex = 1 }
    if (n < 1) { slideIndex = slides.length }
    slides.each(function(index, slide) {
        $(slide).css('display', 'none');
    });
    $(slides[slideIndex-1]).css('display', 'block');
}

$(document).ready(function() {
    $('#filter-form').on('submit', function(event) {
        event.preventDefault(); // Prevent form from submitting the traditional way

        // Collect filter values
        const nom_artisan = $('#nom_artisan').val();
        const wilaya = $('#wilaya').val();

        // Send AJAX request to PHP script
        $.ajax({
            url: 'filter_jobs.php', // This should point to your PHP script
            type: 'GET',
            data: {
                nom_artisan: nom_artisan,
                wilaya: wilaya
            },
            success: function(response) {
                $('#job-listings').html(response); // Update job listings with the response
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
});

</script>

</body>
</html>
