<?php
    session_start();
    require '../db.php';

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user']) && !isset($_SESSION['rolepersonne'])) {
        header("Location: index.php");
        exit();
    }

    // Récupérer les informations 
    $user = $_SESSION['user'];
    $rolepersonne = $_SESSION['rolepersonne'];

    // Récupérer les infos depuis la session
    $id_pers = $user['id_pers'];
    $id_role = $rolepersonne['id_role'];
    $id_rolepers = $rolepersonne['id_rolepers'];

    // Repertoire images
    $rep = "../admin/";

    if (isset($_GET['id']) && isset($_GET['from'])) {
        $id_prod = $_GET['id'];
        $from = $_GET['from'];
        
        $sql = "SELECT * FROM produits WHERE id_prod=? ;";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i",$id_prod);
        $stmt->execute();

        $result = $stmt->get_result();
        $produit = $result->fetch_assoc();

        $sql2 = "SELECT lib_categ FROM categories WHERE id_categ=? ;";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i",$produit['id_categ']);
        $stmt2->execute();

        $result2 = $stmt2->get_result();
        $libcateg = $result2->fetch_assoc();

        if ($from == 's') {
            // Vérification de l'existence de l'entrée
            $sql_check = "SELECT COUNT(*) FROM recherches WHERE entree_rech = ? AND type_rech = ? AND id_entree_rech = ? AND id_pers = ? AND id_role = ?";
            $stmt_check = $conn->prepare($sql_check);
        
            $entree_rech = $produit["desi_prod"];
            $type_rech = 'produit';
        
            $stmt_check->bind_param("ssiii", $entree_rech, $type_rech, $id_prod, $id_pers, $id_role);
            $stmt_check->execute();
        
            $count = 0;
            $stmt_check->bind_result($count);
            $stmt_check->fetch();
            $stmt_check->close();
        
            // Si l'entrée n'existe pas, on fait l'insertion
            if ($count == 0) {
                $sql1 = "INSERT INTO recherches (entree_rech, type_rech, id_entree_rech, id_pers, id_role)
                         VALUES (?, ?, ?, ?, ?)";
                $stmt1 = $conn->prepare($sql1);
                $stmt1->bind_param("ssiii", $entree_rech, $type_rech, $id_prod, $id_pers, $id_role);
                $stmt1->execute();
                $stmt1->close();
            }
        }
                
        $stmt->close();
        $conn->close();
    } else {
        echo "Aucun ID spécifié.";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBS | Détails produit</title>
</head>
<style>
    /* Style de base */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        padding-bottom: 60px; /* Pour ne pas cacher le contenu par la bottom-bar */
    }

    /* Section 1: Carrousel */
    .carousel {
        width: 100%;
        overflow: hidden;
        position: relative;
    }

    .carousel-images {
        display: flex;
        transition: transform 0.5s ease-in-out;
    }

    .carousel-images img {
        width: 100%;
        flex-shrink: 0;
        height: 400px; /* Toutes les images auront la même hauteur */
        object-fit: contain; /* Pour s'assurer que les images remplissent bien le conteneur */
    }

    .carousel-buttons {
        position: absolute;
        top: 50%;
        width: 100%;
        display: flex;
        justify-content: space-between;
        transform: translateY(-50%);
    }

    .carousel-button {
        background-color: rgba(0, 0, 0, 0.5);
        border: none;
        color: white;
        padding: 10px;
        cursor: pointer;
    }

    /* Section 2: Infos du produit */
    .product-info {
        padding: 20px;
        background: #f8f8f8;
    }

    .product-info h1 {
        margin: 0 0 10px;
    }

    .product-info .price {
        font-size: 1.5em;
        color: #d9534f;
    }

    .categ_prod{
        font-weight : bold;
        color : grey;
    }

    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        padding: 0px 0px 0px 0px;
    }

    .rating-container {
        display: flex;
        align-items: center;
    }

    .rating label {
        font-size: 2em;
        color: grey;
        cursor: pointer;
        transition: color 0.2s;
    }

    .average-rating {
        font-weight: bold;
        color: yellow;
        margin-left: 10px; /* Ajustez cette valeur selon vos besoins */
    }

    /* Section 3: Infos de livraison */
    .delivery-info {
        padding: 20px;
    }

    .delivery-info h2 {
        margin: 0 0 10px;
    }



    /* Bottom-bar fixée */
    .bottom-bar {
        position: fixed;
        bottom: 0;
        width: 100%;
        background: #333;
        color: #fff;
        text-align: center;
        padding: 10px 0;
    }

    .bottom-bar a {
        display: inline-block;
        color: white;
        padding: 10px 20px;
        margin: 0 10px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 1em;
    }

    .bottom-bar a:hover {
        background-color: #555;
    }

    .add-to-cart1 {
        background-color: red;
    }

    .add-to-cart2 {
        background-color: green;
    }

    .add-to-cartt {
        background-color: orange;
    }

    /* Section Autres informations */
    .others {
        background: #f8f8f8;
        padding: 20px;
        margin: 20px 0;
    }

    .others h2 {
        margin-bottom: 15px;
    }

    .quantity-container {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .quantity-container label {
        margin-right: 10px;
        font-weight: bold;
    }

    .inpt{
        text-align: center; /* Centre le contenu du champ input */
        width: 50px; /* Ajuste la largeur de l'input selon les besoins */
        height: 30px; /* Ajuste la hauteur de l'input */
        border: 1px solid #ccc; /* Bordure autour de l'input */
        font-size: 16px; /* Taille du texte à l'intérieur de l'input */
        margin: 0 5px; /* Espacement horizontal autour de l'input */
    }

    
    .details-container {
        display: flex;
        flex-direction: column;
    }

    .details-container label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .details-container textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }



    /* Responsive Design */
    @media (max-width: 768px) {
        .product-info, .delivery-info {
            padding: 10px;
        }
        
        .carousel-images img {
            width: 100%;
            /*height : 100%*/
        }
    }

