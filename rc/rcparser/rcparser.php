<?php

	require('replay_definition.php');
	require('replay_classes.php');
	
	define("MAX_DATABLOCK", 1500);

	class ReplayParser {

		var $replayPath, $replayFile, $definition;
		var $datas, $header, $players, $game, $chat, $errors;
		var $bsen, $bsco, $psen, $psco;
		var $max_datablock = MAX_DATABLOCK;

		function DotaParser($replayPath) {
			$this->bsen = array();
			$this->bsco = array();
			$this->game["players"] = 0;
			$this->game["mode"] = '';
			$this->replayPath = $replayPath;
			if (!$this->replayFile = fopen($this->replayPath, "rb")) {
				exit("Can't read replay file: ".$this->replayPath);
			}
			flock($this->replayFile, 1);
			$this->parseHeader();
			$this->parseDatas();
			flock($this->replayFile, 3);
			fclose($this->replayFile);
			unset($this->replayFile);
			$this->psen = $this->bsen;
			$this->psco = $this->bsco;
			foreach ($this->players as $key => $player) {
				if ($player['team'] == 0) {
					if (isset($this->bsen[$player['hero']['code']])) unset($this->bsen[$player['hero']['code']]);
					if ($player['endtime'] >= $this->game['time']) $this->players[$key]['endway'] = 'End';
				} else if ($player['team'] == 1) {
					if (isset($this->bsco[$player['hero']['code']])) unset($this->bsco[$player['hero']['code']]);
					if ($player['endtime'] >= $this->game['time']) $this->players[$key]['endway'] = 'End';
				}
			}
			foreach ($this->players as $key => $player) {
				if ($player['team'] == 0) {
					foreach ($this->bsen as $code => $ban) {
						if ($ban['hero'] == $player['hero']['hero']) {
							unset($this->bsen[$code]);
						}
					}
				} else if ($player['team'] == 1) {
					foreach ($this->bsco as $code => $ban) {
						if ($ban['hero'] == $player['hero']['hero']) {
							unset($this->bsco[$code]);
						}
					}
				}
			}
			foreach ($this->bsen as $key => $ban) {
				if (isset($this->psen[$ban['code']])) unset($this->psen[$ban['code']]);
			}
			foreach ($this->bsco as $key => $ban) {
				if (isset($this->psco[$ban['code']])) unset($this->psco[$ban['code']]);
			}
			//unset($this->header);
			//unset($this->players);
			//unset($this->game);
		}

		function parseHeader() {
			$data = fread($this->replayFile, 48);
			$this->header = @unpack("a28intro/Vheader_size/Vc_size/Vheader_v/Vu_size/Vblocks", $data);
			if ($this->header["intro"] != "Warcraft III recorded game\x1A") {
				exit("Not a replay file");
			}
			if ($this->header["header_v"] == 0) {
				$data = fread($this->replayFile, 16);
				$this->header = array_merge($this->header, unpack("vminor_v/vmajor_v/vbuild_v/vflags/Vlength/Vchecksum", $data));
				$this->header["ident"] = "WAR3";
			} elseif ($this->header["header_v"] == 1) {
				$data = fread($this->replayFile, 20);
				$this->header = array_merge($this->header, unpack("a4ident/Vmajor_v/vbuild_v/vflags/Vlength/Vchecksum", $data));
				$this->header["minor_v"] = 0;
				$this->header["ident"] = strrev($this->header["ident"]);
			}
			//echo convert_time($this->header["length"]).'<br />';
		}

		function parseDatas() {
			fseek($this->replayFile, $this->header["header_size"]);
			$blocks = $this->header["blocks"];
			for ($i = 0; $i < $blocks; $i++) {
				$bheader = @unpack("vc_size/vu_size/Vchecksum", fread($this->replayFile, 8));
				$hdatas = fread($this->replayFile, $bheader["c_size"]);
				$hdatas = substr($hdatas, 2, -4);
				$hdatas{0} = chr(ord($hdatas{0}) | 1);
				if ($hdatas = gzinflate($hdatas)) {
					$this->datas .= $hdatas;
				} else {
					exit("Incomplete replay file: ".$this->replayPath);
				}
				if ($i == 0) {
					$this->datas = substr($this->datas, 4);
					$this->parsePlayer();
					$this->parseGame();
					if ($this->game['version'] == "Unknown") $i = $blocks;
				} elseif ($blocks - $i < 2) {
					$this->max_datablock = 0;
				}
				if ($this->game['version'] != "Unknown") $this->parseBlocks();
			}
		}

		function parsePlayer() {
			$player = unpack("Crecord_id/Cplayer_id", $this->datas);
			$this->datas = substr($this->datas, 2);
			$pid = $player["player_id"];
			$this->players[$pid]["player_id"] = $pid;
			$this->players[$pid]["initiator"] = convert_bool(!$player["record_id"]);
			$this->players[$pid]["name"] = substr($this->datas, 0, strpos($this->datas, chr(0)));
			$this->datas = substr($this->datas, strpos($this->datas, chr(0)) + 1);
			if (!$this->players[$pid]["name"]) {
				$this->players[$pid]["name"] = "Player ".$pid;
			}
			if (ord($this->datas{0}) == 1) {
				$this->datas = substr($this->datas, 2);
			} elseif (ord($this->datas{0}) == 8) {
				$this->data = substr($this->datas, 9);
			}
			$this->players[$pid]["actions"] = 0;
			if (!$this->header["build_v"]) {
				$this->players[$pid]["team"] = ($pid - 1) % 2;
			}
			$this->game["players"]++;
		}

		function parseGame() {
			$this->game["name"] = substr($this->datas, 0, strpos($this->datas, chr(0)));;
			$this->datas = substr($this->datas, strpos($this->datas, chr(0)) + 2);
			$encoded = "";
			for ($i = 0; $this->datas{$i} != chr(0); $i++) {
				if ($i % 8 == 0) {
					$mask = ord($this->datas{$i});
				} else {
					$encoded .= chr(ord($this->datas{$i}) - !($mask & (1 << $i % 8)));
				}
			}
			$this->datas = substr($this->datas, $i + 1);
			//$this->game["speed"] = convert_speed(ord($encoded{0}));
			//if (ord($encoded{1}) & 1) {
			//	$this->game["visibility"] = convert_visibility(0);
			//} else if (ord($encoded{1}) & 2) {
			//	$this->game["visibility"] = convert_visibility(1);
			//} else if (ord($encoded{1}) & 4) {
			//	$this->game["visibility"] = convert_visibility(2);
			//} else if (ord($encoded{1}) & 8) {
			//	$this->game["visibility"] = convert_visibility(3);
			//}
			//$this->game["observers"] = convert_observers(((ord($encoded{1}) & 16) == true) + 2 * ((ord($encoded{1}) & 32) == true));
			$this->game["teams_together"] = convert_bool(ord($encoded{1}) & 64);
			$this->game["lock_teams"] = convert_bool(ord($encoded{2}));
			$this->game["full_shared_unit_control"] = convert_bool(ord($encoded{3}) & 1);
			$this->game["random_hero"] = convert_bool(ord($encoded{3}) & 2);
			$this->game["random_races"] = convert_bool(ord($encoded{3}) & 4);
			//if (ord($encoded{3}) & 64) {
			//	$this->game["observers"] = convert_observers(4);
			//}
			$encoded = substr($encoded, 13);
			$encoded = explode(chr(0), $encoded);
			$this->game["creator"] = $encoded[1];
			$this->game["map"] = $encoded[0];
			$this->game['version'] = "Unknown";
			if (ereg('(v[0-9]\.[0-9][0-9][a-z]{0,1})\.', $this->game["map"], $regs)) {
				$this->game['version'] = strtolower($regs[1]);
				$this->definition = new ReplayDefinition($this->game['version']);
				if (!$this->definition->exists) {
					$this->game['version'] = "Unknown";
				}
			}
			$encoded = unpack("Vslots", $this->datas);
			$this->datas = substr($this->datas, 4);
			$this->game["slots"] = $encoded["slots"];
			//$this->game["type"] = convert_game_type(ord($this->datas[0]));
			$this->game["private"] = convert_bool(ord($this->datas[1]));
			$this->datas = substr($this->datas, 8);
			while (ord($this->datas{0}) == 0x16) {
				$this->parsePlayer();
				$this->datas = substr($this->datas, 4);
			}
			$encoded = unpack("Crecord_id/vrecord_length/Cslot_records", $this->datas);
			$this->datas = substr($this->datas, 4);
			$this->game = array_merge($this->game, $encoded);
			$slots = $encoded["slot_records"];
			for ($i = 0; $i < $slots; $i++) {
				if ($this->header["major_v"] >= 7) {
					$encoded = unpack("Cplayer_id/x1/Cslot_status/Ccomputer/Cteam/Ccolor/Crace/Cai_strength/Chandicap", $this->datas);
					$this->datas = substr($this->datas, 9);
				} elseif ($this->header["major_v"] >= 3) {
					$encoded = unpack("Cplayer_id/x1/Cslot_status/Ccomputer/Cteam/Ccolor/Crace/Cai_strength", $this->datas);
					$this->datas = substr($this->datas, 8);
				} else {
					$encoded = unpack("Cplayer_id/x1/Cslot_status/Ccomputer/Cteam/Ccolor/Crace", $this->datas);
					$this->datas = substr($this->datas, 7);
				}
				$encoded["color"] = convert_color($encoded["color"]);
				//$encoded["race"] = convert_race($encoded["race"]);
				//$encoded["ai_strength"] = convert_ai($encoded["ai_strength"]);
				if ($encoded["slot_status"] == 2) {
					$this->players[$encoded["player_id"]] = array_merge($this->players[$encoded["player_id"]], $encoded);
					//$this->players[$encoded["player_id"]]["retraining_time"] = 0;
				}
			}
			$encoded = unpack("Vrandom_seed/Cselect_mode/Cstart_spots", $this->datas);
			$this->datas = substr($this->datas, 6);
			$this->game["random_seed"] = $encoded["random_seed"];
			//$this->game["select_mode"] = convert_select_mode($encoded["select_mode"]);
			if ($encoded["start_spots"] != 0xCC) {
				$this->game["start_spots"] = $encoded["start_spots"];
			}
		}

		function parseBlocks() {
			$data_left = strlen($this->datas);
			while ($data_left > $this->max_datablock) {
				$block = ord($this->datas{0});
				switch ($block) {
					case 0x17:
						$pack = unpack("x1/Lreason/Cplayer_id/Lresult/Lunknown", $this->datas);
						if ($this->players[$pack['player_id']]['team'] == 0 OR $this->players[$pack['player_id']]['team'] == 1) {
							if ($pack['reason'] == 0x01) {
								switch ($pack['result']) {
									case 0x01:
									case 0x07:
									case 0x0B:
										$this->players[$pack['player_id']]['endtime'] = $this->time;
										$this->players[$pack['player_id']]['endway'] = 'Left';
										break;
									case 0x08:
									case 0x09:
									case 0x0A:
										$this->players[$pack['player_id']]['endtime'] = $this->time;
										$this->players[$pack['player_id']]['endway'] = 'End';
										break;
								}
							} else if ($pack['reason'] == 0x0E) {
								switch ($pack['result']) {
									case 0x01:
									case 0x07:
									case 0x0B:
										$this->players[$pack['player_id']]['endtime'] = $this->time;
										$this->players[$pack['player_id']]['endway'] = 'Left';
										break;
								}
							} else if ($pack['reason'] == 0x0C) {
								switch ($pack['result']) {
									case 0x01:
										$this->players[$pack['player_id']]['endtime'] = $this->time;
										$this->players[$pack['player_id']]['endway'] = 'Left';
										break;
									case 0x07:
									case 0x08:
									case 0x09:
									case 0x0A:
										$this->players[$pack['player_id']]['endtime'] = $this->time;
										$this->players[$pack['player_id']]['endway'] = 'End';
										break;
								}
							}
						}
						$this->datas = substr($this->datas, 14);
						$data_left -= 14;
						break;
					case 0x1A:
					case 0x1B:
					case 0x1C:
						$this->datas = substr($this->datas, 5);
						$data_left -= 5;
						break;
					case 0x1E:
					case 0x1F:
						$pack = unpack("x1/vlength/vtime_inc", $this->datas);
						if (!$this->pause) {
							$this->time += $pack["time_inc"];
						}
						if ($pack["length"] > 2) {
							$this->parseActions(substr($this->datas, 5, $pack["length"] - 2), $pack["length"] - 2);
						}
						$this->datas = substr($this->datas, $pack["length"] + 3);
						$data_left -= $pack["length"] + 3;
						break;
					case 0x20:
						if ($this->header["major_v"] > 2) {
							$pack = unpack("x1/Cplayer_id/Slength/Cflags/Smode", $this->datas);
							if ($pack['flags'] == 0x20) {
								$pack['mode'] = convert_chat_mode($pack['mode']);
								$pack['text'] = substr($this->datas, 9, $pack['length'] - 6);
								
							} else if ($pack['flags'] == 0x10) {
								$pack['text'] = substr($this->datas, 7, $pack['length'] - 3);
								unset($pack['mode']);
							}
							$this->datas = substr($this->datas, $pack["length"] + 4);
							$data_left -= $pack["length"] + 4;
							$pack['time'] = convert_time($this->time);
							$pack['player_name'] = $this->players[$pack['player_id']]['name'];
							$pack['player_color'] = $this->players[$pack['player_id']]['color'];
							$this->chat[] = $pack;
						}
						break;
					case 0x22:
						$bytes = ord($this->datas{1});
						$this->datas = substr($this->datas, $bytes + 2);
						$data_left -= $bytes + 2;
						break;
					case 0x23:
						$this->datas = substr($this->datas, 11);
						$data_left -= 11;
						break;
					case 0x2F:
						$this->datas = substr($this->datas, 9);
						$data_left -= 9;
						break;
					case 0:
						$data_left = 0;
						break;
					default:
						exit("Unhandled replay command block: 0x".sprintf("%02X", $block).", time: ".$this->time." in ".$this->replayPath);
				}
			}
		}
		
		function parseActions($actions, $length) {
			$tlen = 0;
			$plen = 0;
			while ($tlen < $length) {
				if ($plen > 0) {
					$actions = substr($actions, $plen);
				}
				$pack = unpack("Cplayer_id/vlength", $actions);
				$pid = $pack["player_id"];
				$plen = $pack["length"] + 3;
				$tlen += $plen;
				$was_deselect = false;
				$was_subupdate = false;
				$alen = 3;
				while ($alen < $plen) {
					$previous = $action;
					$action = ord($actions{$alen});
					switch ($action) {
						case 0x01:
							$this->pause = 1;
							$alen += 1;
							break;
						case 0x02:
							$this->pause = 0;
							$alen += 1;
							break;
						case 0x03:
							$alen += 2;
							break;
						case 0x04:
							$alen += 1;
							break;
						case 0x05:
							$alen += 1;
							break;
						case 0x06:
							while ($actions{$alen} != "\x00") $alen += 1;
							$alen += 1;
							break;
						case 0x07:
							$alen += 5;
							break;
						case 0x10:
							$item = strrev(substr($actions, $alen + 3, 4));
							if (isset($this->definition->heroes[$item])) {
								if (isset($this->game["modes"]["cm"]) OR isset($this->game["modes"]["xl"])) {
									if ($this->players[$pid]["team"] == 0 AND $this->players[$pid]["color"] == "blue" AND !isset($this->bsen[$item])) {
										$this->bsen[$item] = $this->definition->heroes[$item];
										$this->bsen[$item]["time"] = $this->time;
									} else if ($this->players[$pid]["team"] == 1 AND $this->players[$pid]["color"] == "pink" AND !isset($this->bsco[$item])) {
										$this->bsco[$item] = $this->definition->heroes[$item];
										$this->bsco[$item]["time"] = $this->time;
									}
								} else {
									if (!isset($this->players[$pid]["hero"])) {
										$this->players[$pid]["hero"] = $this->definition->heroes[$item];
										$this->players[$pid]["hero"]["time"] = $this->time;
									} else {
										//TODO: HEROS ALREADY EXISTS ! LEAVE ? SWAP ?
										//$this->players[$pid]["hero"] = $this->definition->heroes[$item];
										//$this->players[$pid]["hero"]["time"] = convert_time($this->time);
									}
								}
							} else if (isset($this->definition->abilities[$item])) {
								if (isset($this->game["modes"]["cm"]) OR isset($this->game["modes"]["xl"])) {
									if ($this->players[$pid]["team"] == 0) {
										$code = convert_heroitem($this->bsen, $this->definition->abilities[$item]['hero']);
										//if (isset($this->bsen[$code])) unset($this->bsen[$code]);
									} else {
										$code = convert_heroitem($this->bsco, $this->definition->abilities[$item]['hero']);
										//if (isset($this->bsco[$code])) unset($this->bsco[$code]);
									}
									if (!isset($this->players[$pid]["hero"]) && $code != '') {
										$this->players[$pid]["hero"] = $this->definition->heroes[$code];
										$this->players[$pid]["hero"]["time"] = $this->time;
									} else {
										if ($code != '') {
											// HEROS ALREADY EXISTS ! LEAVE ? SWAP ?
											$this->players[$pid]["hero"] = $this->definition->heroes[$code];
											$this->players[$pid]["hero"]["time"] = $this->time;
										}
									}
								} else {
									if (!isset($this->players[$pid]["hero"])) {
										$code = convert_heroitem($this->definition->heroes, $this->definition->abilities[$item]['hero']);
										$this->players[$pid]["hero"] = $this->definition->heroes[$code];
										$this->players[$pid]["hero"]["time"] = $this->time;
									} else {
										//TODO: HEROS ALREADY EXISTS ! LEAVE ? SWAP ?
										//$code = convert_heroitem($this->definition->heroes, $this->definition->abilities[$item]['hero']);
										//$this->players[$pid]["hero"] = $this->definition->heroes[$code];
										//$this->players[$pid]["hero"]["time"] = convert_time($this->time);
									}
								}
							}
							$alen += 15;
							break;
						case 0x11:
							$alen += 23;
							break;
						case 0x12:
							$alen += 31;
							break;
						case 0x13:
							$alen += 39;
							break;
						case 0x14:
							$alen += 44;
							break;
						case 0x16:
							$pack = unpack("Cmode/vnum", substr($actions, $alen + 1, 3));
							$was_deselect = ($pack['mode'] == 0x02);
							$alen += 4 + ($pack["num"] * 8);
							break;
						case 0x17:
							$pack = unpack("Cgroup/vnum", substr($actions, $alen + 1, 3));
							$alen += 4 + ($pack["num"] * 8);
							break;
						case 0x18:
							$alen += 3;
							break;
						case 0x19:
							if ($this->header['build_v'] >= 6040 || $this->header['major_v'] > 14) {
								$alen += 13;
							} else {
								$was_subupdate = (ord($actions{$alen + 1}) == 0xFF);
								$alen += 2;
							}
							break;
						case 0x1A:
							$alen += 1;
							break;
						case 0x1B:
							$alen += 10;
							break;
						case 0x1C:
							$alen += 10;
							break;
						case 0x1D:
							$alen += 9;
							break;
						case 0x1E:
							$alen += 6;
							break;
						case 0x20:
							$alen += 1;
							break;
						case 0x21:
							$alen += 9;
							break;
						case 0x22:
							$alen += 1;
							break;
						case 0x23:
							$alen += 1;
							break;
						case 0x24:
							$alen += 1;
							break;
						case 0x25:
							$alen += 1;
							break;
						case 0x26:
							$alen += 1;
							break;
						case 0x27:
							$alen += 6;
							break;
						case 0x28:
							$alen += 6;
							break;
						case 0x29:
							$alen += 1;
							break;
						case 0x2A:
							$alen += 1;
							break;
						case 0x2B:
							$alen += 1;
							break;
						case 0x2C:
							$alen += 1;
							break;
						case 0x2D:
							$alen += 6;
							break;
						case 0x2E:
							$alen += 5;
							break;
						case 0x2F:
							$alen += 1;
							break;
						case 0x30:
							$alen += 1;
							break;
						case 0x31:
							$alen += 1;
							break;
						case 0x32:
							$alen += 1;
							break;
						case 0x50:
							$alen += 6;
							break;
						case 0x51:
							$alen += 10;
							break;
						case 0x60:
							$alen += 1;
							$alen += 8;
							$trigger = "";
							while ($actions{$alen} != "\x00") { 
								$trigger .= $actions{$alen};
								$alen += 1;
							}
							$alen += 1;
							//$pack = array();
							//$pack['text'] = htmlentities($trigger);
							//$pack['flags'] = 32;
							//$pack['mode'] = 'All';
							//$pack['time'] = $this->time;
							//$pack['player_name'] = $this->players[$pid]['name'];
							//$this->chat[] = $pack;
							if ($this->players[$pid]["color"] == "blue" && $this->game["mode"] == "") {
								$trigger = preg_replace("/(-| |wtf)/", "", $trigger);
								$modes = convert_gamemode($trigger);
								if (count($modes) > 0) {
									$this->game["mode"] = $trigger;
									$this->game["modes"] = $modes;
								}
							}
							break;
						case 0x61:
							$alen += 1;
							break;
						case 0x62:
							$alen += 13;
							break;
						case 0x65:
							$alen += 1;
							break;
						case 0x66:
							$alen += 1;
							break;
						case 0x67:
							$alen += 1;
							break;
						case 0x68:
							$alen += 13;
							break;
						case 0x69:
							$alen += 17;
							break;
						case 0x6A:
							$alen += 17;
							break;
						case 0x6B:
							$alen += 1;
							while ($actions{$alen} != "\x00") $alen += 1;
							$alen += 1;
							$dotastats['player_id'] = "";
							while ($actions{$alen} != "\x00") {
								$dotastats['player_id'] .= $actions{$alen};
								$alen += 1;
							}
							$alen += 1;
							$isdataorglobal = $dotastats['player_id'];
							$dotastats['player_id'] = convert_playerid($this->players, $dotastats['player_id']);
							$dotastats['statid'] = "";
							while ($actions{$alen} != "\x00") {
								$dotastats['statid'] .= $actions{$alen};
								$alen += 1;
							}
							$alen += 1;
							$dotastats['statval'] = hexdec(sprintf('%02X', ord($actions{$alen})));
							$dword = substr($actions, $alen , 4);
							$stats = unpack('Lvalue', $dword);
							if ($isdataorglobal == 'Data') {
								if (strpos($dotastats['statid'], 'Tower') !== false) {
									// PARSING TOWERS
									$pid = convert_playerid($this->players, $stats['value']);
									if ($this->players[$pid]) {
										$tmp = str_replace('Tower', '', $dotastats['statid']);
										$team = (int)substr($tmp, 0, 1);
										if ($this->players[$pid]['team'] == $team) {
											if (isset($this->players[$pid]['tdeny'])) {
												$this->players[$pid]['tdeny'] += 1;
											} else {
												$this->players[$pid]['tdeny'] = 1;
											}
										} else {
											if (isset($this->players[$pid]['tkill'])) {
												$this->players[$pid]['tkill'] += 1;
											} else {
												$this->players[$pid]['tkill'] = 1;
											}
										}
									}
								} else if (strpos($dotastats['statid'], 'Rax') !== false) {
									// PARSING RAXS
									$pid = convert_playerid($this->players, $stats['value']);
									if ($this->players[$pid]) {
										$tmp = str_replace('Rax', '', $dotastats['statid']);
										$team = (int)substr($tmp, 0, 1);
										if ($this->players[$pid]['team'] == $team) {
											if (isset($this->players[$pid]['rdeny'])) {
												$this->players[$pid]['rdeny'] += 1;
											} else {
												$this->players[$pid]['rdeny'] = 1;
											}
										} else {
											if (isset($this->players[$pid]['rkill'])) {
												$this->players[$pid]['rkill'] += 1;
											} else {
												$this->players[$pid]['rkill'] = 1;
											}
										}
									}
								} else if (strpos($dotastats['statid'], 'Hero') !== false) {
									// PARSING KILLS / DEATHS
									$pid = convert_playerid($this->players, $stats['value']);
									if ($this->players[$pid]) {
										$tmp = str_replace('Hero', '', $dotastats['statid']);
										$kid = convert_playerid($this->players, $tmp);
										if (isset($this->players[$pid]['kstats'][$kid])) {
											$this->players[$pid]['kstats'][$kid] += 1;
										} else {
											$this->players[$pid]['kstats'][$kid] = 1;
										}
									}
								} else if (strpos($dotastats['statid'], 'CK') !== false) {
									// PARSING CREEPS STATS
									$pid = convert_playerid($this->players, $stats['value']);
									if ($this->players[$pid]) {
										$tmp = str_replace('CK', '', $dotastats['statid']);
										$tmp = str_replace('N', 'D', $tmp);
										$atmp = explode('D', $tmp);
										$this->players[$pid]['creepskills'] = $atmp[0];
										$this->players[$pid]['creepsdenies'] = $atmp[1];
										$this->players[$pid]['neutrals'] = $atmp[2];
									}
								} else if ($dotastats['statid'] == 'GameStart') {
									$this->game['start_time'] = $this->time;
								} else if (strpos($dotastats['statid'], 'Ban') !== false) {
									// PARSING BANS
									$pid = str_replace('Ban', '', $dotastats['statid']);
									$pid = convert_playerid($this->players, $pid);
									$item = strrev(substr($actions, $alen, 4));
									if ($this->players[$pid]["team"] == 0 AND !isset($this->bsen[$item])) {
										$this->bsen[$item] = $this->definition->heroes[$item];
										$this->bsen[$item]["time"] = $this->time;
									} else if ($this->players[$pid]["team"] == 1 AND !isset($this->bsco[$item])) {
										$this->bsco[$item] = $this->definition->heroes[$item];
										$this->bsco[$item]["time"] = $this->time;
									}
								} else {
									//echo $dotastats['statid'].', data<br />';
								}
							} else if ($isdataorglobal != 'Global') {
								if ($this->players[$dotastats['player_id']]) {
									if ($dotastats['statid'].'' == '1') {
										$this->players[$dotastats['player_id']]['kills'] = $stats['value'];
									} else if ($dotastats['statid'].'' == '2') {
										$this->players[$dotastats['player_id']]['deaths'] = $stats['value'];
									} else if ($dotastats['statid'].'' == '3') {
										$this->players[$dotastats['player_id']]['creepskills'] = $stats['value'];
									} else if ($dotastats['statid'].'' == '4') {
										$this->players[$dotastats['player_id']]['creepsdenies'] = $stats['value'];
									} else if ($dotastats['statid'].'' == '5') {
										$this->players[$dotastats['player_id']]['assists'] = $stats['value'];
									} else if ($dotastats['statid'].'' == '6') {
										$this->players[$dotastats['player_id']]['gold'] = $stats['value'];
									} else if ($dotastats['statid'].'' == '7') {
										$this->players[$dotastats['player_id']]['neutrals'] = $stats['value'];
									} else if (substr($dotastats['statid'], 0, 2) == '8_') {
										$slot = str_replace('8_', 's', $dotastats['statid']);
										$tmp = strrev(substr($actions, $alen, 4));
										if ($stats['value'] > 0) {
											$this->players[$dotastats['player_id']]['items'][$slot] = $this->definition->items[$tmp];
										}
									} else {
										$item = strrev(substr($actions, $alen, 4));
										if (isset($this->definition->heroes[$item])) {
											$this->players[$dotastats['player_id']]["hero"] = $this->definition->heroes[$item];
											$this->players[$dotastats['player_id']]["hero"]["time"] = $this->time;
										}
									}
								}
							} else {
								if ($dotastats['statid'].'' == 'Winner') {
									$this->game['time'] = $this->time;
									$this->game['winner'] = $stats['value'];
								}
							}
							$alen += 4;
							break;
						case 0x70:
							$alen += 1;
							while ($actions{$alen} != "\x00") $alen += 1;
							$alen += 1;
							$dotawinner['winner'] = "";
							while ($actions{$alen} != "\x00") {
								$dotawinner['winner'] .= $actions{$alen};
								$alen += 1;
							}
							$alen += 1;
							while ($actions{$alen} != "\x00") {
								$dotawinner['team'] .= $actions{$alen};
								$alen += 1;
							}
							$alen += 1;
							break;
						case 0x75:
							$alen += 2;
							break;
						default:
							$this->errors[$this->time] = "Player: ".$this->players[$pid]["name"].", unknown action: 0x".sprintf("%02X", $action).", previous: 0x".sprintf("%02X", $previous);
							$alen += 2;
					}
				}
				$was_deselect = ($action == 0x16);
				$was_subupdate = ($action == 0x19);
			}
		}

	}

	function convert_bool($value) {
		if (!$value) { return false; }
		return true;
	}

	function convert_color($value) {
		switch ($value) {
			case 0: $value = "red"; break;
			case 1: $value = "blue"; break;
			case 2: $value = "teal"; break;
			case 3: $value = "purple"; break;
			case 4: $value = "yellow"; break;
			case 5: $value = "orange"; break;
			case 6: $value = "green"; break;
			case 7: $value = "pink"; break;
			case 8: $value = "gray"; break;
			case 9: $value = "light-blue"; break;
			case 10: $value = "dark-green"; break;
			case 11: $value = "brown"; break;
			case 12: $value = "observer"; break;
		}
		return $value;
	}

	function convert_playerid($players, $slotid) {
		$color = convert_color($slotid);
		$player_id = -1;
		foreach ($players as $key => $value) {
			if ($value['color'] == $color) {
				$player_id = $value['player_id'];
				break;
			}
		}
		return $player_id;
	}

	function convert_heroitem($heroes, $hero) {
		$code = '';
		foreach ($heroes as $key => $value) {
			if ($value['hero'] == $hero) {
				$code = $value['code'];
				break;
			}
		}
		return $code;
	}

	function convert_chat_mode($value, $player = "unknown") {
		switch ($value) {
			case 0x00: $value = "All"; break;
			case 0x01: $value = "Allies"; break;
			case 0x02: $value = "Observers"; break;
			case 0xFE: $value = "The game has been paused by ".$player."."; break;
			case 0xFF: $value = "The game has been resumed by ".$player."."; break;
			default: $value -= 2;
		}
		return $value;
	}

	function convert_gamemode($value) {
		$modes = array();
		$value = strtolower($value);
		$value = str_replace("allpick", "ap", $value);
		$value = str_replace("allrandom", "ar", $value);
		$value = str_replace("teamrandom", "tr", $value);
		$value = str_replace("moderandom", "mr", $value);
		$value = str_replace("singledraft", "sd", $value);
		$value = str_replace("leaguemode", "lm", $value);
		$value = str_replace("extendedleague", "xl", $value);
		$value = str_replace("randomdraft", "rd", $value);
		$value = str_replace("voterandom", "vr", $value);
		$value = str_replace("captainsmode", "cm", $value);
		$value = str_replace("deathmatch", "dm", $value);
		$value = str_replace("reverse", "rv", $value);
		$value = str_replace("mirrormatch", "mm", $value);
		$value = str_replace("duplicatemode", "du", $value);
		$value = str_replace("shuffleplayers", "sp", $value);
		$value = str_replace("samehero", "sh", $value);
		$value = str_replace("allagility", "aa", $value);
		$value = str_replace("allintelligence", "ai", $value);
		$value = str_replace("allstrength", "as", $value);
		$value = str_replace("itemdrop", "id", $value);
		$value = str_replace("poolingmode", "pm", $value);
		$value = str_replace("easymode", "em", $value);
		$value = str_replace("nopowerups", "np", $value);
		$value = str_replace("supercreeps", "sc", $value);
		$value = str_replace("miniheroes", "mi", $value);
		$value = str_replace("onlymid", "om", $value);
		$value = str_replace("notop", "nt", $value);
		$value = str_replace("nomid", "nm", $value);
		$value = str_replace("nobot", "nb", $value);
		$value = str_replace("noswap", "ns", $value);
		$value = str_replace("norepick", "nr", $value);
		$value = str_replace("terrainsnow", "ts", $value);
		$value = str_replace("observerinfo", "oi", $value);
		$value = str_replace("captainsdraft", "cp", $value);
		if (!(strpos($value, "ap") === false)) $modes["ap"] = "All Pick";
		if (!(strpos($value, "ar") === false)) $modes["ar"] = "All Random";
		if (!(strpos($value, "tr") === false)) $modes["tr"] = "Team Random";
		if (!(strpos($value, "mr") === false)) $modes["mr"] = "Mode Random";
		if (!(strpos($value, "sd") === false)) $modes["sd"] = "Single Draft";
		if (!(strpos($value, "lm") === false)) $modes["lm"] = "League Mode";
		if (!(strpos($value, "xl") === false)) $modes["xl"] = "Extended League";
		if (!(strpos($value, "rd") === false)) $modes["rd"] = "Random Draft";
		if (!(strpos($value, "vr") === false)) $modes["vr"] = "Vote Random";
		if (!(strpos($value, "cm") === false)) $modes["cm"] = "Captains Mode";
		if (!(strpos($value, "dm") === false)) $modes["dm"] = "Death Match";
		if (!(strpos($value, "rv") === false)) $modes["rv"] = "Reverse Mode";
		if (!(strpos($value, "mm") === false)) $modes["mm"] = "Mirror Match";
		if (!(strpos($value, "du") === false)) $modes["du"] = "Duplicate Mode";
		if (!(strpos($value, "sp") === false)) $modes["sp"] = "Shuffle Players";
		if (!(strpos($value, "sh") === false)) $modes["sh"] = "Same Hero";
		if (!(strpos($value, "aa") === false)) $modes["aa"] = "All Agility";
		if (!(strpos($value, "ai") === false)) $modes["ai"] = "All Intelligence";
		if (!(strpos($value, "as") === false)) $modes["as"] = "All Strength";
		if (!(strpos($value, "id") === false)) $modes["id"] = "Item Drop";
		if (!(strpos($value, "pm") === false)) $modes["pm"] = "Pooling Mode";
		if (!(strpos($value, "em") === false)) $modes["em"] = "Easy Mode";
		if (!(strpos($value, "np") === false)) $modes["np"] = "No Powerups";
		if (!(strpos($value, "sc") === false)) $modes["sc"] = "Super Creeps";
		if (!(strpos($value, "mi") === false)) $modes["mi"] = "Mini Heroes";
		if (!(strpos($value, "om") === false)) $modes["om"] = "Only Mid";
		if (!(strpos($value, "nt") === false)) $modes["nt"] = "No Top";
		if (!(strpos($value, "nm") === false)) $modes["nm"] = "No Mid";
		if (!(strpos($value, "nb") === false)) $modes["nb"] = "No Bottom";
		if (!(strpos($value, "ns") === false)) $modes["ns"] = "No Swap";
		if (!(strpos($value, "nr") === false)) $modes["nr"] = "No Repick";
		if (!(strpos($value, "ts") === false)) $modes["ts"] = "Terrain Snow";
		if (!(strpos($value, "oi") === false)) $modes["oi"] = "Observer Info";
		if (!(strpos($value, "cd") === false)) $modes["cd"] = "Captains Draft";
		return $modes;
	}

?>