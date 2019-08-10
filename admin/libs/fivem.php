<?php

ini_set('default_socket_timeout', 5);

function fivemstatus($ip, $port) {
	$url = "http://".$ip.":".$port."/info.json";
	
	if(!urlExists($url)) {
		return false;
	}
	
	$content = json_decode(file_get_contents($url), true);
	if($content):
		return true;
	else:
		return false;
	endif;
}

function fivemplayers($ip, $port) {
	$url = "http://".$ip.":".$port."/players.json";
	
	if(!urlExists($url)) {
		return 0;
	}
	
	$igraci = file_get_contents($url);
	$content = json_decode($igraci, true);
	$broj = count($content);
	return $broj;
}

function urlExists($url=NULL) {
	if($url == NULL) return false;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if($httpcode >= 200 && $httpcode < 300){
		return true;
	} else {
		return false;
	}
}

?>