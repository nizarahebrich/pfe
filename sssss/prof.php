<?php
session_start();

function isActive($pageName) {
    $currentFile = basename($_SERVER['PHP_SELF']);
    return $currentFile === $pageName ? 'active' : '';
}
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
        display: flex;
    }
    .menu {
        background-color: rgb(220, 228, 238);
        padding: 1em;
        width: 250px;
        min-height: 100vh;
        box-sizing: border-box;
    }
    .menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .menu ul li {
        margin: 0.5em 0;
        position: relative;
    }
    .menu ul li a {
        color: #333;
        text-decoration: none;
        font-weight: 600;
        display: block;
        padding: 8px 15px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    .menu ul li a:hover,
    .menu ul li.active > a {
        background-color: #0d6efd;
        color: white;
    }
    .menu ul li .submenu {
        list-style: none;
        padding-left: 15px;
        margin-top: 5px;
        display: none;
    }
    .menu ul li.active > .submenu {
        display: block;
    }
    .menu .header img {
        max-width: 50px;
        vertical-align: middle;
    }
    .menu .header h1 {
        display: inline-block;
        margin-left: 0.5em;
        color: #0d6efd;
        font-weight: 700;
        font-size: 1.5em;
        vertical-align: middle;
    }
</style>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const menuItems = document.querySelectorAll('.menu > ul > li > a');

        menuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                const parentLi = e.target.parentElement;
                const submenu = parentLi.querySelector('.submenu');

                if (submenu) {
                    e.preventDefault();
                    if (parentLi.classList.contains('active')) {
                        parentLi.classList.remove('active');
                    } else {
                        document.querySelectorAll('.menu > ul > li.active').forEach(li => li.classList.remove('active'));
                        parentLi.classList.add('active');
                    }
                } else {
                    document.querySelectorAll('.menu > ul > li.active').forEach(li => li.classList.remove('active'));
                    parentLi.classList.add('active');
                }
            });
        });
    });
</script>

<div class="menu">
    <ul>
        <li class="<?php echo isActive('prof.php'); ?>">
            <div class="header">
                <img src="img/logo.png" alt="Logo">
                <h1>Professeur</h1>
            </div>
            <a href="prof.php">Accueil</a>
            <ul class="submenu">
                <li><a href="ajouter_contenu.php">Ajouter Contenu</a></li>
                <li><a href="vos_cours.php">Vos Cours</a></li>
                <li><a href="tous_cours.php">Tous les Cours Dispo</a></li>
            </ul>
        </li>
        <li>
            <a href="logout.php" style="color:#dc3545;">
                Déconnexion <i class="fas fa-sign-out-alt"></i>
            </a>
        </li>
    </ul>
</div>
