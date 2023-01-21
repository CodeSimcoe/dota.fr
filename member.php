<script language="javascript">
	function UpdateBanner(banner) {
		banner_path = 'img/banners/' + banner + '.jpg';
		$('#argh_banner').attr('src', banner_path);
		$.get('ajax/update_banner.php', {banner: banner});
	}
	
	function UpdateTheme() {
		var theme = $('#theme_select').val();
		//alert(theme);
		$.get('ajax/update_theme.php',
			{theme: theme},
			function(data) {
				window.location = '?f=member';
			});
	}

	//Changement dynamique de l'image du rang
	function img(num, rank) {
		$('#img_rang' + num).attr('src', $('#select_rang' + num).val() + '.gif');
	}

	function CountryPicUpdate(pays) {
		$('#countrypic').attr('src', 'img/flag/' + pays + '.gif');
	}
	function LangPicUpdate(lang) {
		$('#langpic').attr('src', 'img/' + lang + '.gif');
	}

	function MsgOkCancel() {
		var fRet;
		fRet = confirm('<?php echo Lang::TEAM_CONFIRM_DELETE; ?>');
		if (fRet) {
			window.open('team_delete.php', '', 'HEIGHT=150,resizable=no,scrollbars=no,WIDTH=400');
		}
	}
	
	function UpdateClanRank(user, crank) {
		$.get('ajax/set_clanrank.php', {u: user, r: crank});
	}
</script>

<?php
	require_once 'classes/Transaction.php';
	require_once 'classes/LangCodes.php';
	include 'refresh.php';
	ArghSession::exit_if_not_logged();
	
	function isBanned($player) {
		$req = "SELECT * FROM lg_ladderbans WHERE qui = '".$player."'";
		$t = mysql_query($req);
		return (mysql_num_rows($t) > 0);
	}

	//Pays
	$dir = 'img/flag/';
	if (is_dir($dir)) {
	    if ($dh = opendir($dir)) {
		$flags = array();
	        while (($file = readdir($dh)) !== false) {
			if (!is_dir($file)) $flags[] = $file;
		}
		sort($flags);
	        closedir($dh);
	    }
	}
	
	//Suppression de l'avatar
	if ($_GET['action'] == 'avatar_delete') {
		$upd = "UPDATE lg_users SET avatar = '' WHERE username = '".ArghSession::get_username()."';";
		mysql_query($upd);
	}
	
	//Mise à jour des infos de l'user
	if (isset($_POST['_maj_'])) {
		if (!isBanned(ArghSession::get_username())) {
			$upd = "UPDATE lg_users
					SET bnet = '".mysql_real_escape_string($_POST['_bnet_'])."', 
						ggc = '".mysql_real_escape_string($_POST['_ggc_'])."', 
						rgc_account = '".mysql_real_escape_string($_POST['_rgc_account_'])."', 
						mail = '".mysql_real_escape_string($_POST['_mail_'])."', 
						qauth = '".mysql_real_escape_string($_POST['_qauth_'])."', 
						birth = '".substr($_POST['_year_'], 0, 4)."-".substr($_POST['_month_'],0, 2)."-".substr($_POST['_day_'], 0, 2)."', 
						country='".mysql_real_escape_string($_POST['_country_'])."', 
						city = '".mysql_real_escape_string($_POST['_city_'])."',
						lang = '".mysql_real_escape_string($_POST['_lang_'])."'
					WHERE username = '".ArghSession::get_username()."'";
					//theme = '".mysql_real_escape_string($_POST['_theme_'])."',
			mysql_query($upd) or die(mysql_error());
			include 'refresh.php';
		}
	}
	
	//Leave Clan
	if (isset($_POST['_leaveteam_'])) {
		if (ArghSession::get_clan_rank() == ClanRanks::TAUREN) {
			$error_leaveteam = true;
		} else {
			$upd = "UPDATE lg_users
					SET crank = '".ClanRanks::PEON."',
						jclan = '0',
						clan = '0'
					WHERE username='".ArghSession::get_username()."'";
			mysql_query($upd);
		}
	}
	
	//Mise à jour des infos du clan
	if (isset($_POST['_newname_'])) {
		//Vérification Name
		$req = "SELECT * FROM lg_clans";
		$t = mysql_query($req);
		while ($l = mysql_fetch_object($t)) {
			if ((strtolower($l->name) == strtolower($_POST['_newname_']) && $l->id != ArghSession::get_clan()) || $_POST['_newname_'] == '') {
				exit(Lang::MEMBER_INVALID_TEAM_NAME.'<br /><a href="?f=member">'.Lang::GO_BACK.'</a>');
			}
		}
		
		//Vérification Tag
		$req = "SELECT id
				FROM lg_clans
				WHERE tag LIKE '".mysql_real_escape_string($_POST['_newtag_'])."'
				AND id != '".ArghSession::get_clan()."'";
		$t = mysql_query($req);
		if (mysql_num_rows($t)) {
			exit(Lang::MEMBER_TAG_ALREADY_IN_USE.'<br /><a href="?f=member">'.Lang::GO_BACK.'</a>');
		}
		$l = mysql_fetch_row($t);
		if (strlen(trim($_POST['_newtag_'])) == 0 || strlen(trim($_POST['_newtag_'])) > 4) {
			exit(Lang::MEMBER_INVALID_TAG.'<br /><a href="?f=member">'.Lang::GO_BACK.'</a>');
		}
		
		$upd = "UPDATE lg_clans 
				SET name = '".mysql_real_escape_string($_POST['_newname_'])."', 
					tag = '".mysql_real_escape_string($_POST['_newtag_'])."', 
					website = '".mysql_real_escape_string($_POST['_newwebsite_'])."', 
					pass = '".mysql_real_escape_string($_POST['_newpass_'])."' 
				WHERE id = '".ArghSession::get_clan()."'";
		mysql_query($upd);
		include'refresh.php';
	}
	
	ArghPanel::begin_tag(Lang::MEMBER_SPACE);
	
