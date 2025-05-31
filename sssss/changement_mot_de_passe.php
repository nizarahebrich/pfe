<?php
// Configuration de la connexion à la base de données
$servername = 'localhost';
$username = 'root';
$password = '12344321';
$dbname = 'pfe';

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification des erreurs de connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérification des données du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST["email"]));
    $oldPassword = htmlspecialchars(trim($_POST["old_password"]));
    $newPassword = htmlspecialchars(trim($_POST["new_password"]));

    // Validation des données
    if (empty($email) || empty($oldPassword) || empty($newPassword)) {
        echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
        echo '<script>
                swal({
                    icon: "warning",
                    title: "Erreur !",
                    text: "Tous les champs doivent être remplis.",
                    showConfirmButton: true,
                    confirmButtonText: "Fermer",
                    closeOnConfirm: false
                }).then(function(result) {
                    window.location = "changement_mot_de_passe.php";
                })
              </script>';
        exit();
    }

    // Préparation de la requête SQL pour vérifier les informations d'identification
    $stmt = $conn->prepare("SELECT password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérification des informations d'identification
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        // Vérification du mot de passe
        if (password_verify($oldPassword, $hashedPassword)) {
            // Hachage du nouveau mot de passe
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // Préparation de la requête SQL pour mettre à jour le mot de passe
            $updateStmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
            $updateStmt->bind_param("ss", $newPasswordHash, $email);

            if ($updateStmt->execute()) {
                echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
                echo '<script>
                        swal({
                            icon: "success",
                            title: "Bon travail !",
                            text: "Félicitations, le mot de passe a été modifié avec succès.",
                            showConfirmButton: true,
                            confirmButtonText: "Fermer",
                            closeOnConfirm: false
                        }).then(function(result) {
                            window.location = "page_reussie.php";
                        })
                      </script>';
            } else {
                echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
                echo '<script>
                        swal({
                            icon: "warning",
                            title: "Erreur !",
                            text: "Erreur lors de la mise à jour du mot de passe : ' . $updateStmt->error . '",
                            showConfirmButton: true,
                            confirmButtonText: "Fermer",
                            closeOnConfirm: false
                        }).then(function(result) {
                            window.location = "changement_mot_de_passe.php";
                        })
                      </script>';
            }

            $updateStmt->close();
        } else {
            // Ancien mot de passe incorrect
            echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
            echo '<script>
                    swal({
                        icon: "warning",
                        title: "Erreur !",
                        text: "Ancien mot de passe incorrect. Veuillez réessayer.",
                        showConfirmButton: true,
                        confirmButtonText: "Fermer",
                        closeOnConfirm: false
                    }).then(function(result) {
                        window.location = "changement_mot_de_passe.php";
                    })
                  </script>';
        }
    } else {
        // Email non trouvé
        echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
        echo '<script>
                swal({
                    icon: "warning",
                    title: "Erreur !",
                    text: "Adresse e-mail non trouvée. Veuillez vérifier.",
                    showConfirmButton: true,
                    confirmButtonText: "Fermer",
                    closeOnConfirm: false
                }).then(function(result) {
                    window.location = "changement_mot_de_passe.php";
                })
              </script>';
    }

    $stmt->close();
    $conn->close();
}

