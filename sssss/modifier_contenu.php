<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "12344321";
$dbname = "gestion_cours";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de contenu manquant.");
}

$id_contenu = $_GET['id'];

$stmt = $conn->prepare("SELECT titre, module, type FROM contenu WHERE id_contenu = ?");
$stmt->bind_param("i", $id_contenu);
$stmt->execute();
$result = $stmt->get_result();
$contenu = $result->fetch_assoc();
$stmt->close();

if (!$contenu) {
    die("Contenu introuvable.");
}
$typeResult = $conn->query("SELECT DISTINCT type FROM contenu ORDER BY type");
$types = [];
if ($typeResult) {
    while ($row = $typeResult->fetch_assoc()) {
        $types[] = $row['type'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST['titre'];
    $module = $_POST['module'];
    $type = $_POST['type'];

    if (!in_array($type, $types)) {
        die("Type invalide.");
    }

    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $fileName = uniqid() . "_" . basename($_FILES["fichier"]["name"]);
        $filePath = $uploadDir . $fileName;
        move_uploaded_file($_FILES["fichier"]["tmp_name"], $filePath);

        $stmt = $conn->prepare("UPDATE contenu SET titre=?, module=?, type=?, fichier=? WHERE id_contenu=?");
        $stmt->bind_param("ssssi", $titre, $module, $type, $fileName, $id_contenu);
    } else {
        $stmt = $conn->prepare("UPDATE contenu SET titre=?, module=?, type=? WHERE id_contenu=?");
        $stmt->bind_param("sssi", $titre, $module, $type, $id_contenu);
    }

    if ($stmt->execute()) {
        header("Location: vos_cours.php");
        exit();
    } else {
        echo "Erreur lors de la mise à jour.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Contenu</title>
    <style>
      body {
    font-family: 'Poppins', sans-serif;
    background-color: #0a192f;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    color: #ffffff;
}

.container {
    background: #102542;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 188, 212, 0.2);
    width: 500px;
}

h2 {
    margin-bottom: 20px;
    color: #00bcd4;
    text-align: center;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #ffffff;
}

input[type="text"],
select,
input[type="file"] {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 20px;
    border: 1px solid #33475b;
    background-color: #1b2b41;
    color: #ffffff;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s, background-color 0.3s;
}

input[type="text"]:focus,
select:focus,
input[type="file"]:focus {
    border-color: #00bcd4;
    background-color: #22354d;
    outline: none;
}

.file-label {
    font-size: 14px;
    color: #aaa;
}

button {
    width: 100%;
    background-color: #00bcd4;
    color: #0a192f;
    border: none;
    padding: 12px;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #00a2bb;
}

.back {
    margin-top: 10px;
    text-align: center;
}

.back a {
    color: #00bcd4;
    text-decoration: none;
    font-weight: 600;
}

.back a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>

<div class="container">
    <h2>Modifier Contenu</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($contenu['titre']); ?>" required>

        <label for="module">Module :</label>
        <input type="text" id="module" name="module" value="<?php echo htmlspecialchars($contenu['module']); ?>" required>

        <label for="type">Type :</label>
        <select id="type" name="type" required>
            <?php foreach ($types as $t): ?>
                <option value="<?php echo htmlspecialchars($t); ?>" <?php echo ($contenu['type'] === $t) ? 'selected' : ''; ?>>
                    <?php echo ucfirst(htmlspecialchars($t)); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="fichier">Changer de fichier (facultatif) :</label>
        <input type="file" id="fichier" name="fichier" accept=".pdf,.doc,.docx,.ppt,.pptx">
        <span class="file-label" id="file-name"></span>

        <button type="submit">Mettre à jour</button>
    </form>

    <div class="back">
        <a href="vos_cours.php">← Retour</a>
    </div>
</div>

<script>
    document.getElementById("fichier").addEventListener("change", function () {
        const label = document.getElementById("file-name");
        if (this.files.length > 0) {
            label.textContent = "Fichier sélectionné : " + this.files[0].name;
        } else {
            label.textContent = "";
        }
    });
</script>

</body>
</html>
