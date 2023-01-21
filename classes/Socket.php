<?php

	ini_set('display_errors', 1);
	error_reporting(E_ALL ^ E_NOTICE);
	
	
    define ("LINE_END", "\n");

    function socket_normal_read ($socket, $length) {

        static $sockets = array ();
        static $queues = array ();
        static $sock_num = 0;

        for ($i = 0;  isset ($sockets[$i]) && $socket != $sockets[$i]; $i++);

        if (!isset ($sockets[$i])) {
            $sockets [$sock_num] = $socket;
            $queues [$sock_num++] = "";
        }

        $recv = socket_read ($socket, $length, PHP_BINARY_READ);
        if ($recv === "") {
            if (strpos ($queues[$i], LINE_END) === false)
                return false;
        }
        else if ($recv !== false) {
            $queues[$i] .= $recv;
        }

        $pos = strpos ($queues[$i], LINE_END);
        if ($pos === false)
            return "";
        $ret = substr ($queues[$i], 0, $pos);
        $queues[$i] = substr ($queues[$i], $pos+2);

        return $ret;
    }

	echo "<h2>Connexion TCP/IP</h2><br />";
	
	echo '<form method="GET" action="'.$_SERVER['PHP_SELF'].'">
		PORT<br /><input type="text" name="port" value="'.$_GET['port'].'" /><br />
		GN<br /><input type="text" name="gn" value="'.$_GET['gn'].'" /><br />
		<input type="submit" value="GO" />
	</form>';
	
	/* Lit le port du service WWW. */
	//$service_port = getservbyname('www', 'tcp');
	//$service_port = (int) $_GET['port'];
	$service_port = (int) $_GET['port'];
	if ($service_port == 0) exit('Port error');
	$gn = $_GET['gn'];

	/* Lit l'adresse IP du serveur de destination */
	//$address = gethostbyname('www.example.com');
	//$address = '127.0.0.1';
	$address = 'localhost';

	/* Cree une socket TCP/IP. */
	//$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	if ($socket === false) {
		echo "socket_create() a échoué : raison :  " . socket_strerror(socket_last_error()) . "<br />";
	} else {
		echo "OK.<br />";
	}

	echo "Essai de connexion à '$address' sur le port '$service_port'...";
	$result = socket_connect($socket, $address, $service_port);
	if ($socket === false) {
		echo "socket_connect() a échoué : raison : ($result) " . socket_strerror(socket_last_error($socket)) . "<br />";
	} else {
		echo "OK.<br />";
	}

	//$in = "HEAD / HTTP/1.0\r<br />\r<br />";
	//$in .= "Host: www.example.com\r<br />";
	//$in .= "Connection: Close\r<br />\r<br />";
	$in = 'game_create '.$gn;
	$out = '';

	echo 'Envoi de la requête';
	socket_write($socket, $in, strlen($in));
	echo 'OK.<br />';

	echo "Lire la réponse : <br />";
	//while ($out = socket_read($socket, 2048)) {
	while ($out = socket_normal_read($socket, 2048)) {
		echo $out;
	}

	echo '<br />Fermeture de la socket...<br />';
	socket_close($socket);
	echo "Socket closed.<br /><br />";
?>