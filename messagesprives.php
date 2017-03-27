<?php

session_start();

$titre="Le cri des poissons - Messagerie privée";

$balises = true;

include("includes/identifiants.php");
include("includes/debut.php");
include("includes/bbcode.php");
include("includes/menu.php");
 
$action = (isset($_GET['action']))?htmlspecialchars($_GET['action']):'';
 

switch($action) //Switch $action
 {
 
  case "consulter": // On consulte
  
  echo'<p><i>Navi</i> : <a href="./index.php">Index du forum</a> --> <a href="./messagesprives.php">Boîte aux lettres</a> --> Consulter un message</p>';
  
  $id_mess = (int) $_GET['id'];
  
  echo '<h2>Consulter un message</h2><br /><br />';
 
  $query = $db->prepare('SELECT  mp_expediteur, mp_receveur, mp_titre,              
  mp_time, mp_text, mp_lu, apat_id, apat_pseudo, apat_avatar, apat_inscrit, apat_post FROM forum_mp
  LEFT JOIN forum_apat ON apat_id = mp_expediteur
  WHERE mp_id = :id');
  $query->bindValue(':id',$id_mess,PDO::PARAM_INT);
  $query->execute();
  $data=$query->fetch();
 
  if ($id != $data['mp_receveur']) erreur(ERR_WRONG_USER);
        
  echo'<p><a href="./messagesprives.php?action=repondre&amp;dest='.$data['mp_expediteur'].'">
  <img src="./images/repondre.jpg" alt="Répondre"
  title="Répondre à ce message" /></a></p>';   
?>
  <table>
   <tr>
    <th class="vt_auteur"><strong>Auteur</strong></th>            
    <th class="vt_mess"><strong>Message</strong></th>
   </tr>
   <tr>
    <td>
    <?php echo'<strong><a href="./voirprofil.php?m='.$data['apat_id'].'&amp;action=consulter">
	'.stripslashes(htmlspecialchars($data['apat_pseudo'])).'</a></strong>
	</td>
    <td>Posté à '.date('H\hi \l\e d M Y',$data['mp_time']).'
	</td>';
?>
   </tr>
   <tr>
    <td>
<?php
         
  // Infos apat
  echo'<p><img src="./img/avatars/'.$data['apat_avatar'].'" alt="Avatar" />
  <br />Membre inscrit le '.date('d/m/Y',$data['apat_inscrit']).'
  <br />Messages : '.$data['apat_post'].'
  </td><td>';
         
  echo code(nl2br(stripslashes(htmlspecialchars($data['mp_text'])))).'
  <hr />
  </td></tr></table>';
  
  
  if ($data['mp_lu'] == 0) // Mp_lu
   {
   
    $query->CloseCursor();
    $query=$db->prepare('UPDATE forum_mp SET mp_lu = :lu WHERE mp_id= :id');
    $query->bindValue(':id',$id_mess, PDO::PARAM_INT);
    $query->bindValue(':lu','1', PDO::PARAM_STR);
    $query->execute();
    $query->CloseCursor();
	
   }
         
  break;
  
  
  case "nouveau": // Nouveau mp
        
  echo'<p><i>Navi</i> : <a href="./index.php">Index du forum</a> --> <a href="./messagesprives.php">Boîte aux lettres</a> --> Ecrire un message</p>';
  echo '<h2>Nouveau message privé</h2><br /><br />';
?>
  <form method="post" action="postok.php?action=nouveaump" name="formulaire">
  <p>
  <label for="to">Envoyer à : </label>
  <input type="text" size="30" id="to" name="to" />
  <br />
  <label for="titre">Titre : </label>
  <input type="text" size="80" id="titre" name="titre" />
  <br /><br />
  <input type="button" id="gras" name="gras" value="Gras" onClick="javascript:bbcode('[g]', '[/g]');return(false)" />
  <input type="button" id="italic" name="italic" value="Italic" onClick="javascript:bbcode('[i]', '[/i]');return(false)" />
  <input type="button" id="souligné" name="souligné" value="Souligné" onClick="javascript:bbcode('[s]', '[/s]');return(false)" />
  <input type="button" id="lien" name="lien" value="Lien" onClick="javascript:bbcode('[url]', '[/url]');return(false)" />
  <br /><br />
  <img src="./images/smileys/heureux.gif" title="heureux" alt="heureux" onClick="javascript:smilies(':D');return(false)" />
  <img src="./images/smileys/lol.gif" title="lol" alt="lol" onClick="javascript:smilies(':lol:');return(false)" />
  <img src="./images/smileys/triste.gif" title="triste" alt="triste" onClick="javascript:smilies(':triste:');return(false)" />
  <img src="./images/smileys/cool.gif" title="cool" alt="cool" onClick="javascript:smilies(':frime:');return(false)" />
  <img src="./images/smileys/rire.gif" title="rire" alt="rire" onClick="javascript:smilies('XD');return(false)" />
  <img src="./images/smileys/confus.gif" title="confus" alt="confus" onClick="javascript:smilies(':s');return(false)" />
  <img src="./images/smileys/choc.gif" title="choc" alt="choc" onClick="javascript:smilies(':O');return(false)" />
  <img src="./images/smileys/question.gif" title="?" alt="?" onClick="javascript:smilies(':interrogation:');return(false)" />
  <img src="./images/smileys/exclamation.gif" title="!" alt="!" onClick="javascript:smilies(':exclamation:');return(false)" />
  <br />
  <textarea cols="80" rows="8" id="message" name="message"></textarea>
  <br />
  <input type="submit" name="submit" value="Envoyer" />
  <input type="reset" name="Effacer" value="Effacer" /></p>
  </form>
<?php  
  break;
  
  
  case "repondre": //On répond
  echo'<p><i>Navi</i> : <a href="./index.php">Index du forum</a> --> <a href="./messagesprives.php">Boîte aux lettres</a> --> Ecrire un message</p>';
  echo '<h2>Répondre à un message privé</h2><br /><br />';
 
  $dest = (int) $_GET['dest'];
?>
  <form method="post" action="postok.php?action=repondremp&amp;dest=<?php echo $dest ?>" name="formulaire">
  <p>
  <label for="titre">Titre : </label><input type="text" size="80" id="titre" name="titre" />
  <br /><br />
  <input type="button" id="gras" name="gras" value="Gras" onClick="javascript:bbcode('[g]', '[/g]');return(false)" />
  <input type="button" id="italic" name="italic" value="Italic" onClick="javascript:bbcode('[i]', '[/i]');return(false)" />
  <input type="button" id="souligné" name="souligné" value="Souligné" onClick="javascript:bbcode('[s]', '[/s]');return(false)" />
  <input type="button" id="lien" name="lien" value="Lien" onClick="javascript:bbcode('[url]', '[/url]');return(false)" />
  <br /><br />
  <img src="./images/smileys/heureux.gif" title="heureux" alt="heureux" onClick="javascript:smilies(':D');return(false)" />
  <img src="./images/smileys/lol.gif" title="lol" alt="lol" onClick="javascript:smilies(':lol:');return(false)" />
  <img src="./images/smileys/triste.gif" title="triste" alt="triste" onClick="javascript:smilies(':triste:');return(false)" />
  <img src="./images/smileys/cool.gif" title="cool" alt="cool" onClick="javascript:smilies(':frime:');return(false)" />
  <img src="./images/smileys/rire.gif" title="rire" alt="rire" onClick="javascript:smilies('XD');return(false)" />
  <img src="./images/smileys/confus.gif" title="confus" alt="confus" onClick="javascript:smilies(':s');return(false)" />
  <img src="./images/smileys/choc.gif" title="choc" alt="choc" onClick="javascript:smilies(':O');return(false)" />
  <img src="./images/smileys/question.gif" title="?" alt="?" onClick="javascript:smilies(':interrogation:');return(false)" />
  <img src="./images/smileys/exclamation.gif" title="!" alt="!" onClick="javascript:smilies(':exclamation:');return(false)" />
 
  <br /><br />
  <textarea cols="80" rows="8" id="message" name="message"></textarea>
  <br />
  <input type="submit" name="submit" value="Envoyer" />
  <input type="reset" name="Effacer" value="Effacer"/>
  </p></form>
<?php
  break;
  
  
  default;
     
  echo'<p><i>Navi</i> : <a href="./index.php">Index du forum</a> --> Boîte aux lettres';
  echo '<h2>Messagerie Privée</h2><br />';
 
  $query=$db->prepare('SELECT mp_lu, mp_id, mp_expediteur, mp_titre, mp_time, apat_id, apat_pseudo FROM forum_mp
  LEFT JOIN forum_apat ON forum_mp.mp_expediteur = forum_apat.apat_id
  WHERE mp_receveur = :id ORDER BY mp_id DESC');
  $query->bindValue(':id',$id,PDO::PARAM_INT);
  $query->execute();
  
  echo'<p><a href="./messagesprives.php?action=nouveau">
  <img src="./img/nouveau.jpg" alt="Nouveau" title="Nouveau message" />
  </a></p>';
    
  if ($query->rowCount()>0)
   {
?>
    <table>
     <tr>
      <th></th>
      <th class="mp_titre"><strong>Titre</strong></th>
      <th class="mp_expediteur"><strong>Expéditeur</strong></th>
      <th class="mp_time"><strong>Date</strong></th>
      <th><strong>Action</strong></th>
    </tr>
 
<?php

    while ($data = $query->fetch())
     {
      
	  echo'<tr>';

      if($data['mp_lu'] == 0)
       {
	   
        echo'<td><img src="./img/message_nonlu.jpg" alt="Non lu" /></td>';
            
	   }
      
	  else
       {
	   
        echo'<td><img src="./img/message_lu.jpg" alt="Déja lu" /></td>';
            
	   }
      
	  echo'<td id="mp_titre">
      <a href="./messagesprives.php?action=consulter&amp;id='.$data['mp_id'].'">
      '.stripslashes(htmlspecialchars($data['mp_titre'])).'</a></td>
      <td id="mp_expediteur">
      <a href="./voirprofil.php?action=consulter&amp;m='.$data['apat_id'].'">
      '.stripslashes(htmlspecialchars($data['apat_pseudo'])).'</a></td>
      <td id="mp_time">'.date('H\hi \l\e d M Y',$data['mp_time']).'</td>
      <td>
      <a href="./messagesprives.php?action=supprimer&amp;id='.$data['mp_id'].'&amp;sur=0">supprimer</a></td></tr>';
     
	 }
    
	$query->CloseCursor();
    
	echo '</table>';
 
   }
  
  else
   {
   
    echo'<p>Vous n\'avez aucun message privé pour l\'instant, cliquez
    <a href="./index.php">ici</a> pour revenir au début.</p>';
	
   }

 }
 

 
?>
  </div>
 </body>
</html>
  
  
  