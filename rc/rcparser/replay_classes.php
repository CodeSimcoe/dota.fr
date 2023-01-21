<?php

	class DotaPlayer {
	
		var $id, $name, $color, $hero, $items;
		var $kills, $deaths, $creepskills, $creepsdenies, $assists, $neutrals, $gold;
		var $tkill, $tdeny, $rkill, $rdeny, $kstats;
		var $endtime, $endway;
		
		function DotaPlayer($playerName) {
			$this->id = 0;
			$this->name = $playerName;
			$this->hero = '';
			$this->kills = 0;
			$this->deaths = 0;
			$this->assists = 0;
			$this->creepskills = 0;
			$this->creepsdenies = 0;
			$this->neutrals = 0;
			$this->gold = 0;
			$this->tkill = 0;
			$this->tdeny = 0;
			$this->rkill = 0;
			$this->rdeny = 0;
			$this->kstats = Array();
			$this->items = Array();
			$this->endtime = 0;
			$this->endway = 'End';
		}
	
	}
	
	class DotaTeam {
	
		var $name, $bans, $picks, $players;

		function DotaTeam($teamName) {
			$this->name = $teamName;
			$this->bans = array();
			$this->picks = array();
			$this->players = array();
		}
	
	}

	class DotaReplay {

		var $version, $time, $mode, $modes, $chat;
		var $sentinel, $scourge, $observers;

		function DotaReplay() {
			$this->time = 0;
			$this->sentinel = new DotaTeam('Sentinel');
			$this->scourge = new DotaTeam('Scourge');
			$this->observers = array();
			$this->modes = array();
			$this->chat = array();
		}

	}

?>