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

    // Vérifier si l'ID du produit est passé en paramètre
    if (isset($_GET['id'])) {
        $id_prod = $_GET['id'];

        // Récupérer les informations actuelles du produit depuis la base de données
        $sql = "SELECT * FROM produits WHERE id_prod = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_prod);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $nom_prod = $row['desi_prod'];
            $descr_prod = $row['descr_prod'];
            $prix_prod = $row['prix_prod'];
            $id_categ_actuelle = $row['id_categ'];
            $img1_prod = $row['img1_prod'];
            $img2_prod = $row['img2_prod'];
            $img3_prod = $row['img3_prod'];
            $img4_prod = $row['img4_prod'];
            $img5_prod = $row['img5_prod'];
        } else {
            echo "Aucun produit trouvé avec cet identifiant.";
            exit();
        }
    } else {
        echo "Identifiant du produit non spécifié.";
        exit();
    }

    // Configuration des téléchargements d'images
    $uploadDir = '../uploads/' . $dir . '/';
    $uplDir = 'uploads/' . $dir . '/';
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
        $imagePaths = [$img1_prod, $img2_prod, $img3_prod, $img4_prod, $img5_prod];

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
                                $imagePaths[$key] = $resizedFile;
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
                                $imagePaths[$key] = $uplDir . $newFileName;
                            } else {
                                echo "Échec du téléchargement du fichier.";
                                exit;
                            }
                        }
                    } else {
                        echo "Extension de fichier non autorisée.";
                        exit;
                    }
                }
            }

            // Préparer et lier
            $stmt2 = $conn->prepare("UPDATE produits SET id_categ=?, desi_prod=?, prix_prod=?, descr_prod=?, img1_prod=?, img2_prod=?, img3_prod=?, img4_prod=?, img5_prod=? WHERE id_prod=?");
            if ($stmt2 === false) {
                die("Erreur de préparation de la requête : " . $conn->error);
            }

            // Lier les paramètres
            $stmt2->bind_param("isdssssssi", $idcateg, $nom_prod, $prix_prod, $descr_prod, $imagePaths[0], $imagePaths[1], $imagePaths[2], $imagePaths[3], $imagePaths[4], $id_prod);

            // Exécuter la requête
            if ($stmt2->execute()) {
                header("Location: success_modif_prod.html");
            } else {
                echo "Erreur lors de l'exécution de la requête : " . $stmt2->error;
            }

            $stmt2->close();
            $conn->close();
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
    <title>GBS | Modifier produit</title>
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
        display: block;
        margin-top: 10px;
    }

</style>
<body>
    <nav class="navbar">
        <button class="sidebar-toggle" id="sidebarToggle"></button>
        <div class="logo">GOOD BUY STORE</div>
        <div class="search-container">
            <button class="search-icon" id="decoIcon"><span class="green-dot"></span></button>
        </div>
    </nav>
    <div class="navbar-ribbon">Espace vendeur</div>
    <main class="content">
        <h1>Modifier produit</h1>
        <form action="" method="POST" enctype="multipart/form-data" id="productForm">
            <div class="form-group">
                <label for="idcateg">Catégorie du produit</label>
                <select id="idcateg" name="idcateg" required>
                    <?php foreach($lescategories as $categorie): ?>
                        <option value="<?php echo $categorie['id_categ']?>" <?php if ($categorie['id_categ'] == $id_categ_actuelle) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($categorie['lib_categ'])?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nom_prod" class="form-label">Nom du produit</label>
                <input type="text" id="nom_prod" name="nom_prod" class="form-input" value="<?php echo htmlspecialchars($nom_prod) ?>" required>
            </div>
            <div class="form-group">
                <label for="descr_prod" class="form-label">Brève description du produit</label>
                <textarea id="descr_prod" name="descr_prod" class="form-textarea" required><?php echo htmlspecialchars($descr_prod) ?></textarea>
            </div>
            <div class="form-group">
                <label for="prix_prod" class="form-label">Prix du produit (FCFA)</label>
                <input type="number" id="prix_prod" name="prix_prod" class="form-input" value="<?php echo $prix_prod ?>" required>
            </div>
            <div class="form-group">
                <label for="image1" class="form-label">Image 1 (vignette du produit)</label>
                <input type="file" id="image1" name="productImages[]" class="form-input" accept="image/*" onchange="previewImage('image1', 'preview1')">
                <input type="hidden" name="existingImage1" value="<?php echo htmlspecialchars($img1_prod) ?>">
                <img id="preview1" class="preview-img" alt="Image 1" src="<?php echo "../" . htmlspecialchars($img1_prod) ?>">
            </div>
            <div class="form-group">
                <label for="image2" class="form-label">Image 2</label>
                <input type="file" id="image2" name="productImages[]" class="form-input" accept="image/*" onchange="previewImage('image2', 'preview2')">
                <input type="hidden" name="existingImage2" value="<?php echo htmlspecialchars($img2_prod) ?>">
                <img id="preview2" class="preview-img" alt="Image 2" src="<?php echo "../" . htmlspecialchars($img2_prod) ?>">
            </div>
            <div class="form-group">
                <label for="image3" class="form-label">Image 3</label>
                <input type="file" id="image3" name="productImages[]" class="form-input" accept="image/*" onchange="previewImage('image3', 'preview3')">
                <img id="preview3" class="preview-img" alt="(vide)" src="<?php echo "../".$img3_prod ?>">
            </div>
            <div class="form-group">
                <label for="image4" class="form-label">Image 4</label>
                <input type="file" id="image4" name="productImages[]" class="form-input" accept="image/*" onchange="previewImage('image4', 'preview4')">
                <img id="preview4" class="preview-img" alt="(vide)" src="<?php echo "../".$img4_prod ?>">
            </div>
            <div class="form-group">
                <label for="image5" class="form-label">Image 5</label>
                <input type="file" id="image5" name="productImages[]" class="form-input" accept="image/*" onchange="previewImage('image5', 'preview5')">
                <img id="preview5" class="preview-img" alt="(vide)" src="<?php echo "../".$img5_prod ?>">
            </div>
            <button type="submit" class="form-button">Modifier</button>
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

