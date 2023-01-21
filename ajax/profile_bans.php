<?php
	//Page appelee par AJAX
	define('ABSOLUTE_PATH', '/var/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/RightsMode.php';
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'classes/Alternator.php';
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';
	require_once ABSOLUTE_PATH.'mysql_connect.php';
?>
<script language="javascript">
	function show(i) {
		$('#a' + i).hide();
		$('#b' + i).fadeIn();
	}
</script>

<br />
<table class="simple">
<?php
	$isAdminLadder = ArghSession::is_rights(array(RightsMode::LADDER_HEADADMIN, RightsMode::LADDER_ADMIN, RightsMode::VIP_HEADADMIN, RightsMode::VIP_ADMIN));
	
	
	
	function shortDesc($str, $len) {
		$str = str_replace("'", "`", stripslashes($str));
		return ((strlen($str) > $len) ? htmlentities(substr($str, 0, $len-2)).' <b><a href="javascript:alert(\''.Lang::REASON.': '.htmlentities($str).'\');">...</a></b>' : htmlentities($str));
	}
	
	$shortName = substr($_GET['player'], 0, 25);
	$player = mysql_real_escape_string($shortName);
	
	if (!$isAdminLadder && ArghSession::get_username() != $player) exit;
	
	if ($isAdminLadder) {
		$req = "SELECT * 
			FROM lg_ladderbans_follow
			WHERE username = '".$player."' 
			ORDER BY quand DESC";
	} else {
		$req = "SELECT * 
			FROM lg_ladderbans_follow
			WHERE username = '".$player."' 
			AND afficher = 1
			ORDER BY quand DESC";
	}
	$t = mysql_query($req);
	if (mysql_num_rows($t)) {
		$i = 0;
		echo '<tr>
				<th>'.Lang::TYPE.'</th>
				<th>'.Lang::VALUE.'</th>
				<th>'.Lang::REASON.'</th>
				<th>'.Lang::ADMIN.'</th>
				<th>'.Lang::DATE.'</th>
				<th>'.(($isAdminLadder) ? Lang::SHOW : '').'</th>
			</tr>
			<tr><td colspan="6" class="line"></td></tr>';
		while ($l = mysql_fetch_object($t)) {
			$alt = Alternator::get_alternation($i);;
			//Force
			if ($l->type == 'warning') {
				$type = '<span class="draw"><b>'.Lang::WARNING.'</b></span>';
				$force = '<img src="img/'.(($l->force == 4) ? 'red' : $l->force.'yellow').'card.gif" alt="'.$l->force.'" />';
			} else {
				$type = '<span class="lose"><b>'.Lang::BAN.'</b></span>';
				$force = ($l->force == 0) ? '<img src="img/infini.gif" alt="'.Lang::UNDEFINED.'" />' : $l->force.'j';
			}
			
			$motif = $l->motif;
			if ($l->game_id > 0) {
				$motif = '#'.$l->game_id.': '.$l->motif;
			}
			$motif = htmlentities(stripslashes($motif));
			$extender = (strlen($motif) > 20);
			
			//<td'.$alt.'><center>'.shortDesc(stripslashes($motif), 30).'</center></td>
			echo '<tr height="25" valign="middle">
					<td'.$alt.'><center>'.$type.'</center></td>
					<td'.$alt.'><center><b>'.$force.'</b></center></td>
					
					<td'.$alt.'>
						<div id="a'.$i.'">'.($extender ? '<a href="javascript:show('.$i.');"><img src="img/plus.gif" width="16" height="16" /></a>&nbsp;' : '').substr($motif, 0, 20).($extender ? '...' : '').'</div>
						<div style="display: none; width: 150px;" id="b'.$i.'">'.$motif.'</div>
					</td>
					<td'.$alt.'><center>'.(($l->admin == 'LadderGuardian') ? '<span class="vip">'.$l->admin.'</span>' : '<a href="?f=player_profile&player='.$l->admin.'">'.$l->admin.'</a>').'</center></td>
					<td'.$alt.'><center>'.date(Lang::DATE_FORMAT_HOUR, $l->quand).'</center></td>
					<td'.$alt.'><center>'.(($isAdminLadder) ? '<input type="checkbox" name="ban'.$l->id.'" onClick="swapDisplay('.$l->id.')" '.(($l->afficher == 1) ? 'checked' : '').'/>' : '').'</center></td>
				</tr>';
		}
	} else {
		echo '<tr><td colspan="6"><center>'.htmlentities(Lang::NO_ENTRY).'</center></td></tr>';
	}
?>
</table>