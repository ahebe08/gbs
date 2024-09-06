<?php
    session_start();
    require '../db.php';

    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['user']) && isset($_SESSION['rolepersonne'])) {
        require 'admin_decon.php';
        exit();
    }

    $locations = array ("ABOBO","ADJAME","ANYAMA","ATTECOUBE","BINGERVILLE","COCODY","KOUMASSI","MARCORY",
                    "PLATEAU","PORT-BOUET","SONGON","TREICHVILLE","YOPOUGON","YAMOUSSOUKRO","AUTRE");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = $_POST['nom'];
        $prenoms = $_POST['prenoms'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        $commune = $_POST['commune'];
        $mdp = $_POST['mdp'];
        $cmdp = $_POST['cmdp'];

        $directory = 'uploads/upl'.$telephone.'/';
        $dir = 'upl'.$telephone;

        // Vérifier que les mots de passe correspondent
        if ($mdp !== $cmdp) {
            echo "Les mots de passe ne correspondent pas.";
            exit();
        }

        // Hacher le mot de passe
        $options = [
            'memory_cost' => 1<<17, // 128 MB
            'time_cost'   => 4,
            'threads'     => 2,
        ];
        $mdp_hash = password_hash($mdp, PASSWORD_ARGON2I, $options);        

        // Requête pour insérer les informations dans la table `personnes`
        $sql1 = "INSERT INTO personnes (nom_pers, prenoms_pers, telephone_pers,
                email_pers, commune_pers, mdp_pers, rep_pers) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("sssssss", $nom, $prenoms, $telephone, $email, $commune, $mdp_hash, $dir);
        
        if ($stmt1->execute()) {
            // Récupérer les infos de la personne insérée
            $sql1 = "SELECT * FROM personnes WHERE telephone_pers = ?";
            $stmt = $conn->prepare($sql1);
            $stmt->bind_param("s",$telephone);
            $stmt->execute();
            $result1 = $stmt->get_result();
            $user = $result1->fetch_assoc();
            
            $personne_id = $stmt1->insert_id;

            // ID du rôle à insérer dans la table `rolepersonnes` (à adapter selon votre logique)
            $role_id = 2;

            // Requête pour insérer dans la table `rolepersonnes`
            $sql2 = "INSERT INTO rolepersonnes (id_pers, id_role) VALUES (?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ii", $personne_id, $role_id);
            
            if ($stmt2->execute()) {
                //Récupérer les informations du rôle
                $sql2 = "SELECT * FROM rolepersonnes WHERE id_pers = ? AND id_role = ?";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("ii", $personne_id, $role_id);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                $rolepersonne = $result2->fetch_assoc();
                // Démarrer une session et rediriger vers une autre page si nécessaire
                $_SESSION['user'] = $user;
                $_SESSION['rolepersonne'] = $rolepersonne; // Vous pouvez stocker l'ID du rôle ici

                // Vérifier si le répertoire existe déjà
                if (!is_dir($directory)) {
                    // Créer le répertoire avec les permissions 0777
                    if (mkdir($directory, 0777, true)) {
                        //echo 'Le répertoire a été créé avec succès.';
                    } else {
                        echo 'Échec de la création du répertoire.';
                    }
                }else {
                    //echo 'Le répertoire existe déjà.';
                }

                header("Location: success.html");
            } else {
                //echo "Erreur lors de l'insertion dans rolepersonnes : " . $stmt2->error;
                echo "Erreur. Vérifiez vos informations. Un autre utilisateur utiliserait déjà ce numéro de téléphone ou ce mail.";
            }

            $stmt2->close();
        } else {
            //echo "Erreur lors de l'insertion dans rolepersonnes : " . $stmt2->error;
            echo "Erreur. Vérifiez vos informations. Un autre utilisateur utiliserait déjà ce numéro de téléphone ou ce mail.";
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
    <title>GBS | Enregistrement</title>
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

        .login-container,
        .signup-container {
            background-color: #ffffff;
            border: 1px solid #000000;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .login-container h1,
        .signup-container h1 {
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

        button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #007BFF; /* Blue background for button */
            color: #ffffff;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #ff8c00; /* Darker orange on hover */
        }

        .error {
            border-color: red;
        }

        .success {
            border-color: green;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h1>Enregistrement vendeur</h1>
        <form id="signup-form" action="#" method="post">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenoms">Prénoms</label>
                <input type="text" id="prenoms" name="prenoms" required>
            </div>
            <div class="form-group">
                <label for="telephone">Numéro de téléphone</label>
                <input type="text" id="telephone" name="telephone" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="commune">Commune</label>
                <select id="commune" name="commune" required>
                <?php 
                    $i = 0;
                    while($i < count($locations)): 
                    ?>
                        <option value="<?php echo $locations[$i]?>">
                            <?php echo htmlspecialchars($locations[$i])?>
                        </option>
                    <?php 
                    $i++;
                    endwhile; 
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="mdp">Mot de passe</label>
                <input type="password" id="mdp" name="mdp" required>
                <span class="toggle-password" onclick="togglePassword('mdp')"><img src="../img/eye.png"></span>
            </div>
            <div class="form-group">
                <label for="cmdp">Confirmer le mot de passe</label>
                <input type="password" id="cmdp" name="cmdp" required>
                <span class="toggle-password" onclick="togglePassword('cmdp')"><img src="../img/eye.png"></span>
            </div>
            <button type="submit">S'inscrire</button>
        </form>
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

