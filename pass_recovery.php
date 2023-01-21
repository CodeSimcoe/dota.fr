<?php
	ArghPanel::begin_tag(Lang::PASSWORD_RECOVERY);

	//Genere un string aleatoire de 25 caractères
	function generate() {
		$pattern = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		for ($i = 0; $i < 25; $i++) {
				$code .= $pattern[rand(0, strlen($pattern) - 1)];
		}
		return $code;
	}
		
		
	if (isset($_GET['mode']) && isset($_GET['keycode'])) {
		if ($_GET['mode'] == 'newpass') {
			$req = "SELECT * FROM lg_passrecovery WHERE keycode='".mysql_real_escape_string($_GET['keycode'])."'";
			$t = mysql_query($req);
			while ($l = mysql_fetch_object($t)) {
				$found = 1;
				echo '<form method="POST" action="?f=pass_recovery&mode=newone">
				<center>'.sprintf(Lang::USER_PASSWORD_CHANGE, $l->user).'</center><br /><br />';
				echo Lang::PASSWORD.': <input type="password" size="25" name="newpassword" /><br />';
				echo Lang::CONFIRM.': <input type="password" size="25" name="confirmnewpassword" /><br />';
				echo '<input type="hidden" size="25" value="'.htmlentities($_GET['keycode']).'" name="keycode2" /><br />';
				echo '<input type="hidden" size="25" value="'.$l->user.'" name="user" /><br /><br />';
				echo '<center><input type="submit" value="'.Lang::VALIDATE.'"></center></form>';
			}
		}
	}
	if (isset($_GET['mode']) && isset($_POST['keycode2']) && isset($_POST['user'])) {
		if ($_GET['mode'] == 'newone') {
			if ($_POST['newpassword'] == $_POST['confirmnewpassword']) {
				//Verif securite
				$req = "SELECT user, keycode
						FROM lg_passrecovery
						WHERE keycode = '".mysql_real_escape_string($_POST['keycode2'])."'";
				$t = mysql_query($req);
				
				if (mysql_num_rows($t)) {
					//Ok, keycode valide
					$l = mysql_fetch_object($t);
					
					$req = "DELETE FROM lg_passrecovery
							WHERE keycode='".mysql_real_escape_string($l->keycode)."'";
					mysql_query($req);
					
					$req = "UPDATE lg_users
							SET password='".passHash($_POST['newpassword'])."'
							WHERE username='".mysql_real_escape_string($l->user)."'";
					mysql_query($req);
				}
			} else {
				echo '<center><span class="error">'.Lang::PASSWORD_MISMATCH.'</span></center>';
			}
		}
	}
	if (isset($_POST['username'])) {
		
		//Récupération mail
		$req = "SELECT mail FROM lg_users WHERE username='".mysql_real_escape_string($_POST['username'])."'";
		$t = mysql_query($req);
		$l=mysql_fetch_row($t);
		$mail = $l[0];
		
		$query = "SELECT keycode FROM lg_passrecovery WHERE user = '".mysql_real_escape_string($_POST['username'])."'";
		$result = mysql_query($query);
		if (mysql_num_rows($result) > 0) {
			$key = mysql_fetch_row($result);
			$message = sprintf(Lang::PASSWORD_RECOVERY_MAIL_BODY, $key[0]);
			@mail($mail, Lang::PASSWORD_RECOVERY_MAIL_TITLE, $message);
		} else {
			$code = generate();
			//Insertion dans la table lg_passrecovery
			$ins = "INSERT INTO lg_passrecovery (user, keycode, ip, created)
					VALUES ('".mysql_real_escape_string($_POST['username'])."', '".$code."', '".$_SERVER['REMOTE_ADDR']."', '".time()."')";
			mysql_query($ins);
			
			$message = sprintf(Lang::PASSWORD_RECOVERY_MAIL_BODY, $code);
			@mail($mail, Lang::PASSWORD_RECOVERY_MAIL_TITLE, $message);
		}
		
		echo '<center><span class="win">'.Lang::PASSWORD_RECOVERY_MAIL_SENT.'</span>';
	} elseif ($found != 1) {
?>
	<form method="POST" action="?f=pass_recovery">
		<?php echo Lang::USERNAME; ?>: <input type="text" name="username" /><br />
		<span class="info"><?php echo Lang::PASSWORD_RECOVERY_USERNAME_INFO; ?></span><br /><br />
		<center><input type="submit" value="<?php echo Lang::VALIDATE; ?>" /></center>
	</form>
<?php
	}
	ArghPanel::end_tag();
?>