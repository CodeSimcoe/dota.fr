<?php
	ArghSession::exit_if_not_rights(
		array(
			RightsMode::NEWS_HEADADMIN, 
			RightsMode::NEWS_NEWSER 
		)
	);
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#ckeditor').ckeditor({
		language: "en",
		resize_enabled: false,
		contentsCss: "themes/default/default.css",
		//uiColor: "#1A1A1A",
		width: "600px",
		height: "500px"
	});
});

function add_division_recap(division) {
	//var oEditor = $('#ckeditor').ckeditorGet();
	var oEditor = CKEDITOR.instances.ckeditor;
	//var oEditor = FCKeditorAPI.GetInstance('ckeditor');
	
	$.get('ajax/get_division_recap.php',
		{
			div: division,
			ajax: 1
		}, function (data) {
			oEditor.insertHtml(data);
			oEditor.focus();
		}
	);
}

function add_group() {
	//var oEditor = FCKeditorAPI.GetInstance('ckeditor');
	var oEditor = CKEDITOR.instances.ckeditor;
	var items = prompt("<?php echo Lang::NEWS_TOURN_PARTICIPATING_ITEMS; ?>");
	var flag = '<img src="../../img/flag/France.gif" alt="" />&nbsp;';
	
	html = '<center><table class="cw">';
	html += '<colgroup>';
	html += '	<col width="5%" />';
	html += '	<col width="40%" />';
	html += '	<col width="11%" />';
	html += '	<col width="11%" />';
	html += '	<col width="11%" />';
	html += '	<col width="11%" />';
	html += '	<col width="11%" />';
	html += '</colgroup>';
	html += '<thead>';
	html += '<tr class="title">';
	html += '	<td colspan="7" align="center"><strong><?php echo ucfirst(Lang::TOURNAMENT); ?> - Gr. A</strong></td>';
	html += '</tr>';
	html += '<tr class="bottom" style="height: 25px;">';
	html += '	<td class="left diviz"><strong>#</strong></td>';
	html += '	<td class="diviz"><?php echo htmlentities(Lang::TEAM); ?></td>';
	html += '	<td class="diviz"><?php echo htmlentities(Lang::MATCHS); ?></td>';
	html += '	<td class="diviz"><?php echo htmlentities(ucfirst(Lang::POINTS)); ?></td>';
	html += '	<td class="diviz"><?php echo htmlentities(Lang::WINS); ?></td>';
	html += '	<td class="diviz"><?php echo htmlentities(Lang::DRAWS); ?></td>';
	html += '	<td class="diviz right"><?php echo Lang::LOSSES; ?></td>';
	html += '</tr>';
	html += '</thead>';
	html += '<tbody>';
	
	for (i = 1; i <= items; i++) {
		if (i == items) {
			style = ' class="bottom"';
		} else {
			style = '';
		}
		html += '<tr style="height: 20px;"' + style + '>';
		html += '	<td align="center" class="left">' + i + '</td>';
		html += '	<td style="vertical-align: middle">' + flag + '<?php echo Lang::TEAM; ?>' + i + '</td>';
		html += '	<td><center>0</center></td>';
		html += '	<td class="points left right"><span class="red"><strong>0</strong></span></td>';
		html += '	<td><center><span class="win">0</span></center></td>';
		html += '	<td><center><span class="draw">0</span></center></td>';
		html += '	<td class="right"><center><span class="lose">0</span></center></td>';
		html += '</tr>';
	}
	
	html += '</tbody></table></center><br />';
	
	oEditor.insertHtml(html);
	oEditor.focus();
}

function add_icon(text, alt) {
	//var oEditor = FCKeditorAPI.GetInstance('ckeditor');
	var oEditor = CKEDITOR.instances.ckeditor;
	oEditor.insertHtml('<img src="' + text + '" alt="' + alt + '" width="32" height="32" />');
	oEditor.focus();
}

function add_flag(text) {
	//var oEditor = FCKeditorAPI.GetInstance('ckeditor');
	var oEditor = CKEDITOR.instances.ckeditor;
	oEditor.insertHtml('<img src="/ligue/img/flag/' + text + '" alt="" />');
	oEditor.focus();
}

