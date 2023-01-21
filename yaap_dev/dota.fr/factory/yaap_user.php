<?php

class YaapUser extends YaapUserBase {

	public $ggc_account;
	public $team;

	public function __construct() {
		$this->team = new YaapTeam();
	}

}

?>