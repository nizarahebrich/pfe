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

// Get distinct exam modules for ComboBox
$modulesQuery = $conn->prepare("SELECT DISTINCT module FROM contenu WHERE id_f = ? AND type = 'exam'");
$modulesQuery->bind_param("i", $id_filiere);
$modulesQuery->execute();
$modulesResult = $modulesQuery->get_result();
$modules = [];
while ($mod = $modulesResult->fetch_assoc()) {
    $modules[] = $mod['module'];
}

// Get all exams
$query = $conn->prepare("
    SELECT c.titre, c.module, p.username AS prof_nom, c.id_contenu, c.fichier
    FROM contenu c
    JOIN prof p ON c.prof = p.id
    WHERE c.id_f = ? AND c.type = 'Examen'
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
  <style>
    #searchInput, #moduleSelect {
      margin-bottom: 10px;
      padding: 5px;
      width: 300px;
      font-size: 1rem;
    }
    .btn-return {
      display: inline-block;
      margin-bottom: 15px;
      padding: 8px 15px;
      background-color: #007BFF;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      font-weight: bold;
    }
    .btn-return:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <h1>📘 Exams Disponibles</h1>

  <!-- Bouton Retour au menu -->
  <a href="etud.php" class="btn-return">← Retour au menu</a>

  <!-- Barre de recherche -->
  <input type="text" id="searchInput" placeholder="Rechercher par titre..." onkeyup="filterTable()" />

  <!-- ComboBox de modules -->
  <select id="moduleSelect" onchange="filterTable()">
    <option value="">Tous les modules</option>
    <?php foreach ($modules as $module): ?>
      <option value="<?= htmlspecialchars($module) ?>"><?= htmlspecialchars($module) ?></option>
    <?php endforeach; ?>
  </select>

  <section class="table-section">
    <table border="1" cellpadding="5" cellspacing="0">
      <thead>
        <tr>
          <th>Titre</th>
          <th>Module</th>
          <th>Professeur</th>
          <th>Fichier</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($exams->num_rows === 0): ?>
          <tr><td colspan="4">Aucun examen disponible pour votre filière.</td></tr>
        <?php else: ?>
          <?php while ($exam = $exams->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($exam['titre']) ?></td>
            <td><?= htmlspecialchars($exam['module']) ?></td>
            <td><?= htmlspecialchars($exam['prof_nom']) ?></td>
            <td>
              <?php if (!empty($exam['fichier'])): ?>
                <a href="uploads/<?= rawurlencode($exam['fichier']) ?>" target="_blank">Voir</a> |
                <a href="download.php?id=<?= $exam['id_contenu'] ?>">Télécharger</a>
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

<script>
  function filterTable() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const moduleSelect = document.getElementById('moduleSelect').value.toLowerCase();
    const table = document.querySelector('table tbody');
    const trs = table.getElementsByTagName('tr');

    for (let i = 0; i < trs.length; i++) {
      const tdTitre = trs[i].getElementsByTagName('td')[0].textContent.toLowerCase();
      const tdModule = trs[i].getElementsByTagName('td')[1].textContent.toLowerCase();

      const matchesTitle = tdTitre.includes(searchInput);
      const matchesModule = moduleSelect === '' || tdModule === moduleSelect;

      trs[i].style.display = (matchesTitle && matchesModule) ? '' : 'none';
    }
  }
</script>

</body>
</html>
<?php
$conn->close();
?>
