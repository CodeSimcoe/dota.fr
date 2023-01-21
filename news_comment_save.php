<?php
	ArghSession::exit_if_not_logged();
	ArghPanel::begin_tag(Lang::MESSAGE_EDITION);

	if (!isset($_POST['id'])) exit();
	
	$req = "UPDATE lg_comment
			SET comment = '".mysql_real_escape_string($_POST['FCKeditor3'])."', edit_date = '".time()."'
			WHERE id = '".(int)$_POST['id']."'";
	$ins = mysql_query($req);
	
	echo '<center>'.Lang::MESSAGE_SUCCESSFULLY_EDITED.'<br />';
	echo '<a href=?f=news&id='.(int)$_POST['news_id'].'>'.Lang::GO_BACK.'</a></center>';

	ArghPanel::end_tag();
?>