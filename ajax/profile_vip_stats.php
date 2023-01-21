<?php
	//Page appelee par AJAX
	define('ABSOLUTE_PATH', '/var/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'classes/LadderStates.php';
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';

	function getGamesIdByPosition($g, $j) {
		$at = array();
		$ac = array();
		$a1 = array();
		$a2 = array();
		$a3 = array();
		$a4 = array();
		$a5 = array();
		$l = count($g);
		for ($i = 0; $i < $l; $i++) {
			$at[] = $g[$i]->id;
			if ($g[$i]->cap1 == $j OR $g[$i]->cap2 == $j) {
				$ac[] = $g[$i]->id;
			} else if ($g[$i]->pp1 == $j) {
				$a1[] = $g[$i]->id;
			} else if ($g[$i]->pp2 == $j OR $g[$i]->pp3 == $j) {
				$a2[] = $g[$i]->id;
			} else if ($g[$i]->pp4 == $j OR $g[$i]->pp5 == $j) {
				$a3[] = $g[$i]->id;
			} else if ($g[$i]->pp6 == $j OR $g[$i]->pp7 == $j) {
				$a4[] = $g[$i]->id;
			} else if ($g[$i]->pp8 == $j) {
				$a5[] = $g[$i]->id;
			}
		}
		return array($at, $ac, $a1, $a2, $a3, $a4, $a5);
	}

	function getGamesIdByCaptainAndPlayer($g, $c, $j) {
		$ag = array();
		$l = count($g);
		for ($i = 0; $i < $l; $i++) {
			if ($g[$i]->cap1 == $c AND (
				$g[$i]->pp1 == $j
				OR $g[$i]->pp4 == $j
				OR $g[$i]->pp5 == $j
				OR $g[$i]->pp8 == $j
			)) {
				$ag[] = $g[$i]->id;
			} else if ($g[$i]->cap2 == $c AND (
				$g[$i]->pp2 == $j
				OR $g[$i]->pp3 == $j
				OR $g[$i]->pp6 == $j
				OR $g[$i]->pp7 == $j
			)) {
				$ag[] = $g[$i]->id;
			}
		}
		return $ag;
	}
	
	function getTotalsByGames($f, $g) {
		$to = 0;
		$wi = 0;
		$lo = 0;
		$le = 0;
		$aw = 0;
		$l = count($f);
		for ($i = 0; $i < $l; $i++) {
			$as = array_search($f[$i]->game_id, $g);
			if (!($as === false)) {
				$to += 1;
				if ($f[$i]->xp > 0) {
					$wi += 1;
				} else if ($f[$i]->xp < 0 AND $f[$i]->resultat == 'lose') {
					$lo += 1;
				} else if ($f[$i]->xp < 0 AND $f[$i]->resultat == 'left') {
					$le += 1;
				} else if ($f[$i]->xp < 0 AND $f[$i]->resultat == 'away') {
					$aw += 1;
				}
			}
		}
		return array($to, $wi, $lo, $le, $aw);
	}

	if (isset($_GET['player'])) {
	
		$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));

		$games = array();
		$req = "
			SELECT DISTINCTROW 
			 g.id,
			 g.cap1, g.cap2,
			 g.pp1, g.pp2, g.pp3, g.pp4, g.pp5, g.pp6, g.pp7, g.pp8,
			 g.h1, g.h2, g.h3, g.h4, g.h5, g.h6, g.h7, g.h8, g.h9, g.h10,
			 g.ban1, g.ban2, g.ban3, g.ban4,
			 f.xp, f.resultat
			FROM lg_laddervip_follow AS f 
			INNER JOIN lg_laddervip_games AS g ON g.id = f.game_id
			WHERE 
			 f.player = '".$player."' 
			AND f.xp != 0";
		$qry = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($qry) > 0) {
			while ($o = mysql_fetch_object($qry)) $games[] = $o;
		}

		$mygames = array(
			array(0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0),
			array(0, 0, 0, 0, 0)
		);
		$mycaptains = array();
		$myplayers = array();
		$mybans = array();
		$mypicks = array();

		$l = count($games);
		for ($i = 0; $i < $l; $i++) {
			$g = $games[$i];
			$wi = $lo = $le = $aw = 0;
			if ($g->xp > 0) {
				$wi = 1;
			} else if ($g->xp < 0 AND $g->resultat == 'lose') {
				$lo = 1;
			} else if ($g->xp < 0 AND $g->resultat == 'left') {
				$le = 1;
			} else if ($g->xp < 0 AND $g->resultat == 'away') {
				$aw = 1;
			}
			// Compteur global
			$mygames[0][0] += 1;
			$mygames[0][1] += $wi;
			$mygames[0][2] += $lo;
			$mygames[0][3] += $le;
			$mygames[0][4] += $aw;
			if ($g->cap1 == $player) {
				// Compteur Capitaine global
				$mygames[1][0] += 1;
				$mygames[1][1] += $wi;
				$mygames[1][2] += $lo;
				$mygames[1][3] += $le;
				$mygames[1][4] += $aw;
				// Gestion des bans
				if ($g->ban1 != '') {
					if (!array_key_exists($g->ban1, $mybans)) {
						$mybans[$g->ban1] = array(0, 0, 0, 0, 0, $g->ban1);
					}
					$mybans[$g->ban1][0] += 1;
					$mybans[$g->ban1][1] += $wi;
					$mybans[$g->ban1][2] += $lo;
					$mybans[$g->ban1][3] += $le;
					$mybans[$g->ban1][4] += $aw;
				}
				if ($g->ban3 != '') {
					if (!array_key_exists($g->ban3, $mybans)) {
						$mybans[$g->ban3] = array(0, 0, 0, 0, 0, $g->ban3);
					}
					$mybans[$g->ban3][0] += 1;
					$mybans[$g->ban3][1] += $wi;
					$mybans[$g->ban3][2] += $lo;
					$mybans[$g->ban3][3] += $le;
					$mybans[$g->ban3][4] += $aw;
				}
			} else if ($g->cap2 == $player) {
				// Compteur Capitaine global
				$mygames[1][0] += 1;
				$mygames[1][1] += $wi;
				$mygames[1][2] += $lo;
				$mygames[1][3] += $le;
				$mygames[1][4] += $aw;
				if ($g->ban2 != '') {
					if (!array_key_exists($g->ban2, $mybans)) {
						$mybans[$g->ban2] = array(0, 0, 0, 0, 0, $g->ban2);
					}
					$mybans[$g->ban2][0] += 1;
					$mybans[$g->ban2][1] += $wi;
					$mybans[$g->ban2][2] += $lo;
					$mybans[$g->ban2][3] += $le;
					$mybans[$g->ban2][4] += $aw;
				}
				if ($g->ban4 != '') {
					if (!array_key_exists($g->ban4, $mybans)) {
						$mybans[$g->ban4] = array(0, 0, 0, 0, 0, $g->ban4);
					}
					$mybans[$g->ban4][0] += 1;
					$mybans[$g->ban4][1] += $wi;
					$mybans[$g->ban4][2] += $lo;
					$mybans[$g->ban4][3] += $le;
					$mybans[$g->ban4][4] += $aw;
				}
			}
		}
		
		$picked = array(
			'Parties Jou&eacute;es',
			'Capitaine',
			'1<sup>st</sup> picked',
			'2<sup>nd</sup> picked',
			'3<sup>rd</sup> picked',
			'4<sup>th</sup> picked',
			'Last picked'
		);

		function arrayComparer($a, $b) {
			if ($a[0] == $b[0]) {
				if ($a[1] == $b[1]) {
					if ($a[2] == $b[2]) {
						if ($a[3] == $b[3]) {
							if ($a[4] == $b[4]) {
								return strcmp(strtolower($a[5]), strtolower($b[5]));
							}
							return ($a[4] < $b[4]) ? 1 : -1;
						}
						return ($a[3] < $b[3]) ? 1 : -1;
					}
					return ($a[2] < $b[2]) ? 1 : -1;
				}
				return ($a[1] < $b[1]) ? 1 : -1;
			}
			return ($a[0] < $b[0]) ? 1 : -1;
		}

		usort($mybans, "arrayComparer");

		echo '<table style="width: 96%; margin: 0px 2%;">';
		echo '<colgroup><col /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="50" /></colgroup>';
		echo '<tr><td colspan="6">&nbsp;</td></tr>';
		echo '<tr><td align="left" colspan="6"><strong>Ladder VIP</strong></td></tr>';
		echo '<tr><td class="line" colspan="6"></td></tr>';
		$mod = 0;
		$l = count($picked);
		for ($i = 0; $i < $l; $i++) {
			$mod += 1;
			$css = ($mod % 2 == 0) ? ' class="alternate"' : '';
			echo '<tr>';
			echo '<td style="text-align: left; cursor: default"'.$css.'>'.$picked[$i].'</td>';
			echo '<td style="text-align: right; cursor: default" title="Total"'.$css.'><strong>'.$mygames[$i][0].'</strong></td>';
			$tit = ($mygames[$i][0] == 0) ? 'Wins' : 'Wins : '.round(100 * $mygames[$i][1] / $mygames[$i][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="win">'.$mygames[$i][1].'</span></td>';
			$tit = ($mygames[$i][0] == 0) ? 'Loses' : 'Loses : '.round(100 * $mygames[$i][2] / $mygames[$i][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="lose">'.$mygames[$i][2].'</span></td>';
			$tit = ($mygames[$i][0] == 0) ? 'Lefts' : 'Lefts : '.round(100 * $mygames[$i][3] / $mygames[$i][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="draw">'.$mygames[$i][3].'</span></td>';
			$tit = ($mygames[$i][0] == 0) ? 'Aways' : 'Aways : '.round(100 * $mygames[$i][4] / $mygames[$i][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="info">'.$mygames[$i][4].'</span></td>';
			echo '</tr>';
		}
		echo '<tr><td colspan="6">&nbsp;</td></tr>';
		echo '<tr><td align="left" colspan="6"><strong>CAPITAINE : Quels heros je ban ?</strong></td></tr>';
		echo '<tr><td class="line" colspan="6"></td></tr>';
		$mod = 0;
		foreach ($mybans AS $mb) {
			$mod += 1;
			$css = ($mod % 2 == 0) ? ' class="alternate"' : '';
			echo '<tr>';
			echo '<td style="text-align: left; cursor: default"'.$css.'><img src="/ligue/img/heroes/mini/'.$mb[5].'.gif" title="'.$mb[5].'" alt="" align="absmiddle" />&nbsp;'.$mb[5].'</td>';
			echo '<td style="text-align: right; cursor: default" title="Total"'.$css.'><strong>'.$mb[0].'</strong></td>';
			$tit = ($mb[0] == 0) ? 'Wins' : 'Wins : '.round(100 * $mb[1] / $mb[0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="win">'.$mb[1].'</span></td>';
			$tit = ($mb[0] == 0) ? 'Loses' : 'Loses : '.round(100 * $mb[2] / $mb[0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="lose">'.$mb[2].'</span></td>';
			$tit = ($mb[0] == 0) ? 'Lefts' : 'Lefts : '.round(100 * $mb[3] / $mb[0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="draw">'.$mb[3].'</span></td>';
			$tit = ($mb[0] == 0) ? 'Aways' : 'Aways : '.round(100 * $mb[4] / $mb[0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="info">'.$mb[4].'</span></td>';
			echo '</tr>';
		}

/*
		$follows = array();
		$req = "
			SELECT DISTINCTROW 
			 game_id,
			 xp,
			 resultat
			FROM
			 lg_laddervip_follow
			WHERE
			 player = '".$player."'
			AND xp != 0";
		$qry = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($qry) > 0) {
			while ($o = mysql_fetch_object($qry)) $follows[] = $o;
		}

		$captains = array();
		$players = array();
		$captains_values = array();
		$players_values = array();
		$l = count($games);
		for ($i = 0; $i < $l; $i++) {
			if ($games[$i]->cap1 != $player AND $games[$i]->cap2 != $player) {
				$as = array_search($games[$i]->cap1, $captains);
				if ($as === false AND (
					$games[$i]->pp1 == $player
					OR $games[$i]->pp4 == $player
					OR $games[$i]->pp5 == $player
					OR $games[$i]->pp8 == $player
				)) {
					$gamesWith = getGamesIdByCaptainAndPlayer($games, $games[$i]->cap1, $player);
					$captains_values[] = array($games[$i]->cap1, getTotalsByGames($follows, $gamesWith));
					$captains[] = $games[$i]->cap1;
				}
				$as = array_search($games[$i]->cap2, $captains);
				if ($as === false AND (
					$games[$i]->pp2 == $player
					OR $games[$i]->pp3 == $player
					OR $games[$i]->pp6 == $player
					OR $games[$i]->pp7 == $player
				)) {
					$gamesWith = getGamesIdByCaptainAndPlayer($games, $games[$i]->cap2, $player);
					$captains_values[] = array($games[$i]->cap2, getTotalsByGames($follows, $gamesWith));
					$captains[] = $games[$i]->cap2;
				}
			} else if ($games[$i]->cap1 == $player) {
				$as = array_search($games[$i]->pp1, $players);
				if ($as === false) {
					$gamesWith = getGamesIdByCaptainAndPlayer($games, $player, $games[$i]->pp1);
					$players_values[] = array($games[$i]->pp1, getTotalsByGames($follows, $gamesWith));
					$players[] = $games[$i]->pp1;
				}
				$as = array_search($games[$i]->pp4, $players);
				if ($as === false) {
					$gamesWith = getGamesIdByCaptainAndPlayer($games, $player, $games[$i]->pp4);
					$players_values[] = array($games[$i]->pp4, getTotalsByGames($follows, $gamesWith));
					$players[] = $games[$i]->pp4;
				}
				$as = array_search($games[$i]->pp5, $players);
				if ($as === false) {
					$gamesWith = getGamesIdByCaptainAndPlayer($games, $player, $games[$i]->pp5);
					$players_values[] = array($games[$i]->pp5, getTotalsByGames($follows, $gamesWith));
					$players[] = $games[$i]->pp5;
				}
				$as = array_search($games[$i]->pp8, $players);
				if ($as === false) {
					$gamesWith = getGamesIdByCaptainAndPlayer($games, $player, $games[$i]->pp8);
					$players_values[] = array($games[$i]->pp8, getTotalsByGames($follows, $gamesWith));
					$players[] = $games[$i]->pp8;
				}
			} else if ($games[$i]->cap2 == $player) {
				$as = array_search($games[$i]->pp2, $players);
				if ($as === false) {
					$gamesWith = getGamesIdByCaptainAndPlayer($games, $player, $games[$i]->pp2);
					$players_values[] = array($games[$i]->pp2, getTotalsByGames($follows, $gamesWith));
					$players[] = $games[$i]->pp2;
				}
				$as = array_search($games[$i]->pp3, $players);
				if ($as === false) {
					$gamesWith = getGamesIdByCaptainAndPlayer($games, $player, $games[$i]->pp3);
					$players_values[] = array($games[$i]->pp3, getTotalsByGames($follows, $gamesWith));
					$players[] = $games[$i]->pp3;
				}
				$as = array_search($games[$i]->pp6, $players);
				if ($as === false) {
					$gamesWith = getGamesIdByCaptainAndPlayer($games, $player, $games[$i]->pp6);
					$players_values[] = array($games[$i]->pp6, getTotalsByGames($follows, $gamesWith));
					$players[] = $games[$i]->pp6;
				}
				$as = array_search($games[$i]->pp7, $players);
				if ($as === false) {
					$gamesWith = getGamesIdByCaptainAndPlayer($games, $player, $games[$i]->pp7);
					$players_values[] = array($games[$i]->pp7, getTotalsByGames($follows, $gamesWith));
					$players[] = $games[$i]->pp7;
				}
			}
		}

		function arrayComparer($a, $b) {
			if ($a[1][0] == $b[1][0]) {
				if ($a[1][1] == $b[1][1]) {
					if ($a[1][2] == $b[1][2]) {
						if ($a[1][3] == $b[1][3]) {
							if ($a[1][4] == $b[1][4]) {
								return strcmp(strtolower($a[0]), strtolower($b[0]));
							}
							return ($a[1][4] < $b[1][4]) ? 1 : -1;
						}
						return ($a[1][3] < $b[1][3]) ? 1 : -1;
					}
					return ($a[1][2] < $b[1][2]) ? 1 : -1;
				}
				return ($a[1][1] < $b[1][1]) ? 1 : -1;
			}
			return ($a[1][0] < $b[1][0]) ? 1 : -1;
		}

		usort($captains_values, "arrayComparer");
		
		usort($players_values, "arrayComparer");

		$picked = array(
			'Parties Jou&eacute;es',
			'Capitaine',
			'1<sup>st</sup> picked',
			'2<sup>nd</sup> picked',
			'3<sup>rd</sup> picked',
			'4<sup>th</sup> picked',
			'Last picked'
		);

		$positions = getGamesIdByPosition($games, $player);

		$totals = array(
			getTotalsByGames($follows, $positions[0]),
			getTotalsByGames($follows, $positions[1]),
			getTotalsByGames($follows, $positions[2]),
			getTotalsByGames($follows, $positions[3]),
			getTotalsByGames($follows, $positions[4]),
			getTotalsByGames($follows, $positions[5]),
			getTotalsByGames($follows, $positions[6])
		);

		echo '<table style="width: 96%; margin: 0px 2%;">';
		echo '<colgroup><col /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="50" /></colgroup>';
		echo '<tr><td colspan="6">&nbsp;</td></tr>';
		echo '<tr><td align="left" colspan="6"><strong>Ladder VIP</strong></td></tr>';
		echo '<tr><td class="line" colspan="6"></td></tr>';
		$mod = 0;
		$l = count($picked);
		for ($i = 0; $i < $l; $i++) {
			$mod += 1;
			$css = ($mod % 2 == 0) ? ' class="alternate"' : '';
			echo '<tr>';
			echo '<td style="text-align: left; cursor: default"'.$css.'>'.$picked[$i].'</td>';
			echo '<td style="text-align: right; cursor: default" title="Total"'.$css.'><strong>'.$totals[$i][0].'</strong></td>';
			$tit = ($totals[$i][0] == 0) ? 'Wins' : 'Wins : '.round(100 * $totals[$i][1] / $totals[$i][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="win">'.$totals[$i][1].'</span></td>';
			$tit = ($totals[$i][0] == 0) ? 'Loses' : 'Loses : '.round(100 * $totals[$i][2] / $totals[$i][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="lose">'.$totals[$i][2].'</span></td>';
			$tit = ($totals[$i][0] == 0) ? 'Lefts' : 'Lefts : '.round(100 * $totals[$i][3] / $totals[$i][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="draw">'.$totals[$i][3].'</span></td>';
			$tit = ($totals[$i][0] == 0) ? 'Aways' : 'Aways : '.round(100 * $totals[$i][4] / $totals[$i][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="info">'.$totals[$i][4].'</span></td>';
			echo '</tr>';
		}
//		$img = 'http://chart.apis.google.com/chart?cht=p3&chd=t:';
//		$img .= $totals[1][0].',';
//		$img .= $totals[2][0].',';
//		$img .= $totals[3][0].',';
//		$img .= $totals[4][0].',';
//		$img .= $totals[5][0].',';
//		$img .= $totals[6][0].'&chs=450x150&chl=capitaine|1st%20picked|2nd%20picked|3rd%20picked|4th%20picked|last%20picked&chco=0066ff&chf=bg,s,000000';
//		echo '<tr><td colspan="6">&nbsp;</td></tr>';
//		echo '<tr><td colspan="6" style="text-align: center"><img src="'.$img.'" alt="" /></td></tr>';
//		echo '<tr><td colspan="6">&nbsp;</td></tr>';
		echo '<tr><td colspan="6">&nbsp;</td></tr>';
		echo '<tr><td align="left" colspan="6"><strong>MODE JOUEUR : Qui me pick ?</strong></td></tr>';
		echo '<tr><td class="line" colspan="6"></td></tr>';
		$mod = 0;
		$l = count($captains_values);
		for ($i = 0; $i < $l; $i++) {
			$mod += 1;
			$css = ($mod % 2 == 0) ? ' class="alternate"' : '';
			echo '<tr>';
			echo '<td style="text-align: left; cursor: default"'.$css.'><a href="?f=player_profile&player='.$captains_values[$i][0].'">'.$captains_values[$i][0].'</a></td>';
			echo '<td style="text-align: right; cursor: default" title="Total"'.$css.'><strong>'.$captains_values[$i][1][0].'</strong></td>';
			$tit = ($captains_values[$i][1][0] == 0) ? 'Wins' : 'Wins : '.round(100 * $captains_values[$i][1][1] / $captains_values[$i][1][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="win">'.$captains_values[$i][1][1].'</span></td>';
			$tit = ($captains_values[$i][1][0] == 0) ? 'Loses' : 'Loses : '.round(100 * $captains_values[$i][1][2] / $captains_values[$i][1][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="lose">'.$captains_values[$i][1][2].'</span></td>';
			$tit = ($captains_values[$i][1][0] == 0) ? 'Lefts' : 'Lefts : '.round(100 * $captains_values[$i][1][3] / $captains_values[$i][1][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="draw">'.$captains_values[$i][1][3].'</span></td>';
			$tit = ($captains_values[$i][1][0] == 0) ? 'Aways' : 'Aways : '.round(100 * $captains_values[$i][1][4] / $captains_values[$i][1][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="info">'.$captains_values[$i][1][4].'</span></td>';
			echo '</tr>';
		}
		echo '<tr><td colspan="6">&nbsp;</td></tr>';
		echo '<tr><td align="left" colspan="6"><strong>MODE CAPITAINE : Qui je pick ?</strong></td></tr>';
		echo '<tr><td class="line" colspan="6"></td></tr>';
		$mod = 0;
		$l = count($players_values);
		for ($i = 0; $i < $l; $i++) {
			$mod += 1;
			$css = ($mod % 2 == 0) ? ' class="alternate"' : '';
			echo '<tr>';
			echo '<td style="text-align: left; cursor: default"'.$css.'><a href="?f=player_profile&player='.$players_values[$i][0].'">'.$players_values[$i][0].'</a></td>';
			echo '<td style="text-align: right; cursor: default" title="Total"'.$css.'><strong>'.$players_values[$i][1][0].'</strong></td>';
			$tit = ($players_values[$i][1][0] == 0) ? 'Wins' : 'Wins : '.round(100 * $players_values[$i][1][1] / $players_values[$i][1][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="win">'.$players_values[$i][1][1].'</span></td>';
			$tit = ($players_values[$i][1][0] == 0) ? 'Loses' : 'Loses : '.round(100 * $players_values[$i][1][2] / $players_values[$i][1][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="lose">'.$players_values[$i][1][2].'</span></td>';
			$tit = ($players_values[$i][1][0] == 0) ? 'Lefts' : 'Lefts : '.round(100 * $players_values[$i][1][3] / $players_values[$i][1][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="draw">'.$players_values[$i][1][3].'</span></td>';
			$tit = ($players_values[$i][1][0] == 0) ? 'Aways' : 'Aways : '.round(100 * $players_values[$i][1][4] / $players_values[$i][1][0], 2).'%';
			echo '<td style="text-align: right; cursor: default" title="'.$tit.'"'.$css.'><span class="info">'.$players_values[$i][1][4].'</span></td>';
			echo '</tr>';
		}
		echo '<tr><td colspan="6">&nbsp;</td></tr>';
		echo '<tr><td align="left" colspan="6"><strong>MODE CAPITAINE : Quels heros je pick ?</strong></td></tr>';
		echo '<tr><td class="line" colspan="6"></td></tr>';
		echo '</table>';

*/
		
	}

?>