<?php
$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "12344321";
$base_de_donnees = "pfe";
$connexion = new mysqli($serveur, $utilisateur, $motdepasse, $base_de_donnees);
if ($connexion->connect_error) {
    die("Erreur de connexion à la base de données : " . $connexion->connect_error);
}

$nom = $connexion->real_escape_string($_POST['nom']);
$sqlVerif = "SELECT * FROM personne WHERE nom = '$nom'";
$resultat = $connexion->query(query: $sqlVerif);

if ($resultat->num_rows > 0) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Erreur</title>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    </head>
    <body>
        <script>
            swal({
                icon: "warning",
                title: "Erreur !",
                text: "Le nom existe déjà",
                showConfirmButton: true,
                confirmButtonText: "Fermer",
                closeOnConfirm: false
            }).then(function() {
                window.location = "add_person.html";
            });
        </script>
    </body>
    </html>
    <?php
} else {

    $age = $connexion->real_escape_string($_POST['age']);
    $sexe = $connexion->real_escape_string($_POST['sexe']);
    $description = $connexion->real_escape_string($_POST['description']);

    $dossierSource = $_FILES['dossier']['tmp_name'];
    $nomDossier = $_FILES['dossier']['name'];

    // Créer un nouveau dossier avec le nom de la personne dans le répertoire "dataset"
    $cheminNouveauDossier = "C:\\Users\\ERAZER\\Desktop\\pfe\\test\\data\\dataset" . $nom;
    if (!is_dir($cheminNouveauDossier)) {
        mkdir($cheminNouveauDossier);
    }

    // Définir le chemin de destination pour le dossier déplacé
    $cheminDestination = $cheminNouveauDossier . "/" . $nomDossier;

    // Déplacer le dossier complet vers le dossier de destination
    if (deplacerDossier($dossierSource, $cheminDestination)) {

        $nouveauCheminDossier = $cheminDestination;

        // Insérer la nouvelle personne dans la table "personne"
        $sql = "INSERT INTO personne (nom, age, sexe, dossier, description) VALUES ('$nom', '$age', '$sexe', '$nouveauCheminDossier', '$description')";
        if ($connexion->query($sql) === TRUE) {
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Succès</title>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            </head>
            <body>
                <script>
                    swal({
                        icon: "success",
                        title: "Bon travail !",
                        text: "La personne a été ajoutée",
                        showConfirmButton: true,
                        confirmButtonText: "Fermer",
                        closeOnConfirm: false
                    }).then(function() {
                        window.location = "personne.php";
                    });
                </script>
            </body>
            </html>
            <?php
        } else {
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Erreur</title>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            </head>
            <body>
                <script>
                    swal({
                        icon: "error",
                        title: "Erreur !",
                        text: "Erreur lors de l'ajout de la personne : <?php echo $connexion->error; ?>",
                        showConfirmButton: true,
                        confirmButtonText: "Fermer",
                        closeOnConfirm: false
                    }).then(function() {
                        window.location = "add_person.html";
                    });
                </script>
            </body>
            </html>
            <?php
        }
    } else {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Erreur</title>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        </head>
        <body>
            <script>
                swal({
                    icon: "error",
                    title: "Erreur !",
                    text: "Erreur lors du déplacement du dossier!",
                    showConfirmButton: true,
                    confirmButtonText: "Fermer",
                    closeOnConfirm: false
                }).then(function() {
                    window.location = "add_person.html";
                });
            </script>
        </body>
        </html>
        <?php
    }
}

// Fermer la connexion à la base de données
$connexion->close();

// Fonction récursive pour déplacer tous les fichiers et sous-dossiers
function deplacerDossier($source, $destination)
{
    if (is_dir($source)) {
        mkdir($destination);
        $contenuDossier = scandir($source);
        foreach ($contenuDossier as $element) {
            if ($element != '.' && $element != '..') {
                deplacerDossier($source . '/' . $element, $destination . '/' . $element);
            }
        }
        return rmdir($source);
    } else {
        return move_uploaded_file($source, $destination);
    }
}
?>
