<?php

session_start();

$titre="Le cri des poissons - Profil";

include("includes/identifiants.php");
include("includes/debut.php");
include("includes/menu.php");

$action = isset($_GET['action'])?htmlspecialchars($_GET['action']):'consulter';
$membre = isset($_GET['m'])?(int) $_GET['m']:'';


switch($action)
 {

 
  case "consulter":

  
   $query=$db->prepare('SELECT apat_pseudo, apat_avatar, apat_email, apat_post, apat_inscrit FROM forum_apat WHERE apat_id=:membre');
   $query->bindValue(':membre',$membre, PDO::PARAM_INT);
   $query->execute();
   $data=$query->fetch();
 
   echo '<p><i>Navi</i> : <a href="./index.php">Index du forum</a> --> Profil de '.stripslashes(htmlspecialchars($data['apat_pseudo']));
  
   echo '<h2>Profil de '.stripslashes(htmlspecialchars($data['apat_pseudo'])).'</h2>';

   echo '<img src="./img/avatars/'.$data['apat_avatar'].'" alt="Ce membre n\'a pas d\'avatar" />';

   echo '<br /><br />';
        
   echo 'Ce membre est inscrit depuis le <strong>'.date('d/m/Y',$data['apat_inscrit']).'</strong> et a posté <strong>'.$data['apat_post'].'</strong> messages<br /><br />';
  
   $query->CloseCursor();
  
   break;

   
  case "modifier":
   

   if (empty($_POST['sent']))
    {

	 if ($id==0) erreur(ERR_IS_CO);
 
     $query=$db->prepare('SELECT apat_pseudo, apat_email, apat_avatar FROM forum_apat WHERE apat_id=:id');
     $query->bindValue(':id',$id,PDO::PARAM_INT);
     $query->execute();
     $data=$query->fetch();
        
	 echo '<p><i>Navi</i> : <a href="./index.php">Index du forum</a> --> Réglages';
        
	 echo '<h2>Modifier son profil</h2>';
         
     echo '<form method="post" action="voirprofil.php?action=modifier" enctype="multipart/form-data">
	 
     <fieldset><legend>Identifiants</legend>
     Pseudo : <strong>'.stripslashes(htmlspecialchars($data['apat_pseudo'])).'</strong><br />      
     <label for="password">Nouveau MdP : </label>
     <input type="password" name="password" id="password" /><br />
     <label for="confirm">Confirmation MdP : </label>
     <input type="password" name="confirm" id="confirm" /></fieldset>
  
     <fieldset><legend>Contacts</legend>
     <label for="email">E-mail :</label>
     <input type="text" name="email" id="email"
     value="'.stripslashes($data['apat_email']).'" /><br /></fieldset>
  
     <fieldset><legend>Profil sur le forum</legend>
     <label for="avatar">Modifier l\'avatar :</label>
     <input type="file" name="avatar" id="avatar" />
     (Taille max : 100 ko / 150 x 150 px)<br /><br />
     <img src="./img/avatars/'.$data['apat_avatar'].'" alt="pas d avatar" /><br /><br />
	 <label><input type="checkbox" name="delete" value="Delete" />
     Supprimer l\'avatar</label><br /><br /></fieldset>
	 
     <p><input type="submit" value="Modifier son profil" />
     <input type="hidden" id="sent" name="sent" value="1" /></p></form>';
	 
     $query->CloseCursor();
	 
    }  
    
   else
    {

     $mdp_erreur = NULL;
     $email_erreur1 = NULL;
     $email_erreur2 = NULL;
     $avatar_erreur = NULL;
     $avatar_erreur1 = NULL;
     $avatar_erreur2 = NULL;
     $avatar_erreur3 = NULL;
 
     $i = 0;
     $temps = time();
     $email = $_POST['email'];
     $pass = sha1($_POST['password']);
     $confirm = sha1($_POST['confirm']);
 
     if ($pass != $confirm || empty($confirm) || empty($pass))
      {
         
	   $mdp_erreur = "La confirmation du mot de passe est foireuse !";
       $i++;
      
	  }

	 $query=$db->prepare('SELECT apat_email FROM forum_apat WHERE apat_id =:id');
     $query->bindValue(':id',$id,PDO::PARAM_INT);
     $query->execute();
     $data=$query->fetch();
    
	 if (strtolower($data['apat_email']) != strtolower($email))
      {

       $query=$db->prepare('SELECT COUNT(*) AS nbr FROM forum_apat WHERE apat_email =:mail');
       $query->bindValue(':mail',$email,PDO::PARAM_STR);
       $query->execute();
       $mail_free=($query->fetchColumn()==0)?1:0;
       $query->CloseCursor();
       
	   if(!$mail_free)
        {
		
         $email_erreur1 = "Cette adresse mail figure déjà dans nos humbles archives.";
         $i++;
        
		}
 

       if (!preg_match("#^[a-z0-9A-Z._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email) || empty($email))
        {
		
         $email_erreur2 = "Vérifiez votre email !";
         $i++;
			
        }
      
	  }

	 if (!empty($_FILES['avatar']['size']))
      {

       $maxsize = 100240;
       $maxwidth = 150;
       $maxheight = 150;

       $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png', 'bmp' );
  
       if ($_FILES['avatar']['error'] > 0)
        {
		
         $avatar_erreur = "Erreur lors du tranfsert de l'avatar : ";
        
		}
        
	   if ($_FILES['avatar']['size'] > $maxsize)
        {
		
         $i++;
         $avatar_erreur1 = "Le fichier est trop gros : (<strong>".$_FILES['avatar']['size']." Octets</strong> contre <strong>".$maxsize." Octets</strong>)";
        
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
   
     echo '<p><i>Fil d\'Ariane</i> : <a href="./index.php">Index du forum</a> --> Modification du profil';
     echo '<h2>Modification d\'un profil</h2>';
 
     if ($i == 0)
      {
	  
       if (!empty($_FILES['avatar']['size']))
        {
		
         $nomavatar=move_avatar($_FILES['avatar']);
         $query=$db->prepare('UPDATE forum_apat SET apat_avatar = :avatar WHERE apat_id = :id');
         $query->bindValue(':avatar',$nomavatar,PDO::PARAM_STR);
         $query->bindValue(':id',$id,PDO::PARAM_INT);
         $query->execute();
         $query->CloseCursor();
        
		}

       if (isset($_POST['delete']))
        {

         $query=$db->prepare('UPDATE forum_apat SET apat_avatar=0 WHERE apat_id = :id');
         $query->bindValue(':id',$id,PDO::PARAM_INT);
         $query->execute();
         $query->CloseCursor();

		}
  
       echo'<h2>Modification terminée</h2>';
       echo'<p>Votre profil a été modifié avec succès !</p>';
       echo'<p>Cliquez <a href="./index.php">ici</a> pour revenir à la page d accueil</p>';
  
       $query=$db->prepare('UPDATE forum_apat SET apat_mdp = :mdp, apat_email=:mail WHERE apat_id=:id');
       $query->bindValue(':mdp',$pass,PDO::PARAM_INT);
       $query->bindValue(':mail',$email,PDO::PARAM_STR);
       $query->bindValue(':id',$id,PDO::PARAM_INT);
       $query->execute();
       $query->CloseCursor();
    
	  }
    
	 else
      {
	  
        echo'<h2>Modification interrompue</h2>';
        echo'<p>Une ou plusieurs erreurs se sont produites pendant la modification du profil</p>';
        echo'<p>'.$i.' erreur(s)</p>';
        echo'<p>'.$mdp_erreur.'</p>';
        echo'<p>'.$email_erreur1.'</p>';
        echo'<p>'.$email_erreur2.'</p>';
        echo'<p>'.$avatar_erreur.'</p>';
        echo'<p>'.$avatar_erreur1.'</p>';
        echo'<p>'.$avatar_erreur2.'</p>';
        echo'<p>'.$avatar_erreur3.'</p>';
        echo'<p> Cliquez <a href="./voirprofil.php?action=modifier">ici</a> pour recommencer</p>';
    
	  }
    }
    
   break;
  
   default;
   echo'<p>Cette action est impossible</p>';
  
}



?>

  </div>
 </body>
</html>