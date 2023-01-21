<?php

define('BAD_WORDS_PATH', '/home/www/ligue/reports/words/bad_words.txt');
define("REPLAY_DEFINITIONS_IMAGES", "/ligue/parser/Images/");

abstract class ReplayFunctions {

	public static function convert_bool($value) {
		if (!$value) { return false; }
		return true;
	}
	public static function convert_color($value) {
		switch ($value) {
			case 0: $value = "red"; break;
			case 1: $value = "blue"; break;
			case 2: $value = "teal"; break;
			case 3: $value = "purple"; break;
			case 4: $value = "yellow"; break;
			case 5: $value = "orange"; break;
			case 6: $value = "green"; break;
			case 7: $value = "pink"; break;
			case 8: $value = "gray"; break;
			case 9: $value = "light-blue"; break;
			case 10: $value = "dark-green"; break;
			case 11: $value = "brown"; break;
			case 12: $value = "observer"; break;
		}
		return $value;
	}
	public static function convert_playerid($players, $slotid) {
		$color = self::convert_color($slotid);
		$player_id = -1;
		foreach ($players as $key => $value) {
			if ($value['color'] == $color) {
				$player_id = $value['player_id'];
				break;
			}
		}
		return $player_id;
	}
	public static function convert_heroitem($heroes, $hero) {
		$code = '';
		foreach ($heroes as $key => $value) {
			if ($value['hero'] == $hero) {
				$code = $value['code'];
				break;
			}
		}
		return $code;
	}
	public static function convert_chat_mode($value, $player = "unknown") {
		switch ($value) {
			case 0x00: $value = "All"; break;
			case 0x01: $value = "Allies"; break;
			case 0x02: $value = "Observers"; break;
			case 0xFE: $value = "The game has been paused by ".$player."."; break;
			case 0xFF: $value = "The game has been resumed by ".$player."."; break;
			default: $value -= 2;
		}
		return $value;
	}
	public static function convert_gamemode($value) {
		$modes = array();
		$value = strtolower($value);
		$value = str_replace("waterrandom", "", $value);
		$value = str_replace("water", "", $value);
		$value = str_replace("allpick", "ap", $value);
		$value = str_replace("allpick", "ap", $value);
		$value = str_replace("allrandom", "ar", $value);
		$value = str_replace("teamrandom", "tr", $value);
		$value = str_replace("moderandom", "mr", $value);
		$value = str_replace("singledraft", "sd", $value);
		$value = str_replace("leaguemode", "lm", $value);
		$value = str_replace("extendedleague", "xl", $value);
		$value = str_replace("randomdraft", "rd", $value);
		$value = str_replace("voterandom", "vr", $value);
		$value = str_replace("captainsmode", "cm", $value);
		$value = str_replace("deathmatch", "dm", $value);
		$value = str_replace("reverse", "rv", $value);
		$value = str_replace("mirrormatch", "mm", $value);
		$value = str_replace("duplicatemode", "du", $value);
		$value = str_replace("shuffleplayers", "sp", $value);
		$value = str_replace("samehero", "sh", $value);
		$value = str_replace("allagility", "aa", $value);
		$value = str_replace("allintelligence", "ai", $value);
		$value = str_replace("allstrength", "as", $value);
		$value = str_replace("itemdrop", "id", $value);
		$value = str_replace("poolingmode", "pm", $value);
		$value = str_replace("easymode", "em", $value);
		$value = str_replace("nopowerups", "np", $value);
		$value = str_replace("supercreeps", "sc", $value);
		$value = str_replace("miniheroes", "mi", $value);
		$value = str_replace("onlymid", "om", $value);
		$value = str_replace("notop", "nt", $value);
		$value = str_replace("nomid", "nm", $value);
		$value = str_replace("nobot", "nb", $value);
		$value = str_replace("noswap", "ns", $value);
		$value = str_replace("norepick", "nr", $value);
		$value = str_replace("terrainsnow", "ts", $value);
		$value = str_replace("observerinfo", "oi", $value);
		$value = str_replace("captainsdraft", "cd", $value);
		$value = str_replace("unban", "ub", $value);
		$value = str_replace("zoommode", "zm", $value);
		if (!(strpos($value, "ap") === false)) $modes["ap"] = "All Pick";
		if (!(strpos($value, "ar") === false)) $modes["ar"] = "All Random";
		if (!(strpos($value, "tr") === false)) $modes["tr"] = "Team Random";
		if (!(strpos($value, "mr") === false)) $modes["mr"] = "Mode Random";
		if (!(strpos($value, "sd") === false)) $modes["sd"] = "Single Draft";
		if (!(strpos($value, "lm") === false)) $modes["lm"] = "League Mode";
		if (!(strpos($value, "xl") === false)) $modes["xl"] = "Extended League";
		if (!(strpos($value, "rd") === false)) $modes["rd"] = "Random Draft";
		if (!(strpos($value, "vr") === false)) $modes["vr"] = "Vote Random";
		if (!(strpos($value, "cm") === false)) $modes["cm"] = "Captains Mode";
		if (!(strpos($value, "dm") === false)) $modes["dm"] = "Death Match";
		if (!(strpos($value, "rv") === false)) $modes["rv"] = "Reverse Mode";
		if (!(strpos($value, "mm") === false)) $modes["mm"] = "Mirror Match";
		if (!(strpos($value, "du") === false)) $modes["du"] = "Duplicate Mode";
		if (!(strpos($value, "sp") === false)) $modes["sp"] = "Shuffle Players";
		if (!(strpos($value, "sh") === false)) $modes["sh"] = "Same Hero";
		if (!(strpos($value, "aa") === false)) $modes["aa"] = "All Agility";
		if (!(strpos($value, "ai") === false)) $modes["ai"] = "All Intelligence";
		if (!(strpos($value, "as") === false)) $modes["as"] = "All Strength";
		if (!(strpos($value, "id") === false)) $modes["id"] = "Item Drop";
		if (!(strpos($value, "pm") === false)) $modes["pm"] = "Pooling Mode";
		if (!(strpos($value, "em") === false)) $modes["em"] = "Easy Mode";
		if (!(strpos($value, "np") === false)) $modes["np"] = "No Powerups";
		if (!(strpos($value, "sc") === false)) $modes["sc"] = "Super Creeps";
		if (!(strpos($value, "mi") === false)) $modes["mi"] = "Mini Heroes";
		if (!(strpos($value, "om") === false)) $modes["om"] = "Only Mid";
		if (!(strpos($value, "nt") === false)) $modes["nt"] = "No Top";
		if (!(strpos($value, "nm") === false)) $modes["nm"] = "No Mid";
		if (!(strpos($value, "nb") === false)) $modes["nb"] = "No Bottom";
		if (!(strpos($value, "ns") === false)) $modes["ns"] = "No Swap";
		if (!(strpos($value, "nr") === false)) $modes["nr"] = "No Repick";
		if (!(strpos($value, "ts") === false)) $modes["ts"] = "Terrain Snow";
		if (!(strpos($value, "oi") === false)) $modes["oi"] = "Observer Info";
		if (!(strpos($value, "cd") === false)) $modes["cd"] = "Captains Draft";
		if (!(strpos($value, "ub") === false)) $modes["ub"] = "Unban";
		if (!(strpos($value, "zm") === false)) $modes["zm"] = "Zoom Mode";
		return $modes;
	}
	public static function convert_time($value) {
		$output = sprintf('%02d', intval($value / 60000)).':';
		$value = $value % 60000;
		$output .= sprintf('%02d', intval($value / 1000));
		return $output;
	}
	public static function convert_htmlcolor($color) {
		switch ($color) {
			case 'blue': return '#0000FF';
			case 'teal'; return '#00FF80';
			case 'purple'; return '#7700B6';
			case 'yellow'; return '#DBFF00';
			case 'orange'; return '#FF8100';
			case 'pink'; return '#FF7AC4';
			case 'gray'; return '#999999';
			case 'light-blue'; return '#5EB2B6';
			case 'dark-green'; return '#086C4A';
			case 'brown'; return '#4F300D';
		}
		return '#FFFFFF';
	}

