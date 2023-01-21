<?php
	ArghSession::exit_if_not_logged();
?>

<script language="javascript">
	function vote(match_id) {
		var sel = document.getElementById('sel' + match_id);
		
		var choice = sel.options[sel.selectedIndex].value;
		var descr = sel.options[sel.selectedIndex].text;
		
		$.get('ajax/req_pronostic.php',
			{
				match_id: match_id,
				vote: choice
			}, function(data) {
				if (data == 1) {
					$("#cell" + match_id).hide().html('<?php echo Lang::VOTED; ?>: ' + descr).fadeIn();
				}
			}
		);
		
	}
</script>

<?php
	ArghPanel::begin_tag(Lang::DIVISION_CHOICE);
	echo Lang::DIVISION;
?>:
	<form method="POST" action="?f=league_pronostics"><select name="divi">
	<?php
	
	$req = "SELECT * FROM lg_divisions ORDER BY nom ASC";
	$t = mysql_query($req);
	while ($l = mysql_fetch_object($t)) {
		echo '<option'.attr_($_POST['divi'], $l->nom).' value="'.$l->nom.'">'.$l->nom.'</option>';
	}
	?>
	</select> <input type="submit" value="<?php echo Lang::VALIDATE; ?>"></form>
<?php
	ArghPanel::end_tag();
	if (isset($_POST['divi'])) {
	
	ArghPanel::begin_tag(Lang::PRONOSTICS);
?>

<table class="listing">
	<colgroup>
		<col width="25%" />
		<col width="25%" />
		<col width="50%" />
	</colgroup>
<?php
	$req = "SELECT * FROM lg_divisions WHERE nom = '".mysql_real_escape_string($_POST['divi'])."' ORDER BY id ASC";
	$t = mysql_query($req);
	while ($l = mysql_fetch_object($t)) {
		//Titre division
		echo '<tr><td colspan="5">
		<span class="biggest">'.Lang::DIVISION.' '.$l->nom.'</span></td></td></tr>';
		echo '<tr><td colspan="5">&nbsp;</td></td></tr>';
		
		//Boucle journées
		for ($i = 1; $i <= 9; $i++) {
			$sreq="	SELECT m.*, c1.tag AS tag1, c2.tag AS tag2
					FROM lg_matchs m, lg_clans c1, lg_clans c2
					WHERE m.j = '".$i."'
					AND m.divi = '".$l->nom."'
					AND m.team1 = c1.id
					AND m.team2 = c2.id";
			$st = mysql_query($sreq);
			echo '<tr><td><b>'.Lang::DAY.' '.$i.'</b></td><td><b>'.Lang::STATUS.'</b></td><td><b>'.Lang::MY_VOTE.'</b></td><td colspan="2"></td></tr>';
			echo '<tr><td class="line" colspan="5"></td></tr>';
			$k = 0;
			while ($sl=mysql_fetch_object($st)) {
				$alt = Alternator::get_alternation($k);
				switch ($sl->etat) {
				default:
					$winner = 5;
					$msg = Lang::CLOSED;
					break;
				case 2:
				case 4:
				case 7:
				case 8:
					$winner = 1;
					$msg = Lang::WIN.' '.$sl->tag1;
					break;
				case 6:
				case 11:
					$winner = 3;
					$msg = Lang::DRAW;
					break;
				case 3:
				case 5:
				case 9:
				case 10:
					$winner = 2;
					$msg = Lang::WIN.' '.$sl->tag2;
					break;
				case 1:
					$winner = 0;
					$msg = Lang::OPEN;
					break;
				}
				if ($winner == 0) {
					$reqzz = "SELECT * FROM lg_paris WHERE qui_vote='".ArghSession::get_username()."' AND match_id = '".$sl->id."'";
					$tzz = mysql_query($reqzz);
					if (mysql_num_rows($tzz) == 1) {
						//on a déjà voté
						while ($lzz = mysql_fetch_object($tzz)) {
							echo '<tr'.$alt.'><td>'.$sl->tag1.' - '.$sl->tag2.'</td><td>'.$msg.'</td><td>';
							if ($lzz->winner == 1) echo Lang::VOTED.': '.Lang::WIN.' '.$sl->tag1;
							if ($lzz->winner == 2) echo Lang::VOTED.': '.Lang::WIN.' '.$sl->tag2;
							if ($lzz->winner == 3) echo Lang::VOTED.': '.Lang::DRAW;
							echo '</td><td colspan="2"></td></tr>';
						}
					} else {
						//on peut voter
						echo '<form method="POST" action="">
						<tr'.$alt.'><td>'.$sl->tag1.' - '.$sl->tag2.'</td><td>'.$msg.'</td><td id="cell'.$sl->id.'">
						<select name="vote" id="sel'.$sl->id.'">
						<option value="1">'.Lang::WIN.' '.$sl->tag1.'</option>
						<option value="2">'.Lang::WIN.' '.$sl->tag2.'</option>
						<option value="3">'.Lang::DRAW.'</option>
						</select>
						<a href="javascript:vote('.$sl->id.');">'.Lang::TO_VOTE.'</a> 
						</td><td colspan="2"></td></tr>
						</form>
						';
					}
				} else {
					$vote = 0;
					$query = "SELECT winner FROM lg_paris WHERE match_id='".$sl->id."' AND qui_vote='".ArghSession::get_username()."'";
					$tab = mysql_query($query);
					$line = mysql_fetch_row($tab);
					switch ($line[0]) {
						case 1:
							$my_vote = $sl->tag1;
							break;
						case 2:
							$my_vote = $sl->tag2;
							break;
						case 3:
							$my_vote = Lang::DRAW;
							break;
						default:
							$my_vote = Lang::NOT_VOTED;
							break;
					}
					echo '<tr'.$alt.'><td>'.$sl->tag1.' - '.$sl->tag2.'</td><td>'.$msg.'</td><td>'.$my_vote.'</td><td colspan="2"></td></tr>';
				}
			}
			echo '<tr><td colspan="5">&nbsp;</td></td></tr>';
		}
	}
?>
</table>
<?php
	ArghPanel::end_tag();
	}
?>