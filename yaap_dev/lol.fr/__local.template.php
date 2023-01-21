<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" >
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" >
<head>
	<title><?php echo WEBSITE_TITLE; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="/themes/<?php echo THEME_DEFAULT; ?>/global.css" type="text/css" />
	<link rel="stylesheet" href="/themes/<?php echo THEME_DEFAULT; ?>/boxes.css" type="text/css" />
</head>
<body>
	<div class="container">
		<div class="logo"><img src="http://www.dota.fr/ligue/img/banners/6.jpg" alt="" /></div>
		<div class="menu"><?php include_once YAAP_PATH.'pages/menus/menu_top.php'; ?></div>
		<div class="clearfix"> 
			<div class="main">
				<?php
					YaapPanel::begin_tag('Profil');
					for ($i = 0; $i < 50; $i++) echo '<br />';
					YaapPanel::end_tag(YaapPanelMode::RIGHT);
				?>
			</div>
			<div class="left">
				<?php include_once YAAP_PATH.'pages/menus/menu_left_login.php'; ?>
				<?php include_once YAAP_PATH.'pages/menus/'.($menu_left_current == null ? MENU_LEFT_DEFAULT : $menu_left_current); ?>
			</div>
		</div>
		<div class="footer">
			<?php include_once YAAP_PATH.'pages/layout/footer.php'; ?>
		</div>
	</div>
</body>
</html>