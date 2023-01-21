<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::NEWS_HEADADMIN,
			RightsMode::NEWS_NEWSER
		)
	);

	ArghPanel::begin_tag(Lang::REPLAY_CENTER);

	$target = 'replaycenter/';
	$max_size = 3145728; //3 * 1024 * 1024
	$game_id = (int)$_POST['game_id'];
	
	function generateKey($length) {
		$pattern = 'abcdefghijklmnopqrstuvwxyz0123456789-_';
		$key = '';
		for ($i = 1; $i <= $length; $i++) {
			$key .= $pattern[rand(0, strlen($pattern) - 1)];
		}
		return $key;
	}

	$nom_file = $_FILES['fichier']['name'];
	$taille = $_FILES['fichier']['size'];
	$tmp = $_FILES['fichier']['tmp_name'];
	$type = strrchr($_FILES['fichier']['name'], '.');
	$type = substr($type, 1);
	$types_allowed = array('w3g');
	
	$msg = '';
	
	if (!empty($_POST['posted'])) {
		$team1 = substr($_POST['team1'], 0, 5);
		$team2 = substr($_POST['team2'], 0, 5);
	    if (!empty($_FILES['fichier']['name'])) {
	        if (in_array($type, $types_allowed)) {
	            if ($_FILES['fichier']['size'] <= $max_size) {
					$name = 'replay_'.generateKey(16).'.w3g';
	                if (move_uploaded_file($_FILES['fichier']['tmp_name'], $target.$name)) {
						$req = "INSERT INTO lg_replaycenter (
									qui_upload,
									date_upload,
									competition,
									team1,
									team2,
									fichier) 
								VALUES (
								'".ArghSession::get_username()."', 
								'".time()."', 
								'".mysql_real_escape_string(substr($_POST['competition'], 0, 30))."', 
								'".mysql_real_escape_string(substr($_POST['team1'], 0, 30))."', 
								'".mysql_real_escape_string(substr($_POST['team2'], 0, 30))."', 
								'".$name."')";
						mysql_query($req);
	                    $msg .= Lang::FILE_SUCCESSFULLY_UPLOADED.'<br />';
	                    $msg .= Lang::FILE.' : <b>'.$_FILES['fichier']['name'].'</b><br />';
	                    $msg .= Lang::SIZE.' : <b>'.round(($_FILES['fichier']['size'] / 1024), 2).' '.Lang::KILO_BYTES.'</b><br />';
	                } else {
	                    $msg .= '<center>'.Lang::FILE_UPLOAD_ERROR.'</center><br />
								'.Lang::ERROR.': '.$_FILES['fichier']['error'].'<br />';
	                }
	            } else {
	                $msg .= '<center>'.sprintf(Lang::FILE_MAX_WEIGHT_EXCEEDED, round($max_size / (1024 * 1024), 2)).'</center><br />';
	            }
	        } else {
	            $msg .= '<center>'.Lang::FILE_EXTENSION_ERROR_REPLAY_ONLY.'</center><br />';
	        }
	    }
		$msg .= '<br />';
	}
	
	echo '<table class="listing">';
	echo '<colgroup>
			<col width="5%" />
			<col width="10%" />
			<col width="30%" />
			<col width="10%" />
			<col width="10%" />
			<col width="15%" />
			<col width="20%" />
		</colgroup>';
	//Listing fichiers
	echo '<tr>
			<th>'.Lang::TYPE.'</th>
			<th>'.Lang::FILE.'</th>
			<th>'.Lang::COMPETITION.'</th>
			<th>'.Lang::TEAM.' 1</th>
			<th>'.Lang::TEAM.' 2</th>
			<th>'.Lang::UPLOADED_BY.'</th>
			<th>'.Lang::UPLOAD_DATE.'</th>
		</tr>
	<tr><td colspan="7" class="line"></td></tr>';
	
	$req = "SELECT * FROM lg_replaycenter ORDER BY id DESC";
	$t = mysql_query($req);
	$j = 0;
	if (mysql_num_rows($t) > 0) {
		while ($l = mysql_fetch_object($t)) {
			$type = strrchr($l->fichier, '.');
			$type = substr($type, 1);
			echo '<tr'.Alternator::get_alternation($j).'>
				<td><center><img src="icon_w3g.jpg" title="'.Lang::REPLAY.'" /></center></td>
				<td><center><a href="replaycenter/'.$l->fichier.'">'.Lang::LINK.'</a></center></td>
				<td><center>'.$l->competition.'</center></td>
				<td><center>'.$l->team1.'</center></td>
				<td><center>'.$l->team2.'</center></td>
				<td><center>'.$l->qui_upload.'</center></td>
				<td><center>'.date(Lang::DATE_FORMAT_DAY, $l->date_upload).'</center></td>
			</tr>';
		}
	} else {
		echo '<tr><td colspan="7"><br /><center>'.Lang::NO_ENTRY.'</center></td></tr>';
	}
?>
	<tr><td colspan="7">&nbsp;</td></tr>
</table>
<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag(Lang::REPLAY_UPLOAD);
?>
<form enctype="multipart/form-data" action="?f=admin_replay_center" method="POST">
<table class="simple">
	<colgroup>
		<col width="25%" />
		<col width="75%" />
	</colgroup>
	<tr><td><?php echo Lang::COMPETITION; ?></td><td><input name="competition" type="text" maxlength="30" size="30" /></td></tr>
	<tr><td><?php echo Lang::TEAM; ?> 1</td><td><input name="team1" type="text" maxlength="30" size="30" /></td></tr>
	<tr><td><?php echo Lang::TEAM; ?> 2</td><td><input name="team2" type="text" maxlength="30" size="30" /></td></tr>
	<tr><td><?php echo Lang::REPLAY; ?></td><td><input name="fichier" type="file" /></td></tr>
	<tr><td colspan="2" height="15"><input type="hidden" name="posted" value="1" /></td></tr>
	<tr><td colspan="2" align="center"><input type="submit" value="<?php echo Lang::UPLOAD; ?>" /></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">
		<span class="info">
			<?php echo sprintf(Lang::FILE_REQUIREMENTS, round($max_size / (1024*1024), 2)); ?>
		</span>
	</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">
<?php
	echo $msg;
?>
	</td></tr>
</table>
</form>
<?php
	ArghPanel::end_tag();
?>