<?php
	ArghSession::exit_if_not_rights(
		RightsMode::WEBMASTER
	);
	
	if (!isset($_POST['mode'])) $_POST['mode'] = '24h';
	if (!isset($_POST['sort'])) $_POST['sort'] = 'date';
	
	ArghPanel::begin_tag(Lang::ADMIN_LOG);
?>
<form method="POST" action="?f=admin_log">
	<?php echo Lang::MODE; ?> 
	<select name="mode">
		<option value="24h" <?php if (isset($_POST['mode'])) echo attr_('24h', $_POST['mode']); ?>><?php echo Lang::LAST_24_HOURS; ?></option>
		<option value="week" <?php if (isset($_POST['mode'])) echo attr_('week', $_POST['mode']); ?>><?php echo Lang::LAST_WEEK; ?></option>
		<option value="month" <?php if (isset($_POST['mode'])) echo attr_('month', $_POST['mode']); ?>><?php echo Lang::LAST_MONTH; ?></option>
		<option value="all" <?php if (isset($_POST['mode'])) echo attr_('all', $_POST['mode']); ?>><?php echo Lang::ALL_LENGTHS; ?></option>
	</select> 
	<?php echo Lang::SORT; ?> 
	<select name="sort">
		<option value="date" <?php if (isset($_POST['sort'])) echo attr_('date', $_POST['sort']); ?>><?php echo Lang::SORT_CHRONOLOGICAL; ?></option>
		<option value="user" <?php if (isset($_POST['sort'])) echo attr_('user', $_POST['sort']); ?>><?php echo Lang::SORT_USER; ?></option>
		<option value="action" <?php if (isset($_POST['sort'])) echo attr_('action', $_POST['sort']); ?>><?php echo Lang::SORT_ACTION; ?></option>
	</select> 
	<input type="submit" value="<?php echo Lang::VALIDATE; ?>">
</form>

<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag(Lang::ADMIN_LOGGED_ACTIONS);
?>

<table class="listing">
	<colgroup>
		<col width="20%" />
		<col width="60%" />
		<col width="20%" />
	</colgroup>
	<tr>
		<td><b><?php echo Lang::WHO; ?> ?</b></td>
		<td><b><?php echo Lang::WHAT; ?> ?</b></td>
		<td><b><?php echo Lang::WHEN; ?> ?</b></td>
	</tr>
	<tr><td colspan="3" class="line"></td></tr>
<?php
	switch($_POST['mode']) {
			
		case '24h':
			$time = time() - 86400;
			$cond = 'AND quand > '.$time;
			break;
			
		case 'month':
			//31 * 86400
			$time = time() - 2678400;
			$cond = 'AND quand > '.$time;
			break;
			
		case 'all':
			$cond = '';
			break;
		
		case 'week':
		default:
			//7 * 86400
			$time = time() - 604800;
			$cond = 'AND quand > '.$time;
			break;
	}
	
	switch($_POST['sort']) {
		default:
		case 'date':
			$sort = 'quand DESC';
			break;
			
		case 'user':
			$sort = 'qui ASC';
			break;
			
		case 'action':
			$sort = 'quoi ASC';
			break;
	}

	$req = "SELECT u.username, u.access, a.quoi, a.quand
			FROM lg_users u, lg_adminlog a
			WHERE u.username = a.qui
			".$cond."
			ORDER BY ".$sort;
	$t = mysql_query($req);
	$k = 0;
	while ($l = mysql_fetch_row($t)) {
		if ($l[0] == 'CronTask') {
				$qui = '<span class="draw">'.$l[0].'</span>';
		} else if ($l[0] == 'LadderGuardian') {
				$qui = '<span class="red">'.$l[0].'</span>';
		} else if ($l[1] >= 100) {
				$qui = '<span class="lose">'.$l[0].'</span>';
		} else {
				$qui = '<span class="win">'.$l[0].'</span>';
		}
				
		echo '<tr'.Alternator::get_alternation($k).'>
			<td>'.$qui.'</td>
			<td>'.$l[2].'</td>
			<td>'.date(Lang::DATE_FORMAT_HOUR, $l[3]).'</td>
		</tr>';
	}
?>
</table>
<?php
	ArghPanel::end_tag();
?>