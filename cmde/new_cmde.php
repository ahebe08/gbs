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


    if (isset($_GET['id']) && isset($_GET['idrp'])) {
        $id_prod = $_GET['id'];
        $id_rolepers = $_GET['idrp'];
        
        $sql = "SELECT COUNT(*) FROM commandes";
        $stmt = $conn->prepare($sql);
        $stmt->execute();  // Exécution de la requête
        
        $count = 0;
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

                //Commandes

        //statut commande
        $statut = "En cours";
        //Fabrication de num_commande
        $ncommande = "GBS-" . $id_rolepers . ($count + 1);
        
        $sql2 = "INSERT INTO commandes (num_commande, statut_commande, id_rolepers) VALUES (?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ssi", $ncommande, $statut ,$id_rolepers);
        $idcomm = $stmt2->insert_id;

                //ProdCommandes

        $sql3 = "INSERT INTO prodcommande (id_commande, id_prod, Qte_prod) VALUES (?, ?, ?)";
        $stmt3 = $conn->prepare($sql2);
        $stmt3->bind_param("iii", $idcomm, $id_prod ,$id_rolepers);

        if ($stmt2->execute()) {
            
        } else {
            echo "Ahh";
        }
        

    } else {
        echo "Aucun ID spécifié.";
    }
?>
