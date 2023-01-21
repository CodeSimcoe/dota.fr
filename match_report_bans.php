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
	if (isset($_POST['save']) || isset($_POST['delete'])) {
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
			m.h1, m.h2, m.h3, m.h4, m.h5, m.h6, m.h7, m.h8, m.h9, m.h10,
			m.ban1, m.ban2, m.ban3, m.ban4, m.ban5, m.ban6, m.ban7, m.ban8, m.ban9, m.ban10";	
	} else {
		$req .= "
			m.version2 AS 'version',
			m.h1r2 AS 'h1', m.h2r2 AS 'h2', m.h3r2 AS 'h3', m.h4r2 AS 'h4', m.h5r2 AS 'h5', m.h6r2 AS 'h6', m.h7r2 AS 'h7', m.h8r2 AS 'h8', m.h9r2 AS 'h9', m.h10r2 AS 'h10',
			m.ban1r2 AS 'ban1', m.ban2r2 AS 'ban2', m.ban3r2 AS 'ban3', m.ban4r2 AS 'ban4', m.ban5r2 AS 'ban5', m.ban6r2 AS 'ban6', m.ban7r2 AS 'ban7', m.ban8r2 AS 'ban8', m.ban9r2 9AS 'ban9', m.ban10r2 AS 'ban10'";
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
		
	$team1 = $obj->team1;
	$team2 = $obj->team2;
	
	if (ArghSession::is_rights(RightsMode::LEAGUE_ADMIN) && (int)ArghSession::get_league_admin() != $obj->divi) exit();

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
	

?>
<script type="text/javascript">
	function updatePic(item) {
		item.style.backgroundImage = item.options[item.selectedIndex].style.backgroundImage;
	}
</script>
<?php ArghPanel::begin_tag('Bans Report - '.$obj->team1tag.' / '.$obj->team2tag.' - Manche '.$manche); ?>
<form action="?f=match_report_bans&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>&manche=<?php echo $manche ?>" method="post">
	<table width="100%">
		<colgroup>
			<col width="70" />
			<col />
			<col width="250" />
		</colgroup>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td align="left" colspan="2" valign="top"><b>Team <?php echo $obj->team1tag ?></b></td>
			<td align="center">
			<?php
				if ($manche == 1) {
					$hero = replay_definition_heroes($def, $obj->h1);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h2);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h3);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h4);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h5);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
				} else {
					$hero = replay_definition_heroes($def, $obj->h6);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h7);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h8);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h9);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h10);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
				}
			?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">Ban 1 :</td>
			<td align="left" colspan="2">
			<?php
				if ($isPost == true) {
					if ($manche == 1) {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban1']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban1']);
					} else {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban6']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban6']);
					}
				} else {
					if ($manche == 1) {
						echo heroBox($def, $obj->ban1, "ban1");
					} else {
						echo heroBox($def, $obj->ban5, "ban6");
					}
				}
			?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">Ban 2 :</td>
			<td align="left" colspan="2">
			<?php
				if ($isPost == true) {
					if ($manche == 1) {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban2']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban2']);
					} else {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban7']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban7']);
					}
				} else {
					if ($manche == 1) {
						echo heroBox($def, $obj->ban2, "ban2");
					} else {
						echo heroBox($def, $obj->ban6, "ban7");
					}
				}
			?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">Ban 3 :</td>
			<td align="left" colspan="2">
			<?php
				if ($isPost == true) {
					if ($manche == 1) {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban3']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban3']);
					} else {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban8']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban8']);
					}
				} else {
					if ($manche == 1) {
						echo heroBox($def, $obj->ban3, "ban3");
					} else {
						echo heroBox($def, $obj->ban7, "ban8");
					}
				}
			?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">Ban 4 :</td>
			<td align="left" colspan="2">
			<?php
				if ($isPost == true) {
					if ($manche == 1) {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban4']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban4']);
					} else {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban9']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban9']);
					}
				} else {
					if ($manche == 1) {
						echo heroBox($def, $obj->ban4, "ban4");
					} else {
						echo heroBox($def, $obj->ban8, "ban9");
					}
				}
			?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">Ban 5 :</td>
			<td align="left" colspan="2">
			<?php
				if ($isPost == true) {
					if ($manche == 1) {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban5']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban5']);
					} else {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban10']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban10']);
					}
				} else {
					if ($manche == 1) {
						echo heroBox($def, $obj->ban4, "ban5");
					} else {
						echo heroBox($def, $obj->ban8, "ban10");
					}
				}
			?>
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td align="left" colspan="2" valign="top"><b>Team <?php echo $obj->team2tag ?></b></td>
			<td align="center">
			<?php
				if ($manche == 1) {
					$hero = replay_definition_heroes($def, $obj->h6);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h7);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h8);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h9);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h10);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
				} else {
					$hero = replay_definition_heroes($def, $obj->h1);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h2);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h3);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h4);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
					$hero = replay_definition_heroes($def, $obj->h5);
					if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
				}
			?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">Ban 1 :</td>
			<td align="left" colspan="2">
			<?php
				if ($isPost == true) {
					if ($manche == 1) {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban6']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban6']);
					} else {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban1']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban1']);
					}
				} else {
					if ($manche == 1) {
						echo heroBox($def, $obj->ban5, "ban6");
					} else {
						echo heroBox($def, $obj->ban1, "ban1");
					}
				}
			?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">Ban 2 :</td>
			<td align="left" colspan="2">
			<?php
				if ($isPost == true) {
					if ($manche == 1) {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban7']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban7']);
					} else {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban2']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban2']);
					}
				} else {
					if ($manche == 1) {
						echo heroBox($def, $obj->ban6, "ban7");
					} else {
						echo heroBox($def, $obj->ban2, "ban2");
					}
				}
			?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">Ban 3 :</td>
			<td align="left" colspan="2">
			<?php
				if ($isPost == true) {
					if ($manche == 1) {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban8']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban8']);
					} else {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban3']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban3']);
					}
				} else {
					if ($manche == 1) {
						echo heroBox($def, $obj->ban7, "ban8");
					} else {
						echo heroBox($def, $obj->ban3, "ban3");
					}
				}
			?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">Ban 4 :</td>
			<td align="left" colspan="2">
			<?php
				if ($isPost == true) {
					if ($manche == 1) {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban9']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban9']);
					} else {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban4']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban4']);
					}
				} else {
					if ($manche == 1) {
						echo heroBox($def, $obj->ban8, "ban9");
					} else {
						echo heroBox($def, $obj->ban4, "ban4");
					}
				}
			?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">Ban 4 :</td>
			<td align="left" colspan="2">
			<?php
				if ($isPost == true) {
					if ($manche == 1) {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban10']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban10']);
					} else {
						$hero = replay_definition_heroes($def, stripslashes($_POST['ban5']));
						if (hero != null) echo '<img alt="" align="absmiddle" title="'.$hero['hero'].'" src="/ligue/parser/Images/mini/'.$hero['img'].'.png" />';
						echo stripslashes($_POST['ban5']);
					}
				} else {
					if ($manche == 1) {
						echo heroBox($def, $obj->ban8, "ban10");
					} else {
						echo heroBox($def, $obj->ban4, "ban5");
					}
				}
			?>
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
	</table>
	<?php
	if ($isPost == true) {
		if (isset($_POST['save'])) {
			$al = new AdminLog(sprintf(Lang::ADMIN_LOG_FILLING_BANS, $obj->id, $manche), AdminLog::TYPE_LEAGUE);
			$al->save_log();
			//$upd="INSERT INTO lg_adminlog (qui, quand, quoi) VALUES ('".$_SESSION['username']."', '".time()."', 'Remplissage des bans du match ".$obj->id." manche ".$manche."')";
			//mysql_query($upd);
			$msg = "Bans mis à jour";
			$upd = "
				UPDATE lg_matchs SET";
			if ($manche == 1) {
				$upd .= "
					version1 = '".$ver."',
					ban1 = '".mysql_real_escape_string(stripslashes($_POST['ban1']))."',
					ban2 = '".mysql_real_escape_string(stripslashes($_POST['ban2']))."',
					ban3 = '".mysql_real_escape_string(stripslashes($_POST['ban3']))."',
					ban4 = '".mysql_real_escape_string(stripslashes($_POST['ban4']))."',
					ban5 = '".mysql_real_escape_string(stripslashes($_POST['ban5']))."',
					ban6 = '".mysql_real_escape_string(stripslashes($_POST['ban6']))."',
					ban7 = '".mysql_real_escape_string(stripslashes($_POST['ban7']))."',
					ban8 = '".mysql_real_escape_string(stripslashes($_POST['ban8']))."',
					ban9 = '".mysql_real_escape_string(stripslashes($_POST['ban9']))."',
					ban10 = '".mysql_real_escape_string(stripslashes($_POST['ban10']))."'";
			} else {
				$upd .= "
					version2 = '".$ver."',
					ban1r2 = '".mysql_real_escape_string(stripslashes($_POST['ban1']))."',
					ban2r2 = '".mysql_real_escape_string(stripslashes($_POST['ban2']))."',
					ban3r2 = '".mysql_real_escape_string(stripslashes($_POST['ban3']))."',
					ban4r2 = '".mysql_real_escape_string(stripslashes($_POST['ban4']))."',
					ban5r2 = '".mysql_real_escape_string(stripslashes($_POST['ban5']))."',
					ban6r2 = '".mysql_real_escape_string(stripslashes($_POST['ban6']))."',
					ban7r2 = '".mysql_real_escape_string(stripslashes($_POST['ban7']))."',
					ban8r2 = '".mysql_real_escape_string(stripslashes($_POST['ban8']))."',
					ban9r2 = '".mysql_real_escape_string(stripslashes($_POST['ban9']))."',
					ban10r2 = '".mysql_real_escape_string(stripslashes($_POST['ban10']))."'";
			}
			$upd .= "
				WHERE team1 = '".$obj->team1."' AND team2 = '".$obj->team2."'";
		} else {
			$upd="INSERT INTO lg_adminlog (qui, quand, quoi) VALUES ('".$_SESSION['username']."', '".time()."', 'Suppression des bans du match ".$obj->id." manche ".$manche."')";
			mysql_query($upd);
			$msg = "Bans supprimés";
			$upd = "
				UPDATE lg_matchs SET";
			if ($manche == 1) {
				$upd .= "
					ban1 = '',
					ban2 = '',
					ban3 = '',
					ban4 = '',
					ban5 = '',
					ban6 = '',
					ban7 = '',
					ban8 = '',
					ban9 = '',
					ban10 = ''";
			} else {
				$upd .= "
					ban1r2 = '',
					ban2r2 = '',
					ban3r2 = '',
					ban4r2 = '',
					ban5r2 = '',
					ban6r2 = '',
					ban7r2 = '',
					ban8r2 = '',
					ban9r2 = '',
					ban10r2 = ''";
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
		<input type="button" style="width: 30%" name="cancel" value="Retour au Match" onclick="javascript:document.location.href='/ligue/?f=match&team1=<?php echo $team1 ?>&team2=<?php echo $team2 ?>';" />
		<input type="submit" style="width: 30%" name="delete" value="Supprimer" />
		<input type="submit" style="width: 30%" name="save" value="Valider" />
	</div>
	<?php
	}
	?>
</form>
<?php ArghPanel::end_tag(); ?>