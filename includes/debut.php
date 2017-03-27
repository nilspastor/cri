<!DOCTYPE html>
<html>
 <head>
 
<?php

 echo (!empty($titre))?'<title>'.$titre.'</title>':'<title>Les Apatrides Salutaires - Forum</title>';

?>

  <meta http-equiv="Content-type" content="text/html; charset=utf-8" /> 
  <link rel="stylesheet" media="screen" type="text/css" title="Design" href="./css/sabertooth.css" />
  
  
<?php
$balises=(isset($balises))?$balises:0;
if($balises)
{
?>  
  
  <script>
function bbcode(bbdebut, bbfin)
{
var input = window.document.formulaire.message;
input.focus();
if(typeof document.selection != 'undefined')
{
var range = document.selection.createRange();
var insText = range.text;
range.text = bbdebut + insText + bbfin;
range = document.selection.createRange();
if (insText.length == 0)
{
range.move('character', -bbfin.length);
}
else
{
range.moveStart('character', bbdebut.length + insText.length + bbfin.length);
}
range.select();
}
else if(typeof input.selectionStart != 'undefined')
{
var start = input.selectionStart;
var end = input.selectionEnd;
var insText = input.value.substring(start, end);
input.value = input.value.substr(0, start) + bbdebut + insText + bbfin + input.value.substr(end);
var pos;
if (insText.length == 0)
{
pos = start + bbdebut.length;
}
else
{
pos = start + bbdebut.length + insText.length + bbfin.length;
}
input.selectionStart = pos;
input.selectionEnd = pos;
}
  
else
{
var pos;
var re = new RegExp('^[0-9]{0,3}$');
while(!re.test(pos))
{
pos = prompt("insertion (0.." + input.value.length + "):", "0");
}
if(pos > input.value.length)
{
pos = input.value.length;
}
var insText = prompt("Veuillez taper le texte");
input.value = input.value.substr(0, pos) + bbdebut + insText + bbfin + input.value.substr(pos);
}
}
function smilies(img)
{
window.document.formulaire.message.value += '' + img + '';
}
</script>

<?php
}
?>
  
  </head>

<?php
 
 $lvl=(isset($_SESSION['level']))?(int) $_SESSION['level']:1;
 $id=(isset($_SESSION['id']))?(int) $_SESSION['id']:0;
 $pseudo=(isset($_SESSION['pseudo']))?$_SESSION['pseudo']:'';
 
 include("./includes/functions.php");
 include("./includes/constants.php");
 
 
 $ip = ip2long($_SERVER['REMOTE_ADDR']);
 
 $query=$db->prepare('INSERT INTO forum_whosonline VALUES(:id, :time,:ip)
 ON DUPLICATE KEY UPDATE
 online_time = :time , online_id = :id');
 $query->bindValue(':id',$id,PDO::PARAM_INT);
 $query->bindValue(':time',time(), PDO::PARAM_INT);
 $query->bindValue(':ip', $ip, PDO::PARAM_INT);
 $query->execute();
 $query->CloseCursor();
 
 $time_max = time() - (60 * 5);
 $query=$db->prepare('DELETE FROM forum_whosonline WHERE online_time < :timemax');
 $query->bindValue(':timemax',$time_max, PDO::PARAM_INT);
 $query->execute();
 $query->CloseCursor();


?>