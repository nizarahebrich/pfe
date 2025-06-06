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

// Get student's filiere
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

// Get exams for student's filiere
$query = $conn->prepare("
    SELECT c.titre, c.module, p.username AS prof_nom, c.id_contenu, c.fichier, f.nom_f AS filiere_nom
    FROM contenu c
    JOIN prof p ON c.prof = p.id
    JOIN filiere f ON c.id_f = f.id_f
    WHERE c.id_f = ? AND c.type = 'exam'
");
$query->bind_param("i", $id_filiere);
$query->execute();
$exams = $query->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>📘 Exams Disponibles</title>
  <link rel="stylesheet" href="EP.css" />
</head>
<body>
  <h1>📘 Exams Disponibles</h1>

  <section class="table-section">
    <table border="1" cellpadding="5" cellspacing="0">
      <thead>
        <tr>
          <th>Titre</th>
          <th>Module</th>
          <th>Filière</th>
          <th>Professeur</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($exams->num_rows === 0): ?>
          <tr><td colspan="5">Aucun examen disponible pour votre filière.</td></tr>
        <?php else: ?>
          <?php while ($exam = $exams->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($exam['titre']) ?></td>
            <td><?= htmlspecialchars($exam['module']) ?></td>
            <td><?= htmlspecialchars($exam['filiere_nom']) ?></td>
            <td><?= htmlspecialchars($exam['prof_nom']) ?></td>
            <td>
              <a href="uploads/<?= rawurlencode($exam['fichier']) ?>" target="_blank">Voir</a> |
              <a href="download.php?id=<?= $exam['id_contenu'] ?>">Télécharger</a>
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
