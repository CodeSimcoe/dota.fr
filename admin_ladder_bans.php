<?php
	ArghSession::exit_if_not_rights(
		array(
			RightsMode::LADDER_HEADADMIN,
			RightsMode::LADDER_ADMIN,
			RightsMode::VIP_HEADADMIN,
			RightsMode::VIP_ADMIN
		)
	);
	
	//Pagination
	/*
	$query = "SELECT COUNT(*) FROM lg_ladderbans l, lg_users u WHERE u.username = l.qui";
	$result = mysql_query($query);
	$count = mysql_fetch_row($result);
	$count = $count[0];
	
	$nb_pages = floor(($count - 1) / 100);
	*/

	//echo '<script type="text/javascript" src="javascript/ui.slider.js"></script>
	echo '<script type="text/javascript">
		
			var lock = 0;
			
			function getLadderBans() {
				$.get("ajax/get_ladder_bans.php",
					{
						//start: 100 * $("#slider").slider("option", "value"),
						filter: $("#filter").val()
					},
					function(data) {
						$("#ban_content").html(data)
					}
				);
			}
			
			$(document).ready(function() {
				/*
				$("#slider").slider({
					min: 1,
					max: '.$nb_pages.',
					slide: function(event, ui) {
						$("#page_nb").html(ui.value);
					},
					stop: function(event, ui) {
						getLadderBans();
					}
				});
				*/
				
				$("#filter").keyup(function(event) {
					getLadderBans();
				});
			});
		</script>';
	
	//ADD_BAN
	if (isset($_POST['go'])) {
		BanManager::ban($_POST['user'], $_POST['duree'], $_POST['motif']);
	}
	
	//REMOVE_BAN
	if (isset($_GET['action']) && $_GET['action'] == 'delete') {
		$req = "SELECT u.username
				FROM lg_ladderbans l, lg_users u
				WHERE u.username = l.qui
				AND l.id = ".$_GET['id'];
		$res = mysql_query($req);
		if (mysql_num_rows($res) > 0) {
			$obj = mysql_fetch_object($res);
			$al = new AdminLog('Unban : '.$obj->username);
			$al->save_log();
		}
		BanManager::unban($_GET['id']);
	}

	ArghPanel::begin_tag(Lang::LADDER_BANS_MANAGEMENT);
?>
<form action="?f=admin_ladder_bans" method="POST">
<table class="simple">
	<thead>
		<tr>
			<th><?php Lang::USERNAME; ?></th>
			<th><?php Lang::LADDER_BAN_DURATION; ?></th>
			<th><?php Lang::REASON; ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><select name="user">
			<?php
				$req = "SELECT username FROM lg_users ORDER BY username ASC";
				$t = mysql_query($req);
				while ($l = mysql_fetch_row($t)) {
					echo '<option value="'.$l[0].'">'.$l[0].'</option>';
				}
			?>
			</select></td><td>
				<select name="duree">
					<?php
						//1 -> 20 step 1
						echo '<option value="1">1 '.Lang::DAY.'</option>';
						for ($i = 2; $i <= 20; $i++) {
							echo '<option value="'.$i.'">'.$i.' '.Lang::DAYS.'</option>';
						}
						
						//30 -> 180 step 30
						for ($i = 30; $i <= 180; $i += 30) {
							echo '<option value="'.$i.'">'.$i.' '.Lang::DAYS.'</option>';
						}
					?>
					<option value="0"><?php echo Lang::UNLIMITED; ?></option>
				</select>
			</td>
			<td>
				<textarea name="motif" rows="2" cols="25"></textarea>
			</td>
			<td>
				<input type="submit" name="go" value="<?php echo Lang::OK; ?>" />
			</td>
		</tr>
	</tbody>
</table>
</form>

<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag(Lang::LADDER_BANNED_ACCOUNTS);
		
	echo Lang::FILTER.': <input type="text" id="filter" value="" /><br /><br />';
	//echo '<div style="width: 575px; padding: 10px;">'.Lang::PAGE.' <span id="page_nb">1</span><br /><br /><center><div id="slider"></div></div></center>';

	echo '<div id="ban_content">';
	include '/home/www/ligue/ajax/get_ladder_bans.php';
	echo '</div>';
	
	ArghPanel::end_tag();
?>