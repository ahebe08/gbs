<?php
    /*require '../db.php';

    if (isset($_GET['keyword'])){
        $keyword = $_GET['keyword'];
        $results = [];

        if (!empty($keyword)) {
            // Préparation de la requête SQL en fonction du filtre
            $sql = "SELECT * FROM produits WHERE desi_prod LIKE ? OR descr_prod LIKE ?";
            $stmt = $conn->prepare($sql);
            $param = "{$keyword}%";
            $stmt->bind_param('ss', $param, $param);
            $stmt->execute();
            $result = $stmt->get_result();

            // Collecte des résultats
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }

            // Retourner les résultats en JSON
            header('Content-Type: application/json');
            echo json_encode($results);
        } else {
            // Retourner une erreur si le mot-clé est vide
            http_response_code(400);
            echo json_encode(['error' => 'Keyword cannot be empty']);
        }
    } else {
        // Retourner une erreur si le paramètre requis n'est pas fourni
        http_response_code(400);
        echo json_encode(['error' => 'Missing keyword']);
    }
    */
    require '../db.php';

    if (isset($_GET['query']) && isset($_GET['filter'])) {
        $search_query = trim($_GET['query']);
        $filter = $_GET['filter'];

        $results = [];

        if (!empty($search_query)) {
            // Préparation de la requête SQL en fonction du filtre
            if ($filter === 'product') {
                //$sql = "SELECT * FROM produits WHERE desi_prod LIKE ? OR descr_prod LIKE ?";
                $sql = "SELECT * FROM produits WHERE desi_prod LIKE ?";
            } else if ($filter === 'category') {
                $sql = "SELECT * FROM categories WHERE lib_categ LIKE ?";
            } else {
                // Retourner une erreur si le filtre est invalide
                http_response_code(400);
                echo json_encode(['error' => 'Invalid filter type']);
                exit();
            }

            // Exécution de la requête préparée
            if ($stmt = $conn->prepare($sql)) {
                $param = "%{$search_query}%";
                if ($filter === 'product') {
                    //$stmt->bind_param('ss', $param, $param);
                    $stmt->bind_param('s', $param);
                } else if ($filter === 'category') {
                    $stmt->bind_param('s', $param);
                }

                $stmt->execute();
                $result = $stmt->get_result();

                // Collecte des résultats
                while ($row = $result->fetch_assoc()) {
                    $results[] = $row;
                }
                $stmt->close();
            } else {
                // Gérer l'erreur de préparation de la requête
                http_response_code(500);
                echo json_encode(['error' => 'Database query failed']);
                exit();
            }
        }

        // Retourner les résultats en JSON
        header('Content-Type: application/json');
        echo json_encode($results);
        exit();
    } else {
        // Retourner une erreur si les paramètres requis ne sont pas fournis
        http_response_code(400);
        echo json_encode(['error' => 'Missing query or filter']);
        exit();
    } 
?>