function add_tournament() {
    var player_number = prompt("<?php echo Lang::NEWS_TOURN_PARTICIPATING_PLAYERS; ?>");
	//var oEditor       = FCKeditorAPI.GetInstance('ckeditor');
	var oEditor = CKEDITOR.instances.ckeditor;
    var num_tour      = Math.floor(Math.log(player_number) / Math.log(2));
    player_number     = Math.pow(2, num_tour);

    var html = '<table class="tntable"><tr>';

    for (i = 1; i <= num_tour; i++)
    {
        html = html + '<td class="legend"><div><?php echo Lang::TOURNAMENT_ROUND; ?> ' + i + '</div></td>';
    }
    html = html + '<td class="legend"><div><?php echo Lang::WINNER; ?></div></td>';
    html = html + '</tr><tr>';

    /* Generation tableau */

    html = html + '<td>';
    for (j = 1; j <= player_number - 1; j++)
    {
        html = html + '<div class="player"><div class="score">0</div><?php echo Lang::PLAYER; ?></div>';
        html = html + '<div>&nbsp;</div>';
    }
    html = html + '<div class="player"><div class="score">0</div><?php echo Lang::PLAYER; ?></div>';
    html = html + '</td>';

    player_number = player_number / 2;
    num_normal = 0;
    num_join = 1;

    for (i = 2; i <= num_tour; i++)
    {
        upper_motif = '';
        for(normal = 1; normal <= num_normal; normal++)
        {
            upper_motif = upper_motif + '<div>&nbsp;</div>';
        }
        lower_motif = upper_motif;
        for(join = 1; join <= num_join; join++)
        {
            upper_motif = upper_motif + '<div class="join">&nbsp;</div>';
            lower_motif = '<div class="join">&nbsp;</div>' + lower_motif;
        }

        html = html + '<td>';
        for (j = 1; j <= player_number-1; j++)
        {
            html = html + upper_motif + '<div class="player"><div class="score">0</div><?php echo Lang::PLAYER; ?></div>' + lower_motif;
            html = html + '<div>&nbsp;</div>';
        }
        html = html + upper_motif + '<div class="player"><div class="score">0</div><?php echo Lang::PLAYER; ?></div>' + lower_motif;
        html = html + '</td>';
        
        player_number = player_number/2;
        num_normal = num_normal+num_join;
        num_join = num_join*2;
    }

    upper_motif = '';
    for(normal = 1; normal <= num_normal; normal++)
    {
        upper_motif = upper_motif + '<div>&nbsp;</div>';
    }
    lower_motif = upper_motif;
    for(join = 1; join <= num_join; join++)
    {
        upper_motif = upper_motif + '<div class="join">&nbsp;</div>';
        lower_motif = '<div class="join">&nbsp;</div>' + lower_motif;
    }

    html = html + '<td>' + upper_motif + '<div class="player"><?php echo Lang::PLAYER; ?></div>' + lower_motif + '</td>';

    html = html + '</tr></table>';
    
    oEditor.insertHtml(html);
    oEditor.focus();

}

function add_clanwar() {

    //var oEditor = FCKeditorAPI.GetInstance('ckeditor');
	var oEditor = CKEDITOR.instances.ckeditor;
    
	var flag = '<img src="../../img/flag/France.gif" alt="" />';
	var hero = '<img src="/ligue/img/heroes/Lord%20of%20Olympia.gif" width="32" height="32" alt="" />';
	var ban = '&nbsp;<img src="/ligue/img/icons/delete.png" alt="<?php echo Lang::BANS; ?>" />&nbsp;';
	
	var positions = ['top', 'top', 'mid', 'bot', 'bot'];
	var styles = ['alternated', 'alternated', 'alternated', 'alternated', 'alternatedlast'];
	
	var html = '<br /><center><table class="cw">';
	html += '<colgroup>';
	html += '	<col width="44" />';
	html += '	<col width="178" />';
	html += '	<col width="38" />';
	html += '	<col width="38" />';
	html += '	<col width="178" />';
	html += '	<col width="44" />';
	html += '</colgroup>';
	html += '<tr class="title">';
	html += '	<td colspan="2" align="center">' + flag + ' <?php echo Lang::TEAM; ?> 1</td>';
	html += '	<td colspan="2" align="center">VS</td>';
	html += '	<td colspan="2" align="center"><?php echo Lang::TEAM; ?> 2 ' + flag + '</td>';
	html += '</tr>';
	html += '<tr style="height: 25px;">';
	html += '	<td class="left"></td>';
	html += '	<td colspan="4"></td>';
	html += '	<td class="right"></td>';
	html += '</tr>';
	html += '<tr class="bans">';
	html += '	<td colspan="3">' + ban + hero + hero + hero + hero + '</td>';
	html += '	<td colspan="3" align="right">' + hero + hero + hero + hero + ban + '</td>';
	html += '</tr>';
	html += '<tr style="height: 1px;" class="alternated">';
	html += '	<td class="left"></td>';
	html += '	<td colspan="4"></td>';
	html += '	<td class="right"></td>';
	html += '</tr>';
	for (i = 0; i < 5; i++) {
		html += '<tr class="' + styles[i] + '">';
		html += '	<td align="center" class="left">' + hero + '</td>';
		html += '	<td class="lr" align="left" style="padding-left: 4px;"><?php echo Lang::PLAYER; ?></td>';
		html += '	<td class="lr"><center>' + positions[i] + '</center></td>';
		html += '	<td class="lr"><center>' + positions[i] + '</center></td>';
		html += '	<td class="lr" align="right" style="padding-right: 4px;"><?php echo Lang::PLAYER; ?></td>';
		html += '	<td align="center" class="right">' + hero + '</td>';
		html += '</tr>';
	}
	html += '</table></center>';
    oEditor.insertHtml(html);
    oEditor.focus();
}
</script>
<form action="?f=admin_news_save" method="post">
<?
	ArghPanel::begin_tag(Lang::NEWS_MODULE);
	
	//include 'FCKeditor/fckeditor.php';
	
	$news = new News();
	$news->load((int) $_GET['id']);

	$no_edit = ($news->_author_lock == 1 && ArghSession::get_username() != $news->_author) ? true : false;
	if ($no_edit) exit;
