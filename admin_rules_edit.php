<?php

	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LEAGUE_HEADADMIN,
			RightsMode::LADDER_HEADADMIN,
			RightsMode::VIP_HEADADMIN
		)
	);

	require 'classes/RulesCategories.php';
	include 'FCKeditor/fckeditor.php';
	
	define(FCK, "FCK");
?>
	
	<script language="javascript">
		function add_fck_data(data) {
			var fckEditor = FCKeditorAPI.GetInstance('<?php echo FCK; ?>');
			
			fckEditor.SetHTML(data);
			fckEditor.Focus();
		}

		$(document).ready(function() {
			$("#ok_model").bind("click", function() {
				var rule_id = parseInt($("#model").val());
				$.get("ajax/get_rules_content.php",
					{
						rule_id: rule_id,
						anticache: new Date().getTime()
					},
					function(data) {
						add_fck_data(data);
					});
			});
		});
	</script>
	
<?php
	ArghPanel::begin_tag(Lang::RULES_MANAGEMENT);
	
	//Sauvegarde
	if (isset($_POST['post'])) {
		
		//On blinde
		$season = mysql_real_escape_string(substr(trim($_POST['season']), 0, 2));
		$version = mysql_real_escape_string(substr(trim($_POST['version']), 0, 5));
		$type = (int)$_POST['type'];
		$rules = mysql_real_escape_string($_POST[FCK]);
		
		if (isset($_POST['rule_id'])) {
			//Edition
			$rule_id = (int)$_POST['rule_id'];
			$ins = "UPDATE lg_rules
					SET date_rule = '".time()."',
						season = '".$season."',
						version = '".$version."', 
						type = '".$type."',
						rules = '".$rules."'
					WHERE id = '".$rule_id."'";
			mysql_query($ins);
			
			echo '<br /><center>'.Lang::RULES_UPDATED.'</center><br />';
		} else {
			//Insertion
			$ins = "INSERT INTO lg_rules (author, date_rule, season, version, type, rules)
					VALUES ('".ArghSession::get_username()."', '".time()."', '".$season."', '".$version."', '".$type."', '".$rules."')";
			mysql_query($ins);
			
			echo '<br /><center>'.Lang::RULES_ADDED.'</center><br />';
		}
	}
	
	//Suppression
	if (isset($_GET['del']) && isset($_GET['rule_id'])) {
		
		$rule_id = (int)$_GET['rule_id'];
		$del = "DELETE FROM lg_rules WHERE id = '".$rule_id."'";
		mysql_query($del);
		
		echo '<br /><center>'.Lang::RULES_DELETED.'</center><br />';
	}
	
	//Formulaire
	if (isset($_POST['new']) || isset($_GET['edit'])) {
?>
		<form method="POST" action="?f=admin_rules_edit">
		
		<?php
			if (isset($_GET['edit'])) {
				$rule_id = (int)$_GET['rule_id'];
				
				$req = "SELECT * FROM lg_rules WHERE id = '".$rule_id."'";
				$t = mysql_query($req);
				$l = mysql_fetch_object($t);
				$rules_value = $l->rules;
				$season_value = $l->season;
				$rules_type = $l->type;
				
				echo '<input type="hidden" name="rule_id" value="'.$rule_id.'" />';
			}
		?>
		
		<table class="listing">
			<colgroup>
				<col width="25%" />
				<col width="75%" />
			</colgroup>
			<tr>
				<td><?php echo Lang::AUTHOR; ?></td>
				<td><strong><?php echo ArghSession::get_username(); ?></strong></td>
			</tr>
			<tr>
				<td><?php echo Lang::DATE; ?></td>
				<td><strong><?php echo date(Lang::DATE_FORMAT_DAY); ?></strong></td>
			</tr>
			<tr>
				<td><?php echo Lang::SEASON; ?></td>
				<td><input type="text" name="season" value="<?php echo $season_value; ?>" maxlength="2" /></td>
			</tr>
			<tr>
				<td><?php echo Lang::VERSION; ?></td>
				<td><input type="text" name="version" value="1.0" maxlength="5" /></td>
			</tr>
			<tr>
				<td><?php echo Lang::TYPE; ?></td>
				<td><select name="type">
					<option value="<?php echo RulesCategories::LEAGUE; ?>"<?php echo attr_($rules_type, RulesCategories::LEAGUE); ?>><?php echo Lang::LEAGUE; ?></option>
					<option value="<?php echo RulesCategories::LADDER; ?>"<?php echo attr_($rules_type, RulesCategories::LADDER); ?>><?php echo Lang::LADDER; ?></option>
					<option value="<?php echo RulesCategories::LADDER_VIP; ?>"<?php echo attr_($rules_type, RulesCategories::LADDER_VIP); ?>><?php echo Lang::LADDER_VIP; ?></option>
					<option value="<?php echo RulesCategories::TOURNAMENT; ?>"<?php echo attr_($rules_type, RulesCategories::TOURNAMENT); ?>><?php echo ucfirst(Lang::TOURNAMENT); ?></option>
				</select></td>
			</tr>
			<tr>
				<td><?php echo Lang::RULES_MODEL; ?></td>
				<td><select name="model" id="model">
				<?php
					$req = "SELECT * FROM lg_rules ORDER BY date_rule DESC";
					$t = mysql_query($req);
					while ($l = mysql_fetch_object($t)) {
						switch ($l->type) {
							case RulesCategories::LEAGUE:
								$type = Lang::LEAGUE;
								break;
							
							case RulesCategories::LADDER:
								$type = Lang::LADDER;
								break;
								
							case RulesCategories::LADDER_VIP:
								$type = Lang::LADDER_VIP;
								break;
								
							case RulesCategories::TOURNAMENT:
								$type = Lang::TOURNAMENT;
								break;
						}
						echo '<option value="'.$l->id.'">'.$type.' - v'.$l->version.' '.Lang::SEASON.' '.$l->season.' </option>';
					}
				?>
				</select>&nbsp;<input type="button" value="<?php echo Lang::OK; ?>" id="ok_model" /></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php
					$fck = new FCKeditor(FCK);
					$fck->BasePath = '/ligue/FCKeditor/';
					$fck->Value = stripcslashes($rules_value);
					$fck->Width = '100%';
					$fck->Height = '700';
					$fck->Create();
				?>
				<br /><br />
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" value="<?php echo Lang::VALIDATE; ?>" name="post" style="width: 200px;" /></td>
			</tr>
		</table>
		</form>
		<br /><br />
<?php
	}
	$req = "SELECT *
			FROM lg_rules
			ORDER BY type ASC, season DESC, version DESC";
	$t = mysql_query($req);
	$i = 0;
