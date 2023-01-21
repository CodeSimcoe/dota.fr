<?
	ArghSession::exit_if_not_logged();
	ArghPanel::begin_tag(Lang::AVATAR_MANAGEMENT);
	
	include 'refresh.php';
    $upd = "UPDATE lg_users SET avatar = '' WHERE username = '".ArghSession::get_username()."'";
    mysql_query($upd);
    
    echo '<center>'.Lang::AVATAR_DELETED.' <a href="?f=member">'.Lang::GO_ON.'</a></center>';
    
    ArghPanel::end_tag();
?>