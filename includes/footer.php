<?php

 echo '<div id="footer">';

 $totaldesmessages = $db->query('SELECT COUNT(*) FROM forum_post')->fetchColumn();
 $query->CloseCursor();
 
 $TotalDesMembres = $db->query('SELECT COUNT(*) FROM forum_apat')->fetchColumn();
 $query->CloseCursor();  
 $query = $db->query('SELECT apat_pseudo, apat_id FROM forum_apat ORDER BY apat_id DESC LIMIT 0, 1');
 $data = $query->fetch();
 $derniermembre = stripslashes(htmlspecialchars($data['apat_pseudo']));
 
 echo '<p style="text-align:center;"><img src="./img/footer_wings.jpg" alt="ImageDeco" /><br />
 Le flood a sevi <strong>'.$totaldesmessages.'</strong> fois.<br />';
 echo 'Pour un forum qui compte <strong>'.$TotalDesMembres.'</strong> membres.<br />';
 echo 'Souhaitons la bienvenue à <a href="./voirprofil.php?m='.$data['apat_id'].'&amp;action=consulter">'.$derniermembre.'</a>, notre dernier(ère) arrivant(e) !';

 $query->CloseCursor();

 
 $count_online = 0;
 
 // Décompte visiteurs
 $count_visiteurs=$db->query('SELECT COUNT(*) AS nbr_visiteurs FROM forum_whosonline WHERE online_id = 0')->fetchColumn();
 $query->CloseCursor();
 
 //Décompte membres
 $texte_a_afficher = "<br />Membres connectés : ";
 $time_max = time() - (60 * 5);
 $query=$db->prepare('SELECT apat_id, apat_pseudo
 FROM forum_whosonline
 LEFT JOIN forum_apat ON online_id = apat_id
 WHERE online_time > :timemax AND online_id <> 0');
 $query->bindValue(':timemax',$time_max, PDO::PARAM_INT);
 $query->execute();
 $count_membres=0;
 
 while ($data = $query->fetch())
 {
  
  $count_membres ++;
  $texte_a_afficher .= '<a href="./voirprofil.php?m='.$data['apat_id'].'&amp;action=consulter">
  '.stripslashes(htmlspecialchars($data['apat_pseudo'])).'</a> ,';

 }
 
 $texte_a_afficher = substr($texte_a_afficher, 0, -1);
 $count_online = $count_visiteurs + $count_membres;
 
 
 echo '<br />Il y a '.$count_online.' utilisateur'; if ($count_online > 1) { echo 's'; } echo ' en ligne (dont '.$count_membres.' membre'; if ($count_membres > 1) { echo 's'; } echo ' et '.$count_visiteurs.' invité'; if ($count_visiteurs > 1) { echo 's'; } echo ')';
 
 if ($count_membres > 0)
  {
  
   echo $texte_a_afficher;
 
  }
  
 $query->CloseCursor();
 
 echo '<br />Les images sont la propriété d\'Ankama.</p>';
 
 
?>