/*	
	if (isset($_GET['id'])) {
		$id = (int)$_GET['id'];
		$req = "SELECT * FROM lg_newsmod WHERE id='".$id."'";
		$t = mysql_query($req);
		$l = mysql_fetch_object($t);
		$titre = stripslashes($l->titre);
		$categorie = $l->categorie;
		$aff = $l->afficher;
		$texte = stripslashes($l->texte);
	}

*/

	$no_edit = ($news->_author_lock == 1 && ArghSession::get_username() != $news->_author) ? true : false;
	if ($no_edit) exit;
?>
	<table>
	<colgroup>
		<col width="240" />
		<col />
	</colgroup>
	<tr><td><b><img src="/img/icons/text_allcaps.png" alt="" />&nbsp;<?php echo Lang::NEWS_TITLE; ?>: </b></td><td><input type="text" name="titre" size="50" value="<?php echo $news->_title; ?>"></td></tr>
	<tr><td><b><img src="/img/icons/box.png" alt="" />&nbsp;<?php echo Lang::NEWS_CATEGORY; ?>: </b></td>
	<td><select name="cat">
<?php
	echo '<option value="1"'.attr_(1, $news->_category).'>'.Lang::NEWS_CAT_1.'</option>';
	echo '<option value="2"'.attr_(2, $news->_category).'>'.Lang::NEWS_CAT_2.'</option>';
	echo '<option value="3"'.attr_(3, $news->_category).'>'.Lang::NEWS_CAT_3.'</option>';//Inter
	echo '<option value="4"'.attr_(4, $news->_category).'>'.Lang::NEWS_CAT_4.'</option>';
	//echo '<option value="5"'.attr_(5, $news->_category).'>'.Lang::NEWS_CAT_5.'</option>';//LSH
	//echo '<option value="6"'.attr_(6, $news->_category).'>'.Lang::NEWS_CAT_6.'</option>';//ESWC
	echo '<option value="7"'.attr_(7, $news->_category).'>'.Lang::NEWS_CAT_7.'</option>';//ESWC
?>
	</select></td></tr>
	<tr><td><img src="/img/icons/eye.png" alt="" />&nbsp;<?php echo Lang::NEWS_DISPLAY_NEWS; ?> </td>
	<td><select name="aff">
<?php
	echo '<option value="1"'.attr_(true, $news->_is_shown).'>'.Lang::YES.'</option>';
	echo '<option value="0"'.attr_(false, $news->_is_shown).'>'.Lang::NO.'</option>';
?>
	</select></td></tr>
	<tr><td><img src="/img/icons/arrow_up.png" alt="" />&nbsp;<?php echo Lang::NEWS_BUMP; ?> </td>
	<td><select name="bump">
<?php
	echo '<option value="1" selected="selected">'.Lang::YES.'</option>';
	echo '<option value="0">'.Lang::NO.'</option>';
?>
	</select></td></tr>
	<tr><td><img src="/img/icons/lock.png" alt="" />&nbsp;<?php echo Lang::LOCK_COMMENTS; ?> </td>
	<td><select name="lock">
<?php
	echo '<option value="1"'.attr_(true, $news->_comments_locked).'>'.Lang::YES.'</option>';
	echo '<option value="0"'.attr_(false, $news->_comments_locked).'>'.Lang::NO.'</option>';
?>
	</select></td></tr>
