<script type="text/javascript">
$(document).ready(function() {
	$('.allies a').click(function() {
		$('#stats_months').load(
			'ajax/get_ladder_statistics_months.php',
			{ mode: 'allies', player: $(this).text() },
			function() { $('#stats_days, #stats_games').empty(); }
		);
	});
	$('#stats_months a').live('click', function() {
		$('#stats_days').load(
			'ajax/get_ladder_statistics_days.php',
			{ mode: 'allies', player: $(this).attr('p'), year: $(this).attr('y'), month: $(this).attr('m') },
			function() { $('#stats_games').empty(); }
		);
	});
	$('#stats_days a').live('click', function() {
		$('#stats_games').load(
			'ajax/get_ladder_statistics_games.php',
			{ mode: 'allies', player: $(this).attr('p'), year: $(this).attr('y'), month: $(this).attr('m'), day: $(this).attr('d') }
		);
	});
});
</script>
<?php

	ArghSession::exit_if_not_rights(array(RightsMode::WEBMASTER));

	include_once '/home/www/ligue/classes/LadderStatisticsModule.php';
	
	ArghPanel::begin_tag('Ladder - Alli&eacute;s');
	
	$orders = array("SUM(A.xp) DESC", "SUM(A.xp) ASC");
	
	foreach ($orders AS $order) {
	
		$allies = LadderStatisticsModule::get_allies(ArghSession::get_username(), $order, 0, 10);
	
		$count = 0;
		echo '<br /><table class="listing allies">';
		echo '<colgroup><col /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="50" /><col width="80" /></colgroup>';
		echo '<thead><tr><th>Player</th><th>P</th><th>C</th><th>W</th><th>L</th><th>A</th><th>L</th><th>XP</th></tr></thead>';
		foreach ($allies AS $row) {
			echo '<tr'.Alternator::get_alternation($count).'>';
			echo '<td><a href="javascript:void(0);">'.$row->label.'</a></td>';
			echo '<td>'.$row->played.'</td>';
			echo '<td>'.$row->closed.'</td>';
			echo '<td><span class="win">'.$row->win.'</span></td>';
			echo '<td><span class="lose">'.$row->lose.'</span></td>';
			echo '<td><span class="info">'.$row->away.'</span></td>';
			echo '<td><span class="draw">'.$row->left.'</span></td>';
			if ($row->xp == 0) {
				echo '<td><span class="info">'.$row->xp.'</span></td>';
			} else if ($row->xp > 0) {
				echo '<td><span class="win">+'.$row->xp.'</span></td>';
			} else {
				echo '<td><span class="lose">'.$row->xp.'</span></td>';		
			}
			echo '</tr>';
		}
		echo '</table><br />';
	}
	
	echo '<br /><div id="stats_months"></div><br />';
	echo '<br /><div id="stats_days"></div><br />';
	echo '<br /><div id="stats_games"></div><br />';

	ArghPanel::end_tag();

?>