<?php
	require_once '/home/www/ligue/classes/RightsMode.php';
	require_once '/home/www/ligue/classes/ArghSession.php';
	ArghSession::begin();
	require_once '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';
	require_once '/home/www/ligue/mysql_connect.php';
	
	//Escapes strings to be included in javascript
	function jsspecialchars($s) {
   		return preg_replace('/([^ !#$%@()*+,-.\x30-\x5b\x5d-\x7e])/e',"'\\x'.(ord('\\1')<16? '0': '').dechex(ord('\\1'))", $s);
	}
	
	//Nbr commentaires / page
	$nb_aff = 10;
	
	$start = isset($_GET['start']) ? (int)$_GET['start'] - 1 : 0;
	$news_id = (int)$_GET['id'];
	
	//nombre de messages
	$sreq = "SELECT count(*) FROM lg_comment WHERE news_id = '".$news_id."'";
	$st = mysql_query($sreq);
	$sl = mysql_fetch_row($st);
	$nb_msg = $sl[0];
	
	//subdivision en plusieurs pages
	$nb_aff = 10;
	$nb_pages = floor($nb_msg / $nb_aff);
	if (floor($nb_msg / $nb_aff) != $nb_msg / $nb_aff) {
		$nb_pages++;
	}
	
	echo Lang::PAGES.': ';
	for ($k = 1; $k <= $nb_pages; $k++) {
		$a = ($k - 1) * $nb_aff + 1;
		if ($start + 1 != $a) {
			echo '<a href="javascript:show_comments('.$news_id.', '.$a.');">'.$k.'</a> ';
		} else {
			echo '<b>'.$k.'</b> ';
		}
	}
	echo '<br />';
	
	$req = "SELECT *
			FROM lg_comment
			WHERE news_id = '".$news_id."'
			ORDER BY id ASC
			LIMIT ".$start.", ".$nb_aff;
	$t = mysql_query($req);
	if (mysql_num_rows($t)) {
		echo '<table class="listing">
			<colgroup>
				<col width="25%" />
				<col width="75%" />
			</colgroup>';
		while ($l = mysql_fetch_object($t)) {
			echo '<tr><td colspan="2"><b>#'.$l->post_id.'</b> - <font size="1">'.htmlentities(Lang::POSTED_ON).' '.date(Lang::DATE_FORMAT_HOUR, $l->post_date).'</font></td></tr>';
			
			//Infos sur le joueur
			$sreq = "SELECT u.avatar, c.tag, c.id
					FROM lg_users u LEFT JOIN lg_clans c ON u.clan = c.id
					WHERE username = '".$l->poster."'";
			$st = mysql_query($sreq);
			$sl = mysql_fetch_row($st);
			$avatar = $sl[0];
			
			$edit = '';
			if (ArghSession::is_logged()) {
				if (ArghSession::is_rights(array(RightsMode::NEWS_HEADADMIN, RightsMode::NEWS_NEWSER))) {
					$edit .= '<a href="javascript:nuke_msg('.$l->id.');">'.Lang::NUKE.'</a> - ';
				}
				if (ArghSession::get_username() == $l->poster || ArghSession::is_rights(array(RightsMode::NEWS_HEADADMIN, RightsMode::NEWS_NEWSER))) {
					$edit .= '<a href="?f=news_comment_edit&id='.$l->id.'">'.strtolower(Lang::EDIT).'</a> - ';
				}
				$edit .= '<a href="javascript:add_quote(\''.addslashes($l->poster).'\',\''.jsspecialchars($l->comment).'\')">'.Lang::QUOTE.'</a>';
			}
			
			if ($l->edit_date != 0) {
				$l->comment .= '<br /><br /><font size="1">'.htmlentities(Lang::LAST_EDIT_ON).' '.date(Lang::DATE_FORMAT_HOUR, $l->edit_date).'</font>';
			}
			
			echo '<tr><td colspan="2" align="right">'.$edit.'</td></tr>';
			echo '<tr>
				<td><b><a href="?f=player_profile&player='.$l->poster.'">'.$l->poster.'</a></b></td>
				<td class="line"></td>
				</tr>
				<tr><td valign="top">';
			if (!empty($sl[1])) {
				echo Lang::TEAM.': <a href="?f=team_profile&id='.$sl[2].'">'.$sl[1].'</a>';
			}
			echo '</td><td rowspan="2" valign="top" id="msg'.$l->id.'">'.stripslashes(stripslashes($l->comment)).'</td></tr>';
			echo '<tr><td valign="top" style="min-height: 150px;">';
			echo (strlen($avatar) > 0) ? '<img src="'.$avatar.'" alt="" />' : '';
			echo '</td></tr>';
			echo '<tr><td colspan="2" class="spacer"></td></tr>';
		}
		echo '</table>';
		
		echo Lang::PAGES.': ';
		for ($k = 1; $k <= $nb_pages; $k++) {
			$a = ($k - 1) * $nb_aff + 1;
			if ($start + 1 != $a) {
				echo '<a href="javascript:show_comments('.$news_id.', '.$a.');">'.$k.'</a> ';
			} else {
				echo '<b>'.$k.'</b> ';
			}
		}
	}
?>
