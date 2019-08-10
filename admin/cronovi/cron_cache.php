<?php

$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/libs/lgsl/lgsl_class.php');
require($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/SSH2.php");
require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/Crypt/AES.php");

$box = mysql_query( "SELECT * FROM `box` WHERE `cache_time` < '".time()."'" );

$i = 0;

while($row = mysql_fetch_assoc($box)) {
	$i++;
	if($i <= 5) {
		$boxid = $row['boxid'];
		$box_cache_time = time() + (60*60*6); // svakih 6 sati
		query_basic("UPDATE `box` SET `cache_time` = '".$box_cache_time."' WHERE `boxid` = '".$boxid."'");
		echo delete_cache($boxid)." | Box ID : $boxid<br />";
		update_cron( );
	}
}

if($_GET["reset"]) {
	$box = mysql_query( "SELECT * FROM `box`" );
	
	while($row = mysql_fetch_assoc($box)) {
		$boxid = $row['boxid'];
		query_basic("UPDATE `box` SET `cache_time` = '0' WHERE `boxid` = '".$boxid."'");
	}
}

function delete_cache($boxid) {
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
			$cmd = "rm -rf clear.sh;rm -rf clear.sh*;cd /;find . -type f -iname \*.ztmp -delete;cd /root;wget www.gb-hoster.me/Tools/clear.sh;chmod -R 777 clear.sh;screen ./clear.sh";
			
			$stream = ssh2_shell($con, 'xterm');
			fwrite( $stream, "$cmd\n");
			sleep(1);
			
			$data = "";
			
			while($line = fgets($stream)) {
				$data .= $line;
			}	
			
			echo 'Cache deleted!';
		}
	}
}

function update_cron( ) {
	$CronName = basename($_SERVER["SCRIPT_FILENAME"], '.php');
	
	if( query_numrows( "SELECT * FROM `crons` WHERE `cron_name` = '$CronName'" ) == 1 ) {
		mysql_query( "UPDATE `crons` SET `cron_value` = '".date('Y-m-d H:i:s')."' WHERE `cron_name` = '$CronName'" );
	} else {
		mysql_query( "INSERT INTO `crons` SET `cron_name` = '".$CronName."', `cron_value` = '".date('Y-m-d H:i:s')."'" );
	}
}

?>