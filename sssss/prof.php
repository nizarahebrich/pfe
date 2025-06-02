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
      <select name="filiere">
        <option value="1">DSI</option>
        <option value="2">MA</option>
        <option value="3">PME</option>
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
        <!-- PHP  -->
        <tr>
          <td>Intro PHP</td>
          <td>Développement </td>
          <td>DSI</td>
          <td><a href="uploads/intro_php.pdf" target="_blank">Voir</a></td>
          <td>
            <a href="update.php?id=1">Modifier</a> |
            <a href="delete.php?id=1" onclick="return confirm('Supprimer ?')">Supprimer</a>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</body>
</html>
