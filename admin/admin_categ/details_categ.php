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
        $id_pers = $user['id_pers'];
        $id_role = $rolepersonne['id_role'];

        //Repertoire images
        $rep = "../";

    if (isset($_GET['id'])) {
        $id_categ = $_GET['id'];

        $sql = "SELECT c.*, COUNT(p.id_prod) as nbr_prod
            FROM categories c
            LEFT JOIN produits p ON c.id_categ = p.id_categ
            WHERE id_pers = ? 
            AND id_role = ?
            AND c.id_categ = ?;";                                             
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii",$id_pers,$id_role,$id_categ);
        $stmt->execute();

        $result = $stmt->get_result();
        $lacategorie = $result->fetch_assoc();
        

        $sql2 = "SELECT * 
            FROM rolepersonnes AS rp, categories AS c, produits AS p
            WHERE rp.id_pers=c.id_pers
            AND rp.id_role=c.id_role
            AND c.id_categ=p.id_categ
            AND c.id_pers=?
            AND c.id_role=? 
            AND c.id_categ =?
            ORDER BY id_prod DESC;";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("iii", $id_pers, $id_role, $id_categ);
        $stmt2->execute();

        $result2 = $stmt2->get_result();
        $lesproduits = $result2->fetch_all(MYSQLI_ASSOC);
        

        $stmt->close();
        $stmt2->close();
        $conn->close();
    } else {
        echo "Aucun ID de catégorie spécifié.";
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>GBS | Détails catégories</title>
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

    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #000000;
        border-radius: 4px;
        background-color: #f0f8ff;
        color: #000000;
    }

    .preview-img {
        max-width: 100px;
        max-height: 100px;
        display: none;
        margin-top: 10px;
    }

    .prod-card {
        max-width: 100%;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .prod-card h2 {
        text-align: center;
        color: #333;
    }

    .products-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .product-item {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #ddd;
        padding-bottom: 20px;
    }

    .product-item:last-child {
        border-bottom: none;
    }

    .product-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 10px;
    }

    .product-info {
        flex: 1;
        margin-left: 20px;
    }

    .product-name {
        margin: 0;
        font-size: 1.2em;
        color: #333;
    }

    .product-price {
        margin: 5px 0;
        color: #888;
    }

    .product-description {
        color: #666;
    }

    /* Responsive Design */
    @media (max-width: 600px) {
        .product-item {
            flex-direction: row;
            align-items: center;
        }

        .product-info {
            margin-left: 10px;
        }

        .product-image {
            width: 80px;
            height: 80px;
        }
    }

    .card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 100%;
        padding-right: 50px;
        padding-bottom: 30px;
        padding-left: 50px;
        padding-top: 70px;
        box-sizing: border-box;
        margin-top: 20px;
    }

    .card h2 {
        margin: 0 0 10px;
    }

    .card p {
        margin: 0 0 10px;
    }

    .card .number {
        font-size: 1.5em;
        font-weight: bold;
        margin-bottom: 20px;
        color: grey;
    }

    .card-buttons {
        display: flex;
        justify-content: space-between;
    }

    .card-buttons a {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
        color: white;
        flex: 1;
        margin: 0 5px;
    }

    .card-buttons a.left {
        background-color: red;
    }

    .card-buttons a.right {
        background-color: #28a745;
    }

    @media (max-width: 600px) {
        .card-buttons a {
            padding: 8px 15px;
        }
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
    <div class="card">
        <h2><?php echo $lacategorie['lib_categ']?></h2>
        <p><?php echo $lacategorie['desc_categ']?></p>
        <div class="number"><?php echo $lacategorie['nbr_prod']?> produit(s)</div>
        <div class="card-buttons">
            <a href="#" class="left">Supprimer</a>
            <a href="admin_modif_categ.php?id=<?php echo $lacategorie['id_categ']; ?>" class="right">Modifier</a>
        </div>
    </div>
    <div class="prod-card">
        <div class="products-list">
            <?php foreach($lesproduits as $produit):?>
                <div class="product-item">
                    <img src="<?php echo $rep.htmlspecialchars($produit["img1_prod"])?>" alt="Produit" class="product-image">
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($produit['desi_prod'])?></h3>
                        <p class="product-price"><?php echo ($produit['prix_prod'])?> FCFA</p>
                        <p class="product-description"><?php echo htmlspecialchars($produit['descr_prod'])?></p>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
    </div>
</body>
</html>

