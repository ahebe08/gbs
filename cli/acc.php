<?php
    session_start();
    require '../db.php';

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user']) && !isset($_SESSION['rolepersonne'])) {
        header("Location: connexion.php");
        exit();
    }

    //Récupérer les informations 
    $user = $_SESSION['user'];
    $rolepersonne = $_SESSION['rolepersonne'];

    // Récupérer les infos depuis la session
    $id_pers = $user['id_pers'];
    $id_role = $rolepersonne['id_role'];

    $sql = "SELECT * 
            FROM produits
            ORDER BY id_prod DESC";
    $stmt = $conn->prepare($sql);
    //$stmt->bind_param("ii", $id_pers, $id_role);
    $stmt->execute();

    $result = $stmt->get_result();
    $lesproduits = $result->fetch_all(MYSQLI_ASSOC);
    
    
    $stmt->close();
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBS | Accueil</title>
</head>
<style>
        .product {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .product img {
            max-width: 85%;
            height: 200px; /* Hauteur fixe pour toutes les images */
            object-fit: contain; /* Ajuste l'image pour remplir le conteneur */
            border-radius: 4px;
        }
        .product h3 {
            font-weight: bold;
            margin-top: 10px;
        }
        .product p {
            font-size: 18px;
            margin-top: 1px;
        }
        .product .btn {
            background-color: #333;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }
</style>
<body>
    <div class="container text-center">
        <div class="row row-cols-2">
        <?php foreach($lesproduits as $produit):?>
            <div class="col product">
                <img src="<?php echo "../admin/".htmlspecialchars($produit["img1_prod"])?>" alt="Nom de l'article">
                <h3><?php echo htmlspecialchars($produit["desi_prod"])?></h3>
                <p><?php echo htmlspecialchars($produit["prix_prod"])?> FCFA</p>
                <a href="details_prod.php?id=<?php echo $produit['id_prod'];?>&from=v" class="btn btn-primary">Voir produit</a>
            </div>
         <?php endforeach;?>
        </div>
    </div>
</body>
</html>