<?php
	$id = (int)$_GET['id'];
?>
<script language="javascript">
	function stars() {
		$('#stars_img').attr('src', '/ligue/img/' + $('#rating').val() + '_stars.png');
	}
	
	function vote(rating) {
		$.get('ajax/screenshots_rate.php',
			{
				id: '<?php echo $id; ?>',
				rating: $('#rating').val()
			},
			function(data) {
				$('#voting').html('');
			}
		);
	}
</script>
<?php
	require 'classes/ScreenshotModule.php';
	//require 'classes/GenericMessageManager.php';
	
	ArghPanel::begin_tag(Lang::SCREENSHOT);
	
	if (isset($_POST['message']) && ArghSession::is_logged()) {
		$msg = new GenericMessage(Tables::SCREENSHOT_MESSAGES);
		$msg->_author = ArghSession::get_username();
		$msg->_message = $_POST['message'];
		$msg->_reference_id = $id;
		
		$msg->save();
	}
	
	$ss = new Screenshot();
	if ($ss->load_screenshot($id, true)) {
	
		//Screenshot
		echo '<b>"'.$ss->_name.'"</b>&nbsp;';
		echo Lang::BY.'&nbsp;<a href="?f=player_profile">'.$ss->_uploader.'</a> '.Lang::POSTED_ON.'&nbsp;'.date(Lang::DATE_FORMAT_HOUR, $ss->_date_upload).'<br /><br />';
		echo Lang::KEYWORDS.':<br /><ul><li>'.implode("</li><li>", $ss->_keywords).'<br /><br />';
		echo '</li></ul>'.Lang::HEROES_INVOLVED.':<br /><ul>';
		foreach ($ss->_heroes as $hero) {
			echo '<li><img src="img/heroes/'.$hero.'.gif" width="24" height="24" alt="" />&nbsp;'.$hero.'</li>';
		}
		echo '</ul><center>'; $ss->display_medium_thumbnail(); echo '</center><br /><br />';
		
		//Rating
		echo Lang::RATING.': '.($ss->_rating > 0 ? $ss->rating_to_stars() : '-').' ('.$ss->_ratings.' '.($ss->_ratings > 1 ? Lang::VOTES : Lang::VOTE).')';
		
		if (ArghSession::is_logged()) {
			if ($ss->user_has_voted(ArghSession::get_username())) {
				echo ' - '.Lang::ALREADY_VOTED;
			} else {
				echo '<span id="voting">&nbsp; - '.Lang::TO_VOTE.':
					<select id="rating" onChange="stars();">';
				for ($i = 5; $i >= 1; $i--) {
					echo '<option value="'.$i.'">'.$i.'</option>';
				}
				echo '</select>&nbsp;<input type="button" value="'.Lang::OK.'" onClick="vote();" />&nbsp;<img id="stars_img" src="/ligue/img/5_stars.png" /></span>';
			}
		}
		
		echo '<br />';
		
		//Messages
		ArghPanel::end_tag();
		
		$name = 'ckscreen';
		$messager = new Messager($name, Tables::SCREENSHOT_MESSAGES, $id);
		$messager->deploy();
		
		/*
		GenericMessageManager::display_messages($name, $ss->_messages);
		GenericMessageManager::display_message_adding_box($name, Tables::SCREENSHOT_MESSAGES, $id);
		*/
		
	} else {
		echo '<center><span class="lose">'.Lang::ERROR.'</span></center>';
		ArghPanel::end_tag();
	}
?>