?>
<table class="simple">
	<tr><td><i><?php echo Lang::AVATAR; ?> </i>
	<?php
	if (ArghSession::get_avatar() == '') {
		echo '[<a href="?f=avatar">'.Lang::ADD.'</a>]';
		} else {
		echo '[<a href="?f=avatar">'.Lang::CHANGE.'</a>]<br />[<a href="?f=member&action=avatar_delete">'.Lang::DELETE.'</a>]';
	} ?>
	</td>
	<td>
	<?php 
	if (ArghSession::get_avatar() != '') {
		echo '<img src='.ArghSession::get_avatar().' alt="'.Lang::AVATAR.'" />';
	}
	?></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<form method="POST" action="?f=member">
	<tr>
		<td><i><?php echo Lang::USERNAME; ?></i><!-- [<a href="?f=changenick">changer</a>]--></td>
		<td><b><?php echo ArghSession::get_username(); ?></b></td>
	</tr>
	<tr>
		<td><i><?php echo Lang::BNET_ACCOUNT; ?></i></td>
		<td><input type="text" name="_bnet_" value="<?php echo ArghSession::get_bnet(); ?>" maxlength="25"></td>
	</tr>
	<tr>
		<td><i><?php echo Lang::GARENA_ACCOUNT; ?></i></td>
		<td><input type="text" name="_ggc_" value="<?php echo ArghSession::get_garena(); ?>" maxlength="50"></td>
	</tr>
	<tr>
		<td><i><?php echo Lang::RGC_ACCOUNT; ?></i></td>
		<td><input type="text" name="_rgc_account_" value="<?php echo ArghSession::get_rgc(); ?>" maxlength="50"></td>
	</tr>
	<tr>
		<td><i><?php echo Lang::QAUTH; ?></i></td>
		<td><input type="text" name="_qauth_" value="<?php echo ArghSession::get_qauth(); ?>" maxlength="50"></td>
	</tr>
	<tr>
		<td><i><?php echo Lang::EMAIL; ?></i></td>
		<td><input type="text" name="_mail_" value="<?php echo ArghSession::get_mail(); ?>" size="45" maxlength="60"></td>
	</tr>
	<tr><td><i><?php echo Lang::BIRTHDATE; ?></i></td><td>
	<?php
		$birthdate = explode('-', ArghSession::get_birthdate());
		if (strlen(ArghSession::get_country()) == 0) {
			$_SESSION['country'] = 'Afghanistan';
		}
	?>
		<select name="_day_">
		<?php
			for($i = 1; $i <= 31; $i++) {
				echo '<option'.attr_($i, $birthdate[2]).' value="'.$i.'">'.$i.'</option>';
			}
		?>
		</select> <select name="_month_">
		<?php
			for($i = 1; $i <= 12; $i++) {
				echo '<option'.attr_($i, $birthdate[1]).' value="'.$i.'">'.Lang::$MONTHS_ARRAY[$i - 1].'</option>';
			}
		?>
		</select> <select name="_year_">
		<?php
			for ($i = 1900; $i <= 2000; $i++) {
				echo '<option'.attr_($i, $birthdate[0]).' value="'.$i.'">'.$i.'</option>';
			}
		?>
		</select>
	</td></tr>
	<tr><td><i><?php echo Lang::COUNTRY; ?></i></td><td>
		<select name="_country_" onChange="javascript:CountryPicUpdate(this.value);">
			<?php
				foreach($flags as $val) {
					//Removing extension
					$country = substr($val, 0, strlen($val) - 4);
					//Displaying
					echo '<option'.attr_($country, ArghSession::get_country()).' value="'.$country.'">'.$country.'</option>';
				}
			?>
		</select> <img src="img/flag/<?php echo ArghSession::get_country(); ?>.gif" alt="<?php echo Lang::COUNTRY; ?>" id="countrypic" />
	</td></tr>
	<tr><td><i><?php echo Lang::CITY; ?></i></td><td><input type="text" name="_city_" value="<?php echo ArghSession::get_city(); ?>" maxlength="50"></td></tr>
	<?php
		//if (ArghSession::get_rights() != 0) {
	?>
		<tr><td><i><?php echo Lang::LANGUAGE; ?></i></td><td>
			<select name="_lang_" onChange="javascript:LangPicUpdate(this.value);">
			<?php
				$langs = array(
					'frFR' => 'Français',
					'enUS' => 'English'
				);
				
				foreach ($langs as $code => $lang) {
					echo '<option'.attr_(ArghSession::get_lang(), $code).' value="'.$code.'">'.$lang.'</option>';
				}
			?>
			</select> <img src="img/<?php echo ArghSession::get_lang(); ?>.gif" alt="<?php echo Lang::COUNTRY; ?>" id="langpic" />
		</td></tr>
	<?php
		//}
	
		if (!isBanned(ArghSession::get_username())) {
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			echo '<tr><td colspan="2"><center><input type="submit" name="_maj_" value="'.Lang::UPDATE.'" style="width: 200px" /></center></td></tr>';
		}
	?>
	</form>

