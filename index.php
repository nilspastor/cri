<?php

 session_start();
 
 $titre = "Le cri des poissons - Index du forum";
 include("includes/identifiants.php");
 include("includes/debut.php");
 include("includes/menu.php");
 
 
 echo '<p style="text-align:center;margin-top: -14px"><iframe src="http://almanax.zone-bouffe.com/dofus/widget.php?size=small"
                    frameborder="0" scrolling="no" width="433" height="73"></iframe></p>';
 
 echo '<p><i>Navi</i> : <a href ="index.php">Index du forum</a></p>';

 
 $totaldesmessages = 0;
 $categorie = NULL;

 // RequÃªte pour collecter les infos du forum (modif : "membre" en "apat")
 $query=$db->prepare('SELECT cat_id, cat_nom, cat_img, forum_forum.forum_id, forum_name, forum_desc, forum_post, forum_topic, auth_view, forum_topic.topic_id, forum_topic.topic_post, post_id, post_time, post_createur, apat_pseudo, apat_id
 FROM forum_categorie
 LEFT JOIN forum_forum ON forum_categorie.cat_id = forum_forum.forum_cat_id
 LEFT JOIN forum_post ON forum_post.post_id = forum_forum.forum_last_post_id
 LEFT JOIN forum_topic ON forum_topic.topic_id = forum_post.topic_id
 LEFT JOIN forum_apat ON forum_apat.apat_id = forum_post.post_createur
 WHERE auth_view <= :lvl
 ORDER BY cat_ordre, forum_ordre DESC');
 $query->bindValue(':lvl',$lvl,PDO::PARAM_INT);
 $query->execute();

?>

 <table>

<?php

 while ($data = $query->fetch())
 {

  if ($categorie != $data['cat_id'])
   {

    $categorie = $data['cat_id'];

?>

 <tr>
 <th></th>
 <th class="titre"><strong>
<?php 

 echo '<img src="img/'.$data['cat_img'].'" alt="pas d img" style="display: block; margin: auto;" />';

?>
 </strong></th>            
 <th class="nombremessages"><img src="img/sujets.jpg" alt="sujets" style="display: block; margin: auto;" /></th>      
 <th class="nombresujets"><img src="img/messages.jpg" alt="messages" style="display: block; margin: auto;" /></th>      
 <th class="derniermessage"><img src="img/lastmsg.jpg" alt="derniermessage" style="display: block; margin: auto;" /></th>  
 </tr>

<?php
    
    }
	
   if (verif_auth($data['auth_view']))
    {

     echo '<tr><td style="background-color: rgb(128,0,0);"><img src="img/forum.jpg" alt="forum" style="display: block; margin: auto;" /></td>
     <td class="titre"><strong>
     <a href="voirforum.php?f='.$data['forum_id'].'">
     '.stripslashes(htmlspecialchars($data['forum_name'])).'</a></strong>
     <br />'.nl2br(stripslashes(htmlspecialchars($data['forum_desc']))).'</td>
     <td class="nombresujets">'.$data['forum_topic'].'</td>
     <td class="nombremessages">'.$data['forum_post'].'</td>';
 
     if (!empty($data['forum_post']))
      {

       $nombreDeMessagesParPage = 15;
       $nbr_post = $data['topic_post'] +1;
       $page = ceil($nbr_post / $nombreDeMessagesParPage);
          
       echo'<td class="derniermessage">
       '.date('H\hi \l\e d/M/Y',$data['post_time']).'<br />
       <a href="voirprofil.php?m='.stripslashes(htmlspecialchars($data['apat_id'])).'&amp;action=consulter">'.$data['apat_pseudo'].'</a>
       <a href="voirtopic.php?t='.$data['topic_id'].'&amp;page='.$page.'#p_'.$data['post_id'].'">
	   <img src="img/go.gif" alt="go" /></a></td></tr>';
 
      }
	
     else
      {
	
       echo '<td class="derniermessage">Pas de message</td></tr>';
	 
      }
	
	}
 
  }

 $query->CloseCursor();
 echo '</table><br /></div>';
 
 include("includes/footer.php");
 
?>

 </div>
 </body>
</html>