<?php
    session_start();    
    require '../db.php';

    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['user']) && isset($_SESSION['rolepersonne'])) {
        require 'admin_decon.php';
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $telephone = $_POST['telephone'];
        $mdp = $_POST['mdp'];

        // Requête pour vérifier si le numéro de téléphone existe dans la table `personnes`
        $sql1 = "SELECT * FROM personnes WHERE telephone_pers = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("s",$telephone);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        if ($result1->num_rows > 0) {
            // Le numéro de téléphone existe, récupérer les informations de l'utilisateur
            $user = $result1->fetch_assoc();
            $personne_id = $user['id_pers'];
            $hashed_mdp = $user['mdp_pers']; // Assurez-vous que le champ du mot de passe haché dans la table est correct

            // Vérifier le mot de passe
            if (password_verify($mdp, $hashed_mdp)) {
                // Deuxième requête pour vérifier les rôles de la personne dans la table `rolepersonnes`
                $id_role = 2;

                $sql2 = "SELECT * FROM rolepersonnes WHERE id_pers = ? AND id_role = ?";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("ii", $personne_id, $id_role);
                $stmt2->execute();
                $rolepersonne = $stmt2->get_result();

                if ($rolepersonne->num_rows > 0) {
                    // Le rôle existe pour cette personne, démarrer une session
                    $_SESSION['user'] = $user;
                    $_SESSION['rolepersonne'] = $rolepersonne->fetch_assoc(); // Correction pour stocker le rôle
                    // Rediriger vers une autre page si nécessaire
                    header("Location: dashboard.php");
                } else {
                    echo "Rôle non trouvé pour cet utilisateur.";
                }

                $stmt2->close();
            } else {
                echo "Le mot de passe ne correspond pas à cet utilisateur.";
            }
        } else {
            echo "Numéro de téléphone incorrect.";
        }

        $stmt1->close();
        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBS | Connexion vendeur</title>
</head>
<style>
        * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f5f5f5;
    }

    .login-container {
        background-color: #ffffff;
        border: 1px solid #000000;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 300px;
        text-align: center;
    }

    .login-container h1 {
        margin-bottom: 20px;
        color: #000000;
    }

    .form-group {
        margin-bottom: 15px;
        text-align: left;
        position: relative; /* Required for positioning the eye icon */
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #000000;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #000000;
        border-radius: 4px;
        background-color: #f0f8ff; /* Light blue background for input fields */
        color: #000000;
    }

    button {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 4px;
        background-color: #353c43; /* Orange background for button */
        color: #ffffff;
        font-size: 16px;
        cursor: pointer;
    }

    button:hover {
        background-color: #007BFF; /* Darker orange on hover */
    }
    span, img{
        width: 20px;
        height: 20px;
        padding-top: 6px;
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
    }
</style>
<body>
    <div class="login-container">
        <h1>Connexion vendeur</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="telephone">Numéro de téléphone</label>
                <input type="text" id="telephone" name="telephone" required>
            </div>
            <div class="form-group">
                <label for="mdp">Mot de passe</label>
                <input type="password" id="mdp" name="mdp" required>
                <span class="toggle-password" onclick="togglePassword('mdp')"><img src="../img/eye.png"></span>
            </div>
            <button type="submit">Se connecter</button>
        </form>
        <div>
            .
        </div>
        <a href="#">Mot de passe oublié ?</a>
        <div>
            ...
        </div>
        <a href="admin_inscription.php">Je n'ai pas de compte vendeur</a>
    </div>
    <script>
        function togglePassword(fieldId) {
            var field = document.getElementById(fieldId);
            var type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
        }
    </script>
</body>
</html>
