<?php
/******************************************************************************
Additional conversion functions for
Warcraft III Replay Parser 2.3
(c) 2003-2008 Juliusz 'Julas' Gonera
http://w3rep.sourceforge.net/
e-mail: julas@toya.net.pl
******************************************************************************/

function convert_bool($value) {
	if (!$value)
		return false;
	
	return true;
}

function convert_speed($value) {
	switch ($value) {
		case 0: $value = 'Slow'; break;
		case 1: $value = 'Normal'; break;
		case 2: $value = 'Fast'; break;
	}
	return $value;
}

function convert_visibility($value) {
	switch ($value) {
		case 0: $value = 'Hide Terrain'; break;
		case 1: $value = 'Map Explored'; break;
		case 2: $value = 'Always Visible'; break;
		case 3: $value = 'Default'; break;
	}
	return $value;
}

function convert_observers($value) {
	switch ($value) {
		case 0: $value = 'No Observers'; break;
		case 2: $value = 'Observers on Defeat'; break;
		case 3: $value = 'Full Observers'; break;
		case 4: $value = 'Referees'; break;
	}
	return $value;
}

function convert_game_type($value) {
	switch ($value) {
		case 0x01: $value = 'Ladder 1vs1/FFA'; break;
		case 0x09: $value = 'Custom game'; break;
		case 0x0D: $value = 'Single player/Local game'; break;
		case 0x20: $value = 'Ladder team game (AT/RT)'; break;
		default: $value = 'unknown';
	}
	return $value;
}

function convert_color($value) {
	switch ($value) {
		case 0: $value = 'red'; break;
		case 1: $value = 'blue'; break;
		case 2: $value = 'teal'; break;
		case 3: $value = 'purple'; break;
		case 4: $value = 'yellow'; break;
		case 5: $value = 'orange'; break;
		case 6: $value = 'green'; break;
		case 7: $value = 'pink'; break;
		case 8: $value = 'gray'; break;
		case 9: $value = 'light-blue'; break;
		case 10: $value = 'dark-green'; break;
		case 11: $value = 'brown'; break;
		case 12: $value = 'observer'; break;
	}
	return $value;
}

function convert_race($value) {
	switch ($value) {
		case 'hpea': case 0x01: case 0x41: $value = 'Human'; break;
		case 'opeo': case 0x02: case 0x42: $value = 'Orc'; break;
		case 'ewsp': case 0x04: case 0x44: $value = 'NightElf'; break;
		case 'uaco': case 0x08: case 0x48: $value = 'Undead'; break;
		case 0x20: case 0x60: $value = 'Random'; break;
		default: $value = 0; // do not change this line
	}
	return $value;
}

function convert_ai($value) {
	switch ($value) {
		case 0x00: $value = "Easy"; break;
		case 0x01: $value = "Normal"; break;
		case 0x02: $value = "Insane"; break;
	}
	return $value;
}

function convert_select_mode($value) {
	switch ($value) {
		case 0x00: $value = "Team & race selectable"; break;
		case 0x01: $value = "Team not selectable"; break;
		case 0x03: $value = "Team & race not selectable"; break;
		case 0x04: $value = "Race fixed to random"; break;
		case 0xCC: $value = "Automated Match Making (ladder)"; break;
	}
	return $value;
}

function convert_chat_mode($value, $player='unknown') {
	switch ($value) {
		case 0x00: $value = 'All'; break;
		case 0x01: $value = 'Allies'; break;
		case 0x02: $value = 'Observers'; break;
		case 0xFE: $value = 'The game has been paused by '.$player.'.'; break;
		case 0xFF: $value = 'The game has been resumed by '.$player.'.'; break;
		default: $value -= 2; // this is for private messages
	}
	return $value;
}

