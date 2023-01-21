<?php
	require '../mysql_connect.php';
	require '../classes/RightsMode.php';
	require '../classes/ArghSession.php';
	ArghSession::begin();
	
	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LEAGUE_HEADADMIN,
			RightsMode::LADDER_HEADADMIN,
			RightsMode::VIP_HEADADMIN
		)
	);
	
	$rule_id = (int)$_GET['rule_id'];
	
	$req = "SELECT rules FROM lg_rules WHERE id = '".$rule_id."'";
	$t = mysql_query($req);
	$l = mysql_fetch_row($t);
	
	echo stripslashes($l[0]);
?>