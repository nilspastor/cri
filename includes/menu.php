<?php

 echo '<body>
 <div id="banniere"><a href="index.php"><img src="img/logo-cri.png" alt="Banniere" title="Bienvenue sur le forum du Cri des poissons" style="display: block; margin: auto; margin-top: 28px; margin-bottom: 18px" /></a>
 <p class="logo-title">Le cri des poissons</p></div>

 <div id="corps_forum"><p class="menuf" style="text-align:center;"><a href="index.php"><img src="img/accueil.jpg" alt="Icone_accueil" title="Page d\'accueil" /></a>';

 if ($id==0)
  {
   echo ' <a href="register.php"><img src="img/inscriptions.jpg" alt="Icone_registre" title="Inscriptions" /></a> <a href="connexion.php"><img src="img/login.jpg" alt="Icone_login" title="Connexion" /></a>';
  }
  
 else
  {
   echo ' <a href="deconnexion.php"><img src="img/logout.jpg" alt="Icone_logout" title="Déconnexion" /></a>';
  }
 
 echo ' <a href="memberlist.php"><img src="img/membres.jpg" alt="Icone_membres" title="Liste des membres" /></a>';

 if ($id!=0)
  {

   echo ' <a href="messagesprives.php"><img src="img/mp.jpg" alt="Icone_mp" title="Messagerie privée" /></a>
   <a href="voirprofil.php?m='.$_SESSION['id'].'&amp;action=modifier"><img src="img/profil.jpg" alt="Icone_profil" title="Modifier son profil" /></a>
   </p>';

  }
?>