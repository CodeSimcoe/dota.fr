<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LEAGUE_HEADADMIN,
			RightsMode::LEAGUE_ADMIN
		)
	);
	
	ArghPanel::begin_tag(Lang::LEAGUE_WARN_ADDING);
	
	$team_id = (int)$_POST['team'];
	$valeur = (int)$_POST['valeur'];
	$motif = mysql_real_escape_string($_POST['motif']);
	
    $ins = "INSERT INTO lg_warns (qui_warn, date_warn, valeur, team, motif)
    		VALUES ('".ArghSession::get_username()."', '".time()."', '".$valeur."', '".$team_id."', '".$motif."')";
    mysql_query($ins);
	
	//Admin Log
	$al = new AdminLog(sprintf(Lang::ADMIN_LOG_TEAM_WARNED, $team_id, $valeur), AdminLog::TYPE_LEAGUE);
	$al->save_log();
	
	echo '<center>'.Lang::LEAGUE_WARN_ADDED.'</center>';
	
	ArghPanel::end_tag();

?>