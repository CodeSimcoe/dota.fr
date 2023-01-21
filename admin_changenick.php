<?php
	ArghSession::exit_if_not_rights(
		RightsMode::WEBMASTER
	);
?>
<script type="text/javascript" src="/ligue/javascript/jquery.js"></script>
<script type="text/javascript" src="/ligue/javascript/ui.core.js"></script>
<script language="javascript">
	function action(old_username, new_username, accept, id) {
		$.get('ajax/approve_refuse_nick_change.php',
			{
				old_username: old_username,
				new_username: new_username,
				accept: accept
			},
			function(data) {
				if (accept == 1) {
					$('#img_' + id).attr('src', 'img/icons/accept.png');
				} else if (accept == 2) {
					$('#img_' + id).attr('src', 'img/icons/exclamation.png');
				}
			}
		);
	}
</script>
<?php	
	ArghPanel::begin_tag(Lang::USERNAME_CHANGE_REQUESTS);
	
	$query = "SELECT * FROM lg_pending_nick_changes WHERE changed = 0 ORDER BY request_time ASC";
	$result = mysql_query($query);
	
	echo '<table class="listing">
		<colgroup>
			<col width="11%" />
			<col width="30%" />
			<col width="30%" />
			<col width="15%" />
			<col width="16%" />
		<colgroup>
		<thead>
			<tr>
				<th></th>
				<th>'.Lang::CURRENT_USERNAME.'</th>
				<th>'.Lang::REQUESTED_USERNAME.'</th>
				<th>'.Lang::DATE.'</th>
				<th></th>
			</tr>
		</thead>
		<tbody>';
		
	$i = 0;
	while ($obj = mysql_fetch_object($result)) {
	
		switch ($obj->validated) {
			case 0:
				$img = 'information';
				break;
				
			case 1:
				$img = 'accept';
				break;
				
			case 2:
				$img = 'exclamation';
				break;
		}
	
		echo '<tr'.Alternator::get_alternation($i).'>
				<td align="center"><img src="img/icons/'.$img.'" id="img_'.$i.'" /></td>
				<td><a href="?f=player_profile&player='.$obj->old_username.'">'.$obj->old_username.'</a></td>
				<td>'.$obj->new_username.'</td>
				<td>'.date(Lang::DATE_FORMAT_DAY, $obj->request_time).'</td>
				<td>
					<input type="button" value="'.Lang::ACCEPT.'" onClick="action(\''.$obj->old_username.'\', \''.$obj->new_username.'\', 1, '.$i.');" style="width: 75px;" />
					<input type="button" value="'.Lang::REFUSE.'" onClick="action(\''.$obj->old_username.'\', \''.$obj->new_username.'\', 2, '.$i.');" style="width: 75px;" />
				</td>
			</tr>';
	}
	
	echo '</tbody></table>';
	
	ArghPanel::end_tag();
?>