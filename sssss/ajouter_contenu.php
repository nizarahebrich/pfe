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

$insertError = "";
$insertSuccess = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $module = trim($_POST['module'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $prof = $_SESSION['user_id'];

    $allowedTypes = ['cours', 'examen', 'concours'];

    if (!in_array($type, $allowedTypes)) {
        $insertError = "Type de contenu invalide.";
    } else {
        $conn = new mysqli("localhost", "root", "12344321", "gestion_cours");
        if ($conn->connect_error) {
            die("Erreur de connexion: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT id_f FROM prof WHERE id = ?");
        $stmt->bind_param("i", $prof);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $insertError = "Professeur non trouvé.";
        } else {
            $id_f = $result->fetch_assoc()['id_f'];
            $fichier_nom = null;

            if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['fichier']['tmp_name'];
                $fileName = $_FILES['fichier']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedfileExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadDir = './uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $destPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $fichier_nom = $newFileName;
                    } else {
                        $insertError = "Erreur lors du téléchargement du fichier.";
                    }
                } else {
                    $insertError = "Type de fichier non autorisé.";
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
                    $insertError = "Erreur lors de l'ajout : " . $stmt->error;
                }
                $stmt->close();
            }
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Contenu</title>
  <style>

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #0a192f;
      margin: 0;
      padding: 0;
      display: flex;
      height: 100vh;
      color: #ffffff;
    }

    .menu {
      background-color: #102542;
      padding: 1em;
      width: 250px;
      box-sizing: border-box;
      overflow-y: auto;
    }

    .menu ul {
      list-style: none;
      padding: 0;
    }

    .menu ul li {
      margin: 0.5em 0;
    }

    .menu ul li a {
      display: block;
      padding: 10px;
      text-decoration: none;
      color: #cfd8dc;
      font-weight: bold;
      border-radius: 5px;
    }

    .menu ul li a:hover,
    .menu ul li.active > a {
      background-color: #00bcd4;
      color: #0a192f;
    }

    .menu ul li .submenu {
      display: block;
      padding-left: 15px;
    }

    .menu ul li .submenu li a.active-submenu {
      background-color: #00bcd4;
      color: #0a192f;
    }

    .menu .header img {
      max-width: 50px;
      vertical-align: middle;
      border-radius: 20px;
    }

    .menu .header h1 {
      display: inline-block;
      margin-left: 0.5em;
      color: #00bcd4;
      font-weight: 700;
      font-size: 1.5em;
      vertical-align: middle;
    }

    .content {
      flex-grow: 1;
      background: #0a192f;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow-y: auto;
    }

    .center-box {
      background: #ffffff;
      color: #0a192f;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 188, 212, 0.15);
      width: 100%;
      max-width: 500px;
      text-align: center;
    }

    .center-box h1 {
      margin-bottom: 25px;
      color: #007ba7;
    }

    form {
      max-width: 500px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
    }

    input[type="text"],
    select,
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 18px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      padding: 12px;
      background-color: #00bcd4;
      color: #0a192f;
      border: none;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background-color: #007ba7;
    }

    .message {
      margin-bottom: 20px;
      font-weight: 600;
    }

    .error { color: #f44336; }
    .success { color: #4caf50; }

    </style>
</head>
<body>

<div class="menu">
    <ul>
        <li class="<?php echo isActive('prof_home.php'); ?>">
            <div class="header">
                <img src="prof.jpg" alt="Logo">
                <h1>Professeur</h1>
            </div>
            <a href="prof.php">Accueil</a>
            <ul class="submenu">
                <li><a href="ajouter_contenu.php" class="<?php echo isSubActive('ajouter_contenu.php'); ?>">Ajouter Contenu</a></li>
                <li><a href="vos_cours.php" class="<?php echo isSubActive('vos_cours.php'); ?>">Vos Cours</a></li>
                <li><a href="tous_cours.php" class="<?php echo isSubActive('tous_cours.php'); ?>">Tous les Cours Dispo</a></li>
            </ul>
        </li>
        <li><a href="logout.php" style="color: #dc3545;">Déconnexion</a></li>
    </ul>
</div>

<div class="content">
    <div class="center-box">
    <h1>Ajouter un contenu</h1>

    <?php if ($insertError): ?>
        <div class="message error"><?php echo htmlspecialchars($insertError); ?></div>
    <?php endif; ?>

    <?php if ($insertSuccess): ?>
        <div class="message success"><?php echo htmlspecialchars($insertSuccess); ?></div>
    <?php endif; ?>

    <form action="ajouter_contenu.php" method="POST" enctype="multipart/form-data">
        <label for="titre">Titre du contenu</label>
        <input type="text" id="titre" name="titre" required>

        <label for="module">Module</label>
        <input type="text" id="module" name="module" required>

        <label for="type">Type de contenu</label>
        <select id="type" name="type" required>
            <option value="" disabled selected>-- Choisir Type --</option>
            <option value="cours">Cours</option>
            <option value="examen">Examen</option>
            <option value="concours">Concours</option>
        </select>

        <label for="fichier">Fichier (PDF, DOC, DOCX, PPT, PPTX)</label>
        <input type="file" id="fichier" name="fichier" accept=".pdf,.doc,.docx,.ppt,.pptx" required>

        <button type="submit">Ajouter</button>
    </form>
</div>

</body>
</html>
