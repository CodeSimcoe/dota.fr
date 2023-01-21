<?php
	ArghSession::exit_if_not_rights(
		array(
			RightsMode::NEWS_HEADADMIN, 
			RightsMode::NEWS_NEWSER 
		)
	);
	
	ArghPanel::begin_tag(Lang::NEWS_ADDING);
	echo '<center>';
	
	//Arguments passés par POST
	$titre = addslashes($_POST['titre']);
	$cat = (int)$_POST['cat'];
	$aff = (int)$_POST['aff'];
	$texte = addslashes($_POST['ckeditor']);
	$time = time();
	$news_id = (int)$_POST['edit'];
	$lock = (int)$_POST['lock'];
	$author_lock = (int)$_POST['author_lock'];
	
	if (!isset($_POST['edit'])) {
		$req = "INSERT INTO lg_newsmod (poster, daten, titre, categorie, afficher, texte, comments_locked, author_lock)
				VALUES ('".ArghSession::get_username()."', '".$time."', '".$titre."', '".$cat."', '".$aff."', '".$texte."', '".$lock."', '".$author_lock."')";
		mysql_query($req);
		$id = mysql_insert_id();
		
		//Admin Log
		$al = new AdminLog(sprintf(Lang::ADMIN_LOG_NEWS_ADDED, $id), AdminLog::TYPE_NEWS);
		$al->save_log();
		
		echo Lang::NEWS_SUCCESSFULLY_ADDED.'<br /><br /><a href="?f=news&id='.$id.'">'.Lang::NEWS_GO_TO.'</a> - <a href="?f=admin_news_list">'.Lang::NEWS_BACK_TO_MODULE.'</a>';
	} else {
		if ($_POST['bump'] == 1) {
			$req = "UPDATE lg_newsmod
					SET daten='".$time."', titre='".$titre."', categorie='".$cat."', afficher='".$aff."', texte='".$texte."', comments_locked='".$lock."', author_lock='".$author_lock."'
					WHERE id='".$news_id."'";
		} else {
			$req = "UPDATE lg_newsmod
					SET titre='".$titre."', categorie='".$cat."', afficher='".$aff."', texte='".$texte."', comments_locked='".$lock."', author_lock='".$author_lock."'
					WHERE id='".$news_id."'";
		}
		mysql_query($req);
		echo Lang::NEWS_SUCCESSFULLY_UPDATED.'<br /><br /><a href="?f=news&id='.$news_id.'">'.Lang::NEWS_GO_TO.'</a> - <a href="?f=admin_news_list">'.Lang::NEWS_BACK_TO_MODULE.'</a>';
		
		//Admin Log
		$al = new AdminLog(sprintf(Lang::ADMIN_LOG_NEWS_UPDATED, $news_id), AdminLog::TYPE_NEWS);
		$al->save_log();
	}
	
	echo '</center>';
	ArghPanel::end_tag();

?>
