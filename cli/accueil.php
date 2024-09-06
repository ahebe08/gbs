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
    $nom_client = $user['nom_pers'];
    $prenoms_client = $user['prenoms_pers'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../bootstrap.min.css"> 
    <title>GBS | Accueil</title>
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
    }

    .green-dot {
        display: inline-block;
        width: 10px; /* Taille du point */
        height: 10px; /* Taille du point */
        background-color: green; /* Couleur verte */
        border-radius: 50%; /* Rendre le point rond */
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
        background-color: orange;
        width: 100%;
        position: fixed;
        top: 55px;
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

    .admin-info {
        font-size: 16px;
        /* Taille de la police pour le nom/prénom */
    }

    .admin-name {
        text-transform: uppercase;
        /* Mettre le texte en majuscules */
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
    <div class="navbar-ribbon"></div>
    <aside class="sidebar" id="sidebar">
        <div class="admin-profile">
            Bienvenue
            <div class="admin-info">
                <p><strong class="admin-name"><?php echo htmlspecialchars($nom_client); ?></strong>
                <?php echo htmlspecialchars($prenoms_client); ?></p>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="acc.php">Accueil</a></li>
            <li><a href="rsprods.php">Rechercher</a></li>
            <li><a href="">C</a></li>
            <li><a href="">Commandes</a></li>
            <li><a href="">Livraisons</a></li>
            <li><a href="">Annonces</a></li>
        </ul>
        <div class="sidebar-bottom">
            <div class="admin-info">
                <p><a href="decon.php">Déconnexion</a></p>
            </div>
        </div>
    </aside>
    <main class="content">
        <h1>Bienvenue dans notre boutique en ligne</h1>
        <p>Découvrez nos produits incroyables!</p>
    </main>

    <script src="jscli.js"></script>
</body>
</html>
