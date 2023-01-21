<?php
ArghPanel::begin_tag(Lang::MATCH_DATE_REFUSAL);

$id = (int)$_POST['id'];
$id1 = (int)$_POST['id1'];
$id2 = (int)$_POST['id2'];
$team_propose = (int)$_POST['team_propose'];

if ((ArghSession::is_logged() && $team_propose != ArghSession::get_clan() && ((ArghSession::get_clan_rank() <= ClanRanks::SHAMAN && (ArghSession::get_clan() == $id1 || ArghSession::get_clan() == $id2)) || (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN)) && ArghSession::get_clan() != $id1 && ArghSession::get_clan() != $id2))) && $team_propose > 0 && $date_acceptation == 0) {
	$req = "UPDATE lg_matchs SET date_proposee = '0', qui_propose = '', team_propose = '0' WHERE id = '".$id."'";
	if ($upd = mysql_query($req)) {
		echo '<center>'.Lang::MATCH_DATE_REFUSED.'<br /><a href="?f=match&team1='.$id1.'&team2='.$id2.'">'.Lang::GO_ON.'</a></center>';
	}
}

ArghPanel::end_tag();
?>