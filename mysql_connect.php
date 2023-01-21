<?php
	$session = mysql_connect("localhost", "user", "password") or die('Erreur de connection à la BDD');
	mysql_select_db("argh", $session);
?>