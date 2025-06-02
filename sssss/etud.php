<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Étudiant - Cours</title>
  <link rel="stylesheet" href="EP.css">
  
</head>
<body>
  <!-- Profile  -->
  <div class="profile-menu">
    <input type="checkbox" id="toggleProfile">
    <label for="toggleProfile" class="profile-label">
      <img src="prr.jpg" alt="Profil">
      <span>Mon Profil</span>
    </label>
    <div class="dropdown">
      <a href="changer_profil.php">Changer Profil</a>
      <a href="logout.php">Se Déconnecter</a>
    </div>
  </div>

  <h1>Bienvenue, Étudiant</h1>

  <section class="categories">
    <div class="cat-box" onclick="window.location.href='cours.php'">
      <img src="cours.png" alt="Cours">
    </div>
    <div class="cat-box" onclick="window.location.href='examens.php'">
      <img src="exams.png" alt="Examens">
    </div>
    <div class="cat-box" onclick="window.location.href='concours.php'">
      <img src="counc.png" alt="Concours">
    </div>
  </section>
</body>
</html>
