<?php
	ArghSession::exit_if_not_logged();
	
	//Include
	include 'laddervip_functions.php';
	
	$gid = (int)$_POST['game_id'];
	
	//Report du joueur
	if (isset($_POST['SentBtn'])) {
		//Changement statut joueur
		$req = "UPDATE lg_users SET ladder_status = 'ready' WHERE username = '".ArghSession::get_username()."'";
		mysql_query($req);
		
		//Vérif que le joueur a pas déjà report
		$req = "SELECT *
				FROM lg_laddervip_winnersreports
				WHERE game_id = '".$gid."'
				AND qui = '".ArghSession::get_username()."'
				";
		$t = mysql_query($req);
		if (mysql_num_rows($t) > 0) {
			exit;
		}
		
		//Gold
		if ($_POST['winner'] != 'none') addGold(ArghSession::get_username(), 12);
		
		for($i = 1; $i <= 10; $i++) {
			if ($_POST['info'.$i] != 0) {
				$req = "INSERT INTO lg_laddervip_playersreports (game_id, qui, pour_qui, info)
						VALUES ('".$gid."', '".ArghSession::get_username()."', '".$_POST['pp'.$i]."', '".$_POST['info'.$i]."')";
				mysql_query($req) or die(mysql_error());
			}
		}
		
		$req = "INSERT INTO lg_laddervip_winnersreports (game_id, qui, winner)
				VALUES ('".$gid."', '".ArghSession::get_username()."', '".$_POST['winner']."')
		";
		mysql_query($req) or die(mysql_error());
		
		//Reports pour cette game
		$none = 0;
		$se = 0;
		$sc = 0;
		$req = "SELECT * FROM lg_laddervip_winnersreports WHERE game_id = '".$gid."'";
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
		
		//Minimum reports
		if ($total >= 8 and getGameStatus($gid) == 'playing') {
			if ($none >= 6) {
				reportGame($gid, 'none');
			} elseif ($se >= 6) {
				reportGame($gid, 'se');
			} elseif ($sc >= 6) {
				reportGame($gid, 'sc');
			}
			
			//Divergences => close
			if ($total == 10) {
				reportGame($gid, 'none');
			}
		}
	}
