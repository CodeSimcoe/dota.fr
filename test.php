<?php

?>
<html>
	<head>
		<title>Detector</title>
		<script type="text/javascript" src="/ligue/javascript/jquery.js"></script>
		<script type="text/javascript" src="/ligue/javascript/jquery.swf.js"></script>
		<script type="text/javascript" src="/ligue/javascript/jquery.stats.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				var fontDetect = new FontDetect("jquery-stats", "/ligue/FontList.swf", function(fd) {
					var fonts = fd.fonts();
					var text = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
					var size = "32px";
					alert(fonts.length);
					for(var i = 0; i < fonts.length; i++) {
						var node = document.createElement("p");        
						$(node).css("font-family", "'" + fonts[i].fontName + "'");
						$(node).css("font-size", size);
						$(node).addClass("sample-text");
						$(node).html(text);
						$("#content").append(node);
						var nameNode = document.createElement("p");
						$(nameNode).addClass("sample-text-name");
						$(nameNode).html("[" + fonts[i].fontName + "]");
						$("#content").append(nameNode);
					}
					var result = [];
					result.push('USER AGENT: ' + navigator.userAgent);
					result.push('HTTP_ACCEPT: <?php echo $_SERVER["HTTP_ACCEPT"]; ?>');
					result.push('TIMEZONE: ' + new Date().getTimezoneOffset());
					result.push('SCREEN: ' + screen.width + 'x' + screen.height + 'x' + screen.colorDepth);
					result.push('PLUGINS:<blockquote>');
					$(navigator.plugins).each(function() { result.push(this.name); result.push(this.description); result.push(this.filename); });
					result.push('</blockquote>')
					$('div:eq(0)').html(result.join('<br />'));
				});
			});
		</script>
	</head>
	<body>
		<div></div>
		<div id="content"></div>
		<div id="jquery-stats"></div>
	</body>
</html>