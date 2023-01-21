<?php
	class Messager {
	
		public $_name;
		public $_table;
		public $_reference_id;
		public $_can_add_message;
		
		public function Messager($name, $table, $reference_id, $can_add_message = true) {
			$this->_name = $name;
			$this->_table = $table;
			$this->_reference_id = $reference_id;
			$this->_can_add_message = $can_add_message;
		}
		
		public function deploy() {
			$pagination = GenericMessageManager::prepare($this->_name, $this->_table, $this->_reference_id);
			
			ArghPanel::begin_tag('<a name="comment">'.Lang::MESSAGES.'</a>');
			echo $pagination.'<br /><br />';
			echo '<div id="GMM_'.$this->_name.'"></div>';
			echo '<br />'.$pagination;
			ArghPanel::end_tag();
			
			echo '<script language="javascript">
				$(document).ready(function() {
					get_messages(0);
				});
			</script>';
			
			//$pg = GenericMessageManager::prepare($this->_name, $this->_table, $this->_reference_id);
			GenericMessageManager::display_message_adding_box($this->_name, $this->_table, $this->_reference_id, '100%', 200, $this->_can_add_message);
		}
	}

	abstract class GenericMessageManager {
	
		const MESSAGES_PER_PAGE = 10;
	
		//Tables & Rights
		public static $TABLES = array(
			Tables::SCREENSHOT_MESSAGES => array(
				Tables::SCREENSHOT_MESSAGES_REFERENCE_FIELD,
				array(
					//RightsMode::WEBMASTER,
					RightsMode::SCREENSHOTS_ADMIN
				)
			),
			
			Tables::REPORT_MESSAGES => array(
				Tables::REPORT_MESSAGES_REFERENCE_FIELD,
				array(
					//RightsMode::WEBMASTER,
					RightsMode::LADDER_ADMIN,
					RightsMode::LADDER_HEADADMIN
				)
			),
			
			Tables::REPORT_MESSAGES_VIP => array(
				Tables::REPORT_MESSAGES_REFERENCE_FIELD,
				array(
					RightsMode::LADDER_ADMIN,
					RightsMode::LADDER_HEADADMIN,
					RightsMode::VIP_ADMIN,
					RightsMode::VIP_HEADADMIN
				)
			),
			
			Tables::NEWS_MESSAGES => array(
				Tables::NEWS_MESSAGES_REFERENCE_FIELD,
				array(
					RightsMode::NEWS_HEADADMIN,
					RightsMode::NEWS_NEWSER
				)
			),
			
			Tables::LEAGUE_MESSAGES => array(
				Tables::LEAGUE_MESSAGES_REFERENCE_FIELD,
				array(
					RightsMode::LEAGUE_HEADADMIN,
					RightsMode::LEAGUE_ADMIN
				)
			),
		);

		public static function prepare($name, $table, $reference_id) {
			$t = mysql_query("SELECT COUNT(*) FROM `".$table."` WHERE ".self::$TABLES[$table][0]." = ".(int)$reference_id);
			
			$count = mysql_fetch_row($t);
			$count = $count[0];
			
			$nb_pages = ceil($count / self::MESSAGES_PER_PAGE);
			
			
			$pagination = Lang::PAGES.': ';
			for ($i = 1; $i <= $nb_pages; $i++) {
				$pagination .= '<span class="page"><span class="page_'.$i.'"><a href="javascript:get_messages('.(($i - 1) * self::MESSAGES_PER_PAGE).')">'.$i.'</a></span></span> ';
			}
		
			//JS
			echo '<script language="javascript">
				function get_messages(start) {
					
					var i = parseInt(start / '.self::MESSAGES_PER_PAGE.') + 1;
					$(".page > span").css("text-decoration", "");
					$(".page_" + i).css("text-decoration", "underline");
					
				
					$.get("/ligue/ajax/message_manage.php",
						{
							name: "'.$name.'",
							reference_id: '.$reference_id.',
							table: "'.$table.'",
							action: "get",
							start: start,
							limit: '.self::MESSAGES_PER_PAGE.'
						}, function (data) {
							$("#GMM_'.$name.'").html(data);
						}
					);
				}
			</script>';
			
			return $pagination;
		}
		
		public static function display_messages($name, $messages, $start = 0) {
		
			//ArghPanel::begin_tag('<a name="comment">'.Lang::MESSAGES.'</a>');
			
			require_once '/home/www/ligue/FCKeditor/fckeditor.php';
			
			if (count($messages) > 0) {
				
				$i = 0;
				//echo '<div style="overflow: auto; max-height: 450px; border: 1px solid white; padding: 2px;">
				/*<col width="10%" />
				<col width="25%" />
				<col width="60%" />
				<col width="5%" />*/
				echo '<div style="border: 1px solid white; padding: 2px;">
					<table class="listing">
						<colgroup>
							<col width="60" />
							<col width="150" />
							<col width="360" />
							<col width="30" />
						</colgroup>';
				foreach ($messages as $msg) {
				
					$message = stripslashes($msg->_message);
					$message = $msg->_is_moderated ? '<p><i>'.sprintf(Lang::MESSAGE_MODERATED_BY, $msg->_moderated_by).'</i></p>' : $message;
					$message = ($msg->_highlight == 1) ? '<span class="vip">'.$message.'</span>' : $message;
					
					echo '<tr'.Alternator::get_alternation($i).' id="row_'.$msg->_id.'">
							<td valign="top" align="center" style="padding-top: 15px;"><i>'.($i + $start).'.</i></td>
							<td valign="top" style="padding-top: 15px;">
								<a href="?f=player_profile&player='.$msg->_author.'"><span id="author_'.$msg->_id.'">'.$msg->_author.'</span></a><br />
								'.(!empty($msg->_team_id) ? Lang::TEAM.'&nbsp;:&nbsp;<a href="?f=team_profile&id='.$msg->_team_id.'">'.$msg->_team_tag.'</a><br />' : '').'
								<span class="info">'.date(Lang::DATE_FORMAT_HOUR, $msg->_date_message).'</span>
							</td>
							<td>
								<span id="msg_'.$msg->_id.'">'.$message.'</span>
								<div id="fck_'.$msg->_id.'" style="display: none;">';
								$oFCKeditor = new FCKeditor('fckeditor_'.$msg->_id) ;
								$oFCKeditor->BasePath = '/ligue/FCKeditor/';
								$oFCKeditor->ToolbarSet = 'Basic';
								$oFCKeditor->Value = $message;
								$oFCKeditor->Width = 350;
								$oFCKeditor->Height = 200;
								$oFCKeditor->Create();
								echo '<br /><center>
									<span id="loader_'.$msg->_id.'" style="display: none;"><img src="/ligue/img/ajax-loader.gif" alt="" /></span>
									<span id="button_'.$msg->_id.'">
										<input type="button" value="'.Lang::CANCEL.'" onClick="hide_editor('.$msg->_id.')" style="width: 100px;" />
										&nbsp;
										<input type="button" value="'.Lang::EDIT.'" onClick="edit_message('.$msg->_id.')" style="width: 100px;" />
									</span>
								</center></div>
							</td>
							<td valign="top">';
							//Quote
							echo '<a href="javascript:quote('.$msg->_id.')"><img src="img/icons/comment.png" alt="'.Lang::QUOTE.'" /></a><br />';
							
							//Rights
							if ($msg->can_delete()) {
								echo '<a href="javascript:delete_message('.$msg->_id.')"><img src="img/icons/delete.png" alt="'.Lang::DELETE.'" /></a><br />';
							}
							if ($msg->can_moderate()) {
								echo '<a href="javascript:moderate_message('.$msg->_id.')"><img src="img/icons/error.png" alt="'.Lang::MODERATE.'" /></a><br />';
							}
							if ($msg->can_edit()) {
								echo '<a href="javascript:show_editor('.$msg->_id.')"><img src="img/icons/pencil.png" alt="'.Lang::EDIT.'" /></a>';
							}
							echo '</td>
						</tr>';
				}
				echo '</table></div>';
				
				//JS
				echo '<script language="javascript">
				
						var table = \''.$msg->_table.'\';
						
						function quote(msg_id) {
							var quote = \'<strong>\' + $("#author_" + msg_id).html() + \':</strong><br /><div class="quote">\' + $("#msg_" + msg_id).html() + \'</div><br />\';
							var oEditor = FCKeditorAPI.GetInstance("fck_'.$name.'");
							oEditor.InsertHtml(quote);
						}
				
						function delete_message(msg_id) {
						
							if (confirm("'.Lang::DELETE.' ?")) {
						
								$.get("/ligue/ajax/message_manage.php",
									{
										msg_id: msg_id,
										table: table,
										action: "delete"
									}, function (data) {
										$("#row_" + msg_id).fadeOut();
									}
								);
							
							}
						}
						
						function moderate_message(msg_id) {
						
							if (confirm("'.Lang::MODERATE.' ?")) {
						
								$.get("/ligue/ajax/message_manage.php",
									{
										msg_id: msg_id,
										table: table,
										action: "moderate"
									}, function (data) {
										$("#msg_" + msg_id).html("<p><i>'.sprintf(Lang::MESSAGE_MODERATED_BY, ArghSession::get_username()).'</i></p>");
									}
								);
							
							}
						}
						
						function show_editor(msg_id) {
							$("#msg_" + msg_id).hide();
							$("#loader_" + msg_id).hide();
							$("#button_" + msg_id).show();
							$("#fck_" + msg_id).show();
						}
						
						function hide_editor(msg_id) {
							$("#loader_" + msg_id).hide();
							$("#fck_" + msg_id).hide();
							$("#button_" + msg_id).hide();
							$("#msg_" + msg_id).show();
						}
						
						function edit_message(msg_id) {
						
							hide_editor(msg_id);
						
							var oEditor = FCKeditorAPI.GetInstance("fckeditor_" + msg_id);
							var message = oEditor.GetHTML();
							
							$.get("/ligue/ajax/message_manage.php",
								{
									msg_id: msg_id,
									table: table,
									message: message,
									action: "edit"
								}, function (data) {
									$("#fck_" + msg_id).hide();
									$("#msg_" + msg_id).html(message).show();
								}
							);
						}
					</script>';
			} else {
				echo '<center>'.Lang::NO_MESSAGE.'</center>';
			}
			
			//ArghPanel::end_tag();
		}
		
		public static function display_message_adding_box($name, $table, $reference_id, $width = '100%', $height = 200, $can_add_message = true) {
			require_once 'FCKeditor/fckeditor.php';
			
			//$name = substr(uniqid(), 0, 10);
			
			ArghPanel::begin_tag(Lang::MESSAGE_ADDING);
			
			if (ArghSession::is_logged()) {
				if ($can_add_message) {
			
					echo '<script language="javascript">
							function add_message() {
							
								$("#button_'.$name.'").hide();
								$("#loader_'.$name.'").show();
								
								var oEditor = FCKeditorAPI.GetInstance("fck_'.$name.'");
								var message = oEditor.GetHTML();
								
								$.get("/ligue/ajax/message_manage.php",
									{
										table: "'.$table.'",
										message: message,
										action: "add",
										reference_id: '.$reference_id.'
									}, function (data) {
										$("#editor_'.$name.'").html("<center>'.Lang::MESSAGE_ADDED.'</center>");
										$("#loader_'.$name.'").hide();
									}
								);
							}
						</script><span id="editor_'.$name.'">';
					$oFCKeditor = new FCKeditor('fck_'.$name) ;
					$oFCKeditor->BasePath = '/ligue/FCKeditor/';
					$oFCKeditor->ToolbarSet = 'Basic';
					$oFCKeditor->Width = $width;
					$oFCKeditor->Height = $height;
					$oFCKeditor->Create();
					echo '</span><br /><br />
						<center>
							<span id="button_'.$name.'"><input type="button" value="'.Lang::VALIDATE.'" onClick="add_message()" /></span>
							<span id="loader_'.$name.'" style="display: none;"><img src="/ligue/img/ajax-loader.gif" alt="" /></span>
						</center>';
			
				} else {
					echo '<center>'.Lang::MESSAGE_CANT_POST.'</center>';
				}
			
			} else {
				echo '<center>'.Lang::MESSAGE_MUST_BE_LOGGED.'</center>';
			}
			
			ArghPanel::end_tag();
		}
	}

	class GenericMessage {
		
		//Commun
		public $_id;
		public $_author;
		public $_date_message;
		public $_last_edit;
		public $_message;
		public $_reference_id;
		public $_rights;
		public $_is_moderated;
		public $_highlight;
		
		//Optionnel
		public $_team_name;
		public $_team_tag;
		public $_team_id;
		
		//Specifique
		public $_table;
		public $_reference_field;
		
		public function GenericMessage($table = null) {
			$this->_table = $table;
			$this->_reference_field = GenericMessageManager::$TABLES[$table][0];
			$this->_rights = GenericMessageManager::$TABLES[$table][1];
		}
		
		public function build_from_sql_resource($sql_resource) {
			$this->_id = $sql_resource->id;
			$this->_author = $sql_resource->author;
			$this->_date_message = $sql_resource->date_message;
			$this->_message = $sql_resource->message;
			$this->_reference_id = $sql_resource->reference_id;
			$this->_highlight = $sql_resource->highlight;
			$this->_is_moderated = ($sql_resource->is_moderated == 1) ? true : false;
			$this->_moderated_by = $this->_is_moderated ? $sql_resource->moderated_by : '';
			
			if (!empty($sql_resource->team_id)) {
				$this->_team_id = $sql_resource->team_id;
				$this->_team_name = $sql_resource->team_name;
				$this->_team_tag = $sql_resource->team_tag;
			}
		}
		
		public function save() {
		
			//Anti doublons
			$query = "SELECT message FROM `".$this->_table."` WHERE author = '".mysql_real_escape_string($this->_author)."' AND `".$this->_reference_field."` = '".(int)$this->_reference_id."' ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query);
			$sql = mysql_fetch_row($result);
			
			$this->_message = str_replace('&nbsp;', ' ', $this->_message);
			
			if ($sql[0] != $this->_message) {
		
				$query = "INSERT INTO `".$this->_table."` (author, date_message, message, `".$this->_reference_field."`, highlight)
						  VALUES ('".mysql_real_escape_string($this->_author)."', '".time()."', '".mysql_real_escape_string($this->_message)."', '".(int)$this->_reference_id."', '".(int)$this->_highlight."')";
				mysql_query($query);
				
				return mysql_insert_id();
			
			}
		}
		
		public function update() {
			$query = "UPDATE `".$this->_table."`
					  SET message = '".mysql_real_escape_string($this->_message)."',
						  last_edit = '".time()."'
					  WHERE id = '".(int)$this->_id."'";
			mysql_query($query);
		}
		
		public function delete() {
			$query = "DELETE FROM `".$this->_table."` WHERE id = '".(int)$this->_id."'";
			mysql_query($query);
		}
		
		public function moderate() {
			$query = "UPDATE `".$this->_table."`
					  SET is_moderated = 1,
						  moderated_by = '".ArghSession::get_username()."'
					  WHERE id = '".(int)$this->_id."'";
			mysql_query($query);
		}
		
		public static function load_referenced($table, $reference_id, $load_optionnal = false, $start, $limit) {
			$messages = array();
			
			if ($load_optionnal) {
				$query = "
					SELECT t.*, c.id AS team_id, c.name AS team_name, c.tag AS team_tag
					FROM `".$table."` t, lg_users u LEFT JOIN lg_clans c ON u.clan = c.id
					WHERE u.username = t.author
					AND `".GenericMessageManager::$TABLES[$table][0]."` = '".(int)$reference_id."'
					ORDER BY t.id ASC";
			} else {
				$query = "
					SELECT *
					FROM `".$table."`
					WHERE `".GenericMessageManager::$TABLES[$table][0]."` = '".(int)$reference_id."'
					ORDER BY id ASC";
			}
			
			if ($start == 0) {
				$query .= "\n".'LIMIT '.(int)$limit;
			} else {
				$query .= "\n".'LIMIT '.(int)$start.', '.(int)$limit;
			}
			
			$result = mysql_query($query) or die(mysql_error());
			if (mysql_num_rows($result) > 0) {
				while ($sql_resource = mysql_fetch_object($result)) {
					$m = new GenericMessage($table);
					$m->build_from_sql_resource($sql_resource);
					$messages[] = $m;
				}
			}
			
			return $messages;
		}
		
		public function get_author() {
			$query = "SELECT author FROM `".$this->_table."` WHERE id = '".(int)$this->_id."'";
			$result = mysql_query($query);
			$sql = mysql_fetch_row($result);
			
			return $sql[0];
		}
		
		public function can_edit() {
			return ((ArghSession::is_rights($this->_rights) || ArghSession::get_username() == $this->_author) && !$this->_is_moderated);
		}
		
		public function can_delete() {
			return (ArghSession::is_rights($this->_rights));
		}
		
		public function can_moderate() {
			return (ArghSession::is_rights($this->_rights));
		}
	}
?>