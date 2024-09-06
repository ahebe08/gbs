<?php
    session_start();
    require '../db.php';

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user']) && !isset($_SESSION['rolepersonne'])) {
        header("Location: index.php");
        exit();
    }

    // Récupérer les infos utilisateur
    $user = $_SESSION['user'];
    $rolepersonne = $_SESSION['rolepersonne'];

    $telephone = $user['telephone_pers'];
    $nom_client = $user['nom_pers'];
    $prenoms_client = $user['prenoms_pers'];

    // Récupérer la requête de recherche et le type de filtre (produit ou catégorie)
    $search_query = isset($_POST['query']) ? trim($_POST['query']) : '';
    $filter = isset($_POST['filter']) ? $_POST['filter'] : 'product';

    if (!empty($search_query)) {
        // Préparer la requête SQL en fonction du filtre sélectionné
        if ($filter == 'product') {
            $sql = "SELECT * FROM produits WHERE desi_prod LIKE ? OR descr_prod LIKE ?";
        } else if ($filter == 'category') {
            $sql = "SELECT * FROM categories WHERE lib_categ LIKE ?";
        }

        // Préparer et exécuter la requête
        if ($stmt = $conn->prepare($sql)) {
            $param = "{$search_query}%";
            if ($filter == 'product') {
                $stmt->bind_param('ss', $param, $param);
            } else if ($filter == 'category') {
                $stmt->bind_param('s', $param);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            // Renvoyer les résultats sous forme de <li>
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($filter == 'product') {
                        echo "<li>" . htmlspecialchars($row['desi_prod']) . "</li>";
                    } else if ($filter == 'category') {
                        echo "<li>" . htmlspecialchars($row['lib_categ']) . "</li>";
                    }
                }
            } else {
                echo "<li>Aucun résultat trouvé</li>";
            }

            $stmt->close();
        } else {
            echo "<li>Erreur de préparation de la requête : " . $conn->error . "</li>";
        }
    }

    $conn->close();
?>