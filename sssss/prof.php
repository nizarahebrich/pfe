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


$prof_id = $_SESSION['user_id'];

$filiereResult = $conn->query("SELECT id_f, nom_f FROM filiere");
$filieres = [];
while ($row = $filiereResult->fetch_assoc()) {
    $filieres[$row['id_f']] = $row['nom_f'];
}

$stmt = $conn->prepare("SELECT id_contenu, titre, module, id_f, fichier FROM contenu WHERE prof = ?");
$stmt->bind_param("i", $prof_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Prof - Gérer le Contenu</title>
  <link rel="stylesheet" href="EP.css">
</head>
<body>
  <h1>Bienvenue, Professeur</h1>

  <section class="form-section">
    <h2>Ajouter un contenu</h2>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
      <input type="text" name="titre" placeholder="Titre du contenu" required>
      <input type="text" name="module" placeholder="Module" required>
      <select name="filiere" required>
        <option value="">--Choisir Filière--</option>
        <?php foreach ($filieres as $id => $nom): ?>
          <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($nom) ?></option>
        <?php endforeach; ?>
      </select>
      <input type="file" name="fichier" accept=".pdf,.doc,.docx" required>
      <button type="submit">Ajouter</button>
    </form>
  </section>

  <section class="table-section">
    <h2>Contenus Ajoutés</h2>
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Module</th>
          <th>Filière</th>
          <th>Fichier</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($content = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($content['titre']) ?></td>
          <td><?= htmlspecialchars($content['module']) ?></td>
          <td><?= htmlspecialchars($filieres[$content['id_f']] ?? 'Inconnu') ?></td>
          <td>
            <?php if ($content['fichier']): ?>
              <a href="download.php?id=<?= $content['id_contenu'] ?>" target="_blank">Voir</a>
            <?php else: ?>
              Aucun fichier
            <?php endif; ?>
          </td>
          <td>
            <a href="update.php?id=<?= $content['id_contenu'] ?>">Modifier</a> |
            <a href="delete.php?id=<?= $content['id_contenu'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($result->num_rows === 0): ?>
          <tr><td colspan="5">Aucun contenu ajouté pour l'instant.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</body>
</html>
<?php $conn->close(); ?>
