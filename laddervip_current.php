<?php
	function Minutes($started) {
		return round((time() - $started) / 60, 0);
	}
	
	function getPts($player) {
		$req = "SELECT pts_vip FROM lg_users WHERE username='".$player."'";
		$t = mysql_query($req);
		$l = mysql_fetch_row($t);
		return $l[0];
	}
	
	ArghPanel::begin_tag(Lang::LADDERVIP_RUNNING_GAMES);
	include 'campaigns/lol-goa.php';
	echo '<br />';
?>
<table class="listing">
<?php
/*
enum('open', 'p_picking1', 'p_picking2', 'p_picking3', 'p_picking4', 'p_picking5', 'p_picking6', 'p_picking7', 'h_picking1', 'h_picking2', 'h_picking3', 'h_picking4', 'h_picking5', 'h_picking6', 'h_picking7', 'h_picking8', 'h_picking9', 'h_picking0', 'h_banning1', 'h_banning2', 'h_banning3', 'h_banning4', 'playing', 'closed', 'admin_opened', 'reporting')
*/

	$query = "SELECT *
			FROM lg_laddervip_games
			WHERE status NOT IN ('".LadderStates::OPEN."', '".LadderStates::CLOSED."', '".LadderStates::ADMIN_OPENED."', '".REPORTING."', '')
			ORDER BY id DESC";
	$table = mysql_query($query);
	if (mysql_num_rows($table) > 0) {
		$i = 0;
		echo '<tr><td><b>'.Lang::GAME_SHARP.'</b></td><td><b>'.Lang::GAME_STARTED_AGO.'</b></td><td><b>'.Lang::CAPTAINS.'</b></td><td><b>'.Lang::PLAYERS.'</b></td><td><b>'.Lang::STATUS.'</b></td></tr>';
		echo '<tr><td colspan=5 class="line">&nbsp;</td></tr>';
		while ($line = mysql_fetch_object($table)) {
			//Status
			$status = (string)$line->status;
			$redStatus = substr($status, 2, 7);
			$step = $status[strlen($status)-1];
			if ($step == 0) $step = 10;
			
			if ($status[0] == 'p' and $status != 'playing') {
				$etat = 'pick joueur '.$step.'/8';
			} elseif ($status[0] == 'h') {
				if ($status[2] == 'p') {
					//pick
					$etat = 'pick h&eacute;ros '.$step.'/10';
				} else {
					//ban
					$etat = 'ban h&eacute;ros '.$step.'/4';
				}
			} else {
				$etat = 'En cours';
			}
			$alt = ($i++ % 2) ? ' class="alternate"' : '';
			echo '<tr valign="top">
			<td'.$alt.'><a href="?f=laddervip_game&id='.$line->id.'">#'.$line->id.'</a></td>
			<td'.$alt.'>'.Minutes($line->opened).' min</td>
			<td'.$alt.'><a href="?f=player_profile&player='.$line->cap1.'">'.$line->cap1.'</a><br />
				<a href="?f=player_profile&player='.$line->cap2.'">'.$line->cap2.'</a></td>
			<td'.$alt.'><a href="?f=player_profile&player='.$line->p1.'">'.$line->p1.'</a><br />
				<a href="?f=player_profile&player='.$line->p2.'">'.$line->p2.'</a><br />
				<a href="?f=player_profile&player='.$line->p3.'">'.$line->p3.'</a><br />
				<a href="?f=player_profile&player='.$line->p4.'">'.$line->p4.'</a><br />
				<a href="?f=player_profile&player='.$line->p5.'">'.$line->p5.'</a><br />
				<a href="?f=player_profile&player='.$line->p6.'">'.$line->p6.'</a><br />
				<a href="?f=player_profile&player='.$line->p7.'">'.$line->p7.'</a><br />
				<a href="?f=player_profile&player='.$line->p8.'">'.$line->p8.'</a><br />
				</td><td'.$alt.'>'.$etat.'</td></tr>';
			echo '<tr><td colspan=5>&nbsp;</td></tr>';
		}
	} else {
		echo '<tr><td colspan=5><center>Aucune partie en cours.</center></td></tr>';
	}
?>
</table>
<?php
	ArghPanel::end_tag();
?>