<?php
session_start();

$host = 'localhost';
$dbname = 'gestion_cours';
$user = 'root';
$pass = '2005';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password) || empty($role)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        if ($role !== 'prof' && $role !== 'etud') {
            $error = "Rôle invalide.";
        } else {
            $stmt = $conn->prepare("SELECT id, username, passwordd FROM $role WHERE email = ?");
            if (!$stmt) {
                die("Erreur préparation requête: " . $conn->error);
            }
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                if ($password === $user['passwordd']) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $role;
                    $stmt->close();

                    if ($role === 'prof') {
                        header("Location: prof_dashboard.php");
                    } else {
                        header("Location: etud.php");
                    }
                    exit;
                } else {
                    $error = "Email ou mot de passe incorrect.";
                }
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a81368914c.js" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <img class="wave" src="img/wave (2).png" alt="Background Wave Image">
    <div class="container">
        <div class="img">
            <img src="img/bts4.png" alt="Illustration BTS">
        </div>
        <div class="login-content">
            <form method="POST">
                <img src="img/bts2.png" alt="Logo">
                <h2 class="title">BTS Cours</h2>
                <h3 class="title">Bienvenue</h3>

                <?php if (!empty($error)) : ?>
                    <div class="error-message" style="color:red; font-size: 0.9rem; margin-bottom: 10px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="radio-group" role="radiogroup" aria-labelledby="role-label">
                    <label id="role-label">Rôle :</label>
                    <div class="radio-option">
                        <label>
                            <input type="radio" name="role" value="prof" <?= (isset($role) && $role === 'prof') ? 'checked' : '' ?> required>
                            Professeur
                        </label>
                    </div>
                    <div class="radio-option">
                        <label>
                            <input type="radio" name="role" value="etud" <?= (isset($role) && $role === 'etud') ? 'checked' : '' ?> required>
                            Étudiant
                        </label>
                    </div>
                </div>

                <div class="floating-label">
                    <input type="email" name="email" id="email" placeholder="Email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                    <label for="email">Adresse e-mail</label>
                </div>

                <div class="floating-label">
                    <input type="password" name="password" id="password" placeholder="Mot de passe" required>
                    <label for="password">Mot de passe</label>
                </div>

                <input type="submit" class="btn" value="Se connecter">
                <a href="inscrire.html">Pas encore inscrit ?</a>
            </form>
        </div>
    </div>
    <script src="js/main.js"></script>
</body>

</html>
