<?php
/*
<script language="javascript">
	function nuke_msg(msg_id) {
		var fRet;
		fRet = confirm('<?php echo Lang::MESSAGE_NUKE; ?>');
		if (fRet) {
			window.open('ajax/nuke_msg.php?msg=' + msg_id + '&type=news', 'Nuke', 'HEIGHT=150,resizable=no,scrollbars=no,WIDTH=400');
		}
	}
		
	function show_comments(news_id, start) {
		$('#comments').html('<center><img src="img/ajax-loader.gif"><br /><?php echo Lang::LOADING; ?></center>');
		
		$.get('ajax/get_news_comments.php',
			{
				id: news_id,
				start: start
			},
			function(data) {
				$('#comments').html(data);
		});
	}

	function add_quote(nick, text) {
		var oEditor = FCKeditorAPI.GetInstance('FCKeditor2');
		oEditor.InsertHtml('<b>' + nick + ' <?php echo Lang::WROTE; ?>:</b><div class="quote">' + unescape(text) + '</div><br />');
		oEditor.Focus();
	}
</script>
*/

	include 'FCKeditor/fckeditor.php';
	
	$news_id = (int)$_GET['id'];
	
	$news = new News();
	
	if ($news->load($news_id)) {
		//OK, la news existe
		if (!$news->is_shown()) {
			//News masquee ?
			if (!ArghSession::is_rights(array(RightsMode::NEWS_HEADADMIN, RightsMode::NEWS_NEWSER))) {
				ArghPanel::info_panel(Lang::NEWS_MASKED);
				exit;
			}
			if (ArghSession::is_rights(array(RightsMode::NEWS_HEADADMIN, RightsMode::NEWS_NEWSER))) {
				ArghPanel::info_panel(Lang::NEWS_MASKED_NEWSER_MESSAGE);
			}
		}
		
		$news->increment_views();
		$news->show();
		
		//$news->get_messages();
		$name = 'cknews';
		
		//lock comments for normal users if necessary
		$can_add_message = !$news->_comments_locked || ArghSession::is_rights(array(RightsMode::NEWS_HEADADMIN, RightsMode::NEWS_NEWSER));
		
		$messager = new Messager($name, Tables::NEWS_MESSAGES, $news->_id, $can_add_message);
		$messager->deploy();
		
		/*
		GenericMessageManager::display_messages($name, $news->_messages);
		GenericMessageManager::display_message_adding_box($name, Tables::NEWS_MESSAGES, $news->_id);
		*/
		
		/*
		
		ArghPanel::begin_tag(Lang::COMMENTS);
		
		echo '<div id="comments">';
		include 'ajax/get_news_comments.php';
		echo '</div>';
		
		ArghPanel::end_tag();
		*/

		/*
		ArghPanel::begin_tag(Lang::MESSAGE_ADDING);
		
		echo '<center>';

		if (ArghSession::is_logged()) {
			echo '<form method="POST" action="?f=news_comment_add" onSubmit="boutonEnvoi.disabled=true;"><tr><td>';
			$oFCKeditor = new FCKeditor('FCKeditor2') ;
			$oFCKeditor->BasePath = '/ligue/FCKeditor/';
			$oFCKeditor->ToolbarSet = 'Basic';
			$oFCKeditor->Width = '100%';
			$oFCKeditor->Height = '200';
			$oFCKeditor->Create();	
			echo '<input type="hidden" name="id" value="'.$news_id.'" />
			<input type="submit" value="'.Lang::VALIDATE.'" name="boutonEnvoi" /></form>';
		} else {
			echo '<center>'.Lang::MESSAGE_MUST_BE_LOGGED.'</center>';
		}
		
		echo '</center>';
		ArghPanel::end_tag();
		*/
		
		
	} else {
		//KO, news non existante ou supprimee
		ArghPanel::error_panel(Lang::NEWS_DOESNT_EXIST);
	}
?>