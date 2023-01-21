<?php
abstract class RegExps {
	
	const VERSION_PATTERN = '`^[0-9]\.[0-9]{1,2}[a-z]?$`';
	const TEAM_NAME_PATTERN = '`^[a-zA-Z0-9_\[\]\-\. ]{4,}$`';
	const TEAM_TAG_PATTERN = '`^[a-zA-Z0-9_\[\]\-\.]{2,4}$`';
	const TEAM_PASSWORD_PATTERN = '`^[a-zA-Z0-9]{4,15}$`';
	const USERNAME_PATTERN = '`^[a-zA-Z0-9_\-\.]{1,25}$`';
	const ACTIVATION_KEY_PATTERN = '`^[a-z0-9]{16}$`';
}
?>