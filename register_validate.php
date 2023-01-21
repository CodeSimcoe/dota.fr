<?php
	ArghPanel::begin_tag(Lang::REGISTERATION);

	function isBanned($player) {
		$req = "SELECT * FROM lg_ladderbans WHERE qui = '".$player."'";
		$t = mysql_query($req);
		return (mysql_num_rows($t) > 0);
	}

	function canRegister($ip) {
		$req = "SELECT * FROM lg_user_ip WHERE ip = '".$ip."'";
		$t = mysql_query($req);
		while ($l = mysql_fetch_object($t)) {
			if (isBanned($l->user)) return false;
		}
		return true;
	}
	
	function generateKey($length) {
		$pattern = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$key = '';
		for ($i = 1; $i <= $length; $i++) {
			$key .= $pattern[rand(0, strlen($pattern)-1)];
		}
		return $key;
	}
	
	$ok = 1;
	$username = $_POST['username'];
	$user = strtolower($username);
	$bnet = $_POST['bnet'];
	$ggc = $_POST['ggc'];
	$mail = $_POST['mail'];
	$pass = $_POST['password'];
	$password = passHash($_POST['password']);

	$warn = '<img src="img/icons/exclamation.png" alt="" /> ';
	
	//Vérification Username
	if ($username == '') {
		echo $warn.Lang::REG_ENTER_USERNAME.'<br />';
		$ok = 0;
	}
	if (strtolower($username) == 'aucun') {
		echo $warn.Lang::REG_UNAUTHORIZED_USERNAME.'<br />';
		$ok = 0;
	}
	if (strlen($username) > 25) {
		echo $warn.Lang::REG_TOO_LONG_USERNAME.'<br />';
		$ok = 0;
	}
	
	if (!preg_match('`^[a-zA-Z0-9_\[\]\-\.]+$`', $username)) {
		echo $warn.Lang::REG_NO_SPECIAL_CHARACTERS.'<br />';
		$ok = 0;
	}
	
	if (preg_match('`^[0-9]+$`', $username)) {
		echo $warn.Lang::REG_INVALID_USERNAME.'<br />';
		$ok = 0;
	}

	$req="SELECT * FROM lg_users WHERE username LIKE '".$user."'";
	$t=mysql_query($req);
	if (mysql_num_rows($t)) {
		echo $warn.Lang::REG_USERNAME_ALREADY_IN_USE.'<br />';
		$ok = 0;
	}	
	
	if ($_POST['password'] != $_POST['password2']) {
		echo $warn.Lang::PASSWORD_MISMATCH.'<br />';
		$ok = 0;
	}
	
	if ($_POST['mail'] != $_POST['mail2']) {
		echo $warn.Lang::REG_MAIL_MISMATCH.'<br />';
		$ok = 0;
	}
	
	if ($ggc == '') {
		echo $warn.Lang::REG_ENTER_GARENA_ACCOUNT.'<br />';
		$ok = 0;
	} else {
		$req="SELECT * FROM lg_users WHERE ggc LIKE '".$ggc."'";
		$t=mysql_query($req);
		if (mysql_num_rows($t)) {
			echo $warn.Lang::REG_GARENA_ACCOUNT_ALREADY_IN_USE.'<br />';
			$ok = 0;
		}	
	}
	//Vérification Password
	if ($pass == '') {
		echo $warn.Lang::REG_ENTER_PASSWORD.'<br />';
		$ok = 0;
	}

	//Vérification Mail
	//Fonction de vérification
	function ismail($adresse) {
		$Syntaxe = '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#';
		return preg_match($Syntaxe, $adresse);
	}
	
	if (!ismail($mail)) {
		echo $warn.Lang::REG_ENTER_VALID_EMAIL.'<br />';
		$ok=0;
	}
	
	$forbiddenDomains = array('@hotmail.fr', '@hotmail.com', '@jubii.fr', '@yopmail.com');
	$domain = strtolower(strstr($mail, '@'));
	
	if (in_array($domain, $forbiddenDomains)) {
		echo $warn.Lang::REG_CANT_USE_THIS_MAIL;
		$ok=0;
	}
	
	$req="SELECT * FROM lg_users WHERE mail LIKE '".$mail."' LIMIT 1";
	$t=mysql_query($req);
	if (mysql_num_rows($t)) {
		echo $warn.Lang::REG_MAIL_ALREADY_IN_USE;
		$ok=0;
	}
	if ($ok == 1) {
		if (!canRegister($_SERVER['REMOTE_ADDR'])) {
			echo $warn.Lang::REG_MULTI_IP_REGISTERING_INFO.'<br />';
			$ins = mysql_query("
				INSERT INTO lg_ladderbans (qui, par_qui, quand, duree, raison) 
				VALUES ('".$username."', 'LadderGuardian', '".time()."', '0', 'Multi IP Registering')") or die(mysql_error());
			$ins = mysql_query("
				INSERT INTO lg_ladderbans_follow (username, motif, `force`, quand, admin, type) 
				VALUES ('".$username."', 'Multi IP Registering', '0', '".time()."', 'LadderGuardian', 'ban')") or die(mysql_error());
				
			$al = new AdminLog('Ban Multi IP Registering: '.$username, AdminLog::TYPE_LADDER, 'LadderGuardian');
			$al->save_log();
			/*
			$ins = mysql_query("
				INSERT INTO lg_adminlog (qui, quand, quoi) 
				VALUES ('LadderGuardian', '".time()."', 'Ban Multi IP Registering: ".$username."')") or die(mysql_error());
			*/
		}
		
		//Activation du compte
		$key = generateKey(16);
		mysql_query("
			INSERT INTO lg_activation (username, keycode)
			VALUES ('".$username."', '".$key."')");
	
		$message = sprintf(Lang::REG_MAIL_BODY, $username, $pass, $username, $key);
        mail($mail, Lang::REG_MAIL_TITLE, $message);
	        $ins = "INSERT INTO lg_users (username, active, bnet, ggc, password, mail, joined, ip) VALUES
			('".$username."','0','".$bnet."','".$ggc."','".$password."','".$mail."','".time()."','".$_SERVER['REMOTE_ADDR']."');";
			mysql_query($ins);
	        echo '<center><img src="img/form/tinkerwelcome.jpg" alt="'.Lang::REG_SUCCESS.'" /></center>'.Lang::REG_SUCCESS;
	}

	if ($ok != 1) {
		echo '<form method="POST" action="?f=register">';
		echo '<input type="hidden" name="username" value="'.$username.'">';
		echo '<input type="hidden" name="password" value="'.$_POST['password'].'">';
		echo '<input type="hidden" name="password2" value="'.$_POST['password2'].'">';
		echo '<input type="hidden" name="bnet" value="'.$bnet.'">';
		echo '<input type="hidden" name="ggc" value="'.$ggc.'">';
		echo '<input type="hidden" name="mail" value="'.$mail.'">';
		echo '<input type="hidden" name="mail2" value="'.$_POST['mail2'].'">';
		echo '<br /><center><input type="submit" name="correct" value="'.Lang::CORRECT.'" style="width: 200px;" /></center></form>';
	}
	
	ArghPanel::end_tag();
?>