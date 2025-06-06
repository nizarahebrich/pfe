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

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$etud_id = $_SESSION['user_id'];

// Récupérer la filière
$stmt = $conn->prepare("SELECT id_f FROM etud WHERE id = ?");
$stmt->bind_param("i", $etud_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if (!$row) {
    echo "Étudiant introuvable.";
    exit();
}

$id_filiere = $row['id_f'];

// Récupérer les concours (type = 'concours')
$query = $conn->prepare("
    SELECT c.id_contenu, c.titre, c.module, c.fichier, p.username AS prof_nom 
    FROM contenu c 
    JOIN prof p ON c.prof = p.id 
    WHERE c.id_f = ? AND c.type = 'concours'
");
$query->bind_param("i", $id_filiere);
$query->execute();
$concours = $query->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Concours Disponibles</title>
  <link rel="stylesheet" href="EP.css" />
</head>
<body>
  <h1>📘 Concours Disponibles</h1>

  <section class="table-section">
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Module</th>
          <th>Professeur</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($concours->num_rows > 0): ?>
          <?php while ($row = $concours->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['titre']) ?></td>
              <td><?= htmlspecialchars($row['module']) ?></td>
              <td><?= htmlspecialchars($row['prof_nom']) ?></td>
              <td>
                <!-- Ici on suppose que fichier est un BLOB donc il faudra un script pour servir le fichier -->
                <a href="download.php?id=<?= $row['id_contenu'] ?>" target="_blank">Voir / Télécharger</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4">Aucun concours disponible pour votre filière.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</body>
</html>

<?php $conn->close(); ?>
