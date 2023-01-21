<?php

	ArghSession::exit_if_not_logged();
	
	require_once '/home/www/dota/classes/VipManager.php';
	require_once '/home/www/dota/classes/VipCache.php';

	$game_id = 0;
	if (isset($_GET['id'])) 
	{
		$game_id = (int)$_GET['id'];
	}
	
	if ($game_id > 0)
	{
		ArghPanel::begin_tag(Lang::LADDERVIP_GAME.' #'.$game_id);
		if (VipCache::is_in_cache($game_id)) {
			// Game en cours de draft
			$vip = VipCache::load($game_id);
			if ($vip->current_step == VipManager::STEP_ORDER_PICK) {
			
			} else if ($vip->current_step == VipManager::STEP_SIDE_PICK) {
			
			} else if ($vip->current_step == VipManager::STEP_PLAYER_PICK_1) {
			
			} else if ($vip->current_step == VipManager::STEP_PLAYER_PICK_2) {
			
			} else if ($vip->current_step == VipManager::STEP_PLAYER_PICK_3) {
			
			} else if ($vip->current_step == VipManager::STEP_PLAYER_PICK_4) {
			
			} else if ($vip->current_step == VipManager::STEP_PLAYER_PICK_5) {
			
			} else if ($vip->current_step == VipManager::STEP_BAN_1) {
			
			} else if ($vip->current_step == VipManager::STEP_BAN_2) {
			
			} else if ($vip->current_step == VipManager::STEP_BAN_3) {
			
			} else if ($vip->current_step == VipManager::STEP_BAN_4) {
			
			} else if ($vip->current_step == VipManager::STEP_BAN_5) {
			
			} else if ($vip->current_step == VipManager::STEP_BAN_6) {
			
			} else if ($vip->current_step == VipManager::STEP_BAN_7) {
			
			} else if ($vip->current_step == VipManager::STEP_BAN_8) {
			
			} else if ($vip->current_step == VipManager::STEP_HERO_1) {
			
			} else if ($vip->current_step == VipManager::STEP_HERO_2) {
			
			} else if ($vip->current_step == VipManager::STEP_HERO_3) {
			
			} else if ($vip->current_step == VipManager::STEP_HERO_4) {
			
			} else if ($vip->current_step == VipManager::STEP_HERO_5) {
			
			} else if ($vip->current_step == VipManager::STEP_HERO_6) {
			
			}
		} else {
			// Game en base de donnees
			
		}
		ArghPanel::end_tag();
	}

?>