<?php
	if (ArghSession::is_gold()) {
?>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2"><span class="vip"><?php echo Lang::GOLD_ACCOUNT; ?></span></td></tr>
        <tr><td><i><?php echo Lang::THEME; ?></i></td><td>
        <?php
			/*
			$themes = array(
					'default' => Lang::THEME_CLASSIC,
					'black' => Lang::THEME_BLACK,
					'red' => Lang::THEME_RED,
					'purple' => Lang::THEME_PURPLE,
			);
			*/
        ?>

        <select id="theme_select">
        <?php
                foreach (Theme::$THEMES as $theme => $description) {
                        echo '<option value="'.$theme.'"'.attr_($theme, ArghSession::get_theme()).'>'.$description.'</option>';
                }
                /*
                <option value="default"<?php echo attr_('default', ArghSession::get_theme()); ?>>Classic</option>
                <option value="black"<?php echo attr_('black', $_SESSION['theme']); ?>>Black</option>
                <option value="red"<?php echo attr_('red', $_SESSION['theme']); ?>>Red</option>
                <option value="purple"<?php echo attr_('purple', $_SESSION['theme']); ?>>Purple</option>
                */
        ?>
        </select>&nbsp;<input type="button" value="<?php echo Lang::OK; ?>" name="save_theme" onClick="javascript:UpdateTheme();" />
        </td></tr>
	
	<tr><td><i><?php echo Lang::BANNER; ?></i></td><td>
	<select onChange="javascript:UpdateBanner(this.value);">
	<?php
		$query = 'SELECT * FROM lg_banners ORDER BY season DESC, id DESC';
		$result = mysql_query($query) or die(mysql_error());
		$i = 0;
		while ($banner = mysql_fetch_object($result)) {
			echo '<option'.attr_($banner->id, ArghSession::get_banner()).' value="'.$banner->id.'">'.$banner->name.' '.Lang::SEASON.' '.$banner->season.' ('.$banner->author.')</option>';
		}
	?>
	</select>
	
	</td></tr>
	
<?php
	}
