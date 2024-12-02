<?php

include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

if (isset($_GET['logout'])) {
    unset($user_id);
    session_destroy();
    header('location:login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Accueil</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="container">

   <div class="profile">
      <?php
         // Adapter la requête SQL pour la table utilisateur
         $select = mysqli_query($conn, "SELECT * FROM utilisateur WHERE id = '$user_id'") or die('Échec de la requête');
         if (mysqli_num_rows($select) > 0) {
            $fetch = mysqli_fetch_assoc($select);
         }

         // Vérifier si une image est associée à l'utilisateur
         if (empty($fetch['image'])) {
            echo '<img src="images/default-avatar.png">';
         } else {
            echo '<img src="uploaded_img/'.$fetch['image'].'">';
         }
      ?>
      <h3><?php echo htmlspecialchars($fetch['nom'], ENT_QUOTES, 'UTF-8'); ?></h3>
      <a href="update_profile.php" class="btn">Mettre à jour le profil</a>
      <a href="home.php?logout=<?php echo $user_id; ?>" class="delete-btn">Déconnexion</a>
      <p>Nouveau ? <a href="login.php">Connexion</a> ou <a href="register.php">Inscription</a></p>
   </div>

</div>

</body>
</html>