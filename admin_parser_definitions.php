<?php

	require_once('/home/www/ligue/classes/ReplayClasses.php');

	define('IMAGE_PATH', '/home/www/ligue/parser/Images/');

	ArghSession::exit_if_not_rights(
		RightsMode::WEBMASTER
	);
	
	function mysql_fetch_rowsarr($result, $numass = MYSQL_BOTH) {
		$got = array();
		if(mysql_num_rows($result) == 0) return $got;
		mysql_data_seek($result, 0);
		while ($row = mysql_fetch_array($result, $numass)) array_push($got, $row);
		return $got;
	}
	function mysql_fetch_scalar_rowsarr($result, $numass = MYSQL_BOTH) {
		$got = array();
		if(mysql_num_rows($result) == 0) return $got;
		mysql_data_seek($result, 0);
		while ($row = mysql_fetch_array($result, $numass)) array_push($got, $row[0]);
		return $got;
	}

	$get_version = ''; $img_string = '';
	if (isset($_GET['version'])) {
		$get_version = $_GET['version'];
		if (is_file(REPLAY_DEFINITIONS_PATH.$get_version.'.txt')) {
			$del = mysql_query("
				DELETE FROM parser_heroes WHERE version = '".$get_version."'
			") or die(mysql_error());
			$del = mysql_query("
				DELETE FROM parser_items WHERE version = '".$get_version."'
			") or die(mysql_error());
			$del = mysql_query("
				DELETE FROM parser_abilities WHERE version = '".$get_version."'
			") or die(mysql_error());
			$definition = new ReplayDefinition($get_version);
			if ($definition->exists) {
				foreach ($definition->heroes as $key => $hero) {
					if ($hero["code"] != $hero["base_code"]) continue;
					$ins = mysql_query("
						INSERT INTO parser_heroes (version, code, name, image)
						VALUES (
							'".mysql_real_escape_string($get_version)."', 
							'".mysql_real_escape_string($hero["code"])."',
							'".mysql_real_escape_string($hero["hero"])."', 
							'".mysql_real_escape_string($hero["img"])."')
					") or die(mysql_error());
					if (!is_file(IMAGE_PATH.'mini/'.$hero["img"].'.png')) {
						$image = imagecreatefrompng(IMAGE_PATH.$hero["img"].'.png');
						$new_image = imagecreatetruecolor(42, 22);
						imagecopyresampled($new_image, $image, 0, 0, 0, 0, 42, 21, 64, 64);
						imagepng($new_image, IMAGE_PATH.'mini/'.$hero["img"].'.png');
						imagedestroy($image); 
						imagedestroy($new_image);
						$img_string .= '<img src="/ligue/parser/Images/mini/'.$hero["img"].'.png" alt="" />';
					}
				}
				$img_string .= '<br /><br />';
				foreach ($definition->heroes as $key => $hero) {
					if ($hero["code"] != $hero["base_code"]) continue;
					if (!is_file(IMAGE_PATH.'news/'.$hero["img"].'.png')) {
						$image = imagecreatefrompng(IMAGE_PATH.$hero["img"].'.png');
						$new_image = imagecreatetruecolor(32, 32);
						imagecopyresampled($new_image, $image, 0, 0, 0, 0, 32, 32, 64, 64);
						imagepng($new_image, IMAGE_PATH.'news/'.$hero["img"].'.png');
						imagedestroy($image); 
						imagedestroy($new_image);
						$img_string .= '<img src="/ligue/parser/Images/news/'.$hero["img"].'.png" alt="" />';
					}
				}
				$img_string .= '<br /><br />';
				foreach ($definition->items as $key => $item) {
					$ins = mysql_query("
						INSERT INTO parser_items (version, code, is_main, name, image)
						VALUES (
							'".mysql_real_escape_string($get_version)."', 
							'".mysql_real_escape_string($item["code"])."',
							'".mysql_real_escape_string($item["main"])."',
							'".mysql_real_escape_string($item["name"])."', 
							'".mysql_real_escape_string($item["img"])."')
					") or die(mysql_error());
					if (!is_file(IMAGE_PATH.'news/'.$item["img"].'.png')) {
						$image = imagecreatefrompng(IMAGE_PATH.$item["img"].'.png');
						$new_image = imagecreatetruecolor(32, 32);
						imagecopyresampled($new_image, $image, 0, 0, 0, 0, 32, 32, 64, 64);
						imagepng($new_image, IMAGE_PATH.'news/'.$item["img"].'.png');
						imagedestroy($image); 
						imagedestroy($new_image);
						$img_string .= '<img src="/ligue/parser/Images/news/'.$item["img"].'.png" alt="" />';
					}
				}
				$img_string .= '<br /><br />';
				foreach ($definition->abilities as $key => $ability) {
					$ins = mysql_query("
						INSERT INTO parser_abilities (version, code, hero_code, name, image)
						VALUES (
							'".mysql_real_escape_string($get_version)."', 
							'".mysql_real_escape_string($ability["code"])."',
							'".mysql_real_escape_string($ability["hero_code"])."',
							'".mysql_real_escape_string($ability["ability"])."', 
							'".mysql_real_escape_string($ability["img"])."')
					") or die(mysql_error());
					if (!is_file(IMAGE_PATH.'news/'.$ability["img"].'.png')) {
						$image = imagecreatefrompng(IMAGE_PATH.$ability["img"].'.png');
						$new_image = imagecreatetruecolor(32, 32);
						imagecopyresampled($new_image, $image, 0, 0, 0, 0, 32, 32, 64, 64);
						imagepng($new_image, IMAGE_PATH.'news/'.$ability["img"].'.png');
						imagedestroy($image); 
						imagedestroy($new_image);
						$img_string .= '<img src="/ligue/parser/Images/news/'.$ability["img"].'.png" alt="" />';
					}
				}
			}
		}
	}

	ArghPanel::begin_tag(Lang::PARSER_DEFINITIONS);

	$req = "SELECT DISTINCT version FROM parser_heroes ORDER BY version";
	$res = mysql_query($req) or die(mysql_error());
	$bdd = mysql_fetch_scalar_rowsarr($res, MYSQL_NUM);
	
	$files = scandir(REPLAY_DEFINITIONS_PATH);
	
	$count = 0;
	echo '<table class="listing" border="0" cellpadding="0" cellspacing="0">';
	echo '<colgroup><col width="50" /><col /><col width="200" /></colgroup>';
	echo '<thead><tr><th>&nbsp;</th><th>'.Lang::FILE.'</th><th>&nbsp;</th></tr></thead>';
	foreach ($files as $file) {
		if (is_file(REPLAY_DEFINITIONS_PATH.$file)) {
			if (substr($file, -3) == 'txt') {
				$version = substr($file, 0, strlen($file) - 4);
				echo '<tr'.Alternator::get_alternation($count).'>';
				echo '<td style="text-align:center"><img src="/ligue/img/icons/'.(in_array($version, $bdd) ? 'accept' : 'exclamation').'.png" alt="" /></td>';
				echo '<td>'.$version.'</td>';
				echo '<td style="text-align:right; padding-right: 10px"><a href="?f=admin_parser_definitions&version='.$version.'">'.Lang::UPDATE.'</a></td>';
				echo '</tr>';
			}
		}
	}
	echo '</table>';
	
	if ($img_string != "") echo '<br /><br />'.$img_string;
	
	ArghPanel::end_tag();

?>