?>
	<table class="listing">
		<colgroup>
		</colgroup>
		<thead>
			<tr>
				<th><?php echo Lang::TYPE; ?></th>
				<th><?php echo Lang::SEASON; ?></th>
				<th><?php echo Lang::VERSION; ?></th>
				<th><?php echo Lang::AUTHOR; ?></th>
				<th><?php echo Lang::DATE; ?></th>
				<th><?php echo Lang::ACTION; ?></th>
			</tr>
			<tr><td colspan="6" class="line">&nbsp;</td></tr>
		</thead>
		<tbody>
<?php
	while ($l = mysql_fetch_object($t)) {
		echo '<tr'.Alternator::get_alternation($i).'>
			<td>'.RulesCategories::getCategoryByType($l->type).'</td>
			<td>'.$l->season.'</td>
			<td>'.$l->version.'</td>
			<td>'.$l->author.'</td>
			<td>'.date(Lang::DATE_FORMAT_DAY, $l->date_rule).'</td>
			<td align="center">
				<a href="?f=admin_rules_edit&edit=1&rule_id='.$l->id.'">edit</a>
				&nbsp;&nbsp;&nbsp;
				<a href="?f=admin_rules_edit&del=1&rule_id='.$l->id.'">del</a>
			</td>
		</tr>';
	}
?>
	</tbody>
	</table>
	<br /><br />
<?php

	//Choix ajout / edition
	echo '<form method="POST" action="?f=admin_rules_edit">';
	echo '<center><input type="submit" name="new" value="'.Lang::RULES_NEW.'" style="width: 200px;" /></center>';
	echo '</form>';
	
	ArghPanel::end_tag();
?>