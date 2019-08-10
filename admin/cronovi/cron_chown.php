<?php
$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/libs/lgsl/lgsl_class.php');
require($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/SSH2.php");
require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/Crypt/AES.php");

$servers = mysql_query("SELECT * FROM `serveri` LIMIT 40, 50");

while($row = mysql_fetch_assoc($servers)) {
	$serverid = $row['id'];
	
	echo chown_server($serverid)." | Server ID : $serverid<br />";
}

unset($servers);

function chown_server($serverid) {
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '{$serverid}'");
	$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '{$server[box_id]}'");
	$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `boxid` = '{$server[box_id]}'");
	
	$aes = new Crypt_AES();
	$aes->setKeyLength(256);
	$aes->setKey(CRYPT_KEY);
	
	$boxpw = $aes->decrypt($box['password']);
	
	if (!function_exists("ssh2_connect")) echo $jezik['text290'];
	
	if(!($con = ssh2_connect("$boxip[ip]", "$box[sshport]"))) echo $jezik['text292'];
	else {
		if(!ssh2_auth_password($con, "$box[login]", "$boxpw")) echo $jezik['text293'];
		else {
			$cmd = "chown -Rf $server[username]:$server[username] /home/$server[username]/*";
			
			$stream = ssh2_shell($con, 'xterm');
			fwrite( $stream, "$cmd\n");
			sleep(1);
			
			$data = "";
			
			while($line = fgets($stream)) {
				$data .= $line;
			}	
			
			echo 'Server chowned';
		}
	}
}

?>