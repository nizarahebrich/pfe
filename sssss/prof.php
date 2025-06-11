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
    background-color: #0a192f;
    margin: 0;
    padding: 0;
    display: flex;
    color: #ffffff;
}

.menu {
    background-color: #102542;
    padding: 1em;
    width: 250px;
    min-height: 100vh;
    box-sizing: border-box;
    box-shadow: 2px 0 8px rgba(0, 188, 212, 0.1);
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
    color: #dcdcdc;
    text-decoration: none;
    font-weight: 600;
    display: block;
    padding: 8px 15px;
    border-radius: 5px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.menu ul li a:hover,
.menu ul li.active > a {
    background-color: #00bcd4;
    color: #0a192f;
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
    border-radius: 50%;
}

.menu .header h1 {
    display: inline-block;
    margin-left: 0.5em;
    color: #00bcd4;
    font-weight: 700;
    font-size: 1.5em;
    vertical-align: middle;
}

.right-img {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2em;
    background-color: #1c2a3a;
}

.right-img img {
    max-width: 100%;
    max-height: 90vh;
    object-fit: contain;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 188, 212, 0.15);
}

.header img {
    max-width: 100%;
    max-height: 90vh;
    object-fit: contain;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 188, 212, 0.15);
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
        <li class="">
            <div class="header">
                <img src="prof.jpg" alt="Logo">
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
  <div class="right-img">
    <img src="prof.jpg" alt="Professeur Illustration">
  </div>

