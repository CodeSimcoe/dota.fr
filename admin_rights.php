<?php
	
	include('refresh.php');

	ArghSession::exit_if_not_rights(
		RightsMode::WEBMASTER
	);
	
	$player = '';
	if (isset($_GET['player'])) {
		$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));
	}

	function input_radio($rights_mode, $rights_base) {
		$label = RightsMode::get_rights_label($rights_mode);
		if ($label == '') $label = Lang::RIGHTS_NONE;
		$checked = '';
		if ($rights_mode == $rights_base) $checked = ' checked="checked"';
		return '<input type="radio" name="rdo_rights" id="rdo_rights" value="'.$rights_mode.'"'.$checked.' />&nbsp;'.$label.'<br  />';
	}
	
	function rights_checkbox($rights_base, $rights_mode) {
		$label = RightsMode::get_rights_label($rights_mode);
		if ($label == '') $label = Lang::RIGHTS_NONE;
		if ($rights_mode == 0) {
			$checked = ($rights_base == 0) ? ' checked="checked"' : '';
		} else {
			$checked = (($rights_base & $rights_mode) == $rights_mode) ? ' checked="checked"' : '';
		}
		return '<input type="checkbox" name="rdo_rights[]" id="rdo_rights[]" value="'.$rights_mode.'"'.$checked.' />&nbsp;'.$label.'<br  />';
	}

	function select_option($rights_base, $rights_mode) {
		$label = RightsMode::get_rights_label($rights_mode);
		if ($label == '') $label = Lang::RIGHTS_NONE;
		return '<option value="'.$rights_mode.'"'.attr_($rights_base, $rights_mode).'>'.$label.'</option>';
	}
	
	ArghPanel::begin_tag(Lang::ADMIN_RIGHTS_TITLE);

