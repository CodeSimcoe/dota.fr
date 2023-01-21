<?
	require '/home/www/ligue/classes/ArghSession.php';
	require '/home/www/ligue/classes/AdminLog.php';
	ArghSession::begin();
	
	require '/home/www/ligue/lang/'.ArghSession::get_lang().'/Lang.php';
	require '/home/www/ligue/classes/ArghPanel.php';
	require '/home/www/ligue/classes/Transaction.php';
	require '/home/www/ligue/mysql_connect.php';
	
	ArghSession::exit_if_not_logged();
	
	$fail = false;
	
	//Security
	//$RECALL = $_GET['RECALL'];
	$RECALL = $_GET['codes'];
	if (trim($RECALL) == '') {
		// La variable RECALL est vide, renvoi de l'internaute
		//vers une page d'erreur
		//Header("Location: erreur.html");
		$fail = true;
	}

	// $RECALL contient le code d'accès
	$RECALL = urlencode($RECALL); 
	
	//Code already used ?
	$req = "SELECT code FROM lg_transactions WHERE code = '".$RECALL."'";
	$t = mysql_query($req);
	if (mysql_num_rows($t)) exit;

	// $AUTH doit contenir l'identifiant de VOTRE document

	$AUTH = urlencode('176058/509718/346424');

	// envoi de la requête vers le serveur Allopass
	// dans la variable $r[0] on aura la réponse du serveur
	// dans la variable $r[1] on aura "ABOCB"

	$r = @file('http://payment.allopass.com/api/checkcode.apu?code='.$RECALL.'&auth='.$AUTH);

	// on teste la réponse du serveur
	if (ereg('ERR', $r[0]) || ereg('NOK', $r[0])) {
	// Le serveur a répondu ERR ou NOK : l'accès est donc refusé
	//header("Location: erreur.html");
		$fail = true;
	}
	
	if ($fail) {
		//Tentative de baisage de gueule ! On sort les baguettes YATAAAA
		$al = new AdminLog(
			ArghSession::get_username().' tentative de baguettes allopass ! (Recall : '.$RECALL.', Referer : '.$_SERVER['HTTP_REFERER'].')',
			AdminLog::TYPE_PAYMENT,
			'LadderGuardian'
		);
		$al->save_log();
		exit;
	}
	
?>
<html>

<head>
	<link rel="stylesheet" href="themes/default/boxes.css" type="text/css">
	<link rel="stylesheet" href="themes/default/default.css" type="text/css">
	<link rel="stylesheet" href="themes/default/listings.css" type="text/css">

	<noscript>
		<meta http-equiv="Refresh" content="0;url=https://payment.allopass.com/error.apu?ids=176058&idd=509718">
	</noscript>
	<script language="Javascript" src="https://payment.allopass.com/api/secure.apu?ids=176058&idd=509718"></script>

</head>
<body>
	<br /><br /><br />
	<center>
	<div style="width: 800px;">
<?php
	ArghPanel::begin_tag(Lang::ARGH_DOTA_LEAGUE);
	
	if (ArghSession::is_gold()) {
		$query = "SELECT gold_expire FROM lg_users WHERE username = '".ArghSession::get_username()."'";
		$result = mysql_query($query);
		$expiration = mysql_fetch_row($result);
		echo '<center>'.sprintf(Lang::GOLD_ALREADY_MEMBER, date(Lang::DATE_FORMAT_DAY, $expiration[0])).'</center>';
	} else {
		$expiration_time = time() + 31 * 24 * 3600;
		$query = "UPDATE lg_users SET is_gold = 1, gold_expire = '".$expiration_time."' WHERE username = '".ArghSession::get_username()."' AND is_gold = 0";
		mysql_query($query) or die(mysql_error());
		
		$t = new Transaction();
		$t->_username = ArghSession::get_username();
		$t->_product = '1map';
		$t->_code = $RECALL;
		$t->save();
		
		echo '<center>'.sprintf(Lang::GOLD_SUBSCRIBED, ArghSession::get_username()).'<br /><br /><a href="index.php?f=main">'.Lang::GO_ON.'</a></center>';
		$al = new AdminLog(
			sprintf(Lang::ADMIN_LOG_GOLD_CREATION, ArghSession::get_username(), ArghSession::get_username(), $t->_product, $RECALL),
			AdminLog::TYPE_PAYMENT,
			'LadderGuardian'
		);
		$al->save_log();
		include 'refresh.php';
	}
	
	ArghPanel::end_tag();
?>
	</center>
	</div>
</body>
