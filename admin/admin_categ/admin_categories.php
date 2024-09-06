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

    //$sql2 = "SELECT * FROM categories WHERE id_pers = ? AND id_role = ? ORDER BY id_categ DESC";
    $sql2 = "SELECT c.*, COUNT(p.id_prod) as nbr_prod
            FROM categories c
            LEFT JOIN produits p ON c.id_categ = p.id_categ
            WHERE id_pers = ? 
            AND id_role = ?
            GROUP BY c.id_categ
            ORDER BY c.id_categ DESC;";                                             
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ii", $id_pers, $id_role);
    $stmt2->execute();

    $result = $stmt2->get_result();
    $lescategorie = $result->fetch_all(MYSQLI_ASSOC);
    
    
    $stmt2->close();
    $conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBS | Categories</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    #search-form {
        display: flex;
        justify-content: center; /* Centre les éléments horizontalement */
        align-items: center; /* Centre les éléments verticalement */
        gap: 1rem;
        max-width: 1200px; /* Largeur maximale du formulaire */
        margin: 0 auto; /* Centre le formulaire horizontalement dans le header */
        width: 100%; /* Formulaire occupe toute la largeur du header */
    }

    #search-form input[type="text"] {
        padding: 0.5rem;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        flex: 1; /* Permet de s'ajuster à l'espace disponible */
        max-width: 200px; /* Largeur maximale pour les champs de saisie */
    }

    #search-form button {
        background-color: #555;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
    }

    #search-form button:hover {
        background-color: #777;
    }

    /* Responsivité pour le formulaire de recherche */
    @media (max-width: 768px) {
        #search-form {
            flex-direction: row; /* Conserve l'alignement horizontal sur les écrans moyens et petits */
            flex-wrap: nowrap; /* Empêche le retour à la ligne */
            gap: 0.5rem;
        }

        #search-form input[type="text"], #search-form button {
            width: auto; /* S'adapte à la taille du contenu */
            flex: 1; /* Permet de s'ajuster à l'espace disponible */
        }
    }

    @media (max-width: 480px) {
        #search-form {
            flex-direction: row; /* Conserve l'alignement horizontal sur les petits écrans */
            flex-wrap: nowrap; /* Empêche le retour à la ligne */
            gap: 0.5rem;
        }

        #search-form input[type="text"], #search-form button {
            width: auto; /* S'adapte à la taille du contenu */
            flex: 1; /* Permet de s'ajuster à l'espace disponible */
        }
    }

    /* CSS existant pour le reste de la page */
    main {
        padding: 1rem;
    }

    .category {
        margin-bottom: 2rem;
    }

    .category h2 {
        border-bottom: 2px solid #333;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }

    .categ {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .categ-card {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1rem;
        width: calc(33.333% - 1rem);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .categ-card img {
        max-width: 100%;
        height: 200px; /* Hauteur fixe pour toutes les images */
        object-fit: cover; /* Ajuste l'image pour remplir le conteneur */
        border-radius: 4px;
    }

    .categ-card h3 {
        margin: 0.5rem 0;
    }

    .categ-card p {
        margin: 0.5rem 0;
    }

    .categ-card button {
        background-color: #333;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
    }

    .categ-card button:hover {
        background-color: #555;
    }

    /* Responsivité pour les cartes de produit */
    @media (max-width: 768px) {
        .categ-card {
            width: calc(50% - 1rem);
        }
    }

    @media (max-width: 480px) {
        .categ-card {
            width: 100%;
        }
    }

    .add_btn{
        background-color: blue;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
    }

</style>
<body>
    <header>
        <form id="search-form">
            <input type="text" id="search-input" placeholder="Rechercher catégories...">
            <button type="submit">Rechercher</button>
        </form>
        <h1>Catégories</h1>
        <a href="admin_categ/admin_add_categ.php"><button class="add_btn">Ajouter</button></a>
    </header>

    <main>
        <!-- Container pour une catégorie -->
        <?php foreach($lescategorie as $categorie):?>
            <section class="category">
                <h2><?php echo htmlspecialchars($categorie['lib_categ'])?></h2>
                <div class="categ">
                    <!-- Carte de produit -->
                    <div class="categ-card">
                        <h3><?php echo htmlspecialchars($categorie['desc_categ'])?></h3>
                        <p>Produits : <?php echo htmlspecialchars($categorie['nbr_prod'])?></p>
                        <a href="admin_categ/details_categ.php?id=<?php echo $categorie['id_categ']; ?>"><button>Afficher détails</button></a>
                    </div>
                </div>
            </section>
        <?php endforeach;?>
        <!-- Autres containers de catégories ici -->
    </main>
</body>

</html>
