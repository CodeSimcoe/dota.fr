<?php
	if (!ArghSession::is_gold()) exit;
	
	//require 'ladder_functions.php';
	
	ArghPanel::begin_tag('');
	
	$query = "SELECT last_rename FROM lg_users WHERE username = '".ArghSession::get_username()."'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	
	$query = "SELECT COUNT(*) FROM lg_pending_nick_changes WHERE old_username = '".ArghSession::get_username()."' AND validated = 0";
	$result = mysql_query($query);
	$r = mysql_fetch_row($result);
	
	$_7days = 604800;
	if (time() - $row[0] < $_7days) {
		echo '<center>'.Lang::CHANGENICK_7DAYS.'
				<br /><br />
				'.sprintf(Lang::CHANGENICK_NEXT_OPPORTUNITY, date(Lang::DATE_FORMAT_HOUR, $row[0] + $_7days)).'</center>';
	} elseif ($r[0] == 1) {
		echo '<center>'.Lang::CHANGENICK_PENDING_REQUEST.'</center>';
	} else {
	
		if (isset($_POST['go'])) {
		
			$new_user = mysql_real_escape_string($_POST['new_username']);
			$query = "SELECT COUNT(*) FROM lg_users WHERE username LIKE '".$new_user."'";
			$result = mysql_query($query);
			$info = mysql_fetch_row($result);
			
			$ok = true;
			
			//Vérification Username
			if ($new_user == '' || $new_user == ArghSession::get_username()) {
				echo $warn.Lang::REG_ENTER_USERNAME.'<br />';
				$ok = false;
			}
			
			if (strtolower($new_user) == 'aucun') {
				echo $warn.Lang::REG_UNAUTHORIZED_USERNAME.'<br />';
				$ok = false;
			}
			
			if (strlen($new_user) > 25) {
				echo $warn.Lang::REG_TOO_LONG_USERNAME.'<br />';
				$ok = false;
			}
			
			if (!preg_match('`^[a-zA-Z0-9_\[\]\-\.]+$`', $new_user)) {
				echo $warn.Lang::REG_NO_SPECIAL_CHARACTERS.'<br />';
				$ok = false;
			}
			
			if (preg_match('`^[0-9]+$`', $new_user)) {
				echo $warn.Lang::REG_INVALID_USERNAME.'<br />';
				$ok = false;
			}
			
			if ($info[0] == 0 && $ok) {
				//SEGO
				$query = "INSERT INTO lg_pending_nick_changes (old_username, new_username, request_time) VALUES ('".ArghSession::get_username()."', '".$new_user."', '".time()."')";
				mysql_query($query);
				
				echo '<span class="win">'.Lang::CHANGENICK_REQUEST_ACCEPTED.' <a href="?f=main">'.Lang::GO_ON.'</a></span>';
				
				$dont_display_again = true;
			} else {
				echo '<span class="lose">'.Lang::CHANGENICK_UNAVAILABLE.'</span>';
			}
			
		}
		
		if (!$dont_display_again) {
			echo '<center>
				Ceci est une <b>demande de changement</b>, elle sera soumise à validation d\'admin. <br />
				Le username que vous allez choisir doit être correct, ne pas contenir d\'insulte ni de grossièreté.<br />
				Le staff sera instransigeant et tout manquement à ce règlement sera sanctionné de 7 jours de ban.<br /><br />
				<form method="POST" action="?f=changenick">
					Nouveau nickname : 
					<input type="text" name="new_username" value="'.stripslashes($_POST['new_username']).'" />
					<input type="submit" name="go" id="go_button" value="'.Lang::VALIDATE.'" />
				</form>
			</center>';
		}
	}
	
	ArghPanel::end_tag();
?>