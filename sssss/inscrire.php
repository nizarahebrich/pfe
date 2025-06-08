<?php
$host = 'localhost';
$dbname = 'gestion_cours';
$user = 'root';
$pass = '12344321';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$message = "";
$message_type = "";
$selected_role = 'etud';
$selected_genre = 'M';
$input_username = '';
$input_email = '';
$input_nom_f = '';
$input_code_prof = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selected_role = htmlspecialchars(trim($_POST['role']));
    $input_username = htmlspecialchars(trim($_POST['username']));
    $input_email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['passwordd']);
    $selected_genre = htmlspecialchars(trim($_POST['genre']));
    $input_nom_f = htmlspecialchars(trim($_POST['nom_f']));
    $input_code_prof = isset($_POST['code_prof']) ? htmlspecialchars(trim($_POST['code_prof'])) : null;

    $stmt = $conn->prepare("SELECT id_f FROM filiere WHERE nom_f = ?");
    $stmt->bind_param("s", $input_nom_f);
    $stmt->execute();
    $result = $stmt->get_result();
    $filiere = $result->fetch_assoc();
    $stmt->close();

    if ($filiere) {
        $id_f = $filiere['id_f'];
    } else {
        $stmt = $conn->prepare("INSERT INTO filiere (nom_f) VALUES (?)");
        $stmt->bind_param("s", $input_nom_f);
        $stmt->execute();
        $id_f = $conn->insert_id;
        $stmt->close();
    }

    if ($selected_role === 'prof') {
        if ($input_code_prof !== "2005") {
            $message = "Code Professeur incorrect !";
            $message_type = "error";
        }
    }

    if (empty($message)) {
        if ($selected_role === 'etud') {
            $stmt = $conn->prepare("INSERT INTO etud (genre, username, email, passwordd, id_f) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $selected_genre, $input_username, $input_email, $password, $id_f);
        } elseif ($selected_role === 'prof') {
            $stmt = $conn->prepare("INSERT INTO prof (genre, username, email, passwordd, id_f) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $selected_genre, $input_username, $input_email, $password, $id_f);
        }

        if ($stmt->execute()) {
            echo "<script>alert('Inscription réussie !'); window.location.href='login.php';</script>";
            exit();
        } else {
            if ($conn->errno === 1062) {
                $message = "Erreur : Email déjà utilisé.";
                $message_type = "error";
            } else {
                $message = "Erreur : " . $conn->error;
                $message_type = "error";
            }
        }
        $stmt->close();
    }
}

$filieresResult = $conn->query("SELECT nom_f FROM filiere ORDER BY nom_f ASC");
$filieres = [];
if ($filieresResult && $filieresResult->num_rows > 0) {
    while ($row = $filieresResult->fetch_assoc()) {
        $filieres[] = $row['nom_f'];
    }
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
        .message.error {
            color: #b00020;
            background-color: #fddede;
            border: 1px solid #b00020;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-weight: bold;
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
            <?php if (!empty($message)): ?>
                <div class="message <?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <img src="img/bts2.png" alt="Avatar">
                <h2 class="title">BTS Cours</h2>
                <h3 class="title">Créer un compte</h3>

                <div class="div">
                    <h5 class="title">Rôle</h5>
                    <label>
                        <input type="radio" name="role" value="etud" onchange="toggleCodeProf()" <?= ($selected_role === 'etud') ? 'checked' : '' ?>>
                        Étudiant
                    </label>
                    <label>
                        <input type="radio" name="role" value="prof" onchange="toggleCodeProf()" <?= ($selected_role === 'prof') ? 'checked' : '' ?>>
                        Professeur
                    </label>
                </div>

                <div class="floating-label">
                    <input type="text" id="username" name="username" placeholder=" " value="<?= $input_username ?>" required>
                    <label for="username">Nom d'utilisateur</label>
                </div>

                <div class="floating-label">
                    <input type="email" id="email" name="email" placeholder=" " value="<?= $input_email ?>" required>
                    <label for="email">Email</label>
                </div>

                <div class="div">
                    <h5 class="title">Genre</h5>
                    <label><input type="radio" name="genre" value="M" <?= ($selected_genre === 'M') ? 'checked' : '' ?>> Homme</label>
                    <label><input type="radio" name="genre" value="F" <?= ($selected_genre === 'F') ? 'checked' : '' ?>> Femme</label>
                </div>

                <div class="floating-label">
                    <input type="password" id="passwordd" name="passwordd" placeholder=" " required>
                    <label for="passwordd">Mot de passe</label>
                </div>

                <div class="floating-label" id="codeProfField">
                    <input type="text" id="code_prof" name="code_prof" placeholder=" " value="<?= $input_code_prof ?>">
                    <label for="code_prof">Code Professeur</label>
                </div>

                <div class="floating-label">
                    <select id="nom_f" name="nom_f" required>
                        <option value="" disabled <?= $input_nom_f === '' ? 'selected' : '' ?>>Filière</option>
                        <?php foreach ($filieres as $filiere): ?>
                            <option value="<?= htmlspecialchars($filiere) ?>" <?= ($input_nom_f === $filiere) ? 'selected' : '' ?>><?= htmlspecialchars($filiere) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="submit" class="btn" value="S'inscrire">
                <a href="login.php">Déjà inscrit ? Connexion</a>
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
