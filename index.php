<?php
	//Error Reporting
	ini_set('display_errors', 1);
	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	
	require 'classes/ArghSession.php';
	require 'classes/LangCodes.php';
	ArghSession::begin();
	
	//Changement langue
	if (isset($_GET['nlg'])) {
		//Code langue valide ?
		if (in_array($_GET['nlg'], LangCodes::$CODES)) {
			setcookie(ArghSession::COOKIE_LANGUAGE, substr($_GET['nlg'], 0, 4), time() + 31536000);
			ArghSession::set_lang(substr($_GET['nlg'], 0, 4));
		}
	}
	
	require 'classes/RightsMode.php';
	require 'classes/Team.php';
	require 'classes/ArghPanel.php';
	require 'classes/AdminLog.php';
	require 'classes/CacheManager.php';
	require 'classes/BanManager.php';
	require 'classes/ClanRanks.php';
	require 'classes/RegExps.php';
	require 'classes/Alternator.php';
	require 'classes/LadderStates.php';
	require 'classes/Tables.php';
	require 'classes/Mail.php';
	require 'classes/Match.php';
	require 'classes/News.php';
	require 'classes/Theme.php';
	require 'classes/GenericMessageManager.php';
	require 'security/allowedPages.php';
	require 'security/hash.php';
	require 'conf/ArghConf.php';
	
	require 'mysql_connect.php';
	require 'misc.php';
	
	//Inclusion
	if (!isset($_GET['f'])) $_GET['f'] = 'main';
	$_GET['f'] .= '.php';
	
	//Protection de l'inclusion
	if (!array_key_exists($_GET['f'], $allowedPages)) exit();
	if (preg_match('/script|document/', $_SERVER["REQUEST_URI"])) exit();

	$slmultis = '';
	
	//login sur cookie
	if (!ArghSession::is_logged() && ArghSession::is_cookie_set()) {
	
		$username = ArghSession::get_username_cookie();
		$password = ArghSession::get_password_cookie();
		
		
		$req = "SELECT * FROM lg_users WHERE username = '".mysql_real_escape_string($username)."' LIMIT 1";
		$t = mysql_query($req);
		$l = mysql_fetch_object($t);
		
		if ($username == $l->username && $password == cookieHash($l->password) && $l->active == 1) {
			
			//IP
			$ins = "INSERT INTO lg_user_ip (user, ip) VALUES ('".$l->username."', '".$_SERVER['REMOTE_ADDR']."')";
			@mysql_query($ins);
			
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
			
			$slmultis .= '<object type="application/x-shockwave-flash" width="0" height="0" data="http://www.dota.fr/e-stats/stats.swf">';
			$slmultis .= '<param name="movie" value="http://www.dota.fr/e-stats/stats.swf" />';
			$slmultis .= '<param name="scale" value="noScale" />';
			$slmultis .= '<param name="flashVars" value="pseudo='.ArghSession::get_username().'&guid='.uniqid(rand(), true).'" />';
			$slmultis .= '</object>';
			
			if (ArghSession::is_rights(array(RightsMode::LEAGUE_HEADADMIN, RightsMode::LEAGUE_ADMIN))) {
				//Admin League
				$req = "SELECT nom FROM lg_divisions WHERE admin = '".ArghSession::get_username()."'";
				$t = mysql_query($req);
				if (mysql_num_rows($t) != 0) {
					$l = mysql_fetch_row($t);
					ArghSession::set_league_admin($l[0]);
				}
			}
		}
	}
	
	require 'lang/'.ArghSession::get_lang().'/Lang.php';

	//Vouched ?
	function isVouched($player) {
		//BanList
		$req = "SELECT * FROM lg_laddervip_vouchlist WHERE username = '".$player."'";
		$t = mysql_query($req);
		return (mysql_num_rows($t) > 0) ? true : false;
	}
	if (ArghSession::is_logged()) {
		ArghSession::set_vouched(isVouched(ArghSession::get_username()));
	}
	
	trackUser(ArghSession::get_vouched());


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<title>
	<?php
		//Titre dynamique
		$preFix = Lang::ARGH_TITLE;
		if (strlen($allowedPages[$_GET['f']]) > 2) {
			//News ?
			if ($_GET['f'] == 'news.php') {
				echo $preFix.getNewsTitle((int)$_GET['id']);
			} else {
				echo $preFix.$allowedPages[$_GET['f']];
			}
		} else {
			echo $preFix.Lang::ARGH_SHORT_DESCRIPTION;
		}
	?>
