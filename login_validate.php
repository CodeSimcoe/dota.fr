<?php
	require '/home/www/ligue/classes/ArghSession.php';
	require '/home/www/ligue/classes/RightsMode.php';
	ArghSession::begin();

	require '/home/www/ligue/classes/ArghPanel.php';
	require '/home/www/ligue/classes/Theme.php';
	require '/home/www/ligue/mysql_connect.php';
	require '/home/www/ligue/security/hash.php';
	
	$slmultis = '';
	
	$username = mysql_real_escape_string(substr($_POST['username'], 0, 25));
	$password = mysql_real_escape_string($_POST['password']);
	$url = htmlentities($_POST['url']);
	
	$msg = '<img src="pics/brewmaster-static.gif" alt="Pandaren Brewmaster" /><br /><br />';
	
	$req = "SELECT * FROM lg_users WHERE username = '".$username."' LIMIT 1";
	$t = mysql_query($req);
	$l = mysql_fetch_object($t);
	
	$autolog = (isset($_POST['autolog'])) ? 1 : 0;
	if ($username == $l->username && passHash($password) == $l->password && $l->active == 1) {
		
		if ($autolog == 1) {
			$validity_time = time() + 31536000;
			setcookie(ArghSession::COOKIE_USERNAME, $l->username, $validity_time);
			setcookie(ArghSession::COOKIE_PASSWORD, cookieHash($l->password), $validity_time);
			setcookie(ArghSession::COOKIE_LANGUAGE, ArghSession::get_lang(), $validity_time);
		}
		$clean = "DELETE FROM lg_user_ip WHERE user = '".$l->username."' AND ip ='".$_SERVER['REMOTE_ADDR']."'";
		mysql_query($clean);
		//Le timestamp est genere par la BDD
		$ins = "INSERT INTO lg_user_ip (user, ip) VALUES ('".$l->username."', '".$_SERVER['REMOTE_ADDR']."')";
		mysql_query($ins);
		
		include('/home/www/ligue/mobile_device_detect.php');
		$mobile_status = '';
		$mobile = mobile_device_detect($mobile_status);
		if ($mobile == true) {
			@mysql_query("INSERT INTO lg_mobiles (username, mobile_status, ip) VALUES ('".$l->username."', '".$mobile_status."', '".$_SERVER['REMOTE_ADDR']."')");
			ArghSession::set_mobile(true);	
		} else {
			ArghSession::set_mobile(false);	
		}
		
		ArghSession::store_session_vars($l);
		
		// Admin Ladder - Deprecated (nouveau moteur de droits)
		// $req = "SELECT * FROM lg_ladderadmins WHERE user = '".ArghSession::get_username()."'";
		// $t = mysql_query($req);
		// ArghSession::set_ladder_admin((int)mysql_num_rows($t));
		
		$slmultis .= '<object type="application/x-shockwave-flash" width="0" height="0" data="http://www.dota.fr/e-stats/stats.swf">';
		$slmultis .= '<param name="movie" value="http://www.dota.fr/e-stats/stats.swf" />';
		$slmultis .= '<param name="scale" value="noScale" />';
		$slmultis .= '<param name="flashVars" value="pseudo='.ArghSession::get_username().'&guid='.uniqid(rand(), true).'" />';
		$slmultis .= '</object>';
		
		// Admin Ladder VIP - Deprecated (nouveau moteur de droits)
		// $req = "SELECT * FROM lg_laddervip_admins WHERE user = '".ArghSession::get_username()."'";
		// $t = mysql_query($req);
		// ArghSession::set_laddervip_admin((int)mysql_num_rows($t));
		
		// get_access deprecated (nouveau moteur de droits)
		//if (ArghSession::get_access() >= 75) {
		if (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN))) {
			//Admin League
			$req = "SELECT nom FROM lg_divisions WHERE admin = '".ArghSession::get_username()."'";
			$t = mysql_query($req);
			if (mysql_num_rows($t) != 0) {
				$l = mysql_fetch_row($t);
				ArghSession::set_league_admin($l[0]);
			}
		}
		
		require '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';

		//Confirmation
		$msg .= '<span class="win">'.Lang::LOGIN_SUCCESS.'</span><br /><br /><a href="'.$url.'">'.Lang::CONTINUE_WHERE_I_WERE.'</a> - <a href="index.php?f=member">'.Lang::MEMBER_SPACE.'</a> - <a href="index.php?f=ladder_join">'.Lang::LADDER.'</a>';
		//if (strlen($_SESSION['country']) == 0 or $_SESSION['birth'] == '1900-01-01') $update = true;
	} else {
	
		require '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';
	
		$msg .= '<span class="lose">'.(!empty($l->active) && $l->active == 0 ? Lang::LOGIN_ERROR_INACTIVE : Lang::LOGIN_ERROR_WRONG).'</span>
				<br /><br />
				<a href="'.$_POST['url'].'">'.Lang::CONTINUE_WHERE_I_WERE.'</a> - <a href="index.php">'.Lang::BACK_TO_HOME.'</a>';
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
	<?php
		Theme::initialize_theme();
	?>
</head>

<body>
	<center>
	<div style="width: 800px;">
<?php
	ArghPanel::begin_tag(Lang::ARGH_DOTA_LEAGUE);
	echo $msg;
	ArghPanel::end_tag();
	
	/*if ($update) {
		echo '<b>Visitez votre <a href="index.php?f=member">profil</a> pour mettre à jour vos informations personnelles.</b>';
	}*/
?>
	</div>
	</center>
<?php
	echo $slmultis;
?>
</body>
</html>