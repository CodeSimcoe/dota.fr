<?php

class YaapUser extends YaapUserBase {

	public $lol_account;
	public $team;

	public function __construct() {
		$this->team = new YaapTeam();
	}

}

?>