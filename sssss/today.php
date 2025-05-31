<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <link rel="stylesheet" href="css/style1.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.menu ul li').forEach(item => {
                item.addEventListener('click', () => {
                    document.querySelectorAll('.menu ul li').forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                });
            });
        });
    </script>
</head>

<body>
    <div class="menu">
        <ul>
            <li class="active">
                <div class="header">
                    <img src="img/logo.png" alt="Logo">
                    <h1>FaceDetect</h1>
                </div>
                <a href="page_reussie.php">Accueil</a>
                <ul class="submenu">
                    <li><a href="today.php">Aujourd'hui</a></li>
                    <li><a href="heir.php">Hier</a></li>
                    <li><a href="affichage_detections.php">Rechercher une date</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Paramètres</a>
                <ul class="submenu">
                    <li><a href="add_person.html">Ajouter une personne</a></li>
                    <li><a href="delet_person.html">Supprimer une personne</a></li>
                    <li><a href="personne.php">Liste des personnes</a></li>
                    <li><a href="add_user.html">Ajouter un utilisateur</a></li>
                    <li><a href="delet_user.html">Supprimer un utilisateur</a></li>
                    <li><a href="modi_user.php">Modifier un utilisateur</a></li>
                    <li><a href="modif_password.html">Changer le mot de passe</a></li>
                </ul>
            </li>
            <li><a href="index.html">Déconnexion</a></li>
        </ul>
    </div>
    <div class="content">
        <h1>Résultats de la détection d'aujourd'hui</h1>
        <form method="get" class="rech">
            <label for="search">Rechercher par nom :</label>
            <input type="text" id="search" name="search">
            <input type="submit" value="Rechercher">
        </form>

        <?php
        $servername = 'localhost';
        $username = 'root';
        $password = '12344321';
        $dbname = 'pfe';

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $today = date('Y-m-d');
        $searchName = $_GET["search"] ?? '';
        $sql = "SELECT * FROM detection WHERE date_detection = '$today'";
        if (!empty($searchName)) {
            $sql .= " AND nom_visage LIKE '%$searchName%'";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Date</th><th>Heure</th><th>Nom du visage</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["date_detection"] . "</td>";
                echo "<td>" . $row["heure_detection"] . "</td>";
                echo "<td>" . $row["nom_visage"] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "Aucun résultat trouvé pour aujourd'hui.";
        }

        $conn->close();
        ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
