<?php
$servername = 'localhost';
$username = 'root';
$password = '12344321';
$dbname = 'pfe';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    $stmt = $conn->prepare("DELETE FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Suppression Réussie</title>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        </head>
        <body>
            <script>
                swal({
                    icon: "success",
                    title: "Bon travail !",
                    text: "Utilisateur supprimé avec succès.",
                    showConfirmButton: true,
                    confirmButtonText: "Fermer"
                }).then(function() {
                    window.location = "modi_user.php";
                });
            </script>
        </body>
        </html>
<?php
    } else {
?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Erreur de Suppression</title>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        </head>
        <body>
            <script>
                swal({
                    icon: "error",
                    title: "Erreur !",
                    text: "Erreur lors de la suppression de l'utilisateur : <?php echo $conn->error; ?>",
                    showConfirmButton: true,
                    confirmButtonText: "Fermer"
                }).then(function() {
                    window.location = "delet_user.html";
                });
            </script>
        </body>
        </html>
<?php
    }

    $stmt->close();
    $conn->close();
}
?>
