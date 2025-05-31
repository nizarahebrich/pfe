<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
     <link rel="stylesheet" type="text/css" href="css/style1.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-top: 0;
        }

        .user-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .user-list li {
            background-color: #f9f9f9;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
        }

        .user-list li:last-child {
            border-bottom: none;
        }

        .user-list li .user-info {
            flex-grow: 1;
        }

        .user-list li .user-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .user-list li .user-options {
            display: flex;
            align-items: center;
        }

        .user-list li .user-options select {
            margin-right: 10px;
        }
    </style>


    <title>Supprimer un utilisateur</title>

    <link rel="stylesheet" type="text/css" href="css/style1.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
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
                <a href="page_reussie.php">Accueil</a>
                <ul class="submenu">
                    <li><a href="today.php">Aujourd'hui</a></li>
                    <li><a href="heir.php">Hier</a></li>
                    <li><a href="affichage_detections.php">recherché un date</a></li>
                </ul>
            </li>
           
            <li>
                <a href="#">Paramètres</a>
                <ul class="submenu">
                    <li><a href="add_person.html">ajoutée un personne </a></li>
                    <li><a href="delet_person.html">Supprimer un personne </a></li>
                    <li><a href="personne.php"> List personnes </a></li>
                    <li><a href="add_user.html">ajouter un utilisateur</a></li>
                    <li><a href="delet_user.html">Supprimer un utilisateur</a></li>
                    <li><a href="modi_user.php">Modifier utilisateur</a></li>
                    <li><a href="modif_password.html">Changer mot de passer </a></li>
                </ul>
            </li>
            <li><a href="index.html">Déconnexion</a></li>
        </ul>
    </div>


    <?php
    // Connexion à la base de données
    $servername = 'localhost';
    $username = 'root';
    $password = '12344321';
    $dbname = 'pfe';

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérification des erreurs de connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Traitement des actions de modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $userId = $_POST['user_id'] ?? '';

        if ($action === 'delete') {
            // Supprimer l'utilisateur
            $sql = "DELETE FROM user WHERE userid = '$userId'";
            if ($conn->query($sql) === true) {
                echo "Utilisateur supprimé avec succès.";
            } else {
                echo "Erreur lors de la suppression de l'utilisateur: " . $conn->error;
            }
        } elseif ($action === 'block') {
            // Bloquer l'utilisateur
            $sql = "UPDATE user SET compte = 'bloque' WHERE userid = '$userId'";
            if ($conn->query($sql) === true) {
                echo "";
    ?>
                <html>

                <body>

                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                        swal({
                            icon: "success",
                            title: "bon travail !",
                            text: "  Utilisateur bloqué avec succès.",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            closeOnConfirm: false
                        }).then(function(result) {
                            window.location = "employes.php";
                        })
                    </script>

                </body>

                </html>
            <?php
            } else {
                echo "Erreur lors du blocage de l'utilisateur: " . $conn->error;
            }
        } elseif ($action === 'unblock') {
            // Débloquer l'utilisateur
            $sql = "UPDATE user SET compte = 'debloque' WHERE userid = '$userId'";
            if ($conn->query($sql) === true) {

            ?>
                <html>

                <body>

                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                        swal({
                            icon: "success",
                            title: "bon travail !",
                            text: " Utilisateur débloqué avec succès.",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            closeOnConfirm: false
                        }).then(function(result) {
                            window.location = "employes.php";
                        })
                    </script>

                </body>

                </html>
            <?php
            } else {
                echo "Erreur lors du déblocage de l'utilisateur: " . $conn->error;
            }
        } elseif ($action === 'update_role') {
            // Modifier le rôle de l'utilisateur
            $newRole = $_POST['new_role'] ?? '';
            $sql = "UPDATE user SET role = '$newRole' WHERE userid = '$userId'";
            if ($conn->query($sql) === true) {

            ?>
                <html>

                <body>

                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                        swal({
                            icon: "success",
                            title: "bon travail !",
                            text: "  Félicitations,Rôle de l'utilisateur mis à jour avec succès.",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            closeOnConfirm: false
                        }).then(function(result) {
                            window.location = "modi_user.php";
                        })
                    </script>

                </body>

                </html>
            <?php
            } else {
                echo "Erreur lors de la mise à jour du rôle de l'utilisateur: " . $conn->error;
            }
        } elseif ($action === 'update_compte') {
            // Modifier le champ "compte" de l'utilisateur
            $newCompte = $_POST['new_compte'] ?? '';
            $sql = "UPDATE user SET compte = '$newCompte' WHERE userid = '$userId'";
            if ($conn->query($sql) === true) {
            ?>
                <html>

                <body>

                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                        swal({
                            icon: "success",
                            title: "bon travail !",
                            text: " Champ 'compte' de l'utilisateur mis à jour avec succès. ",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            closeOnConfirm: false
                        }).then(function(result) {
                            window.location = "employes.php";
                        })
                    </script>

                </body>

                </html>
            <?php


            } else {

            ?>
                <html>

                <body>

                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                        swal({
                            icon: "warning",
                            title: "Erreur !",
                            text: "Erreur lors de la mise à jour du champ 'compte' de l'utilisateur: . $conn->error",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            closeOnConfirm: false
                        }).then(function(result) {
                            window.location = "modi_user.php";
                        })
                    </script>

                </body>

                </html>
    <?php
            }
        }
    }

    // Récupération de la liste des utilisateurs
    $sql = "SELECT * FROM user";
    $result = $conn->query($sql);

    // Fermeture de la connexion à la base de données
    $conn->close();
    ?>
    </head>

    <body>
        <div class="container">
            <h1>Gestion des utilisateurs</h1>
            <ul class="user-list">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $userId = $row['userid'];
                        $userName = $row['name'];
                        $mail = $row['email'];
                        $userStatus = $row['compte'];
                        $userRole = $row['role'];

                        echo '<li>';
                        echo '<div class="user-info">';
                        echo '<p class="user-name">' . $userName . '</p>';
                        echo '</div>';
                        echo '<div class="user-info">';
                        echo '<p class="user-name">' . $mail . '</p>';
                        echo '</div>';
                        echo '<div class="user-options">';
                        echo '<form method="post" class="rech">';
                        echo '<input type="hidden" name="user_id" value="' . $userId . '">';

                        echo '<select name="new_role">';
                        echo '<option value="administrateur" ' . ($userRole === 'administrateur' ? 'selected' : '') . '>Administrateur</option>';
                        echo '<option value="utilisateur" ' . ($userRole === 'utilisateur' ? 'selected' : '') . '>Utilisateur</option>';
                        echo '</select>';
                        echo '<select name="new_compte">';
                        echo '<option value="bloque" ' . ($userStatus === 'bloque' ? 'selected' : '') . '>Bloqué</option>';
                        echo '<option value="debloque" ' . ($userStatus === 'debloque' ? 'selected' : '') . '>Débloqué</option>';
                        echo '</select>';
                        echo '<input type="hidden" name="action" value="update_role">';
                        echo '<input type="submit" value="Modifier">';
                        echo '</form>';
                        echo '</div>';
                        echo '</li>';
                    }
                } else {
                ?>
                    <html>

                    <body>

                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                            swal({
                                icon: "warning",
                                title: "Erreur !",
                                text: "Aucun utilisateur trouvé.",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar",
                                closeOnConfirm: false
                            }).then(function(result) {
                                window.location = "delet_user.html";
                            })
                        </script>

                    </body>

                    </html>
                <?php

                }
                ?>
            </ul>
        </div>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/js/bootstrap.bundle.min.js"></script>

    </body>

</html>