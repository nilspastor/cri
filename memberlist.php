<?php

session_start();

$titre="Le cri des poissons - Liste des membres";

include("includes/identifiants.php");
include("includes/debut.php");
include("includes/menu.php");
 
$query=$db->query('SELECT COUNT(*) AS nbr FROM forum_apat');
$data = $query->fetch();
 
$total = $data['nbr'] +1;
$query->CloseCursor();
$MembreParPage = 25;
$NombreDePages = ceil($total / $MembreParPage);
echo '<p><i>Navi</i> : <a href="./index.php">Index du forum</a> --> <a href="./memberlist.php">Liste des membres</a></p>';
 
$page = (isset($_GET['page']))?intval($_GET['page']):1;
 
echo 'Page : ';

for ($i = 1 ; $i <= $NombreDePages ; $i++)
 {
  
  if ($i == $page) //On ne met pas de lien sur la page actuelle
   {
   
    echo $i;
   
   }
  
  else
   {
   
    echo '<p><a href="memberlist.php?page='.$i.'">'.$i.'</a></p>';
   
   }

 }

echo '</p>';
 
$premier = ($page - 1) * $MembreParPage;
 
echo '<h2>Liste des membres</h2><br /><br />';


$convert_order = array('apat_pseudo', 'apat_inscrit', 'apat_post', 'apat_derniere_visite');
$convert_tri = array('ASC', 'DESC');
//On récupère la valeur de s
if (isset ($_POST['s'])) $sort = $convert_order[$_POST['s']];
else $sort = $convert_order[0];
//On récupère la valeur de t
if (isset ($_POST['t'])) $tri = $convert_tri[$_POST['t']];
else $tri = $convert_tri[0];
 
?>
<form action="memberlist.php" method="post">
<p><label for="s">Trier par : </label>
 
<select name="s" id="s">
<option value="0" name="0">Pseudo</option>
<option value="1" name="1">Inscription</option>
<option value="2" name="2">Messages</option>
<option value="3" name="3">Dernière visite</option>
</select>
 
<select name="t" id="t">
<option value="0" name="0">Croissant</option>
<option value="1" name="1">Décroissant</option>
</select>
<input type="submit" value="Trier" /></p>
</form>
<?php
//Requête
 
$query = $db->prepare('SELECT apat_id, apat_pseudo, apat_inscrit, apat_post, apat_avatar, apat_derniere_visite, online_id
FROM forum_apat
LEFT JOIN forum_whosonline ON online_id = apat_id
ORDER BY '.$sort.', online_id '.$tri.'
LIMIT :premier, :membreparpage');
$query->bindValue(':premier',$premier,PDO::PARAM_INT);
$query->bindValue(':membreparpage',$MembreParPage, PDO::PARAM_INT);
$query->execute();
 
if ($query->rowCount() > 0)
 {
?>
  <table style="width: 100%;">
  <tr>
  <th class="avatar"><strong>Avatar</strong></th>
  <th class="pseudo"><strong>Pseudo</strong></th>            
  <th class="posts"><strong>Messages</strong></th>
  <th class="inscrit"><strong>Inscrit depuis le</strong></th>
  <th class="derniere_visite"><strong>Dernière visite</strong></th>                      
  <th><strong>Connecté</strong></th>            
 
  </tr>
<?php
       //On lance la boucle
        
  while ($data = $query->fetch())
   {
   
    echo '<tr><td style="text-align: center;"><img src="./img/avatars/'.$data['apat_avatar'].'" /></td>
	<td style="text-align: center;">
    <a href="./voirprofil.php?m='.$data['apat_id'].'&amp;action=consulter">
    '.stripslashes(htmlspecialchars($data['apat_pseudo'])).'</a></td>
    <td style="text-align: center;">'.$data['apat_post'].'</td>
    <td style="text-align: center;">'.date('d/m/Y',$data['apat_inscrit']).'</td>
    <td style="text-align: center;">'.date('d/m/Y',$data['apat_derniere_visite']).'</td>';
    if (empty($data['online_id'])) echo '<td style="text-align: center;">non</td>';
    else echo '<td style="text-align: center;">oui</td>';
    echo '</tr>';

   }

  $query->CloseCursor();
?>
  </table>
<?php
 }

else //S'il n'y a pas de message
 {
 
  echo'<p>Ce forum ne contient aucun membre actuellement</p>';
  
 }
?>
<br /></div>
</body></html>