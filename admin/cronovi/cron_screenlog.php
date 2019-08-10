<?php
$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/libs/lgsl/lgsl_class.php');
require($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/SSH2.php");
require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/Crypt/AES.php");

//$hour = date('H');

//$servers = mysql_query("SELECT * FROM `serveri` WHERE `cache`='{$hour}'");

$servers = mysql_query("SELECT * FROM `serveri`");

while($row = mysql_fetch_assoc($servers)) {
	$serverid = $row['id'];
	
	if($serverid != 489)
		echo delete_screenlog($serverid)." | Server ID : $serverid<br />";
	//echo delete_screenlog($serverid)." | Server ID : $serverid<br />";
}

unset($servers);

function delete_screenlog($serverid) {
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
			$cmd = "rm -rf /home/$server[username]/screenlog.0";
			
			$stream = ssh2_shell($con, 'xterm');
			fwrite( $stream, "$cmd\n");
			sleep(1);
			
			$data = "";
			
			while($line = fgets($stream)) {
				$data .= $line;
			}	
			
			echo 'Deleted screenlog.0';
		}
	}
}


update_cron( );

function update_cron( ) {
	$CronName = basename($_SERVER["SCRIPT_FILENAME"], '.php');
	
	if( query_numrows( "SELECT * FROM `crons` WHERE `cron_name` = '$CronName'" ) == 1 ) {
		mysql_query( "UPDATE `crons` SET `cron_value` = '".date('Y-m-d H:i:s')."' WHERE `cron_name` = '$CronName'" );
	} else {
		mysql_query( "INSERT INTO `crons` SET `cron_name` = '".$CronName."', `cron_value` = '".date('Y-m-d H:i:s')."'" );
	}
}

?>