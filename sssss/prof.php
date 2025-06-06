<?php
session_start();

// Simuler un professeur connecté avec ID = 1 (à remplacer par ton système de session)
$_SESSION['user_id'] = 1;

$host = 'localhost';
$dbname = 'gestion_cours';
$user = 'root';
$pass = '12344321';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$prof_id = $_SESSION['user_id'] ?? null;
if (!$prof_id) {
    header("Location: login.html");
    exit();
}

// Récupérer les filières pour le select
$filieres = [];
$filiereResult = $conn->query("SELECT id_f, nom_f FROM filiere");
while ($row = $filiereResult->fetch_assoc()) {
    $filieres[$row['id_f']] = $row['nom_f'];
}

// Gestion du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $module = $_POST['module'] ?? '';
    $filiere = $_POST['filiere'] ?? '';
    $type = $_POST['type'] ?? '';
    $fileName = $_FILES['fichier']['name'] ?? '';
    $fileTmp = $_FILES['fichier']['tmp_name'] ?? '';

    if ($titre && $module && $filiere && $type && $fileName && $fileTmp) {
        // Enregistrer le fichier dans un dossier 'uploads'
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filePath = $uploadDir . basename($fileName);
        move_uploaded_file($fileTmp, $filePath);

        // Insérer dans la base (on stocke le nom du fichier ici)
        $stmt = $conn->prepare("INSERT INTO contenu (titre, module, id_f, prof, type, fichier) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $titre, $module, $filiere, $prof_id, $type, $fileName);
        $stmt->execute();
        $stmt->close();

        header("Location: manage_content.php"); // Reload pour éviter le repost
        exit();
    } else {
        $error = "Veuillez remplir tous les champs et sélectionner un fichier.";
    }
}

// Récupérer les contenus du prof (pour tableau en haut)
$stmt = $conn->prepare("SELECT id_contenu, titre, module, id_f, type, fichier FROM contenu WHERE prof = ?");
$stmt->bind_param("i", $prof_id);
$stmt->execute();
$result = $stmt->get_result();

// Récupérer tous les contenus (pour tableau en bas, comme pour les étudiants)
$allContentsResult = $conn->query("SELECT id_contenu, titre, module, id_f, type, fichier FROM contenu ORDER BY id_contenu DESC");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Prof - Gérer le Contenu</title>
  <link rel="stylesheet" href="EP.css" />
</head>
<body>
  <h1>Bienvenue, Professeur</h1>

  <?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <section class="form-section">
    <h2>Ajouter un contenu</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="text" name="titre" placeholder="Titre du contenu" required />
      <input type="text" name="module" placeholder="Module" required />

      <select name="filiere" required>
        <option value="">--Choisir Filière--</option>
        <?php foreach ($filieres as $id => $nom): ?>
          <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($nom) ?></option>
        <?php endforeach; ?>
      </select>

      <select name="type" required>
        <option value="">--Choisir Type--</option>
        <option value="cour">Cours</option>
        <option value="exam">Examen</option>
        <option value="concour">Concours</option>
      </select>

      <input type="file" name="fichier" accept=".pdf,.doc,.docx" required />
      <button type="submit">Ajouter</button>
    </form>
  </section>

  <section class="table-section">
    <h2>Vos Contenus Ajoutés</h2>
    <table border="1" cellpadding="5" cellspacing="0">
      <thead>
        <tr>
          <th>Titre</th>
          <th>Module</th>
          <th>Filière</th>
          <th>Type</th>
          <th>Fichier</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows === 0): ?>
          <tr><td colspan="5">Aucun contenu ajouté pour l'instant.</td></tr>
        <?php else: ?>
          <?php while ($content = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($content['titre']) ?></td>
            <td><?= htmlspecialchars($content['module']) ?></td>
            <td><?= htmlspecialchars($filieres[$content['id_f']] ?? 'Inconnu') ?></td>
            <td><?= htmlspecialchars($content['type']) ?></td>
            <td>
              <?php if ($content['fichier']): ?>
                <a href="uploads/<?= rawurlencode($content['fichier']) ?>" target="_blank">Voir</a>
              <?php else: ?>
                Aucun fichier
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </section>

  <hr>

  <section class="table-section">
    <h2>Tous les contenus disponibles (comme pour les étudiants)</h2>
    <table border="1" cellpadding="5" cellspacing="0">
      <thead>
        <tr>
          <th>Titre</th>
          <th>Module</th>
          <th>Filière</th>
          <th>Type</th>
          <th>Fichier</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($allContentsResult->num_rows === 0): ?>
          <tr><td colspan="5">Aucun contenu disponible.</td></tr>
        <?php else: ?>
          <?php while ($content = $allContentsResult->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($content['titre']) ?></td>
            <td><?= htmlspecialchars($content['module']) ?></td>
            <td><?= htmlspecialchars($filieres[$content['id_f']] ?? 'Inconnu') ?></td>
            <td><?= htmlspecialchars($content['type']) ?></td>
            <td>
              <?php if ($content['fichier']): ?>
                <a href="uploads/<?= rawurlencode($content['fichier']) ?>" target="_blank">Voir</a>
              <?php else: ?>
                Aucun fichier
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </section>

</body>
</html>

<?php
$conn->close();
?>
