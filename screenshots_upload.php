<script language="javascript">
	/*
	function hideSubmit() {
		$('#submitter').html('<img src="img/ajax-loader.gif" alt="" />');
	}
	*/
</script>
<?php
	require 'classes/ScreenshotModule.php';
	
	ArghPanel::begin_tag(Lang::SCREENSHOTS_UPLOAD);
	
	//UPLOAD
	if (isset($_POST['go'])) {
		$max_size = 2097152; //2 * 1024 * 1024
		$msg = '';
		
		if (empty($_POST['name'])) {
			$global_error = true;
			$screenshot_error_info = MISSING_NAME;
		}
		
		if (!empty($_FILES['screenshot']['name']) && !$global_error) {
			if (strtolower(strrchr($_FILES['screenshot']['name'], '.')) == Screenshot::SCREENSHOT_EXTENSION) {
				if ($_FILES['screenshot']['size'] <= $max_size) {
				
					$ss = new Screenshot();
					$ss->_name = $_POST['name'];
					$ss->_uploader = ArghSession::get_username();
					$ss->_heroes = $_POST['heroes'];
					$ss->_keywords = $_POST['keywords'];
					$ss->_id = $ss->save_screenshot();
					
					if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $ss->get_screenshot_filepath())) {
					
						//Thumbnail
						$img = imagecreatefromjpeg($ss->get_screenshot_filepath());
						$img_size = getimagesize($ss->get_screenshot_filepath());
						$new_width = Screenshot::THUMBNAIL_WIDTH;

						$ratio = $new_width * 100 / $img_size[0];
						$new_height = $img_size[1] * $ratio / 100;

						$new_img = imagecreatetruecolor($new_width , $new_height) or die(Lang::ERROR);

						imagecopyresampled($new_img, $img, 0, 0, 0, 0, $new_width, $new_height, $img_size[0], $img_size[1]);
						
						$img_name = $ss->_id.Screenshot::THUMBNAIL_SUFFIX.Screenshot::SCREENSHOT_EXTENSION;

						$black = imagecolorallocate($new_img, 0, 0, 0);
						$white = imagecolorallocate($new_img, 255, 255, 255);
						
						$font = "./fonts/visitor1.ttf";
						$fontsize_small = 8;
						$fontsize = 10;
						
						imagettftext($new_img, $fontsize_small, 0, $new_width - 65, $new_height - 3, $black, $font, 'www.dota.fr');	
						imagettftext($new_img, $fontsize_small, 0, $new_width - 66, $new_height - 4, $white, $font, 'www.dota.fr');
						
						imagettftext($img, $fontsize, 0, $img_size[0] - 79, $img_size[1] - 4, $black, $font, 'www.dota.fr');	
						imagettftext($img, $fontsize, 0, $img_size[0] - 80, $img_size[1] - 5, $white, $font, 'www.dota.fr');
						
						imagejpeg($img , $ss->get_screenshot_filepath(), 100);
						imagejpeg($new_img , Screenshot::SCREENSHOT_FOLDER.$img_name, 100);
						imagedestroy($img);
					
						$screenshot_ok = true;
					} else {
						$global_error = true;
						$screenshot_error_info .= Lang::FILE_UPLOAD_ERROR.'<br />'.Lang::ERROR.': '.$_FILES['screenshot']['error'];
						$ss->delete_screenshot(false);
					}
				} else {
					$global_error = true;
					$screenshot_error_info .= sprintf(Lang::FILE_MAX_WEIGHT_EXCEEDED, round($max_size / (1024 * 1024), 2));
				}
			} else {
				$global_error = true;
				$screenshot_error_info .= Lang::FILE_EXTENSION_ERROR_JPEG_ONLY;
			}
		}
		
		if ($global_error) {
			echo $screenshot_error_info;
		} else {
			$_POST = array();
			echo '<center><span class="win">'.Lang::SCREENSHOTS_WAITING_FOR_VALIDATION.'</span></center><br />';
		}
	}
	
	echo '<form enctype="multipart/form-data" method="POST" action="?f=screenshots_upload">
		<table class="listing">
			<colgroup>
				<col width="25%" />
				<col width="75%" />
			</colgroup>
			<tr>
				<td>'.Lang::NAME.'</td>
				<td><input type="text" value="'.stripslashes($_POST['name']).'" name="name" /></td>
			</tr>
			<tr>
				<td>'.Lang::FILE.'</td>
				<td><input type="file" name="screenshot" value="" /></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td valign="top">'.Lang::KEYWORDS.'</td>
				<td><table>';
				$query = "SELECT * FROM lg_screenshots_base_keywords ORDER BY keyword ASC";
				$result = mysql_query($query);
				while ($obj = mysql_fetch_object($result)) {
					echo '<tr>
							<td><input type="checkbox" name="keywords[]" value="'.$obj->keyword.'"'.check_box_array($obj->keyword, 'keywords').' /></td><td>'.$obj->keyword.'</td>';
							if ($obj = mysql_fetch_object($result)) {
								echo '<td>&nbsp;&nbsp;&nbsp;</td><td><input type="checkbox" name="keywords[]" value="'.$obj->keyword.'"'.check_box_array($obj->keyword, 'keywords').' /></td><td>'.$obj->keyword.'</td>';
								if ($obj = mysql_fetch_object($result)) {
									echo '<td>&nbsp;&nbsp;&nbsp;</td><td><input type="checkbox" name="keywords[]" value="'.$obj->keyword.'"'.check_box_array($obj->keyword, 'keywords').' /></td><td>'.$obj->keyword.'</td>';
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
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td valign="top">'.Lang::HEROES_INVOLVED.'</td>
				<td>
					<table>';

	$query = "SELECT * FROM lg_heroes ORDER BY hero ASC";
	$result = mysql_query($query);
	while ($obj = mysql_fetch_object($result)) {
		echo '<tr>
				<td><input type="checkbox" name="heroes[]" value="'.$obj->hero.'"'.check_box_array($obj->hero, 'heroes').' /></td><td><img src="img/heroes/'.$obj->hero.'.gif" width="24" height="24" alt="" /></td><td>'.$obj->hero.'</td>';
				if ($obj = mysql_fetch_object($result)) {
					echo '<td>&nbsp;&nbsp;&nbsp;</td><td><input type="checkbox" name="heroes[]" value="'.$obj->hero.'"'.check_box_array($obj->hero, 'heroes').' /></td><td><img src="img/heroes/'.$obj->hero.'.gif" width="24" height="24" alt="" /></td><td>'.$obj->hero.'</td>';
				} else {
					echo '<td colspan="2"></td>';
				}
		echo '</tr>';
	}
	
			
	echo '</table></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="go" value="'.Lang::VALIDATE.'" style="width: 150px;" /></td>
			</tr>';
	
	echo '</table></form>';
	
	ArghPanel::end_tag();
	
	
?>