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

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$etud_id = $_SESSION['user_id'];
$message = "";
$message_type = ""; // "success" or "error"

// Fetch current user data, including photo
$stmt = $conn->prepare("SELECT username, email, passwordd, photo FROM etud WHERE id = ?");
$stmt->bind_param("i", $etud_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Utilisateur non trouvé.");
}

$current_name = $user['username'];
$current_email = $user['email'];
$current_password_db = $user['passwordd'];
$current_photo = $user['photo'] ?? null;

$passwords_hashed = false; // adapte selon ton usage

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Vérification du mot de passe actuel
    if ($passwords_hashed) {
        if (!password_verify($current_password, $current_password_db)) {
            $message = "Mot de passe actuel incorrect.";
            $message_type = "error";
        }
    } else {
        if ($current_password !== $current_password_db) {
            $message = "Mot de passe actuel incorrect.";
            $message_type = "error";
        }
    }

    // Vérification confirmation mot de passe
    if (empty($message) && $new_password !== $confirm_password) {
        $message = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
        $message_type = "error";
    }

    // Gestion de l'upload photo
    $upload_dir = __DIR__ . '/uploads/photos/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $new_photo_filename = $current_photo; // par défaut, on garde l'ancienne

    if (empty($message) && isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $photo = $_FILES['photo'];

        // Vérifications simples (taille, type)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($photo['type'], $allowed_types)) {
            $message = "Format de fichier non autorisé. Seuls JPEG, PNG, GIF sont acceptés.";
            $message_type = "error";
        } elseif ($photo['size'] > 2 * 1024 * 1024) { // 2 Mo max
            $message = "Le fichier est trop volumineux (max 2 Mo).";
            $message_type = "error";
        } else {
            // Générer un nom unique pour éviter écrasement
            $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
            $new_photo_filename = uniqid('photo_') . '.' . $ext;

            $destination = $upload_dir . $new_photo_filename;

            if (!move_uploaded_file($photo['tmp_name'], $destination)) {
                $message = "Erreur lors de l'upload de la photo.";
                $message_type = "error";
            } else {
                // Supprimer ancienne photo si existante et différente de défaut
                if ($current_photo && file_exists($upload_dir . $current_photo)) {
                    unlink($upload_dir . $current_photo);
                }
            }
        }
    }

    // Mise à jour si pas d'erreur
    if (empty($message)) {
        if ($passwords_hashed) {
            $password_to_store = password_hash($new_password, PASSWORD_DEFAULT);
        } else {
            $password_to_store = $new_password;
        }

        $update_stmt = $conn->prepare("UPDATE etud SET username = ?, email = ?, passwordd = ?, photo = ? WHERE id = ?");
        $update_stmt->bind_param("ssssi", $name, $email, $password_to_store, $new_photo_filename, $etud_id);

        if ($update_stmt->execute()) {
            $message = "Profil mis à jour avec succès !";
            $message_type = "success";
            $current_name = $name;
            $current_email = $email;
            $current_password_db = $password_to_store;
            $current_photo = $new_photo_filename;
        } else {
            $message = "Erreur lors de la mise à jour du profil.";
            $message_type = "error";
        }
        $update_stmt->close();
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Changer le Profil</title>
  <link rel="stylesheet" href="profile.css" />
  <style>
    .message {
      padding: 10px 15px;
      margin-bottom: 20px;
      border-radius: 4px;
      font-weight: 600;
      font-size: 1rem;
      max-width: 400px;
    }
    .message.error {
      background-color: #ffe6e6;
      color: #cc0000;
      border: 1px solid #cc0000;
    }
    .message.success {
      background-color: #e6ffe6;
      color: #006600;
      border: 1px solid #006600;
    }
    .profile-pic img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      display: block;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Modifier votre profil</h1>

    <?php if($message): ?>
      <div class="message <?php echo $message_type; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <form class="profile-form" action="#" method="post" enctype="multipart/form-data">
      <div class="profile-pic">
        <img src="uploads/photos/<?php echo htmlspecialchars($current_photo ?? 'default-profile.png'); ?>" alt="Photo de profil" id="preview" />
        <input type="file" name="photo" accept="image/*" onchange="previewImage(event)" />
      </div>

      <label for="name">Nom complet</label>
      <input
        type="text"
        id="name"
        name="name"
        placeholder="Entrez votre nom complet"
        required
        value="<?php echo htmlspecialchars($current_name); ?>"
      />

      <label for="email">Adresse e-mail</label>
      <input
        type="email"
        id="email"
        name="email"
        placeholder="exemple@mail.com"
        required
        value="<?php echo htmlspecialchars($current_email); ?>"
      />

      <label for="current_password">Mot de passe actuel</label>
      <input
        type="password"
        id="current_password"
        name="current_password"
        placeholder="••••••••"
        required
      />

      <label for="password">Nouveau mot de passe</label>
      <input
        type="password"
        id="password"
        name="password"
        placeholder="••••••••"
        required
      />

      <label for="confirm_password">Confirmer le mot de passe</label>
      <input
        type="password"
        id="confirm_password"
        name="confirm_password"
        placeholder="••••••••"
        required
      />

      <button type="submit">Enregistrer les modifications</button>
      <button type="button" onclick="window.location.href='etud.php';" style="margin-left: 10px;">
        Retour au menu
      </button>
    </form>
  </div>

  <script>
    function previewImage(event) {
      const reader = new FileReader();
      reader.onload = function () {
        const output = document.getElementById("preview");
        output.src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>
</body>
</html>
