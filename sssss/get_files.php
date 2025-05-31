<?php
// Vérification de la présence du paramètre 'directory' dans l'URL
if (!isset($_GET['directory']) || empty($_GET['directory'])) {
    http_response_code(400); // Mauvaise requête
    echo json_encode(['error' => 'Le paramètre "directory" est requis.']);
    exit();
}

// Récupération du paramètre 'directory' et validation
$directory = trim($_GET['directory']);
$directory = realpath($directory);

// Vérification que le répertoire est valide et existe
if ($directory === false || !is_dir($directory)) {
    http_response_code(404); // Non trouvé
    echo json_encode(['error' => 'Le répertoire spécifié est invalide ou n\'existe pas.']);
    exit();
}

// Scanning du répertoire pour obtenir les fichiers
$files = scandir($directory);
$images = [];

// Filtrage des fichiers pour ne garder que les images jpg et png
foreach ($files as $file) {
    $filePath = $directory . '/' . $file;

    if (is_file($filePath)) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($extension === 'jpg' || $extension === 'png') {
            $images[] = $filePath;
        }
    }
}

// Envoi des en-têtes JSON
header('Content-Type: application/json');
echo json_encode($images);
?>
