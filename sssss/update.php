<?php
session_start();
$host = 'localhost';
$dbname = 'gestion_cours';
$user = 'root';
$pass = '12344321';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: prof.php");
    exit();
}

$id = intval($_GET['id']);
$prof_id = $_SESSION['user_id'];

// Get filieres
$filiereResult = $conn->query("SELECT id_f, nom_f FROM filiere");
$filieres = [];
while ($row = $filiereResult->fetch_assoc()) {
    $filieres[$row['id_f']] = $row['nom_f'];
}

// Fetch current content
$stmt = $conn->prepare("SELECT * FROM contenu WHERE id_contenu = ? AND prof = ?");
$stmt->bind_param("ii", $id, $prof_id);
$stmt->execute();
$content = $stmt->get_result()->fetch_assoc();

if (!$content) {
    echo "Contenu non trouvé ou non autorisé.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $module = $_POST['module'];
    $filiere = $_POST['filiere'];
    $fichier = $content['fichier'];

    // If a new file is uploaded
    if (!empty($_FILES['fichier']['name'])) {
        $targetDir = "uploads/";
        $fileName = uniqid() . "_" . basename($_FILES["fichier"]["name"]);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES["fichier"]["tmp_name"], $targetFile);
        $fichier = $targetFile;
    }

    $stmt = $conn->prepare("UPDATE contenu SET titre = ?, module = ?, id_f = ?, fichier = ? WHERE id_contenu = ? AND prof = ?");
    $stmt->bind_param("ssissi", $titre, $module, $filiere, $fichier, $id, $prof_id);
    $stmt->execute();

    header("Location: prof.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="EP.css">
  <title>Modifier Contenu</title>
</head>
<body>
  <h1>Modifier le contenu</h1>
  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="titre" value="<?= htmlspecialchars($content['titre']) ?>" required><br><br>
    <input type="text" name="module" value="<?= htmlspecialchars($content['module']) ?>" required><br><br>
    <select name="filiere" required>
      <?php foreach ($filieres as $id_f => $nom): ?>
        <option value="<?= $id_f ?>" <?= $id_f == $content['id_f'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($nom) ?>
        </option>
      <?php endforeach; ?>
    </select><br><br>

    <label>Fichier actuel : <?= basename($content['fichier']) ?></label><br>
    <input type="file" name="fichier" accept=".pdf,.doc,.docx"><br><br>

    <button type="submit">Mettre à jour</button>
  </form>
  <br>
  <a href="prof.php">⬅ Retour</a>
</body>
</html>
