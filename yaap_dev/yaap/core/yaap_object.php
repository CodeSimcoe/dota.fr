<?php

abstract class YaapObject {

	public function __set($name, $value) {
		if (strpos($name, '->') !== false) eval('$this->'.$name.' = $value;');
	}

}

?>