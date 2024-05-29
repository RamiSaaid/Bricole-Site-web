} else {
                echo "<hr style='border: 1px solid white; margin-top: 35px;'>";
                echo "<div style='margin-top: 20px; text-align: center; color: white; background-color: #ef7900; padding: 20px;'>";
                echo "<h3 style='color: white;'>Confirmation pour les professionnels</h3>";
                echo "<p>Veuillez accepter les règles et confirmer que ce travail vous convient.</p>";
                echo "<form method='POST' action=''>";
                echo "<input type='hidden' name='job_id' value='" . htmlspecialchars($job['id']) . "'>";
                echo "<input type='hidden' name='pro_user_id' value='" . htmlspecialchars($pro_user_id) . "'>";
                echo "<input type='checkbox' name='agree' required> J'accepte les règles et je confirme que ce travail me convient.<br>";
                echo "<button type='submit' style='color: white; text-decoration: none; border: none; background-color: white; color: #ef7900; padding: 5px 10px; margin-top: 10px;'>Confirmer</button>";
                echo "</form>";
                echo "</div>";
            }