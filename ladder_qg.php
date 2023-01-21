<?php
	ArghSession::exit_if_not_logged();
	
	
	//Include
	include 'ladder_functions.php';
	
	//Report du joueur
	if (isset($_POST['info_sent'])) {
		//Vérif que le joueur a pas déjà report
		$req = "SELECT *
				FROM lg_winnersreports
				WHERE game_id = '".(int)$_POST['game_id']."'
				AND qui = '".ArghSession::get_username()."'
				";
		$t = mysql_query($req);
		if (mysql_num_rows($t) > 0) {
			exit(Lang::INFORMATION_SENT);
		} else {
			mysql_query("UPDATE lg_users SET soundplayed = '0' WHERE username = '".ArghSession::get_username()."'");
			$sreq = "SELECT * FROM lg_laddergames WHERE id = '".(int)$_POST['game_id']."'";
			$st = mysql_query($sreq);
			$sl = mysql_fetch_object($st);
			if (time() < ($sl->opened + 300) && !ArghSession::is_rights(RightsMode::WEBMASTER)) {
				ArghPanel::begin_tag();
				
				$minutes = 5;
				
				echo '<center>'.sprintf(Lang::LADDER_MUST_WAIT_BEFORE_CLOSING, $minutes).'</center>';
				ArghPanel::end_tag();
				exit;
			}
		}
		
		//Changement statut joueur
		$req = "UPDATE lg_users SET ladder_status = '".LadderStates::READY."' WHERE username = '".ArghSession::get_username()."'";
		mysql_query($req);
		
		//Gold
		if ($_POST['winner'] != 'none') addGold(ArghSession::get_username(), 12);
		
		for ($i = 1; $i <= 10; $i++) {
			if ($_POST['info'.$i] != 0) {
				$req = "INSERT INTO lg_playersreports (game_id, qui, pour_qui, info)
						VALUES ('".(int)$_POST['game_id']."', '".ArghSession::get_username()."', '".mysql_real_escape_string($_POST['p'.$i])."', '".(int)$_POST['info'.$i]."')
				";
				mysql_query($req) or die(mysql_error());
			}
		}
		
		$req = "INSERT INTO lg_winnersreports (game_id, qui, winner)
				VALUES ('".(int)$_POST['game_id']."', '".ArghSession::get_username()."', '".mysql_real_escape_string($_POST['winner'])."')
		";
		mysql_query($req) or die(mysql_error());
		
		//Reports pour cette game
		$none = 0;
		$se = 0;
		$sc = 0;
		$req = "SELECT * FROM lg_winnersreports WHERE game_id = '".(int)$_POST['game_id']."'";
		$t = mysql_query($req);
		while ($l = mysql_fetch_object($t)) {
			switch ($l->winner) {
				case 'none':
					$none++;
					break;
				case 'se':
					$se++;
					break;
				case 'sc':
					$sc++;
					break;
				default:
					break;
			}
		}
		$total = $none + $se + $sc;
		$reported = false;
		
		//Minimum reports
		$gid = (int)$_POST['game_id'];
		if ($total == 9 && getGameStatus($gid) == LadderStates::PLAYING) {
			if ($none == max($none, $se, $sc)) {
				GameReporter::report($gid, GameReporter::NO_WINNER);
			} elseif ($se == max($none, $se, $sc)) {
				GameReporter::report($gid, GameReporter::SENTINEL);
			} elseif ($sc = max($none, $se, $sc)) {
				GameReporter::report($gid, GameReporter::SCOURGE);
			}
		}
		/*
		if ($total >= 8 and getGameStatus((int)$_POST['game_id']) == 'playing') {
			if ($none >= 6) {
				reportGame((int)$_POST['game_id'], 'none', ArghSession::get_username());
				$reported = true;
			} elseif ($se >= 6) {
				reportGame((int)$_POST['game_id'], 'se', ArghSession::get_username());
				$reported = true;
			} elseif ($sc >= 6) {
				reportGame((int)$_POST['game_id'], 'sc', ArghSession::get_username());
				$reported = true;
			}
			
			//Divergences => close
			if ($total == 10 and !$reported) {
				reportGame($_POST['game_id'], 'none');
			}
		}
		*/
	}
