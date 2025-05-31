<?php
$cheminDossier = 'C:\\Users\\ERAZER\\Desktop\\pfe\\Inconnus_visage';

// Assurez-vous que le dossier existe
if (is_dir($cheminDossier)) {
    // Obtient tous les fichiers d'images du dossier
    $images = glob($cheminDossier . '*.jpg');

    // Vérifie si des images ont été trouvées
    if (count($images) > 0) {
        foreach ($images as $image) {
            // Nettoie le chemin de l'image pour éviter les problèmes de sécurité
            $imagePath = str_replace('C:/xampp/htdocs/pfe/', '', $image);
            // Affiche l'image avec une taille maximum définie pour les images
            echo '<div style="margin-bottom: 10px;">';
            echo '<img src="' . htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') . '" alt="Image" style="max-width: 100%; height: auto;">' . PHP_EOL;
            echo '</div>';
        }
    } else {
        echo '<p>Aucune image trouvée dans le dossier.</p>';
    }
} else {
    echo '<p>Le dossier spécifié n\'existe pas.</p>';
}
