
<?php
session_start();


echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');

// Check if the user is logged in as either a regular user or a pro user
if (!isset($_SESSION['user']) || (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] != 'user' && $_SESSION['user_type'] != 'pro'))) {
    header("Location: login.php"); // Redirect to login if not logged in or not the correct user type
    exit();
}
require_once 'database.php';


// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status']) && isset($_POST['job_id'])) {
    $newStatus = $_POST['status'];
    $jobId = $_POST['job_id'];
    // Update the job status in the database
    $updateQuery = "UPDATE jobs SET status = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    if (!$updateStmt) {
        die('SQL prepare failed: ' . htmlspecialchars($conn->error));
    }
    $updateStmt->bind_param("si", $newStatus, $jobId);
    $updateStmt->execute();
    if ($updateStmt->affected_rows > 0) {
        echo "<p style='color: green;'></p>";
    } else {
        echo "<p style='color: red;'></p>";
    }
    $updateStmt->close();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status'], $_POST['job_id'])) {
    $status = $_POST['status'];
    $job_id = $_POST['job_id'];

    // Prepare and execute the update statement
    $stmt = mysqli_prepare($conn, "UPDATE jobs SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $status, $job_id);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo "";
    } else {
        echo "Error updating status: " . mysqli_error($conn);
    }
}

$nom_artisan = isset($_GET['nom_artisan']) ? $_GET['nom_artisan'] : "";
$wilaya = isset($_GET['wilaya']) ? $_GET['wilaya'] : "";

// Build the SQL query based on the presence of filters
$query = "SELECT * FROM jobs WHERE 1=1";  // 1=1 is always true, it just simplifies appending conditions

if (!empty($nom_artisan)) {
    $query .= " AND nom_artisan = '" . mysqli_real_escape_string($conn, $nom_artisan) . "'";
}

if (!empty($wilaya)) {
    $query .= " AND wilaya = '" . mysqli_real_escape_string($conn, $wilaya) . "'";
}

$result = mysqli_query($conn, $query);

// Additional logic for specific profession filtering
if(isset($_GET['profession'])) {
    $selectedProfession = $_GET['profession'];

    // Query to retrieve jobs filtered by the selected profession
    $query = "SELECT * FROM jobs WHERE nom_artisan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selectedProfession);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display the filtered results
    while ($job = $result->fetch_assoc()) {
        // Output job details
        echo "<div>";
        echo "<h2>" . $job['titre'] . "</h2>";
        echo "<p>" . $job['description'] . "</p>";
        echo "<p>" . $job['nom_artisan'] . "</p>";
        echo "<p>" . $job['wilaya'] . "</p>";
        echo "</div>";
    }
} else {
    // If profession parameter is not set, retrieve all jobs
    // You can add your existing code here to retrieve all jobs
}

// Error handling and result fetching
if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}

while ($job = mysqli_fetch_assoc($result)) {
    // Display each job as per your existing setup
}


// Check if the profession parameter is set in the URL
if(isset($_GET['profession'])) {
    $selectedProfession = $_GET['profession'];

    // Query to retrieve jobs filtered by the selected profession
    $query = "SELECT * FROM jobs WHERE nom_artisan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selectedProfession);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display the filtered results
    while ($job = $result->fetch_assoc()) {
        // Output job details
    }
} else {
    // If profession parameter is not set, retrieve all jobs
    // You can add your existing code here to retrieve all jobs
}
// Determine the appropriate dashboard based on user type
$dashboardLink = ($_SESSION["user_type"] === 'pro') ? "dashboardpro.php" : "dashboard.php";
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


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
    <title>Toutes les offres</title>
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
                <a class="logo" href="bricole.php"><img src="./images/logo.png" alt="Hirafee"></a> 
                <div class="flinks">
                    <li>
                        <a class="connexion dropdown-toggle" href="logout.php">
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
    <h1>Toutes les offres</h1>

        <div class="reteur-btn">
            <a href="<?php echo $dashboardLink; ?>" class="button" style="background-color: #ef7900; text-align: center; color: white; padding: 10px;">Retour au tableau de bord</a>
        </div>
        <p class="tarifs"> Tous les tarifs sont en dinar algérien (DZD) </p>
        <form id="filter-form" method="GET" action="">
    <label for="metier">Métier:</label>
    <select id="nom_artisan" name="nom_artisan">
        
