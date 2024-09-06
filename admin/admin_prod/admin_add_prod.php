<?php
    session_start();
    require '../../db.php';

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user']) || !isset($_SESSION['rolepersonne'])) {
        header("Location: index.php");
        exit();
    }

    // Récupérer les informations de l'utilisateur
    $user = $_SESSION['user'];
    $rolepersonne = $_SESSION['rolepersonne'];
    $id_pers = $user['id_pers'];
    $id_role = $rolepersonne['id_role'];
    $dir = $user['rep_pers'];

    // Récupérer les catégories depuis la base de données
    $sql = "SELECT * FROM categories WHERE id_pers = ? AND id_role = ? ORDER BY id_categ DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_pers, $id_role);
    $stmt->execute();
    $result = $stmt->get_result();
    $lescategories = $result->fetch_all(MYSQLI_ASSOC);

    // Configuration
    $uploadDir = '../uploads/'.$dir.'/';
    $uplDir = 'uploads/'.$dir.'/';
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize = 5 * 1024 * 1024; // Taille maximale du fichier en octets (5MB)

    function resizeImage($file, $width, $height, $fileExt) {
        list($originalWidth, $originalHeight) = getimagesize($file);
        $src = imagecreatefromstring(file_get_contents($file));
    
        $tmp = imagecreatetruecolor($width, $height);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
    
        $newFileName = uniqid() . '.' . $fileExt;
        $uploadFile = 'uploads/' . $newFileName;
    
        switch ($fileExt) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($tmp, $uploadFile, 85); // 85 est la qualité de l'image
                break;
            case 'png':
                imagepng($tmp, $uploadFile, 8); // 0-9 est le niveau de compression
                break;
            case 'gif':
                imagegif($tmp, $uploadFile);
                break;
        }
    
        imagedestroy($tmp);
        imagedestroy($src);
    
        return $uploadFile;
    }

    // Traitement du formulaire à la soumission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $idcateg = $_POST['idcateg'];
        $nom_prod = htmlspecialchars($_POST['nom_prod']);
        $descr_prod = htmlspecialchars($_POST['descr_prod']);
        $prix_prod = $_POST['prix_prod'];
        $imagePaths = [];
    
        // Vérifiez si les fichiers ont été téléchargés
        if (isset($_FILES['productImages'])) {
            foreach ($_FILES['productImages']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['productImages']['error'][$key] == 0) {
                    $fileInfo = pathinfo($_FILES['productImages']['name'][$key]);
                    $fileExt = strtolower($fileInfo['extension']);
    
                    // Vérifiez l'extension du fichier
                    if (in_array($fileExt, $allowed)) {
                        // Redimensionner l'image si elle dépasse 5MB
                        if ($_FILES['productImages']['size'][$key] > $maxFileSize) {
                            $resizedFile = resizeImage($tmp_name, 800, 600, $fileExt);
    
                            if (filesize($resizedFile) <= $maxFileSize) {
                                $imagePaths[] = $resizedFile;
                            } else {
                                echo "Impossible de redimensionner l'image à une taille acceptable.";
                                exit;
                            }
                        } else {
                            // Générer un nom unique pour l'image
                            $newFileName = uniqid() . '.' . $fileExt;
                            $uploadFile = $uploadDir . $newFileName;
    
                            // Déplacer le fichier téléchargé dans le répertoire cible
                            if (move_uploaded_file($tmp_name, $uploadFile)) {
                                //$imagePaths[] = $uploadFile;
                                $imagePaths[] = $uplDir . $newFileName;
                            } else {
                                echo "Échec du téléchargement du fichier.";
                                exit;
                            }
                        }
                    } else {
                        echo "Extension de fichier non autorisée.";
                        exit;
                    }
                } else {
                    echo "Aucun fichier téléchargé ou une erreur est survenue.";
                    exit;
                }
            }
    
            if (count($imagePaths) == 2) {
    
                // Préparer et lier
                $stmt2 = $conn->prepare("INSERT INTO produits (id_categ, desi_prod, prix_prod, descr_prod, img1_prod, img2_prod) 
                        VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt2 === false) {
                    die("Erreur de préparation de la requête : " . $conn->error);
                }

                $stmt2->bind_param("isdsss", $idcateg, $nom_prod, $prix_prod, $descr_prod, $imagePaths[0], $imagePaths[1]);

    
                // Exécuter la requête
                if ($stmt2->execute()) {
                    header("Location: success_add_prod.html");
                } else {
                    echo "Erreur lors de l'exécution de la requête : " . $stmt2->error;
                }
    
                $stmt2->close();
                $conn->close();
            } else {
                echo "Les deux images n'ont pas été téléchargées correctement.";
            }
        } else {
            echo "Aucun fichier téléchargé.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>GBS | Ajouter produit</title>
    <script defer src="scripts.js"></script>
</head>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #333;
        color: white;
        padding: 10px;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
    }

    .sidebar-toggle {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
    }

    .logo {
        font-size: 24px;
    }

    .search-container {
        display: flex;
        align-items: center;
    }

    .search-icon {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        margin-right: 10px;
    }

    .search-input {
        display: none;
        margin-left: 10px;
        padding: 5px;
        font-size: 16px;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: -250px;
        width: 250px;
        height: 100%;
        background-color: #444;
        color: white;
        transition: left 0.3s ease;
        padding-top: 60px;
        z-index: 999;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        padding: 15px;
        text-align: center;
    }

    .sidebar ul li a {
        color: white;
        text-decoration: none;
        display: block;
    }

    .content {
        margin-top: 60px;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar-ribbon {
        height: 25px;
        background-color: rgb(101, 103, 120);
        width: 100%;
        position: fixed;
        top: 50px;
        z-index: 999;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .admin-profile {
        text-align: center;
        margin-bottom: 50px;
    }

    .admin-photo {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 1px;
    }

    .admin-info {
        font-size: 16px;
    }

    .admin-name {
        text-transform: uppercase;
    }

    .green-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        background-color: green;
        border-radius: 50%;
    }

    /* Responsive Styles */
    @media (max-width: 600px) {
        .search-input {
            width: 100%;
        }
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 5px;
    }

    .form-input {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        font-size: 16px;
    }

    .form-textarea {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        font-size: 16px;
        height: 60px;
        resize: vertical;
    }

    .form-button {
        background-color: blue;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
    }

    .form-button:hover {
        background-color: #555;
    }

    .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #000000;
            border-radius: 4px;
            background-color: #f0f8ff; /* Light blue background for input fields */
            color: #000000;
    }

    .form-group label{
        font-weight : bold;
    }

    .preview-img {
        max-width: 100px;
        max-height: 100px;
        display: none;
        margin-top: 10px;
    }

