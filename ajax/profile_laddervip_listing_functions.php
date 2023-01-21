<?php

	require('mysql_connect.php');
	
	$pagesize = 15;
	$current = 0;
	if (isset($_GET['p'])) {
		$current = (int)$_GET['p'];
		if ($current < 0) {
			$current = 0;
		}
	}

	$month = 0;
	if (isset($_GET['month'])) {
		$month = (int)$_GET['month'];
		if ($month < 1 or $month > 12) {
			$month = 0;
		}
	}
	
	$year = 0;
	if (isset($_GET['year'])) {
		$year = (int)$_GET['year'];
		if ($year < 2008) {
			$year = 0;
		}
	}
	
	$isday = 1;
	$day = 0;
	if (isset($_GET['day'])) {
		$day = (int)$_GET['day'];
		if ($day < 1 or $day > 31) {
			$day = 0;
		}
	} else {
		$isday = 0;
	}
	
	$player = '';
	if (isset($_GET['player'])) {
		$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));
	}
		
	$pwith = '';
	if (isset($_GET['pwith'])) {
		$pwith = mysql_real_escape_string(substr($_GET['pwith'], 0, 25));
	}
	
	function createDatesTable($t, $r, $mo, $pw, $d) {
		$q = mysql_query($r) or die(mysql_error());
		$table = '<table style="width: 96%; margin: 0px 2%;">';
		$table .= '<colgroup><col /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="50" /></colgroup>';
		$table .= '<tr><td align="right" colspan="8"><strong>'.$t.'</strong></td></tr>';
		$table .= '<tr><td class="line" colspan="8"></td></tr>';
		$m = 0;
		$tga = $twi = $tlo = $tle = $taw = $tcl = $tba = 0;
		while ($o = mysql_fetch_object($q)) {
			$m = $m + 1;
			$c = (($m % 2 == 0) ? ' class="alternate"' : '');
			$table .= '<tr>';
			if ($d == 'M') {
				$mkt = mktime(0, 0, 0, $o->month, 1, $o->year);
				$table .= '<td style="text-align:left;"'.$c.'>&nbsp;<a href="javascript:void(0);" onclick="GetLadderListing2(\''.$mo.'\', \''.$pw.'\', '.date("Y", $mkt).', '.date("n", $mkt).', null);">'.date("F Y", $mkt).'</a></td>';
			} else if ($d == 'D') {
				$mkt = mktime(0, 0, 0, $o->month, $o->day, $o->year);
				$table .= '<td style="text-align:left;"'.$c.'>&nbsp;<a href="javascript:void(0);" onclick="GetLadderListing2(\''.$mo.'\', \''.$pw.'\', '.date("Y", $mkt).', '.date("n", $mkt).', '.date("j", $mkt).');">'.date("d F Y, l", $mkt).'</a></td>';
			} else {
				$table .= '<td>&nbsp;</td>';
			}
			$table .= '<td title="Nombre de parties" style="text-align:right; cursor: default"'.$c.'>'.$o->games.'</td>';
			$pga = $pwi = $plo = $ple = $paw = 0;
			if ($o->games > 0) {
				$pga = $o->games - $o->closed;
				if ($pga > 0) {
					$pwi = round(100 * $o->wins / $pga, 2);
					$plo = round(100 * $o->loses / $pga, 2);
					$ple = round(100 * $o->lefts / $pga, 2);
					$paw = round(100 * $o->aways / $pga, 2);
				}
			}
			$pwi = ($pga == 0) ? 'Victoires' : 'Victoires : '.$pwi.'%';
			$plo = ($pga == 0) ? 'D&eacute;faites' : 'D&eacute;faites : '.$plo.'%';
			$ple = ($pga == 0) ? 'Parties quitt&eacute;es' : 'Parties quitt&eacute;es : '.$ple.'%';
			$paw = ($pga == 0) ? 'Non venu' : 'Non venu : '.$paw.'%';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="win" title="'.$pwi.'">'.$o->wins.'</span></td>';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="lose" title="'.$plo.'">'.$o->loses.'</span></td>';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="draw" title="'.$ple.'">'.$o->lefts.'</span></td>';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="info" title="'.$paw.'">'.$o->aways.'</span></td>';
			$table .= '<td title="Ferm&eacute;e" style="text-align:right; cursor: default"'.$c.'>'.$o->closed.'</td>';
			$table .= '<td title="Total XP" style="text-align:right; cursor: default"'.$c.'>'.(($o->balance > 0) ? '+'.$o->balance : $o->balance).'</td>';
			$table .= '</tr>';
			$tga += $o->games;
			$twi += $o->wins;
			$tlo += $o->loses;
			$tle += $o->lefts;
			$taw += $o->aways;
			$tcl += $o->closed;
			$tba += $o->balance;
		}
		$pga = $pwi = $plo = $ple = $paw = 0;
		if ($tga > 0) {
			$pga = $tga - $tcl;
			if ($pga > 0) {
				$pwi = round(100 * $twi / $pga, 2);
				$plo = round(100 * $tlo / $pga, 2);
				$ple = round(100 * $tle / $pga, 2);
				$paw = round(100 * $taw / $pga, 2);
			}
		}
		$pwi = ($pga == 0) ? 'Victoires' : 'Victoires : '.$pwi.'%';
		$plo = ($pga == 0) ? 'D&eacute;faites' : 'D&eacute;faites : '.$plo.'%';
		$ple = ($pga == 0) ? 'Parties quitt&eacute;es' : 'Parties quitt&eacute;es : '.$ple.'%';
		$paw = ($pga == 0) ? 'Non venu' : 'Non venu : '.$paw.'%';
		$table .= '<tr><td colspan="8" style="overflow: hidden; height: 4px; font-size: 0pt">&nbsp;</td></tr>';
		$table .= '<tr><td class="line" colspan="8"></td></tr>';
		$table .= '<tr>';
		$table .= '<td style="text-align:left; cursor: default">&nbsp;Total</td>';
		$table .= '<td title="Nombre de parties" style="text-align:right; cursor: default">'.$tga.'</td>';
		$table .= '<td style="text-align:right; cursor: help"><span class="win" title="'.$pwi.'">'.$twi.'</span></td>';
		$table .= '<td style="text-align:right; cursor: help"><span class="lose" title="'.$plo.'">'.$tlo.'</span></td>';
		$table .= '<td style="text-align:right; cursor: help"><span class="draw" title="'.$ple.'">'.$tle.'</span></td>';
		$table .= '<td style="text-align:right; cursor: help"><span class="info" title="'.$paw.'">'.$taw.'</span></td>';
		$table .= '<td title="Ferm&eacute;e" style="text-align:right; cursor: default">'.$tcl.'</td>';
		$table .= '<td title="Total XP" style="text-align:right; cursor: default">'.(($tba > 0) ? '+'.$tba : $tba).'</td>';
		$table .= '</tr>';
		$table .= '</table>';
		return $table;
	}

	function createGamesTable($t, $r) {
		$q = mysql_query($r) or die(mysql_error());
		$table = '<table style="width: 96%; margin: 0px 2%;">';
		$table .= '<colgroup><col /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="50" /></colgroup>';
		$table .= '<tr><td align="right" colspan="7"><strong>'.$t.'</strong></td></tr>';
		$table .= '<tr><td class="line" colspan="7"></td></tr>';
		$m = 0;
		while ($o = mysql_fetch_object($q)) {
			$m = $m + 1;
			$c = (($m % 2 == 0) ? ' class="alternate"' : '');
			
			if ($o->xp > 0) {
				$txt = 'Victoire';
				$css = ' class="win"';
			} elseif ($o->xp == 0) {
				$txt = 'Ferm&eacute;e';
				$css = '';
			} elseif ($o->xp < 0 and $o->resultat == 'left') {
				$txt = 'Quitt&eacute;e';
				$css = ' class="draw"';
			} elseif ($o->xp < 0 and $o->resultat == 'away') {
				$txt = 'Pas venu';
				$css = ' class="info"';
			} else {
				$txt = 'Perdue';
				$css = ' class="lose"';
			}
			$table .= '<tr>';
			$table .= '<td style="text-align:left;"'.$c.'>&nbsp;<a href="?f=laddervip_game&id='.$o->id.'">'.date("H:i", $o->opened).', Game #'.$o->id.'</a></td>';
			$table .= '<td style="text-align:right;"'.$c.'>&nbsp;</td>';
			$table .= '<td colspan="3" style="text-align:right;"'.$c.'><span'.$css.'>'.$txt.'</span></td>';
			$table .= '<td style="text-align:right;"'.$c.'>&nbsp;</td>';
			$table .= '<td style="text-align:right;"'.$c.'><span'.$css.'>'.(($o->xp > 0) ? '+'.$o->xp : $o->xp).'</span></td>';
			$table .= '</tr>';
		}
		$table .= '</table>';
		return $table;
	}

	function createPagedTable($t, $r, $tp, $cp, $mo, $img) {
		$q = mysql_query($r) or die(mysql_error());
		$table = '<table style="width: 96%; margin: 0px 2%;">';
		$table .= '<colgroup><col width="30" /><col /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="50" /></colgroup>';
		$table .= '<tr><td align="right" colspan="9"><strong>'.$t.'</strong></td></tr>';
		$table .= '<tr><td class="line" colspan="9"></td></tr>';
		$m = 0;
		while ($o = mysql_fetch_object($q)) {
			$m = $m + 1;
			$c = (($m % 2 == 0) ? ' class="alternate"' : '');
			$mkt = mktime(0, 0, 0, $o->month, 1, $o->year);
			$table .= '<tr>';
			$table .= '<td style="text-align: center"'.$c.'><a href="?f=player_profile&player='.$o->pwith.'"><img src="/ligue/img/'.$img.'.gif" alt="" title="Voir le profil de '.$o->pwith.'" border="0" /></a></td>';
			$table .= '<td style="text-align:left;"'.$c.'><a href="javascript:void(0);" onclick="GetLadderListing2(\''.$mo.'\', \''.$o->pwith.'\', null, null, null);">'.$o->pwith.'</a></td>';
			$table .= '<td title="Nombre de parties" style="text-align:right; cursor: default"'.$c.'>'.$o->games.'</td>';
			$pga = $pwi = $plo = $ple = $paw = 0;
			if ($o->games > 0) {
				$pga = $o->games - $o->closed;
				if ($pga > 0) {
					$pwi = round(100 * $o->wins / $pga, 2);
					$plo = round(100 * $o->loses / $pga, 2);
					$ple = round(100 * $o->lefts / $pga, 2);
					$paw = round(100 * $o->aways / $pga, 2);
				}
			}
			$pwi = ($pga == 0) ? 'Victoires' : 'Victoires : '.$pwi.'%';
			$plo = ($pga == 0) ? 'D&eacute;faites' : 'D&eacute;faites : '.$plo.'%';
			$ple = ($pga == 0) ? 'Parties quitt&eacute;es' : 'Parties quitt&eacute;es : '.$ple.'%';
			$paw = ($pga == 0) ? 'Non venu' : 'Non venu : '.$paw.'%';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="win" title="'.$pwi.'">'.$o->wins.'</span></td>';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="lose" title="'.$plo.'">'.$o->loses.'</span></td>';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="draw" title="'.$ple.'">'.$o->lefts.'</span></td>';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="info" title="'.$paw.'">'.$o->aways.'</span></td>';
			$table .= '<td title="Ferm&eacute;e" style="text-align:right; cursor: default"'.$c.'>'.$o->closed.'</td>';
			$table .= '<td title="Total XP" style="text-align:right; cursor: default"'.$c.'>'.(($o->balance > 0) ? '+'.$o->balance : $o->balance).'</td>';
			$table .= '</tr>';
		}
		$table .= '<tr><td colspan="9" style="overflow: hidden; height: 14px; font-size: 0pt">&nbsp;</td></tr>';
		$table .= '<tr><td class="line" colspan="9"></td></tr>';
		$table .= '<tr><td colspan="9" style="text-align: right">';
		for ($i = 0; $i < $tp; $i++) {
			if ($i != $cp) {
				$table .= ($i == 0 ? '' : '&nbsp;-&nbsp;').'<a href="javascript:void(0);" onclick="GetLadderPage(\''.$mo.'\', '.$i.');"">'.($i + 1).'</a>';
			} else {
				$table .= ($i == 0 ? '' : '&nbsp;-&nbsp;').($i + 1);
			}
		}
		$table .= '</td></tr>';
		$table .= '</table>';
		return $table;
	}
	
	function createCaptainTable($t, $r, $mo) {
		$q = mysql_query($r) or die(mysql_error());
		$table = '<table style="width: 96%; margin: 0px 2%;">';
		$table .= '<colgroup><col width="30" /><col /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="35" /><col width="50" /></colgroup>';
		$table .= '<tr><td align="right" colspan="8"><strong>'.$t.'</strong></td></tr>';
		$table .= '<tr><td class="line" colspan="8"></td></tr>';
		$m = 0;
		$tga = $twi = $tlo = $tle = $taw = $tcl = $tba = 0;
		while ($o = mysql_fetch_object($q)) {
			$m = $m + 1;
			$c = (($m % 2 == 0) ? ' class="alternate"' : '');
			$mkt = mktime(0, 0, 0, $o->month, 1, $o->year);
			$table .= '<tr>';
			$table .= '<td style="text-align:left;"'.$c.'>&nbsp;<a href="javascript:void(0);" onclick="GetLadderListing2(\''.$mo.'\', \''.$o->iscap.'\', null, null, null);">'.$o->iscaplib.'</a></td>';
			$table .= '<td title="Nombre de parties" style="text-align:right; cursor: default"'.$c.'>'.$o->games.'</td>';
			$pga = $pwi = $plo = $ple = $paw = 0;
			if ($o->games > 0) {
				$pga = $o->games - $o->closed;
				if ($pga > 0) {
					$pwi = round(100 * $o->wins / $pga, 2);
					$plo = round(100 * $o->loses / $pga, 2);
					$ple = round(100 * $o->lefts / $pga, 2);
					$paw = round(100 * $o->aways / $pga, 2);
				}
			}
			$pwi = ($pga == 0) ? 'Victoires' : 'Victoires : '.$pwi.'%';
			$plo = ($pga == 0) ? 'D&eacute;faites' : 'D&eacute;faites : '.$plo.'%';
			$ple = ($pga == 0) ? 'Parties quitt&eacute;es' : 'Parties quitt&eacute;es : '.$ple.'%';
			$paw = ($pga == 0) ? 'Non venu' : 'Non venu : '.$paw.'%';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="win" title="'.$pwi.'">'.$o->wins.'</span></td>';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="lose" title="'.$plo.'">'.$o->loses.'</span></td>';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="draw" title="'.$ple.'">'.$o->lefts.'</span></td>';
			$table .= '<td style="text-align:right; cursor: help"'.$c.'><span class="info" title="'.$paw.'">'.$o->aways.'</span></td>';
			$table .= '<td title="Ferm&eacute;e" style="text-align:right; cursor: default"'.$c.'>'.$o->closed.'</td>';
			$table .= '<td title="Total XP" style="text-align:right; cursor: default"'.$c.'>'.(($o->balance > 0) ? '+'.$o->balance : $o->balance).'</td>';
			$table .= '</tr>';
			$tga += $o->games;
			$twi += $o->wins;
			$tlo += $o->loses;
			$tle += $o->lefts;
			$taw += $o->aways;
			$tcl += $o->closed;
			$tba += $o->balance;
		}
		$pwi = ($pga == 0) ? 'Victoires' : 'Victoires : '.$pwi.'%';
		$plo = ($pga == 0) ? 'D&eacute;faites' : 'D&eacute;faites : '.$plo.'%';
		$ple = ($pga == 0) ? 'Parties quitt&eacute;es' : 'Parties quitt&eacute;es : '.$ple.'%';
		$paw = ($pga == 0) ? 'Non venu' : 'Non venu : '.$paw.'%';
		$table .= '<tr><td colspan="8" style="overflow: hidden; height: 4px; font-size: 0pt">&nbsp;</td></tr>';
		$table .= '<tr><td class="line" colspan="8"></td></tr>';
		$table .= '<tr>';
		$table .= '<td style="text-align:left; cursor: default">&nbsp;Total</td>';
		$table .= '<td title="Nombre de parties" style="text-align:right; cursor: default">'.$tga.'</td>';
		$table .= '<td style="text-align:right; cursor: help"><span class="win" title="'.$pwi.'">'.$twi.'</span></td>';
		$table .= '<td style="text-align:right; cursor: help"><span class="lose" title="'.$plo.'">'.$tlo.'</span></td>';
		$table .= '<td style="text-align:right; cursor: help"><span class="draw" title="'.$ple.'">'.$tle.'</span></td>';
		$table .= '<td style="text-align:right; cursor: help"><span class="info" title="'.$paw.'">'.$taw.'</span></td>';
		$table .= '<td title="Ferm&eacute;e" style="text-align:right; cursor: default">'.$tcl.'</td>';
		$table .= '<td title="Total XP" style="text-align:right; cursor: default">'.(($tba > 0) ? '+'.$tba : $tba).'</td>';
		$table .= '</tr>';
		$table .= '</table>';
		return $table;
	}
	
?>