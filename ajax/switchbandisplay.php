<?php
	require_once('../classes/RightsMode.php');
	require_once('../classes/ArghSession.php');
	ArghSession::begin();
	
	require('../mysql_connect.php');

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::LADDER_ADMIN, 
			RightsMode::VIP_HEADADMIN, 
			RightsMode::VIP_ADMIN
		)
	);

	$banId = (int)$_GET['banId'];
	
	mysql_query("UPDATE lg_ladderbans_follow SET afficher = ABS(afficher - 1) WHERE id = '".$banId."'");
?>