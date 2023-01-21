<?php
	if (ArghSession::is_logged()) {
		$req = "SELECT * FROM lg_users WHERE username='".ArghSession::get_username()."'";
		$t = mysql_query($req);
		$l = mysql_fetch_object($t);
		
		ArghSession::store_session_vars($l);
	}
?>