?>

	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	
	<!-- Form changement pass -->
	<form method="POST" action="?f=member">
	<tr>
		<td><i><?php echo Lang::MEMBER_CHANGE_PASSWORD; ?></i></td>
		<td><input type="password" value="" name="pass1" /></td>
	</tr>
	<tr>
		<td><i><?php echo Lang::CONFIRM; ?></i></td>
		<td><input type="password" value="" name="pass2" /></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2"><center><input type="submit" value="<?php echo Lang::CHANGE; ?>" name="chgpass" style="width: 200px" /></center></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2"><center>
	<?php
		if (isset($_POST['chgpass'])) {
			//Changement MDP
			$ok = true;
			if ($_POST['pass1'] != $_POST['pass2']) {
				echo '<span class="lose">'.Lang::MEMBER_INVALID_CONFIRMATION_PASSWORD.'</span>';
				$ok = false;
			}
			
			if (strlen($_POST['pass1']) < 4 and $ok) {
				echo '<span class="lose">'.Lang::MEMBER_TOO_SHORT_PASSWORD.'</span>';
				$ok = false;
			}
			
			if ($ok) {
				$req = "UPDATE lg_users SET password = '".passHash($_POST['pass1'])."' WHERE username = '".ArghSession::get_username()."'";
				mysql_query($req);
				echo '<span class="win">'.Lang::MEMBER_PASSWORD_CHANGED.'</span>';
			}
		}
	?>
	</center></td></tr>
	</form>
	<!-- Fin form changement pass -->
</table>

