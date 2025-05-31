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
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row["password"];
        $compte = $row["compte"];

        if (password_verify($password, $hashed_password)) {
            if ($role == "administrateur") {
                header("Location: page_reussie.php");
                exit();
            } else if ($role == "utilisateur" && $compte == "debloque") {
                header("Location: page_reussie2.php");
                exit();
            } else if ($role == "utilisateur" && $compte == "bloque") {
                ?>
                <html>
                <body>
                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                        swal({
                            icon: "warning",
                            title: "Erreur !",
                            text: "Votre compte est bloqué. Veuillez contacter l'administrateur.",
                            showConfirmButton: true,
                            confirmButtonText: "Fermer",
                            closeOnConfirm: false
                        }).then(function(result) {
                            window.location = "index.html";
                        });
                    </script>
                </body>
                </html>
                <?php
                exit();
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
                        text: "Email ou mot de passe invalide. Veuillez réessayer.",
                        showConfirmButton: true,
                        confirmButtonText: "Fermer",
                        closeOnConfirm: false
                    }).then(function(result) {
                        window.location = "index.html";
                    });
                </script>
            </body>
            </html>
            <?php
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
                    text: "Email ou mot de passe invalide. Veuillez réessayer.",
                    showConfirmButton: true,
                    confirmButtonText: "Fermer",
                    closeOnConfirm: false
                }).then(function(result) {
                    window.location = "index.html";
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