function convert_itemid($value) {
	// ignore numeric Item IDs (0x41 - ASCII A, 0x7A - ASCII z)
	if (ord($value{0}) < 0x41 || ord($value{0}) > 0x7A) {
		return 0;
	}
	
	else {
		switch ($value) {
			case 'E002': $value = 'h_Lightning Revenant'; break;
			case 'E004': $value = 'h_Bone Fletcher'; break;
			case 'E005': $value = 'h_Moon Rider'; break;
			case 'E00P': $value = 'h_Twin Head Dragon'; break;
			case 'E01A': $value = 'h_Witch Doctor'; break;
			case 'E01B': $value = 'h_Spectre'; break;
			case 'E01C': $value = 'h_Warlock'; break;
			case 'E01P': $value = 'h_Dummy: SlowTurn-Range'; break;
			case 'E01Y': $value = 'h_Templar Assassin'; break;
			case 'E021': $value = 'h_Dummy: FastTurn-Melee'; break;
			case 'E023': $value = 'h_Dummy: SlowTurn-Melee'; break;
			case 'E024': $value = 'h_Dummy: FastTurn-Range'; break;
			case 'EC45': $value = 'h_Faceless Void'; break;
			case 'EC57': $value = 'h_Venomancer'; break;
			case 'EC77': $value = 'h_Netherdrake'; break;
			case 'Edem': $value = 'h_Anti-Mage'; break;
			case 'Edmm': $value = 'h_Demon Hunter'; break;
			case 'Eevi': $value = 'h_Soul Keeper'; break;
			case 'Eevm': $value = 'h_Soul Keeper'; break;
			case 'Ekee': $value = 'h_Tormented Soul'; break;
			case 'Emns': $value = 'h_Prophet'; break;
			case 'Emoo': $value = 'h_Enchantress'; break;
			case 'Ewar': $value = 'h_Phantom Assassin'; break;
			case 'H000': $value = 'h_Centaur Warchief'; break;
			case 'H001': $value = 'h_Rogueknight'; break;
			case 'H004': $value = 'h_Slayer'; break;
			case 'H008': $value = 'h_Bristleback'; break;
			case 'H00A': $value = 'h_Holy Knight'; break;
			case 'H00D': $value = 'h_Beastmaster'; break;
			case 'H00E': $value = 'h_Dragon Knight'; break;
			case 'H00F': $value = 'h_Dragon Knight'; break;
			case 'H00G': $value = 'h_Dragon Knight'; break;
			case 'H00H': $value = 'h_Oblivion'; break;
			case 'H00I': $value = 'h_Geomancer'; break;
			case 'H00J': $value = 'h_Geomancer'; break;
			case 'H00K': $value = 'h_Goblin Techies'; break;
			case 'H00N': $value = 'h_Dark Seer'; break;
			case 'H00Q': $value = 'h_Sacred Warrior'; break;
			case 'H00R': $value = 'h_Undying'; break;
			case 'H00S': $value = 'h_Storm Spirit'; break;
			case 'H00U': $value = 'h_Invoker'; break;
			case 'H00V': $value = 'h_Gorgon'; break;
			case 'Hamg': $value = 'h_Treant Protector'; break;
			case 'Harf': $value = 'h_Omniknight'; break;
			case 'Hblm': $value = 'h_Keeper of the Light'; break;
			case 'HC49': $value = 'h_Naga Siren'; break;
			case 'HC92': $value = 'h_Stealth Assassin'; break;
			case 'Hhkl': $value = 'h_Paladin'; break;
			case 'Hjai': $value = 'h_Crystal Maiden'; break;
			case 'Hlgr': $value = 'h_Dragon Knight'; break;
			case 'Hmbr': $value = 'h_Lord of Olympia'; break;
			case 'Hmkg': $value = 'h_Ogre Magi'; break;
			case 'Hpal': $value = 'h_Paladin'; break;
			case 'Hpb1': $value = 'h_Paladin'; break;
			case 'Huth': $value = 'h_Ursa Warrior'; break;
			case 'Hvsh': $value = 'h_Bloodseeker'; break;
			case 'Hvwd': $value = 'h_Vengeful Spirit'; break;
			case 'N00B': $value = 'h_Faerie Dragon'; break;
			case 'N00R': $value = 'h_Pit Lord'; break;
			case 'N013': $value = 'h_Lone Druid'; break;
			case 'N014': $value = 'h_Lone Druid'; break;
			case 'N015': $value = 'h_Lone Druid'; break;
			case 'N016': $value = 'h_Troll Warlord'; break;
			case 'N017': $value = 'h_Troll Warlord'; break;
			case 'N01A': $value = 'h_Silencer'; break;
			case 'N01H': $value = 'h_Alchemist'; break;
			case 'N01I': $value = 'h_Alchemist'; break;
			case 'N01J': $value = 'h_Alchemist'; break;
			case 'N01O': $value = 'h_Lone Druid'; break;
			case 'N01T': $value = 'h_Alchemist'; break;
			case 'N01V': $value = 'h_Priestess of the Moon'; break;
			case 'N01W': $value = 'h_Shadow priest'; break;
			case 'N02B': $value = 'h_Troll Warlord'; break;
			case 'Naka': $value = 'h_Bounty Hunter'; break;
			case 'Nbbc': $value = 'h_Juggernaut'; break;
			case 'Nbrn': $value = 'h_Drow Ranger'; break;
			case 'NC00': $value = 'h_Skeleton King'; break;
			case 'Nfir': $value = 'h_Shadow Fiend'; break;
			case 'Npbm': $value = 'h_Pandaren Brewmaster'; break;
			case 'Nrob': $value = 'h_Tinker'; break;
			case 'Ntin': $value = 'h_Tinker'; break;
			case 'O00J': $value = 'h_Spiritbreaker'; break;
			case 'O00P': $value = 'h_Morphling'; break;
			case 'Obla': $value = 'h_Blademaster'; break;
			case 'Ofar': $value = 'h_Tidehunter'; break;
			case 'Ogrh': $value = 'h_Phantom Lancer'; break;
			case 'Opgh': $value = 'h_Axe'; break;
			case 'Orkn': $value = 'h_Shadow Shaman'; break;
			case 'Oshd': $value = 'h_Bane Elemental'; break;
			case 'Otch': $value = 'h_Earthshaker'; break;
			case 'U000': $value = 'h_Nerubian Assassin'; break;
			case 'U006': $value = 'h_Broodmother'; break;
			case 'U007': $value = 'h_Lifestealer'; break;
			case 'U008': $value = 'h_Lycanthrope'; break;
			case 'U00A': $value = 'h_Chaos Knight'; break;
			case 'U00C': $value = 'h_Lifestealer'; break;
			case 'U00E': $value = 'h_Necrolyte'; break;
			case 'U00F': $value = 'h_Butcher'; break;
			case 'U00K': $value = 'h_Sand King'; break;
			case 'U00P': $value = 'h_Obsidian Destroyer'; break;
			case 'Ubal': $value = 'h_Nerubian Weaver'; break;
			case 'UC01': $value = 'h_Queen of Pain'; break;
			case 'UC11': $value = 'h_Magnataur'; break;
			case 'UC18': $value = 'h_Demon Witch'; break;
			case 'UC42': $value = 'h_Doom Bringer'; break;
			case 'UC60': $value = 'h_Necro lic'; break;
			case 'UC76': $value = 'h_Death Prophet'; break;
			case 'UC91': $value = 'h_Slithereen Guard'; break;
			case 'Ucrl': $value = 'h_Stone Giant'; break;
			case 'Udea': $value = 'h_Lord of Avernus'; break;
			case 'Udre': $value = 'h_Night Stalker'; break;
			case 'Uktl': $value = 'h_Enigma'; break;
			case 'Ulic': $value = 'h_Lich'; break;
			case 'Usyl': $value = 'h_Dwarven Sniper'; break;
			case 'A0YT': $value = 'a_Lightning Revenant:Storm Seeker'; break;
			case 'A00N': $value = 'a_Lightning Revenant:Unholy Fervor'; break;
			case 'A00Y': $value = 'a_Lightning Revenant:Chain Lightning'; break;
			case 'A0RY': $value = 'a_Lightning Revenant:Frenzy'; break;
			case 'A04Q': $value = 'a_Bone Fletcher:Death Pact'; break;
			case 'A025': $value = 'a_Bone Fletcher:Wind Walk'; break;
			case 'A030': $value = 'a_Bone Fletcher:Strafe'; break;
			case 'AHfa': $value = 'a_Bone Fletcher:Searing Arrows'; break;
			case 'A062': $value = 'a_Moon Rider:Lunar Blessing'; break;
			case 'A042': $value = 'a_Moon Rider:Lucent Beam'; break;
			case 'A054': $value = 'a_Moon Rider:Eclipse'; break;
			case 'A041': $value = 'a_Moon Rider:Moon Glaive'; break;
			case 'A00U': $value = 'a_Moon Rider:Eclipse'; break;
			case 'A0O7': $value = 'a_Twin Head Dragon:Dual Breath'; break;
			case 'A0O6': $value = 'a_Twin Head Dragon:Ice Path'; break;
			case 'A0O8': $value = 'a_Twin Head Dragon:Auto Fire'; break;
			case 'A0O5': $value = 'a_Twin Head Dragon:Macropyre'; break;
			case 'A0NO': $value = 'a_Witch Doctor:Maledict'; break;
			case 'A0NM': $value = 'a_Witch Doctor:Paralyzing Cask'; break;
			case 'A0NT': $value = 'a_Witch Doctor:Death Ward'; break;
			case 'A0NE': $value = 'a_Witch Doctor:Voodoo Restoration'; break;
			case 'A0NX': $value = 'a_Witch Doctor:Death Ward'; break;
			case 'A0FX': $value = 'a_Spectre:Desolate'; break;
			case 'A0NA': $value = 'a_Spectre:Dispersion'; break;
			case 'A0HW': $value = 'a_Spectre:Spectral Dagger'; break;
			case 'A0H9': $value = 'a_Spectre:Haunt'; break;
			case 'A0J5': $value = 'a_Warlock:Fatal Bonds'; break;
			case 'A0AS': $value = 'a_Warlock:Shadow Word'; break;
			case 'A06P': $value = 'a_Warlock:Upheaval'; break;
			case 'S008': $value = 'a_Warlock:Rain of Chaos'; break;
			case 'A0RP': $value = 'a_Templar Assassin:Psionic Trap'; break;
			case 'A0RO': $value = 'a_Templar Assassin:Psi Blades'; break;
			case 'A0RE': $value = 'a_Templar Assassin:Refraction'; break;
			case 'A0RV': $value = 'a_Templar Assassin:Meld'; break;
			case 'A081': $value = 'a_Faceless Void:Time Lock'; break;
			case 'A0CZ': $value = 'a_Faceless Void:Backtrack'; break;
			case 'A0LK': $value = 'a_Faceless Void:Time Walk'; break;
			case 'A0J1': $value = 'a_Faceless Void:Chronosphere'; break;
			case 'AEsh': $value = 'a_Venomancer:Shadow Strike'; break;
			case 'A0MY': $value = 'a_Venomancer:Poison Sting'; break;
			case 'A0MS': $value = 'a_Venomancer:Plague Ward'; break;
			case 'A013': $value = 'a_Venomancer:Poison Nova'; break;
			case 'A0A6': $value = 'a_Venomancer:Poison Nova'; break;
			case 'A05D': $value = 'a_Netherdrake:Frenzy'; break;
			case 'A080': $value = 'a_Netherdrake:Viper Strike'; break;
			case 'A09V': $value = 'a_Netherdrake:Poison Attack'; break;
			case 'A0MM': $value = 'a_Netherdrake:Corrosive Skin'; break;
			case 'A0KY': $value = 'a_Anti-Mage:Spell Shield'; break;
			case 'A022': $value = 'a_Anti-Mage:Mana Break'; break;
			case 'AEbl': $value = 'a_Anti-Mage:Blink'; break;
			case 'A0E3': $value = 'a_Anti-Mage:Mana Void'; break;
			case 'AEmb': $value = 'a_Demon Hunter:Mana Burn'; break;
			case 'AEim': $value = 'a_Demon Hunter:Immolation'; break;
			case 'AEev': $value = 'a_Demon Hunter:Evasion'; break;
			case 'AEme': $value = 'a_Demon Hunter:Metamorphosis'; break;
			case 'A04L': $value = 'a_Soul Keeper:Soul Steal'; break;
			case 'A0H4': $value = 'a_Soul Keeper:Conjure Image'; break;
			case 'A07Q': $value = 'a_Soul Keeper:Sunder'; break;
			case 'A035': $value = 'a_Tormented Soul:Diabolic Edict'; break;
			case 'A06W': $value = 'a_Tormented Soul:Split Earth'; break;
			case 'A06V': $value = 'a_Tormented Soul:Lightning Storm'; break;
			case 'A06X': $value = 'a_Tormented Soul:Pulse Nova'; break;
			case 'A0AQ': $value = 'a_Tormented Soul:Pulse Nova'; break;
			case 'A06Q': $value = 'a_Prophet:Sprout'; break;
			case 'A01O': $value = 'a_Prophet:Teleportation'; break;
			case 'AEfn': $value = 'a_Prophet:Force of Nature'; break;
			case 'A07X': $value = 'a_Prophet:Wrath of Nature'; break;
			case 'A0AL': $value = 'a_Prophet:Wrath of Nature'; break;
			case 'A0DX': $value = 'a_Enchantress:Enchant'; break;
			case 'A01B': $value = 'a_Enchantress:Nature s Attendants'; break;
			case 'A0DW': $value = 'a_Enchantress:Untouchable'; break;
			case 'A0DY': $value = 'a_Enchantress:Impetus'; break;
			case 'A0YM': $value = 'a_Phantom Assassin:Stifling Dagger'; break;
			case 'A0PL': $value = 'a_Phantom Assassin:Blink Strike'; break;
			case 'A03P': $value = 'a_Phantom Assassin:Blur'; break;
			case 'A03Q': $value = 'a_Phantom Assassin:Coup de grâce'; break;
			case 'A01L': $value = 'a_Centaur Warchief:Great Fortitude'; break;
			case 'A00L': $value = 'a_Centaur Warchief:Double Edge'; break;
			case 'A00V': $value = 'a_Centaur Warchief:Return'; break;
			case 'A00S': $value = 'a_Centaur Warchief:Hoof Stomp'; break;
			case 'A0RZ': $value = 'a_Rogueknight:Storm Bolt'; break;
			case 'A01K': $value = 'a_Rogueknight:Great Cleave'; break;
			case 'A01M': $value = 'a_Rogueknight:Toughness Aura'; break;
			case 'A0WP': $value = 'a_Rogueknight:God s Strength'; break;
			case 'A01F': $value = 'a_Slayer:Dragon Slave'; break;
			case 'A027': $value = 'a_Slayer:Light Strike Array'; break;
			case 'A001': $value = 'a_Slayer:Ultimate'; break;
			case 'A01P': $value = 'a_Slayer:Laguna Blade'; break;
			case 'A09Z': $value = 'a_Slayer:Laguna Blade'; break;
			case 'A0FV': $value = 'a_Bristleback:Warpath'; break;
			case 'A0M3': $value = 'a_Bristleback:Bristleback'; break;
			case 'A0GP': $value = 'a_Bristleback:Quill Spray'; break;
			case 'A0FW': $value = 'a_Bristleback:Viscous Nasal Goo'; break;
			case 'A0LV': $value = 'a_Holy Knight:Test of Faith'; break;
			case 'A069': $value = 'a_Holy Knight:Holy Persuasion'; break;
			case 'A0LT': $value = 'a_Holy Knight:Hand of God'; break;
			case 'A0KM': $value = 'a_Holy Knight:Penitence'; break;
			case 'A0OO': $value = 'a_Beastmaster:Call of the Wild'; break;
			case 'A0O1': $value = 'a_Beastmaster:Wild Axes'; break;
			case 'A0O0': $value = 'a_Beastmaster:Beast Rage'; break;
			case 'A0O2': $value = 'a_Beastmaster:Primal Roar'; break;
			case 'A09D': $value = 'a_Oblivion:Nether Ward'; break;
			case 'A0MT': $value = 'a_Oblivion:Nether Blast'; break;
			case 'A0CE': $value = 'a_Oblivion:Decrepify'; break;
			case 'A0CC': $value = 'a_Oblivion:Life Drain'; break;
			case 'A02Z': $value = 'a_Oblivion:Life Drain'; break;
			case 'A06H': $value = 'a_Goblin Techies:Stasis Trap'; break;
			case 'A05J': $value = 'a_Goblin Techies:Land Mines'; break;
			case 'A06B': $value = 'a_Goblin Techies:Suicide Squad, Attack!'; break;
			case 'A0AK': $value = 'a_Goblin Techies:Remote Mines'; break;
			case 'A0QK': $value = 'a_Dark Seer:Wall of Replica'; break;
			case 'A0R7': $value = 'a_Dark Seer:Surge'; break;
			case 'A0QG': $value = 'a_Dark Seer:Ion Shell'; break;
			case 'A0QE': $value = 'a_Dark Seer:Vacuum'; break;
			case 'A0QR': $value = 'a_Sacred Warrior:Life Break'; break;
			case 'A0QQ': $value = 'a_Sacred Warrior:Berserker s Blood'; break;
			case 'A0QN': $value = 'a_Sacred Warrior:Burning Spear'; break;
			case 'A0QP': $value = 'a_Sacred Warrior:Inner Vitality'; break;
			case 'A0R3': $value = 'a_Undying:Plague'; break;
			case 'A0R5': $value = 'a_Undying:Soul Rip'; break;
			case 'A0QV': $value = 'a_Undying:Raise Dead'; break;
			case 'A01N': $value = 'a_Undying:Heartstopper Aura'; break;
			case 'A0R6': $value = 'a_Storm Spirit:Barrier'; break;
			case 'A0R1': $value = 'a_Storm Spirit:Lightning Grapple'; break;
			case 'A0QY': $value = 'a_Storm Spirit:Electric Rave'; break;
			case 'A0QW': $value = 'a_Storm Spirit:Overload'; break;
			case 'A0VF': $value = 'a_Invoker:Invoke'; break;
			case 'A0VA': $value = 'a_Invoker:Wex'; break;
			case 'A0V9': $value = 'a_Invoker:Exort'; break;
			case 'A0VB': $value = 'a_Invoker:Quas'; break;
			case 'A02V': $value = 'a_Gorgon:Purge'; break;
			case 'A0G2': $value = 'a_Gorgon:Chain Lightning'; break;
			case 'A0MP': $value = 'a_Gorgon:Mana Shield'; break;
			case 'A012': $value = 'a_Gorgon:Split Shot'; break;
			case 'A07Z': $value = 'a_Treant Protector:Overgrowth'; break;
			case 'A01Z': $value = 'a_Treant Protector:Nature s Guise'; break;
			case 'A01V': $value = 'a_Treant Protector:Eyes in the Forest'; break;
			case 'A01U': $value = 'a_Treant Protector:Living Armor'; break;
			case 'A08N': $value = 'a_Omniknight:Purification'; break;
			case 'A08V': $value = 'a_Omniknight:Repel'; break;
			case 'A0ER': $value = 'a_Omniknight:Guardian Angel'; break;
			case 'A06A': $value = 'a_Omniknight:Degen Aura'; break;
			case 'A085': $value = 'a_Keeper of the Light:Illuminate'; break;
			case 'A07Y': $value = 'a_Keeper of the Light:Mana Leak'; break;
			case 'A0MO': $value = 'a_Keeper of the Light:Ignis Fatuus'; break;
			case 'A07N': $value = 'a_Keeper of the Light:Chakra Magic'; break;
			case 'A0EO': $value = 'a_Keeper of the Light:Ignis Fatuus'; break;
			case 'A00E': $value = 'a_Naga Siren:Critical Strike'; break;
			case 'A0BA': $value = 'a_Naga Siren:Ensnare'; break;
			case 'A063': $value = 'a_Naga Siren:Mirror Image'; break;
			case 'A07U': $value = 'a_Naga Siren:Song of the Siren'; break;
			case 'A0DZ': $value = 'a_Stealth Assassin:Backstab'; break;
			case 'A0K9': $value = 'a_Stealth Assassin:Blink Strike'; break;
			case 'A00J': $value = 'a_Stealth Assassin:Permanent Invisibility'; break;
			case 'A0RG': $value = 'a_Stealth Assassin:Smoke Screen'; break;
			case 'A01D': $value = 'a_Crystal Maiden:Frost Nova'; break;
			case 'A04C': $value = 'a_Crystal Maiden:Frostbite'; break;
			case 'AHab': $value = 'a_Crystal Maiden:Brilliance Aura'; break;
			case 'A03R': $value = 'a_Crystal Maiden:Freezing Field'; break;
			case 'A0AV': $value = 'a_Crystal Maiden:Freezing Field'; break;
			case 'A020': $value = 'a_Lord of Olympia:Arc Lightning'; break;
			case 'A0N5': $value = 'a_Lord of Olympia:Static Field'; break;
			case 'A0JC': $value = 'a_Lord of Olympia:Lightning Bolt'; break;
			case 'A07C': $value = 'a_Lord of Olympia:Thundergod s Wrath'; break;
			case 'A06L': $value = 'a_Lord of Olympia:Thundergod s Wrath'; break;
			case 'A011': $value = 'a_Ogre Magi:Ignite'; break;
			case 'A04W': $value = 'a_Ogre Magi:Fireblast'; break;
			case 'A083': $value = 'a_Ogre Magi:Bloodlust'; break;
			case 'A088': $value = 'a_Ogre Magi:Multi Cast'; break;
			case 'A089': $value = 'a_Ogre Magi:Fireblast'; break;
			case 'A007': $value = 'a_Ogre Magi:Ignite 1'; break;
			case 'A08F': $value = 'a_Ogre Magi:Bloodlust'; break;
			case 'A08A': $value = 'a_Ogre Magi:Fireblast'; break;
			case 'A01T': $value = 'a_Ogre Magi:Ignite 2'; break;
			case 'A08G': $value = 'a_Ogre Magi:Bloodlust'; break;
			case 'A08D': $value = 'a_Ogre Magi:Fireblast'; break;
			case 'A00F': $value = 'a_Ogre Magi:Ignite 3'; break;
			case 'A08I': $value = 'a_Ogre Magi:Bloodlust'; break;
			case 'A059': $value = 'a_Ursa Warrior:Overpower'; break;
			case 'A0LC': $value = 'a_Ursa Warrior:Enrage'; break;
			case 'A03Y': $value = 'a_Ursa Warrior:Earthshock'; break;
			case 'ANic': $value = 'a_Ursa Warrior:Fury Swipes'; break;
			case 'A0LH': $value = 'a_Bloodseeker:Rupture'; break;
			case 'A0I8': $value = 'a_Bloodseeker:Strygwyr s Thirst'; break;
			case 'A0EC': $value = 'a_Bloodseeker:Bloodrage'; break;
			case 'A0LE': $value = 'a_Bloodseeker:Blood Bath'; break;
			case 'A02A': $value = 'a_Vengeful Spirit:Magic Missile'; break;
			case 'ACac': $value = 'a_Vengeful Spirit:Command Aura'; break;
			case 'A0AP': $value = 'a_Vengeful Spirit:Terror'; break;
			case 'A0IN': $value = 'a_Vengeful Spirit:Nether Swap'; break;
			case 'A0S8': $value = 'a_Faerie Dragon:Dream Coil'; break;
			case 'A0SC': $value = 'a_Faerie Dragon:Waning Rift'; break;
			case 'A0SB': $value = 'a_Faerie Dragon:Phase Shift'; break;
			case 'A0S9': $value = 'a_Faerie Dragon:Illusory Orb'; break;
			case 'A0QT': $value = 'a_Pit Lord:Expulsion'; break;
			case 'A0RA': $value = 'a_Pit Lord:Pit of Malice'; break;
			case 'A01I': $value = 'a_Pit Lord:Firestorm'; break;
			case 'A0R0': $value = 'a_Pit Lord:Dark Rift'; break;
			case 'A0LZ': $value = 'a_Silencer:Glaives of Wisdom'; break;
			case 'A0MC': $value = 'a_Silencer:Last Word'; break;
			case 'A0KD': $value = 'a_Silencer:Curse of the Silent'; break;
			case 'A0L3': $value = 'a_Silencer:Global Silence'; break;
			case 'A0AA': $value = 'a_Lone Druid:Rabid'; break;
			case 'A0AG': $value = 'a_Lone Druid:True Form'; break;
			case 'A0AB': $value = 'a_Lone Druid:Rabid'; break;
			case 'A0AC': $value = 'a_Lone Druid:Rabid'; break;
			case 'A0AD': $value = 'a_Lone Druid:Rabid'; break;
			case 'A0AE': $value = 'a_Lone Druid:Rabid'; break;
			case 'A0L8': $value = 'a_Priestess of the Moon:Elune s Arrow'; break;
			case 'A0LN': $value = 'a_Priestess of the Moon:Leap'; break;
			case 'A0KV': $value = 'a_Priestess of the Moon:Starfall'; break;
			case 'A0KU': $value = 'a_Priestess of the Moon:Moonlight Shadow'; break;
			case 'A0OS': $value = 'a_Shadow priest:Shallow Grave'; break;
			case 'A0OR': $value = 'a_Shadow priest:Shadow Wave'; break;
			case 'A0NV': $value = 'a_Shadow priest:Weave'; break;
			case 'A0NQ': $value = 'a_Shadow priest:Poison Touch'; break;
			case 'A000': $value = 'a_Bounty Hunter:Jinada'; break;
			case 'A07A': $value = 'a_Bounty Hunter:Wind Walk'; break;
			case 'A004': $value = 'a_Bounty Hunter:Shuriken Toss'; break;
			case 'A0B4': $value = 'a_Bounty Hunter:Track'; break;
			case 'A047': $value = 'a_Juggernaut:Healing Ward'; break;
			case 'A05G': $value = 'a_Juggernaut:Blade Fury'; break;
			case 'A00K': $value = 'a_Juggernaut:Blade Dance'; break;
			case 'A0M1': $value = 'a_Juggernaut:Omnislash'; break;
			case 'A0QB': $value = 'a_Drow Ranger:Silence'; break;
			case 'A029': $value = 'a_Drow Ranger:Trueshot Aura'; break;
			case 'A026': $value = 'a_Drow Ranger:Frost Arrows'; break;
			case 'A0VC': $value = 'a_Drow Ranger:Marksmanship'; break;
			case 'AHtb': $value = 'a_Skeleton King:Hellfire Blast'; break;
			case 'AUav': $value = 'a_Skeleton King:Vampiric Aura'; break;
			case 'A01Y': $value = 'a_Skeleton King:Reincarnation'; break;
			case 'A0FU': $value = 'a_Shadow Fiend:Presence of the Dark Lord'; break;
			case 'A0BR': $value = 'a_Shadow Fiend:Necromastery'; break;
			case 'A0HE': $value = 'a_Shadow Fiend:Requiem of Souls'; break;
			case 'A0EY': $value = 'a_Shadow Fiend:Shadowraze'; break;
			case 'A06M': $value = 'a_Pandaren Brewmaster:Thunder Clap'; break;
			case 'Acdh': $value = 'a_Pandaren Brewmaster:Drunken Haze'; break;
			case 'A0MX': $value = 'a_Pandaren Brewmaster:Drunken Brawler'; break;
			case 'A0MQ': $value = 'a_Pandaren Brewmaster:Primal Split'; break;
			case 'ANsy': $value = 'a_Tinker:Pocket Factory'; break;
			case 'ANcs': $value = 'a_Tinker:Cluster Rockets'; break;
			case 'ANeg': $value = 'a_Tinker:Engineering Upgrade'; break;
			case 'ANrg': $value = 'a_Tinker:Robo-Goblin'; break;
			case 'ANs1': $value = 'a_Tinker:Pocket Factory'; break;
			case 'ANc1': $value = 'a_Tinker:Cluster Rockets'; break;
			case 'ANs2': $value = 'a_Tinker:Pocket Factory'; break;
			case 'ANc2': $value = 'a_Tinker:Cluster Rockets'; break;
			case 'ANs3': $value = 'a_Tinker:Pocket Factory'; break;
			case 'ANc3': $value = 'a_Tinker:Cluster Rockets'; break;
			case 'A065': $value = 'a_Tinker:Rearm'; break;
			case 'A049': $value = 'a_Tinker:Laser'; break;
			case 'A05E': $value = 'a_Tinker:Heat Seeking Missile'; break;
			case 'A0BQ': $value = 'a_Tinker:March of the Machines'; break;
			case 'A0ML': $value = 'a_Spiritbreaker:Charge of Darkness'; break;
			case 'A0G4': $value = 'a_Spiritbreaker:Nether Strike'; break;
			case 'A0G5': $value = 'a_Spiritbreaker:Greater Bash'; break;
			case 'A0ES': $value = 'a_Spiritbreaker:Empowering Haste'; break;
			case 'A0G6': $value = 'a_Morphling:Adaptive Strike'; break;
			case 'A0G8': $value = 'a_Morphling:Replicate'; break;
			case 'A0KX': $value = 'a_Morphling:Morph'; break;
			case 'A0FN': $value = 'a_Morphling:Waveform'; break;
			case 'AOwk': $value = 'a_Blademaster:Wind Walk'; break;
			case 'AOmi': $value = 'a_Blademaster:Mirror Image'; break;
			case 'AOww': $value = 'a_Blademaster:Bladestorm'; break;
			case 'A03Z': $value = 'a_Tidehunter:Ravage'; break;
			case 'A044': $value = 'a_Tidehunter:Anchor Smash'; break;
			case 'A046': $value = 'a_Tidehunter:Gush'; break;
			case 'A04E': $value = 'a_Tidehunter:Kraken Shell'; break;
			case 'A0DB': $value = 'a_Phantom Lancer:Juxtapose'; break;
			case 'A0DA': $value = 'a_Phantom Lancer:Spirit Lance'; break;
			case 'A0D7': $value = 'a_Phantom Lancer:Doppelwalk'; break;
			case 'A0YK': $value = 'a_Phantom Lancer:Phantom Edge'; break;
			case 'A0I6': $value = 'a_Axe:Berserker s Call'; break;
			case 'A0S1': $value = 'a_Axe:Battle Hunger'; break;
			case 'A0C6': $value = 'a_Axe:Counter Helix'; break;
			case 'A0E2': $value = 'a_Axe:Culling Blade'; break;
			case 'A00P': $value = 'a_Shadow Shaman:Shackles'; break;
			case 'A0RX': $value = 'a_Shadow Shaman:Voodoo'; break;
			case 'A010': $value = 'a_Shadow Shaman:Forked Lightning'; break;
			case 'A00H': $value = 'a_Shadow Shaman:Mass Serpent Ward'; break;
			case 'A0A1': $value = 'a_Shadow Shaman:Mass Serpent Ward'; break;
			case 'A02Q': $value = 'a_Bane Elemental:Fiend s Grip'; break;
			case 'A0GK': $value = 'a_Bane Elemental:Brain Sap'; break;
			case 'A04V': $value = 'a_Bane Elemental:Enfeeble'; break;
			case 'A04Y': $value = 'a_Bane Elemental:Nightmare'; break;
			case 'A0DH': $value = 'a_Earthshaker:Echo Slam'; break;
			case 'A0DL': $value = 'a_Earthshaker:Enchant Totem'; break;
			case 'A0SK': $value = 'a_Earthshaker:Fissure'; break;
			case 'A0DJ': $value = 'a_Earthshaker:Aftershock'; break;
			case 'A0X7': $value = 'a_Nerubian Assassin:Impale'; break;
			case 'A09U': $value = 'a_Nerubian Assassin:Vendetta'; break;
			case 'A02K': $value = 'a_Nerubian Assassin:Mana Burn'; break;
			case 'A02L': $value = 'a_Nerubian Assassin:Spiked Carapace'; break;
			case 'A0WQ': $value = 'a_Broodmother:Insatiable Hunger'; break;
			case 'A0BK': $value = 'a_Broodmother:Incapacitating Bite'; break;
			case 'A0BH': $value = 'a_Broodmother:Spawn Spiderlings'; break;
			case 'A0BG': $value = 'a_Broodmother:Spin Web'; break;
			case 'A0JQ': $value = 'a_Lifestealer:Feast'; break;
			case 'A06Y': $value = 'a_Lifestealer:Anabolic Frenzy'; break;
			case 'A01E': $value = 'a_Lifestealer:Poison Sting'; break;
			case 'A028': $value = 'a_Lifestealer:Rage'; break;
			case 'A03E': $value = 'a_Lycanthrope:Feral Heart'; break;
			case 'A02G': $value = 'a_Lycanthrope:Howl'; break;
			case 'A093': $value = 'a_Lycanthrope:Shapeshift'; break;
			case 'A03D': $value = 'a_Lycanthrope:Summon Wolves'; break;
			case 'A03O': $value = 'a_Chaos Knight:Phantasm'; break;
			case 'A03N': $value = 'a_Chaos Knight:Critical Strike'; break;
			case 'A0RW': $value = 'a_Chaos Knight:Blink Strike'; break;
			case 'A055': $value = 'a_Chaos Knight:Chaos Bolt'; break;
			case 'A0SW': $value = 'a_Lifestealer:Infest'; break;
			case 'A0SS': $value = 'a_Lifestealer:Feast'; break;
			case 'A0T2': $value = 'a_Lifestealer:Rage'; break;
			case 'A0TI': $value = 'a_Lifestealer:Open Wounds'; break;
			case 'A067': $value = 'a_Necrolyte:Reaper s Scythe'; break;
			case 'A05V': $value = 'a_Necrolyte:Death Pulse'; break;
			case 'A060': $value = 'a_Necrolyte:Sadist'; break;
			case 'AIcd': $value = 'a_Necrolyte:Diffusion Aura'; break;
			case 'A08P': $value = 'a_Necrolyte:Reaper s Scythe'; break;
			case 'A06I': $value = 'a_Butcher:Meat Hook'; break;
			case 'A06D': $value = 'a_Butcher:Flesh Heap'; break;
			case 'A0FL': $value = 'a_Butcher:Dismember'; break;
			case 'A06K': $value = 'a_Butcher:Rot'; break;
			case 'A06R': $value = 'a_Sand King:Epicenter'; break;
			case 'A0H0': $value = 'a_Sand King:Sand Storm'; break;
			case 'A06O': $value = 'a_Sand King:Burrowstrike'; break;
			case 'A0FA': $value = 'a_Sand King:Caustic Finale'; break;
			case 'A0F1': $value = 'a_Sand King:Burrowstrike'; break;
			case 'A0F3': $value = 'a_Sand King:Epicenter'; break;
			case 'A0OK': $value = 'a_Obsidian Destroyer:Sanity s Eclipse'; break;
			case 'A0OJ': $value = 'a_Obsidian Destroyer:Astral Imprisonment'; break;
			case 'A0OI': $value = 'a_Obsidian Destroyer:Arcane Orb'; break;
			case 'A0IF': $value = 'a_Obsidian Destroyer:Essence Aura'; break;
			case 'A00T': $value = 'a_Nerubian Weaver:Watcher'; break;
			case 'A0CA': $value = 'a_Nerubian Weaver:Shukuchi'; break;
			case 'A0CG': $value = 'a_Nerubian Weaver:Geminate Attack'; break;
			case 'A0CT': $value = 'a_Nerubian Weaver:Time Lapse'; break;
			case 'A0Q7': $value = 'a_Queen of Pain:Shadow Strike'; break;
			case 'A0ME': $value = 'a_Queen of Pain:Blink'; break;
			case 'A04A': $value = 'a_Queen of Pain:Scream of Pain'; break;
			case 'A00O': $value = 'a_Queen of Pain:Sonic Wave'; break;
			case 'A0AF': $value = 'a_Queen of Pain:Sonic Wave'; break;
			case 'A02S': $value = 'a_Magnataur:Shockwave'; break;
			case 'A037': $value = 'a_Magnataur:Empower'; break;
			case 'A024': $value = 'a_Magnataur:Mighty Swing'; break;
			case 'A06F': $value = 'a_Magnataur:Reverse Polarity'; break;
			case 'A0X5': $value = 'a_Demon Witch:Impale'; break;
			case 'A0MN': $value = 'a_Demon Witch:Voodoo'; break;
			case 'A02N': $value = 'a_Demon Witch:Mana Drain'; break;
			case 'A095': $value = 'a_Demon Witch:Finger of Death'; break;
			case 'A09W': $value = 'a_Demon Witch:Finger of Death'; break;
			case 'A094': $value = 'a_Doom Bringer:LVL? Death'; break;
			case 'A05Y': $value = 'a_Doom Bringer:Devour'; break;
			case 'A0FE': $value = 'a_Doom Bringer:Scorched Earth'; break;
			case 'A0MU': $value = 'a_Doom Bringer:Doom'; break;
			case 'A0A2': $value = 'a_Doom Bringer:Doom'; break;
			case 'A08X': $value = 'a_Necro lic:Grave Chill'; break;
			case 'A0VY': $value = 'a_Necro lic:Soul Assumption'; break;
			case 'A0VX': $value = 'a_Necro lic:Gravekeeper s Cloak'; break;
			case 'A07K': $value = 'a_Necro lic:Raise Revenants'; break;
			case 'A02C': $value = 'a_Death Prophet:Witchcraft'; break;
			case 'A02M': $value = 'a_Death Prophet:Carrion Swarm'; break;
			case 'A0P6': $value = 'a_Death Prophet:Silence'; break;
			case 'A073': $value = 'a_Death Prophet:Exorcism'; break;
			case 'A07H': $value = 'a_Death Prophet:Silence'; break;
			case 'A03J': $value = 'a_Death Prophet:Exorcism'; break;
			case 'A06N': $value = 'a_Death Prophet:Carrion Swarm'; break;
			case 'A07I': $value = 'a_Death Prophet:Silence'; break;
			case 'A04J': $value = 'a_Death Prophet:Exorcism'; break;
			case 'A072': $value = 'a_Death Prophet:Carrion Swarm'; break;
			case 'A07J': $value = 'a_Death Prophet:Silence'; break;
			case 'A04M': $value = 'a_Death Prophet:Exorcism'; break;
			case 'A074': $value = 'a_Death Prophet:Carrion Swarm'; break;
			case 'A07M': $value = 'a_Death Prophet:Silence'; break;
			case 'A04N': $value = 'a_Death Prophet:Exorcism'; break;
			case 'A078': $value = 'a_Death Prophet:Carrion Swarm'; break;
			case 'A05C': $value = 'a_Slithereen Guard:Sprint'; break;
			case 'A01W': $value = 'a_Slithereen Guard:Slithereen Crush'; break;
			case 'A0JJ': $value = 'a_Slithereen Guard:Bash'; break;
			case 'A034': $value = 'a_Slithereen Guard:Amplify Damage'; break;
			case 'A0BU': $value = 'a_Stone Giant:Craggy Exterior'; break;
			case 'A0CY': $value = 'a_Stone Giant:Grow'; break;
			case 'A0LL': $value = 'a_Stone Giant:Avalanche'; break;
			case 'A0BZ': $value = 'a_Stone Giant:Toss'; break;
			case 'A0NS': $value = 'a_Lord of Avernus:Borrowed Time'; break;
			case 'A0MG': $value = 'a_Lord of Avernus:Frostmourne'; break;
			case 'A0I3': $value = 'a_Lord of Avernus:Death Coil'; break;
			case 'A0MF': $value = 'a_Lord of Avernus:Aphotic Shield'; break;
			case 'A086': $value = 'a_Night Stalker:Hunter in the Night'; break;
			case 'A08E': $value = 'a_Night Stalker:Crippling Fear'; break;
			case 'A03K': $value = 'a_Night Stalker:Darkness'; break;
			case 'A02H': $value = 'a_Night Stalker:Void'; break;
			case 'A0I7': $value = 'a_Enigma:Malefice'; break;
			case 'A0B7': $value = 'a_Enigma:Conversion'; break;
			case 'A0B1': $value = 'a_Enigma:Midnight Pulse'; break;
			case 'A0BY': $value = 'a_Enigma:Black Hole'; break;
			case 'A053': $value = 'a_Lich:Dark Ritual'; break;
			case 'A07F': $value = 'a_Lich:Frost Nova'; break;
			case 'A08R': $value = 'a_Lich:Frost Armor'; break;
			case 'A05T': $value = 'a_Lich:Chain Frost'; break;
			case 'A08H': $value = 'a_Lich:Chain Frost'; break;
			case 'A04P': $value = 'a_Dwarven Sniper:Assassinate'; break;
			case 'A03U': $value = 'a_Dwarven Sniper:Take Aim'; break;
			case 'A03S': $value = 'a_Dwarven Sniper:Headshot'; break;
			case 'A064': $value = 'a_Dwarven Sniper:ScatterShot'; break;
			case 'Aamk': $value = 'a_Any:Attribute Bonus'; break;
			case 'A0MV': $value = 'a_Soul Keeper:Metamorphosis'; break;
			case 'A03G': $value = 'a_Dragon Knight:Elder Dragon Form'; break;
			case 'A03F': $value = 'a_Dragon Knight:Breathe Fire'; break;
			case 'A0AR': $value = 'a_Dragon Knight:Dragon Tail'; break;
			case 'A0CL': $value = 'a_Dragon Knight:Dragon Blood'; break;
			case 'A0N8': $value = 'a_Geomancer:Poof'; break;
			case 'A0NB': $value = 'a_Geomancer:Earthbind'; break;
			case 'A0N7': $value = 'a_Geomancer:Geostrike'; break;
			case 'A0MW': $value = 'a_Geomancer:Divided We Stand'; break;
			case 'AHhb': $value = 'a_Paladin:Holy Light'; break;
			case 'AHds': $value = 'a_Paladin:Divine Shield'; break;
			case 'AHre': $value = 'a_Paladin:Resurrection'; break;
			case 'AHad': $value = 'a_Paladin:Devotion Aura'; break;
			case 'A0A5': $value = 'a_Lone Druid:Summon Spirit Bear'; break;
			case 'A0A8': $value = 'a_Lone Druid:Synergy'; break;
			case 'ANsw': $value = 'a_Lone Druid:Summon Hawk'; break;
			case 'ANst': $value = 'a_Lone Druid:Stampede'; break;
			case 'A0BE': $value = 'a_Troll Warlord:Berserker Rage'; break;
			case 'A0BC': $value = 'a_Troll Warlord:Blind'; break;
			case 'A0BD': $value = 'a_Troll Warlord:Fervor'; break;
			case 'A0BB': $value = 'a_Troll Warlord:Rampage'; break;
			case 'A0J6': $value = 'a_Alchemist:Unstable Concoction'; break;
			case 'A0IL': $value = 'a_Alchemist:Acid Spray'; break;
			case 'A0O3': $value = 'a_Alchemist:Goblin s Greed'; break;
			case 'ANcr': $value = 'a_Alchemist:Chemical Rage'; break;
			case 'A0NR': $value = 'a_Any:Attribute Bonus'; break;
			case 'AOcr': $value = 'a_Any:Critical Strike'; break;
		}
		return $value;
	}
}

