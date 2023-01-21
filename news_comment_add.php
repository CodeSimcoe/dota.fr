<?php
	ArghPanel::begin_tag(Lang::MESSAGE_ADDING);

	if (!ArghSession::is_logged()) {
		clean_exit(Lang::LOGGING_REQUIRED);
    }
	if (!isset($_POST['FCKeditor2'])) {
		clean_exit(Lang::ERROR_EMPTY_MESSAGE);
	}
	
	/*
	function getStart($posts) {
		if ($posts == 0) {
			return 1;
		}
		if ($posts%10 == 0) {
			return $posts - 9;
		} else {
			return $posts - $posts%10 + 1;
		}
	}
	*/
	
	$postId = (int) $_POST['id'];
	
	$req = "SELECT count(*)
			FROM lg_comment
			WHERE news_id = '".$postId."'";
	$t = mysql_query($req);
	while ($l = mysql_fetch_row($t)) {
		$post = $l[0] + 1;
	}
	
	$texte = eregi_replace("<script[^>]*>(.|\n)*script>(\r\n)?", "", $_POST['FCKeditor2']);
	$req = "INSERT INTO lg_comment (poster, news_id, post_id, comment, post_date) VALUES ('".ArghSession::get_username()."', '".$postId."', '".$post."', '".$texte."', '".time()."')";
	$ins = mysql_query($req);
	
	
	//Gold
	//Date News
	$req2 = "SELECT daten FROM lg_newsmod WHERE id = '".$postId."'";
	$t = mysql_query($req2);
	$l = mysql_fetch_row($t);
	
	//Maximum 3 posts donnent du gold
	$req2 = "SELECT count(*) FROM lg_comment WHERE news_id = '".$postId."' AND poster = '".$_SESSION['username']."'";
	$t2 = mysql_query($req2);
	$l2 = mysql_fetch_row($t2);
	
	//Validité du gain de gold: 15j après derniere update de la news
	//$j15 = 15*24*3600;
	$j15 = 1296000;
	if (time() - $l[0] < $j15 and $l2[0] <= 3) {
		addGold(ArghSession::get_username(), 5);
	}
	
	$sreq = "SELECT * FROM lg_comment WHERE news_id='".$postId."'";
	$st = mysql_query($sreq);
	$posts = mysql_num_rows($st);
	
	echo '<center>'.Lang::MESSAGE_ADDED.'<br />
		<a href="?f=news&id='.$postId.'&start='.getStart($posts).'#comment">'.Lang::GO_BACK.'</a></center>';
	ArghPanel::end_tag();
?>