<option value="">Choisir un Métier</option>
        <option value="Chauffagiste">Chauffagiste</option>
        <option value="Electricien">Electricien</option>
        <option value="Peintre">Peintre</option>
        <option value="Plombier">Plombier</option>
        <option value="Autre">Autre</option>
    </select>
    
    <label for="wilaya">Wilaya:</label>
    <select id="wilaya" name="wilaya">
    <option value="">Wilaya</option>
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
    
    <label for="date_end">Date de fin:</label>
        <input type="date" name="date_end" id="date_end" value="<?php echo isset($_GET['date_end']) ? htmlspecialchars($_GET['date_end']) : ''; ?>">

        <label for="budget_order">Trier par Budget:</label>
        <select name="budget_order" id="budget_order">
            <option value="">Sélectionner</option>
            <option value="asc" <?php echo (isset($_GET['budget_order']) && $_GET['budget_order'] == 'asc') ? 'selected' : ''; ?>>Croissant</option>
            <option value="desc" <?php echo (isset($_GET['budget_order']) && $_GET['budget_order'] == 'desc') ? 'selected' : ''; ?>>Décroissant</option>
        </select>

    <button type="submit">Filter</button>
</form>


        <div class="job-container" id="job-listings">
            
        <?php
session_start();

// Check if the user is logged in as either a regular user or a pro user
if (!isset($_SESSION['user']) || (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] != 'user' && $_SESSION['user_type'] != 'pro'))) {
    header("Location: login.php"); // Redirect to login if not logged in or not the correct user type
    exit();
}

require_once 'database.php'; // Include the database connection

// Initialize filters
$nom_artisan = isset($_GET['nom_artisan']) ? $_GET['nom_artisan'] : "";
$wilaya = isset($_GET['wilaya']) ? $_GET['wilaya'] : "";
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : "";
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : "";
$budget_order = isset($_GET['budget_order']) ? $_GET['budget_order'] : "";

// Build the SQL query based on the presence of filters
$query = "SELECT * FROM jobs WHERE 1=1";  // 1=1 is always true, it just simplifies appending conditions

$params = [];
$types = '';

if (!empty($nom_artisan)) {
    $query .= " AND nom_artisan = ?";
    $types .= 's';
    $params[] = $nom_artisan;
}
if (!empty($wilaya)) {
    $query .= " AND wilaya LIKE ?";
    $types .= 's';
    $params[] = '%' . $wilaya . '%';
}
if (!empty($date_start)) {
    $query .= " AND date_debut >= ?";
    $types .= 's';
    $params[] = $date_start;
}
if (!empty($date_end)) {
    $query .= " AND date_fin <= ?";
    $types .= 's';
    $params[] = $date_end;
}

// Add budget order to the query
if (!empty($budget_order)) {
    $query .= " ORDER BY budget " . ($budget_order == 'asc' ? 'ASC' : 'DESC');
}

$stmt = $conn->prepare($query);
if (!$stmt) {
    die('SQL prepare failed: ' . htmlspecialchars($conn->error));
}

// Bind parameters if any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Handle form submission for pro user confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agree']) && isset($_POST['job_id'])) {
    $confirmed_job_id = $_POST['job_id'];
    $pro_user_id = $_SESSION['user']; // Get pro_user_id from the session

    // Check if job_id exists in jobs table
    $check_job_query = "SELECT id FROM jobs WHERE id = ?";
    $check_job_stmt = $conn->prepare($check_job_query);
    if ($check_job_stmt) {
        $check_job_stmt->bind_param("i", $confirmed_job_id);
        $check_job_stmt->execute();
        $check_job_result = $check_job_stmt->get_result();
        if ($check_job_result->num_rows > 0) {
            // Check if pro_user_id exists in prousers table
            $check_user_query = "SELECT id FROM prousers WHERE id = ?";
            $check_user_stmt = $conn->prepare($check_user_query);
            if ($check_user_stmt) {
                $check_user_stmt->bind_param("i", $pro_user_id);
                $check_user_stmt->execute();
                $check_user_result = $check_user_stmt->get_result();
                if ($check_user_result->num_rows > 0) {
                    // Insert confirmation into the database
                    $insert_query = "INSERT INTO job_confirmations (job_id, pro_user_id) VALUES (?, ?)";
                    $insert_stmt = $conn->prepare($insert_query);
                    if ($insert_stmt) {
                        $insert_stmt->bind_param("ii", $confirmed_job_id, $pro_user_id);
                        if ($insert_stmt->execute()) {
                            // Store the confirmation in the session
                            $_SESSION['confirmed_jobs'][$confirmed_job_id] = $pro_user_id;
                        } else {
                            echo "Error: " . htmlspecialchars($insert_stmt->error);
                        }
                        $insert_stmt->close();
                    } else {
                        echo "Error: " . htmlspecialchars($conn->error);
                    }
                } else {
                    echo "Error: pro_user_id does not exist in prousers table.";
                }
                $check_user_stmt->close();
            } else {
                echo "Error: " . htmlspecialchars($conn->error);
            }
        } else {
            echo "Error: job_id does not exist in jobs table.";
        }
        $check_job_stmt->close();
    } else {
        echo "Error: " . htmlspecialchars($conn->error);
    }
}