function convert_buildingid($value) {
	// non-ASCII ItemIDs
	if (ord($value{0}) < 0x41 || ord($value{0}) > 0x7A) {
		return 0;
	}
	
	switch ($value) {
		case 'halt': $value = 'Altar of Kings'; break;
		case 'harm': $value = 'Workshop'; break;
		case 'hars': $value = 'Arcane Sanctum'; break;
		case 'hbar': $value = 'Barracks'; break;
		case 'hbla': $value = 'Blacksmith'; break;
		case 'hhou': $value = 'Farm'; break;
		case 'hgra': $value = 'Gryphon Aviary'; break;
		case 'hwtw': $value = 'Scout Tower'; break;
		case 'hvlt': $value = 'Arcane Vault'; break;
		case 'hlum': $value = 'Lumber Mill'; break;
		case 'htow': $value = 'Town Hall'; break;

		case 'etrp': $value = 'Ancient Protector'; break;
		case 'etol': $value = 'Tree of Life'; break;
		case 'edob': $value = 'Hunter\'s Hall'; break;
		case 'eate': $value = 'Altar of Elders'; break;
		case 'eden': $value = 'Ancient of Wonders'; break;
		case 'eaoe': $value = 'Ancient of Lore'; break;
		case 'eaom': $value = 'Ancient of War'; break;
		case 'eaow': $value = 'Ancient of Wind'; break;
		case 'edos': $value = 'Chimaera Roost'; break;
		case 'emow': $value = 'Moon Well'; break;

		case 'oalt': $value = 'Altar of Storms'; break;
		case 'obar': $value = 'Barracks'; break;
		case 'obea': $value = 'Beastiary'; break;
		case 'ofor': $value = 'War Mill'; break;
		case 'ogre': $value = 'Great Hall'; break;
		case 'osld': $value = 'Spirit Lodge'; break;
		case 'otrb': $value = 'Orc Burrow'; break;
		case 'orbr': $value = 'Reinforced Orc Burrow'; break;
		case 'otto': $value = 'Tauren Totem'; break;
		case 'ovln': $value = 'Voodoo Lounge'; break;
		case 'owtw': $value = 'Watch Tower'; break;

		case 'uaod': $value = 'Altar of Darkness'; break;
		case 'unpl': $value = 'Necropolis'; break;
		case 'usep': $value = 'Crypt'; break;
		case 'utod': $value = 'Temple of the Damned'; break;
		case 'utom': $value = 'Tomb of Relics'; break;
		case 'ugol': $value = 'Haunted Gold Mine'; break;
		case 'uzig': $value = 'Ziggurat'; break;
		case 'ubon': $value = 'Boneyard'; break;
		case 'usap': $value = 'Sacrificial Pit'; break;
		case 'uslh': $value = 'Slaughterhouse'; break;
		case 'ugrv': $value = 'Graveyard'; break;

		default: $value = 0;
	}
	return $value;
}

function convert_action($value) {
	switch ($value) {
		case 'rightclick': $value = 'Right click'; break;
		case 'select': $value = 'Select / deselect'; break;
		case 'selecthotkey': $value = 'Select group hotkey'; break;
		case 'assignhotkey': $value = 'Assign group hotkey'; break;
		case 'ability': $value = 'Use ability'; break;
		case 'basic': $value = 'Basic commands'; break;
		case 'buildtrain': $value = 'Build / train'; break;
		case 'buildmenu': $value = 'Enter build submenu'; break;
		case 'heromenu': $value = 'Enter hero\'s abilities submenu'; break;
		case 'subgroup': $value = 'Select subgroup'; break;
		case 'item': $value = 'Give item / drop item'; break;
		case 'removeunit': $value = 'Remove unit from queue'; break;
		case 'esc': $value = 'ESC pressed'; break;
	}
	return $value;
}

function convert_time($value) {
	$output = sprintf('%02d', intval($value/60000)).':';
	$value = $value%60000;
	$output .= sprintf('%02d', intval($value/1000));
	
	return $output;
}

function convert_yesno($value) {
	if (!$value)
		return 'No';
	
	return 'Yes';
}

?>
