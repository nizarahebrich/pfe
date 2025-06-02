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


$query = $conn->prepare("SELECT c.titre, c.module, p.username AS prof_nom, c.id_contenu
                         FROM contenu c
                         JOIN prof p ON c.prof = p.id
                         WHERE c.id_f = ?");
$query->bind_param("i", $id_filiere);
$query->execute();
$cours = $query->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes Cours</title>
  <link rel="stylesheet" href="EP.css">
</head>
<body>
  <h1>Mes Cours</h1>

  <section class="table-section">
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Module</th>
          <th>Professeur</th>
          <th>Fichier</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $cours->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['titre']) ?></td>
          <td><?= htmlspecialchars($row['module']) ?></td>
          <td><?= htmlspecialchars($row['prof_nom']) ?></td>
          <td>
            <a href="download.php?id=<?= $row['id_contenu'] ?>" target="_blank">Télécharger</a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($cours->num_rows === 0): ?>
        <tr><td colspan="4">Aucun cours disponible pour votre filière.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</body>
</html>
<?php $conn->close(); ?>
