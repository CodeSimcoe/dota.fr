<?php

require_once '/home/www/dota/classes/CacheManager.php';

abstract class LadderManager
{

	public static function AddPlayerToCache($cache, $username, $xp, $ggc, $rights) {
		$handle = fopen($cache, 'a');
		$content = $username.';'.$xp.';'.$ggc.';'.time().';'.$rights."\n";
		fwrite($handle, $content);
		fclose($handle);
	}
	public static function RemovePlayerFromCache($cache, $username) {
		$content = file($cache);
		$handle = fopen($cache, 'w+');
		$removed = false;
		foreach ($content as $val) {
			$line = explode(';', $val);
			if ($line[0] != $username) fwrite($handle, $val);
			else $removed = true;
		}
		fclose($handle);
		return $removed;
	}
	public static function GetPlayersFromCache($cache) {
		$players = array();
		$content = file($cache);
		foreach ($content as $val) {
			$line = explode(';', $val);
			if (count($line) == 5) $players = array_merge($players, array($line));
		}
		return $players;
	}
	public static function ClearCache($cache) {
		$handle = fopen($cache, 'w');
		fwrite($handle, '');
		fclose($handle);
	}
	public static function UpdatePlayersStatus($players, $status) {
		$players = array_map('mysql_real_escape_string', $players);
		$req = mysql_query("UPDATE lg_users SET ladder_status = '".$status."' WHERE username IN ('".implode("', '", $players)."')") or die(mysql_error());
	}
	public static function AddPlayerCredit($username) {
		$username = mysql_real_escape_string($username);
		$req = mysql_query("UPDATE lg_users SET daily_games = daily_games + 1 WHERE username = '".$username."'") or die(mysql_error());
	}
	public static function RemovePlayerCredit($username) {
		$username = mysql_real_escape_string($username);
		$req = mysql_query("UPDATE lg_users SET daily_games = daily_games - 1 WHERE username = '".$username."'") or die(mysql_error());
	}
	public static function GetPointsFromDatabase($players) {
		$players = array_map('mysql_real_escape_string', $players);
		$db = array();
		$req = mysql_query("SELECT username, pts FROM lg_users WHERE username IN ('".implode("', '", $players)."')") or die(mysql_error());
		if (mysql_num_rows($req) != 0) {
			while ($row = mysql_fetch_row($req)) {
				$db = array_merge($db, array($row));
			}
		}
		return $db;
	}
	public static function html_players_table($file, $username) {
		$html = '';
		if (file_exists($file)) {
			$content = file($file);
			$i = 0;
			$html .= '<table class="listing">';
			$html .= '<colgroup><col width="70" /><col /><col width="100" /><col width="200" /></colgroup>';
			$html .= '<thead><tr>';
			$html .= '<th>'.Lang::SLOT.'</th>';
			$html .= '<th>'.Lang::PLAYER.'</th>';
			$html .= '<th>'.Lang::XP.'</th>';
			$html .= '<th>'.Lang::GARENA_ACCOUNT.'</th>';
			$html .= '</tr></thead>';
			$html .= '<tbody>';
			foreach ($content as $val) {
				$line = explode(';', $val);
				if (count($line) == 5) {
					$i++;
					$bg = ($line[0] == $username) ? ' class="alternate"' : '';
					$icon = RightsMode::colorize_rights_mini_ladder($line[4]);
					if ($icon != '') $icon .= '&nbsp;';
					$html .= '<tr'.$bg.'>';
					$html .= '<td><i>'.$i.'.</i></td>';
					$html .= '<td>'.$icon.'<a href="?f=player_profile&amp;player='.urlencode($line[0]).'">'.htmlentities($line[0]).'</a></td>';
					$html .= '<td><b>'.XPColorize($line[1]).'</b></td>';
					$html .= '<td>'.htmlentities($line[2]).'</td>';
					$html .= '</tr>';
				}
			}
			$html .= '</tbody>';
			$html .= '</table>';
		}
		return $html;
	}
	public static function html_game_info($type, $game_id, $mode, $ts_channel) {
		$html = '';
		$html .= '<table class="listing">';
		$html .= '<colgroup><col width="15%" /><col width="35%" /><col /></colgroup>';
		$html .= '<tbody>';
		$html .= '<tr>';
		$html .= '<td><strong>'.Lang::GAME_ID.'</strong>:</td>';
		$html .= '<td>#'.$game_id.'</td>';
		$html .= '<td align="right" rowspan="3"><img src="ladder/btn_join.jpg" alt="Join" style="cursor:pointer;" id="join_'.$type.'" /></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td><strong>'.Lang::MODE.'</strong>:</td>';
		$html .= '<td><strong><span class="vip">'.$mode.'</span></strong></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td><strong>'.Lang::TEAMSPEAK_CHANNEL.'</strong>:</td>';
		$html .= '<td>:: '.Lang::LADDER.' - '.$ts_channel.' ::</td>';
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}

}

?>