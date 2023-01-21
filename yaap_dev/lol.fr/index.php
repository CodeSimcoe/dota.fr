<?php

	require_once '__local.config.php';

	include_once '__local.template.php';

?>


<?php

/*
	include_once '__local.config.php';
*/
	/*
	$qry = $global_bdd->prepare("SELECT uid, username, password, email, is_active, creation_date FROM users WHERE username = :username");
	$qry->execute(array(':username' => 'aurelien.net'));
	$qry->setFetchMode(PDO::FETCH_CLASS, FACTORY_USER_TYPE);
	$user = $qry->fetch();
	$qry->closeCursor();

	$qry = $local_bdd->prepare("
		SELECT 
			T1.ggc_account AS 'ggc_account', 
			T2.id AS 'team_id', 
			T2.name AS 'team_name' 
		FROM users_infos AS T1
		LEFT JOIN teams_infos AS T2 
		ON T1.team_id = T2.id 
		WHERE uid = :uid
	");
	$qry->execute(array(':uid' => $user->uid));
	$qry->bindColumn('ggc_account', $user->ggc_account);
	$qry->bindColumn('team_id', $user->team->id);
	$qry->bindColumn('team_name', $user->team->name);
	$qry->setFetchMode(PDO::FETCH_INTO, $user);
	$user = $qry->fetch();
	$qry->closeCursor();

	echo $user->ggc_account.'<br />';
	echo $user->uid.'<br />';
	echo $user->team->id.'<br />';
	echo $user->team->name.'<br />';
	*/
/*
	$qry = $local_bdd->query("
		SELECT 
			T1.uid,
			T1.username,
			T1.password,
			T1.email,
			T1.creation_date,
			T1.is_active,
			T1.ggc_account, 
			T2.id AS 'team->id', 
			T2.name AS 'team->name' 
		FROM users_infos AS T1
		LEFT JOIN teams_infos AS T2 
		ON T1.team_id = T2.id 
	");
	$qry->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, FACTORY_USER_TYPE);
	$users = $qry->fetchAll();
	$qry->closeCursor();

	echo $users[1]->ggc_account.'<br />';
	echo $users[1]->uid.'<br />';
	echo $users[1]->creation_date.'<br />';
	echo $users[1]->is_active.'<br />';
	echo $users[1]->team->id.'<br />';
	echo $users[1]->team->name.'<br />';
*/
?>