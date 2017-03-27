<?php

session_start();

$titre="Le cri des poissons - Poster";

$balises = true;

include("includes/identifiants.php");
include("includes/debut.php");
include("includes/menu.php");
include("includes/bbcode.php");


$action = (isset($_GET['action']))?htmlspecialchars($_GET['action']):'';
 
if ($id==0) erreur(ERR_IS_CO);
 
if (isset($_GET['f']))
 {
 
  $forum = (int) $_GET['f'];
  $query= $db->prepare('SELECT forum_name, auth_view, auth_post, auth_topic, auth_annonce, auth_modo
  FROM forum_forum WHERE forum_id =:forum');
  $query->bindValue(':forum',$forum,PDO::PARAM_INT);
  $query->execute();
  $data=$query->fetch();
  echo '<p><i>Fil d\'Ariane</i> : <a href="./index.php">Index du forum</a> -->
  <a href="./voirforum.php?f='.$data['forum_id'].'">'.stripslashes(htmlspecialchars($data['forum_name'])).'</a>
  --> Nouveau topic</p>';
 
 }
  
elseif (isset($_GET['t']))
 {
    
  $topic = (int) $_GET['t'];
  $query=$db->prepare('SELECT topic_titre, forum_topic.forum_id,
  forum_name, auth_view, auth_post, auth_topic, auth_annonce, auth_modo
  FROM forum_topic
  LEFT JOIN forum_forum ON forum_forum.forum_id = forum_topic.forum_id
  WHERE topic_id =:topic');
  $query->bindValue(':topic',$topic,PDO::PARAM_INT);
  $query->execute();
  $data=$query->fetch();
  $forum = $data['forum_id']; 
 
  echo '<p><i>Fil d\'Ariane</i> : <a href="./index.php">Index du forum</a> -->
  <a href="./voirforum.php?f='.$data['forum_id'].'">'.stripslashes(htmlspecialchars($data['forum_name'])).'</a>
  --> <a href="./voirtopic.php?t='.$topic.'">'.stripslashes(htmlspecialchars($data['topic_titre'])).'</a>
  --> Répondre</p>';

 }
  
elseif (isset ($_GET['p']))
 {
 
  $post = (int) $_GET['p'];
  $query=$db->prepare('SELECT post_createur, forum_post.topic_id, topic_titre, forum_topic.forum_id,
  forum_name, auth_view, auth_post, auth_topic, auth_annonce, auth_modo
  FROM forum_post
  LEFT JOIN forum_topic ON forum_topic.topic_id = forum_post.topic_id
  LEFT JOIN forum_forum ON forum_forum.forum_id = forum_topic.forum_id
  WHERE forum_post.post_id =:post');
  $query->bindValue(':post',$post,PDO::PARAM_INT);
  $query->execute();
  $data=$query->fetch();
 
  $topic = $data['topic_id'];
  $forum = $data['forum_id'];
  
  echo '<p><i>Fil d\'Ariane</i> : <a href="./index.php">Index du forum</a> -->
  <a href="./voirforum.php?f='.$data['forum_id'].'">'.stripslashes(htmlspecialchars($data['forum_name'])).'</a>
  --> <a href="./voirtopic.php?t='.$topic.'">'.stripslashes(htmlspecialchars($data['topic_titre'])).'</a>
  --> Modérer un message</p>';

 }
 
if (!verif_auth($data['auth_view']))
 {
  
  erreur(ERR_AUTH_VIEW);
  
 }
 
$query->CloseCursor();  

// Gros switch sa mère
switch($action)
 {
// REPONDRE
  case "repondre":
  
  if (verif_auth($data['auth_post']))
   {
   

    $topic = (int) $_GET['t'];
   
	$query=$db->prepare('SELECT post_id , post_createur , post_texte , post_time ,
    apat_id, apat_pseudo, apat_avatar FROM forum_post
    LEFT JOIN forum_apat ON forum_apat.apat_id = forum_post.post_createur
    WHERE topic_id =:topic
    ORDER BY post_id
    LIMIT 0, 20');
    $query->bindValue(':topic',$topic,PDO::PARAM_INT);
    $query->execute();
	
?>
  
    <div style="overflow:auto; height:380px"><table style="width: 100%;">
    <tr>
    <th class="vt_auteur"><img src="./img/auteur.jpg" alt="auteur" style="display: block; margin: auto;" /></th>            
    <th class="vt_mess"><img src="./img/messages.jpg" alt="messages" style="display: block; margin: auto;" /></th>      
    </tr>
	
<?php
  
    while ($data = $query->fetch())
     {
    
	  echo '<tr><td style="background-color: rgb(211,211,211); text-align: center; vertical-align: bottom;"><strong><a href="./voirprofil.php?m='.$data['apat_id'].'&amp;action=consulter" >'.stripslashes(htmlspecialchars($data['apat_pseudo'])).'</a></strong></td>';

      if ($id == $data['post_createur'] OR $lvl >= 3)
       {
	 
        echo'<td style="background-color: rgb(165,42,42);" id=p_'.$data['post_id'].'>Posté à '.date('H\hi \l\e d M y',$data['post_time']).' </td></tr>';

       }

	  else
       {
	 
        echo'<td style="background-color: rgb(165,42,42);">Posté à '.date('H\hi \l\e d M y',$data['post_time']).'</td></tr>';

	   }
        
      echo'<tr><td style="background-color: rgb(165,42,42);"><img src="./img/avatars/'.$data['apat_avatar'].'" alt="Avatar" style="display: block; margin: auto;" /></td>';
                
      echo'<td>'.code(nl2br(stripslashes(htmlspecialchars($data['post_texte'])))).'</td></tr>';
         
     }

    $query->CloseCursor();
	
?>

    </table></div>

    <h2>Poster une réponse</h2>
  
    <form method="post" action="postok.php?action=repondre&amp;t=<?php echo $topic ?>" name="formulaire">
  
    <fieldset><legend>Mise en forme</legend>
    <input type="button" id="gras" name="gras" value="Gras" onClick="javascript:bbcode('[g]', '[/g]');return(false)" />
    <input type="button" id="italic" name="italic" value="Italic" onClick="javascript:bbcode('[i]', '[/i]');return(false)" />
    <input type="button" id="souligné" name="souligné" value="Souligné" onClick="javascript:bbcode('[s]', '[/s]');return(false)" />
    <input type="button" id="lien" name="lien" value="Lien" onClick="javascript:bbcode('[url]', '[/url]');return(false)" />
    <input type="button" id="centre" name="centre" value="Centre" onClick="javascript:bbcode('[center]', '[/center]');return(false)" />
    <input type="button" id="image" name="image" value="Image" onClick="javascript:bbcode('[img]', '[/img]');return(false)" />
  
    <!-- Smileys retirés -->
  
    </fieldset>
  
    <fieldset><legend>Message</legend><textarea cols="80" rows="8" id="message" name="message"></textarea></fieldset>
  
    <input type="submit" name="submit" value="Poster" />
    <input type="reset" name = "Effacer" value = "Effacer"/>
    </p></form>
<?php

   }

  break;
  
  case "nouveautopic":
  
  if (verif_auth($data['auth_topic']))
   {
?>
  
    <h2>Nouveau topic</h2>
    <form method="post" action="postok.php?action=nouveautopic&amp;f=<?php echo $forum ?>" name="formulaire">
  
    <fieldset><legend>Titre</legend>
    <input type="text" size="80" id="titre" name="titre" /></fieldset>
  
    <fieldset><legend>Mise en forme</legend>
    <input type="button" id="gras" name="gras" value="Gras" onClick="javascript:bbcode('[g]', '[/g]');return(false)" />
    <input type="button" id="italic" name="italic" value="Italic" onClick="javascript:bbcode('[i]', '[/i]');return(false)" />
    <input type="button" id="souligné" name="souligné" value="Souligné" onClick="javascript:bbcode('[s]', '[/s]');return(false)" />
    <input type="button" id="lien" name="lien" value="Lien" onClick="javascript:bbcode('[url]', '[/url]');return(false)" />
    <input type="button" id="centre" name="centre" value="Centre" onClick="javascript:bbcode('[center]', '[/center]');return(false)" />
    <input type="button" id="image" name="image" value="Image" onClick="javascript:bbcode('[img]', '[/img]');return(false)" />
  
    </fieldset>
  
    <fieldset><legend>Message</legend>
    <textarea cols="80" rows="8" id="message" name="message"></textarea><br />
    <label><input type="radio" name="mess" value="Annonce" />Annonce</label>
    <label><input type="radio" name="mess" value="Message" checked="checked" />Topic</label>
    </fieldset>
    <p>
    <input type="submit" name="submit" value="Poster" />
    <input type="reset" name = "Effacer" value = "Effacer" /></p>
    </form>
<?php

   }

  break;
  
  case "edit": // EDITER UN POST
  
  $post = (int) $_GET['p'];
  
  echo '<h2>Edition</h2>';
  
  $query=$db->prepare('SELECT post_createur, post_texte, auth_modo FROM forum_post
  LEFT JOIN forum_forum ON forum_post.post_forum_id = forum_forum.forum_id
  WHERE post_id=:post');
  $query->bindValue(':post',$post,PDO::PARAM_INT);
  $query->execute();
  $data=$query->fetch();
  
  $text_edit = $data['post_texte'];
  
  if (!verif_auth($data['auth_modo']) && $data['post_createur'] != $id)
   {
   
    erreur(ERR_AUTH_EDIT); // Constants.php modifié en conséquence
	
   }
   
  else
   {
?>
    <form method="post" action="postok.php?action=edit&amp;p=<?php echo $post ?>" name="formulaire">
    <fieldset><legend>Mise en forme</legend>
    <input type="button" id="gras" name="gras" value="Gras" onClick="javascript:bbcode('[g]', '[/g]');return(false)" />
    <input type="button" id="italic" name="italic" value="Italic" onClick="javascript:bbcode('[i]', '[/i]');return(false)" />
    <input type="button" id="souligné" name="souligné" value="Souligné" onClick="javascript:bbcode('[s]', '[/s]');return(false)"/>
    <input type="button" id="lien" name="lien" value="Lien" onClick="javascript:bbcode('[url]', '[/url]');return(false)" />
	<input type="button" id="centre" name="centre" value="Centre" onClick="javascript:bbcode('[center]', '[/center]');return(false)" />
	<input type="button" id="image" name="image" value="Image" onClick="javascript:bbcode('[img]', '[/img]');return(false)" />
    </fieldset>
  
    <fieldset><legend>Message</legend><textarea cols="80" rows="8" id="message" name="message"><?php echo $text_edit ?>
    </textarea>
    </fieldset>
    <p>
    <input type="submit" name="submit" value="Editer !" />
    <input type="reset" name = "Effacer" value = "Effacer"/></p>
    </form>
	
<?php

   }
   
  break;
  
  case "delete": // SUPPRIMER UN POST
  
  $post = (int) $_GET['p'];
  
  echo '<h2>Suppression d\'un message</h2>';
  
  $query=$db->prepare('SELECT post_createur, auth_modo
  FROM forum_post
  LEFT JOIN forum_forum ON forum_post.post_forum_id = forum_forum.forum_id
  WHERE post_id= :post');
  $query->bindValue(':post',$post,PDO::PARAM_INT);
  $query->execute();
  $data = $query->fetch();
  
  if (!verif_auth($data['auth_modo']) && $data['post_createur'] != $id)
   {
   
    erreur(ERR_AUTH_DELETE);
	
   }
   
  else
   {
   
    echo '<p>Supprimer ce message définitivement ?</p>';
	echo '<p><a href="./postok.php?action=delete&amp;p='.$post.'">Oui !</a> ou <a href="./index.php">Euh... Nan.</a></p>';
    
   }
   
  $query->CloseCursor();
  
  break;
  
  // ...


default:
echo'<p>Cette action est impossible</p>';
} //Fin du switch



?>

  </div>
  
<?php include("includes/footer.php"); ?>
  
 </body>
</html>