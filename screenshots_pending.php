<?php
	ArghSession::exit_if_not_rights(
		RightsMode::SCREENSHOTS_ADMIN
	);
?>

<script language="javascript">
	function show(screen_id) {
		$('.hidden_row_' + screen_id).toggle();
	}
</script>

<?php
	require 'classes/ScreenshotModule.php';
	
	//DELETE
	if (isset($_POST['delete'])) {
		$ss = new Screenshot();
		$ss->_id = (int)$_POST['screenshot_id'];
		
		$ss->delete_screenshot();
	}
	
	//VALIDATE
	if (isset($_POST['validate'])) {
		$ss = new Screenshot();
		$ss->_id = (int)$_POST['screenshot_id'];
		$ss->_name = $_POST['name'];
		//$ss->_uploader = ArghSession::get_username();
		$ss->_heroes = $_POST['heroes'];
		$ss->_keywords = $_POST['keywords'];
		
		$ss->validate_screenshot();
	}
	
	//LAST_SCREENSHOTS
	$sql_last_screenshots = ScreenshotModule::get_pending_screenshots();
	
	ArghPanel::begin_tag(Lang::SCREENSHOTS_PENDING);
	
	if (mysql_num_rows($sql_last_screenshots)) {
		echo '<table class="listing">
			<colgroup>
				<col width="25%" />
				<col width="35%" />
				<col width="25%" />
				<col width="15%" />
			</colgroup>';
		while ($sql_ss = mysql_fetch_object($sql_last_screenshots)) {
			$ss = new Screenshot();
			$ss->_id = $sql_ss->id;
			$ss->_uploader = $sql_ss->uploader;
			$ss->_name = stripslashes($sql_ss->name);
			$ss->_date_upload = $sql_ss->date_upload;
			
			$ss->get_screenshot_keywords();
			$ss->get_screenshot_heroes();
			
			echo '<form method="POST" action="?f=screenshots_pending">
				<input type="hidden" name="screenshot_id" value="'.$ss->_id.'" />
				<tr>
					<td><input type="text" name="name" value="'.stripslashes($ss->_name).'" /><br />'.date(Lang::DATE_FORMAT_HOUR, $ss->_date_upload).'</td>
					<td>';$ss->display_thumbnail_pending();echo '</td>
					<td>'.Lang::BY.' <a href="?f=player_profile&player='.$ss->_uploader.'">'.$ss->_uploader.'</a></td>
					<td align="center">
						
							<input type="button" value="'.Lang::INFOS.'" onClick="javascript:show('.$ss->_id.');" />
							<br />
							<input type="submit" name="validate" value="'.Lang::VALIDATE.'" class="hidden_row_'.$ss->_id.'" style="display: none;" />
							<br />
							<input type="submit" name="delete" value="'.Lang::DELETE.'" class="hidden_row_'.$ss->_id.'" style="display: none;" />
					</td>
				</tr>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr class="hidden_row_'.$ss->_id.'" style="display: none;">
					<td valign="top">'.Lang::KEYWORDS.'</td>
					<td colspan="3"><table>';
			$query = "SELECT * FROM lg_screenshots_base_keywords ORDER BY keyword ASC";
			$result = mysql_query($query);
			while ($obj = mysql_fetch_object($result)) {
				echo '<tr>
						<td><input type="checkbox" name="keywords[]" value="'.$obj->keyword.'"'.check_box_array2($obj->keyword, $ss->_keywords).' /></td><td>'.$obj->keyword.'</td>';
						if ($obj = mysql_fetch_object($result)) {
							echo '<td>&nbsp;&nbsp;&nbsp;</td><td><input type="checkbox" name="keywords[]" value="'.$obj->keyword.'"'.check_box_array2($obj->keyword, $ss->_keywords).' /></td><td>'.$obj->keyword.'</td>';
							if ($obj = mysql_fetch_object($result)) {
								echo '<td>&nbsp;&nbsp;&nbsp;</td><td><input type="checkbox" name="keywords[]" value="'.$obj->keyword.'"'.check_box_array2($obj->keyword, $ss->_keywords).' /></td><td>'.$obj->keyword.'</td>';
							} else {
								echo '<td></td>';
							}
						} else {
							echo '<td></td>';
						}
				echo '</tr>';
			}
			echo '</table></td>
				</tr>
				<tr class="hidden_row_'.$ss->_id.'" style="display: none;">
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr class="hidden_row_'.$ss->_id.'" style="display: none;">
					<td valign="top">'.Lang::HEROES_INVOLVED.'</td>
					<td colspan="3"><table>';
			$query = "SELECT * FROM lg_heroes ORDER BY hero ASC";
			$result = mysql_query($query);
			while ($obj = mysql_fetch_object($result)) {
				echo '<tr>
						<td><input type="checkbox" name="heroes[]" value="'.$obj->hero.'"'.check_box_array2($obj->hero, $ss->_heroes).' /></td><td><img src="img/heroes/'.$obj->hero.'.gif" width="24" height="24" alt="" /></td><td>'.$obj->hero.'</td>';
						if ($obj = mysql_fetch_object($result)) {
							echo '<td>&nbsp;&nbsp;&nbsp;</td><td><input type="checkbox" name="heroes[]" value="'.$obj->hero.'"'.check_box_array2($obj->hero, $ss->_heroes).' /></td><td><img src="img/heroes/'.$obj->hero.'.gif" width="24" height="24" alt="" /></td><td>'.$obj->hero.'</td>';
						} else {
							echo '<td></td>';
						}
				echo '</tr>';
			}
			echo '</table></td>
				</tr></form>';
		}
		echo '</table>';
		
	} else {
		echo '<center>'.Lang::NO_PENDING_SCREENSHOTS.'</center>';
	}
	
	ArghPanel::end_tag();
	
?>