<?php
include 'config.php';
session_start();

// Vérifiez que l'ID de l'utilisateur est bien défini dans la session
if (!isset($_SESSION['user_id'])) {
    die('Utilisateur non connecté.'); // L'utilisateur n'est pas connecté
}

$user_id = $_SESSION['user_id'];

// Vérifiez si l'utilisateur existe dans la base de données avec cet ID
$stmt = $conn->prepare("SELECT * FROM `utilisateur` WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $fetch = $result->fetch_assoc();
} else {
    die('Utilisateur introuvable.');
}

// Traitement de la mise à jour du profil
if (isset($_POST['update_profile'])) {
    // Mise à jour du nom, email et numéro de téléphone
    $update_nom = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $update_num_tel = mysqli_real_escape_string($conn, $_POST['update_num_tel']);  // Champ ajouté pour le numéro de téléphone

    // Vérification des champs de mot de passe
    $update_pass = mysqli_real_escape_string($conn, md5($_POST['update_pass'])); // On peut remplacer md5 par un meilleur hash comme bcrypt.
    $new_pass = mysqli_real_escape_string($conn, md5($_POST['new_pass']));
    $confirm_pass = mysqli_real_escape_string($conn, md5($_POST['confirm_pass']));

    // Mise à jour du mot de passe si fourni et vérification
    if (!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)) {
        if ($update_pass != $fetch['mot_de_passe']) {
            $message[] = 'L\'ancien mot de passe ne correspond pas !';
        } elseif ($new_pass != $confirm_pass) {
            $message[] = 'Les mots de passe ne correspondent pas !';
        } else {
            // Mise à jour du mot de passe
            $update_password_query = "UPDATE `utilisateur` SET mot_de_passe = ? WHERE id = ?";
            $stmt = $conn->prepare($update_password_query);
            $stmt->bind_param("si", $confirm_pass, $user_id);
            $stmt->execute();
            $message[] = 'Mot de passe mis à jour avec succès !';
        }
    }

    // Mise à jour du nom, email et numéro de téléphone
    $update_query = "UPDATE `utilisateur` SET nom = ?, email = ?, numero_tel = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssii", $update_nom, $update_email, $update_num_tel, $user_id);
    $stmt->execute();

    // Gestion de l'image
    if (!empty($_FILES['update_image']['name'])) {
        $update_image = $_FILES['update_image']['name'];
        $update_image_size = $_FILES['update_image']['size'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_folder = 'uploaded_img/' . $update_image;

        if ($update_image_size > 2000000) {
            $message[] = 'La taille de l\'image est trop grande !';
        } else {
            $image_update_query = "UPDATE `utilisateur` SET image = ? WHERE id = ?";
            $stmt = $conn->prepare($image_update_query);
            $stmt->bind_param("si", $update_image, $user_id);
            $stmt->execute();

            // Déplacement de l'image téléchargée
            move_uploaded_file($update_image_tmp_name, $update_image_folder);
            $message[] = 'Image mise à jour avec succès !';
        }
    }

    // Message de succès après la mise à jour
    if (!empty($message)) {
        foreach ($message as $msg) {
            echo '<div class="message">' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mettre à jour le profil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="update-profile">

    <form action="" method="post" enctype="multipart/form-data">
        <?php
            if (empty($fetch['image'])) {
                echo '<img src="images/default-avatar.png">';
            } else {
                echo '<img src="uploaded_img/' . $fetch['image'] . '">';
            }
        ?>
        
        <div class="flex">
            <div class="inputBox">
                <span>Nom d'utilisateur :</span>
                <input type="text" name="update_name" value="<?php echo htmlspecialchars($fetch['nom'], ENT_QUOTES, 'UTF-8'); ?>" class="box">
                <span>Votre email :</span>
                <input type="email" name="update_email" value="<?php echo htmlspecialchars($fetch['email'], ENT_QUOTES, 'UTF-8'); ?>" class="box">
                <span>Numéro de téléphone :</span>
                <input type="text" name="update_num_tel" value="<?php echo htmlspecialchars($fetch['numero_tel'], ENT_QUOTES, 'UTF-8'); ?>" class="box">
                <span>Mettre à jour votre photo :</span>
                <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">
            </div>
            <div class="inputBox">
                <input type="hidden" name="old_pass" value="<?php echo $fetch['mot_de_passe']; ?>">
                <span>Ancien mot de passe :</span>
                <input type="password" name="update_pass" placeholder="Entrez votre ancien mot de passe" class="box">
                <span>Nouveau mot de passe :</span>
                <input type="password" name="new_pass" placeholder="Entrez un nouveau mot de passe" class="box">
                <span>Confirmez le mot de passe :</span>
                <input type="password" name="confirm_pass" placeholder="Confirmez le nouveau mot de passe" class="box">
            </div>
        </div>
        <input type="submit" value="Mettre à jour le profil" name="update_profile" class="btn">
        <a href="home.php" class="delete-btn">Retour</a>
    </form>

</div>

</body>
</html>
