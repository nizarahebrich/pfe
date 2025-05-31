<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Résultats de Détection</title>
    <link rel="stylesheet" type="text/css" href="css/style1.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>

    <style>
        /* Style personnalisé pour le tableau */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .rech {
            margin-bottom: 20px;
        }

        .rech input[type="text"] {
            width: 200px;
        }

        .rech input[type="submit"] {
            background-color: #40536b; /* Couleur du bouton */
            color: white;
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
                    <li><a href="modif_password.html">Changer mot de passe</a></li>
                </ul>
            </li>
            <li><a href="index.html">Déconnexion</a></li>
        </ul>
    </div>
    <div class="content">
        <h1>Résultats de la Détection d'Hier</h1>

        <form method="get" class="rech">
            <label for="search">Rechercher par nom :</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>">
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
            die("Connection failed: " . $conn->connect_error);
        }

        // Récupération des résultats d'hier
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // Récupération du nom recherché (si spécifié)
        $searchName = $_GET['search'] ?? '';
        $searchName = $conn->real_escape_string($searchName); // Protection contre les injections SQL

        // Construction de la requête SQL avec la clause WHERE pour filtrer par nom
        $sql = "SELECT * FROM detection WHERE date_detection = '$yesterday'";
        if (!empty($searchName)) {
            $sql .= " AND nom_visage LIKE '%$searchName%'";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Affichage des résultats dans un tableau
            echo "<table>";
            echo "<tr><th>ID</th><th>Date</th><th>Heure</th><th>Nom du Visage</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date_detection']) . "</td>";
                echo "<td>" . htmlspecialchars($row['heure_detection']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nom_visage']) . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Aucun résultat trouvé pour hier.</p>";
        }

        // Fermeture de la connexion à la base de données
        $conn->close();
        ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