// Check if there are results
if ($result) {
    while ($job = $result->fetch_assoc()) {
        $current_status = $job['status'];  // Make sure this line correctly fetches the status from your job array
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

        // Pro user confirmation section
        if ($_SESSION['user_type'] == 'pro' && $current_status != "Travail terminé") {
            $pro_user_id = $_SESSION['user']; // Get pro_user_id from the session
            
            // Check if the current pro user has confirmed the job
            $check_query = "SELECT * FROM job_confirmations WHERE job_id = ?";
            $check_stmt = $conn->prepare($check_query);
            $confirmed = false;
            $confirming_user_id = null;
            if ($check_stmt) {
                $check_stmt->bind_param("i", $job['id']);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                if ($check_result->num_rows > 0) {
                    $confirmed = true;
                    $row = $check_result->fetch_assoc();
                    $confirming_user_id = $row['pro_user_id'];
                }
                $check_stmt->close();
            }

            if ($confirmed) {
                if ($confirming_user_id == $pro_user_id) {
                    echo "<hr style='border: 1px solid white; margin-top: 35px;'>";
                    echo "<div style='margin-top: 20px; text-align: center; color: white; background-color: #ef7900; padding: 20px;'>";
                    echo "<p>Merci pour votre confirmation, et si vous avez des questions, contactez le support.</p>";
                    echo "</div>";
                } else {
                    echo "<hr style='border: 1px solid white; margin-top: 35px;'>";
                    echo "<div style='margin-top: 20px; text-align: center; color: white; background-color: #ef7900; padding: 20px;'>";
                    echo "<p>Un autre artisan travaille déjà sur ce projet.</p>";
                    echo "</div>";
                }
            } else {
                echo "<hr style='border: 1px solid white; margin-top: 35px;'>";
                echo "<div style='margin-top: 20px; text-align: center; color: white; background-color: #ef7900; padding: 20px;'>";
                echo "<h3 style='color: white;'>Confirmation pour les professionnels</h3>";
                echo "<p>Veuillez accepter les règles et confirmer que vous vous engagez à terminer ce travail et à en assumer la responsabilité.</p>";
                echo "<form method='POST' action=''>";
                echo "<input type='hidden' name='job_id' value='" . htmlspecialchars($job['id']) . "'>";
                echo "<input type='hidden' name='pro_user_id' value='" . htmlspecialchars($pro_user_id) . "'>";
                echo "<input type='checkbox' name='agree' required> J'accepte les règles et je confirme que ce travail me convient.<br>";
                echo "<button type='submit' style='color: white; text-decoration: none; border: none; background-color: white; color: #ef7900; padding: 5px 10px; margin-top: 10px;'>Confirmer</button>";
                echo "</form>";
                echo "</div>";
            }
        }

        // Display review if the status is "Travail terminé"
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
                    $review_query = "SELECT review FROM reviews WHERE job_id = ? AND pro_user_id = ?";
                    $review_stmt = mysqli_prepare($conn, $review_query);
                    if ($review_stmt) {
                        mysqli_stmt_bind_param($review_stmt, 'ii', $job['id'], $pro_user_id);
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
                    }
                }
            }
        }

        echo "</div>"; // Close job div
    }
    $stmt->close(); // Close statement
} else {
    echo "<p>No jobs found</p>";
}

mysqli_close($conn); // Close the database connection
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
