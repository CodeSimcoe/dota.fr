<?php

	require_once 'ReplayClasses.php';

	define("MAX_DATABLOCK", 1500);
	
	class ReplayParser {

		var $replayPath, $replayFile, $definition;
		var $datas, $header, $players, $game, $chat, $errors;
		var $towers, $raxs, $thrones;
		var $bsen, $bsco, $psen, $psco, $hsen, $hsco;
		var $max_datablock = MAX_DATABLOCK;

		function ReplayParser($replayPath) {
		
			$this->bsen = array();
			$this->bsco = array();
			$this->psen = array();
			$this->psco = array();
			$this->hsen = array();
			$this->hsco = array();
			
			$this->towers = array();
			$this->raxs = array();
			$this->thrones = array();
			
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
			
			$tmp_time = isset($this->game['time']) ? $this->game['time'] : $this->header['length'];
			if (count($this->bsen) > 0) {
				foreach ($this->bsen as $key => $ban) {
					if (isset($this->hsen[$ban['code']])) unset($this->hsen[$ban['code']]);
					if (isset($this->hsco[$ban['code']])) unset($this->hsco[$ban['code']]);
				}
			}
			if (count($this->bsco) > 0) {
				foreach ($this->bsco as $key => $ban) {
					if (isset($this->hsen[$ban['code']])) unset($this->hsen[$ban['code']]);
					if (isset($this->hsco[$ban['code']])) unset($this->hsco[$ban['code']]);
				}
			}
			$this->psen = $this->hsen;
			$this->psco = $this->hsco;
			foreach ($this->players as $key => $player) {
				if ($player['endtime'] >= $tmp_time) $this->players[$key]['endway'] = 'End';
			}
		}

		function txt_serialize() {
			$replay = new DotaReplay();
			$replay->version = $this->game['version'];
			if (isset($this->game['time'])) {
				$replay->time = $this->game['time'];
			} else {
				$replay->time = $this->header['length'];
			}
			$replay->mode = $this->game['mode'];
			if ($replay->mode !== '') {
				$replay->modes = $this->game['modes'];
			}
			$replay->host = $this->game["creator"];
			$replay->towers = $this->towers;
			$replay->raxs = $this->raxs;
			$replay->thrones = $this->thrones;
			foreach ($this->players as $key => $player) {
				$dplayer = new DotaPlayer($player['name']);
				$dplayer->id = $player['player_id'];
				$dplayer->color = $player['color'];
				$dplayer->hero = $player['hero'];
				if ($player['kills']) $dplayer->kills = $player['kills'];
				if ($player['deaths']) $dplayer->deaths = $player['deaths'];
				if ($player['creepskills']) $dplayer->creepskills = $player['creepskills'];
				if ($player['creepsdenies']) $dplayer->creepsdenies = $player['creepsdenies'];
				if ($player['assists']) $dplayer->assists = $player['assists'];
				if ($player['gold']) $dplayer->gold = $player['gold'];
				if ($player['neutrals']) $dplayer->neutrals = $player['neutrals'];
				if ($player['tkill']) $dplayer->tkill = $player['tkill'];
				if ($player['tdeny']) $dplayer->tdeny = $player['tdeny'];
				if ($player['rkill']) $dplayer->rkill = $player['rkill'];
				if ($player['rdeny']) $dplayer->rdeny = $player['rdeny'];
				if ($player['kstats']) $dplayer->kstats = $player['kstats'];
				if ($player['wards']) $dplayer->wards = $player['wards'];
				if ($player['items']) $dplayer->items = $player['items'];
				if ($player['endtime']) $dplayer->endtime = $player['endtime'];
				if ($player['endway']) $dplayer->endway = $player['endway'];
				if ($player['team'] == 0) {
					$replay->sentinel->players[] = $dplayer;
				} else if ($player['team'] == 1) {
					$replay->scourge->players[] = $dplayer;
				} else {
					$replay->observers[] = $dplayer;
				}
			}
			$replay->sentinel->bans = $this->bsen;
			$replay->sentinel->picks = $this->psen;
			$replay->scourge->bans = $this->bsco;
			$replay->scourge->picks = $this->psco;
			$replay->chat = $this->chat;
			$rdatas = serialize($replay);
			$rfile = fopen($this->replayPath.'.txt', 'w');
			fwrite($rfile, $rdatas);
			fclose($rfile);
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
			$this->players[$pid]["last_selection"] = '';
			$this->players[$pid]["initiator"] = ReplayFunctions::convert_bool(!$player["record_id"]);
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
			$this->game["teams_together"] = ReplayFunctions::convert_bool(ord($encoded{1}) & 64);
			$this->game["lock_teams"] = ReplayFunctions::convert_bool(ord($encoded{2}));
			$this->game["full_shared_unit_control"] = ReplayFunctions::convert_bool(ord($encoded{3}) & 1);
			$this->game["random_hero"] = ReplayFunctions::convert_bool(ord($encoded{3}) & 2);
			$this->game["random_races"] = ReplayFunctions::convert_bool(ord($encoded{3}) & 4);
			//if (ord($encoded{3}) & 64) {
			//	$this->game["observers"] = convert_observers(4);
			//}
			$encoded = substr($encoded, 13);
			$encoded = explode(chr(0), $encoded);
			$this->game["creator"] = $encoded[1];
			$this->game["map"] = $encoded[0];
			$this->game['version'] = "Unknown";
			if (ereg('(v[0-9]\.[0-9][0-9][a-z]{0,1})', $this->game["map"], $regs)) {
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
			$this->game["private"] = ReplayFunctions::convert_bool(ord($this->datas[1]));
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
				$encoded["color"] = ReplayFunctions::convert_color($encoded["color"]);
				//$encoded["race"] = convert_race($encoded["race"]);
				//$encoded["ai_strength"] = convert_ai($encoded["ai_strength"]);
				if ($encoded["slot_status"] == 2) {
					if (is_array($this->players[$encoded["player_id"]])) {
						$this->players[$encoded["player_id"]] = array_merge($this->players[$encoded["player_id"]], $encoded);
					}
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
										if ($this->players[$pack['player_id']]["name"] == $this->game["creator"]) $this->game["time"] = $this->time;
										break;
									case 0x08:
									case 0x09:
									case 0x0A:
										$this->players[$pack['player_id']]['endtime'] = $this->time;
										$this->players[$pack['player_id']]['endway'] = 'End';
										if ($this->players[$pack['player_id']]["name"] == $this->game["creator"]) $this->game["time"] = $this->time;
										break;
								}
							} else if ($pack['reason'] == 0x0E) {
								switch ($pack['result']) {
									case 0x01:
									case 0x07:
									case 0x0B:
										$this->players[$pack['player_id']]['endtime'] = $this->time;
										$this->players[$pack['player_id']]['endway'] = 'Left';
										if ($this->players[$pack['player_id']]["name"] == $this->game["creator"]) $this->game["time"] = $this->time;
										break;
								}
							} else if ($pack['reason'] == 0x0C) {
								switch ($pack['result']) {
									case 0x01:
										$this->players[$pack['player_id']]['endtime'] = $this->time;
										$this->players[$pack['player_id']]['endway'] = 'Left';
										if ($this->players[$pack['player_id']]["name"] == $this->game["creator"]) $this->game["time"] = $this->time;
										break;
									case 0x07:
									case 0x08:
									case 0x09:
									case 0x0A:
										$this->players[$pack['player_id']]['endtime'] = $this->time;
										$this->players[$pack['player_id']]['endway'] = 'End';
										if ($this->players[$pack['player_id']]["name"] == $this->game["creator"]) $this->game["time"] = $this->time;
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
								$pack['mode'] = ReplayFunctions::convert_chat_mode($pack['mode']);
								$pack['text'] = substr($this->datas, 9, $pack['length'] - 6);
							} else if ($pack['flags'] == 0x10) {
								$pack['text'] = substr($this->datas, 7, $pack['length'] - 3);
								unset($pack['mode']);
							}
							$this->datas = substr($this->datas, $pack["length"] + 4);
							$data_left -= $pack["length"] + 4;
							$pack['time'] = ReplayFunctions::convert_time($this->time);
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
							$item = strtolower(strrev(substr($actions, $alen + 3, 4)));
							if (isset($this->game["modes"]["cd"]) AND $item == "h06n" AND $this->players[$pid]["last_selection"] != ""
								AND ($this->players[$pid]["color"] == "blue" OR $this->players[$pid]["color"] == "pink")) {
								/*
								$item = $this->players[$pid]["last_selection"];
								echo $item.'<br />';
								if ($this->players[$pid]["team"] == 0 AND !isset($this->hsen[$item])) {
									$this->hsen[$item] = $this->definition->heroes[$item];
									$this->hsen[$item]["time"] = $this->time;
								} else if ($this->players[$pid]["team"] == 1 AND !isset($this->hsco[$this->players[$pid]["last_selection"]])) {
									$this->hsco[$item] = $this->definition->heroes[$item];
									$this->hsco[$item]["time"] = $this->time;
								}
								*/
							} else if (isset($this->definition->heroes[$item])) {
								$item = $this->definition->heroes[$item]["base_code"];
								if (isset($this->definition->heroes[$item])) {
									if (isset($this->game["modes"]["cm"]) 
									    AND ($this->players[$pid]["color"] == "blue" OR $this->players[$pid]["color"] == "pink")) {
										if ($this->players[$pid]["team"] == 0 AND !isset($this->hsen[$item])) {
											$this->hsen[$item] = $this->definition->heroes[$item];
											$this->hsen[$item]["time"] = $this->time;
										} else if ($this->players[$pid]["team"] == 1 AND !isset($this->hsco[$item])) {
											$this->hsco[$item] = $this->definition->heroes[$item];
											$this->hsco[$item]["time"] = $this->time;
										}
									} else if (isset($this->game["modes"]["xl"]) 
											   AND ($this->players[$pid]["color"] == "blue" OR $this->players[$pid]["color"] == "pink")) {
										if ($this->players[$pid]["team"] == 0 AND !isset($this->hsen[$item])) {
											$this->hsen[$item] = $this->definition->heroes[$item];
											$this->hsen[$item]["time"] = $this->time;
										} else if ($this->players[$pid]["team"] == 1 AND !isset($this->hsco[$item])) {
											$this->hsco[$item] = $this->definition->heroes[$item];
											$this->hsco[$item]["time"] = $this->time;
										}
									} else {
										if (!isset($this->players[$pid]["hero"])) {
											$this->players[$pid]["hero"] = $this->definition->heroes[$item];
											$this->players[$pid]["hero"]["time"] = $this->time;
										} else {
											// TODO: HEROS ALREADY EXISTS ! LEAVE ? SWAP ?
										}
									}
								}
							} else if (isset($this->definition->abilities[$item])) {
								// TODO: IMPLEMENTATION DETECTION PAR ABILITIES ?
								/*
								if (isset($this->game["modes"]["cm"]) OR isset($this->game["modes"]["xl"])) {
									if ($this->players[$pid]["team"] == 0) {
										$code = ReplayFunctions::convert_heroitem($this->bsen, $this->definition->abilities[$item]['hero']);
									} else {
										$code = ReplayFunctions::convert_heroitem($this->bsco, $this->definition->abilities[$item]['hero']);
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
										$code = ReplayFunctions::convert_heroitem($this->definition->heroes, $this->definition->abilities[$item]['hero']);
										$this->players[$pid]["hero"] = $this->definition->heroes[$code];
										$this->players[$pid]["hero"]["time"] = $this->time;
									} else {
										//TODO: HEROS ALREADY EXISTS ! LEAVE ? SWAP ?
										//$code = ReplayFunctions::convert_heroitem($this->definition->heroes, $this->definition->abilities[$item]['hero']);
										//$this->players[$pid]["hero"] = $this->definition->heroes[$code];
										//$this->players[$pid]["hero"]["time"] = ReplayFunctions::convert_time($this->time);
									}
								}
								*/
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
								if (isset($this->game["modes"]["cd"])) {
									if ($this->players[$pid]["color"] == "blue" OR $this->players[$pid]["color"] == "pink") {
										$item = strtolower(strrev(substr($actions, $alen + 1, 4)));
										if (isset($this->definition->heroes[$item])) {
											$item = $this->definition->heroes[$item]["base_code"];
											if (isset($this->definition->heroes[$item])) {
												$this->players[$pid]["last_selection"] = $item;
											}
										}
									}
								}
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
							$pack = array();
							$pack['text'] = htmlentities($trigger);
							$pack['flags'] = 32;
							$pack['mode'] = 'All';
							$pack['time'] = $this->time;
							$pack['player_name'] = $this->players[$pid]['name'];
							//$this->chat[] = $pack;
							if ($this->players[$pid]["color"] == "blue" && $this->game["mode"] == "") {
								$trigger = preg_replace("/(-| |wtf)/", "", $trigger);
								$modes = ReplayFunctions::convert_gamemode($trigger);
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
							$dotastats['player_id'] = ReplayFunctions::convert_playerid($this->players, $dotastats['player_id']);
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
									$pid = ReplayFunctions::convert_playerid($this->players, $stats['value']);
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
									if ($dotastats['statid'] == 'Tower041' || $dotastats['statid'] == 'Tower141') {
										if (in_array($dotastats['statid'].'_1', $this->towers)) $this->towers[] = $dotastats['statid'].'_2';
										else $this->towers[] = $dotastats['statid'].'_1';
									} else {
										$this->towers[] = $dotastats['statid'];
									}
								} else if (strpos($dotastats['statid'], 'Rax') !== false) {
									// PARSING RAXS
									$pid = ReplayFunctions::convert_playerid($this->players, $stats['value']);
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
									$this->raxs[] = $dotastats['statid'];
								} else if (strpos($dotastats['statid'], 'Courier') !== false) {

								} else if (strpos($dotastats['statid'], 'Throne') !== false) {
								
								} else if (strpos($dotastats['statid'], 'Tree') !== false) {
								
								} else if (strpos($dotastats['statid'], 'Hero') !== false) {
									// PARSING KILLS / DEATHS
									$pid = ReplayFunctions::convert_playerid($this->players, $stats['value']);
									if (isset($this->players[$pid])) {
										$tmp = str_replace('Hero', '', $dotastats['statid']);
										$kid = ReplayFunctions::convert_playerid($this->players, $tmp);
										if (isset($this->players[$pid]['kstats'][$kid])) {
											$this->players[$pid]['kstats'][$kid] += 1;
										} else {
											$this->players[$pid]['kstats'][$kid] = 1;
										}
									}
								} else if (strpos($dotastats['statid'], 'CK') !== false) {
									// PARSING CREEPS STATS
									$pid = ReplayFunctions::convert_playerid($this->players, $stats['value']);
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
									$pid = ReplayFunctions::convert_playerid($this->players, $pid);
									$item = strtolower(strrev(substr($actions, $alen, 4)));
									if (isset($this->definition->heroes[$item])) {
										$item = $this->definition->heroes[$item]["base_code"];
										if (isset($this->definition->heroes[$item])) {
											if ($this->players[$pid]["team"] == 0 AND !isset($this->bsen[$item])) {
												$this->bsen[$item] = $this->definition->heroes[$item];
												$this->bsen[$item]["time"] = $this->time;
											} else if ($this->players[$pid]["team"] == 1 AND !isset($this->bsco[$item])) {
												$this->bsco[$item] = $this->definition->heroes[$item];
												$this->bsco[$item]["time"] = $this->time;
											}
										}
									}
								} else if (strpos($dotastats['statid'], 'PUI_') !== false) {
										$pid = str_replace('PUI_', '', $dotastats['statid']);
										$pid = ReplayFunctions::convert_playerid($this->players, $pid);
										$item = strtolower(strrev(substr($actions, $alen, 4)));
										if (isset($this->definition->items[$item])) {
											if ($this->players[$pid]) {
												if (strpos(strtolower($this->definition->items[$item]['name']), 'wards') !== false) {
													if (isset($this->players[$pid]['wards'])) {
														$this->players[$pid]['wards'] += 2;
													} else {
														$this->players[$pid]['wards'] = 2;
													}
												}
											}
										}
								} else {
									//echo $dotastats['statid'].' - '.$stats['value'].'<br />';
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
										$tmp = strtolower(strrev(substr($actions, $alen, 4)));
										if ($stats['value'] > 0 && isset($this->definition->items[$tmp])) {
											$this->players[$dotastats['player_id']]['items'][$slot] = $this->definition->items[$tmp];
										}
									} else if ($dotastats['statid'].'' == '9') {
										$item = strtolower(strrev(substr($actions, $alen, 4)));
										if (isset($this->definition->heroes[$item])) {
											$item = $this->definition->heroes[$item]["base_code"];
											if (isset($this->definition->heroes[$item])) {
												$this->players[$dotastats['player_id']]["hero"] = $this->definition->heroes[$item];
												$this->players[$dotastats['player_id']]["hero"]["time"] = $this->time;
											}
										}
									} else {
										//echo $dotastats['statid'].' - '.$stats['value'].'<br />';
									}
								}
							} else {
								if ($dotastats['statid'].'' == 'Winner') {
									$this->game['time'] = $this->time;
									$this->game['winner'] = $stats['value'];
								} else {
									//echo $dotastats['statid'].' - '.$stats['value'].'<br />';
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

?>