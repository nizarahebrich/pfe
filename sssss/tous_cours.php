<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";
$password = "12344321";
$dbname = "gestion_cours";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}
$sql = "SELECT c.id_contenu, c.titre, c.module, c.type, c.fichier, c.prof, p.username AS prof_nom
        FROM contenu c
        JOIN prof p ON c.prof = p.id
        ORDER BY c.titre ASC";

$result = $conn->query($sql);
$cours = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$conn->close();

function isActive($pageName) {
    return basename($_SERVER['PHP_SELF']) === $pageName ? 'active' : '';
}

function isSubActive($pageName) {
    return basename($_SERVER['PHP_SELF']) === $pageName ? 'active-submenu' : '';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Tous les Cours</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0; padding: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .menu {
            background-color: rgb(220, 228, 238);
            padding: 1em;
            width: 250px;
            box-sizing: border-box;
            overflow-y: auto;
        }
        .menu ul {
            list-style: none;
            padding: 0; margin: 0;
        }
        .menu ul li {
            margin: 0.5em 0;
            position: relative;
        }
        .menu ul li a {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            display: block;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .menu ul li a:hover,
        .menu ul li.active > a {
            background-color: #0d6efd;
            color: white;
        }
        .menu ul li .submenu {
            list-style: none;
            padding-left: 15px;
            margin-top: 5px;
            display: none;
        }
        .menu ul li.active > .submenu {
            display: block;
        }
        .menu ul li .submenu li a.active-submenu {
            background-color: #0d6efd;
            color: white;
            font-weight: 700;
        }
        .menu .header img {
            max-width: 50px;
            vertical-align: middle;
        }
        .menu .header h1 {
            display: inline-block;
            margin-left: 0.5em;
            color: #0d6efd;
            font-weight: 700;
            font-size: 1.5em;
            vertical-align: middle;
        }
        .content {
            flex-grow: 1;
            padding: 30px 40px;
            background: white;
            overflow-y: auto;
        }
        h1 {
            margin-bottom: 24px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px 15px;
            text-align: left;
        }
        th {
            background-color: #0d6efd;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f6fc;
        }
        a.link-view, a.link-download {
            font-weight: 600;
            text-decoration: none;
            margin-right: 8px;
        }
        a.link-view {
            color: #007bff;
        }
        a.link-view:hover {
            text-decoration: underline;
        }
        a.link-download {
            color: #28a745;
        }
        a.link-download:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="menu">
    <ul>
        <li class="<?php echo isActive('prof_home.php'); ?>">
            <div class="header">
                <img src="img/logo.png" alt="Logo">
                <h1>Professeur</h1>
            </div>
            <a href="prof.php">Accueil</a>
            <ul class="submenu" style="display: block;">
                <li><a href="ajouter_contenu.php" class="<?php echo isSubActive('ajouter_contenu.php'); ?>">Ajouter Contenu</a></li>
                <li><a href="vos_cours.php" class="<?php echo isSubActive('vos_cours.php'); ?>">Vos Cours</a></li>
                <li><a href="tous_cours.php" class="<?php echo isSubActive('tous_cours.php'); ?>">Tous les Cours Dispo</a></li>
            </ul>
        </li>
        <li>
            <a href="logout.php" style="color:#dc3545;">Déconnexion</a>
        </li>
    </ul>
</div>

<div class="content">
    <h1>Tous les cours disponibles</h1>

    <?php if (empty($cours)): ?>
        <p>Aucun cours disponible pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Professeur</th>
                    <th>Titre</th>
                    <th>Module</th>
                    <th>Type</th>
                    <th>Fichier</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cours as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['prof_nom']) ?></td>
                        <td><?= htmlspecialchars($c['titre']) ?></td>
                        <td><?= htmlspecialchars($c['module']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($c['type'])) ?></td>
                        <td>
                            <?php if (!empty($c['fichier'])): ?>
                                <a class="link-view" href="uploads/<?= urlencode($c['fichier']) ?>" target="_blank" rel="noopener noreferrer">Voir</a>
                                <?php if ($c['prof'] != $user_id): ?>
                                    <a class="link-download" href="uploads/<?= urlencode($c['fichier']) ?>" download>Télécharger</a>
                                <?php endif; ?>
                            <?php else: ?>
                                Aucun fichier
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

</body>
</html>
