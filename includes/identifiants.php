<?php

try
 {
  $db = new PDO('mysql:host=linkeuhcyzdb.mysql.db;dbname=linkeuhcyzdb', 'linkeuhcyzdb', 'TourmueTTe84');
 }
 
catch (Exception $e)
 {
  die('Erreur : ' . $e->getMessage());
 }

?>