<?php
	ArghPanel::begin_tag(Lang::FILE_UPLOAD);

	//Genere un string aleatoire de x caracteres
	function generate() {
		$pattern = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		for ($i = 0; $i < 5; $i++) {
				$code .= $pattern[rand(0, strlen($pattern) - 1)];
		}
		return $code;
	}

	//----------------------------
	//  DEFINITION DES VARIABLES 
	//----------------------------

	$target     = 'match_files/';
	$max_size	= 4194304; //4 * 1024 * 1024 (2 Mo)
	$allowed_extensions = array('w3g', 'jpg');

	//---------------------------------------------
	//  DEFINITION DES VARIABLES LIEES AU FICHIER
	//---------------------------------------------

	$nom_fichier = $_FILES['fichier']['name'];
	$taille = $_FILES['fichier']['size'];
	$tmp = $_FILES['fichier']['tmp_name'];
	$type = strrchr($_FILES['fichier']['name'], '.');
	$type = substr($type, 1);

	if (!empty($_POST['go'])) {
	    	if (!empty($_FILES['fichier']['name'])) {
	        	//if ($type == 'w3g' or $type == 'jpg') {
	        	if (in_array($type, $allowed_extensions)) {
	            	if($_FILES['fichier']['size'] <= $max_size) {
						if ($type == 'w3g') {
							$nom = 'replay';
						} else {
							$nom = 'screen';
						}
						//Nom sans le . et l'extension
						$com = str_replace('#','', addslashes(substr($_FILES['fichier']['name'], 0, strlen($_FILES['fichier']['name'])-4)));
						$nom .= '_'.$com.'_'.generate().'.'.$type;
						$nom = str_replace(' ', '_', $nom);
	                	if (move_uploaded_file($_FILES['fichier']['tmp_name'], $target.$nom)) {
							$req = "INSERT INTO lg_uploads (qui_upload, date_upload, match_id, fichier, comment)
									VALUES ('".ArghSession::get_username()."', '".time()."', '".(int)$_POST['match_id']."', '".$nom."', '".$com."')";
							mysql_query($req);
	                    	echo '<b>'.Lang::FILE_SUCCESSFULLY_UPLOADED.' !</b>';
	                    	echo '<b>'.Lang::FILE.' :</b> '.$_FILES['fichier']['name'];
	                    	echo '<b>'.Lang::WEIGHT.' :</b> '.((int)$_FILES['fichier']['size'] / 1024).' '.Lang::KILO_BYTES;
	                    	echo '<center><a href="?f=match&team1='.$_POST['team1'].'&team2='.$_POST['team2'].'">'.Lang::GO_BACK.'</a></center>';
						} else {
							//echo $target.$nom;
							$uploadErrors = array(
							    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
							    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
							    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
							    UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
							    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
							    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
							    UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
							);
	                    	echo '<center>'.Lang::FILE_UPLOAD_ERROR.'</center>
								<center><span class="lose">Erreur: ', $uploadErrors[$_FILES['fichier']['error']], '</span></center>';
						}
					} else {
						echo '<center>'.Lang::FILE_MAX_WEIGHT_EXCEEDED.'</center>';
					}
	        } else {
	            echo '<center>'.Lang::FILE_EXTENSION_ERROR.'</center>';
	        }
	    } else {
	        echo '<center>'.Lang::EMPTY_FORM.'</center>';
	    }
	}/* else {
		echo '<center>Aucune action effectu&eacute;e.</center>';
	}*/
	
	ArghPanel::end_tag();
?>