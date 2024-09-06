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

    if (isset($_GET['id'])) {
        $id_rech = $_GET['id'];
        
        $sql = "DELETE FROM recherches WHERE id_rech = ?;";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i",$id_rech);
        $stmt->execute();
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
    <title>Document</title>
</head>
<body>
    <script>
        // Attendre 2 secondes avant de rediriger
        setTimeout(function() {
            window.location.replace("accueil.php");
        }, 500);
    </script>
</body>
</html>