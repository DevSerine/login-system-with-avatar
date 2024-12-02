<?php

include 'config.php';

if (isset($_POST['submit'])) {

   $nom = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
   $numero_tel = mysqli_real_escape_string($conn, $_POST['telephone']); // Utilisation du bon nom de champ
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;

   // Vérifie si l'utilisateur existe déjà
   $select = mysqli_query($conn, "SELECT * FROM `utilisateur` WHERE email = '$email'");

   if (!$select) {
      die('Erreur SQL : ' . mysqli_error($conn)); // Affiche l'erreur si la requête échoue
   }

   if (mysqli_num_rows($select) > 0) {
      $message[] = 'Cet utilisateur existe déjà !';
   } else {
      if ($pass != $cpass) {
         $message[] = 'Les mots de passe ne correspondent pas !';
      } elseif ($image_size > 2000000) {
         $message[] = 'La taille de l\'image est trop grande !';
      } else {
         // Vérifie si l'email est celui de l'administrateur
         if ($email == 'admin@gmail.com') {
            $role = 'admin'; // Utilisateur admin
         } else {
            $role = 'user'; // Utilisateur classique
         }

         // Insertion des données dans la table `utilisateur`
         $insert = mysqli_query($conn, "INSERT INTO `utilisateur`(nom, email, mot_de_passe, numero_tel, image, type_utilisateur) 
            VALUES('$nom', '$email', '$pass', '$numero_tel', '$image', '$role')");

         if (!$insert) {
            die('Erreur SQL : ' . mysqli_error($conn)); // Affiche l'erreur SQL si l'insertion échoue
         }

         // Si l'insertion est réussie
         if ($insert) {
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Inscription réussie !';
            // Affichage du message avant la redirection
            foreach ($message as $msg) {
                echo '<div class="message">' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
            }
            // Rediriger vers la page de connexion après un délai (par exemple 2 secondes)
            header("Refresh:2; url=login.php");
            exit;
         } else {
            $message[] = 'Échec de l\'inscription !';
         }
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
   <title>Inscription</title>

   <!-- Lien vers le fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Inscrivez-vous maintenant</h3>
      <?php
      // Affichage des messages de succès ou d'erreur
      if (isset($message)) {
         foreach ($message as $msg) {
            echo '<div class="message">' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
         }
      }
      ?>
      <input type="text" name="name" placeholder="Entrez votre nom" class="box" required>
      <input type="email" name="email" placeholder="Entrez votre email" class="box" required>
      <input type="password" name="password" placeholder="Entrez votre mot de passe" class="box" required>
      <input type="password" name="cpassword" placeholder="Confirmez votre mot de passe" class="box" required>
      <input type="text" name="telephone" placeholder="Entrez votre numéro de téléphone" class="box" required> <!-- Nouveau champ téléphone -->
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <input type="submit" name="submit" value="S'inscrire" class="btn">
      <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous</a></p>
   </form>

</div>

</body>
</html>
