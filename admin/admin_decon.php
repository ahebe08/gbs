<?php
    //session_start();
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    // Vérifier si l'utilisateur a cliqué sur le bouton de déconnexion
    if (isset($_POST['logout'])) {
        // Destruction de la session
        session_destroy();
        // Redirection vers index.php après déconnexion
        header("Location: index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>GBS | Déconnexion</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        background-color: #f0f0f0;
    }

    .card {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        max-width: 300px;
        text-align: center;
        position: relative; /* Add this line */
    }

    .card-body {
        font-size: 18px;
        color: #333;
    }

    .card-body button {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        cursor: pointer;
    }

</style>
<body>
    <div class="card">
        <div class="card-body">
            <h2>Êtes-vous sûr de vouloir vous déconnecter en tant qu'administrateur ?</h2>
            <form method="post">
                <button type="submit" name="logout">Déconnexion</button>
                <a href="dashboard.php">Annuler</a>
            </form>
        </div>
    </div>
</body>
</html>