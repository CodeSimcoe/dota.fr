
<?php

	if ($_SESSION['access'] < 101) exit();

?>
<script type="text/javascript">
postIt = function(replay) {
	document.getElementById('rcreplay').value = replay;
	document.getElementById('rcanalyze').submit();
}
</script>
<table class="simple">
	<tr>
		<td class="top_left"></td>
		<td class="top">Replay Center - En attente</td>
		<td class="top_right"></td>
	</tr>
	<tr>
		<td class="left"></td>
		<td>
			<form action="/ligue/?f=rc/rcanalyze" method="post" id="rcanalyze" name="rcanalyze">
			<input type="hidden" name="rcreplay" id="rcreplay" value="" />
			<table style="width: 96%; margin: 0px 2%;">
				<thead>
					<tr>
						<th style="width: 80px;">&nbsp;</th>
						<th>Fichier</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2" class="line"></td>
					</tr>
				<?php
					$path = "rc/rctmp";
					$folder = opendir($path);
					while ($file = readdir($folder)) {
						if ($file != "." && $file != "..") {
							$ext = strtolower(substr($file, strrpos($file, '.') + 1));
							if ($ext == "w3g") {
				?>
					<tr>
						<td><a href="javascript:void(0);" onclick="postIt('<?php echo $file; ?>');">Analyse</a></td>
						<td><?php echo $file; ?></td>
					</tr>
				<?php
							}
						}
					}
					closedir($folder);
				?>
				</tbody>
			</table>
			</form>
		</td>
		<td class="right"></td>
	</tr>
	<tr>
		<td class="bottom_left"></td>
		<td class="bottom"></td>
		<td class="bottom_right"></td>
	</tr>
</table>
