<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::LADDER_ADMIN
		)
	);

	include ('ladder_functions.php');
	
	function preSelect($x, $y) {
		return ($x == $y) ? ' selected="selected"' : '';
	}
	
	$_POST['search_value'] = trim($_POST['search_value']);
	
	ArghPanel::begin_tag(Lang::ADMIN_MULTIPLE_IP);
	echo '<form method="POST" action="?f=admin_ladder_multiip">';
	echo Lang::ADMIN_CHOOSE_SEARCH_CRITERIA.'<br /><select name="what">';
	echo '<option value="user"'.preSelect($_POST['what'], 'user').'>'.Lang::USERNAME.'</option>
		<option value="ip"'.preSelect($_POST['what'], 'ip').'>'.Lang::IP.'</option>
		<option value="ipbegin"'.preSelect($_POST['what'], 'ipbegin').'>'.Lang::IP_BEGINS.'</option>
		<option value="ipcontains"'.preSelect($_POST['what'], 'ipcontains').'>'.Lang::IP_CONTAINS.'</option>';
	echo '</select>
	<input type="text" name="search_value" value="'.$_POST['search_value'].'" /><br />
	<input type="submit" name="go" value="Rechercher" />
	</form>';

	if (isset($_POST['go']) and !empty($_POST['search_value'])) {
		$allowedFields = array('ip', 'user', 'ipbegin', 'ipcontains');
		if (!in_array($_POST['what'], $allowedFields)) {
			exit;
		}
		
	echo '<br />
		<table class="listing">
			<colgroup>
				<col width="150" />
				<col />
				<col width="150" />
			</colgroup>
			<thead>
				<tr>
				<th>'.Lang::PLAYER.'</th>
				<th>'.Lang::WITH.'</th>
				<th>'.Lang::IP.'</th>
				</tr>
			</thead>
			<tbody>';

		if ($_POST['what'] == "ipcontains") {
			$req = "
				SELECT DISTINCTROW
					Users.user,
					'',
					''
				FROM (
					SELECT DISTINCTROW 
						u1.user, '', u1.ip
					FROM lg_user_ip u1
					WHERE u1.ip LIKE '%".mysql_real_escape_string($_POST['search_value'])."%'
				) AS Users
				ORDER BY Users.user ASC";
		} else if ($_POST['what'] == "ipbegin") {
			$req = "
				SELECT DISTINCTROW
					Users.user,
					'',
					''
				FROM (
					SELECT DISTINCTROW 
						u1.user, '', u1.ip
					FROM lg_user_ip u1
					WHERE u1.ip LIKE '".mysql_real_escape_string($_POST['search_value'])."%'
				) AS Users
				ORDER BY Users.user ASC";
		} else {
			$req = "SELECT DISTINCTROW u1.user, u2.user, u1.ip, u2.log_time
					FROM lg_user_ip u1, lg_user_ip u2
					WHERE u1.user != u2.user
					AND u1.ip = u2.ip
					AND u1.".$_POST['what']." LIKE '%".mysql_real_escape_string($_POST['search_value'])."%'
					ORDER BY u1.ip ASC";
		}
		$t = mysql_query($req);
		$j = 0;
		while ($l = mysql_fetch_row($t)) {
			if ($ipold != $l[2]) $j++;
			$alt = ($j%2 == 0) ? ' class="alternate"' : '';
			echo '<tr'.$alt.'>
				<td><a href="?f=player_profile&player='.$l[0].'">'.$l[0].'</a></td>
				<td><a href="?f=player_profile&player='.$l[1].'">'.$l[1].'</a> - '.$l[3].'</td>
				<td>'.$l[2].'</td>
				</tr>';
			$ipold = $l[2];
		}
		
		echo '</tbody>
			</table>';
	}
	
	ArghPanel::end_tag();
?>