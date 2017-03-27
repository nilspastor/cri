<?php

function code($texte)
 {
  //Smileys
  $texte = str_replace(':-D ', '<img src="./img/smileys/heureux.gif" title="heureux" alt="heureux" />', $texte);
  $texte = str_replace(':lol: ', '<img src="./img/smileys/lol.jpg" title="lol" alt="lol" />', $texte);
  $texte = str_replace(':triste:', '<img src="./img/smileys/triste.jpg" title="triste" alt="triste" />', $texte);
  $texte = str_replace(':frime:', '<img src="./img/smileys/cool.jpg" title="cool" alt="cool" />', $texte);
  $texte = str_replace(':rire:', '<img src="./img/smileys/rire.jpg" title="rire" alt="rire" />', $texte);
  $texte = str_replace(':s', '<img src="./img/smileys/confus.jpg" title="confus" alt="confus" />', $texte);
  $texte = str_replace(':O', '<img src="./img/smileys/choc.jpg" title="choc" alt="choc" />', $texte);
  $texte = str_replace(':question:', '<img src="./img/smileys/question.jpg" title="?" alt="?" />', $texte);
  $texte = str_replace(':exclamation:', '<img src="./img/smileys/exclamation.jpg" title="!" alt="!" />', $texte);
 
  //Mise en forme du texte
  //gras
  $texte = preg_replace('`\[g\](.+)\[/g\]`isU', '<strong>$1</strong>', $texte);
  //italique
  $texte = preg_replace('`\[i\](.+)\[/i\]`isU', '<em>$1</em>', $texte);
  //souligné
  $texte = preg_replace('`\[s\](.+)\[/s\]`isU', '<u>$1</u>', $texte);
  //lien
  $texte = preg_replace('#http://[a-z0-9._/-]+#i', '<a href="$0">$0</a>', $texte);
  //quote
  $texte = preg_replace('`\[quote\](.+)\[/quote\]`isU', '<div id="quote">$1</div>', $texte);
  //centre
  $texte = preg_replace('!\[center\](.+)\[/center\]!isU', '<p style="text-align:center;margin:0px;padding:0px;">$1</p>', $texte);
  //img
  $texte = preg_replace('!\[img\](.+)\[/img\]!isU', '<img src="$1" />', $texte);
  
  
  //On retourne la variable texte
  return $texte;
  
 }
?>