?>

<script language="javascript">
	function winnerPicture() {
		$('#winner_img').attr('src', 'ladder/' + $('#winner').val() + '.jpg');
		/*
		var val = document.getElementById("winner").value;
		document.getElementById("winner_img").src = "ladder/" + val + ".jpg";
		*/
	}
</script>

<?php
	$player = ArghSession::get_username();
	
	if (getStatus($player) == LadderStates::IN_NORMAL_GAME) {

		//Game en cours
		$req = "SELECT *
				FROM lg_laddergames
				WHERE p1 = '".$player."'
				OR p2 = '".$player."'
				OR p3 = '".$player."'
				OR p4 = '".$player."'
				OR p5 = '".$player."'
				OR p6 = '".$player."'
				OR p7 = '".$player."'
				OR p8 = '".$player."'
				OR p9 = '".$player."'
				OR p10 = '".$player."'
				ORDER BY id DESC
				LIMIT 1
		";
		$t = mysql_query($req);
		if (mysql_num_rows($t) > 0) {
			$l = mysql_fetch_object($t);
			ArghPanel::begin_tag(sprintf(Lang::LADDER_CURRENT_GAME, $l->id));
			echo '<form method="POST" action="?f=ladder_qg">
					<table class="simple">';
			for ($i = 1; $i <= 10; $i++) {
				if ($i == 1) echo '<tr><td colspan="3"><img src="side_sentinel.jpg" alt="'.Lang::SENTINEL.'" /></td></tr>';
				if ($i == 6) echo '	<tr><td colspan="3"></td></tr>
									<tr><td colspan="3"><img src="side_scourge.jpg" alt="'.Lang::SCOURGE.'" /></td></tr>';
				$pl = 'p'.$i;
				$alt = (($i < 6 and $i%2 == 0) or ($i > 5 and $i%2 == 1)) ? ' class="alternate"' : '';
				echo '<tr'.$alt.'><td>';
				
				//if ($player != $l->$pl) echo '<input type="hidden" name="p'.$i.'" value="'.$l->$pl.'">';
				echo '<input type="hidden" name="p'.$i.'" value="'.$l->$pl.'">';
				
				echo '<i>'.$i.'.</i></td><td><a href="?f=player_profile&player='.$l->$pl.'">'.$l->$pl.'</a> [ '.getGGC($l->$pl).' ]</td><td><div align="right">';
				
				echo '	<select name="info'.$i.'" id="info_'.$i.'">';
				echo '<option value="0">     -     </option>';
				foreach (LadderStates::$PLAYERS_INFOS as $value => $label) {
					echo '<option value="'.$value.'">'.$label.'</option>';
				}
				echo '</select>';
				
				echo '</div></td></tr>';
			}
			
			echo '<tr><td colspan="3">&nbsp;</td></tr>';
			echo '<tr><td>&nbsp;</td><td><strong>'.Lang::WINNER.'</strong><br />
				<select name="winner" id="winner" onChange="winnerPicture()">
					<option value="none">'.Lang::NONE.'</option>
					<option value="se">'.Lang::SENTINEL.'</option>
					<option value="sc">'.Lang::SCOURGE.'</option>
				</select>
			</td><td><img src="ladder/none.jpg" id="winner_img" alt="'.Lang::WINNER.'" /></td></tr>';
			
			echo '<tr><td colspan="3">&nbsp;</td></tr>';
			echo '	<tr><td colspan="3">
					<input type="hidden" name="game_id" value="'.$l->id.'">
					</td></tr>';
			echo '<tr><td colspan="3"><center><input type="submit" value="'.Lang::VALIDATE.'" name="info_sent" /></center></td></tr>';
			
			echo '</table></form>';
			ArghPanel::end_tag();
		}
	}
	blocLastGames($player, 25);
?>


	
