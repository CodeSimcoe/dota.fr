<?php

abstract class YaapFactory {

	public static function get_module($module_name) {
		return new $module_name();
	}

}

?>