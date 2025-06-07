<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function isActive($pageName) {
    return basename($_SERVER['PHP_SELF']) === $pageName ? 'active' : '';
}

function isSubActive($pageName) {
    return basename($_SERVER['PHP_SELF']) === $pageName ? 'active-submenu' : '';
}

$servername = "localhost";
$username = "root";
$password = "12344321";
$dbname = "gestion_cours";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

$prof = $_SESSION['user_id'];

$sql = "SELECT id_contenu, titre, module, type, fichier FROM contenu WHERE prof = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $prof);
$stmt->execute();
$result = $stmt->get_result();
$contenus = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Vos Cours</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
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
            padding: 0;
            margin: 0;
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
        a.download-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }
        a.download-link:hover {
            text-decoration: underline;
        }
        .menu::-webkit-scrollbar,
        .content::-webkit-scrollbar {
            width: 6px;
        }
        .menu::-webkit-scrollbar-thumb,
        .content::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.1);
            border-radius: 3px;
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
            <a href="logout.php" style="color:#dc3545;">
                Déconnexion
            </a>
        </li>
    </ul>
</div>

<div class="content">
    <h1>Vos contenus</h1>

    <?php if (empty($contenus)): ?>
        <p>Aucun contenu ajouté pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Module</th>
                    <th>Type</th>
                    <th>Fichier</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contenus as $contenu): ?>
                    <?php
                    if (empty($contenu['titre']) || empty($contenu['fichier'])) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contenu['titre']); ?></td>
                        <td><?php echo htmlspecialchars($contenu['module']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($contenu['type'])); ?></td>
                        <td>
                            <a class="download-link" href="uploads/<?php echo urlencode($contenu['fichier']); ?>" target="_blank" rel="noopener noreferrer">
                                Voir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
