<?php
$host = 'localhost';
$dbname = 'gestion_cours';
$user = 'root';
$pass = '12344321';

session_start();
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch all filieres for dropdown
$filiere_list = [];
$result = $conn->query("SELECT id_f, nom_f FROM filiere ORDER BY nom_f ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $filiere_list[] = $row;
    }
    $result->free();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['passwordd'] ?? '';
    $id_f = intval($_POST['id_f']); // Now comes from dropdown, id instead of name

    if (!empty($password)) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        if ($role === 'etud') {
            $stmt = $conn->prepare("UPDATE etud SET username=?, email=?, passwordd=?, id_f=? WHERE id=?");
            $stmt->bind_param("sssii", $username, $email, $password_hashed, $id_f, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE prof SET username=?, email=?, passwordd=?, id_f=? WHERE id=?");
            $stmt->bind_param("sssii", $username, $email, $password_hashed, $id_f, $user_id);
        }
    } else {
        if ($role === 'etud') {
            $stmt = $conn->prepare("UPDATE etud SET username=?, email=?, id_f=? WHERE id=?");
            $stmt->bind_param("ssii", $username, $email, $id_f, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE prof SET username=?, email=?, id_f=? WHERE id=?");
            $stmt->bind_param("ssii", $username, $email, $id_f, $user_id);
        }
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profil mis à jour !'); window.location.href='changer_profil.php';</script>";
    } else {
        if ($conn->errno === 1062) {
            echo "<script>alert('Erreur : Email déjà utilisé.'); history.back();</script>";
        } else {
            echo "Erreur : " . $conn->error;
        }
    }
    $stmt->close();
}

// Fetch user info from correct table
if ($role === 'etud') {
    $stmt = $conn->prepare("SELECT username, email, id_f FROM etud WHERE id = ?");
} else {
    $stmt = $conn->prepare("SELECT username, email, id_f FROM prof WHERE id = ?");
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Changer Profil</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a81368914c.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
    <img class="wave" src="img/wave (2).png" alt="Wave" />
    <div class="container">
        <div class="img">
            <img src="img/bts4.png" alt="BTS" />
        </div>
        <div class="login-content">
            <form method="POST" action="">
                <img src="img/bts2.png" alt="Avatar" />
                <h2 class="title">BTS Cours</h2>
                <h3 class="title">Modifier votre profil</h3>


                <div class="input-div one">
                    <div class="i"><i class="fas fa-user"></i></div>
                    <div class="div">
                        <h5>Nom d'utilisateur</h5>
                        <input
                            type="text"
                            class="input"
                            name="username"
                        />
                    </div>
                </div>

                <div class="input-div one">
                    <div class="i"><i class="fas fa-envelope"></i></div>
                    <div class="div">
                        <h5>Email</h5>
                        <input
                            type="email"
                            class="input"
                            name="email"

                        />
                    </div>
                </div>

                <div class="input-div pass">
                    <div class="i"><i class="fas fa-lock"></i></div>
                    <div class="div">
                        <h5>Mot de passe (laisser vide pour garder actuel)</h5>
                        <input
                            type="password"
                            class="input"
                            name="passwordd"
                        />
                    </div>
                </div>

                <div class="input-div one">
                    <div class="i"><i class="fas fa-graduation-cap"></i></div>
                    <div class="div">
                        <h5>Filière</h5>
                        <select name="id_f" class="input" required>
                            <option value="">-- Sélectionnez une filière --</option>
                            <?php foreach ($filiere_list as $filiere): ?>
                                <option
                                    value="<?= $filiere['id_f'] ?>"
                                    <?= ($user['id_f'] == $filiere['id_f']) ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($filiere['nom_f']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <input type="submit" class="btn" value="Mettre à jour" />
                <a href="profil.php">Retour au profil</a>
            </form>
        </div>
    </div>
</body>
</html>
