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

// Récupérer les modules distincts pour le combobox
$modulesQuery = $conn->prepare("SELECT DISTINCT module FROM contenu WHERE id_f = ? AND type = 'concours'");
$modulesQuery->bind_param("i", $id_filiere);
$modulesQuery->execute();
$modulesResult = $modulesQuery->get_result();
$modules = [];
while ($mod = $modulesResult->fetch_assoc()) {
    $modules[] = $mod['module'];
}

// Récupérer les concours (type = 'concours') avec nom prof
$query = $conn->prepare("
    SELECT c.id_contenu, c.titre, c.module, c.fichier, p.username AS prof_nom 
    FROM contenu c 
    JOIN prof p ON c.prof = p.id 
    WHERE c.id_f = ? AND c.type = 'Concours'
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
  <style>
    #searchInput, #moduleSelect {
      margin-bottom: 10px;
      padding: 5px;
      width: 300px;
      font-size: 1rem;
    }
  </style>
</head>
<body>
  <h1>📘 Concours Disponibles</h1>

  <!-- Barre de recherche -->
  <input type="text" id="searchInput" placeholder="Rechercher par titre..." onkeyup="filterTable()" />

  <!-- Combobox module -->
  <select id="moduleSelect" onchange="filterTable()">
    <option value="">Tous les modules</option>
    <?php foreach ($modules as $module): ?>
      <option value="<?= htmlspecialchars($module) ?>"><?= htmlspecialchars($module) ?></option>
    <?php endforeach; ?>
  </select>

  <section class="table-section">
    <table id="concoursTable" border="1" cellpadding="5" cellspacing="0">
      <thead>
        <tr>
          <th>Titre</th>
          <th>Module</th>
          <th>Professeur</th>
          <th>Fichier</th>
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
                <?php if ($row['fichier']): ?>
                  <a href="uploads/<?= rawurlencode($row['fichier']) ?>" target="_blank">Voir</a>
                <?php else: ?>
                  Aucun fichier
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4">Aucun concours disponible pour votre filière.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>

  <br />
  <a href="etud.php">← Retour au menu</a>

  <script>
    function filterTable() {
      const searchInput = document.getElementById('searchInput').value.toLowerCase();
      const moduleSelect = document.getElementById('moduleSelect').value.toLowerCase();
      const table = document.getElementById('concoursTable');
      const trs = table.tBodies[0].getElementsByTagName('tr');

      for (let i = 0; i < trs.length; i++) {
        const tdTitre = trs[i].getElementsByTagName('td')[0].textContent.toLowerCase();
        const tdModule = trs[i].getElementsByTagName('td')[1].textContent.toLowerCase();

        // Filtrer par titre et module
        const matchesTitle = tdTitre.includes(searchInput);
        const matchesModule = moduleSelect === '' || tdModule === moduleSelect;

        trs[i].style.display = (matchesTitle && matchesModule) ? '' : 'none';
      }
    }
  </script>
</body>
</html>

<?php $conn->close(); ?>
