<?php
    session_start();
    require '../db.php';

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user']) && !isset($_SESSION['rolepersonne'])) {
        header("Location: index.php");
        exit();
    }

    //Récupérer les informations 
    $user = $_SESSION['user'];
    $rolepersonne = $_SESSION['rolepersonne'];

    // Récupérer les infos depuis la session
    $telephone = $user['telephone_pers'];
    $nom_admin = $user['nom_pers'];
    $prenoms_admin = $user['prenoms_pers'];
    $photo_adminn = $user['photo_pers'];

    if (is_null($photo_adminn) || empty($photo_adminn)) {
        // Afficher la photo par défaut si photo_pers est NULL ou vide
        $photo_admin = "../img/profile1.jpg";
    } else {
        // Afficher la photo de l'admin depuis la base de données
        $photo_admin = $photo_adminn;
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>GBS | Tableau de bord</title>
    <script defer src="scripts.js"></script>
</head>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;

    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #333;
        color: white;
        padding: 10px;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
    }

    .sidebar-toggle {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
    }

    .logo {
        font-size: 24px;
    }

    .search-container {
        display: flex;
        align-items: center;
    }

    .search-icon {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        margin-right: 10px;
    }

    .search-input {
        display: none;
        margin-left: 10px;
        padding: 5px;
        font-size: 16px;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: -250px;
        width: 250px;
        height: 100%;
        background-color: #444;
        color: white;
        transition: left 0.3s ease;
        padding-top: 60px;
        z-index: 999;

    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        padding: 15px;
        text-align: center;
    }

    .sidebar ul li a {
        color: white;
        text-decoration: none;
        display: block;
    }

    .sidebar-bottom div {
        list-style: none;
        padding: 0;
    }

    .sidebar-bottom div p {
        padding: 15px;
        text-align: center;
    }

    .sidebar-bottom div p a {
        color: white;
        text-decoration: none;
        display: block;
    }

    .content {
        margin-top: 60px;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar-ribbon {
        height: 25px;
        background-color: rgb(101, 103, 120);
        width: 100%;
        position: fixed;
        top: 50px;
        /* Ajustez si la hauteur de la barre de navigation change */
        z-index: 999;
        color: white;
        display: flex;
        justify-content: center;
        /* Centre le contenu horizontalement */
        align-items: center;
        /* Centre le contenu verticalement */
    }

    .admin-profile {
        text-align: center;
        /* Centrer le contenu horizontalement */
        margin-bottom: 50px;
        /* Espacement en bas */
    }

    .admin-profile div p a {
        color: white;
        text-decoration: none;
        display: block;
    }

    .admin-photo {
        width: 100px;
        /* Taille de l'image */
        height: 100px;
        /* Taille de l'image */
        border-radius: 50%;
        /* Rendre l'image ronde */
        object-fit: cover;
        /* Ajuster la taille de l'image */
        margin-bottom: 1px;
        /* Espacement en bas de l'image */
    }

    .admin-info {
        font-size: 16px;
        /* Taille de la police pour le nom/prénom */
    }

    .admin-name {
        text-transform: uppercase;
        /* Mettre le texte en majuscules */
    }

    .green-dot {
    display: inline-block;
    width: 10px; /* Taille du point */
    height: 10px; /* Taille du point */
    background-color: green; /* Couleur verte */
    border-radius: 50%; /* Rendre le point rond */
    }


    /* Responsive Styles */
    @media (max-width: 600px) {
        .search-input {
            width: 100%;
        }
    }
</style>

<body>
    <nav class="navbar">
        <button class="sidebar-toggle" id="sidebarToggle">&#9776;</button>
        <div class="logo">GOOD BUY STORE</div>
        <div class="search-container">
            <button class="search-icon" id="decoIcon"><span class="green-dot"></span>
            </button>
        </div>
    </nav>
    <div class="navbar-ribbon">Espace vendeur</div>
    <aside class="sidebar" id="sidebar">
        <div class="admin-profile">
            <img src="<?php echo htmlspecialchars($photo_admin); ?>" class="admin-photo">
            <div class="admin-info">
                <p><a href="admin_infos.php"><strong class="admin-name"><?php echo htmlspecialchars($nom_admin); ?></strong>
                <?php echo htmlspecialchars($prenoms_admin); ?></a></p>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_prod/defaut.php">Accueil</a></li>
            <li><a href="admin_prod/admin_produits.php">Produits</a></li>
            <li><a href="admin_categ/admin_categories.php">Catégories</a></li>
            <li><a href="admin_com/admin_commandes.php">Commandes</a></li>
            <li><a href="admin_liv/admin_livraisons.php">Livraisons</a></li>
            <li><a href="admin_bout/admin_boutique.php">Ma boutique</a></li>
        </ul>
        <div class="sidebar-bottom">
            <div class="admin-info">
            <p><a href="admin_decon.php">Déconnexion</a></p>
            </div>
        </div>
    </aside>
    <main class="content">
        <h1>
            Defaut
        </h1>
    </main>
</body>
<script>
    /*// Sélection de tous les liens de la barre latérale
    const sidebarLinks = document.querySelectorAll('.sidebar-menu a');

    // Fonction pour charger le contenu de la page cible dans le main
    function loadPageContent(url) {
        fetch(url)
            .then(response => response.text())
            .then(data => {
                // Mettre à jour le contenu de main avec la réponse de la requête
                document.querySelector('.content').innerHTML = data;
            })
            .catch(error => {
                console.error('Erreur lors du chargement de la page :', error);
            });
    }

    // Ajouter un écouteur d'événement à chaque lien de la barre latérale
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // Empêcher le comportement par défaut du lien

            const url = this.getAttribute('href'); // Obtenir l'URL du lien cliqué
            loadPageContent(url); // Charger le contenu de la page cible dans le main
        });
    });*/
    // Sélection de tous les liens de la barre latérale
    const sidebarLinks = document.querySelectorAll('.sidebar-menu a');

    // Fonction pour charger le contenu de la page cible dans le main
    function loadPageContent(url) {
        fetch(url)
            .then(response => response.text())
            .then(data => {
                // Mettre à jour le contenu de main avec la réponse de la requête
                document.querySelector('.content').innerHTML = data;
            })
            .catch(error => {
                console.error('Erreur lors du chargement de la page :', error);
            });
    }

    // Ajouter un écouteur d'événement à chaque lien de la barre latérale
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // Empêcher le comportement par défaut du lien

            const url = this.getAttribute('href'); // Obtenir l'URL du lien cliqué
            localStorage.setItem('lastPage', url); // Enregistrer l'URL dans le localStorage
            loadPageContent(url); // Charger le contenu de la page cible dans le main
        });
    });

    // Charger la dernière page visitée lors du chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        const lastPage = localStorage.getItem('lastPage');
        if (lastPage) {
            loadPageContent(lastPage);
        }
    });
</script>

</html>