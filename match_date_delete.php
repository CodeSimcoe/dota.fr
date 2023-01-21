<!--
<table class="simple">
<tr><td class="top_left"></td><td class="top">Suppression d'une proposition de date</td><td class="top_right"></td></tr>
-->
<?php
	ArghPanel::begin_tag();
	$id = (int)$_POST['id'];
	$qui_propose = $_POST['qui_propose'];
	
	if (ArghSession::is_logged() and (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN)) or ArghSession::get_username() == $qui_propose)) {
		$req = "UPDATE lg_matchs SET date_proposee='0', qui_propose='', team_propose='0', date_acceptation='0', qui_accepte='', team_accepte='0' WHERE id='".$id."'";
		if ($upd = mysql_query($req)) {
			echo "<center>La proposition de date a bien été supprimée.<br><a href=\"?f=match&team1=".$_POST['team1']."&team2=".$_POST['team2']."\">Continuer</a></center>";
		}
	}
	ArghPanel::end_tag();
?>
<!--
<tr><td class="bottom_left"></td><td class="bottom"></td><td class="bottom_right"></td></tr>
</table>
-->