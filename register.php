<?php

 session_start();

 $titre="Le cri des poissons - Inscriptions";

 include("includes/identifiants.php");
 include("includes/debut.php");
 include("includes/menu.php");

 echo '<p><i>Navi</i> : <a href="./index.php">Index du forum</a> --> Inscriptions';
 
 if ($id!=0) erreur(ERR_IS_CO);

 if (empty($_POST['pseudo'])) // Si on la variable est vide, on peut considérer qu'on est sur le formulaire
  {
    
   echo '<h2>Formularus Regestus</h2>';
   echo '<form method="post" action="register.php" enctype="multipart/form-data">
   <fieldset class="fieldsetregister"><legend><em>Identifiants</em></legend>
   <label for="pseudo">* Pseudo :</label>  <input name="pseudo" type="text" id="pseudo" /> (3-26 caractères)<br />
   <label for="password">* Mot de Passe :</label><input type="password" name="password" id="password" /><br />
   <label for="confirm">* Confirmer le MdP :</label><input type="password" name="confirm" id="confirm" />
   </fieldset>
   <fieldset class="fieldsetregister"><legend><em>Contact</em></legend>
   <label for="email">* E-mail :</label><input type="text" name="email" id="email" /><br />
   </fieldset>
   <fieldset class="fieldsetregister"><legend><em>Profil sur le forum</em></legend>
   <label for="avatar">Avatar :</label><input type="file" name="avatar" id="avatar" /> <br />Taille max : 100 Ko / 150 x 150 px)<br />
   </fieldset>
   <p>* Information requise</p>
   <p><input type="submit" value="Créer la session" /></p></form>
   </div>
   </body>
   </html>';
     
  } // Fin du formulaire

 else // Traitement
  {
    
   $pseudo_erreur1 = NULL;
   $pseudo_erreur2 = NULL;
   $mdp_erreur = NULL;
   $email_erreur1 = NULL;
   $email_erreur2 = NULL;
   $avatar_erreur = NULL;
   $avatar_erreur1 = NULL;
   $avatar_erreur2 = NULL;
   $avatar_erreur3 = NULL;
  
   // On récupère les variables
   $i = 0;
   $temps = time();
   $pseudo=$_POST['pseudo'];
   $email = $_POST['email'];
   $pass = sha1($_POST['password']);
   $confirm = sha1($_POST['confirm']);
     
   // Chickidickeck pseudo
   
   $query=$db->prepare('SELECT COUNT(*) AS nbr FROM forum_apat WHERE apat_pseudo =:pseudo');
   $query->bindValue(':pseudo',$pseudo, PDO::PARAM_STR);
   $query->execute();
   $pseudo_free=($query->fetchColumn()==0)?1:0;
   $query->CloseCursor();
   
   if(!$pseudo_free)
    {
	
     $pseudo_erreur1 = "Ce pseudo est déjà enregistré !";
     $i++;
    
	}
 
   if (strlen($pseudo) < 3 || strlen($pseudo) > 26)
    {
	
     $pseudo_erreur2 = "Le pseudo doit contenir entre 3 et 26 caractères !!";
     $i++;
    
	}
 
    // Chickidickeck mdp
    
   if ($pass != $confirm || empty($confirm) || empty($pass))
    {
    
     $mdp_erreur = "La confirmation du mot de passe est foireuse !";
     $i++;

	}

   // Vérification du mail
 
   // Disponibilité du mail
   
   $query=$db->prepare('SELECT COUNT(*) AS nbr FROM forum_apat WHERE apat_email =:mail');
   $query->bindValue(':mail',$email, PDO::PARAM_STR);
   $query->execute();
   $mail_free=($query->fetchColumn()==0)?1:0;
   $query->CloseCursor();
     
   if(!$mail_free)
    {
    
     $email_erreur1 = "Cette adresse mail figure déjà dans nos humbles archives.";
     $i++;

	}
    
   // Format du mail
   if (!preg_match("#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-z]{2,4}$#", $email) || empty($email))
    {
     
	 $email_erreur2 = "Vérifiez votre email !";
     $i++;
	 
    }
   
   //Vérification de l'avatar :
   if (!empty($_FILES['avatar']['size']))
    {

     $maxsize = 100240; //Poid de l'image
     $maxwidth = 150; //Largeur de l'image
     $maxheight = 150; //Longueur de l'image
     $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png', 'bmp' ); //Liste des extensions valides
         
     if ($_FILES['avatar']['error'] > 0)
      {
                
	   $avatar_erreur = "Erreur lors du transfert de l'avatar : ";
        
	  }
        
	 if ($_FILES['avatar']['size'] > $maxsize)
      {
                
	   $i++;
       $avatar_erreur1 = "Le fichier est trop gros : (<strong>".$_FILES['avatar']['size']." Octets</strong>    contre <strong>".$maxsize." Octets</strong>)";
        
	  }
 
      $image_sizes = getimagesize($_FILES['avatar']['tmp_name']);
        
	  if ($image_sizes[0] > $maxwidth OR $image_sizes[1] > $maxheight)
       {
                
		$i++;
        $avatar_erreur2 = "Image trop large ou trop longue : (<strong>".$image_sizes[0]."x".$image_sizes[1]."</strong> contre <strong>".$maxwidth."x".$maxheight."</strong>)";
        
	   }
         
      $extension_upload = strtolower(substr(  strrchr($_FILES['avatar']['name'], '.')  ,1));
      
	  if (!in_array($extension_upload,$extensions_valides) )
       {
       
	    $i++;
        $avatar_erreur3 = "Extension de l'avatar incorrecte";
        
	   }
    
	}
	
   if ($i==0)
    {
    
	 echo'<h2>You got it !</h2>';
     echo'<p>Bienvenue '.stripslashes(htmlspecialchars($_POST['pseudo'])).' vous êtes désormais inscrit(e) sur le forum !</p>
     <p>Cliquez <a href="./index.php">ici</a> pour revenir à l\'accueil</p>';
     
     //La ligne suivante sera commentée plus bas
     $nomavatar=(!empty($_FILES['avatar']['size']))?move_avatar($_FILES['avatar']):'';
    
     $query=$db->prepare('INSERT INTO forum_apat (apat_pseudo, apat_mdp, apat_email,            
     apat_avatar, apat_inscrit, apat_derniere_visite)
     VALUES (:pseudo, :pass, :email, :nomavatar, :temps, :temps)');
     $query->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
     $query->bindValue(':pass', $pass, PDO::PARAM_STR);
     $query->bindValue(':email', $email, PDO::PARAM_STR);
     $query->bindValue(':nomavatar', $nomavatar, PDO::PARAM_STR);
     $query->bindValue(':temps', $temps, PDO::PARAM_INT);
     $query->execute();
 
     //Et on définit les variables de sessions
     $_SESSION['pseudo'] = $pseudo;
     $_SESSION['id'] = $db->lastInsertId(); ;
     $_SESSION['level'] = 2;
     $query->CloseCursor();
    
	}
    
   else
    {
     
	 echo'<h2>Failure...</h2>';
     echo'<p>Une ou plusieurs erreurs se sont produites pendant l\'inscription</p>';
     echo'<p>'.$i.' erreur(s)</p>';
     echo'<p>'.$pseudo_erreur1.'</p>';
     echo'<p>'.$pseudo_erreur2.'</p>';
     echo'<p>'.$mdp_erreur.'</p>';
     echo'<p>'.$email_erreur1.'</p>';
     echo'<p>'.$email_erreur2.'</p>';
     echo'<p>'.$avatar_erreur.'</p>';
     echo'<p>'.$avatar_erreur1.'</p>';
     echo'<p>'.$avatar_erreur2.'</p>';
     echo'<p>'.$avatar_erreur3.'</p>';
        
     echo'<p>Cliquez <a href="./register.php">ici</a> pour réessayer</p>';
    
	}

  }

?>

  </div>
 </body>
</html>