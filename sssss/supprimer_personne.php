<?php
$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "12344321";
$base_de_donnees = "pfe";

$connexion = new mysqli($serveur, $utilisateur, $motdepasse, $base_de_donnees);
if ($connexion->connect_error) {
    die("Erreur de connexion à la base de données : " . $connexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];

    $directory = 'C:\\Users\\ERAZER\\Desktop\\pfe\\test\\data\\dataset\\' . $nom;

    if (is_dir($directory)) {
        deleteDirectory($directory);

        $sqlSuppression = "DELETE FROM personne WHERE nom = ?";
        $statement = $connexion->prepare($sqlSuppression);
        $statement->bind_param("s", $nom);

        if ($statement->execute()) {
?>
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Suppression Réussie</title>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            </head>
            <body>
                <script>
                    swal({
                        icon: "success",
                        title: "Bon travail !",
                        text: "La personne a été supprimée avec succès.",
                        showConfirmButton: true,
                        confirmButtonText: "Fermer"
                    }).then(() => {
                        window.location = "personne.php";
                    });
                </script>
            </body>
            </html>
<?php
        } else {
?>
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Erreur de Suppression</title>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            </head>
            <body>
                <script>
                    swal({
                        icon: "error",
                        title: "Erreur !",
                        text: "Erreur lors de la suppression de la personne : <?php echo $statement->error; ?>",
                        showConfirmButton: true,
                        confirmButtonText: "Fermer"
                    }).then(() => {
                        window.location = "accueil.php";
                    });
                </script>
            </body>
            </html>
<?php
        }

        $statement->close();
    } else {
?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Dossier Non Trouvé</title>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        </head>
        <body>
            <script>
                swal({
                    icon: "warning",
                    title: "Erreur !",
                    text: "Le dossier n'existe pas!",
                    showConfirmButton: true,
                    confirmButtonText: "Fermer"
                }).then(() => {
                    window.location = "personne.php";
                });
            </script>
        </body>
        </html>
<?php
    }
}

$connexion->close();

function deleteDirectory($directory)
{
    if (is_dir($directory)) {
        $dirHandle = opendir($directory);
        while (($file = readdir($dirHandle)) !== false) {
            if ($file != '.' && $file != '..') {
                $path = $directory . '\\' . $file;
                if (is_dir($path)) {
                    deleteDirectory($path);
                } else {
                    unlink($path);
                }
            }
        }
        closedir($dirHandle);
        rmdir($directory);
    }
}
?>
