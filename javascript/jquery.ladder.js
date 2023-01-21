$(document).ready(function() {
	$('#ladder_listing #refresh').live('click', function() {
		if (this.src.indexOf('norefresh') != -1) return;
		this.src = this.src.replace('refresh', 'norefresh');
		this.style.cursor = 'default';
		$('#ladder_listing').load('ajax/ladder_listing.php');
	});
});