<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de la Détection</title>
    <link rel="stylesheet" type="text/css" href="css/style1.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
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
    <style>
        .content {
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
            color: #007bff;
        }
        .rech {
            margin-bottom: 20px;
        }
        .rech label {
            margin-right: 10px;
        }
        .rech input[type="text"] {
            padding: 5px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .rech input[type="submit"] {
            padding: 5px 10px;
            border: 1px solid #007bff;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        .rech input[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #dee2e6;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f1f1f1;
        }
    </style>
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
                    <li><a href="modif_password.html">Changer le mot de passe</a><i class="material-icons" data-toggle="tooltip" title="Modifier">&#xE254;</i></li>
                </ul>
            </li>
            <li><a href="index.html">Déconnexion</a></li>
        </ul>
    </div>
    <div class="content">
        <h1>Résultats de la Détection</h1>
        <form method="get" class="rech">
            <label for="search">Rechercher par nom :</label>
            <input type="text" id="search" name="search" placeholder="Nom du visage">
            <input type="submit" value="Rechercher">
        </form>
        <?php
        // Configuration de la connexion à la base de données
        $servername = 'localhost';
        $username = 'root';
        $password = '12344321';
        $dbname = 'pfe';

        // Connexion à la base de données
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Vérification des erreurs de connexion
        if ($conn->connect_error) {
            die("Échec de la connexion : " . $conn->connect_error);
        }

        // Récupération du nom recherché (si spécifié)
        $searchName = $conn->real_escape_string($_GET["search"] ?? '');

        // Construction de la requête SQL avec la clause WHERE pour filtrer par nom et l'ordre de tri DESC
        $sql = "SELECT * FROM detection";
        if (!empty($searchName)) {
            $sql .= " WHERE nom_visage LIKE '%$searchName%'";
        }
        $sql .= " ORDER BY date_detection DESC, heure_detection DESC";

        $result = $conn->query($sql);

        // Affichage des résultats
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Date</th><th>Heure</th><th>Nom du visage</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["date_detection"] . "</td><td>" . $row["heure_detection"] . "</td><td>" . $row["nom_visage"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Aucun résultat trouvé.</p>";
        }

        // Fermeture de la connexion à la base de données
        $conn->close();
        ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
