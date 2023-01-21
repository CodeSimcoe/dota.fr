<?php
	require 'classes/ScreenshotModule.php';

	ArghPanel::begin_tag(Lang::SCREENSHOTS_LAST_ONES);
	
	//LAST_SCREENSHOTS
	$sql_screenshots = ScreenshotModule::get_last_screenshots();
	
	if (mysql_num_rows($sql_screenshots)) {
		
		echo '<table class="simple"><tr>';
		while ($sql_ss = mysql_fetch_object($sql_screenshots)) {
			$ss = new Screenshot();
			$ss->build_from_sql_resource($sql_ss);
			
			echo '<td align="center"><strong>'.$ss->_name.'</strong><br />'.Lang::POSTED_BY.': <a href="?f=player_profile&player='.$ss->_uploader.'">'.$ss->_uploader.'</a><br /><br />';
			$ss->display_thumbnail();
			echo '<br /><br />'.date(Lang::DATE_FORMAT_HOUR, $ss->_date_upload).'<br />'.$ss->rating_to_stars().'</td>';
		}
		echo '</tr></table>';
		
	}
	
	ArghPanel::end_tag();
	
	ArghPanel::begin_tag(Lang::SCREENSHOTS_RANDOM);
	
	//RANDOM_SCREENSHOTS
	$sql_screenshots = ScreenshotModule::get_random_screenshots();
	
	if (mysql_num_rows($sql_screenshots)) {
		
		echo '<table class="simple"><tr>';
		while ($sql_ss = mysql_fetch_object($sql_screenshots)) {
			$ss = new Screenshot();
			$ss->build_from_sql_resource($sql_ss);
			
			echo '<td align="center"><strong>'.$ss->_name.'</strong><br />'.Lang::POSTED_BY.': <a href="?f=player_profile&player='.$ss->_uploader.'">'.$ss->_uploader.'</a><br /><br />';
			$ss->display_thumbnail();
			echo '<br /><br />'.date(Lang::DATE_FORMAT_HOUR, $ss->_date_upload).'<br />'.$ss->rating_to_stars().'</td>';
		}
		echo '</tr></table>';
		
	}
	
	ArghPanel::end_tag();
	
	ArghPanel::begin_tag(Lang::SCREENSHOTS_LAST_ONES);
	
	//RANDOM_SCREENSHOTS
	$sql_screenshots = ScreenshotModule::get_last_screenshots(20);
	
	if (mysql_num_rows($sql_screenshots)) {
		
		echo '<table class="listing">
			<colgroup>
				<col width="35%" />
				<col width="25%" />
				<col width="15%" />
				<col width="25%" />
			</colgroup>
			<thead>
				<tr>
					<th>'.Lang::NAME.'</th>
					<th>'.Lang::USERNAME.'</th>
					<th>'.Lang::RATING.'</th>
					<th>'.Lang::HEROES.'</th>
				</tr>
			</thead>
			<tbody>';
		$i = 0;
		while ($sql_ss = mysql_fetch_object($sql_screenshots)) {
			$ss = new Screenshot();
			$ss->build_from_sql_resource($sql_ss);
			$ss->get_screenshot_heroes();
			
			echo '<tr'.Alternator::get_alternation($i).'>
					<td><a href="?f=screenshot&id='.$ss->_id.'">'.$ss->_name.'</a></td>
					<td><a href="?f=player_profile&player='.$ss->_uploader.'">'.$ss->_uploader.'</a></td>
					<td>'.$ss->rating_to_stars().'</td>
					<td>';
			$j = 0;
			foreach ($ss->_heroes as $hero) {
				$j++;
				echo '<img src="img/heroes/'.$hero.'.gif" width="24" height="24" alt="" />';
				if ($j == 5) {
					echo '&nbsp;...';
					break;
				}
			}
			echo '</td>
				</tr>';
		}
		echo '</tbody></table>';
		
	}
	
	ArghPanel::end_tag();
	
	ArghPanel::begin_tag(Lang::SCREENSHOTS_BESTS);
	
	//BEST_SCREENSHOTS
	$sql_screenshots = ScreenshotModule::get_best_screenshots(10);
	
	if (mysql_num_rows($sql_screenshots)) {
		
		echo '<table class="listing">
			<colgroup>
				<col width="35%" />
				<col width="25%" />
				<col width="15%" />
				<col width="25%" />
			</colgroup>
			<thead>
				<tr>
					<th>'.Lang::NAME.'</th>
					<th>'.Lang::USERNAME.'</th>
					<th>'.Lang::RATING.'</th>
					<th>'.Lang::HEROES.'</th>
				</tr>
			</thead>
			<tbody>';
		$i = 0;
		while ($sql_ss = mysql_fetch_object($sql_screenshots)) {
			$ss = new Screenshot();
			$ss->build_from_sql_resource($sql_ss);
			$ss->get_screenshot_heroes();
			
			echo '<tr'.Alternator::get_alternation($i).'>
					<td><a href="?f=screenshot&id='.$ss->_id.'">'.$ss->_name.'</a></td>
					<td><a href="?f=player_profile&player='.$ss->_uploader.'">'.$ss->_uploader.'</a></td>
					<td>'.$ss->rating_to_stars().'</td>
					<td>';
			$j = 0;
			foreach ($ss->_heroes as $hero) {
				$j++;
				echo '<img src="img/heroes/'.$hero.'.gif" width="24" height="24" alt="" />';
				if ($j == 5) {
					echo '&nbsp;...';
					break;
				}
			}
			echo '</td>
				</tr>';
		}
		echo '</tbody></table>';
		
	}
	
	ArghPanel::end_tag();
?>