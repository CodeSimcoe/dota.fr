<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::LADDER_ADMIN,
			RightsMode::VIP_HEADADMIN,
			RightsMode::VIP_ADMIN
		)
	);

	require 'classes/ReportModule.php';
	
	//HANDLE
	if ($_GET['action'] == 'administrate') {
		$table = $is_vip ? 'lg_reports_vip' : 'lg_reports';
		$query = "UPDATE ".$table." SET admin = '".ArghSession::get_username()."', status='".Report::STATUS_BEING_HANDLED."' WHERE game_id = '".(int)$_GET['game_id']."' AND admin = ''";
		mysql_query($query);
	}
	
	$is_vip = ($_GET['vip'] == 'true');
	$get_vip = $is_vip ? '&vip=true' : '';
	$rm = new ReportModule($is_vip);
	
	$sql_reports = $rm->get_opened_reports();
	$nb_reports = mysql_num_rows($sql_reports);
	ArghPanel::begin_tag(sprintf(Lang::REPORT_OPENED_REPORTS, $nb_reports));
	
	//Color code
	$colors = array(
		'flame' => '<span class="lose">F</span>',
		'result' => '<span class="win">R</span>',
		'leaver' => '<span class="vip">L</span>',
		'ruining' => '<span class="fun">X</span>',
		'other' => '<span class="newser">O</span>',
	);
	
	echo '<center>';
	foreach ($colors as $key => $val) {
		echo $val.' : '.$key.'&nbsp;&nbsp;';
	}
	echo '</center><br />';
	
	if ($nb_reports > 0) {
		echo '<table class="listing">
			<colgroup>
				<col width="10%" />
				<col width="25%" />
				<col width="25%" />
				<col width="20%" />
				<col width="20%" />
			</colgroup>
			<thead>
				<tr>
					<th>'.Lang::GAME.'</th>
					<th>'.Lang::DATE.'</th>
					<th>'.Lang::REPORT_INITIATOR.'</th>
					<th>'.Lang::ADMIN.'</th>
					<th>'.Lang::REPORT_OPENING_REASONS.'</th>
				</tr>
			</thead>
			<tbody>';
		$i = 0;
		while ($report = mysql_fetch_object($sql_reports)) {
		
			$reasons = explode(';', $report->reasons);
			$str = '';
			foreach ($reasons as $reason) {
				$str .= $colors[$reason].'&nbsp;';
			}
		
			echo '<tr'.Alternator::get_alternation($i).'>
					<td><a href="?f=ladder_report&id='.$report->game_id.$get_vip.'">#'.$report->game_id.'</a></td>
					<td>'.date(Lang::DATE_FORMAT_HOUR, $report->opening_time).'</td>
					<td><a href="?f=player_profile&player='.$report->initiator.'">'.$report->initiator.'</a></td>
					<td>'.(empty($report->admin) ? '<img src="img/icons/arrow_right.png" alt="" />&nbsp;<a href="?f=admin_ladder_reports&action=administrate&game_id='.$report->game_id.$get_vip.'">'.Lang::REPORT_HANDLE.'</a>' : '<a href="?f=player_profile&player='.$report->admin.'">'.$report->admin).'</a></td>
					<td align="center">'.$str.'</td>
				</tr>';
		}
		echo '</tbody></table>';
	} else {
		echo '<center>'.Lang::REPORT_NO_OPENED_REPORTS.'</center>';
	}
	ArghPanel::end_tag();
	
	$sql_reports = $rm->get_last_reports();
	$nb_reports = mysql_num_rows($sql_reports);
	ArghPanel::begin_tag(Lang::REPORT_LAST_REPORTS);
	if ($nb_reports > 0) {
		echo '<table class="listing">
			<colgroup>
				<col width="10%" />
				<col width="25%" />
				<col width="20%" />
				<col width="20%" />
				<col width="25%" />
			</colgroup>
			<thead>
				<tr>
					<th>'.Lang::GAME.'</th>
					<th>'.Lang::DATE.'</th>
					<th>'.Lang::REPORT_INITIATOR.'</th>
					<th>'.Lang::ADMIN.'</th>
					<th>'.Lang::REPORT_CLOSE_TIME.'</th>
				</tr>
			</thead>
			<tbody>';
		$i = 0;
		while ($report = mysql_fetch_object($sql_reports)) {
		
			echo '<tr'.Alternator::get_alternation($i).'>
					<td><a href="?f=ladder_report&id='.$report->game_id.$get_vip.'">#'.$report->game_id.'</a></td>
					<td>'.date(Lang::DATE_FORMAT_HOUR, $report->opening_time).'</td>
					<td><a href="?f=player_profile&player='.$report->initiator.'">'.$report->initiator.'</a></td>
					<td><a href="?f=player_profile&player='.$report->admin.'">'.$report->admin.'</a></td>
					<td>'.date(Lang::DATE_FORMAT_HOUR, $report->close_time).'</td>
				</tr>';
		}
		echo '</tbody></table>';
	} else {
		echo '<center>'.Lang::REPORT_NO_OPENED_REPORTS.'</center>';
	}
	ArghPanel::end_tag();
	
	//1 month
	$limit = time() - 2678400;
	
	ArghPanel::begin_tag();
	$query = "SELECT admin, COUNT(admin) as Treated FROM lg_reports WHERE admin != '' AND close_time > ".$limit." GROUP BY admin ORDER BY Treated DESC";
	$result = mysql_query($query);
	echo '<table class="listing">
		<colgroup>
			<col width="30%" />
			<col width="70%" />
		</colgroup>
		<thead>
			<tr>
				<th>'.Lang::ADMIN.'</th>
				<th></th>
			</tr>
		</thead>
		<tbody>';
	$i = 0;
	while ($l = mysql_fetch_row($result)) {
		echo '<tr'.Alternator::get_alternation($i).'>
			<td>'.$l[0].'</td>
			<td>'.$l[1].'</td>
		</tr>';
	}
	echo '</tbody>
		</table>';
	ArghPanel::end_tag();
?>