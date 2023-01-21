<?php
	ArghPanel::begin_tag(Lang::ACCOUNT_ACTIVATION);
	
	echo '<center>';
	
	$user = mysql_real_escape_string(substr($_GET['user'], 0, 25));
	$key = mysql_real_escape_string($_GET['key']);

	$req = "SELECT *
			FROM lg_activation
			WHERE username = '".$user."'
			AND keycode = '".$key."'";
	
	$t = mysql_query($req);
	if (mysql_num_rows($t) > 0) {
		
		$upd = "UPDATE lg_users
				SET active = 1
				WHERE username='".$user."'";
		mysql_query($upd);
		
		$del = "DELETE FROM lg_activation
				WHERE username = '".$user."'
				AND keycode = '".$key."'";
		mysql_query($del);
		
		echo sprintf(Lang::ACCOUNT_ACTIVATED, $user);
	} else {
		echo Lang::ACCOUNT_ACTIVATION_ERROR;
	}
	
	echo '</center>';
	ArghPanel::end_tag();
?>