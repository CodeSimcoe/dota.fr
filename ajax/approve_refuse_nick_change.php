<?php
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	require ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require ABSOLUTE_PATH.'classes/RightsMode.php';
	require ABSOLUTE_PATH.'classes/NotificationManager.php';
	require ABSOLUTE_PATH.'mysql_connect.php';
	require ABSOLUTE_PATH.'misc.php';
	
	$nick = mysql_real_escape_string($_GET['old_username']);
	$new_nick = mysql_real_escape_string($_GET['new_username']);
	$accept = (int)$_GET['accept'];
	
	require ABSOLUTE_PATH.'lang/'.get_user_lang($nick).'/Lang.php';
	ArghSession::exit_if_not_logged();
	
	ArghSession::exit_if_not_rights(
		RightsMode::WEBMASTER
	);
	
	$query = "UPDATE lg_pending_nick_changes SET validated = ".$accept." WHERE old_username = '".$nick."' AND new_username = '".$new_nick."'";
	mysql_query($query) or die(mysql_error());
	
	$notif = new Notification();
	$notif->_destinator = $nick;
	$notif->_link = '?f=changenick';
	$notif->_message = $accept == 1 ? sprintf(Lang::NOTIFICATION_NICK_ACCEPTED, $new_nick) : sprintf(Lang::NOTIFICATION_NICK_REFUSED, $nick);
	$notif->_notif_time = time();
	
	$notif->save();
?>