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
    header("Location: login.php");
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

$modulesQuery = $conn->prepare("SELECT DISTINCT module FROM contenu WHERE id_f = ? AND LOWER(type) = 'cours'");
$modulesQuery->bind_param("i", $id_filiere);
$modulesQuery->execute();
$modulesResult = $modulesQuery->get_result();
$modules = [];
while ($mod = $modulesResult->fetch_assoc()) {
    $modules[] = $mod['module'];
}

$query = $conn->prepare("
    SELECT c.titre, c.module, p.username AS prof_nom, c.id_contenu, c.fichier
    FROM contenu c
    JOIN prof p ON c.prof = p.id
    WHERE c.id_f = ? AND LOWER(c.type) = 'cours'
");
$query->bind_param("i", $id_filiere);
$query->execute();
$cours = $query->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Mes Cours</title>
  <link rel="stylesheet" href="EP.css" />
  <style>
    #searchInput, #moduleSelect {
      margin-bottom: 10px;
      padding: 5px;
      width: 300px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      padding: 8px;
      border: 1px solid #ccc;
      text-align: left;
    }
    th {
      background-color: #007BFF;
      color: white;
    }
    tr:hover {
      background-color: #f0f8ff;
    }
    a {
      color: #007BFF;
      text-decoration: none;
      font-weight: bold;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <h1>Mes Cours</h1>

  <input type="text" id="searchInput" placeholder="Rechercher par titre..." onkeyup="filterTable()" />
  <select id="moduleSelect" onchange="filterTable()">
    <option value="">Tous les modules</option>
    <?php foreach ($modules as $module): ?>
      <option value="<?= htmlspecialchars(strtolower($module)) ?>"><?= htmlspecialchars($module) ?></option>
    <?php endforeach; ?>
  </select>

  <table>
    <thead>
      <tr>
        <th>Titre</th>
        <th>Module</th>
        <th>Professeur</th>
        <th>Fichiers</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($cours->num_rows > 0): ?>
        <?php while ($row = $cours->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['titre']) ?></td>
            <td><?= htmlspecialchars($row['module']) ?></td>
            <td><?= htmlspecialchars($row['prof_nom']) ?></td>
            <td>
              <?php if (!empty($row['fichier'])): ?>
                <a href="uploads/<?= rawurlencode($row['fichier']) ?>" target="_blank">Voir</a> |
                <a href="uploads/<?= rawurlencode($row['fichier']) ?>" download>Télécharger</a>
              <?php else: ?>
                Aucun fichier
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="4">Aucun cours disponible pour votre filière.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <br />
  <a href="etud.php">← Retour au menu</a>

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

<?php $conn->close(); ?>
