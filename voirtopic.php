<?php

session_start();

$titre="Le cri des poissons - Consulter un topic";

include("includes/identifiants.php");
include("includes/debut.php");
include("includes/menu.php");
include("includes/bbcode.php");
  
$topic = (int) $_GET['t'];



$query=$db->prepare('SELECT topic_titre, topic_post, forum_topic.forum_id, topic_last_post,
forum_name, auth_view, auth_topic, auth_post
FROM forum_topic
LEFT JOIN forum_forum ON forum_topic.forum_id = forum_forum.forum_id
WHERE topic_id = :topic');
$query->bindValue(':topic',$topic,PDO::PARAM_INT);
$query->execute();
$data=$query->fetch();
$forum=$data['forum_id'];
$totalDesMessages = $data['topic_post'] + 1;
$nombreDeMessagesParPage = 15;
$nombreDePages = ceil($totalDesMessages / $nombreDeMessagesParPage);

if (!verif_auth($data['auth_view']))
 {
 
  erreur(ERR_AUTH_VIEW);
  
 }


echo '<p><i>Navi</i> : <a href="./index.php">Index du forum</a> -->
<a href="./voirforum.php?f='.$forum.'">'.stripslashes(htmlspecialchars($data['forum_name'])).'</a>
 --> <a href="./voirtopic.php?t='.$topic.'">'.stripslashes(htmlspecialchars($data['topic_titre'])).'</a>';
echo '<h2>'.stripslashes(htmlspecialchars($data['topic_titre'])).'</h2><br />';


$page = (isset($_GET['page']))?intval($_GET['page']):1;
 
echo '<p>Page : ';

for ($i = 1 ; $i <= $nombreDePages ; $i++)
 {

  if ($i == $page)
   {

    echo $i;
    
   }
   
  else
    {

     echo '<a href="voirtopic.php?t='.$topic.'&page='.$i.'">' . $i . '</a>';
    
	}
	
 }

 
echo '</p>';
  
$premierMessageAafficher = ($page - 1) * $nombreDeMessagesParPage;


if (verif_auth($data['auth_topic']))
 {
 
  echo '<a href="./poster.php?action=nouveautopic&amp;f='.$data['forum_id'].'">
  <img src="./img/nouveau.jpg" alt="Nouveau topic" title="Poster un nouveau topic" /></a> ';

 }
 
else
 {
 
  echo '<img src="./img/verr_nouveau.jpg" alt="Icone_verrou_nouveau" title="Connexion requise pour poster" /> ';
  
 }

if (verif_auth($data['auth_post']))
 {
 
  echo '<a href="./poster.php?action=repondre&amp;t='.$topic.'"><img src="./img/repondre.jpg" alt="Répondre" title="Répondre à ce topic" /></a>';

 }
 
else
 {
 
  echo '<img src="./img/verr_repondre.jpg" alt="Icone_verrou_repondre" title="Connexion requise pour répondre" />';
  
 }

  
$query->CloseCursor(); 


$query=$db->prepare('SELECT post_id , post_createur , post_texte , post_time ,
apat_id, apat_pseudo, apat_inscrit, apat_avatar, apat_post FROM forum_post
LEFT JOIN forum_apat ON forum_apat.apat_id = forum_post.post_createur
WHERE topic_id =:topic
ORDER BY post_id
LIMIT :premier, :nombre');
$query->bindValue(':topic',$topic,PDO::PARAM_INT);
$query->bindValue(':premier',(int) $premierMessageAafficher,PDO::PARAM_INT);
$query->bindValue(':nombre',(int) $nombreDeMessagesParPage,PDO::PARAM_INT);
$query->execute();


if ($query->rowCount()<1)
 {

  echo '<p>Il n y a aucun post sur ce topic, vérifiez l url et reessayez !</p>';

 }
 
else
 {
?>
  <table style="width: 100%;margin-bottom: 4px">
  <tr>
  <th class="vt_auteur"><img src="./img/auteur.jpg" alt="auteur" style="display: block; margin: auto;" /></th>            
  <th class="vt_mess"><img src="./img/messages.jpg" alt="messages" style="display: block; margin: auto;" /></th>      
  </tr>
<?php
  while ($data = $query->fetch())
   {
   
   
    echo '<tr><td style="text-align: center; vertical-align: bottom;"><strong><a href="./voirprofil.php?m='.$data['apat_id'].'&amp;action=consulter" >'.stripslashes(htmlspecialchars($data['apat_pseudo'])).'</a></strong></td>';

         /* Si on est l'auteur du message, on affiche des liens pour
         Modérer celui-ci.
         Les modérateurs pourront aussi le faire, il faudra donc revenir sur
         ce code un peu plus tard ! */    

    if ($id == $data['post_createur'] OR $lvl >= 3)
     {
	 
      echo'<td style="background-color: rgb(165,42,42);" id=p_'.$data['post_id'].'>Posté à '.date('H\hi \l\e d M y',$data['post_time']).' <a href="./poster.php?p='.$data['post_id'].'&amp;action=delete"><img src="./img/supprimer.jpg" alt="Supprimer" title="Supprimer ce message" style="vertical-align: bottom;" /></a> <a href="./poster.php?p='.$data['post_id'].'&amp;action=edit"><img src="./img/editer.jpg" alt="Editer" title="Editer ce message" style="vertical-align: bottom;" /></a></td></tr>';

     }

	else
     {
	 
      echo'<td style="background-color: rgb(165,42,42);">Posté à '.date('H\hi \l\e d M y',$data['post_time']).'</td></tr>';

	 }
        
//Détails sur le membre qui a posté
    echo'<tr><td style="background-color: rgb(165,42,42);"><img src="./img/avatars/'.$data['apat_avatar'].'" alt="Avatar" style="display: block; margin: auto; margin-top: 5px; margin-bottom: 10px" />Membre inscrit le '.date('d/m/Y',$data['apat_inscrit']).'<br />Messages : '.$data['apat_post'].'<br /></td>';
                
//Message
    echo'<td>'.code(nl2br(stripslashes(htmlspecialchars($data['post_texte'])))).'</td></tr>';
         
   } //Fin de la boucle ! \o/

$query->CloseCursor();
?>
</table>
<?php



if (verif_auth($data['auth_topic']))
 {
 
  echo '<a href="./poster.php?action=nouveautopic&amp;f='.$forum.'">
  <img src="./img/nouveau.jpg" alt="Nouveau topic" title="Poster un nouveau topic" /></a> ';

 }
 
else
 {
 
  echo '<img src="./img/verr_nouveau.jpg" alt="Icone_verrou_nouveau" title="Connexion requise pour poster" /> ';
  
 }

if (verif_auth($data['auth_post']))
 {
 
  echo '<a href="./poster.php?action=repondre&amp;t='.$topic.'"><img src="./img/repondre.jpg" alt="Répondre" title="Répondre à ce topic" /></a>';

 }
 
else
 {
 
  echo '<img src="./img/verr_repondre.jpg" alt="Icone_verrou_repondre" title="Connexion requise pour répondre" />';
  
 }



echo '<p>Page : ';

for ($i = 1 ; $i <= $nombreDePages ; $i++)
 {

  if ($i == $page) //On affiche pas la page actuelle en lien
   {

    echo $i;

   }

  else
   {

    echo '<a href="voirtopic.php?t='.$topic.'&amp;page='.$i.'">' . $i . '</a> ';

   }

 }
 
 
$query = $db->prepare('SELECT topic_titre, topic_post, forum_topic.forum_id, topic_last_post,
forum_name, auth_view, auth_topic, auth_post, topic_locked, auth_modo
FROM forum_topic
LEFT JOIN forum_forum ON forum_topic.forum_id = forum_forum.forum_id
WHERE topic_id = :topic');
$query->bindValue(':topic',$topic,PDO::PARAM_INT);
$query->execute();
$data=$query->fetch();

if (verif_auth($data['auth_modo']))
 {
 
  if ($data['topic_locked'] == 1) // Topic verrouillé !
   {
    
	echo' [ <a href="./postok.php?action=unlock&t='.$topic.'">
    <img src="./images/unlock.jpg" alt="Déverrouiller ce sujet" title="Déverrouiller ce sujet" /></a> ]';
    
	$query->CloseCursor();
   
   }
  
  else //Sinon le topic est déverrouillé !
   {
    
	echo' [ <a href="./postok.php?action=lock&amp;t='.$topic.'">
    <img src="./images/lock.jpg" alt="Verrouiller ce sujet" title="Verrouiller ce sujet" /></a> ]';
    
	$query->CloseCursor();
	
   }
   
  $query=$db->prepare('SELECT forum_id, forum_name FROM forum_forum WHERE forum_id <> :forum');
  $query->bindValue(':forum',$forum,PDO::PARAM_INT);
  $query->execute();
  
  echo '<form method="post" action=postok.php?action=deplacer&amp;t='.$topic.'>
  <select name="dest">';
  
  while($data=$query->fetch())
   {
   
    echo'<option value='.$data['forum_id'].' id='.$data['forum_id'].'>'.$data['forum_name'].'</option>';
	
   }
   
  $query->CloseCursor();
  
  echo '</select><input type="hidden" name="from" value='.$forum.'><input type="submit" name="submit" value="Déplacer le topic" /></form>';
  
 }
 


echo'</p>';
        
//On ajoute 1 au nombre de visites de ce topic
$query=$db->prepare('UPDATE forum_topic SET topic_vu = topic_vu + 1 WHERE topic_id = :topic');
$query->bindValue(':topic',$topic,PDO::PARAM_INT);
$query->execute();
$query->CloseCursor();
 
 } //Fin du if qui vérifiait si le topic contenait au moins un message
 
?>          
  </div>
  
<?php include("includes/footer.php"); ?>
  
 </body>
</html>