<?php
class ArghDatabase {
	
	public static function get_scalar($table, $field, $condition_field, $condition_value) {
		$req = "SELECT `$field` FROM `$table` WHERE `$condition_field` = '$condition_value'";
	}
}
?>