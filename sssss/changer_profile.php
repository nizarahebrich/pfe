<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Changer le Profil</title>
  <link rel="stylesheet" href="profile.css">
</head>
<body>
  <div class="container">
    <h1>Modifier votre profil</h1>
    <form class="profile-form" action="#" method="post" enctype="multipart/form-data">
      <div class="profile-pic">
        <img src="images/default-profile.png" alt="Photo de profil" id="preview">
        <input type="file" name="photo" accept="image/*" onchange="previewImage(event)">
      </div>

      <label for="name">Nom complet</label>
      <input type="text" id="name" name="name" placeholder="Entrez votre nom complet" required>

      <label for="email">Adresse e-mail</label>
      <input type="email" id="email" name="email" placeholder="exemple@mail.com" required>

      <label for="password">Nouveau mot de passe</label>
      <input type="password" id="password" name="password" placeholder="••••••••" required>

      <button type="submit">Enregistrer les modifications</button>
    </form>
  </div>

  <script>
    function previewImage(event) {
      const reader = new FileReader();
      reader.onload = function () {
        const output = document.getElementById('preview');
        output.src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>
</body>
</html>
