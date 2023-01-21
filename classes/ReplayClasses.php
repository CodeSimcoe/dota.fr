<?php

	require_once 'ReplayFunctions.php';
	
	define("REPLAY_DEFINITIONS_PATH", "/home/www/ligue/parser/");

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
			$this->exists = file_exists(REPLAY_DEFINITIONS_PATH.$this->version.'.txt');
			if ($this->exists) {
				$content = file(REPLAY_DEFINITIONS_PATH.$this->version.'.txt');
				foreach ($content as $val) {
					$line = explode('$$', $val);
					$code = preg_replace('/(\n|\r)/', '', utf8_decode($line[0]));
					if ($line[1]{0} == 'h') {
						$this->heroes[$code]['code'] = $code;
						$this->heroes[$code]['hero'] = preg_replace('/(\n|\r)/', '', utf8_decode(substr($line[1], 2)));
						$this->heroes[$code]['base_code'] = preg_replace('/(\n|\r)/', '', utf8_decode($line[2]));
						$this->heroes[$code]['img'] = preg_replace('/(\n|\r)/', '', utf8_decode($line[3]));
					} else if ($line[1]{0} == 'a') {
						$this->abilities[$code]['code'] = $code;
						$this->abilities[$code]['hero_code'] = preg_replace('/(\n|\r)/', '', utf8_decode(substr($line[1], 2)));
						$this->abilities[$code]['ability'] = preg_replace('/(\n|\r)/', '', utf8_decode($line[2]));
						$this->abilities[$code]['img'] = preg_replace('/(\n|\r)/', '', utf8_decode($line[3]));
					} else if ($line[1]{0} == 'i') {
						$this->items[$code]['code'] = $code;
						$this->items[$code]['name'] = preg_replace('/(\n|\r)/', '', utf8_decode(substr($line[1], 2)));
						$this->items[$code]['main'] = preg_replace('/(\n|\r)/', '', utf8_decode($line[2]));
						$this->items[$code]['img'] = preg_replace('/(\n|\r)/', '', utf8_decode($line[3]));
					}
				}
			}
		}
	}

	class DotaPlayer {
	
		var $id, $name, $color, $hero, $items;
		var $kills, $deaths, $creepskills, $creepsdenies, $assists, $neutrals, $gold;
		var $tkill, $tdeny, $rkill, $rdeny, $kstats, $wards;
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
			$this->wards = 0;
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

		var $host, $version, $time, $mode, $modes, $chat;
		var $sentinel, $scourge, $observers;
		var $towers, $raxs, $thrones;

		function DotaReplay() {
			$this->time = 0;
			$this->sentinel = new DotaTeam('Sentinel');
			$this->scourge = new DotaTeam('Scourge');
			$this->observers = array();
			$this->modes = array();
			$this->chat = array();
			$this->towers = array();
			$this->raxs = array();
			$this->thrones = array();
		}

		static function load_from_txt($path) {
			$content = file($path);
			return unserialize($content[0]);
		}

	}

?>