<?php
ArghSession::exit_if_not_logged();
if (ArghSession::get_clan_rank() >= 3) {
	exit(Lang::AUTHORIZATION_REQUIRED);
}
ArghPanel::begin_tag(Lang::LOGO_MANAGEMENT);
		
//----------------------------
//  DEFINITION DES VARIABLES 
//----------------------------

$target     = 'upload/teams';
$max_size   = 200 * 1024;
$width_max  = 300;
$height_max = 150;

//---------------------------------------------
//  DEFINITION DES VARIABLES LIEES AU FICHIER
//---------------------------------------------

$nom_file   = $_FILES['fichier']['name'];
$taille     = $_FILES['fichier']['size'];
$tmp        = $_FILES['fichier']['tmp_name'];
$type = strrchr($_FILES['fichier']['name'], '.');
$allowedExtensions = array('.jpg', '.gif', '.png');

//----------------------
//  SCRIPT D'UPLOAD
//----------------------
if (!empty($_POST['posted'])) {
    if (!empty($_FILES['fichier']['name'])) {
        if (in_array($type, $allowedExtensions)) {
            $infos_img = getimagesize($tmp);
            if (($infos_img[0] <= $width_max) and ($infos_img[1] <= $height_max) and ($_FILES['fichier']['size'] <= $max_size)) {
				$img = ArghSession::get_clan().'.'.$type;
                if (move_uploaded_file($_FILES['fichier']['tmp_name'], $target.$img)) {
					$upd = "UPDATE lg_clans SET logo = '".$target.$img."' WHERE id = '".ArghSession::get_clan()."'";
					mysql_query($upd);
                    echo '<span class="win"><b>'.Lang::AVATAR_SUCCESSFULLY_UPLOADED.' !</b></span>';
                    echo '<b>'.Lang::FILE.' :</b> '.$_FILES['fichier']['name'];
                    echo '<b>'.Lang::SIZE.' :</b> '.round($_FILES['fichier']['size'] / 1024).' '.Lang::KILO_BYTES.'';
                    echo '<b>'.Lang::WIDTH.' :</b> '.$infos_img[0].' px';
                    echo '<b>'.Lang::HEIGHT.' :</b> '.$infos_img[1].' px';
                } else {
                    echo '<center><span class="lose">'.Lang::AVATAR_UPLOAD_ERROR.' : '.$_FILES['fichier']['error'].'</span></center>';
                }
            } else {
				$maxsz = (int)round($max_size / 1024);
				echo '<center><span class="lose">'.sprintf(Lang::AVATAR_DIMENSIONS_ERROR, $width_max, $height_max, $maxsz).'</span></center>';
            }
        } else {
        	echo '<center><span class="lose">'.Lang::AVATAR_EXTENSION_ERROR.'</span></center>';
        }
    } else {
    	echo '<center><span class="lose">'.Lang::EMPTY_FORM.'</span></center>';
    }
}
?>
	<center>
		<form enctype="multipart/form-data" action="?f=team_logo" method="POST">
			<input type="hidden" name="posted" value="1" />
			<input name="fichier" type="file" />
			<input type="submit" value="<?php echo Lang::UPLOAD; ?>" />
		</form>
		<br />
		<?php echo sprintf(Lang::AVATAR_REQUIREMENTS, $width_max, $height_max); ?>
	</center>
<?php
	ArghPanel::end_tag();
?>