</title>
<?php
	echo '<META NAME="description" CONTENT="'.Lang::ARGH_DESCRIPTION.'">
	<META NAME="keywords" CONTENT="'.Lang::ARGH_KEYWORDS.'">
	<META NAME="identifier-url" CONTENT="'.ArghConf::URL.'">
	<META http-equiv="Content-type" CONTENT="'.ArghConf::CONTENT_TYPE.'">
	<META http-equiv="Content-Language" CONTENT="'.ArghConf::LANG_SHORT.'">';
?>
<script type="text/javascript" src="/ligue/javascript/jquery.js"></script>
<script type="text/javascript" src="/ligue/javascript/ui.core.js"></script>
<script type="text/javascript" src="/ligue/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="/ligue/ckeditor/adapters/jquery.js"></script>
<?php

	Theme::initialize_theme();
	
/*
<link rel="stylesheet" href="themes/default/bottom_nav.css" type="text/css" media="screen" />
<!--[if lt IE 7]>
<link rel="stylesheet" href="themes/default/bottom_nav_ie.css" type="text/css" media="screen" />
<![endif]-->
*/
?>
<link rel="SHORTCUT ICON" href="favicon.ico">
<base target="_parent">
</head>

<body>
	<div id="main_container">
		<!--<div class="content_wrapper">!-->
			<div class="global">
				<div class="logo">
				<a href="index.php">
				<?php
					$banner = ArghSession::get_banner();
					if (!empty($banner) && ArghSession::is_gold()) {
						echo '<img src="img/banners/'.ArghSession::get_banner().'.jpg" width="1000" height="176" border="0" id="argh_banner" alt="Argh DotA League / Ladder" />';
					} else {
						echo '<img src="img/banners/default.jpg" width="1000" height="176" border="0" id="argh_banner" alt="Argh DotA League / Ladder" />';
					}
				?>
				</a>
				</div>
				<?php
					$nlg = (ArghSession::get_lang() == LangCodes::FRENCH) ? LangCodes::ENGLISH_US : LangCodes::FRENCH;
				
					//MenuBar
					echo '<div class="menu">
							<a href="?f=main">'.Lang::MENU_HOME.'</a>
							<a href="?f=newshome">'.Lang::NEWS.'</a>
							<a href="/forum/index.php" target="_blank">'.Lang::MENU_FORUM.'</a>
							<a href="?f=ladder_join">'.Lang::LADDER.'</a>
							
							<a href="?f=league">'.Lang::LEAGUE.'</a>
							<a href="?f=screenshots">'.Lang::SCREENSHOTS.'</a>
							<span style="float: right; margin-right: 20px;"><a href="?nlg='.$nlg.'">'.ArghSession::get_flag().'</a></span>
						</div>';
				?>
				<div class="left">
					<?php include 'left.php'; ?>
				</div>
				<div class="middle" id="argh_main">
					<?php include($_GET['f']); ?>
					<br />
					<center><?php echo Lang::ARGH_FOOTER; ?></center>
				</div>
				<div class="right">
					<?php include 'right.php'; ?>
				</div>
			<!--</div>-->
		<!--</div>-->
		<div class="spacer"></div>
	</div>
	<!--
	<div id="nav_menu_wrapper">
        <div class="nav_menu">
            <ul>
                <li><a href="/">Accueil</a></li>
                <li><a href="?f=ladder_join">Ladder</a></li>
                <li><a href="/forum">Forum</a></li>
            </ul>
        </div>
    </div>
	-->
<?php
	echo $slmultis;
	
	//GOOGLE ANALYTICS
	include 'google/analytics.html';
?>
<br />
</body>
</html>