<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN
		)
	);
	
	//RegExps
	$regexp_version = '`^[0-9]\.[0-9]{1,2}[a-z]?$`';
	$regexp_mode = '`^-[0-9a-z]{2,4}$`';
	$regexp_w3_version = '`^[0-9]\.[0-9]{1,2}[a-z]?$`';

	ArghPanel::begin_tag(Lang::LADDER_VERSION);
	$file = CacheManager::LADDER_VERSION_CACHE;
	
	if (isset($_POST['version'])) {
		if (preg_match($regexp_version, $_POST['version'])
			&& preg_match($regexp_mode, $_POST['mode_odd'])
			&& preg_match($regexp_mode, $_POST['mode_even'])
			&& preg_match($regexp_w3_version, $_POST['w3_version'])) {
			
			CacheManager::write_ladder_version($_POST['version']);
			CacheManager::write_ladder_mode_odd($_POST['mode_odd']);
			CacheManager::write_ladder_mode_even($_POST['mode_even']);
			CacheManager::write_w3_version($_POST['w3_version']);
			
			$al = new AdminLog(sprintf(Lang::ADMIN_LOG_LADDER_VERSION_MODIFIED, $_POST['version'], $_POST['mode_odd'].'/'.$_POST['mode_even'], $_POST['w3_version']), AdminLog::TYPE_LADDER);
			$al->save_log();
			
			echo '<center><span class="win">'.Lang::MODIFICATIONS_SAVED.'</span></center><br />';
		} else {
			echo '<center><span class="lose">'.Lang::ERROR_IN_INPUT_PARAMETERS.'</span></center><br />';
		}
	}
	
	$version = CacheManager::get_ladder_version();
	$mode_odd = CacheManager::get_ladder_mode_odd();
	$mode_even = CacheManager::get_ladder_mode_even();
	$w3_version = CacheManager::get_w3_version();
?>
	<form method="POST" action="?f=admin_ladder_version">
		<center>
			<span class="win"><b><?php echo Lang::VERSION; ?> </b></span> 
			<input type="text" value="<?php echo $version; ?>" name="version" size="4" />&nbsp;
			<span class="win"><b><?php echo Lang::MODE; ?> </b></span> 
			<input type="text" value="<?php echo $mode_odd; ?>" name="mode_odd" size="4" />/<input type="text" value="<?php echo $mode_even; ?>" name="mode_even" size="4" />
			<span class="win"><b><?php echo Lang::W3_VERSION; ?> </b></span> 
			<input type="text" value="<?php echo $w3_version; ?>" name="w3_version" size="4" />
			<br /><br />
			<input type="submit" value="<?php echo Lang::VALIDATE; ?>" style="width: 150px;" />
		</center>
	</form>
<?php
	ArghPanel::end_tag();
?>