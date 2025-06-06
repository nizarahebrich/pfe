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
$stmt = $conn->prepare("SELECT username FROM etud WHERE id = ?");
$stmt->bind_param("i", $etud_id);
$stmt->execute();
$result = $stmt->get_result();
$etud = $result->fetch_assoc();
$username = htmlspecialchars($etud['username']);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Étudiant - Cours</title>
  <link rel="icon" href="logo.jpg" type="image/jpg">
  <link rel="stylesheet" href="EP.css">
</head>
<body>
  <div class="profile-menu">
    <input type="checkbox" id="toggleProfile">
    <label for="toggleProfile" class="profile-label">
      <img src="prr.jpg" alt="Profil">
      <span>Mon Profil</span>
    </label>
    <div class="dropdown">
      <a href="changer_profile.html">Changer Profil</a>
      <a href="logout.php">Se Déconnecter</a>
    </div>
  </div>

  <h1>Bonjour, </h1>
  // zid php hna 3la nom 
  

  <section class="categories">
    <div class="cat-box" onclick="window.location.href='cours.html'">
      <img src="cours.png" alt="Cours">
    </div>
    <div class="cat-box" onclick="window.location.href='exams.html'">
      <img src="exams.png" alt="Examens">
    </div>
    <div class="cat-box" onclick="window.location.href='councours.html'">
      <img src="counc.png" alt="Concours">
    </div>
  </section>
</body>
</html>




<?php $conn->close(); ?>
