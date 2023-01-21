<?php
	ArghPanel::begin_tag(Lang::HALL_OF_FAME);
	
	$seasons = array(
		//12 => 2010,
		11 => 2010,
		10 => 2009,
		9 => 2009,
		8 => 2009,
		7 => 2008,
		6 => 2008,
		5 => 2007,
		4 => 2007,
		3 => 2006,
		2 => 2006,
		1 => 2006,
	);
	
	$winners = array(
		//Saison
		1 => array(
			'kS' 	=> array('Krabe', '1mb4dot4', 'Maldejambes', 'Kaowru', 'Sonsaku', 'Dominating', 'Carnage)', 'Velo'),
			'TGGT' 	=> array('Jester', 'Radensson', 'Dolvean', 'Odul', 'Kiram', 'Xplo', 'Tharkis', 'Kirua'),
			'Argh' 	=> array('Thunderbolt_', 'Buezito', 'Fightwar', 'Muto.Kenji', 'Redagay', 'Anodranador', 'Vieudep'),
			'7o'	=> array('Inferno114', 'Zymok', 'Hadora', 'Mika-', 'Cocofujin', 'Pyrox', 'Haziel', 'Hiko', 'Kiasyd'),
		),
		2 => array(
			'D4' 	=> array('Fatalerror', 'Amyga', 'Krabe', 'Naika', 'Rwar'),
			'mOpD' 	=> array('Soycd', 'Immersion', 'Kaowru', 'Maldejambes', '1mb4dot4', 'Velo', 'Karader', 'Sonsaku', 'Inferno114'),
			'aAa' 	=> array('Radensson', 'Xplo', 'Odkl', 'Jester', 'Kirdy', 'Kiram')
		),
		3 => array(
			'aAa' 	=> array('Radensson', 'Xplo', 'Jester', 'Kirdy', 'Kiram', 'Odul', 'Toto'),
			'D4' 	=> array('Rwar', 'Krabe', 'Sonsaku', 'D0ctor', 'Dada', 'Thunderbolt_', 'Naika'),
			'mOpD' 	=> array('Soycd', 'Immersion', 'Kaowru', 'Maldejambes', '1mb4dot4', 'Velo', 'Karader', 'i)arkarax4', 'Inferno114')
		),
		4 => array(
			'D4' 	=> array('Fatalerror', 'Jorge', 'Rwar', 'i)arkarax4', 'Krabe', 'D0ctor', 'Radensson'),
			'aAa' 	=> array('Jester', 'Kiram', 'Odul', 'Season-of-mist', 'Toto', 'Xplo', 'Sendo', 'Dolvean'),
			'WodW' 	=> array('Lliew', 'Drachka', 'Elyoya', 'Aurelien', 'Gambit', 'User', 'New', 'Jey')
		),
		5 => array(
			'rB' 	=> array('i)arkarax4', 'Fatalerror', 'Silentcat', 'Jorge', 'Krabe', 'D0ctor', 'Radensson', 'Skam'),
			'aAa' 	=> array('Jester', 'Kiram', 'Odul', 'Toto', 'Xplo', 'Sendo', 'Amyga', 'Kirdy'),
			'Argh' 	=> array('Thunderbolt_', 'Naika', 'Buezito', 'Soycd', 'Inferno114', 'Kaowru', 'Cold', 'Mika', 'Balthy')
		),
		6 => array(
			'srs' 	=> array('i)arkarax4', 'Silentcat', 'Jorge', 'Jester', 'Kiram'),
			'Argh' 	=> array('Thunderbolt_', 'Naika', 'Buezito', 'Drep', 'Inferno114', 'Balthy', 'Aki', 'Kirdy'),
			'wL' 	=> array('Shiba', 'Hadora', 'Kiki', 'Maryblue', 'Kaoru', 'Lov', 'Claguerre', 'Vieudep')
		),
		7 => array(
			'Argh' 	=> array('Matrice', 'Hulla', 'Ptinoobz', 'Aki', 'Rollmops', 'Jack'),
			'srs' 	=> array('i)arkarax4', 'Neosia', 'Jester', 'Soycd', 'Silentcat', 'Jorge', 'Baja'),
			'oXz' 	=> array('Yogourt', 'Toto', 'Margeta', 'Xplo', 'Drep', 'Malda', 'Amyga', 'Odul')
		),
		8 => array(
			'srs' => array('i)arkarax4', 'Jester', 'Ptinoobz', 'Silentcat', 'Baja', 'Gordan', 'Yogourt'),
			'Argh' => array('Matrice', 'Hulla', 'Thunderbolt_', 'Jack', 'Teky', 'TontonXX', 'Rollmops'),
			'wL' => array('Kiki', 'Claguerre', 'Kaoru', 'Maryblue', 'Capten', 'Shiba', 'Kevouf', 'Bodom')
		),
		9 => array(
			'aAa' => array('R4gn4r0x', 'Toto', 'Dhany', 'Roll', 'Soycd', 'Odul'),
			'Xeo.int' => array('Nozg', 'Pajkatt', 'Nilk', 'Sond', 'Snyft', 'Swoopie', 'Lacoste'),
			'Xeo.fr' => array('Margeta', 'Lliew', 'New', 'Yogourt', 'Lemaune', 'Neosia')
		),
		10 => array(
			'aAa' => array('Dhany', 'Toto', 'Harts-', 'Maldejambes', 'i)arkarax4', 'Amyga', 'Claguerre'),
			'TGGT' => array('Jester', 'Kiram', 'K-rott', 'Soycd', 'Radensson', 'TontonXX', 'Ptinoobz'),
			'DIV' => array('GodGoblin', 'Popo', 'Jesus_quintana', 'Gojiro)', 'Todo)', 'Mouse-of-dota', 'o.o_man', 'Voodz')
		),
		11 => array(
			'aAa' => array('Dhany', 'Toto', 'R4gn4r0x', 'Maldejambes', 'RollmopS', 'Amyga'),
			'IE' => array('Renxiao', 'Morbix', 'Virus', 'E.S.CN', 'Monster', 'Saberstyle'),
			'Tribal' => array('Ph0eNiiX', 'Neosia', 'New', 'YoYa', 'CoMbaL', 'Margeta', 'Todo)', 'LlieW')
		),
	);/*
		12 => array(
			'Mojawi' => array('Dhany', 'Toto', 'R4gn4r0x', 'Maldejambes', 'RollmopS', 'Amyga'),
			'Wodw' => array('Renxiao', 'Morbix', 'Virus', 'E.S.CN', 'Monster', 'Saberstyle'),
			'NFO' => array('Ph0eNiiX', 'Neosia', 'New', 'YoYa', 'CoMbaL', 'Margeta', 'Todo)', 'LlieW')
		),*/
	
	echo '<table class="simple">';
	foreach ($seasons as $season => $year) {
		echo '<tr><td colspan="2">'.Lang::ARGH_DOTA_LEAGUE.' #'.$season.'</td></tr>';
		echo '<tr><td colspan="2" class="line">&nbsp;</td></tr>';
		
		$teams = $winners[$season];
		$i = 1;
		foreach ($teams as $tag => $players) {
			echo '<tr><td>';
			if ($i == 1) {
				echo '<img src="first.gif" alt="1" /> <b>'.$tag.'</b></td><td>';
				$i++;
			} elseif ($i == 2) {
				echo '<img src="second.gif" alt="2" /> <b>'.$tag.'</b></td><td>';
				$i++;
			} else {
				echo '<img src="third.gif" alt="3" /> <b>'.$tag.'</b></td><td>';
			}
			$j = 0;
			foreach ($players as $player) {
				$alt = ($j++ % 2) ? 'DDDDFF' : 'DDFFDD';
				echo '<font color="#'.$alt.'">'.$player.'</font>&nbsp;';
			}
			echo '</td></tr>';
		}
		echo '<tr><td colspan="2">&nbsp;</td></tr>';
	}
	echo '</table>';
	
	ArghPanel::end_tag();
?>