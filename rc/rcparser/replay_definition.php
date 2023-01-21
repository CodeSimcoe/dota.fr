<?php

	class ReplayDefinition {
		var $version, $exists;
		var $heroes, $abilities, $items;
		function ReplayDefinition($version) {
			$this->version = $version;
			$this->heroes = array();
			$this->abilities = array();
			$this->items = array();
			$this->load();
		}
		function load() {
			$this->exists = file_exists('rc/rcparser/'.$this->version.'.txt');
			if ($this->exists) {
				$content = file('rc/rcparser/'.$this->version.'.txt');
				foreach ($content as $val) {
					$line = explode('$$', $val);
					$code = utf8_decode($line[0]);
					if ($line[1]{0} == 'h') {
						$this->heroes[$code]['code'] = $code;
						$this->heroes[$code]['hero'] = preg_replace('/(\n|\r)/', '', utf8_decode(substr($line[1], 2)));
					} else if ($line[1]{0} == 'a') {
						$line = explode(':', $line[1]);
						$this->abilities[$code]['code'] = $code;
						$this->abilities[$code]['hero'] = preg_replace('/(\n|\r)/', '', utf8_decode(substr($line[0], 2)));
						$this->abilities[$code]['ability'] = preg_replace('/(\n|\r)/', '', utf8_decode($line[1]));
					} else if ($line[1]{0} == 'i') {
						$line = explode('::', $line[1]);
						$this->items[$code]['code'] = $code;
						$this->items[$code]['name'] = preg_replace('/(\n|\r)/', '', utf8_decode(substr($line[0], 2)));
						$this->items[$code]['img'] = preg_replace('/(\n|\r)/', '', utf8_decode($line[1]));
					}
				}
			}
		}
	}
	
?>