/*
?>

<script language="javascript">
function imgW() {
	var val = document.getElementById("winner").value;
	document.getElementById("winner_img").src = "ladder/" + val + ".jpg";
}
</script>

<?php
*/
	$player = ArghSession::get_username();
	
	if (getStatus($player) == 'busy_vip') {

		//Game en cours
		$req = "SELECT *
				FROM lg_laddervip_games
				WHERE p1 = '".$player."'
				OR p2 = '".$player."'
				OR p3 = '".$player."'
				OR p4 = '".$player."'
				OR p5 = '".$player."'
				OR p6 = '".$player."'
				OR p7 = '".$player."'
				OR p8 = '".$player."'
				OR cap1 = '".$player."'
				OR cap2 = '".$player."'
				ORDER BY id DESC
				LIMIT 1
		";
		$t = mysql_query($req);
		if (mysql_num_rows($t) > 0) {
			$l = mysql_fetch_object($t);
			
			//Status
			$status = (string)$l->status;
			$redStatus = substr($status, 2, 7);
			if ($redStatus == 'picking' and $status[0] == 'p') {
				//Pick des joueurs
				exit('Pick des joueurs en cours');
			}
			
			ArghPanel::begin_tag('');
			
			echo '<form method="POST" action="?f=laddervip_qg">
					<table class=simple>';
			
			echo '<tr><td class="top_left"></td><td class="top" colspan=6>Partie en cours - Game <span class="vip">VIP</span> #'.$l->id.'</td><td class="top_right"></td></tr>
			<tr><td colspan=6><center><img src="side_sentinel.jpg" alt="Sentinel"></center></td></tr>';
			echo '<tr><td width=25>&nbsp;</td><td><b>#</b></td><td><b>Username</b></td><td><b>GGC</b></td><td><b>XP</b></td><td width=25>&nbsp;</td></tr>';
			echo '<td width=25>&nbsp;</td><td><i>1.</i></td><td><a href="?f=player_profile&player='.$l->cap1.'">'.$l->cap1.'</a></td><td>'.getGGC($l->cap1).'</td><td><div align="right"><select name="info9">
				<option value="0">     -     </option>
				<option value="1">A quitté avant la fin</option>
				<option value="2">Ne s\'est pas présenté</option>
				<option value="3">Mauvais comportement</option>
			</select></div></td><td width=25>&nbsp;</td></tr>';
			echo '<td width=25>&nbsp;</td><td bgcolor="#111133"><i>2.</i></td><td bgcolor="#111133"><a href="?f=player_profile&player='.$l->pp1.'">'.$l->pp1.'</a></td><td bgcolor="#111133">'.getGGC($l->pp1).'</td><td bgcolor="#111133"><div align="right"><select name="info1">
				<option value="0">     -     </option>
				<option value="1">A quitté avant la fin</option>
				<option value="2">Ne s\'est pas présenté</option>
				<option value="3">Mauvais comportement</option>
			</select></div></td><td width=25>&nbsp;</td></tr>';
			echo '<td width=25>&nbsp;</td><td><i>3.</i></td><td><a href="?f=player_profile&player='.$l->pp4.'">'.$l->pp4.'</a></td><td>'.getGGC($l->pp4).'</td><td><div align="right"><select name="info4">
				<option value="0">     -     </option>
				<option value="1">A quitté avant la fin</option>
				<option value="2">Ne s\'est pas présenté</option>
				<option value="3">Mauvais comportement</option>
			</select></div></td><td width=25>&nbsp;</td></tr>';
			echo '<td width=25>&nbsp;</td><td bgcolor="#111133"><i>4.</i></td><td bgcolor="#111133"><a href="?f=player_profile&player='.$l->pp5.'">'.$l->pp5.'</a></td><td bgcolor="#111133">'.getGGC($l->pp5).'</td><td bgcolor="#111133"><div align="right"><select name="info5">
				<option value="0">     -     </option>
				<option value="1">A quitté avant la fin</option>
				<option value="2">Ne s\'est pas présenté</option>
				<option value="3">Mauvais comportement</option>
			</select></div></td><td width=25>&nbsp;</td></tr>';
			echo '<td width=25>&nbsp;</td><td><i>5.</i></td><td><a href="?f=player_profile&player='.$l->pp8.'">'.$l->pp8.'</a></td><td>'.getGGC($l->pp8).'</td><td><div align="right"><select name="info8">
				<option value="0">     -     </option>
				<option value="1">A quitté avant la fin</option>
				<option value="2">Ne s\'est pas présenté</option>
				<option value="3">Mauvais comportement</option>
			</select></div></td><td width=25>&nbsp;</td></tr>';
			
			echo '<tr><td colspan=6>&nbsp;</td></tr>';
			
			echo '<tr><td colspan=6><center><img src="side_scourge.jpg" alt="Sentinel"></center></td></tr>';
			echo '<tr><td width=25>&nbsp;</td><td><b>#</b></td><td><b>Username</b></td><td><b>GGC</b></td><td><b>XP</b></td><td width=25>&nbsp;</td></tr>';
			echo '<td width=25>&nbsp;</td><td><i>6.</i></td><td><a href="?f=player_profile&player='.$l->cap2.'">'.$l->cap2.'</a></td><td>'.getGGC($l->cap2).'</td><td><div align="right"><select name="info10">
				<option value="0">     -     </option>
				<option value="1">A quitté avant la fin</option>
				<option value="2">Ne s\'est pas présenté</option>
				<option value="3">Mauvais comportement</option>
			</select></div></td><td width=25>&nbsp;</td></tr>';
			echo '<td width=25>&nbsp;</td><td bgcolor="#111133"><i>7.</i></td><td bgcolor="#111133"><a href="?f=player_profile&player='.$l->pp2.'">'.$l->pp2.'</a></td><td bgcolor="#111133">'.getGGC($l->pp2).'</td><td bgcolor="#111133"><div align="right"><select name="info2">
				<option value="0">     -     </option>
				<option value="1">A quitté avant la fin</option>
				<option value="2">Ne s\'est pas présenté</option>
				<option value="3">Mauvais comportement</option>
			</select></div></td><td width=25>&nbsp;</td></tr>';
			echo '<td width=25>&nbsp;</td><td><i>8.</i></td><td><a href="?f=player_profile&player='.$l->pp3.'">'.$l->pp3.'</a></td><td>'.getGGC($l->pp3).'</td><td><div align="right"><select name="info3">
				<option value="0">     -     </option>
				<option value="1">A quitté avant la fin</option>
				<option value="2">Ne s\'est pas présenté</option>
				<option value="3">Mauvais comportement</option>
			</select></div></td><td width=25>&nbsp;</td></tr>';
			echo '<td width=25>&nbsp;</td><td bgcolor="#111133"><i>9.</i></td><td bgcolor="#111133"><a href="?f=player_profile&player='.$l->pp6.'">'.$l->pp6.'</a></td><td bgcolor="#111133">'.getGGC($l->pp6).'</td><td bgcolor="#111133"><div align="right"><select name="info6">
				<option value="0">     -     </option>
				<option value="1">A quitté avant la fin</option>
				<option value="2">Ne s\'est pas présenté</option>
				<option value="3">Mauvais comportement</option>
			</select></div></td><td width=25>&nbsp;</td></tr>';
			echo '<td width=25>&nbsp;</td><td><i>10.</i></td><td><a href="?f=player_profile&player='.$l->pp7.'">'.$l->pp7.'</a></td><td>'.getGGC($l->pp7).'</td><td><div align="right"><select name="info7">
				<option value="0">     -     </option>
				<option value="1">A quitté avant la fin</option>
				<option value="2">Ne s\'est pas présenté</option>
				<option value="3">Mauvais comportement</option>
			</select></div></td><td width=25>&nbsp;</td></tr>';
			
			
			
			echo '<tr><td colspan=6>&nbsp;</td></tr>';
			echo '<tr><td colspan=2>&nbsp;</td><td><strong>Vainqueur</strong><br />
				<select name="winner" id="winner" onChange="imgW()">
					<option value="none">'.Lang::NONE.'</option>
					<option value="se">'.Lang::TEAM.' 1</option>
					<option value="sc">'.Lang::TEAM.' 2</option>
				</select>
			</td><td colspan=3><img src="ladder/none.jpg" id="winner_img" /></td></tr>';
			
			echo '<tr><td colspan=6>&nbsp;</td></tr>';
			echo '<tr><td colspan=6>
					<input type="hidden" name="game_id" value="'.$l->id.'">
					</td></tr>';
			echo '<tr><td colspan=6><center><input type="submit" value="Envoyer" name="SentBtn"></center></td></tr>';
			
			echo '<input type="hidden" name="pp1" value="'.$l->pp1.'">';
			echo '<input type="hidden" name="pp2" value="'.$l->pp2.'">';
			echo '<input type="hidden" name="pp3" value="'.$l->pp3.'">';
			echo '<input type="hidden" name="pp4" value="'.$l->pp4.'">';
			echo '<input type="hidden" name="pp5" value="'.$l->pp5.'">';
			echo '<input type="hidden" name="pp6" value="'.$l->pp6.'">';
			echo '<input type="hidden" name="pp7" value="'.$l->pp7.'">';
			echo '<input type="hidden" name="pp8" value="'.$l->pp8.'">';
			echo '<input type="hidden" name="pp9" value="'.$l->cap1.'">';
			echo '<input type="hidden" name="pp10" value="'.$l->cap2.'">';
			
			echo '</form>';
			
			echo '</table>';
			
			ArghPanel::end_tag();
		}
	}
	blocLastGames($player, 200);
?>