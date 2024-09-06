<?php
    session_start();
    require '../../db.php';

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
    $id_pers = $user['id_pers'];
    $id_role = $rolepersonne['id_role'];
    
    if ($_SERVER["REQUEST_METHOD"] == "POST"){

        $nom_categ = $_POST['nom_categ'];
        $descr_categ = $_POST['descr_categ'];
        
        // Requête pour insérer les informations
        $sql1 = "INSERT INTO categories (lib_categ, desc_categ, id_pers, id_role)
                 VALUES (?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("ssii", $nom_categ, $descr_categ, $id_pers, $id_role);

        if ($stmt1->execute()) {
            header("Location: success_add_categ.html");
            exit();
        } else {
            echo "Erreur. Vérifiez vos informations.";
        }

    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>GBS | Ajouter catégorie</title>
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
        z-index: 999;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .admin-profile {
        text-align: center;
        margin-bottom: 50px;
    }

    .admin-photo {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 1px;
    }

    .admin-info {
        font-size: 16px;
    }

    .admin-name {
        text-transform: uppercase;
    }

    .green-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        background-color: green;
        border-radius: 50%;
    }

    /* Responsive Styles */
    @media (max-width: 600px) {
        .search-input {
            width: 100%;
        }
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group label{
        font-weight : bold;
    }

    .form-input {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        font-size: 16px;
    }

    .form-textarea {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        font-size: 16px;
        height: 60px;
        resize: vertical;
    }

    .form-button {
        background-color: blue;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
    }

    .form-button:hover {
        background-color: #555;
    }
</style>

<body>
    <nav class="navbar">
        <button class="sidebar-toggle" id="sidebarToggle"> </button>
        <div class="logo">GOOD BUY STORE</div>
        <div class="search-container">
            <button class="search-icon" id="decoIcon"><span class="green-dot"></span>
            </button>
        </div>
    </nav>
    <div class="navbar-ribbon">Espace vendeur</div>

    <main class="content">
        <h1>Nouvelle catégorie</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nom_categ" class="form-label">Nom de la catégorie</label>
                <input type="text" id="nom_categ" name="nom_categ" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="descr_categ" class="form-label">Brève description de la catégorie</label>
                <textarea id="descr_categ" name="descr_categ" class="form-textarea" required></textarea>
            </div>
            <button type="submit" class="form-button">Ajouter</button>
        </form>
    </main>

</body>
</html>
