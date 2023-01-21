<?php

	require_once '__local.config.php';
	
	if (!ALLOW_LEAGUE) header('Location: /index.php');

	$menu_left_current = 'menu_left_league.php';

	include_once '__local.template.php';

?>