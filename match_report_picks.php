<?php
	
	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LEAGUE_HEADADMIN,
			RightsMode::LEAGUE_ADMIN
		)
	);
	
	require_once '/home/www/ligue/classes/ReplayClasses.php';

	$manche = 0;
	if (isset($_GET['manche'])) {
		$manche = (int)$_GET['manche'];
	}
	
	$team1 = 0;
	if (isset($_GET['team1'])) {
		$team1 = (int)$_GET['team1'];
	}
	
	$team2 = 0;
	if (isset($_GET['team2'])) {
		$team2 = (int)$_GET['team2'];
	}
	
	// Verification paramètre entrants
	if ($manche + $team1 + $team2 == 0) {
		exit();
	}
	
	$isPost = false;
	if (isset($_POST['save'])) {
		$isPost = true;
	}
	
	$req = "
		SELECT
		 m.id,
		 m.divi,
		 m.team1,
		 t1.tag AS 'team1tag',
		 t1.name AS 'team1name',
		 t1.logo AS 'team1logo',
		 m.team2,
		 t2.tag AS 'team2tag',
		 t2.name AS 'team2name',
		 t2.logo AS 'team2logo',";
	if ($manche == 1) {
		$req .= "
			m.version1 AS 'version',
			m.p1, m.p2, m.p3, m.p4, m.p5, m.p6, m.p7, m.p8, m.p9, m.p10,
			m.h1, m.h2, m.h3, m.h4, m.h5, m.h6, m.h7, m.h8, m.h9, m.h10";	
	} else {
		$req .= "
			m.version2 AS 'version',
			m.p1r2 AS 'p1', m.p2r2 AS 'p2', m.p3r2 AS 'p3', m.p4r2 AS 'p4', m.p5r2 AS 'p5', m.p6r2 AS 'p6', m.p7r2 AS 'p7', m.p8r2 AS 'p8', m.p9r2 AS 'p9', m.p10r2 AS 'p10',
			m.h1r2 AS 'h1', m.h2r2 AS 'h2', m.h3r2 AS 'h3', m.h4r2 AS 'h4', m.h5r2 AS 'h5', m.h6r2 AS 'h6', m.h7r2 AS 'h7', m.h8r2 AS 'h8', m.h9r2 AS 'h9', m.h10r2 AS 'h10'";
	}
	$req .= "
		FROM
		 lg_matchs AS m
		INNER JOIN lg_clans AS t1 ON t1.id = m.team1
		INNER JOIN lg_clans AS t2 ON t2.id = m.team2
		WHERE
		 (m.team1 = '".$team1."' AND m.team2 = '".$team2."')
		OR (m.team1 = '".$team2."' AND m.team2 = '".$team1."')";
	$qry = mysql_query($req) or die(mysql_error());
	if (mysql_num_rows($qry) == 0) {
		exit();
	}
	$obj = mysql_fetch_object($qry);
	
	if (ArghSession::is_rights(RightsMode::LEAGUE_ADMIN) && (int)ArghSession::get_league_admin() != $obj->divi) exit();
		
	$team1 = $obj->team1;
	$team2 = $obj->team2;
	$p1 = $obj->p1;
	$p2 = $obj->p2;
	$p3 = $obj->p3;
	$p4 = $obj->p4;
	$p5 = $obj->p5;
	$p6 = $obj->p6;
	$p7 = $obj->p7;
	$p8 = $obj->p8;
	$p9 = $obj->p9;
	$p10 = $obj->p10;
	$h1 = $obj->h1;
	$h2 = $obj->h2;
	$h3 = $obj->h3;
	$h4 = $obj->h4;
	$h5 = $obj->h5;
	$h6 = $obj->h6;
	$h7 = $obj->h7;
	$h8 = $obj->h8;
	$h9 = $obj->h9;
	$h10 = $obj->h10;
	$ver = $obj->version;
	
	if ($ver == '') {
		$req = "SELECT version FROM parser_versions WHERE is_league_version = 1";
		$res = mysql_query($req) or die(mysql_error());
		$row = mysql_fetch_row($res);
		$ver = $row[0];
	}
	
	function replay_definition_heroes_sort($a, $b) {
		if ($a['hero'] == $b['hero']) return 0;
		return ($a['hero'] < $b['hero']) ? -1 : 1;
	}
	function replay_definition_heroes($definition, $name) {
		foreach ($definition->heroes as $key => $value) {
			if ($value['code'] != $value['base_code']) continue;
			if ($value['hero'] == $name) return $value;
		}
		return null;
	}
	
	$def = new ReplayDefinition($ver);
	uasort($def->heroes, 'replay_definition_heroes_sort');

	function heroBox($definition, $selectedHero, $boxName) {
		$out = '';
		$hero = replay_definition_heroes($definition, $selectedHero);
		if ($hero == null) {
			$out .= '<select style="height: 26px; background-repeat:no-repeat; padding: 0px 0px 0px 42px;" name="'.$boxName.'" style="width: 200px;" onChange="javascript:updatePic(this)">';
		} else {
			$out .= '<select style="height: 26px; background-image:url(\'/ligue/parser/Images/mini/'.$hero['img'].'.png\'); background-repeat:no-repeat; padding: 0px 0px 0px 42px;" name="'.$boxName.'" style="width: 200px;" onChange="javascript:updatePic(this)">';
		}
		$out .= '<option style="height: 21px;" value=""></option>';
		foreach ($definition->heroes as $key => $value) {
			if ($value['code'] != $value['base_code']) continue;
			if ($selectedHero == $value['hero']) {
				$out .= '<option selected="selected" style="height: 21px; background-image:url(\'/ligue/parser/Images/mini/'.$value['img'].'.png\'); background-repeat:no-repeat; padding: 0px 0px 0px 42px;" value="'.$value['hero'].'">'.$value['hero'].'</option>';
			} else {
				$out .= '<option style="height: 21px; background-image:url(\'/ligue/parser/Images/mini/'.$value['img'].'.png\'); background-repeat:no-repeat; padding: 0px 0px 0px 42px;" value="'.$value['hero'].'">'.$value['hero'].'</option>';
			}
		}
		$out .= '</select>';
		return $out;
	}
	
	function teamBox($array, $selected, $name) {
		$out = '';
		$out .= '<select name="'.$name.'" style="width: 95%">';
		$out .= '<option value=""></option>';
		foreach ($array as $key => $value) {
			if ($selected == $value->username) {
				$out .= '<option value="'.$value->username.'" selected="selected">'.$value->ggc.' ('.$value->username.')'.'</option>';
			} else {
				$out .= '<option value="'.$value->username.'">'.$value->ggc.' ('.$value->username.')'.'</option>';
			}
		}
		$out .= '</select>';
		return $out;
	}

	$players1 = array();
	$req = "SELECT * FROM lg_users WHERE clan = '".$obj->team1."' ORDER BY ggc";
	$res = mysql_query($req);
	while ($h = mysql_fetch_object($res)) $players1[] = $h;

	$players2 = array();
	$req = "SELECT * FROM lg_users WHERE clan = '".$obj->team2."' ORDER BY ggc";
	$res = mysql_query($req);
	while ($h = mysql_fetch_object($res)) $players2[] = $h;