<?php
	if ($news->_author == ArghSession::get_username() || empty($news->_author)) {
?>
        <tr><td><img src="/img/icons/key.png" alt="" />&nbsp;<?php echo Lang::AUTHOR_LOCK; ?> </td>
        <td><select name="author_lock">
<?php
        echo '<option value="1"'.attr_(1, $news->_author_lock).'>'.Lang::YES.'</option>';
        echo '<option value="0"'.attr_(0, $news->_author_lock).'>'.Lang::NO.'</option>';
?>
        </select></td></tr>
<?php
	}
?>
	</table>
	<br />
	<b><?php echo Lang::NEWS_EDITOR_COMMANDS; ?></b><br /><br />
	<u><?php echo Lang::NEWS_ADD_DIVISION_RECAP; ?></u><br /><ul>
<?php
	$divisions = CacheManager::get_division_cache();
	foreach ($divisions as $val) {
		echo '<li><a href="javascript:add_division_recap(\''.$val.'\');">'.Lang::DIVISION.'&nbsp;'.$val.'</a></li>';
	}
?>
	</ul>
	<br />
	<a href="javascript:add_tournament();"><?php echo Lang::NEWS_ADD_TOURNAMENT_TREE; ?></a><br />
	<a href="javascript:add_clanwar();"><?php echo Lang::NEWS_ADD_CLANWAR; ?></a><br />
	<a href="javascript:add_group();"><?php echo Lang::NEWS_ADD_GROUP; ?></a><br />
	<!--<a href="javascript:add_ranking();"><?php echo Lang::NEWS_ADD_RANKING; ?></a><br />-->
	<br />
	<a href="admin_news_logos.php" onclick="window.open('admin_news_logos.php', 'Logos', 'HEIGHT=500,resizable=yes,scrollbars=yes,WIDTH=800');" target="_blank" class="nav"><?php echo Lang::LOGOS; ?></a><br />

	<br /><b><?php echo ucfirst(Lang::HEROES); ?></b><br />
	<!--<b><?php echo Lang::SCOURGE_HEROES; ?></b><br />-->
<?php
	//$req = "SELECT hero FROM lg_heroes WHERE affiliation = 1 ORDER BY hero ASC";
	$req = "SELECT hero FROM lg_heroes ORDER BY hero ASC";
	$t = mysql_query($req);
	$i = 0;

	while ($l = mysql_fetch_row($t)) {
		echo '<a href="javascript:add_icon(\'/ligue/img/heroes/'.$l[0].'.gif\', \''.$l[0].'\')"><img src="/ligue/img/heroes/'.$l[0].'.gif" alt="'.$l[0].'" width="32" height="32" border="0" /></a>&nbsp;';
	}
/*
?>
	<br />
	<b><?php echo Lang::SENTINEL_HEROES; ?></b><br />
	
<?php
	$req = "SELECT hero FROM lg_heroes WHERE affiliation = 0 ORDER BY hero ASC";
	$t = mysql_query($req);
	$i = 0;
	
	while ($l = mysql_fetch_row($t)) {
		echo '<a href="javascript:add_icon(\'/ligue/img/heroes/'.$l[0].'.gif\', \''.$l[0].'\')"><img src="/ligue/img/heroes/'.$l[0].'.gif" alt="'.$l[0].'" width="32" height="32" border="0" /></a>&nbsp;';
	}
*/
?>
	<br /><br />
	<b><?php echo ucfirst(Lang::FLAGS); ?></b><br />
<?php
	$dir = 'img/flag/';

	if (is_dir($dir)) {
	    if ($dh = opendir($dir)) {
			$i = 0;
			$flags = array();
	        while (($file = readdir($dh)) !== false) {
				$flags = array_merge($flags, array($file));
	        }
			sort($flags);
			
			foreach ($flags as $val) {
				echo '<a href="javascript:add_flag(\''.$val.'\')"><img src="'.$dir.$val.'" border="0" alt="" /></a>';
			}
	        closedir($dh);
	    }
	}
?>
	<br /><br />
	<center>
		<textarea id="ckeditor" name="ckeditor"><?php echo $news->_content; ?></textarea>
	</center>
<?php
	/*
	$oFCKeditor = new FCKeditor('ckeditor');
	$oFCKeditor->BasePath = '/ligue/FCKeditor/';
	$oFCKeditor->Value = $news->_content;
	$oFCKeditor->Width = '100%';
	$oFCKeditor->Height = '700';
	$oFCKeditor->Create();
	*/
	if (isset($news->_id)) {
		echo '<input type="hidden" name="edit" value="'.$news->_id.'" />';
	}
?>
	<br />
	<center><input type="submit" value="<?php echo Lang::NEWS_SUBMIT; ?>"></center>
</form>
<?php
	ArghPanel::end_tag();
?>