	public static function array_key($arr, $pos) {
		if (!empty($arr)) {
			if ($pos === null) $pos = 0;
			$all_keys = array_keys($arr);
			$key = $all_keys[$pos];
			unset($all_keys);
			if (isset($key)) {
				return $key;
			} else {
				unset($key);
				return null;
			}
		}
	}

	public static function html($replay, $show_header = true, $show_teams = true, $show_teams_lefts = false, $show_picks = true, $show_chat = true, $show_bad_words = false, $show_stats = true) {
		$html = '';
		if ($show_header) $html .= self::html_header($replay).'<hr />';
		if ($show_teams) $html .= self::html_teams($replay, $show_teams_lefts).'<hr />';
		if ($show_chat) $html .= self::html_chat($replay, $show_bad_words).'<hr />';
		if ($show_picks) $html .= self::html_picks($replay).'<hr />';
		if ($show_stats) $html .= self::html_stats($replay).'<hr />';
		return $html;
	}
	public static function html_header($replay) {
		$html = '<table border="0" cellpadding="0" cellspacing="0" class="parser center">';
		$html .= '<colgroup><col /><col width="152" /></colgroup>';
		$html .= '<tr><td valign="top">';
		$html .= '<table border="0" cellpadding="0" cellspacing="0" class="fixed">';
		$html .= '<colgroup><col width="125" /><col /></colgroup>';
		$html .= '<tr><td valign="top">Host</td><td valign="top">'.$replay->host.'</td></tr>';
		$html .= '<tr><td valign="top">Version</td><td valign="top">'.$replay->version.'</td></tr>';
		if ($replay->time > 0)  $html .= '<tr><td valign="top">Dur&eacute;e</td><td valign="top">'.self::convert_time($replay->time).'</td></tr>';
		$html .= '<tr><td valign="top">Modes</td><td valign="top">';
		foreach ($replay->modes as $key => $value) $html .= $value.'<br />';
		$html .= '</td></tr>';
		if (count($replay->observers) > 0) {
			$html .= '<tr><td colspan="2">&nbsp;</td></tr>';
			$html .= '<tr><td valign="top">Observers</td><td valign="top">';
			foreach ($replay->observers as $key => $value) $html .= utf8_decode($value->name).'<br />';
			$html .= '</td></tr>';
		}
		$html .= '</table>';
		$html .= '</td>';
		$html .= '<td><div class="map">';
		$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'basemap.png" alt="" />';
		if (!in_array('Tower010', $replay->towers)) $html .= '<div class="tower sentinel" style="top: 61px; left: 8px;">&nbsp;</div>';
		if (!in_array('Tower020', $replay->towers)) $html .= '<div class="tower sentinel" style="top: 91px; left: 8px;">&nbsp;</div>';
		if (!in_array('Tower030', $replay->towers)) $html .= '<div class="tower sentinel" style="top: 111px; left: 8px;">&nbsp;</div>';
		if (!in_array('Tower011', $replay->towers)) $html .= '<div class="tower sentinel" style="top: 81px; left: 68px;">&nbsp;</div>';
		if (!in_array('Tower021', $replay->towers)) $html .= '<div class="tower sentinel" style="top: 101px; left: 48px;">&nbsp;</div>';
		if (!in_array('Tower031', $replay->towers)) $html .= '<div class="tower sentinel" style="top: 121px; left: 28px;">&nbsp;</div>';
		if (!in_array('Tower012', $replay->towers)) $html .= '<div class="tower sentinel" style="top: 141px; left: 128px;">&nbsp;</div>';
		if (!in_array('Tower022', $replay->towers)) $html .= '<div class="tower sentinel" style="top: 141px; left: 68px;">&nbsp;</div>';
		if (!in_array('Tower032', $replay->towers)) $html .= '<div class="tower sentinel" style="top: 141px; left: 38px;">&nbsp;</div>';
		if (!in_array('Tower041_1', $replay->towers)) $html .= '<div class="tower sentinel" style="top: 131px; left: 11px;">&nbsp;</div>';
		if (!in_array('Tower041_2', $replay->towers)) $html .= '<div class="tower sentinel" style="top: 138px; left: 18px;">&nbsp;</div>';
		if (!in_array('Rax000', $replay->raxs)) $html .= '<div class="rax sentinel" style="top: 117px; left: 11px;">&nbsp;</div>';
		if (!in_array('Rax001', $replay->raxs)) $html .= '<div class="rax sentinel" style="top: 117px; left: 4px;">&nbsp;</div>';
		if (!in_array('Rax010', $replay->raxs)) $html .= '<div class="rax sentinel" style="top: 128px; left: 26px;">&nbsp;</div>';
		if (!in_array('Rax011', $replay->raxs)) $html .= '<div class="rax sentinel" style="top: 122px; left: 20px;">&nbsp;</div>';
		if (!in_array('Rax020', $replay->raxs)) $html .= '<div class="rax sentinel" style="top: 144px; left: 31px;">&nbsp;</div>';
		if (!in_array('Rax021', $replay->raxs)) $html .= '<div class="rax sentinel" style="top: 137px; left: 31px;">&nbsp;</div>';
		$html .= '<div class="throne sentinel" style="top: 138px; left: 8px;">&nbsp;</div>';
		if (!in_array('Tower110', $replay->towers)) $html .= '<div class="tower scourge" style="top: 8px; left: 21px;">&nbsp;</div>';
		if (!in_array('Tower120', $replay->towers)) $html .= '<div class="tower scourge" style="top: 8px; left: 81px;">&nbsp;</div>';
		if (!in_array('Tower130', $replay->towers)) $html .= '<div class="tower scourge" style="top: 8px; left: 111px;">&nbsp;</div>';
		if (!in_array('Tower111', $replay->towers)) $html .= '<div class="tower scourge" style="top: 68px; left: 81px;">&nbsp;</div>';
		if (!in_array('Tower121', $replay->towers)) $html .= '<div class="tower scourge" style="top: 48px; left: 101px;">&nbsp;</div>';
		if (!in_array('Tower131', $replay->towers)) $html .= '<div class="tower scourge" style="top: 28px; left: 121px;">&nbsp;</div>';
		if (!in_array('Tower112', $replay->towers)) $html .= '<div class="tower scourge" style="top: 88px; left: 141px;">&nbsp;</div>';
		if (!in_array('Tower122', $replay->towers)) $html .= '<div class="tower scourge" style="top: 58px; left: 141px;">&nbsp;</div>';
		if (!in_array('Tower132', $replay->towers)) $html .= '<div class="tower scourge" style="top: 38px; left: 141px;">&nbsp;</div>';
		if (!in_array('Tower141_1', $replay->towers)) $html .= '<div class="tower scourge" style="top: 18px; left: 138px;">&nbsp;</div>';
		if (!in_array('Tower141_2', $replay->towers)) $html .= '<div class="tower scourge" style="top: 11px; left: 131px;">&nbsp;</div>';
		if (!in_array('Rax100', $replay->raxs)) $html .= '<div class="rax scourge" style="top: 11px; left: 117px;">&nbsp;</div>';
		if (!in_array('Rax101', $replay->raxs)) $html .= '<div class="rax scourge" style="top: 4px; left: 117px;">&nbsp;</div>';
		if (!in_array('Rax110', $replay->raxs)) $html .= '<div class="rax scourge" style="top: 26px; left: 128px;">&nbsp;</div>';
		if (!in_array('Rax111', $replay->raxs)) $html .= '<div class="rax scourge" style="top: 20px; left: 122px;">&nbsp;</div>';
		if (!in_array('Rax120', $replay->raxs)) $html .= '<div class="rax scourge" style="top: 31px; left: 144px;">&nbsp;</div>';
		if (!in_array('Rax121', $replay->raxs)) $html .= '<div class="rax scourge" style="top: 31px; left: 137px;">&nbsp;</div>';
		$html .= '<div class="throne scourge" style="top: 8px; left: 138px;">&nbsp;</div>';
		$html .= '</div></td>';
		$html .= '</tr></table>';
		return $html;
	}
	public static function html_teams($replay, $show_teams_lefts = false) {
		$html = '';
		$html .= '<table border="0" cellpadding="0" cellspacing="0" class="parser center">';
		$html .= '<colgroup><col width="50%" /><col width="50%" /></colgroup>';
		if (count($replay->sentinel->bans) > 0) {
			$html .= '<tr><td class="left"><img src="'.REPLAY_DEFINITIONS_IMAGES.'forbidden.jpg" width="32" alt="" align="absmiddle" title="Bans" />';
			foreach ($replay->sentinel->bans as $key => $value) {
				if (isset($value['img']) && $value['img'] != "") {
					$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.$value['img'].'.png" width="32" align="absmiddle" alt="" title="'.$value['hero'].'" />';
				} else {
					$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" />';
				}
			}
			$html .= '</td><td class="right">';
			foreach ($replay->scourge->bans as $key => $value) {
				if (isset($value['img']) && $value['img'] != "") {
					$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.$value['img'].'.png" width="32" align="absmiddle" alt="" title="'.$value['hero'].'" />';
				} else {
					$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" />';
				}
			}
			$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'forbidden.jpg" width="32" alt="" align="absmiddle" title="Bans" /></td></tr>';
		}
		$html .= '<tr><td class="left">';
		foreach ($replay->sentinel->players as $key => $value) {
			$html .= '<p class="teams-player" style="color: '.self::convert_htmlcolor($value->color).';">';
			if ($show_teams_lefts) $html .= '<span>('.($value->endway == 'Left' ? self::convert_time($value->endtime) : $value->endway).')</span>';
			if (isset($value->hero['img']) && $value->hero['img'] != "") {
				$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.$value->hero['img'].'.png" width="32" align="absmiddle" alt="" title="'.$value->hero['hero'].'" />';
			} else {
				$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" />';
			}
			$html .= utf8_decode($value->name);
			$html .= '</p>';
		}
		$html .= '</td><td class="right">';
		foreach ($replay->scourge->players as $key => $value) {
			$html .= '<p class="teams-player" style="color: '.self::convert_htmlcolor($value->color).';">';
			if ($show_teams_lefts) $html .= '<span>('.($value->endway == 'Left' ? self::convert_time($value->endtime) : $value->endway).')</span>';
			$html .= utf8_decode($value->name);
			if (isset($value->hero['img']) && $value->hero['img'] != "") {
				$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.$value->hero['img'].'.png" width="32" align="absmiddle" alt="" title="'.$value->hero['hero'].'" /></p>';
			} else {
				$html .= '<img src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" width="32" align="absmiddle" alt="" /></p>';
			}
		}
		$html .= '</td></tr></table>';
		return $html;
	}
	public static function html_chat($replay, $show_bad_words = false) {
		$html = '';
		if (count($replay->chat) == 0) return '';
		$html .= '<div class="replay-chat"><table class="parser" cellpadding="0" cellspacing="0" border="0"><colgroup><col width="45" /><col width="80" /><col width="120" /><col /></colgroup>';
		foreach ($replay->chat as $key => $value) {
			$html .= '<tr>';
			if ($value['mode'] == "Allies" OR $value['mode'] == "Observers") {
				$html .= '<td valign="top" style="color: #888888;">'.$value['time'].'</td>';
				$html .= '<td valign="top" style="color: #888888;">'.$value['mode'].'</td>';
			} else {
				$html .= '<td valign="top">'.$value['time'].'</td>';
				$html .= '<td valign="top">'.$value['mode'].'</td>';
			}
			$html .= '<td valign="top" style="color: '.self::convert_htmlcolor($value['player_color']).';">'.utf8_decode($value['player_name']).'</td>';
			
			$text = utf8_decode($value['text']);
			
			if ($show_bad_words) {
				$bad_words = file_get_contents(BAD_WORDS_PATH);
				if ($value['mode'] == "All") {
					$text = preg_replace('/(ff)/i', '<span class="vip"><b>$1</b></span>', $text);
				}
				$text = preg_replace('/('.$bad_words.')/i', '<span class="lose"><b>$1</b></span>', $text);
			}
			
			if ($value['mode'] == "Allies" OR $value['mode'] == "Observers") {
				$html .= '<td valign="top" style="color: #888888;">'.$text.'</td>';
			} else {
				$html .= '<td valign="top">'.$text.'</td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table></div>';
		return $html;
	}
	private static function html_pick_old($replay) {
		
		$img_ban = ''; $img_pick = '';
		
		for ($i = 0; $i < 10; $i++) {
			if ($i < 8) {
				$img_ban .= '<img align="absmiddle" src="'.REPLAY_DEFINITIONS_IMAGES.'%s.png" alt="" width="24" title="%s" />';
			}
			$img_pick .= '<img align="absmiddle" src="'.REPLAY_DEFINITIONS_IMAGES.'%s.png" alt="" width="24" title="%s" />';
		}

		if ($replay->sentinel->bans[$b0]['time'] < $replay->scourge->bans[$b1]['time']) {
			$fp_array_ban = $replay->sentinel->bans; $fp_array_pick = $replay->sentinel->picks;
			$sp_array_ban = $replay->scourge->bans; $sp_array_pick = $replay->scourge->picks;
		} else {
			$sp_array_ban = $replay->sentinel->bans; $sp_array_pick = $replay->sentinel->picks;
			$fp_array_ban = $replay->scourge->bans; $fp_array_pick = $replay->scourge->picks;
		}
		
		$fp_ban = sprintf($img_ban, 
						  $fp_array_ban[self::array_key($fp_array_ban, 0)]['img'], $fp_array_ban[self::array_key($fp_array_ban, 0)]['hero'],
						  'emptypick', '', 
						  $fp_array_ban[self::array_key($fp_array_ban, 1)]['img'], $fp_array_ban[self::array_key($fp_array_ban, 1)]['hero'],
						  'emptypick', '', 
						  (self::array_key($fp_array_ban, 2) == null) ? 'emptypick' : $fp_array_ban[self::array_key($fp_array_ban, 2)]['img'], (self::array_key($fp_array_ban, 2) == null) ? '' : $fp_array_ban[self::array_key($fp_array_ban, 2)]['hero'],
						  'emptypick', '', 
						  (self::array_key($fp_array_ban, 3) == null) ? 'emptypick' : $fp_array_ban[self::array_key($fp_array_ban, 3)]['img'], (self::array_key($fp_array_ban, 3) == null) ? '' : $fp_array_ban[self::array_key($fp_array_ban, 3)]['hero'],
						  'emptypick', '');
		$fp_pick = sprintf($img_pick, 
						  $fp_array_pick[self::array_key($fp_array_pick, 0)]['img'], $fp_array_pick[self::array_key($fp_array_pick, 0)]['hero'],
						  'emptypick', '', 
						  'emptypick', '', 
						  $fp_array_pick[self::array_key($fp_array_pick, 1)]['img'], $fp_array_pick[self::array_key($fp_array_pick, 1)]['hero'],
						  $fp_array_pick[self::array_key($fp_array_pick, 2)]['img'], $fp_array_pick[self::array_key($fp_array_pick, 2)]['hero'],
						  'emptypick', '', 
						  'emptypick', '', 
						  $fp_array_pick[self::array_key($fp_array_pick, 3)]['img'], $fp_array_pick[self::array_key($fp_array_pick, 3)]['hero'],
						  $fp_array_pick[self::array_key($fp_array_pick, 4)]['img'], $fp_array_pick[self::array_key($fp_array_pick, 4)]['hero'],
						  'emptypick', '');

		$sp_ban = sprintf($img_ban,
						  'emptypick', '',
						  $sp_array_ban[self::array_key($sp_array_ban, 0)]['img'], $sp_array_ban[self::array_key($sp_array_ban, 0)]['hero'],
						  'emptypick', '', 
						  $sp_array_ban[self::array_key($sp_array_ban, 1)]['img'], $sp_array_ban[self::array_key($sp_array_ban, 1)]['hero'],
						  'emptypick', '', 
						  (self::array_key($sp_array_ban, 2) == null) ? 'emptypick' : $sp_array_ban[self::array_key($sp_array_ban, 2)]['img'], (self::array_key($sp_array_ban, 2) == null) ? '' : $sp_array_ban[self::array_key($sp_array_ban, 2)]['hero'],
						  'emptypick', '', 
						  (self::array_key($sp_array_ban, 3) == null) ? 'emptypick' : $sp_array_ban[self::array_key($sp_array_ban, 3)]['img'], (self::array_key($sp_array_ban, 3) == null) ? '' : $sp_array_ban[self::array_key($sp_array_ban, 3)]['hero']);
		$sp_pick = sprintf($img_pick, 
						  'emptypick', '',
						  $sp_array_pick[self::array_key($sp_array_pick, 0)]['img'], $sp_array_pick[self::array_key($sp_array_pick, 0)]['hero'],
						  $sp_array_pick[self::array_key($sp_array_pick, 1)]['img'], $sp_array_pick[self::array_key($sp_array_pick, 1)]['hero'],
						  'emptypick', '', 
						  'emptypick', '', 
						  $sp_array_pick[self::array_key($sp_array_pick, 2)]['img'], $sp_array_pick[self::array_key($sp_array_pick, 2)]['hero'],
						  $sp_array_pick[self::array_key($sp_array_pick, 3)]['img'], $sp_array_pick[self::array_key($sp_array_pick, 3)]['hero'],
						  'emptypick', '', 
						  'emptypick', '', 
						  $sp_array_pick[self::array_key($sp_array_pick, 4)]['img'], $sp_array_pick[self::array_key($sp_array_pick, 4)]['hero']);
		
		if ($replay->sentinel->bans[$b0]['time'] < $replay->scourge->bans[$b1]['time']) {
			$se_ban = $fp_ban; $se_pick = $fp_pick;
			$sc_ban = $sp_ban; $sc_pick = $sp_pick;
		} else {
			$se_ban = $sp_ban; $se_pick = $sp_pick;
			$sc_ban = $fp_ban; $sc_pick = $fp_pick;
		}
		
		$html = '';
		$html .= '<table class="parser center border-all" border="0" cellpadding="1" cellspacing="0">';
		$html .= '<colgroup><col /><col width="210" /><col width="260" /></colgroup>';
		$html .= '<tr><td class="padding"></td><td class="padding border-left"></td><td class="padding border-left"></td></tr>';
		$html .= '<tr><td class="center" style="color: '.self::convert_htmlcolor($replay->sentinel->players[0]->color).';">'.utf8_decode($replay->sentinel->players[0]->name).'</td>';
		$html .= '<td valign="middle" class="center border-left">'.$se_ban.'</td>';
		$html .= '<td valign="middle" class="center border-left">'.$se_pick.'</td></tr>';
		$html .= '<tr><td class="padding"></td><td class="padding border-left"></td><td class="padding border-left"></td></tr>';
		$html .= '<tr><td class="center" style="color: '.self::convert_htmlcolor($replay->scourge->players[0]->color).';">'.utf8_decode($replay->scourge->players[0]->name).'</td>';
		$html .= '<td valign="middle" class="center border-left">'.$sc_ban.'</td>';
		$html .= '<td valign="middle" class="center border-left">'.$sc_pick.'</td></tr>';
		$html .= '<tr><td class="padding"></td><td class="padding border-left"></td><td class="padding border-left"></td></tr>';
		$html .= '</table>';
		return $html;
	}
	private static function html_pick_new($replay) {
	
		$img_pick = '';
		for ($i = 0; $i < 20; $i++) {
			$img_pick .= '<img align="absmiddle" src="'.REPLAY_DEFINITIONS_IMAGES.'%s.png" alt="" width="24" title="%s" />';
			if ($i == 5) { $img_pick .= '</td><td valign="middle" class="center border-left">'; }
			if ($i == 11) { $img_pick .= '</td><td valign="middle" class="center border-left">'; }
			if ($i == 15) { $img_pick .= '</td><td valign="middle" class="center border-left">'; }
		}

		if ($replay->sentinel->bans[$b0]['time'] < $replay->scourge->bans[$b1]['time']) {
			$fp_array_ban = $replay->sentinel->bans; $fp_array_pick = $replay->sentinel->picks;
			$sp_array_ban = $replay->scourge->bans; $sp_array_pick = $replay->scourge->picks;
		} else {
			$sp_array_ban = $replay->sentinel->bans; $sp_array_pick = $replay->sentinel->picks;
			$fp_array_ban = $replay->scourge->bans; $fp_array_pick = $replay->scourge->picks;
		}

		$fp = sprintf($img_pick, 
					  $fp_array_ban[self::array_key($fp_array_ban, 0)]['img'], $fp_array_ban[self::array_key($fp_array_ban, 0)]['hero'],
					  'emptypick', '',
					  $fp_array_ban[self::array_key($fp_array_ban, 1)]['img'], $fp_array_ban[self::array_key($fp_array_ban, 1)]['hero'],
					  'emptypick', '',
					  $fp_array_ban[self::array_key($fp_array_ban, 2)]['img'], $fp_array_ban[self::array_key($fp_array_ban, 2)]['hero'],
					  'emptypick', '',
					  $fp_array_pick[self::array_key($fp_array_pick, 0)]['img'], $fp_array_pick[self::array_key($fp_array_pick, 0)]['hero'],
					  'emptypick', '',
					  'emptypick', '',
					  $fp_array_pick[self::array_key($fp_array_pick, 1)]['img'], $fp_array_pick[self::array_key($fp_array_pick, 1)]['hero'],
					  $fp_array_pick[self::array_key($fp_array_pick, 2)]['img'], $fp_array_pick[self::array_key($fp_array_pick, 2)]['hero'],
					  'emptypick', '',
					  $fp_array_ban[self::array_key($fp_array_ban, 3)]['img'], $fp_array_ban[self::array_key($fp_array_ban, 3)]['hero'],
					  'emptypick', '',
					  $fp_array_ban[self::array_key($fp_array_ban, 4)]['img'], $fp_array_ban[self::array_key($fp_array_ban, 4)]['hero'],
					  'emptypick', '',
					  $fp_array_pick[self::array_key($fp_array_pick, 3)]['img'], $fp_array_pick[self::array_key($fp_array_pick, 3)]['hero'],
					  'emptypick', '',
					  $fp_array_pick[self::array_key($fp_array_pick, 4)]['img'], $fp_array_pick[self::array_key($fp_array_pick, 4)]['hero'],
					  'emptypick', '');

		$sp = sprintf($img_pick, 
					  'emptypick', '',
					  $sp_array_ban[self::array_key($sp_array_ban, 0)]['img'], $sp_array_ban[self::array_key($sp_array_ban, 0)]['hero'],
					  'emptypick', '',
					  $sp_array_ban[self::array_key($sp_array_ban, 1)]['img'], $sp_array_ban[self::array_key($sp_array_ban, 1)]['hero'],
					  'emptypick', '',
					  $sp_array_ban[self::array_key($sp_array_ban, 2)]['img'], $sp_array_ban[self::array_key($sp_array_ban, 2)]['hero'],
					  'emptypick', '',
					  $sp_array_pick[self::array_key($sp_array_pick, 0)]['img'], $sp_array_pick[self::array_key($sp_array_pick, 0)]['hero'],
					  $sp_array_pick[self::array_key($sp_array_pick, 1)]['img'], $sp_array_pick[self::array_key($sp_array_pick, 1)]['hero'],
					  'emptypick', '',
					  'emptypick', '',
					  $sp_array_pick[self::array_key($sp_array_pick, 2)]['img'], $sp_array_pick[self::array_key($sp_array_pick, 2)]['hero'],
					  'emptypick', '',
					  $sp_array_ban[self::array_key($sp_array_ban, 3)]['img'], $sp_array_ban[self::array_key($sp_array_ban, 3)]['hero'],
					  'emptypick', '',
					  $sp_array_ban[self::array_key($sp_array_ban, 4)]['img'], $sp_array_ban[self::array_key($sp_array_ban, 4)]['hero'],
					  'emptypick', '',
					  $sp_array_pick[self::array_key($sp_array_pick, 3)]['img'], $sp_array_pick[self::array_key($sp_array_pick, 3)]['hero'],
					  'emptypick', '',
					  $sp_array_pick[self::array_key($sp_array_pick, 4)]['img'], $sp_array_pick[self::array_key($sp_array_pick, 4)]['hero']);
		
		if ($replay->sentinel->bans[$b0]['time'] < $replay->scourge->bans[$b1]['time']) {
			$se = $fp; $sc = $sp;
		} else {
			$se = $sp; $sc = $fp;
		}

		$html = '';
		$html .= '<table class="parser center border-all" border="0" cellpadding="1" cellspacing="0">';
		$html .= '<colgroup><col /><col width="160" /><col width="160" /><col width="110" /><col width="110" /></colgroup>';
		$html .= '<tr><td class="padding"></td><td class="padding border-left"></td><td class="padding border-left"></td><td class="padding border-left"></td><td class="padding border-left"></td></tr>';
		$html .= '<tr><td class="center" style="font-size:8pt; color: '.self::convert_htmlcolor($replay->sentinel->players[0]->color).';">Sentinel</td>';
		$html .= '<td valign="middle" class="center border-left">'.$se.'</td></tr>';
		$html .= '<tr><td class="padding"></td><td class="padding border-left"></td><td class="padding border-left"></td><td class="padding border-left"></td><td class="padding border-left"></td></tr>';
		$html .= '<tr><td class="center" style="font-size:8pt; color: '.self::convert_htmlcolor($replay->scourge->players[0]->color).';">Scourge</td>';
		$html .= '<td valign="middle" class="center border-left">'.$sc.'</td></tr>';
		$html .= '<tr><td class="padding"></td><td class="padding border-left"></td><td class="padding border-left"></td><td class="padding border-left"></td><td class="padding border-left"></td></tr>';
		$html .= '</table>';
		return $html;
	}
	public static function html_picks($replay) {
		
		if ($replay->version < 'v6.68') {
			return self::html_pick_old($replay);
		} else {
			return self::html_pick_new($replay);
		}
	}
	public static function html_stats($replay) {
		$html = '';
		$html .= self::html_stats_team($replay, $replay->sentinel->players, $replay->scourge->players);
		$html .= '<br />';
		$html .= self::html_stats_team($replay, $replay->scourge->players, $replay->sentinel->players);
		return $html;
	}
	private static function html_stats_team($replay, $team, $opp) {
		$line0 = ''; $line1 = ''; $line2 = ''; $line3 = ''; $line4 = ''; $line5 = ''; $line6 = ''; $line7 = ''; $line8 = ''; $line9 = ''; $line10 = '';
		foreach ($team as $key => $player) {
			$line0 .= '<td class="center">'.$player->gold.'</td>';
			$line1 .= '<td class="center" style="color: '.self::convert_htmlcolor($player->color).';">'.utf8_decode($player->name).'</td>';
			if (isset($player->hero['img']) && $player->hero['img'] != '') {
				$line2 .= '<td class="center"><img align="absmiddle" width="48" src="'.REPLAY_DEFINITIONS_IMAGES.$player->hero['img'].'.png" alt="" title="'.$player->hero['hero'].'" /></td>';
			} else {
				$line2 .= '<td class="center"><img align="absmiddle" width="48" src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" alt="" /></td>';
			}
			$line3 .= '<td class="center">';
			for ($i = 0; $i < 6; $i++) {
				if ($i % 2 == 0 && $i > 0) $line3 .= '<br />';
				if (isset($player->items['s'.$i])) {
					$line3 .= '<img align="absmiddle" width="32" src="'.REPLAY_DEFINITIONS_IMAGES.$player->items['s'.$i]['img'].'.png" alt="" title="'.$player->items['s'.$i]['name'].'" />';
				} else {
					$line3 .= '<img align="absmiddle" width="32" src="'.REPLAY_DEFINITIONS_IMAGES.'emptyitem.jpg" alt="" />';
				}
			}
			$line3 .= '</td>';
			$line4 .= '<td class="center alternate">'.$player->kills.'/'.$player->deaths.'/'.$player->assists.'</td>';
			$line5 .= '<td class="center">'.$player->creepskills.'/'.$player->creepsdenies.'</td>';
			$line6 .= '<td class="center alternate">'.$player->neutrals.'</td>';
			$line7 .= '<td class="center">'.$player->tkill.'/'.$player->tdeny.'</td>';
			$line10 .= '<td class="center alternate">'.$player->wards.'</td>';
			$line8 .= '<td class="center">';
			foreach ($opp as $okey => $oplayer) {
				$k = 0;
				$d = 0;
				if (isset($player->kstats[$oplayer->id])) $k = $player->kstats[$oplayer->id];
				if (isset($oplayer->kstats[$player->id])) $d = $oplayer->kstats[$player->id];
				if (isset($player->hero['img']) && $player->hero['img'] != '') {
					$line8 .= '<img align="absmiddle" width="24" src="'.REPLAY_DEFINITIONS_IMAGES.$player->hero['img'].'.png" alt="" title="'.$player->hero['hero'].'" />';
				} else {
					$line8 .= '<img align="absmiddle" width="24" src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" alt="" />';
				}
				if (isset($oplayer->hero['img']) && $oplayer->hero['img'] != '') {
					$line8 .= '<img align="absmiddle" width="24" src="'.REPLAY_DEFINITIONS_IMAGES.$oplayer->hero['img'].'.png" alt="" title="'.$oplayer->hero['hero'].'" />';
				} else {
					$line8 .= '<img align="absmiddle" width="24" src="'.REPLAY_DEFINITIONS_IMAGES.'emptypick.png" alt="" />';
				}
				$line8 .= '&nbsp;&nbsp;'.$k.' / '.$d;
				$line8 .= '<br />';
			}
			$line8 .= '</td>';
			$line9 .= '<td class="center">';
			if ($player->endway == 'Left') {
				$line9 .= self::convert_time($player->endtime);
			} else {
				$line9 .= $player->endway;
			}
			$line9 .= '</td>';
		}
		$html = '';
		$html .= '<table class="parser border-all center stats" border="0" cellpadding="1" cellspacing="0">';
		$html .= '<colgroup><col /><col width="110" /><col width="110" /><col width="110" /><col width="110" /><col width="110" /></colgroup>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td>&nbsp;</td>'.$line1.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td>&nbsp;</td>'.$line2.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td>&nbsp;</td>'.$line3.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td class="legend" title="Current Gold"><img src="'.REPLAY_DEFINITIONS_IMAGES.'gold.gif" alt="" /></td>'.$line0.'</tr>';
		$html .= '<tr><td class="legend alternate" title="Hero Kills/Deaths/Assists">K/D/A</td>'.$line4.'</tr>';
		$html .= '<tr><td class="legend" title="Creeps Stats">CS</td>'.$line5.'</tr>';
		$html .= '<tr><td class="legend alternate" title="Neutrals">N</td>'.$line6.'</tr>';
		$html .= '<tr><td class="legend" title="Tower Stats">TS</td>'.$line7.'</tr>';
		$html .= '<tr><td class="legend alternate" title="Wards">W</td>'.$line10.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td>&nbsp;</td>'.$line8.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td class="legend" title="Left At">Left At</td>'.$line9.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '</table>';		
		return $html;
	}

}

?>