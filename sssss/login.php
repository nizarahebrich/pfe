<?php
session_start();

$host = 'localhost';
$dbname = 'gestion_cours';
$user = 'root';
$pass = '12344321';

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
            $error = "Role invalide.";
        } else {
            $stmt = $conn->prepare("SELECT id, username, passwordd FROM $role WHERE email = ?");
            if (!$stmt) {
                die("Erreur préparation requête: " . $conn->error);
            }
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                // Password comparison (plaintext for now)
                if ($password === $user['passwordd']) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $role;
                    $stmt->close();

                    // Redirect based on role, or common dashboard
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
    <title>Login Form</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a81368914c.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <img class="wave" src="img/wave (2).png" alt="Background Wave Image">
    <div class="container">
        <div class="img">
            <img src="img/bts4.png" alt="Background Image">
        </div>
        <div class="login-content">
            <form method="POST" novalidate>
                <img src="img/bts2.png" alt="Avatar">
                <h2 class="title">BTS Cours</h2>
                <h3 class="title">Welcome</h3>

                <?php if (!empty($error)) : ?>
                    <div class="error-message" role="alert"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="div" role="radiogroup" aria-labelledby="role-label">
                    <h5 class="title" id="role-label">Role</h5>
                    <label>
                        <input type="radio" class="input" name="role" value="prof" <?= (isset($role) && $role === 'prof') ? 'checked' : '' ?> required>
                        Prof
                    </label>
                    <label>
                        <input type="radio" class="input" name="role" value="etud" <?= (isset($role) && $role === 'etud') ? 'checked' : '' ?> required>
                        Etud
                    </label>
                </div>

                <div class="input-div one">
                    <div class="i">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="div">
                        <h5>Email</h5>
                        <input type="email" class="input" name="email" required autocomplete="username" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                    </div>
                </div>

                <div class="input-div pass">
                    <div class="i">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <h5>Mot de passe</h5>
                        <input type="password" class="input" name="password" required autocomplete="current-password">
                    </div>
                </div>

                <input type="submit" class="btn" value="Login">
                <a href="inscrire.html">Inscrire?</a>
            </form>
        </div>
    </div>
    <script src="js/main.js" defer></script>
</body>

</html>
