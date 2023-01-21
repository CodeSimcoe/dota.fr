<?php

	require_once('classes/RightsMode.php');
	require_once('classes/ArghSession.php');

	ArghSession::begin();
	
	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::LADDER_ADMIN
		)
	);

?>
<table class="simple">
<?php
	require('mysql_connect.php');
	
	$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));
	
	$req = "SELECT ip FROM lg_user_ip WHERE user = '".$player."' ORDER BY ip ASC";
	$t = mysql_query($req);
	while ($l = mysql_fetch_row($t)) {
		echo '<tr><td>'.$l[0].'</td></tr>';
	}

?>
	</table>