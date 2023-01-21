<?php
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	require_once ABSOLUTE_PATH.'classes/RightsMode.php';
	require_once ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::NEWS_HEADADMIN,
			RightsMode::NEWS_NEWSER
		)
	);
	
	require_once ABSOLUTE_PATH.'mysql_connect.php';
?>
<html>

<head>
	<link rel="stylesheet" href="/ligue/themes/default/boxes.css" type="text/css">
	<link rel="stylesheet" href="/ligue/themes/default/default.css" type="text/css">
	<link rel="stylesheet" href="/ligue/themes/default/listings.css" type="text/css">
	<script language="javascript">
		function nuke(msg_id) {
			window.opener.document.getElementById('msg' + msg_id).innerHTML = '<i><?php echo sprintf(Lang::MESSAGE_MODERATED_BY, ArghSession::get_username()); ?> </i>';
			window.close();
		}
	</script>
</head>

<body>
<br /><br /><br />
<center>
<?php
	//Vérif
	$msg_id = (int)$_GET['msg'];
	
	if ($_GET['type'] == 'news') {
		$table = 'lg_comment';
		$field = 'comment';
	}
	if ($_GET['type'] == 'match'){
		$table = 'lg_text';
		$field = 'text';
	}
	
	//Ok
	$upd = "UPDATE `".$table."` SET `".$field."` = '<i>".sprintf(Lang::MESSAGE_MODERATED_BY, ArghSession::get_username())."</i>' WHERE id = '".$msg_id."'";
	mysql_query($upd) or die(mysql_error());
	
	echo Lang::MESSAGE_MODERATED.'<br /><br /><a href="javascript:nuke(\''.$msg_id.'\')">'.Lang::GO_ON.'</a>';
	
?>
</center>
</body>
</html>