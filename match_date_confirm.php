<?php
	ArghSession::exit_if_not_logged();
	ArghPanel::begin_tag(Lang::MATCH_DATE_CONFIRMATION);
	
	$id = (int)$_POST['id'];
	$team1 = (int)$_POST['id1'];
	$team2 = (int)$_POST['id2'];
	$team_propose = (int)$_POST['team_propose'];
	$date_acceptation = (int)$_POST['date_acceptation'];
	
	if (($team_propose != ArghSession::get_clan() && ((ArghSession::get_clan_rank() <= 2 && (ArghSession::get_clan() == $team1 or ArghSession::get_clan() == $team2)) or (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN)) and ArghSession::get_clan() != $team1 and ArghSession::get_clan() != $team2))) and $team_propose > 0 and $date_acceptation == 0) {
		
		$upd = "UPDATE lg_matchs
				SET date_acceptation = '".time()."',
					qui_accepte = '".ArghSession::get_username()."',
					team_accepte = '".ArghSession::get_clan()."'
				WHERE id = '".$id."'";
		
		mysql_query($upd);
		
		echo Lang::DATE_CONFIRMED.'<br /><a href="?f=match&team1='.$team1.'&team2='.$team2.'">'.Lang::GO_ON.'</a>';

	} else {
		echo Lang::AUTHORIZATION_REQUIRED;
	}
	
	ArghPanel::end_tag();
?>
