<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe</title>
    <link rel="stylesheet" type="text/css" href="css/style1.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <script>
        // JavaScript pour rendre le menu dynamique
        window.addEventListener('DOMContentLoaded', (event) => {
            const menuItems = document.querySelectorAll('.menu ul li');
            menuItems.forEach(item => {
                item.addEventListener('click', () => {
                    menuItems.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                });
            });
        });
    </script>
</head>

<body>

    <div class="menu">
        <ul>
            <li class="active">
                <div class="header">
                    <img src="img/logo.png" alt="Logo">
                    <h1>FaceDetect</h1>
                </div>
                <a href="page_reussie2.php">Accueil</a>
                <ul class="submenu">
                    <li><a href="today2.php">Aujourd'hui</a></li>
                    <li><a href="heir2.php">Hier</a></li>
                    <li><a href="affichage_detections2.php">Rechercher une date</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Profil</a>
                <ul class="submenu">
                    <li><a href="modif_password2.php">Changer le mot de passe</a></li>
                    <li><a href="personne2.php">Liste des personnes</a></li>
                    <li><a href="index.html">Déconnexion</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="content">
        <h2>Changer le mot de passe</h2>
        <form method="POST" class="rech">
            <div>
                <label for="email">Adresse e-mail :</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div>
                <label for="old_password">Ancien mot de passe :</label>
                <input type="password" name="old_password" id="old_password" required>
            </div>
            <div>
                <label for="new_password">Nouveau mot de passe :</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
            <div>
                <input type="submit" value="Changer le mot de passe">
            </div>
        </form>
    </div>
</body>

</html>

<?php
$servername = 'localhost';
$username = 'root';
$password = '12344321';
$dbname = 'pfe';

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification des erreurs de connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $oldPassword = $_POST["old_password"];
    $newPassword = $_POST["new_password"];

    // Validation des données
    if (empty($email) || empty($oldPassword) || empty($newPassword)) {
        echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
        echo '<script>
            swal({
                icon: "warning",
                title: "Erreur !",
                text: "Tous les champs sont requis.",
                showConfirmButton: true,
                confirmButtonText: "Fermer",
                closeOnConfirm: false
            }).then(function(result) {
                window.location = "modif_password2.php";
            })
        </script>';
        exit;
    }

    // Préparer la requête pour vérifier l'utilisateur
    $stmt = $conn->prepare("SELECT password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        $stmt->close();

        // Vérifier l'ancien mot de passe
        if (password_verify($oldPassword, $hashedPassword)) {
            // Hasher le nouveau mot de passe
            $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Préparer la requête pour mettre à jour le mot de passe
            $updateStmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
            $updateStmt->bind_param("ss", $newHashedPassword, $email);

            if ($updateStmt->execute()) {
                echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
                echo '<script>
                    swal({
                        icon: "success",
                        title: "Succès !",
                        text: "Le mot de passe a été modifié avec succès.",
                        showConfirmButton: true,
                        confirmButtonText: "Fermer",
                        closeOnConfirm: false
                    }).then(function(result) {
                        window.location = "index.html";
                    })
                </script>';
            } else {
                echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
                echo '<script>
                    swal({
                        icon: "error",
                        title: "Erreur !",
                        text: "Une erreur est survenue lors de la modification du mot de passe.",
                        showConfirmButton: true,
                        confirmButtonText: "Fermer",
                        closeOnConfirm: false
                    }).then(function(result) {
                        window.location = "modif_password2.php";
                    })
                </script>';
            }
            $updateStmt->close();
        } else {
            echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
            echo '<script>
                swal({
                    icon: "warning",
                    title: "Erreur !",
                    text: "Ancien mot de passe incorrect.",
                    showConfirmButton: true,
                    confirmButtonText: "Fermer",
                    closeOnConfirm: false
                }).then(function(result) {
                    window.location = "modif_password2.php";
                })
            </script>';
        }
    } else {
        echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
        echo '<script>
            swal({
                icon: "warning",
                title: "Erreur !",
                text: "Aucun utilisateur trouvé avec cet e-mail.",
                showConfirmButton: true,
                confirmButtonText: "Fermer",
                closeOnConfirm: false
            }).then(function(result) {
                window.location = "modif_password2.php";
            })
        </script>';
    }

    $conn->close();
}
?>
