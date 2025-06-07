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
    exit;
}

$etud_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, photo FROM etud WHERE id = ?");
$stmt->bind_param("i", $etud_id);
$stmt->execute();
$result = $stmt->get_result();
$etud = $result->fetch_assoc();

if (!$etud) {
    die("Étudiant introuvable.");
}

$username = htmlspecialchars($etud['username']);

// Chemin vers la photo de profil si existe, sinon photo par défaut
$photoPath = 'default-profile.jpg';  // image par défaut
if (!empty($etud['photo']) && file_exists('uploads/photos/' . $etud['photo'])) {
    $photoPath = 'uploads/photos/' . rawurlencode($etud['photo']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Étudiant - Cours</title>
  <link rel="icon" href="logo.jpg" type="image/jpg">
  <link rel="stylesheet" href="EP.css">
  <style>
    /* Exemple simple pour l'image de profil */
    .profile-label img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      vertical-align: middle;
      margin-right: 8px;
    }
  </style>
</head>
<body>
  <div class="profile-menu">
    <input type="checkbox" id="toggleProfile" />
    <label for="toggleProfile" class="profile-label">
      <img src="<?= $photoPath ?>" alt="Profil" />
      <span>Mon Profil</span>
    </label>
    <div class="dropdown">
      <a href="changer_profile.php">Changer Profil</a>
      <a href="logout.php">Se Déconnecter</a>
    </div>
  </div>

  <h1>Bonjour, <?= $username ?> !</h1>

  <section class="categories">
    <div class="cat-box" onclick="window.location.href='cours.php'">
      <img src="cours.png" alt="Cours" />
    </div>
    <div class="cat-box" onclick="window.location.href='exams.php'">
      <img src="exams.png" alt="Examens" />
    </div>
    <div class="cat-box" onclick="window.location.href='councours.php'">
      <img src="counc.png" alt="Concours" />
    </div>
  </section>
</body>
</html>

<?php $conn->close(); ?>
