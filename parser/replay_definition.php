<?php

	class ReplayDefinition {
		var $version, $exists;
		var $heroes, $abilities;
		function ReplayDefinition($version) {
			$this->version = $version;
			$this->heroes = array();
			$this->abilities = array();
			$this->load();
		}
		function load() {
			$this->exists = file_exists($this->version.'.txt');
			if ($this->exists) {
				$content = file($this->version.'.txt');
				foreach ($content as $val) {
					$line = explode('$$', $val);
					$code = $line[0];
					if ($line[1]{0} == 'h') {
						$this->heroes[$code]['code'] = $code;
						$this->heroes[$code]['hero'] = preg_replace('/(\n|\r)/', '', substr($line[1], 2));
					} else if ($line[1]{0} == 'a') {
						$line = explode(':', $line[1]);
						$this->abilities[$code]['code'] = $code;
						$this->abilities[$code]['hero'] = preg_replace('/(\n|\r)/', '', substr($line[0], 2));
						$this->abilities[$code]['ability'] = preg_replace('/(\n|\r)/', '', $line[1]);
					}
				}
			}
		}
	}
	
?>