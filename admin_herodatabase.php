<?php
	ArghSession::exit_if_not_rights(
		RightsMode::WEBMASTER
	);
	
	ArghPanel::begin_tag(Lang::HERO_ADDING);
	
	$target = 'img/heroes/';
	
	//DELETE
	if (isset($_GET['delete'])) {
		
		$query = "SELECT hero FROM lg_heroes WHERE hero = '".mysql_real_escape_string($_GET['hero'])."'";
		$result = mysql_query($query) or die(mysql_error());
		
		//Par precaution pour le unlink
		if (mysql_num_rows($result)) {
			$hero = mysql_fetch_row($result);
			$query = "DELETE FROM lg_heroes WHERE hero = '".$hero[0]."'";
			mysql_query($query) or die(mysql_error());
			unlink($target.$hero[0].'.gif');
		}
	}
	
	//INSERT
	$max_weight = 10240;
	$max_weight_ko = 10;
	$width = 64;
	$height = 64;

	$nom_file = $_FILES['hero_file']['name'];
	$taille = $_FILES['hero_file']['size'];
	$tmp = $_FILES['hero_file']['tmp_name'];
	$type = strrchr($_FILES['hero_file']['name'], '.');
	$allowedExtension = '.gif';

	if (!empty($_POST['posted'])) {
	    if (!empty($_FILES['hero_file']['name'])) {
	        if ($type == $allowedExtension && preg_match('`^[a-zA-Z0-9 ]+$`', $_POST['hero_name'])) {
	            $infos_img = getimagesize($tmp);
	            if ($infos_img[0] == $width && $infos_img[1] == $height && $_FILES['hero_file']['size'] <= $max_weight) {
				
					$hero = $_POST['hero_name'].$type;
					
	                if (move_uploaded_file($_FILES['hero_file']['tmp_name'], $target.$hero)) {
						$query = "INSERT INTO lg_heroes (
									hero,
									main_attribute,
									affiliation
								) VALUES (
									'".mysql_real_escape_string($_POST['hero_name'])."',
									'".$_POST['hero_type']."'
									'".$_POST['hero_affiliation']."'
								)";
						mysql_query($query);
						
					} else {
	                    $msg = Lang::FILE_UPLOAD_ERROR.' : '.$_FILES['hero_file']['error'];
	                }
	            } else {
					$msg = sprintf(Lang::FILE_DIMENSIONS_ERROR, $width, $height, $max_weight_ko);
	            }
	        } else {
	        	$msg = Lang::FILE_EXTENSION_ERROR_GIF_ONLY;
	        }
	    } else {
	    	$msg = Lang::EMPTY_FORM;
	    }
	}
	
	if (!empty($msg)) {
		echo '<center><span class="lose">'.$msg.'</span></center><br />';
	}
?>
<form enctype="multipart/form-data" action="?f=admin_herodatabase" method="POST">
	<table class="listing">
		<colgroup>
			<col width="20%" />
			<col width="80%" />
		</colgroup>
		<tr>
			<td><?php echo Lang::NAME; ?></td>
			<td><input type="texte" name="hero_name" value="" /></td>
		</tr>
		<tr>
			<td><?php echo Lang::TYPE; ?></td>
			<td>
				<select name="hero_type" style="height: 25px;">
				<?php
					$attributes = array(
						'str' => Lang::STRENGTH,
						'agi' => Lang::AGILITY,
						'int' => Lang::INTELLIGENCE
					);
					
					foreach ($attributes as $key => $val) {
						echo '<option value="'.$key.'" style="height: 25px; background: no-repeat url(\'img/attributes/'.$key.'.jpg\'); padding-left: 38px; padding-top: 10px;">'.$val.'</option>';
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo Lang::AFFILIATION; ?></td>
			<td>
				<select name="hero_affiliation" style="height: 25px;">
				<?php
					$affiliations = array(
						'se' => Lang::SENTINEL,
						'ne' => Lang::NEUTRAL,
						'sc' => Lang::SCOURGE
					);
					
					foreach ($affiliations as $key => $val) {
						echo '<option value="'.$key.'">'.$val.'</option>';
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo Lang::PICTURE; ?></td>
			<td><input name="hero_file" type="file" /></td>
		</tr>
	</table>
	<center>
		<br /><input type="submit" name="posted" value="<?php echo Lang::VALIDATE; ?>" style="width: 200px;" />
		<br /><br /><?php echo Lang::PICTURE; ?> 64 x 64 - <?php echo $max_weight_ko.' '.Lang::KILO_BYTES; ?>
	</center>
</form>
<?php
	ArghPanel::end_tag();
	
	
	ArghPanel::begin_tag(Lang::HERO_DATABASE);
?>
	<table class="listing">
		<colgroup>
			<col width="25%" />
			<col width="15%" />
			<col width="15%" />
			<col width="15%" />
			<col width="20%" />
		</colgroup>
		<thead>
			<tr>
				<th><?php echo Lang::NAME; ?></th>
				<th><?php echo Lang::PICTURE; ?></th>
				<th><?php echo Lang::TYPE; ?></th>
				<th><?php echo Lang::AFFILIATION; ?></th>
				<th><?php echo Lang::ACTION; ?></th>
			</tr>
		</thead>
		<tbody>
<?php
		$query = "SELECT * FROM lg_heroes ORDER BY hero ASC";
		$result = mysql_query($query);
		$i = 0;
		while ($hero = mysql_fetch_object($result)) {
			echo '<tr'.Alternator::get_alternation($i).'>
					<td>'.$hero->hero.'</td>
					<td><img src="img/heroes/'.$hero->hero.'.gif" width="48" height="48" alt="'.$hero->hero.'" /></td>
					<td><img src="img/attributes/'.$hero->main_attribute.'.jpg" alt="" /></td>
					<td>'.$affiliations[$hero->affiliation].'</td>
					<td>
						<a href="?f=admin_herodatabase&hero='.$hero->hero.'"><img src="img/icons/pencil.png" alt="" /></a>
						&nbsp;
						<a href="?f=admin_herodatabase&delete=1&hero='.$hero->hero.'"><img src="img/icons/cross.png" alt="" /></a>
					</td>
					
					
					
				</tr>';
		}
?>
		</tbody>
	</table>

<?php
	ArghPanel::end_tag();
?>