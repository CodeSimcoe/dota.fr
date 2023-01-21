<?php
	if (!ArghSession::is_gold()) {
		exit;
	}

	$max_friends = 25;

	$query = "SELECT COUNT(*) FROM lg_friendlist WHERE username = '".ArghSession::get_username()."'";
	$result = mysql_query($query);
	$count = mysql_fetch_row($result);
	$friends = $count[0];
	
	//DELETE_FRIEND
	if ($_GET['action'] == 'delete') {
		$query = "DELETE FROM lg_friendlist WHERE username = '".ArghSession::get_username()."' AND friend = '".mysql_real_escape_string($_GET['friend'])."'";
		mysql_query($query);
		$friends--;
	}
	
	if ($friends < $max_friends) {
		//ADD_FRIEND
		if (isset($_POST['add_friend'])) {
			$query = "SELECT username FROM lg_users WHERE username LIKE '".mysql_real_escape_string($_POST['friend'])."'";
			$result = mysql_query($query);
			if (mysql_num_rows($result) && $_POST['friend'] != ArghSession::get_username()) {
				$l = mysql_fetch_object($result);
				$friendWithCase = $l->username;
				$query = "INSERT INTO lg_friendlist (username, friend) VALUES ('".ArghSession::get_username()."', '".$friendWithCase."')";
				mysql_query($query);
				$friends++;
			}
		}
	}
	if ($friends >= $max_friends) {
		$msg = '<center><span class="lose">'.Lang::FRIENDLIST_FULL.'</span></center>';
	}

	ArghPanel::begin_tag(Lang::FRIENDLIST);
	
	echo '<center>'.Lang::FRIENDLIST_INFO.'</center>';
	
	if (isset($msg)) {
		echo $msg.'<br />';
	}
	
	//Friends + online status
	$query = "SELECT f.friend, o.user FROM lg_friendlist f LEFT JOIN lg_usersonline o ON f.friend = o.user WHERE f.username = '".ArghSession::get_username()."'";
	$result = mysql_query($query);
	
	//People who have me in their friendlist
	$query2 = "SELECT username FROM lg_friendlist WHERE friend = '".ArghSession::get_username()."'";
	$result2 = mysql_query($query2);
	$frienders = array();
	while ($l = mysql_fetch_object($result2)) {
		$frienders[] = $l->username;
	}

	echo '<table class="listing">
		<colgroup>
			<col width="15%" />
			<col width="15%" />
			<col width="55%" />
			<col width="15%" />
		</colgroup>
		<thead>
			<tr>
				<th>'.Lang::STATUS.'</th>
				<th>'.Lang::MUTUAL.' ?</th>
				<th>'.Lang::USERNAME.'</th>
				<th>'.Lang::DELETE.'</th>
			</tr>
		</thead>
		<tbody>';
	$i = 0;
	while ($ofriend = mysql_fetch_object($result)) {
		echo '<tr'.Alternator::get_alternation($i).'>
			<td>'.(empty($ofriend->user) ? '<img src="img/icons/user_delete.png" alt="" />&nbsp;<span class="lose">'.Lang::OFFLINE.'</span>' : '<img src="img/icons/user_go.png" alt="" />&nbsp;<span class="win">'.Lang::ONLINE.'</span>').'</td>
			<td>'.(in_array($ofriend->friend, $frienders) ? '<img src="img/icons/group.png" alt="" />' : '').'</td>
			<td><a href="?f=player_profile&player='.$ofriend->friend.'">'.$ofriend->friend.'</a></td>
			<td><a href="?f=friendlist&action=delete&friend='.$ofriend->friend.'"><img src="img/icons/cross.png" alt="'.Lang::DELETE.'" /></a></td>
		</tr>';
	}
	echo '</tbody></table>';
	
	if ($friends < $max_friends) {
		echo '<br /><br /><center>
			<form method="POST" action="?f=friendlist">
				<img src="img/icons/add.png" alt="" />&nbsp;'.Lang::ADD_FRIEND.'&nbsp;<input type="texte" name="friend" value="" />&nbsp;<input type="submit" name="add_friend" value="'.Lang::ADD.'" />
			</form>
			</center>';
	}
	ArghPanel::end_tag();
?>