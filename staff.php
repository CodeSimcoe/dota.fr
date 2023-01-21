<?php
	ArghPanel::begin_tag(Lang::MENU_STAFF);
	
	$req = "
		SELECT username, rights_base 
		FROM lg_users
		WHERE rights_base != 0
		ORDER BY rights_base, username";
	$res = mysql_query($req) or die(mysql_error());
	if (mysql_num_rows($res) != 0) {
		echo '<table class="listing">';
		echo '<colgroup>
				<col width="200" />
				<col />
			</colgroup>
			<thead>
				<tr>
					<th>'.Lang::USERNAME.'</th>
					<th>'.Lang::ROLE.'</th>
				</tr>
			</thead>';
		$count = 0;
		while ($obj = mysql_fetch_object($res)) {
			echo '<tr'.Alternator::get_alternation($count).'>';
			echo '<td><a href="?f=player_profile&player='.$obj->username.'">'.$obj->username.'</a></td>';
			echo '<td>'.RightsMode::colorize_rights(RightsMode::get_rights_label($obj->rights_base)).'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
	
/*
?>
<table class="simple">
	<tr><td><b><?php echo Lang::USERNAME; ?></b></td><td><b><?php echo Lang::STAFF_FUNCTION; ?></b></td><td><b><?php echo Lang::EMAIL; ?></b></td></tr>
	<tr><td colspan="3" class="line"></td></tr>
	
<?php

	function mail_protect($mail) {
		return str_replace(array('.', '@'), array(' <i>point</i> ', ' <i>arobase</i> '), $mail);
	}
	
	//Webmaster
	$req="SELECT * FROM lg_users WHERE rank='webmaster' ORDER BY username ASC";
	$t=mysql_query($req);
	while ($l=mysql_fetch_object($t)) {
		echo '<tr><td><a href="?f=player_profile&player='.$l->username.'">'.$l->username.'</a></td><td><span class="lose"><b>webmaster</b></span></td><td>'.mail_protect($l->mail).'</td></tr>';
	}
	
	//Admin de div
	$req="SELECT u.*, d.* FROM lg_users u, lg_divisions d WHERE u.username=d.admin ORDER BY d.nom ASC";
	$t=mysql_query($req);
	if (mysql_num_rows($t) > 0) echo '<tr><td colspan="3">&nbsp;</td></tr>';
	while ($l=mysql_fetch_object($t)) {
		echo '<tr><td><a href="?f=player_profile&player='.$l->username.'">'.$l->username.'</a></td><td><span class="vip"><b>admin d'.$l->nom.'</b></span></td><td>'.mail_protect($l->mail).'</td></tr>';
	}
	
	//Admin ladder
	$req="SELECT u.*, a.* FROM lg_users u, lg_ladderadmins a WHERE u.username = a.user ORDER BY u.username ASC";
	$t=mysql_query($req);
	if (mysql_num_rows($t) > 0) echo '<tr><td colspan="3">&nbsp;</td></tr>';
	while ($l=mysql_fetch_object($t)) {
		echo '<tr><td><a href="?f=player_profile&player='.$l->username.'">'.$l->username.'</a></td><td><span class="win"><b>Admin Ladder</b></span></td><td>'.mail_protect($l->mail).'</td></tr>';
	}
	
	//Admin news
	$req="SELECT * FROM lg_users WHERE rank='admin news' ORDER BY username ASC";
	$t=mysql_query($req);
	if (mysql_num_rows($t) > 0) echo '<tr><td colspan="3">&nbsp;</td></tr>';
	while ($l=mysql_fetch_object($t)) {
		echo '<tr><td><a href="?f=player_profile&player='.$l->username.'">'.$l->username.'</a></td><td><b>admin news</b></td><td>'.mail_protect($l->mail).'</td></tr>';
	}
	
	//Newser
	$req="SELECT * FROM lg_users WHERE rank='newser' ORDER BY username ASC";
	$t=mysql_query($req);
	if (mysql_num_rows($t) > 0) echo '<tr><td colspan="3">&nbsp;</td></tr>';
	while ($l=mysql_fetch_object($t)) {
		echo '<tr><td><a href="?f=player_profile&player='.$l->username.'">'.$l->username.'</a></td><td><span style="color: #9900FF;"><b>newser<b/></span></td><td>'.mail_protect($l->mail).'</td></tr>';
	}
?>
</table>
<?php
*/
	ArghPanel::end_tag();
?>