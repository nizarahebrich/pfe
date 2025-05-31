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
    // Récupération des données du formulaire en les sécurisant
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));
    $compte = htmlspecialchars(trim($_POST["compte"]));
    $role = htmlspecialchars(trim($_POST["role"]));

    // Vérification que les champs ne sont pas vides
    if (empty($name) || empty($email) || empty($password) || empty($compte) || empty($role)) {
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
                    window.location = "add_user.html";
                })
              </script>';
        exit();
    }

    // Requête de recherche pour vérifier si l'email existe déjà
    $checkQuery = "SELECT COUNT(*) as count FROM user WHERE email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $emailExists = $row['count'];

    if ($emailExists > 0) {
        // Email existe déjà
        echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
        echo '<script>
                swal({
                    icon: "warning",
                    title: "Erreur !",
                    text: "L\'email existe déjà dans la base de données.",
                    showConfirmButton: true,
                    confirmButtonText: "Fermer",
                    closeOnConfirm: false
                }).then(function(result) {
                    window.location = "add_user.html";
                })
              </script>';
    } else {
        // Préparation de la requête SQL pour insérer un nouvel utilisateur
        $passwordHash = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe
        $stmt = $conn->prepare("INSERT INTO user (name, email, password, compte, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $passwordHash, $compte, $role);

        // Exécution de la requête SQL
        if ($stmt->execute()) {
            // Succès
            echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
            echo '<script>
                    swal({
                        icon: "success",
                        title: "Bon travail !",
                        text: "Utilisateur ajouté avec succès.",
                        showConfirmButton: true,
                        confirmButtonText: "Fermer",
                        closeOnConfirm: false
                    }).then(function(result) {
                        window.location = "modi_user.php";
                    })
                  </script>';
        } else {
            // Erreur lors de l'ajout de l'utilisateur
            echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
            echo '<script>
                    swal({
                        icon: "warning",
                        title: "Erreur !",
                        text: "Erreur lors de l\'ajout de l\'utilisateur : ' . $stmt->error . '",
                        showConfirmButton: true,
                        confirmButtonText: "Fermer",
                        closeOnConfirm: false
                    }).then(function(result) {
                        window.location = "add_user.html";
                    })
                  </script>';
        }

        // Fermeture de la requête et de la connexion
        $stmt->close();
    }
}

// Fermeture de la connexion à la base de données
$conn->close();

