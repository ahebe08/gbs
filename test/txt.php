<?php
require '../db.php';

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
?>