<?
	ArghPanel::end_tag();
	ArghPanel::begin_tag(Lang::MEMBER_TEAM_MANAGEMENT);

    if (ArghSession::get_clan() == 0) {
		//Pas de team
	    echo '<a href="?f=teams_list&mode=name">'.Lang::TEAM_JOINING.'</a><br />';
	    echo '<a href="?f=team_create">'.Lang::TEAM_CREATION.'</a>';
	} else {
		//Membre d'une team
	    $req = "SELECT * FROM lg_clans WHERE id='".ArghSession::get_clan()."'";
	    $t = mysql_query($req);
		$l = mysql_fetch_object($t);
		$teamname = $l->name;
		$teamtag = $l->tag;
		$teampass = $l->pass;
		if (empty($l->website)) {
			$teamwebsite = 'http://';
		} else {
			$teamwebsite = $l->website;
		}
		//Si l'user fait partie d'un clan
		echo Lang::TEAM.': <a href="?f=team_profile&id='.ArghSession::get_clan().'">'.$teamname.'</a>';
		
		//dissoudre
		if (ArghSession::get_clan_rank() == ClanRanks::TAUREN) {
			echo ' - <a href="javascript:MsgOkCancel();">'.Lang::TEAM_DELETE.'</a>';
		}
		
		echo '<br />';
		
		if ($error_leaveteam) {
			echo '<span class="lose">'.Lang::TEAM_ERROR_TAUREN_CANT_JOIN.'</span><br />';
		}
		
		if (ArghSession::get_clan_rank() != ClanRanks::TAUREN) {
		echo '<form method="POST" action="?f=member">
		<br /><center><input type="submit" name="_leaveteam_" value="'.Lang::TEAM_LEAVE.'" style="width: 200px" /></center>
		</form><br />';
		}
	    if (ArghSession::get_clan() != 0 && ArghSession::get_clan_rank() == ClanRanks::PEON) {
	        echo '<br />'.Lang::TEAM_PROBATIONARY_PERIOD.'<br />'.Lang::TEAM_PEON_CANT_PARTICIPATE.'<br />';
		}
		
		//Administration du clan
		if (ArghSession::get_clan_rank() == ClanRanks::SHAMAN || ArghSession::get_clan_rank() == ClanRanks::TAUREN) {
			//Logo
			echo '<br /><a href="?f=team_logo">'.Lang::TEAM_ADD_OR_MODIFY_LOGO.'</a><br />';
			echo '<form method="POST" action="?f=member">
			<input type="hidden" name="clanid" value="'.ArghSession::get_clan().'" /><br /><table class="simple">';
			//Droits spéciaux du Tauren
			if (ArghSession::get_clan_rank() == ClanRanks::TAUREN) {
				//Id
				echo '<tr><td><i>'.Lang::ID.'</i></td><td><b>'.ArghSession::get_clan().'</b></td></tr>';
				//Nom du clan
				echo '<tr><td><i>'.Lang::TEAM_NAME.'</i></td><td><input type="text" name="_newname_" value="'.$teamname.'" maxlength="30" /></td></tr>';
				//Tag
				echo '<tr><td><i>'.Lang::TAG.'</i></td><td><input type=text name="_newtag_" value="'.$teamtag.'" maxlength="4" /></td></tr>';
				//Pass
				echo '<tr><td><i>'.Lang::PASSWORD.'</i></td><td><input type="text" name="_newpass_" value="'.$teampass.'" maxlength="30" /></td></tr>';
			}
			//Website
			echo '<tr><td><i>'.Lang::WEBSITE.'</i></td><td><input type="text" name="_newwebsite_" value="'.$teamwebsite.'" maxlenght="400" size="60" /></td></tr>';
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			echo '<tr><td colspan="2"><center><input type="submit" value="'.Lang::VALIDATE.'" style="width: 200px" /></center></td></tr>';
			echo '</form></table>';
		}
	}
	
	ArghPanel::end_tag();
	
	//Mise-à-jour d'un membre
	/*
	if (isset($_POST['maj'])) {
		foreach ($_POST as $key => $value) {
			if ($value != -1) {
				$upd="UPDATE lg_users SET crank='".$value."' WHERE username='".$key."'";
				mysql_query($upd);
			}
		}
	}
	*/
	
	//Cession du lead
	if (isset($_POST['newleader'])) {
		foreach ($_POST as $key => $value) {
			if ($value != -1) {
				//La personne désignée passe leader
				$upd = "UPDATE lg_users SET crank='".ClanRanks::TAUREN."' WHERE username='".$value."' AND crank < 4";
				mysql_query($upd);
				
				//L'user devient Shaman
				$upd = "UPDATE lg_users SET crank='".ClanRanks::SHAMAN."' WHERE username='".ArghSession::get_username()."'";
				mysql_query($upd);
				
				//Refresh
				include('refresh.php');
			}
		}
	}
	//Kick d'un membre du clan
	if (isset($_POST['whoiskicked'])) {
		$upd = "UPDATE lg_users SET clan='0', crank='".ClanRanks::PEON."' WHERE username='".mysql_real_escape_string($_POST['whoiskicked'])."'";
		mysql_query($upd);
	}
	
	//Si l'user est tauren
	if (ArghSession::get_clan_rank() == ClanRanks::TAUREN) {
	
		ArghPanel::begin_tag(Lang::TEAM_MEMBER_MANAGEMENT);
		
		//Listing des membres du clan
		$req = "SELECT *
				FROM lg_users
				WHERE clan = '".ArghSession::get_clan()."'
				AND username != '".ArghSession::get_username()."'
				ORDER BY crank ASC, username ASC";
		$t = mysql_query($req);
		
		echo '<table class="simple">';
		echo '<tr>
			<td><b>#</b></td>
			<td><b>'.Lang::USERNAME.'</b></td>
			<td colspan="2"><b>'.Lang::RANK.'</b></td>
			<td colspan="3"><b>'.Lang::ACTION.'</b></td></tr>';
		echo '<tr><td colspan="7"></td></tr>';
		$i = 1;
		echo '<tr>
			<td><i>'.$i.'</i></td>
			<td><a href="?f=player_profile&player='.ArghSession::get_username().'">'.ArghSession::get_username().'</a></td>
			<td>'.Lang::TAUREN.'</td>
			<td><img src="1.gif" alt="" /></td>
			<td colspan="3"></td></tr>';
		while ($l = mysql_fetch_object($t)) {
			$i++;
			echo '<form method="POST" action="?f=member"><tr>
				<td>'.$i.'</td>
				<td><a href="?f=player_profile&player='.$l->username.'">'.$l->username.'</a></td>
				<td><select name="'.$l->username.'" id="select_rang'.$i.'" onChange="UpdateClanRank(\''.$l->username.'\', this.value);img('.$i.', '.$l->crank.');">';
			if ($l->crank != ClanRanks::PEON) {
				echo '<option value="'.ClanRanks::SHAMAN.'"'.attr_(ClanRanks::SHAMAN, $l->crank).'>'.Lang::SHAMAN.'</option>
					<option value="'.ClanRanks::GRUNT.'"'.attr_(ClanRanks::GRUNT, $l->crank).'>'.Lang::GRUNT.'</option>';
			} else {
				echo '<option value="'.ClanRanks::PEON.'"'.attr_(ClanRanks::PEON, $l->crank).'>'.Lang::PEON.'</option>';
			}
			echo '</select></td>
			<td><img id="img_rang'.$i.'" src="'.$l->crank.'.gif" alt="" /></td><td>';
			echo '</td></form><form method="POST" action="?f=member"><td>';
			if ($l->crank != ClanRanks::PEON) {
				echo '<input type="hidden" name="newleader" value="'.$l->username.'"><input type="submit" value="'.Lang::TEAM_GIVE_LEAD.'" />';
			}
			echo '</td></form>
			<form method="POST" action="?f=member"><td>
			<input type="hidden" name="whoiskicked" value="'.$l->username.'" /><input type="submit" value="'.Lang::KICK.'" />
			</td></form>
			</tr>';
		}
		
		//Fin cadre
		echo '</table>';
		ArghPanel::end_tag();
	}
	
	$transacs = Transaction::load_user_transactions(ArghSession::get_username());
	if (count($transacs) > 0) {
		ArghPanel::begin_tag(Lang::TRANSACTIONS);
		echo '<table class="listing">
			<colgroup>
			</colgroup>
			<thead>
				<tr>
					<th>'.Lang::DATE.'</th>
					<th>'.Lang::PRODUCT.'</th>
				</tr>
			</thead>
			<tbody>';
		$i = 0;
		foreach ($transacs as $transac) {
			echo '<tr'.Alternator::get_alternation($i).'>
					<td>'.date(Lang::DATE_FORMAT_HOUR, $transac->_date_transaction).'</td>
					<td>'.$transac->_product.'</td>
				</tr>';
		}
		echo '</tbody>
			</table>';
		ArghPanel::end_tag();
	}
?>
