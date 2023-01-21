<?php
	require('mysql_connect.php');
	if (empty($_GET['file_id'])) echo '0';
	if (mysql_query("UPDATE lg_uploads SET dls=dls+1 WHERE id='".(int)$_GET['file_id']."'")) echo '1';
?>