<?php

 session_start();

 $titre="Le cri des poissons - Connexion";

 include("includes/identifiants.php");
 include("includes/debut.php");
 include("includes/menu.php");
 
 echo '<p><i>Navi</i> : <a href="./index.php">Index du forum</a> --> Connexion';
 
 if ($id!=0) erreur(ERR_IS_CO);
 
 
 
 if (!isset($_POST['pseudo'])) // Formulaire
  {
   
   echo '<br /><img src="img/gameover.jpg" alt="GameOverImage" title="Continue ?" />
   
   <form method="post" action="connexion.php">
   <fieldset id="fieldsetconnexion">
   <legend><em>Identifiants</em></legend>
   <p>
   <label for="pseudo">Pseudo :</label><input name="pseudo" type="text" id="pseudo" /><br />
   <label for="password">Mot de Passe :</label><input type="password" name="password" id="password" />
   </p>
   </fieldset>
   <p><input type="submit" value="Se connecter" /></p></form>
   
   Cliquez <a href="./register.php">ici</a> pour vous inscrire.<br /><br />
   
   
   
   </div>
   </body>
   </html>';
   
  }
  
 else
  {
    
   $message='';
   
   if (empty($_POST['pseudo']) || empty($_POST['password']) ) // Oublie d'un champ
    {
	
     $message = '<p>une erreur s\'est produite pendant votre identification.
     Vous devez remplir tous les champs</p>
     <p><a href="./connexion.php">Réessayer ?</a></p>';
    
	}
    
   else // Chickidicheck le mdp
    {

	 $query=$db->prepare('SELECT apat_mdp, apat_id, apat_rang, apat_pseudo
     FROM forum_apat WHERE apat_pseudo = :pseudo');
     $query->bindValue(':pseudo',$_POST['pseudo'], PDO::PARAM_STR);
     $query->execute();
     $data=$query->fetch();
   
     if ($data['apat_mdp'] == sha1($_POST['password'])) // Acces OK et attribution des valeurs
      {
	  
	   $_SESSION['pseudo'] = $data['apat_pseudo'];
       $_SESSION['level'] = $data['apat_rang'];
       $_SESSION['id'] = $data['apat_id'];;
	   
	   $query->CloseCursor();
	   
	   $derniere_visite = time();
	   
	   $query=$db->prepare('UPDATE forum_apat SET apat_derniere_visite = :derniere
	   WHERE apat_id = :id');
	   $query->bindValue(':derniere',$derniere_visite,PDO::PARAM_INT);
	   $query->bindValue(':id',$_SESSION['id'],PDO::PARAM_INT);
	   $query->execute();

       header('refresh:3;url=index.php');
       exit ('<p>Bienvenue '.$_SESSION['pseudo'].',
       vous êtes à présent connecté(e) !</p>
       <p>Vous allez être redirigé vers l\'accueil dans quelques instants...</p>');

      }
	  
     else // Acces refusé
      {
	  
       $message = '<p>Une erreur s\'est produite
       pendant votre identification.<br /> Le mot de passe ou le pseudo
       entré n\'est pas correcte.</p><p><a href="./connexion.php">Réessayer ?</a>
       <br /><br /><a href="./index.php">Revenir à l\'accueil</a></p>';
    
	  }
	  
     $query->CloseCursor();
	 
    }
	
    echo $message.'</div></body></html>';
 
  }
  

 
?>