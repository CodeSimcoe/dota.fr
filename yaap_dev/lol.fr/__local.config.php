<?php

	define('WEBSITE_PATH', 'C:\\wamp\\www\\yaap_dev\\lol.fr\\');

	require_once WEBSITE_PATH.'__local.define.php';
	require_once YAAP_PATH.'__yaap.config.php';
	require_once WEBSITE_PATH.'__local.require.php';

	$menu_left_current = null;

	$local_bdd = new PDO('mysql:dbname=yaap_leagueoflegend;host=127.0.0.1', 'root', '');

?>