<?
	ArghSession::exit_if_not_logged();
	include 'FCKeditor/fckeditor.php';
	
	ArghPanel::begin_tag(Lang::MESSAGE_EDITION);
	
	$req = "SELECT * FROM lg_comment WHERE id='".(int)$_GET['id']."'";
	$t=mysql_query($req);
	while ($l = mysql_fetch_object($t)) {
		if (ArghSession::get_username() == $l->poster 
		    OR ArghSession::is_rights(
				array(
					RightsMode::NEWS_HEADADMIN,
					RightsMode::NEWS_NEWSER
				)
			)) {
			echo '<form method="POST" action="?f=news_comment_save" onSubmit="boutonEnvoi.disabled=true;"><center>';
				$oFCKeditor = new FCKeditor('FCKeditor3');
				$oFCKeditor->BasePath = '/ligue/FCKeditor/';
				$oFCKeditor->Value = $l->comment;
				$oFCKeditor->ToolbarSet = 'Basic';
				$oFCKeditor->Width = '100%';
				$oFCKeditor->Height = '200';
				$oFCKeditor->Create();
			echo '
			<input type="hidden" name="news_id" value="'.$l->news_id.'" />
			<input type="hidden" name="id" value="'.(int)$_GET['id'].'" />
			<input type="submit" value="Valider" name="boutonEnvoi" />
			</form>';
		} else {
			echo Lang::AUTHORIZATION_REQUIRED;
		}
	}
	
	ArghPanel::end_tag();
?>