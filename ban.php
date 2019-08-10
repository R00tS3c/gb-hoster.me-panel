<?php

$banned_ips = array("62.4.55.26", "IP Adresa");
$arrlength = count($banned_ips);

check_ban($banned_ips, $arrlength);

function check_ban($banned_ips, $arrlength) {
	for($x = 0; $x < $arrlength; $x++) {
		if(get_client_ip_adress() == $banned_ips[$x])
			die("You are banned!");
	}
}

function get_client_ip_adress() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
}
/*
if(get_client_ip_adress() == "109.245.27.9") {
	session_destroy();
	
	header("Location: /home");
	die();
}
*/
?>