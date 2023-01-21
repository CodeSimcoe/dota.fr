<script language="javascript">
$(document).ready(function() {
	$('.banner').click(function() {
		$('#argh_banner').attr('src', $(this).attr('src'));
	});
});
</script>
<?php
	
	ArghSession::exit_if_not_rights(
		RightsMode::WEBMASTER
	);
	
	$target = 'img/banners/';
	
	//DELETE
	if (isset($_GET['delete'])) {
		$banner_id = (int)$_GET['id'];
		
		$query = "DELETE FROM lg_banners WHERE id = '".$banner_id."'";
		mysql_query($query) or die(mysql_error());
		unlink($target.$banner_id.'.jpg');
	}
	
	//INSERT
	$max_weight = 512000;
	$max_weight_ko = 500;
	$width = 1000;
	$height = 176;

	$nom_file = $_FILES['banner_file']['name'];
	$taille = $_FILES['banner_file']['size'];
	$tmp = $_FILES['banner_file']['tmp_name'];
	$type = strrchr($_FILES['banner_file']['name'], '.');
	$allowedExtension = '.jpg';

	if (!empty($_POST['posted'])) {
	    if (!empty($_FILES['banner_file']['name'])) {
	        //if (in_array($type, $allowedExtensions)) {
	        if ($type == $allowedExtension) {
	            $infos_img = getimagesize($tmp);
	            if ($infos_img[0] == $width && $infos_img[1] == $height && $_FILES['banner_file']['size'] <= $max_weight) {
					
					$query = "INSERT INTO lg_banners (
								author,
								name,
								season
							) VALUES (
								'".mysql_real_escape_string($_POST['banner_author'])."',
								'".mysql_real_escape_string($_POST['banner_name'])."',
								'".(int)$_POST['banner_season']."'
							)";
					mysql_query($query);
					
					$banner_id = mysql_insert_id();
					$banner = $banner_id.$type;
					
	                if (!move_uploaded_file($_FILES['banner_file']['tmp_name'], $target.$banner)) {
	                    $msg = Lang::FILE_UPLOAD_ERROR.' : '.$_FILES['banner_file']['error'];
						
						$query = "DELETE FROM lg_banners WHERE id = '".$banner_id."'";
						mysql_query($query) or die(mysql_error());
	                }
	            } else {
					$msg = sprintf(Lang::FILE_DIMENSIONS_ERROR, $width, $height, $max_weight_ko);
	            }
	        } else {
	        	$msg = Lang::FILE_EXTENSION_ERROR_JPEG_ONLY;
	        }
	    } else {
	    	$msg = Lang::EMPTY_FORM;
	    }
	}
	
	ArghPanel::begin_tag(Lang::BANNER_MANAGEMENT);
?>

<table class="listing">
	<colgroup>
		<col width="20%" />
		<col width="42%" />
		<col width="10%" />
		<col width="20%" />
		<col width="8%" />
	</colgroup>
	<thead>
		<tr>
			<th><?php echo Lang::AUTHOR; ?></th>
			<th><?php echo Lang::BANNER; ?></th>
			<th><?php echo Lang::SEASON; ?></th>
			<th><?php echo Lang::NAME; ?></th>
			<th><center><?php echo Lang::ACTION; ?></center></th>
		</tr>
	</thead>
	<tbody>
<?php
	$query = 'SELECT * FROM lg_banners ORDER BY season DESC, id DESC';
	$result = mysql_query($query) or die(mysql_error());
	$i = 0;
	while ($banner = mysql_fetch_object($result)) {
		echo '<tr'.Alternator::get_alternation($i).'>
			<td>'.$banner->author.'</td>
			<td><img src="img/banners/'.$banner->id.'.jpg" title="id = '.$banner->id.'" alt="'.$banner->id.'" width="240" class="banner" style="cursor: pointer;" /></td>
			<td align="center">'.$banner->season.'</td>
			<td>'.$banner->name.'</td>
			<td align="center"><a href="?f=admin_banners&delete=1&id='.$banner->id.'"><img src="img/icon_post_delete.gif" alt="" /></a></td>
		</tr>';
	}
?>
	</tbody>
</table>

<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag(Lang::BANNER_MANAGEMENT);

	if (!empty($msg)) {
		echo '<center><span class="lose">'.$msg.'</span></center><br />';
	}
	
?>
<form enctype="multipart/form-data" action="?f=admin_banners" method="POST">
<center>
	<table>
		<colgroup>
			<col width="25%" />
			<col width="50%" />
		</colgroup>
		<tr>
			<td><?php echo Lang::AUTHOR; ?></td>
			<td><input type="text" name="banner_author" value="" /></td>
		</tr>
		<tr>
			<td><?php echo Lang::NAME; ?></td>
			<td><input type="text" name="banner_name" value="" /></td>
		</tr>
		<tr>
			<td><?php echo Lang::SEASON; ?></td>
			<td><input type="text" name="banner_season" value="" /></td>
		</tr>
	</table>
	
		<input type="hidden" name="posted" value="1" />
		<input name="banner_file" type="file" />
		<input type="submit" value="<?php echo Lang::UPLOAD; ?>" style="width: 150px;" />
	<br />
	<?php echo Lang::BANNER; ?> 1000 x 176 - <?php echo $max_weight_ko.' '.Lang::KILO_BYTES; ?>
</center>
</form>
<?php
	ArghPanel::end_tag();
?>