</style>

<body>
    <nav class="navbar">
        <button class="sidebar-toggle" id="sidebarToggle"> </button>
        <div class="logo">GOOD BUY STORE</div>
        <div class="search-container">
            <button class="search-icon" id="decoIcon"><span class="green-dot"></span>
            </button>
        </div>
    </nav>
    <div class="navbar-ribbon">Espace vendeur</div>
    <main class="content">
        <h1>Nouveau produit</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="idcateg">Catégorie du produit</label>
                <select id="idcateg" name="idcateg" required>
                    <?php foreach($lescategories as $categorie): ?>
                        <option value="<?php echo $categorie['id_categ']?>">
                            <?php echo htmlspecialchars($categorie['lib_categ'])?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nom_prod" class="form-label">Nom du produit</label>
                <input type="text" id="nom_prod" name="nom_prod" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="descr_prod" class="form-label">Brève description du produit</label>
                <textarea id="descr_prod" name="descr_prod" class="form-textarea" required></textarea>
            </div>
            <div class="form-group">
                <label for="prix_prod" class="form-label">Prix du produit (FCFA)</label>
                <input type="number" id="prix_prod" name="prix_prod" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="image1" class="form-label">Image 1 (vignette du produit)</label>
                <input type="file" id="image1" name="productImages[]" class="form-input" accept="image/*" onchange="previewImage('image1', 'preview1')" required>
                <img id="preview1" class="preview-img" alt="Aperçu de l'image 1">
            </div>
            <div class="form-group">
                <label for="image2" class="form-label">Image 2</label>
                <input type="file" id="image2" name="productImages[]" class="form-input" accept="image/*" onchange="previewImage('image2', 'preview2')" required>
                <img id="preview2" class="preview-img" alt="Aperçu de l'image 2">
            </div>
            <button type="submit" class="form-button">Ajouter</button>
        </form>
    </main>

    <script>
        function previewImage(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }

    </script>
</body>
</html>
