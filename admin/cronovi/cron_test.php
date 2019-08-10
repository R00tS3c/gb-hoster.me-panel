<?php
$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/libs/lgsl/lgsl_class.php');
require($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/SSH2.php");
require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/Crypt/AES.php");

$servers = mysql_query("SELECT * FROM `serveri` WHERE `igra`='1'");

while($row = mysql_fetch_assoc($servers)) {
	$serverid = $row['id'];
	echo download_steamclient($serverid)." | Server ID : $serverid<br />";
}

unset($servers);

function download_steamclient($serverid) {
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
			$cmd = "rm -rf /home/$server[username]/.steam/;cd /home/$server[username]/;mkdir .steam;cd /home/$server[username]/.steam/;mkdir sdk32;cd /home/$server[username]/.steam/sdk32/;wget www.gb-hoster.me/Tools/steamclient.so;chmod -R 777 *;chown -Rf $server[username]:$server[username] /home/$server[username]/.steam/sdk32/steamclient.so";
			
			$stream = ssh2_shell($con, 'xterm');
			fwrite( $stream, "$cmd\n");
			sleep(1);
			
			$data = "";
			
			while($line = fgets($stream)) {
				$data .= $line;
			}	
			
			echo 'Sucessfully Downloaded steamclient.so';
		}
	}
}

?>