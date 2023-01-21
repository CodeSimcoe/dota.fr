<?php
	//Page appelee par AJAX
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'classes/MatchStates.php';
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';

	function heroes_sort($a, $b) {
		if ($a['count'] == $b['count']) {
			if ($a['hero'] == $b['hero']) return 0;
			return ($a['hero'] < $b['hero']) ? -1 : 1;
		}
		return ($a['count'] < $b['count']) ? 1 : -1;
	}

	if (isset($_GET['player'])) {
		$heroes = array();
		$player = substr($_GET['player'], 0, 25);
		$played = 0; $wins = 0; $loses = 0;
		$req = "
			SELECT 
				CASE 
					WHEN T1.p1 = '".mysql_real_escape_string($player)."' THEN T1.p1
					WHEN T1.p2 = '".mysql_real_escape_string($player)."' THEN T1.p2
					WHEN T1.p3 = '".mysql_real_escape_string($player)."' THEN T1.p3
					WHEN T1.p4 = '".mysql_real_escape_string($player)."' THEN T1.p4
					WHEN T1.p5 = '".mysql_real_escape_string($player)."' THEN T1.p5
					WHEN T1.p1r2 = '".mysql_real_escape_string($player)."' THEN T1.p1r2
					WHEN T1.p2r2 = '".mysql_real_escape_string($player)."' THEN T1.p2r2
					WHEN T1.p3r2 = '".mysql_real_escape_string($player)."' THEN T1.p3r2
					WHEN T1.p4r2 = '".mysql_real_escape_string($player)."' THEN T1.p4r2
					WHEN T1.p5r2 = '".mysql_real_escape_string($player)."' THEN T1.p5r2
				END AS 'username',
				CASE 
					WHEN T1.p1 = '".mysql_real_escape_string($player)."' THEN H1.name
					WHEN T1.p2 = '".mysql_real_escape_string($player)."' THEN H2.name
					WHEN T1.p3 = '".mysql_real_escape_string($player)."' THEN H3.name
					WHEN T1.p4 = '".mysql_real_escape_string($player)."' THEN H4.name
					WHEN T1.p5 = '".mysql_real_escape_string($player)."' THEN H5.name
					WHEN T1.p1r2 = '".mysql_real_escape_string($player)."' THEN H1r2.name
					WHEN T1.p2r2 = '".mysql_real_escape_string($player)."' THEN H2r2.name
					WHEN T1.p3r2 = '".mysql_real_escape_string($player)."' THEN H3r2.name
					WHEN T1.p4r2 = '".mysql_real_escape_string($player)."' THEN H4r2.name
					WHEN T1.p5r2 = '".mysql_real_escape_string($player)."' THEN H5r2.name
				END AS 'hero',
				CASE 
					WHEN T1.p1 = '".mysql_real_escape_string($player)."' THEN H1.image
					WHEN T1.p2 = '".mysql_real_escape_string($player)."' THEN H2.image
					WHEN T1.p3 = '".mysql_real_escape_string($player)."' THEN H3.image
					WHEN T1.p4 = '".mysql_real_escape_string($player)."' THEN H4.image
					WHEN T1.p5 = '".mysql_real_escape_string($player)."' THEN H5.image
					WHEN T1.p1r2 = '".mysql_real_escape_string($player)."' THEN H1r2.image
					WHEN T1.p2r2 = '".mysql_real_escape_string($player)."' THEN H2r2.image
					WHEN T1.p3r2 = '".mysql_real_escape_string($player)."' THEN H3r2.image
					WHEN T1.p4r2 = '".mysql_real_escape_string($player)."' THEN H4r2.image
					WHEN T1.p5r2 = '".mysql_real_escape_string($player)."' THEN H5r2.image
				END AS 'img',
				T1.team1,
				T1.team2,
				CASE
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p1, T1.p2, T1.p3, T1.p4, T1.p5) THEN C2.name
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p1r2, T1.p2r2, T1.p3r2, T1.p4r2, T1.p5r2) THEN C1.name
				END AS 'opp_name',
				CASE
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p1, T1.p2, T1.p3, T1.p4, T1.p5) THEN C2.tag
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p1r2, T1.p2r2, T1.p3r2, T1.p4r2, T1.p5r2) THEN C1.tag
				END AS 'opp_tag',
				CASE
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p1, T1.p2, T1.p3, T1.p4, T1.p5) THEN CASE WHEN T1.etat IN (".MatchStates::TEAM_ONE_REGULAR_WIN.", ".MatchStates::DRAW_REGULAR_SENTINEL.", ".MatchStates::TEAM_ONE_WINS_WITH_SCOURGE_DEFWIN.") THEN 'win' ELSE 'lose' END
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p1r2, T1.p2r2, T1.p3r2, T1.p4r2, T1.p5r2) THEN CASE WHEN T1.etat IN (".MatchStates::TEAM_TWO_REGULAR_WIN.", ".MatchStates::DRAW_REGULAR_SENTINEL.", ".MatchStates::TEAM_TWO_WINS_WITH_SCOURGE_DEFWIN.") THEN 'win' ELSE 'lose' END
				END AS 'result'
			FROM lg_matchs AS T1
			LEFT JOIN parser_heroes AS H1 ON (H1.name = T1.h1 AND H1.version = T1.version1)
			LEFT JOIN parser_heroes AS H2 ON (H2.name = T1.h2 AND H2.version = T1.version1)
			LEFT JOIN parser_heroes AS H3 ON (H3.name = T1.h3 AND H3.version = T1.version1)
			LEFT JOIN parser_heroes AS H4 ON (H4.name = T1.h4 AND H4.version = T1.version1)
			LEFT JOIN parser_heroes AS H5 ON (H5.name = T1.h5 AND H5.version = T1.version1)
			LEFT JOIN parser_heroes AS H1r2 ON (H1r2.name = T1.h1r2 AND H1r2.version = T1.version2)
			LEFT JOIN parser_heroes AS H2r2 ON (H2r2.name = T1.h2r2 AND H2r2.version = T1.version2)
			LEFT JOIN parser_heroes AS H3r2 ON (H3r2.name = T1.h3r2 AND H3r2.version = T1.version2)
			LEFT JOIN parser_heroes AS H4r2 ON (H4r2.name = T1.h4r2 AND H4r2.version = T1.version2)
			LEFT JOIN parser_heroes AS H5r2 ON (H5r2.name = T1.h5r2 AND H5r2.version = T1.version2)
			LEFT JOIN lg_clans AS C1 ON C1.id = T1.team1
			LEFT JOIN lg_clans AS C2 ON C2.id = T1.team2
			WHERE '".mysql_real_escape_string($player)."' IN (T1.p1, T1.p2, T1.p3, T1.p4, T1.p5, T1.p1r2, T1.p2r2, T1.p3r2, T1.p4r2, T1.p5r2)
			ORDER BY T1.id";
		$res = mysql_query($req) or die(mysql_error());
		echo '<table class="listing parser center" border="0" cellpadding="0" cellspacing="0">';
		echo '<colgroup><col width="40" /><col /><col width="300" /><col width="80" /></colgroup>';
		echo '<caption style="text-align: center;"><img src="/ligue/img/sentinel.png" title="'.Lang::SENTINEL.'" /></caption>';
		echo '<thead><tr><th>&nbsp;</th><th>'.Lang::HERO.'</th><th>'.Lang::TEAM_OPPONENT.'</th><th>'.Lang::RESULT.'</th></tr></thead>';
		echo '<tbody>';
		if (mysql_num_rows($res) > 0) {
			while ($obj = mysql_fetch_object($res)) {
				echo '<tr style="height: 40px;">';
				echo '<td><img src="/ligue/parser/Images/'.$obj->img.'.png" alt="" width="32" title="'.$obj->hero.'" /></td>';
				echo '<td>'.htmlentities($obj->hero).'</td>';
				echo '<td><a href="?f=match&team1='.$obj->team1.'&team2='.$obj->team2.'">'.htmlentities($obj->opp_name).' ['.htmlentities($obj->opp_tag).']</a></td>';
				echo '<td><span class="'.$obj->result.'">'.($obj->result == 'win' ? Lang::WIN : Lang::LOSS).'</span></td>';
				echo '</tr>';
				$played += 1;
				$wins += ($obj->result == 'win' ? 1 : 0);
				$loses += ($obj->result == 'lose' ? 1 : 0);
				if (isset($heroes[$obj->hero])) $heroes[$obj->hero]['count'] += 1;
				else $heroes[$obj->hero] = array('hero' => $obj->hero, 'count' => 1);
			}
		}
		echo '</tbody></table>';
		echo '<br /><br /><br />';
		$req = "
			SELECT 
				CASE 
					WHEN T1.p6 = '".mysql_real_escape_string($player)."' THEN T1.p6
					WHEN T1.p7 = '".mysql_real_escape_string($player)."' THEN T1.p7
					WHEN T1.p8 = '".mysql_real_escape_string($player)."' THEN T1.p8
					WHEN T1.p9 = '".mysql_real_escape_string($player)."' THEN T1.p9
					WHEN T1.p10 = '".mysql_real_escape_string($player)."' THEN T1.p10
					WHEN T1.p6r2 = '".mysql_real_escape_string($player)."' THEN T1.p6r2
					WHEN T1.p7r2 = '".mysql_real_escape_string($player)."' THEN T1.p7r2
					WHEN T1.p8r2 = '".mysql_real_escape_string($player)."' THEN T1.p8r2
					WHEN T1.p9r2 = '".mysql_real_escape_string($player)."' THEN T1.p9r2
					WHEN T1.p10r2 = '".mysql_real_escape_string($player)."' THEN T1.p10r2
				END AS 'username',
				CASE 
					WHEN T1.p6 = '".mysql_real_escape_string($player)."' THEN H1.name
					WHEN T1.p7 = '".mysql_real_escape_string($player)."' THEN H2.name
					WHEN T1.p8 = '".mysql_real_escape_string($player)."' THEN H3.name
					WHEN T1.p9 = '".mysql_real_escape_string($player)."' THEN H4.name
					WHEN T1.p10 = '".mysql_real_escape_string($player)."' THEN H5.name
					WHEN T1.p6r2 = '".mysql_real_escape_string($player)."' THEN H1r2.name
					WHEN T1.p7r2 = '".mysql_real_escape_string($player)."' THEN H2r2.name
					WHEN T1.p8r2 = '".mysql_real_escape_string($player)."' THEN H3r2.name
					WHEN T1.p9r2 = '".mysql_real_escape_string($player)."' THEN H4r2.name
					WHEN T1.p10r2 = '".mysql_real_escape_string($player)."' THEN H5r2.name
				END AS 'hero',
				CASE 
					WHEN T1.p6 = '".mysql_real_escape_string($player)."' THEN H1.image
					WHEN T1.p7 = '".mysql_real_escape_string($player)."' THEN H2.image
					WHEN T1.p8 = '".mysql_real_escape_string($player)."' THEN H3.image
					WHEN T1.p9 = '".mysql_real_escape_string($player)."' THEN H4.image
					WHEN T1.p10 = '".mysql_real_escape_string($player)."' THEN H5.image
					WHEN T1.p6r2 = '".mysql_real_escape_string($player)."' THEN H1r2.image
					WHEN T1.p7r2 = '".mysql_real_escape_string($player)."' THEN H2r2.image
					WHEN T1.p8r2 = '".mysql_real_escape_string($player)."' THEN H3r2.image
					WHEN T1.p9r2 = '".mysql_real_escape_string($player)."' THEN H4r2.image
					WHEN T1.p10r2 = '".mysql_real_escape_string($player)."' THEN H5r2.image
				END AS 'img',
				T1.team1,
				T1.team2,
				CASE
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p6, T1.p7, T1.p8, T1.p9, T1.p10) THEN C1.name
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p6r2, T1.p7r2, T1.p8r2, T1.p9r2, T1.p10r2) THEN C2.name
				END AS 'opp_name',
				CASE
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p6, T1.p7, T1.p8, T1.p9, T1.p10) THEN C1.tag
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p6r2, T1.p7r2, T1.p8r2, T1.p9r2, T1.p10r2) THEN C2.tag
				END AS 'opp_tag',
				CASE
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p6, T1.p7, T1.p8, T1.p9, T1.p10) THEN CASE WHEN T1.etat IN (".MatchStates::TEAM_TWO_REGULAR_WIN.", ".MatchStates::DRAW_REGULAR_SCOURGE.", ".MatchStates::TEAM_TWO_WINS_WITH_SENTINEL_DEFWIN.") THEN 'win' ELSE 'lose' END
					WHEN '".mysql_real_escape_string($player)."' IN (T1.p6r2, T1.p7r2, T1.p8r2, T1.p9r2, T1.p10r2) THEN CASE WHEN T1.etat IN (".MatchStates::TEAM_ONE_REGULAR_WIN.", ".MatchStates::DRAW_REGULAR_SCOURGE.", ".MatchStates::TEAM_ONE_WINS_WITH_SENTINEL_DEFWIN.") THEN 'win' ELSE 'lose' END
				END AS 'result'
			FROM lg_matchs AS T1
			LEFT JOIN parser_heroes AS H1 ON (H1.name = T1.h6 AND H1.version = T1.version1)
			LEFT JOIN parser_heroes AS H2 ON (H2.name = T1.h7 AND H2.version = T1.version1)
			LEFT JOIN parser_heroes AS H3 ON (H3.name = T1.h8 AND H3.version = T1.version1)
			LEFT JOIN parser_heroes AS H4 ON (H4.name = T1.h9 AND H4.version = T1.version1)
			LEFT JOIN parser_heroes AS H5 ON (H5.name = T1.h10 AND H5.version = T1.version1)
			LEFT JOIN parser_heroes AS H1r2 ON (H1r2.name = T1.h6r2 AND H1r2.version = T1.version2)
			LEFT JOIN parser_heroes AS H2r2 ON (H2r2.name = T1.h7r2 AND H2r2.version = T1.version2)
			LEFT JOIN parser_heroes AS H3r2 ON (H3r2.name = T1.h8r2 AND H3r2.version = T1.version2)
			LEFT JOIN parser_heroes AS H4r2 ON (H4r2.name = T1.h9r2 AND H4r2.version = T1.version2)
			LEFT JOIN parser_heroes AS H5r2 ON (H5r2.name = T1.h10r2 AND H5r2.version = T1.version2)
			LEFT JOIN lg_clans AS C1 ON C1.id = T1.team1
			LEFT JOIN lg_clans AS C2 ON C2.id = T1.team2
			WHERE '".mysql_real_escape_string($player)."' IN (T1.p6, T1.p7, T1.p8, T1.p9, T1.p10, T1.p6r2, T1.p7r2, T1.p8r2, T1.p9r2, T1.p10r2)
			ORDER BY T1.id";
		$res = mysql_query($req) or die(mysql_error());
		echo '<table class="listing parser center" border="0" cellpadding="0" cellspacing="0">';
		echo '<colgroup><col width="40" /><col /><col width="300" /><col width="80" /></colgroup>';
		echo '<caption style="text-align: center;"><img src="/ligue/img/scourge.png" title="'.Lang::SCOURGE.'" /></caption>';
		echo '<thead><tr><th>&nbsp;</th><th>'.Lang::HERO.'</th><th>'.Lang::TEAM_OPPONENT.'</th><th>'.Lang::RESULT.'</th></tr></thead>';
		echo '<tbody>';
		if (mysql_num_rows($res) > 0) {
			while ($obj = mysql_fetch_object($res)) {
				echo '<tr style="height: 40px;">';
				echo '<td><img src="/ligue/parser/Images/'.$obj->img.'.png" alt="" width="32" title="'.$obj->hero.'" /></td>';
				echo '<td>'.htmlentities($obj->hero).'</td>';
				echo '<td><a href="?f=match&team1='.$obj->team1.'&team2='.$obj->team2.'">'.htmlentities($obj->opp_name).' ['.htmlentities($obj->opp_tag).']</a></td>';
				echo '<td><span class="'.$obj->result.'">'.($obj->result == 'win' ? Lang::WIN : Lang::LOSS).'</span></td>';
				echo '</tr>';
				$played += 1;
				$wins += ($obj->result == 'win' ? 1 : 0);
				$loses += ($obj->result == 'lose' ? 1 : 0);
				if (isset($heroes[$obj->hero])) $heroes[$obj->hero]['count'] += 1;
				else $heroes[$obj->hero] = array('hero' => $obj->hero, 'count' => 1);
			}
		}
		echo '</tbody></table>';
		echo '<br /><br /><br />';
		echo '<table class="listing parser center" border="0" cellpadding="0" cellspacing="0">';
		echo '<colgroup><col width="40" /><col width="200" /><col /></colgroup>';
		echo '<caption style="text-align: center;"><img src="/ligue/img/lang/'.ArghSession::get_lang().'/recap.png" title="'.htmlentities(Lang::RECAP).'" /></caption>';
		echo '<tbody>';
		echo '<tr><td>&nbsp;</td><td valign="top">Manches jou&eacute;es</td><td valign="top"><strong>'.$played.'</strong></td></tr>';
		echo '<tr><td>&nbsp;</td><td valign="top">Manches remport&eacute;es</td><td valign="top"><strong>'.$wins.'</strong>'.($played == 0 ? '' : '&nbsp;<span class="info"><i>'.round(100 * $wins / $played, 2).'%</i></span>').'</td></tr>';
		echo '<tr><td>&nbsp;</td><td valign="top">Manches perdues</td><td valign="top"><strong>'.$loses.'</strong>'.($played == 0 ? '' : '&nbsp;<span class="info"><i>'.round(100 * $loses / $played, 2).'%</i></span>').'</td></tr>';
		if (count($heroes) > 0) {
			uasort($heroes, 'heroes_sort');
			echo '<tr>';
			echo '<td>&nbsp;</td>';
			echo '<td valign="top">H&eacute;ros jou&eacute;s</td>';
			echo '<td valign="top">';
			foreach ($heroes AS $hero => $stats) {
				echo $stats['count'].' x '.$stats['hero'].'&nbsp;<span class="info"><i>'.round(100 * $stats['count'] / $played, 2).'%</i></span><br />';
			}
			echo '</td>';
			echo '</tr>';
		}
		$req = "
			SELECT 
				SUM(note) / COUNT(pour_qui) AS 'total',
				COUNT(pour_qui) AS 'votes'
			FROM lg_ranks
			WHERE pour_qui='".mysql_real_escape_string($player)."'
			GROUP BY pour_qui
			ORDER BY total DESC
			LIMIT 1";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) > 0) {
			$obj = mysql_fetch_object($res);
			$appreciation = $obj->total;
			$votes = ($obj->votes > 1) ? $obj->votes.'&nbsp;votes' : $obj->votes.'&nbsp;vote';
			echo '<tr><td colspan="3"></td></tr>';
			echo '<tr><td>&nbsp;</td><td>Appr&eacute;ciation</td><td><strong>'.(round($appreciation * 100, 0) / 100).'</strong>&nbsp;<span class="info"><i>'.$votes.'</i></span></td></tr>';
		}
		echo '</tbody></table>';
	}

?>