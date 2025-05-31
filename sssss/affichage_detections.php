<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Affichage des Détections par Date</title>
    <link rel="stylesheet" type="text/css" href="css/style1.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>

    <style>
        .centered-form {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 2em 0;
        }

        .menu {
            background-color:rgb(220, 228, 238);
            padding: 1em;
        }

        .menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu ul li {
            margin: 0.5em 0;
        }

        .menu ul li a {
            color: #fff;
            text-decoration: none;
        }

        .menu .header img {
            max-width: 50px;
            vertical-align: middle;
        }

        .menu .header h1 {
            display: inline-block;
            margin-left: 0.5em;
            color: #fff;
        }

        .content h1 {
            margin-bottom: 1em;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
        }

        .content table, .content th, .content td {
            border: 1px solid #ddd;
        }

        .content th, .content td {
            padding: 0.5em;
            text-align: center;
        }

        .content th {
            background-color: #f4f4f4;
        }

        .no-detections {
            color: #d9534f;
        }
    </style>

    <script>
        // JavaScript pour rendre le menu dynamique
        window.addEventListener('DOMContentLoaded', (event) => {
            const menuItems = document.querySelectorAll('.menu ul li');
            menuItems.forEach(item => {
                item.addEventListener('click', () => {
                    menuItems.forEach(i => i.classList.remove('active'));
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
                    <li><a href="affichage_detections.php">Rechercher une Date</a></li>
                </ul>
            </li>
           
            <li>
                <a href="#">Paramètres</a>
                <ul class="submenu">
                    <li><a href="add_person.html">Ajouter une Personne</a></li>
                    <li><a href="delet_person.html">Supprimer une Personne</a></li>
                    <li><a href="personne.php">Liste des Personnes</a></li>
                    <li><a href="add_user.html">Ajouter un Utilisateur</a></li>
                    <li><a href="delet_user.html">Supprimer un Utilisateur</a></li>
                    <li><a href="modi_user.php">Modifier un Utilisateur</a></li> 
                    <li><a href="modif_password.html">Changer Mot de Passe</a></li>
                </ul>
            </li>
            <li><a href="index.html">Déconnexion</a></li>
        </ul>
    </div>

    <div class="content">
        <h1>Affichage des Détections par Date</h1>
        <div class="centered-form">
            <form action="affichage_detections.php" method="POST" class="rech">
                <label for="date">Saisissez une Date :</label>
                <input type="date" id="date" name="date" required>
                <input type="submit" value="Afficher les Détections" class="btn btn-primary">
            </form>
        </div>

        <?php
        // Informations de connexion à la base de données
        $serveur = "localhost";
        $utilisateur = "root";
        $motdepasse = "12344321";
        $base_de_donnees = "pfe";

        // Connexion à la base de données
        $connexion = new mysqli($serveur, $utilisateur, $motdepasse, $base_de_donnees);
        if ($connexion->connect_error) {
            die("Erreur de connexion à la base de données : " . $connexion->connect_error);
        }

        // Récupérer la date saisie par l'utilisateur de manière sécurisée
        if (isset($_POST['date'])) {
            $date = $connexion->real_escape_string($_POST['date']);

            // Requête pour récupérer les détections de la date spécifiée
            $sql = "SELECT * FROM detection WHERE DATE(date_detection) = ?";
            $stmt = $connexion->prepare($sql);
            $stmt->bind_param('s', $date);
            $stmt->execute();
            $resultat = $stmt->get_result();

            // Afficher les détections
            if ($resultat->num_rows > 0) {
                echo "<h2>Détections pour la Date $date :</h2>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Heure</th><th>Nom</th></tr>";
                while ($row = $resultat->fetch_assoc()) {
                    $idDetection = $row['id'];
                    $heureDetection = $row['heure_detection'];
                    $description = $row['nom_visage'];

                    echo "<tr><td>$idDetection</td><td>$heureDetection</td><td>$description</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='no-detections'>Aucune détection n'a été trouvée pour la Date $date.</p>";
            }

            $stmt->close();
        }

        // Fermer la connexion à la base de données
        $connexion->close();
        ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
