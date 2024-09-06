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

        
    $sql = "SELECT * FROM recherches WHERE id_role=? AND id_pers=? ORDER BY id_rech DESC;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii",$id_role,$id_pers);
    $stmt->execute();

    $result = $stmt->get_result();
    $lesrecents = $result->fetch_all(MYSQLI_ASSOC);
        
    $stmt->close();
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBS | Produits</title>   
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .search-cont, .recent-searches-container {
            width: 100%;
            padding: 20px;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        .recent-searches-container, #recent-searches {
            width: 100%;
        }


        #search-inpt {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .switches {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .switches label {
            cursor: pointer;
            flex: 1;
            text-align: center;
        }

        .switches input {
            margin-right: 5px;
        }

        #suggestions {
            list-style-type: none;
            padding: 0;
            margin: 0;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            box-sizing: border-box;
            position: absolute; /* Assurez-vous que la position est correcte */
            z-index: 1000; /* Assurez-vous que l'élément est au-dessus des autres éléments */
        }

        #suggestions li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        #suggestions li:last-child {
            border-bottom: none;
        }

        .hidden {
            display: none;
        }

        #recent-searches li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            position: relative; /* Nécessaire pour positionner le bouton de suppression */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #recent-searches li a {
            text-decoration: none;
            color: rgb(70, 130, 180); /* Bleu acier */
            flex-grow: 1; /* Le lien prend tout l'espace disponible */
        }

        #recent-searches li a:hover {
            text-decoration: underline;
        }

        #recent-searches span {
            color : grey;
            font-size: 14px; /* Taille de texte plus petite */
        }

        .delete-btn {
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            font-size: 16px;
            padding: 0;
            margin-left: 10px;
            flex-shrink: 0; /* Empêche le bouton de rétrécir */
        }

        .delete-btn:hover {
            color: #ff0000;
        }
        
        .lien-end{
            display: flex;
            justify-content: end;
        }

        /* Media Queries for Responsive Design */
        @media (max-width: 600px) {
            .search-cont, .recent-searches-container {
                padding: 10px;
            }

            #search-inpt {
                padding: 8px;
            }

            .switches label {
                font-size: 14px;
            }

            #suggestions li, #recent-searches li {
                padding: 8px;
            }
        }

</style>

<body>
    <div class="search-cont">
        <input type="text" onkeyup="searchkeyword()" id="search-inpt" name="query" placeholder="Rechercher...">
        <div class="switches">
            <label>
                <input type="radio" name="filter" value="product" checked>
                Produit
            </label>
            <label>
                <input type="radio" name="filter" value="category">
                Catégorie
            </label>
    </div>
    <ul id="suggestions" class="hidden"></ul>
    </div>
    <div class="recent-searches-container">
        <h3>Récents</h3>
        <ul id="recent-searches">
            <?php foreach($lesrecents as $recent): ?>
                <li>
                    <a href="
                        <?php 
                            if ($recent['type_rech'] == 'produit') {
                                echo "details_prod.php?id=".$recent['id_entree_rech']."&from=s";
                            }else if ($recent['type_rech'] == 'catégorie') {
                                echo "details_categ.php?id=".$recent['id_entree_rech'];
                            }
                        ?>
                    ">
                        <?php echo htmlspecialchars($recent['entree_rech']);?>
                        <span>- dans <?php echo htmlspecialchars($recent['type_rech']);?></span>
                    </a>
                    <a class="lien-end" href="del_rech.php?id=<?php echo htmlspecialchars($recent['id_rech']);?>">&#10006;</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
