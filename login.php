<?php

include 'config.php';
session_start();

if (isset($_POST['submit'])) {

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

   // Adapter la requête pour utiliser la table utilisateur
   $select = mysqli_query($conn, "SELECT * FROM `utilisateur` WHERE email = '$email' AND mot_de_passe = '$pass'") or die('Échec de la requête');

   if (mysqli_num_rows($select) > 0) {
      $row = mysqli_fetch_assoc($select);
      $_SESSION['user_id'] = $row['id'];
      header('location:home.php');
      exit;
   } else {
      $message[] = 'Email ou mot de passe incorrect !';
   }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Connexion</title>

   <!-- Lien vers le fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Connexion</h3>
      <?php
      if (isset($message)) {
         foreach ($message as $message) {
            echo '<div class="message">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
         }
      }
      ?>
      <input type="email" name="email" placeholder="Entrez votre email" class="box" required>
      <input type="password" name="password" placeholder="Entrez votre mot de passe" class="box" required>
      <input type="submit" name="submit" value="Se connecter" class="btn">
      <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous maintenant</a></p>
   </form>

</div>

</body>
</html>
