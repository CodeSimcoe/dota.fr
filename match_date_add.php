<?php
	//Logged + Posted ?
	if (!(isset($_POST['id']) && ArghSession::is_logged())) exit;

	//POST
	$id = (int)$_POST['id'];
	$hour = (int)$_POST['hour'];
	$minute = (int)$_POST['minute'];
	$month = (int)$_POST['month'];
	$day = (int)$_POST['day'];
	$year = (int)$_POST['year'];
	$team1 = (int)$_POST['team1'];
	$team2 = (int)$_POST['team2'];
	$team_propose = (int)$_POST['team_propose'];
	
	ArghPanel::begin_tag(Lang::DATE_PROPOSAL);
	
	$time = mktime($hour, $minute, 0, $month, $day, $year);
	
	if (ArghSession::is_logged() && ((ArghSession::get_clan_rank() <= 2 and (ArghSession::get_clan() == $team1 or ArghSession::get_clan() == $team2)) or ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN)))) {
		
		$req = "UPDATE lg_matchs
				SET date_proposee = '".$time."',
					qui_propose = '".ArghSession::get_username()."',
					team_propose = '".$team_propose."'
				WHERE id = '".$id."'";
		
		echo '<center>';
		if (mysql_query($req)) {
			echo sprintf(Lang::DATE_PROPOSED, date(Lang::DATE_FORMAT_HOUR, $time), ArghSession::get_username()).'<br />
			<a href="?f=match&team1='.$team1.'&amp;team2='.$team2.'">'.Lang::GO_ON.'</a>';
		} else {
			echo Lang::ERROR_OCCURED;
		}
		echo '</center>';
	} else {
		echo Lang::AUTHORIZATION_REQUIRED;
	}
	
	ArghPanel::end_tag();
?>