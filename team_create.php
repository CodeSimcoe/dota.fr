<?php
	ArghSession::exit_if_not_logged();
	ArghPanel::begin_tag(Lang::TEAM_CREATION);
	
	//Deja un clan
	if (ArghSession::get_clan() != 0) {
		echo '<center>'.Lang::TEAM_CANT_CREATE.'</center>';
		ArghPanel::end_tag();
		exit;
	}
	
	//Traitement du formulaire
	if (isset($_POST['Sent'])) {
		
		$post = trim_array($_POST);
		
		$ok = true;
		$inserted = false;
		
		if (!preg_match(RegExps::TEAM_NAME_PATTERN, $post['name'])) {
			//Nom invalide
			$ok = false;
			$name = Lang::TEAM_ENTER_VALID_NAME;
		} else {
			//Nom deja pris
			$req = "SELECT * FROM lg_clans WHERE name LIKE '".mysql_real_escape_string($post['name'])."'";
			$t = mysql_query($req);
			if (mysql_num_rows($t)) {
				$ok = false;
				$name = Lang::TEAM_NAME_ALREADY_IN_USE;
			}
		}
		
		if (!preg_match(RegExps::TEAM_TAG_PATTERN, $post['tag'])) {
			//Tag invalide
			$ok = false;
			$tag = Lang::TEAM_ENTER_VALID_TAG;
		} else {
			//Tag deja pris
			$req = "SELECT * FROM lg_clans WHERE name LIKE '".mysql_real_escape_string($post['tag'])."'";
			$t = mysql_query($req);
			if (mysql_num_rows($t)) {
				$ok = false;
				$tag = Lang::TEAM_TAG_ALREADY_IN_USE;
			}
		}
		
		if (!preg_match(RegExps::TEAM_PASSWORD_PATTERN, $post['pass'])) {
			$ok = false;
			$pass = Lang::TEAM_ENTER_VALID_PASSWORD;
		}
		
		//Insertion
		if ($ok) {
			$ins = "INSERT INTO lg_clans (name, tag, pass, created, website)
					VALUES ('".mysql_real_escape_string($post['name'])."', '".mysql_real_escape_string($post['tag'])."', '".mysql_real_escape_string($post['pass'])."', '".time()."', '".mysql_real_escape_string($post['website'])."')";
			mysql_query($ins);
			$id = mysql_insert_id();
			$upd = "UPDATE lg_users
					SET crank = '1',
						jclan = '".time()."',
						clan = '".$id."'
					WHERE username = '".ArghSession::get_username()."'";
			mysql_query($upd);
			$inserted = true;
		}
	}
	if ($inserted) {
		echo '<center><span class="win">'.Lang::TEAM_SUCCESSFULLY_CREATED.'</span></center>';
		include('refresh.php');
	} else {
		echo '<form action="?f=team_create" method="POST">
		<table><tr>
			<td>'.Lang::NAME.'</td>
			<td><input type="text" size="25" name="name" maxlength="30" value="'.htmlentities($post['name']).'"> '.$name.'</td>
		</tr>
		<tr>
			<td>'.Lang::TAG.'</td>
			<td><input size="4" name="tag" maxlength="4" value="'.htmlentities($post['tag']).'"> '.$tag.'</td>
		</tr>
		<tr>
			<td>'.Lang::PASSWORD.'</td>
			<td><input type="text" size="25" name="pass" maxlength="30" value="'.htmlentities($post['pass']).'"> '.$pass.'</td>
		</tr>
		<tr>
			<td colspan="2"><em>('.Lang::TEAM_PASSWORD_EXPLANATION.')</em></td>
		</tr>
		<tr>
			<td>'.Lang::WEBSITE.'</td>
			<td><input size="60" name="website" maxlength="400" value="'.((empty($_POST['Sent'])) ? 'http://' : htmlentities($post['website'])).'"></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2"><center><input type="submit" value="'.Lang::CREATE.'" name="Sent" value="1" /></center></td>
		</tr></table>';
	}
	
	ArghPanel::end_tag();
?>