?>
<form name="frmPlayers" method="post" action="?f=admin_rights">
<input type="text" name="tbSearch" id="tbSearch" style="width: 400px;" />
<input type="submit" value="<?php echo Lang::LOOK_FOR; ?>" style="width: 100px;" />
<?php
	if (isset($_POST['tbSearch'])) {
		echo '<br /><br />';
		$search = trim(mysql_real_escape_string($_POST['tbSearch']));
		if ($search != '') {
			$req = "
				SELECT username, ggc, bnet 
				FROM lg_users
				WHERE username LIKE '%".$search."%'
				OR ggc LIKE '%".$search."%'
				OR bnet LIKE '%".$search."%'
				ORDER BY username";
			$res = mysql_query($req) or die(mysql_error());
			if (mysql_num_rows($res) != 0) {
				echo '<table class="listing">';
				echo '<colgroup><col width="200" /><col width="200" /><col /></colgroup>';
				echo '<thead><tr><th>'.Lang::USERNAME.'</th><th>'.Lang::GARENA_ACCOUNT.'</th><th>'.Lang::BNET_ACCOUNT.'</th></tr></thead>';
				$count = 0;
				while ($obj = mysql_fetch_object($res)) {
					echo '<tr'.Alternator::get_alternation($count).'>';
					echo '<td><a href="?f=admin_rights&player='.$obj->username.'">'.$obj->username.'</a></td>';
					echo '<td>'.$obj->ggc.'</td>';
					echo '<td>'.$obj->bnet.'</td>';
					echo '</tr>';
				}
				echo '</table>';
			} else {
				echo Lang::SEARCH_NO_RESULT;
			}
		} else {
			echo Lang::SEARCH_NO_CRITERIA;
		}
	} else if ($player == '') {
		$req = "
			SELECT username, rights_base 
			FROM lg_users
			WHERE rights_base != 0
			ORDER BY rights_base, username";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			echo '<br /><br />';
			echo '<table class="listing">';
			echo '<colgroup>
					<col width="200" />
					<col />
				</colgroup>
				<thead>
					<tr>
						<th>'.Lang::USERNAME.'</th>
						<th>'.Lang::ROLE.'</th>
					</tr>
				</thead>';
			$count = 0;
			while ($obj = mysql_fetch_object($res)) {
				echo '<tr'.Alternator::get_alternation($count).'>';
				echo '<td><a href="?f=admin_rights&player='.$obj->username.'">'.$obj->username.'</a></td>';
				echo '<td>'.RightsMode::colorize_rights(RightsMode::get_rights_label($obj->rights_base)).'</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	}
?>
</form>
<?php>

	ArghPanel::end_tag();
	
	if ($player != '') {
		$msg = '';
		if (isset($_POST['validate'])) {

			$rank = RightsMode::get_rights_label($_POST['rights_base']);
			$rights_base = $_POST['rights_base'];
			$rights = 0;
			if(!empty($_POST["rdo_rights"])) {
				for ($i = 0; $i < count($_POST["rdo_rights"]); $i++) {
					$value = (int)$_POST["rdo_rights"][$i];
					if ($value == 0) { $rights = 0; break; }
					$rights |= $value;
				}
			}
			
			$msg = Lang::MODIFICATIONS_SAVED;
			$res = mysql_query("
				UPDATE lg_users SET
					rights = ".$rights.",
					rights_base = ".$rights_base.",
					rank = '".$rank."'
				WHERE username = '".$player."'
			") or die(mysql_error());

			// AdminLog
			$al = new AdminLog(sprintf(Lang::ADMIN_LOG_RIGHTS_MODIFIED, $player, $player, ($rank == '') ? Lang::RIGHTS_NONE : $rank), AdminLog::TYPE_ADMIN);
			$al->save_log();

		}
		$req = "
			SELECT username, rights, rights_base FROM lg_users WHERE username = '".$player."'";
		$res = mysql_query($req) or die(mysql_error());
		if (mysql_num_rows($res) != 0) {
			$obj = mysql_fetch_object($res);
			$player = $obj->username;
			
			echo '<form name="frmRights" method="post" action="?f=admin_rights&player='.$player.'">';
			ArghPanel::begin_tag(Lang::ADMIN_RIGHTS_TITLE);

			if ($msg != '') echo '<div style="color: red; font-weight: bold; text-align: center; margin-bottom: 10px;">'.$msg.'</div><script type="text/javascript">$(document).ready(function() { setTimeout(function() { document.location.href= "?f=admin_rights"; }, 1000); });</script>';

			echo '<table border="0" cellpadding="2" cellspacing="0">';
			echo '<colgroup><col width="170" /><col width="430" /></colgroup>';
			echo '<tr><td>'.Lang::PLAYER.'</td><td><a href="http://www.dota.fr/ligue/?f=player_profile&player='.$obj->username.'">'.$obj->username.'</a></td></tr>';
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			echo '<tr><td>'.Lang::ROLE.'</td><td><select name="rights_base" id="rights_base" style="width: 350px;">';
			echo select_option($obj->rights_base, 0);
			echo select_option($obj->rights_base, RightsMode::WEBMASTER);
			echo '<optgroup label="'.Lang::LEAGUE.'">';
			echo select_option($obj->rights_base, RightsMode::LEAGUE_HEADADMIN);
			echo select_option($obj->rights_base, RightsMode::LEAGUE_ADMIN);
			echo '</optgroup>';
			echo '<optgroup label="'.Lang::LADDER.'">';
			echo select_option($obj->rights_base, RightsMode::LADDER_HEADADMIN);
			echo select_option($obj->rights_base, RightsMode::LADDER_ADMIN);
			echo '</optgroup>';
			echo '<optgroup label="'.Lang::LADDER_VIP.'">';
			echo select_option($obj->rights_base, RightsMode::VIP_HEADADMIN);
			echo select_option($obj->rights_base, RightsMode::VIP_ADMIN);
			echo select_option($obj->rights_base, RightsMode::VIP_VOUCHER);
			echo '</optgroup>';
			echo '<optgroup label="News">';
			echo select_option($obj->rights_base, RightsMode::NEWS_HEADADMIN);
			echo select_option($obj->rights_base, RightsMode::NEWS_NEWSER);
			echo '</optgroup>';
			echo '<optgroup label="'.Lang::SHOUTCAST.'">';
			echo select_option($obj->rights_base, RightsMode::SHOUTCAST_HEADADMIN);
			echo select_option($obj->rights_base, RightsMode::SHOUTCAST_SHOUTCASTER);
			echo '</optgroup>';
			echo '</select></td></tr>';
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			echo '<tr><td valign="top">'.Lang::RIGHTS.'</td><td>';
			echo rights_checkbox($obj->rights, 0);
			echo rights_checkbox($obj->rights, RightsMode::WEBMASTER);
			echo '<br />'.Lang::LEAGUE.'<blockquote>';
			echo rights_checkbox($obj->rights, RightsMode::LEAGUE_HEADADMIN);
			echo rights_checkbox($obj->rights, RightsMode::LEAGUE_ADMIN);
			echo '</blockquote>'.Lang::LADDER.'<blockquote>';
			echo rights_checkbox($obj->rights, RightsMode::LADDER_HEADADMIN);
			echo rights_checkbox($obj->rights, RightsMode::GUARDIAN_ADMIN);
			echo rights_checkbox($obj->rights, RightsMode::LADDER_ADMIN);
			echo '</blockquote>'.Lang::LADDER_VIP.'<blockquote>';
			echo rights_checkbox($obj->rights, RightsMode::VIP_HEADADMIN);
			echo rights_checkbox($obj->rights, RightsMode::VIP_ADMIN);
			echo rights_checkbox($obj->rights, RightsMode::VIP_VOUCHER);
			echo '</blockquote>'.Lang::NEWS.'<blockquote>';
			echo rights_checkbox($obj->rights, RightsMode::NEWS_HEADADMIN);
			echo rights_checkbox($obj->rights, RightsMode::NEWS_NEWSER);
			echo '</blockquote>'.Lang::SHOUTCAST.'<blockquote>';
			echo rights_checkbox($obj->rights, RightsMode::SHOUTCAST_HEADADMIN);
			echo rights_checkbox($obj->rights, RightsMode::SHOUTCAST_SHOUTCASTER);
			echo '</blockquote>'.Lang::SCREENSHOTS.'<blockquote>';
			echo rights_checkbox($obj->rights, RightsMode::SCREENSHOTS_ADMIN);
			echo '</blockquote></td></tr>';
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			echo '<tr><td colspan="2" style="text-align: center;">';
			echo '<input type="button" name="cancel" id="cancel" value="'.Lang::CANCEL.'" onclick="document.location.href = \'?f=admin_rights\';" style="width: 150px;" />';
			echo '<input type="submit" name="validate" id="validate" value="'.Lang::VALIDATE.'" style="width: 150px;" />';
			echo '</td></tr>';
			echo '</table>';

			ArghPanel::end_tag();
			echo '</form>';

		}
	}

?>