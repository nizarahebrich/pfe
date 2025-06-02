<?php
$host = 'localhost';
$dbname = 'gestion_cours';
$user = 'root';
$pass = '12344321';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $role = $_POST['role'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['passwordd'], PASSWORD_DEFAULT);
    $genre = $_POST['genre'];
    $nom_f = $_POST['nom_f'];

    $stmt = $conn->prepare("SELECT id_f FROM filiere WHERE nom_f = ?");
    $stmt->bind_param("s", $nom_f);
    $stmt->execute();
    $result = $stmt->get_result();
    $filiere = $result->fetch_assoc();
    $stmt->close();

    if ($filiere) {
        $id_f = $filiere['id_f'];
    } else {
        $stmt = $conn->prepare("INSERT INTO filiere (nom_f) VALUES (?)");
        $stmt->bind_param("s", $nom_f);
        $stmt->execute();
        $id_f = $conn->insert_id;
        $stmt->close();
    }

    if ($role === 'etud') {
        $stmt = $conn->prepare("INSERT INTO etud (genre, username, email, passwordd, id_f) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $genre, $username, $email, $password, $id_f);
    } elseif ($role === 'prof') {
        $stmt = $conn->prepare("INSERT INTO prof (genre, username, email, passwordd, id_f) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $genre, $username, $email, $password, $id_f);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Inscription réussie !'); window.location.href='login.html';</script>";
    } else {
        if ($conn->errno === 1062) {
            echo "<script>alert('Erreur : Email déjà utilisé.'); history.back();</script>";
        } else {
            echo "Erreur : " . $conn->error;
        }
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a81368914c.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        #codeProfField {
            display: none;
        }
    </style>
</head>
<body>
    <img class="wave" src="img/wave (2).png" alt="Wave">
    <div class="container">
        <div class="img">
            <img src="img/bts4.png" alt="BTS">
        </div>
        <div class="login-content">
            <form method="POST">
                <img src="img/bts2.png" alt="Avatar">
                <h2 class="title">BTS Cours</h2>
                <h3 class="title">Créer un compte</h3>

                <div class="div">
                    <h5 class="title">Rôle</h5>
                    <label><input type="radio" name="role" value="etud" checked onchange="toggleCodeProf()"> Étudiant</label>
                    <label><input type="radio" name="role" value="prof" onchange="toggleCodeProf()"> Professeur</label>
                </div>

                <div class="input-div one">
                    <div class="i"><i class="fas fa-user"></i></div>
                    <div class="div">
                        <h5>Nom d'utilisateur</h5>
                        <input type="text" class="input" name="username" required>
                    </div>
                </div>

                <div class="input-div one">
                    <div class="i"><i class="fas fa-envelope"></i></div>
                    <div class="div">
                        <h5>Email</h5>
                        <input type="email" class="input" name="email" required>
                    </div>
                </div>

                <div class="div">
                    <h5 class="title">Genre</h5>
                    <label><input type="radio" name="genre" value="M" checked> Homme</label>
                    <label><input type="radio" name="genre" value="F"> Femme</label>
                </div>

                <div class="input-div pass">
                    <div class="i"><i class="fas fa-lock"></i></div>
                    <div class="div">
                        <h5>Mot de passe</h5>
                        <input type="password" class="input" name="passwordd" required>
                    </div>
                </div>

                <div class="input-div one" id="codeProfField">
                    <div class="i"><i class="fas fa-id-badge"></i></div>
                    <div class="div">
                        <h5>Code Professeur</h5>
                        <input type="text" class="input" name="code_prof">
                    </div>
                </div>

                <div class="input-div one">
                    <div class="i"><i class="fas fa-graduation-cap"></i></div>
                    <div class="div">
                        <h5>Filière </h5>
                        <input type="text" class="input" name="nom_f" required>
                    </div>
                </div>

                <input type="submit" class="btn" value="S'inscrire">
                <a href="login.html">Déjà inscrit ? Connexion</a>
            </form>
        </div>
    </div>

    <script>
        function toggleCodeProf() {
            const role = document.querySelector('input[name="role"]:checked').value;
            const codeProfField = document.getElementById('codeProfField');
            codeProfField.style.display = (role === 'prof') ? 'block' : 'none';
        }
        document.addEventListener('DOMContentLoaded', toggleCodeProf);
    </script>
</body>
</html>
