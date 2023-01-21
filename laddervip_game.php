<script language="javascript">
	//Ajax
	var http;
	var game_id;
	game_id = <?php echo (int)$_GET['id']; ?>;
	
	function PickPlayer(player, game_id, cap) {
		ShowLoading();
		
		$.get('ajax/laddervip_pickplayer.php',
			{
				player: player,
				id: game_id
			},
			function (data) {
				Refresh(0);
			}
		);
	}
	
	function PickHero(hero) {
		ShowLoading();

		$.get('ajax/laddervip_pickhero.php',
			{
				id: game_id,
				hero: hero
			},
			function (data) {
				Refresh(0);
			}
		);
	}
	
	function PickSide(side, game_id) {
		ShowLoading();
		
		$.get('ajax/laddervip_pickside.php',
			{
				id: game_id,
				side: side
			},
			function (data) {
				Refresh(0);
			}
		);
	}
	
	function BanHero(hero) {
		ShowLoading();

		$.get('ajax/laddervip_banhero.php',
			{
				id: game_id,
				hero: hero
			},
			function (data) {
				Refresh(0);
			}
		);
	}
	
	function ShowLoading() {
		$('#loader').html('<img src="img/ajax-loader2.gif" alt="" />');
	}
	
	function Refresh(loadIcon) {
		ShowLoading();
		if (loadIcon == 1) ShowLoading();
		
		$.get('laddervip_gameinc.php',
			{
				id: game_id
			},
			function (data) {
				$('#zoneref').html(data);
				$('#loader').html('<img src="img/black.jpg" alt="" />');
				$('#btn_refresh').html('<a href="javascript:Refresh(1);"><img src="ladder/btn_refresh.jpg" alt="" /></a>');
			}
		);
	}

	function createRequestObject() {
	    var http;
	    if (window.XMLHttpRequest) {
	        http = new XMLHttpRequest();
		}
	    else if (window.ActiveXObject) {
			http = new ActiveXObject("Microsoft.XMLHTTP");
		}
	    return http;
	}
	
	function decTimer() {
		var sec = parseInt(document.getElementById('timer').innerHTML);
		sec--;
		if (sec < 0) sec = 0;
		document.getElementById('timer').innerHTML = sec;
	}

	setInterval("Refresh(1)", 9000);
	setInterval("decTimer()", 1000);
</script>

<?php
	function Minutes($started) {
		return round((time() - $started) / 60, 0);
	}
	
	echo '<div id="zoneref">';
	include 'laddervip_gameinc.php';
	echo '</div>';
?>
