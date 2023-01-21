<?php
	abstract class Theme {
	
		const DEFAULT_THEME = 'classic';
	
		public static $THEMES = array(
			'purple' => 'Amethyst',
			
			'default' => 'Blackout',
			'red' => 'Blood',
			Theme::DEFAULT_THEME => 'Classic',
			'blue' => 'Cobalt',
			'orange' => 'Desert',
			'white' => 'Frost (beta)',
			'yellow' => 'Lemon',
			'green' => 'Nature',
			'pink' => 'Pink',
		);
		
		
		public static function initialize_theme() {
			$theme = Theme::DEFAULT_THEME;
			if (ArghSession::is_gold()) {
				$theme = ArghSession::get_theme();
				if (!array_key_exists($theme, Theme::$THEMES)) $theme = 'default';
			}

			echo '<link rel="stylesheet" href="themes/'.$theme.'/vader/jquery-ui-1.7.2.custom.css" type="text/css" media="screen">
				<link rel="stylesheet" href="themes/'.$theme.'/boxes.css" type="text/css">
				<link rel="stylesheet" href="themes/'.$theme.'/default.css" type="text/css">
				<link rel="stylesheet" href="themes/'.$theme.'/listings.css" type="text/css">
				<link rel="stylesheet" href="themes/vip.css" type="text/css">';
		}
	}
?>