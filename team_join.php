<?
	ArghPanel::begin_tag(Lang::TEAM_ABOUT_TO_BE_DELETED);
	$ok = true;
	
	if (!ArghSession::is_logged()) {
		echo Lang::LOGGING_REQUIRED;
		$ok = false;
	}
	
	$teamid = (int)$_POST['teamid'];
	
	if (ArghSession::get_clan() == $teamid && $teamid != 0 && $ok) {
		echo Lang::TEAM_ALREADY_MEMBER_OF;
		$ok = false;
	}
	
	$joinpass = $_POST['joinpass'];
	
	//Recuperation du passe de la team
	$req = "SELECT pass FROM lg_clans WHERE id = '".$teamid."'";
	$t = mysql_query($req);
	
	if (mysql_num_rows($t) == 0) {
		exit(Lang::NO_TEAM);
	} else {
		$l = mysql_fetch_object($t);
		$teampass = $l->pass;
	}

	if (ArghSession::get_clan_rank() == 1 && $ok) {
		echo Lang::TEAM_ERROR_TAUREN_CANT_JOIN;
		$ok = false;
	}

	if ($teampass != $joinpass && $ok) {
    	echo Lang::PASSWORD_MISMATCH.'<br />'.Lang::CASE_IMPORTANCE;
	} elseif ($ok) {
        $ins = "UPDATE lg_users
        		SET clan = '".$teamid."',
        			jclan = '".time()."',
        			crank = '".ClanRanks::PEON."'
        		WHERE username = '".ArghSession::get_username()."'";
		mysql_query($ins);
		$req = "SELECT name FROM lg_clans WHERE id = '".$teamid."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		echo sprintf(Lang::TEAM_JOINED, $l[0]);
    }
    
    ArghPanel::end_tag();
?>