</style>
<body>
    <div class="container">
        <!-- Section 1: Carrousel de 5 images -->
        <div class="carousel">
            <div class="carousel-images">
                <img src="<?php echo $rep.htmlspecialchars($produit["img1_prod"])?>" alt="Image 1">
                <img src="<?php echo $rep.htmlspecialchars($produit["img2_prod"])?>" alt="Image 2">
                <img src="<?php echo $rep.htmlspecialchars($produit["img3_prod"])?>" alt="Image 3">
                <img src="<?php echo $rep.htmlspecialchars($produit["img4_prod"])?>" alt="Image 4">
                <img src="<?php echo $rep.htmlspecialchars($produit["img5_prod"])?>" alt="Image 5">
            </div>
            <div class="carousel-buttons">
                <button class="carousel-button" id="prev">❮</button>
                <button class="carousel-button" id="next">❯</button>
            </div>
        </div>
        <!-- Section 2: Infos du produit -->
        <div class="product-info">
            <h1><?php echo ($produit['desi_prod'])?></h1>
            <p><?php echo ($produit['descr_prod'])?></p>
            <span class="categ_prod">Catégorie : <?php echo ($libcateg['lib_categ'])?></span>
            <p class="price"><?php echo ($produit['prix_prod'])?> FCFA</p>
            <div class="rating-container">
                <div class="rating">
                    <label for="star5" title="5 étoiles">☆</label>
                    <label for="star4" title="4 étoiles">☆</label>
                    <label for="star3" title="3 étoiles">☆</label>
                    <label for="star2" title="2 étoiles">☆</label>
                    <label for="star1" title="1 étoile">☆</label>
                </div>
                <span class="average-rating">4,5/5</span>
            </div>
        </div>

        <!-- Section 3: Infos de livraison -->
        <div class="delivery-info">
            <h2>Informations de livraison</h2>
            <p>Délai de livraison estimé: 3-5 jours ouvrables</p>
            <p>Frais de livraison: 4.99€</p>
        </div>

        <div class="others">
            <h2>Autres informations</h2>
            <div class="quantity-container">
                <label for="quantity-input">Quantité:</label>
                <button class="quantity-btn" id="decrease">-</button>
                <input class="inpt" type="number" id="quantity-input" value="1">
                <button class="quantity-btn" id="increase">+</button>
            </div>
            <div class="details-container">
                <label for="details">Préciser détails:</label>
                <textarea id="details" rows="2" placeholder="Ajouter des informations supplémentaires ici..."></textarea>
            </div>
        </div>

    </div>

    <div class="bottom-bar">
        <a href="../cmde/new_cmde.php?id=<?php echo $produit['id_prod'];?>&idrp=<?php echo $id_rolepers;?>" class="add-to-cartt">Commander</a>
    </div>
    <p>branch test</p>
    <script>
        let currentIndex = 0;
        const images = document.querySelectorAll('.carousel-images img');
        const totalImages = images.length;

        function showImage(index) {
            const offset = -index * 100;
            document.querySelector('.carousel-images').style.transform = `translateX(${offset}%)`;
        }

        document.getElementById('next').addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % totalImages;
            showImage(currentIndex);
        });

        document.getElementById('prev').addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + totalImages) % totalImages;
            showImage(currentIndex);
        });

        // Défilement automatique
        setInterval(() => {
            currentIndex = (currentIndex + 1) % totalImages;
            showImage(currentIndex);
        }, 15000); // Changer d'image toutes les 7 secondes

        document.getElementById('increase').addEventListener('click', function() {
            let quantityInput = document.getElementById('quantity-input');
            let currentValue = parseInt(quantityInput.value);
            quantityInput.value = currentValue + 1;
        });

        document.getElementById('decrease').addEventListener('click', function() {
            let quantityInput = document.getElementById('quantity-input');
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });

    </script>
</body>
</html>
