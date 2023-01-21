<?php

	function convert_time($value) {
		$output = sprintf('%02d', intval($value / 60000)).':';
		$value = $value % 60000;
		$output .= sprintf('%02d', intval($value / 1000));
		return $output;
	}

	function convert_htmlcolor($color) {
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
		return '#888888';
	}
	
	function generateDisplayHeader($r, $br) {
		$html = '';
		$html .= '<tr><td>Replay</td><td>'.$br->replay_title.'</td></tr>';
		$html .= '<tr><td>Version</td><td>'.$r->version.'</td></tr>';
		if ($r->time > 0)  $html .= '<tr><td>Dur&eacute;e</td><td>'.convert_time($r->time).'</td></tr>';
		//$html .= '<tr><td>Fichier</td><td><a href="/ligue/rc/rcdwld/'.$br->replay_file.'">'.$br->replay_file.'</a></td></tr>';
		$html .= '<tr><td valign="top">Modes</td><td>';
		foreach ($r->modes as $key => $value) $html .= $value.'<br />';
		$html .= '</td></tr>';
		if (count($r->observers) > 0) {
			$html .= '<tr><td colspan="2">&nbsp;</td></tr>';
			$html .= '<tr><td valign="top">Observers</td><td>';
			foreach ($r->observers as $key => $value) $html .= utf8_decode($value->name).'<br />';
			$html .= '</td></tr>';
		}
		return $html;
	}
	
	function generateHeader($r, $pr) {
		$html = '';
		//$html .= '<tr><td>Fichier</td><td><a href="/ligue/rc/rctmp/'.$pr.'">'.$pr.'</a></td></tr>';
		$html .= '<tr><td>Version</td><td>'.$r->version.'</td></tr>';
		if ($r->time > 0)  $html .= '<tr><td>Dur&eacute;e</td><td>'.convert_time($r->time).'</td></tr>';
		$html .= '<tr><td valign="top">Modes</td><td>';
		foreach ($r->modes as $key => $value) $html .= $value.'<br />';
		$html .= '</td></tr>';
		if (count($r->observers) > 0) {
			$html .= '<tr><td colspan="2">&nbsp;</td></tr>';
			$html .= '<tr><td valign="top">Observers</td><td>';
			foreach ($r->observers as $key => $value) $html .= utf8_decode($value->name).'<br />';
			$html .= '</td></tr>';
		}
		return $html;
	}
	
	function generateTeams($r) {
		$html = '';
		$html .= '<tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2" class="line"></td></tr><tr><td colspan="2">&nbsp;</td></tr>';
		$html .= '<tr><td colspan="2"><table style="width: 100%;"><colgroup><col width="50%" /><col width="50%" /></colgroup>';
		if (count($r->sentinel->bans) > 0) {
			$html .= '<tr><td style="text-align: left;"><img src="/ligue/forbidden2.jpg" alt="" align="absmiddle" title="Bans" />';
			foreach ($r->sentinel->bans as $key => $value) {
				$html .= '<img src="/ligue/img/heroes/'.$value['hero'].'.gif" width="32" align="absmiddle" alt="" title="'.$value['hero'].'" />';
			}
			$html .= '</td><td style="text-align: right;">';
			foreach ($r->scourge->bans as $key => $value) {
				$html .= '<img src="/ligue/img/heroes/'.$value['hero'].'.gif" width="32" align="absmiddle" alt="" title="'.$value['hero'].'" />';
			}
			$html .= '<img src="/ligue/forbidden2.jpg" alt="" align="absmiddle" title="Bans" /></td></tr>';
		}
		$html .= '<tr><td style="text-align: left;">';
		foreach ($r->sentinel->players as $key => $value) {
			$html .= '<p style="margin: 2px 0px; padding: 0px; color: '.convert_htmlcolor($value->color).';">';
			$html .= '<img align="absmiddle" src="/ligue/img/heroes/'.$value->hero['hero'].'.gif" width="32" alt="" title="'.$value->hero['hero'].'" />';
			$html .= '&nbsp;'.utf8_decode($value->name).'</p>';
		}
		$html .= '</td><td style="text-align: right;">';
		foreach ($r->scourge->players as $key => $value) {
			$html .= '<p style="margin: 2px 0px; padding: 0px; color: '.convert_htmlcolor($value->color).';">';
			$html .= utf8_decode($value->name).'&nbsp;';
			$html .= '<img align="absmiddle" src="/ligue/img/heroes/'.$value->hero['hero'].'.gif" width="32" alt="" title="'.$value->hero['hero'].'" /></p>';
		}
		$html .= '</td></tr></table></td></tr>';
		return $html;
	}
	
	function generateChat($r) {
		$html = '';
		$html .= '<tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2" class="line"></td></tr><tr><td colspan="2">&nbsp;</td></tr>';
		$html .= '<tr><td colspan="2"><div class="ochat"><table class="chat" cellpadding="0" cellspacing="0"><colgroup><col width="45" /><col width="80" /><col width="120" /><col /></colgroup>';
		foreach ($r->chat as $key => $value) {
			$html .= '<tr>';
			if ($value['mode'] == "Allies" OR $value['mode'] == "Observers") {
				$html .= '<td valign="top" style="color: #888888;">'.$value['time'].'</td>';
				$html .= '<td valign="top" style="color: #888888;">'.$value['mode'].'</td>';
			} else {
				$html .= '<td valign="top">'.$value['time'].'</td>';
				$html .= '<td valign="top">'.$value['mode'].'</td>';
			}
			$html .= '<td valign="top" style="color: '.convert_htmlcolor($value['player_color']).';">'.utf8_decode($value['player_name']).'</td>';
			if ($value['mode'] == "Allies" OR $value['mode'] == "Observers") {
				$html .= '<td valign="top" style="color: #888888;">'.utf8_decode($value['text']).'</td>';
			} else {
				$html .= '<td valign="top">'.utf8_decode($value['text']).'</td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table></div></td></tr>';
		return $html;
	}

	function generateStats($team, $opp) {
		$line0 = '';
		$line1 = '';
		$line2 = '';
		$line3 = '';
		$line4 = '';
		$line5 = '';
		$line6 = '';
		$line7 = '';
		$line8 = '';
		$line9 = '';
		foreach ($team as $key => $player) {
			$line0 .= '<td class="center">'.$player->gold.'</td>';
			$line1 .= '<td class="center" style="color: '.convert_htmlcolor($player->color).';">'.utf8_decode($player->name).'</td>';
			$line2 .= '<td class="center"><img align="absmiddle" src="/ligue/img/heroes/'.$player->hero['hero'].'.gif" width="32" alt="" title="'.$player->hero['hero'].'" /></td>';
			$line3 .= '<td class="center">';
			if (isset($player->items['s0'])) {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/rcitems/'.$player->items['s0']['img'].'" alt="" title="'.$player->items['s0']['name'].'" />';
			} else {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/BTNEmpty.jpg" alt="" />';
			}
			if (isset($player->items['s1'])) {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/rcitems/'.$player->items['s1']['img'].'" alt="" title="'.$player->items['s1']['name'].'" />';
			} else {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/BTNEmpty.jpg" alt="" />';
			}
			$line3 .= '<br />';
			if (isset($player->items['s2'])) {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/rcitems/'.$player->items['s2']['img'].'" alt="" title="'.$player->items['s2']['name'].'" />';
			} else {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/BTNEmpty.jpg" alt="" />';
			}
			if (isset($player->items['s3'])) {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/rcitems/'.$player->items['s3']['img'].'" alt="" title="'.$player->items['s3']['name'].'" />';
			} else {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/BTNEmpty.jpg" alt="" />';
			}
			$line3 .= '<br />';
			if (isset($player->items['s4'])) {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/rcitems/'.$player->items['s4']['img'].'" alt="" title="'.$player->items['s4']['name'].'" />';
			} else {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/BTNEmpty.jpg" alt="" />';
			}
			if (isset($player->items['s5'])) {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/rcitems/'.$player->items['s5']['img'].'" alt="" title="'.$player->items['s5']['name'].'" />';
			} else {
				$line3 .= '<img align="absmiddle" width="32" src="/ligue/rc/BTNEmpty.jpg" alt="" />';
			}
			$line3 .= '</td>';
			$line4 .= '<td class="center alternate">'.$player->kills.'/'.$player->deaths.'/'.$player->assists.'</td>';
			$line5 .= '<td class="center">'.$player->creepskills.'/'.$player->creepsdenies.'</td>';
			$line6 .= '<td class="center alternate">'.$player->neutrals.'</td>';
			$line7 .= '<td class="center">'.$player->tkill.'/'.$player->tdeny.'</td>';
			$line8 .= '<td class="center">';
			foreach ($opp as $okey => $oplayer) {
				$k = 0;
				$d = 0;
				if (isset($player->kstats[$oplayer->id])) $k = $player->kstats[$oplayer->id];
				if (isset($oplayer->kstats[$player->id])) $d = $oplayer->kstats[$player->id];
				$line8 .= '<img align="absmiddle" src="/ligue/img/heroes/'.$player->hero['hero'].'.gif" alt="" width="24" title="'.$player->hero['hero'].'" />';
				$line8 .= '<img align="absmiddle" src="/ligue/img/heroes/'.$oplayer->hero['hero'].'.gif" alt="" width="24" title="'.$oplayer->hero['hero'].'" />';
				$line8 .= '&nbsp;&nbsp;'.$k.' / '.$d;
				$line8 .= '<br />';
			}
			$line8 .= '</td>';
			$line9 .= '<td class="center">';
			if ($player->endway == 'Left') {
				$line9 .= convert_time($player->endtime);
			} else {
				$line9 .= $player->endway;
			}
			$line9 .= '</td>';
		}
		$html = '';
		$html .= '<table class="pstats" cellpadding="1" cellspacing="0">';
		$html .= '<colgroup><col width="50" /><col width="110" /><col width="110" /><col width="110" /><col width="110" /><col width="110" /></colgroup>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td>&nbsp;</td>'.$line1.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td>&nbsp;</td>'.$line2.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td>&nbsp;</td>'.$line3.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td class="legend" title="Current Gold"><img src="/ligue/img/gold.gif" alt="" /></td>'.$line0.'</tr>';
		$html .= '<tr><td class="legend alternate" title="Hero Kills/Deaths/Assists">K/D/A</td>'.$line4.'</tr>';
		$html .= '<tr><td class="legend" title="Creeps Stats">CS</td>'.$line5.'</tr>';
		$html .= '<tr><td class="legend alternate" title="Neutrals">N</td>'.$line6.'</tr>';
		$html .= '<tr><td class="legend" title="Tower Stats">TS</td>'.$line7.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td>&nbsp;</td>'.$line8.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '<tr><td class="legend" title="Left At">Left At</td>'.$line9.'</tr>';
		$html .= '<tr><td colspan="6" class="padding"></td></tr>';
		$html .= '</table>';
		return $html;
	}
	
	function array_key($arr, $pos) {
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

	function generatePicks($r) {
		$line0 = '';
		$line1 = '';
		$line2 = '';
		$line3 = '';
		$b0 = array_key($r->sentinel->bans, 0);
		$b1 = array_key($r->scourge->bans, 0);
		if ($r->sentinel->bans[$b0]['time'] < $r->scourge->bans[$b1]['time']) {
			foreach ($r->sentinel->bans as $key => $ban) {
				$line0 .= '<img align="absmiddle" src="/ligue/img/heroes/'.$ban['hero'].'.gif" alt="" width="24" title="'.$ban['hero'].'" />';
				$line0 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
			}
			$turn = 0;
			foreach ($r->sentinel->picks as $key => $pick) {
				if ($turn == 1 OR $turn == 3) {
					$line2 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
					$line2 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
				}
				$line2 .= '<img align="absmiddle" src="/ligue/img/heroes/'.$pick['hero'].'.gif" alt="" width="24" title="'.$pick['hero'].'" />';
				$turn += 1;
				if ($turn == 5) {
					$line2 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
				}
			}
			foreach ($r->scourge->bans as $key => $ban) {
				$line1 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
				$line1 .= '<img align="absmiddle" src="/ligue/img/heroes/'.$ban['hero'].'.gif" alt="" width="24" title="'.$ban['hero'].'" />';
			}
			$turn = 0;
			foreach ($r->scourge->picks as $key => $pick) {
				if ($turn == 0) {
					$line3 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
				} else if ($turn == 2 OR $turn == 4) {
					$line3 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
					$line3 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
				}
				$line3 .= '<img align="absmiddle" src="/ligue/img/heroes/'.$pick['hero'].'.gif" alt="" width="24" title="'.$pick['hero'].'" />';
				$turn += 1;
			}
		} else {
			foreach ($r->sentinel->bans as $key => $ban) {
				$line0 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
				$line0 .= '<img align="absmiddle" src="/ligue/img/heroes/'.$ban['hero'].'.gif" alt="" width="24" title="'.$ban['hero'].'" />';
			}
			$turn = 0;
			foreach ($r->sentinel->picks as $key => $pick) {
				if ($turn == 0) {
					$line2 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
				} else if ($turn == 2 OR $turn == 4) {
					$line2 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
					$line2 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
				}
				$line2 .= '<img align="absmiddle" src="/ligue/img/heroes/'.$pick['hero'].'.gif" alt="" width="24" title="'.$pick['hero'].'" />';
				$turn += 1;
			}
			foreach ($r->scourge->bans as $key => $ban) {
				$line1 .= '<img align="absmiddle" src="/ligue/img/heroes/'.$ban['hero'].'.gif" alt="" width="24" title="'.$ban['hero'].'" />';
				$line1 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
			}
			$turn = 0;
			foreach ($r->scourge->picks as $key => $pick) {
				if ($turn == 1 OR $turn == 3) {
					$line3 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
					$line3 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
				}
				$line3 .= '<img align="absmiddle" src="/ligue/img/heroes/'.$pick['hero'].'.gif" alt="" width="24" title="'.$pick['hero'].'" />';
				$turn += 1;
				if ($turn == 5) {
					$line3 .= '<img align="absmiddle" src="/ligue/rc/BTNEmpty.png" alt="" width="24" height="24" />';
				}
			}
		}
		$html = '';
		$html .= '<tr><td colspan="2" align="center"><table class="pstats" cellpadding="1" cellspacing="0">';
		$html .= '<colgroup><col /><col width="210" /><col width="260" /></colgroup>';
		$html .= '<tr><td class="padding"></td><td class="padding" style="border-left: solid 2px #E0B73F;"></td><td class="padding" style="border-left: solid 2px #E0B73F;"></td></tr>';
		$html .= '<tr><td style="text-indent: 5px; color: '.convert_htmlcolor($r->sentinel->players[0]->color).';">'.utf8_decode($r->sentinel->players[0]->name).'</td>';
		$html .= '<td align="center" valign="middle" style="border-left: solid 2px #E0B73F;">'.$line0.'</td>';
		$html .= '<td align="center" valign="middle" style="border-left: solid 2px #E0B73F;">'.$line2.'</td></tr>';
		$html .= '<tr><td class="padding"></td><td class="padding" style="border-left: solid 2px #E0B73F;"></td><td class="padding" style="border-left: solid 2px #E0B73F;"></td></tr>';
		$html .= '<tr><td style="text-indent: 5px; color: '.convert_htmlcolor($r->scourge->players[0]->color).';">'.utf8_decode($r->scourge->players[0]->name).'</td>';
		$html .= '<td align="center" valign="middle" style="border-left: solid 2px #E0B73F;">'.$line1.'</td>';
		$html .= '<td align="center" valign="middle" style="border-left: solid 2px #E0B73F;">'.$line3.'</td></tr>';
		$html .= '<tr><td class="padding"></td><td class="padding" style="border-left: solid 2px #E0B73F;"></td><td class="padding" style="border-left: solid 2px #E0B73F;"></td></tr>';
		$html .= '</table></td></tr>';
		return $html;
	}

?>