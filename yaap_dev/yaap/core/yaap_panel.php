<?php

require_once YAAP_PATH.'core\yaap_panel_mode.php';

abstract class YaapPanel {

	public static function begin_tag($title = '', $id = null) {
		echo self::str_begin_tag($title, $id);
	}

	public static function end_tag($mode = YaapPanelMode::NORMAL) {
		echo self::str_end_tag($mode);
	}
	
	public static function str_begin_tag($title, $id = null) {
		return '<div class="boxes"'.((!empty($id)) ? 'id="'.$id.'"' : '' ).'><div class="title"><p><b>'.$title.'</b></p></div><div class="arghcontent"><br />';
	}
	
	public static function str_end_tag($mode = YaapPanelMode::NORMAL) {
		return '<br /></div><div class="footer"><p class="'.$mode.'"><b>&nbsp;</b></p></div></div>';
	}
	
	public static function info_panel($message) {
		self::begin_tag(Lang::INFORMATION);
		echo '<center>'.$message.'</center>';
		self::end_tag();
	}
	
}
?>