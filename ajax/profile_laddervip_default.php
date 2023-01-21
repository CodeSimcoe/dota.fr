<?php

	require('mysql_connect.php');
	require('ladder_functions.php');
	
	$player = '';
	if (isset($_GET['player'])) {
		$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));
	}
		
	if ($player != '') {
		
		$req = "
			SELECT
			 rank,
			 games,
			 wins,
			 loses,
			 lefts,
			 aways,
			 closed,
			 balance
			FROM
			 lg_laddervip_stats_ranks
			WHERE
			 player = '".$player."'";
		$qry = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($qry) > 0) {
			$o = mysql_fetch_object($qry);
			echo '<table style="width: 96%; margin: 0px 2%;">';
			echo '<colgroup><col width="200px" /><col /></colgroup>';
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			echo '<tr><td colspan="2" align="left"><strong>Ladder VIP</strong></td></tr>';
			echo '<tr><td colspan="2" class="line"></td></tr>';
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			$rank = $o->rank;
			$rank .= '<sup>'.(($rank == 1) ? 'er' : '&egrave;me').'</sup>';
			echo '<tr><td><img src="img/xp_vip.gif" alt="" align="absmiddle">&nbsp;XP :</td><td><strong>'.XPColorize($o->balance).' ('.$rank.')</strong></td></tr>';
			echo '<tr><td valign="top">Global :</td><td>';
			echo '<b>'.$o->games.'</b> parties dont '.$o->closed.'</span> ferm&eacute;es<br />';
			echo '</td></tr>';
			echo '<tr><td valign="top">Statistiques :</td><td>';
			echo '<b>'.($o->games - $o->closed).'</b> parties jou&eacute;es<br />';
			$wi = $lo = $le = $aw = 0;
			if ($o->games > 0) {
				$subt = $o->games - $o->closed;
				if ($subt > 0) {
					$wi = round(100 * $o->wins / $subt, 2);
					$lo = round(100 * $o->loses / $subt, 2);
					$le = round(100 * $o->lefts / $subt, 2);
					$aw = round(100 * $o->aways / $subt, 2);
				}
			}
			echo '<b><span class="win">'.$o->wins.'</span></b> victoires <span class="info">('.$wi.'%)</span><br />';
			echo '<b><span class="lose">'.$o->loses.'</span></b> d&eacute;faites <span class="info">('.$lo.'%)</span><br />';
			echo '<b><span class="draw">'.$o->lefts.'</span></b> parties quitt&eacute;es <span class="info">('.$le.'%)</span><br />';
			echo '<b><span class="info">'.$o->aways.'</span></b> fois non venu <span class="info">('.$aw.'%)</span><br />';
			echo '</td></tr>';
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			echo '<tr><td colspan="2" align="center"><img src="http://chart.apis.google.com/chart?cht=p3&chd=t:'.$wi.','.$lo.','.$le.','.$aw.'&chs=450x150&chl=Victoires|Defaites|Quittees|Pas%20venu&chco=66ff66,ff0000,ffff33,999999&chf=bg,s,000000" alt="" /></td></tr>';
			echo '</table>';
		}
	}

?>