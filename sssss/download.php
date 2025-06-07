<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Identifiant manquant ou invalide.");
}

$id = (int)$_GET['id'];

$host = 'localhost';
$dbname = 'gestion_cours';
$user = 'root';
$pass = '12344321';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer le nom du fichier en base
$stmt = $conn->prepare("SELECT fichier FROM contenu WHERE id_contenu = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($filename);
$stmt->fetch();
$stmt->close();
$conn->close();

if (!$filename) {
    die("Fichier introuvable.");
}

// Chemin complet vers le fichier sur le serveur
$filePath = __DIR__ . "/uploads/" . basename($filename);

if (!file_exists($filePath)) {
    die("Fichier non trouvé sur le serveur.");
}

// Envoi des headers pour forcer le téléchargement
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
flush();
readfile($filePath);
exit();
