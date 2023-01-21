<?php
	ArghSession::exit_if_not_logged();
	if (!ArghSession::is_gold()) exit;
?>
	<script language="javascript">
		$(document).ready(function() {
			$('#checkall').click(function() {
				isChecked = this.checked;
				$('.box').each(function() {
					this.checked = isChecked;
				});
			});
		});
	</script>
<?php	
	ArghPanel::begin_tag(Lang::NOTIFICATIONS);
	
	//DELETE
	if (isset($_POST['delete']) && is_array($_POST['mail'])) {
		NotificationManager::remove_notifications($_POST['mail'], ArghSession::get_username());
	}
	
	$notifs_sql = NotificationManager::get_user_notifications(ArghSession::get_username(), false);
	NotificationManager::update_user_new_status(ArghSession::get_username(), 0);
	
	if (mysql_num_rows($notifs_sql) > 0) {
		echo '<form method="POST" action="?f=notifications">
		<table class="listing">
			<colgroup>
				<col width="5%" />
				<col width="5%" />
				<col width="20%" />
				<col width="62%" />
				<col width="8%" />
			</colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" /></th>
					<th></th>
					<th>'.Lang::DATE.'</th>
					<th>'.Lang::MESSAGE.'</th>
					<th>'.Lang::LINK.'</th>
				</tr>
			</thead>
			<tbody>';
		$i = 0;
		while ($notif = mysql_fetch_object($notifs_sql)) {
			echo '<tr'.Alternator::get_alternation($i).'>
					<td><input type="checkbox" name="mail[]" value="'.$notif->id.'" class="box" /></td>
					<td><img src="img/icons/'.(($notif->new_notif == 1) ? 'email' : 'email_open').'.png" alt="" /></td>
					<td>'.date(Lang::DATE_FORMAT_HOUR, $notif->notif_time).'</td>
					<td>'.stripslashes($notif->message).'</td>
					<td><a href="'.$notif->link.'"><img src="img/icons/link_go.png" alt="" /><a></td>
				</tr>';
		}
		
		echo '</tbody>
			</table>
			<br />
			'.Lang::SELECTION.': <input type="submit" name="delete" value="'.Lang::DELETE.'" />
			</form>';
	} else {
		echo '<center>'.Lang::NO_MESSAGE.'</center>';
	}
	
	ArghPanel::end_tag();
?>