<?php
	define('ABSOLUTE_PATH', '/home/www/ligue/');
	
	require ABSOLUTE_PATH.'classes/ArghSession.php';
	ArghSession::begin();
	require_once ABSOLUTE_PATH.'lang/'.ArghSession::get_lang().'/Lang.php';
	
	require ABSOLUTE_PATH.'mysql_connect.php';
	require ABSOLUTE_PATH.'classes/Tables.php';
	require ABSOLUTE_PATH.'classes/GenericMessageManager.php';
	require ABSOLUTE_PATH.'classes/RightsMode.php';
	require ABSOLUTE_PATH.'classes/ArghPanel.php';
	require ABSOLUTE_PATH.'classes/Alternator.php';
	
	ArghSession::exit_if_not_logged();
	
	//Table OK ?
	if (!array_key_exists($_GET['table'], GenericMessageManager::$TABLES)) exit;
	
	$msg = new GenericMessage($_GET['table']);
	
	switch ($_GET['action']) {
	
		//GET
		case 'get':
		
			$name = $_GET['name'];
			$reference_id = (int)$_GET['reference_id'];
			$load_optionnal = (boolean)$_GET['load_optionnal'];
			
			$messages = GenericMessage::load_referenced($_GET['table'], $reference_id, $load_optionnal, $_GET['start'], $_GET['limit']);
			GenericMessageManager::display_messages($name, $messages, (int)$_GET['start']);
			
			break;
	
		//EDIT
		case 'edit':
			
			//Chargement bdd auteur
			$msg->_id = $_GET['msg_id'];
			$msg->_author = $msg->get_author();
			
			if ($msg->can_edit()) {
			
				//Security dans la fonction
				$msg->_message = $_GET['message'];
				
				$msg->update();
			}
			
			break;
			
		case 'moderate':
			
			$msg->_id = $_GET['msg_id'];
			
			if ($msg->can_moderate()) {
				
				$msg->moderate();
			}
		
			break;
			
		case 'delete':
			
			$msg->_id = $_GET['msg_id'];
			
			if ($msg->can_delete()) {
				
				$msg->delete();
			}
			
			break;
			
		case 'add':
		
			$msg->_author = ArghSession::get_username();
			$msg->_message = $_GET['message'];
			$msg->_reference_id = $_GET['reference_id'];
		
			$msg->save();
			
			break;
		
	}
?>