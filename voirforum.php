<?php

session_start();

$titre="Le cri des poissons - Consulter un forum";

include("includes/identifiants.php");
include("includes/debut.php");
include("includes/menu.php");
 
$forum = (int) $_GET['f'];
 
$query=$db->prepare('SELECT forum_name, forum_topic, auth_view, auth_topic FROM forum_forum WHERE forum_id = :forum');
$query->bindValue(':forum',$forum,PDO::PARAM_INT);
$query->execute();
$data=$query->fetch();

if (!verif_auth($data['auth_view']))
 {
 
  erreur(ERR_AUTH_VIEW);
  
 }
 
$totalDesMessages = $data['forum_topic'] + 1;
$nombreDeMessagesParPage = 25;
$nombreDePages = ceil($totalDesMessages / $nombreDeMessagesParPage);


echo '<p><i>Navi</i> : <a href="./index.php">Index du forum</a> -->
<a href="./voirforum.php?f='.$forum.'">'.stripslashes(htmlspecialchars($data['forum_name'])).'</a>';
echo '<h2>'.stripslashes(htmlspecialchars($data['forum_name'])).'</h2><br />';
 
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
   
    echo '<a href="voirforum.php?f='.$forum.'&amp;page='.$i.'">'.$i.'</a>';
    
   }

 }

echo '</p>';
 
$premierMessageAafficher = ($page - 1) * $nombreDeMessagesParPage;

if (verif_auth($data['auth_topic']))
 { 

  echo '<a href="./poster.php?action=nouveautopic&amp;f='.$forum.'"><img src="./img/nouveau.jpg" alt="Nouveau topic" title="Poster un nouveau topic" /></a>';

 }
 
else
 {
 
  echo '<img src="./img/verr_nouveau.jpg" alt="Icone_verrou" title="Connexion requise pour poster" />';
  
 }

$query->CloseCursor();


