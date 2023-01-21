<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LEAGUE_HEADADMIN,
			RightsMode::LEAGUE_ADMIN
		)
	);

	ArghPanel::begin_tag(Lang::ADMIN_WARNING_REMOVAL);
	
	$warn_id = (int)$_POST['id'];
	
	$req = "DELETE FROM lg_warns WHERE id = '".$warn_id."'";
	$t = mysql_query($req);
	
	//Admin Log
	$sentence = sprintf(Lang::ADMIN_LOG_WARNING_REMOVED, $warn_id);
	$al = new AdminLog($sentence, AdminLog::TYPE_LEAGUE);
	$al->save_log();
	
	echo '<center>'.Lang::ADMIN_WARNING_REMOVED.'</center>';
	
	ArghPanel::end_tag();

?>