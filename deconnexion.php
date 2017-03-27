<?php

 session_start();

 $titre="Ciao ciao ! =)";

 include("includes/identifiants.php");
 include("includes/debut.php");
 include("includes/menu.php");
 
 session_destroy();

 $query=$db->prepare('DELETE FROM forum_whosonline WHERE online_id= :id');
 $query->bindValue(':id',$id,PDO::PARAM_INT);
 $query->execute();
 $query->CloseCursor();
 
 if ($id==0) erreur(ERR_IS_CO);

 header('refresh:3;url=index.php');
 exit ('<p>Vous êtes à présent déconnecté(e).</p>
 <p>Vous allez être redirigé vers l\'accueil dans quelques instants...</p>');
 
 echo '<p>Vous êtes à présent déconnecté(e). <br /><br />
 Cliquez <a href="./index.php">ici</a> pour revenir à l\'accueil.</p>';
 echo '</div></body></html>';

 ?>