<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function isActive($pageName) {
    $currentFile = basename($_SERVER['PHP_SELF']);
    return $currentFile === $pageName ? 'active' : '';
}

function isSubActive($pageName) {
    $currentFile = basename($_SERVER['PHP_SELF']);
    return $currentFile === $pageName ? 'active-submenu' : '';
}

$insertError = "";
$insertSuccess = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $module = trim($_POST['module'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $prof = $_SESSION['user_id'];
    $servername = "localhost";
    $username = "root";
    $password = "12344321";
    $dbname = "gestion_cours";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Erreur de connexion: " . $conn->connect_error);
    }
    $sql = "SELECT id_f FROM prof WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $prof);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $insertError = "Professeur non trouvé.";
    } else {
        $row = $result->fetch_assoc();
        $id_f = $row['id_f'];
        $fichier_nom = null;
        if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['fichier']['tmp_name'];
            $fileName = $_FILES['fichier']['name'];
            $fileSize = $_FILES['fichier']['size'];
            $fileType = $_FILES['fichier']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = './uploads/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                $dest_path = $uploadFileDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $fichier_nom = $newFileName;
                } else {
                    $insertError = "Erreur lors du téléchargement du fichier.";
                }
            } else {
                $insertError = "Type de fichier non autorisé. Autorisé: " . implode(", ", $allowedfileExtensions);
            }
        } else {
            $insertError = "Veuillez sélectionner un fichier.";
        }
        if (empty($insertError)) {
            $stmt = $conn->prepare("INSERT INTO contenu (id_f, prof, titre, module, fichier, type) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $id_f, $prof, $titre, $module, $fichier_nom, $type);
            if ($stmt->execute()) {
                $insertSuccess = "Contenu ajouté avec succès.";
            } else {
                $insertError = "Erreur lors de l'ajout du contenu : " . $stmt->error;
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Ajouter un Contenu</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
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
        form {
            max-width: 500px;
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 6px;
            font-weight: 600;
            color: #555;
        }
        input[type="text"],
        select,
        input[type="file"] {
            padding: 10px 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        select:focus,
        input[type="file"]:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-bottom: 20px;
            font-weight: 600;
        }
        .error {
            color: #dc3545;
        }
        .success {
            color: #28a745;
        }
        .menu {
            height: 100vh;
            overflow-y: auto;
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
                Déconnexion <i class="fas fa-sign-out-alt"></i>
            </a>
        </li>
    </ul>
</div>

<div class="content">
    <h1>Ajouter un contenu</h1>

    <?php if ($insertError): ?>
        <div class="message error"><?php echo htmlspecialchars($insertError); ?></div>
    <?php endif; ?>

    <?php if ($insertSuccess): ?>
        <div class="message success"><?php echo htmlspecialchars($insertSuccess); ?></div>
    <?php endif; ?>

    <form action="ajouter_contenu.php" method="POST" enctype="multipart/form-data">
        <label for="titre">Titre du contenu</label>
        <input type="text" id="titre" name="titre" placeholder="Titre du contenu" required />

        <label for="module">Module</label>
        <input type="text" id="module" name="module" placeholder="Module" required />

        <label for="type">Type de contenu</label>
        <select id="type" name="type" required>
            <option value="" disabled selected>-- Choisir Type --</option>
            <option value="cour">Cours</option>
            <option value="exam">Examen</option>
            <option value="concour">Concours</option>
        </select>

        <label for="fichier">Fichier (PDF, DOC, DOCX, PPT, PPTX)</label>
        <input type="file" id="fichier" name="fichier" accept=".pdf,.doc,.docx,.ppt,.pptx" required />

        <button type="submit">Ajouter</button>
    </form>
</div>

</body>
</html>