// Reqûete de fou furieux
$query=$db->prepare('SELECT forum_topic.topic_id, topic_titre, topic_createur, topic_vu, topic_post, topic_time, topic_last_post,
Mb.apat_pseudo AS apat_pseudo_createur, post_createur, post_time, Ma.apat_pseudo AS apat_pseudo_last_posteur, post_id FROM forum_topic
LEFT JOIN forum_apat Mb ON Mb.apat_id = forum_topic.topic_createur
LEFT JOIN forum_post ON forum_topic.topic_last_post = forum_post.post_id
LEFT JOIN forum_apat Ma ON Ma.apat_id = forum_post.post_createur   
WHERE topic_genre = "Annonce" AND forum_topic.forum_id = :forum
ORDER BY topic_last_post DESC');
$query->bindValue(':forum',$forum,PDO::PARAM_INT);
$query->execute();


// On lance notre tableau seulement s'il y a des requêtes !
if ($query->rowCount()>0)
 {
?>
  <table>  
  <tr>
  <th></th>
  <th class="titre"><img src="./img/titre.jpg" alt="titre" style="display: block; margin: auto;" /></th>            
  <th class="nombremessages"><img src="./img/sujets.jpg" alt="sujets" style="display: block; margin: auto;" /></th>
  <th class="nombrevu"><img src="./img/vus.jpg" alt="vus" style="display: block; margin: auto;" /></th>
  <th class="auteur"><img src="./img/auteur.jpg" alt="auteur" style="display: block; margin: auto;" /></th>                      
  <th class="derniermessage"><img src="./img/lastmsg.jpg" alt="derniermessage" style="display: block; margin: auto;" /></th>
  </tr>  
<?php
// Début de la boucle
  while ($data=$query->fetch())
   {

	echo '<tr><td style="background-color: rgb(128,0,0);"><img src="./img/annonce.jpg" alt="Annonce" style="display: block; margin: auto;"/></td><td id="titre"><strong>Annonce : </strong><strong><a href="./voirtopic.php?t='.$data['topic_id'].'" title="Topic commencé à '.date('H\hi \l\e d M,y',$data['topic_time']).'">'.stripslashes(htmlspecialchars($data['topic_titre'])).'</a></strong></td>
	
    <td class="nombremessages">'.$data['topic_post'].'</td>
    
	<td class="nombrevu">'.$data['topic_vu'].'</td>
	
    <td class="auteur"><a href="./voirprofil.php?m='.$data['topic_createur'].'&amp;action=consulter">'.stripslashes(htmlspecialchars($data['apat_pseudo_createur'])).'</a></td>';
 
    $nombreDeMessagesParPage = 15;
    $nbr_post = $data['topic_post'] +1;
    $page = ceil($nbr_post / $nombreDeMessagesParPage);
 
    echo '<td class="derniermessage">Par <a href="./voirprofil.php?m='.$data['post_createur'].'&amp;action=consulter">'.stripslashes(htmlspecialchars($data['apat_pseudo_last_posteur'])).'</a><br />
    A <a href="./voirtopic.php?t='.$data['topic_id'].'&amp;page='.$page.'#p_'.$data['post_id'].'">'.date('H\hi \l\e d M y',$data['post_time']).'</a></td></tr>';

   }
?>
  </table>
<?php
 }

$query->CloseCursor();


// On prend tout ce qu'on a sur les topics normaux du forum
$query=$db->prepare('SELECT forum_topic.topic_id, topic_titre, topic_createur, topic_vu, topic_post, topic_time, topic_last_post,
Mb.apat_pseudo AS apat_pseudo_createur, post_id, post_createur, post_time, Ma.apat_pseudo AS apat_pseudo_last_posteur FROM forum_topic
LEFT JOIN forum_apat Mb ON Mb.apat_id = forum_topic.topic_createur
LEFT JOIN forum_post ON forum_topic.topic_last_post = forum_post.post_id
LEFT JOIN forum_apat Ma ON Ma.apat_id = forum_post.post_createur  
WHERE topic_genre <> "Annonce" AND forum_topic.forum_id = :forum
ORDER BY topic_last_post DESC
LIMIT :premier ,:nombre');
$query->bindValue(':forum',$forum,PDO::PARAM_INT);
$query->bindValue(':premier',(int) $premierMessageAafficher,PDO::PARAM_INT);
$query->bindValue(':nombre',(int) $nombreDeMessagesParPage,PDO::PARAM_INT);
$query->execute();
 
if ($query->rowCount()>0)
 {
?>
  <table>
  <tr>
  <th></th>
  <th class="titre"><img src="./img/titre.jpg" alt="titre" style="display: block; margin: auto;" /></th>            
  <th class="nombremessages"><img src="./img/sujets.jpg" alt="sujets" style="display: block; margin: auto;" /></th>
  <th class="nombrevu"><img src="./img/vus.jpg" alt="vus" style="display: block; margin: auto;" /></th>
  <th class="auteur"><img src="./img/auteur.jpg" alt="auteur" style="display: block; margin: auto;" /></th>                      
  <th class="derniermessage"><img src="./img/lastmsg.jpg" alt="derniermessage" style="display: block; margin: auto;" /></th>
  </tr>
<?php
// Let's boucle !

  while ($data = $query->fetch())
   {
// Echo de fou is back !
    echo'<tr><td style="background-color: rgb(128,0,0);"><img src="./img/message.jpg" alt="Message" style="display: block; margin: auto;" /></td><td class="titre"><strong><a href="./voirtopic.php?t='.$data['topic_id'].'" title="Topic commencé à '.date('H\hi \l\e d M,y',$data['topic_time']).'">'.stripslashes(htmlspecialchars($data['topic_titre'])).'</a></strong></td>
 
    <td class="nombremessages">'.$data['topic_post'].'</td>
 
    <td class="nombrevu">'.$data['topic_vu'].'</td>
 
    <td class="auteur"><a href="./voirprofil.php?m='.$data['topic_createur'].'&amp;action=consulter">'.stripslashes(htmlspecialchars($data['apat_pseudo_createur'])).'</a></td>';
 
//Selection dernier message
    $nombreDeMessagesParPage = 15;
    $nbr_post = $data['topic_post'] +1;
    $page = ceil($nbr_post / $nombreDeMessagesParPage);
 
    echo '<td class="derniermessage">Par <a href="./voirprofil.php?m='.$data['post_createur'].'&amp;action=consulter">'.stripslashes(htmlspecialchars($data['apat_pseudo_last_posteur'])).'</a><br />
    A <a href="./voirtopic.php?t='.$data['topic_id'].'&amp;page='.$page.'#p_'.$data['post_id'].'">'.date('H\hi \l\e d M y',$data['post_time']).'</a></td></tr>';
 
   }
?>
  </table>
<?php
 }

 else
  {
  
   echo'<p>Ce forum ne contient aucun topic actuellement !</p>';
  
  }

$query->CloseCursor();

?>
  <br /></div>
  
<?php include("includes/footer.php"); ?>
  
 </body>
</html>