?>
<script type="text/javascript">
	function updatePic(item) {
		item.style.backgroundImage = item.options[item.selectedIndex].style.backgroundImage;
	}
</script>
<?php ArghPanel::begin_tag('Picks Report - '.$obj->team1tag.' / '.$obj->team2tag.' - Manche '.$manche); ?>
<form action="?f=match_report_picks&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>&manche=<?php echo $manche ?>" method="post">
	<table width="100%">
		<colgroup>
			<col width="20" />
			<col width="300" />
			<col width="250" />
		</colgroup>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td align="left" colspan="3" valign="top"><b>Team <?php echo $obj->team1tag ?></b></td>
		</tr>
		<?php
			for ($i = 1; $i < 6; $i++) {
		?>
		<tr>
			<td align="left" valign="top"></td>
			<td align="left">
			<?php
				if ($manche == 1) {
					$tmp = 'p'.$i;
				} else {
					$tmp = 'p'.($i + 5);
				}
				if ($isPost == true) {
					echo stripslashes($_POST[$tmp]);
				} else {
					echo teamBox($players1, ${$tmp}, $tmp);
				}
			?>
			</td>
			<td align="left">
			<?php
				if ($manche == 1) {
					$tmp = 'h'.$i;
				} else {
					$tmp = 'h'.($i + 5);
				}
				if ($isPost == true) {
					$hero = replay_definition_heroes($def, stripslashes($_POST[$tmp]));
					echo '<img alt="" align="absmiddle" title="'.stripslashes($_POST[$tmp]).'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />&nbsp;';
					echo stripslashes($_POST[$tmp]);
				} else {
					echo heroBox($def, ${$tmp}, $tmp);
				}
			?>
			</td>
		</tr>
		<?php
			}
		?>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td align="left" colspan="3" valign="top"><b>Team <?php echo $obj->team2tag ?></b></td>
		</tr>
		<?php
			for ($i = 1; $i < 6; $i++) {
		?>
		<tr>
			<td align="left" valign="top"></td>
			<td align="left">
			<?php
				if ($manche == 1) {
					$tmp = 'p'.($i + 5);
				} else {
					$tmp = 'p'.$i;
				}
				if ($isPost == true) {
					echo stripslashes($_POST[$tmp]);
				} else {
					echo teamBox($players2, ${$tmp}, $tmp);
				}
			?>
			</td>
			<td align="left">
			<?php
				if ($manche == 1) {
					$tmp = 'h'.($i + 5);
				} else {
					$tmp = 'h'.$i;
				}
				if ($isPost == true) {
					$hero = replay_definition_heroes($def, stripslashes($_POST[$tmp]));
					echo '<img alt="" align="absmiddle" title="'.stripslashes($_POST[$tmp]).'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />&nbsp;';
					echo stripslashes($_POST[$tmp]);
				} else {
					echo heroBox($def, ${$tmp}, $tmp);
				}
			?>
			</td>
		</tr>
		<?php
			}
		?>
		<tr><td colspan="3">&nbsp;</td></tr>
	</table>
	<?php
	if ($isPost == true) {
		if (isset($_POST['save'])) {
			$al = new AdminLog(sprintf(Lang::ADMIN_LOG_FILLING_PICKS, $obj->id, $manche), AdminLog::TYPE_LEAGUE);
			$al->save_log();
			
			//$upd="INSERT INTO lg_adminlog (qui, quand, quoi) VALUES ('".$_SESSION['username']."', '".time()."', 'Remplissage des picks du match ".$obj->id." manche ".$manche."')";
			//mysql_query($upd);
			$msg = "Picks mis à jour";
			$upd = "
				UPDATE lg_matchs SET";
			if ($manche == 1) {
				$upd .= "
					version1 = '".$ver."',
					p1 = '".mysql_real_escape_string(stripslashes($_POST['p1']))."',
					p2 = '".mysql_real_escape_string(stripslashes($_POST['p2']))."',
					p3 = '".mysql_real_escape_string(stripslashes($_POST['p3']))."',
					p4 = '".mysql_real_escape_string(stripslashes($_POST['p4']))."',
					p5 = '".mysql_real_escape_string(stripslashes($_POST['p5']))."',
					p6 = '".mysql_real_escape_string(stripslashes($_POST['p6']))."',
					p7 = '".mysql_real_escape_string(stripslashes($_POST['p7']))."',
					p8 = '".mysql_real_escape_string(stripslashes($_POST['p8']))."',
					p9 = '".mysql_real_escape_string(stripslashes($_POST['p9']))."',
					p10 = '".mysql_real_escape_string(stripslashes($_POST['p10']))."',
					h1 = '".mysql_real_escape_string(stripslashes($_POST['h1']))."',
					h2 = '".mysql_real_escape_string(stripslashes($_POST['h2']))."',
					h3 = '".mysql_real_escape_string(stripslashes($_POST['h3']))."',
					h4 = '".mysql_real_escape_string(stripslashes($_POST['h4']))."',
					h5 = '".mysql_real_escape_string(stripslashes($_POST['h5']))."',
					h6 = '".mysql_real_escape_string(stripslashes($_POST['h6']))."',
					h7 = '".mysql_real_escape_string(stripslashes($_POST['h7']))."',
					h8 = '".mysql_real_escape_string(stripslashes($_POST['h8']))."',
					h9 = '".mysql_real_escape_string(stripslashes($_POST['h9']))."',
					h10 = '".mysql_real_escape_string(stripslashes($_POST['h10']))."'";
			} else {
				$upd .= "
					version2 = '".$ver."',
					p1r2 = '".mysql_real_escape_string(stripslashes($_POST['p1']))."',
					p2r2 = '".mysql_real_escape_string(stripslashes($_POST['p2']))."',
					p3r2 = '".mysql_real_escape_string(stripslashes($_POST['p3']))."',
					p4r2 = '".mysql_real_escape_string(stripslashes($_POST['p4']))."',
					p5r2 = '".mysql_real_escape_string(stripslashes($_POST['p5']))."',
					p6r2 = '".mysql_real_escape_string(stripslashes($_POST['p6']))."',
					p7r2 = '".mysql_real_escape_string(stripslashes($_POST['p7']))."',
					p8r2 = '".mysql_real_escape_string(stripslashes($_POST['p8']))."',
					p9r2 = '".mysql_real_escape_string(stripslashes($_POST['p9']))."',
					p10r2 = '".mysql_real_escape_string(stripslashes($_POST['p10']))."',
					h1r2 = '".mysql_real_escape_string(stripslashes($_POST['h1']))."',
					h2r2 = '".mysql_real_escape_string(stripslashes($_POST['h2']))."',
					h3r2 = '".mysql_real_escape_string(stripslashes($_POST['h3']))."',
					h4r2 = '".mysql_real_escape_string(stripslashes($_POST['h4']))."',
					h5r2 = '".mysql_real_escape_string(stripslashes($_POST['h5']))."',
					h6r2 = '".mysql_real_escape_string(stripslashes($_POST['h6']))."',
					h7r2 = '".mysql_real_escape_string(stripslashes($_POST['h7']))."',
					h8r2 = '".mysql_real_escape_string(stripslashes($_POST['h8']))."',
					h9r2 = '".mysql_real_escape_string(stripslashes($_POST['h9']))."',
					h10r2 = '".mysql_real_escape_string(stripslashes($_POST['h10']))."'";
			}
			$upd .= "
				WHERE team1 = '".$obj->team1."' AND team2 = '".$obj->team2."'";
		}
		mysql_query($upd);
	?>
	<div style="text-align: center">
		<span class="lose"><?php echo $msg ?></span><br /><br />
		<input type="button" style="width: 80%" name="cancel" value="Retour au Match" onclick="javascript:document.location.href='/ligue/?f=match&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>';" />
	</div>
	<?php
	} else {
	?>
	<div style="text-align: center">
		<input type="button" style="width: 40%" name="cancel" value="Retour au Match" onclick="javascript:document.location.href='/ligue/?f=match&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>';" />
		<input type="submit" style="width: 40%" name="save" value="Valider" />
	</div>
	<?php
	}
	?>
</form>
<?php ArghPanel::end_tag(); ?>