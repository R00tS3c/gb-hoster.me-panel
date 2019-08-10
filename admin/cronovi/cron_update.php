<?php

$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/libs/lgsl/lgsl_class.php');
require($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/SSH2.php");
require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/Crypt/AES.php");

$boxid = 34;

echo update_box($boxid)." | Box ID : $boxid<br />";

function update_box($boxid) {
	$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '{$boxid}'");
	$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `boxid` = '{$boxid}'");
	
	$aes = new Crypt_AES();
	$aes->setKeyLength(256);
	$aes->setKey(CRYPT_KEY);
	
	$boxpw = $aes->decrypt($box['password']);
	
	if (!function_exists("ssh2_connect")) echo "SSH2 PHP extenzija nije instalirana.";
	
	if(!($con = ssh2_connect("$boxip[ip]", "$box[sshport]"))) echo "Ne mogu se spojiti sa serverom.";
	else {
		if(!ssh2_auth_password($con, "$box[login]", "$boxpw")) echo "Netačni podatci za prijavu";
		else {
			$cmd = "cd /home;rm -rf GameFiles;mkdir GameFiles;cd GameFiles;wget 94.156.174.134/GameFiles.tar.gz;tar xvfz GameFiles.tar.gz;rm -rf GameFiles.tar.gz;chown -R 777 *";
			
			$stream = ssh2_shell($con, 'xterm');
			fwrite( $stream, "$cmd\n");
			sleep(300);
			
			$data = "";
			
			while($line = fgets($stream)) {
				$data .= $line;
			}	
			
			echo 'Box updated!';
		}
	}
}

?>