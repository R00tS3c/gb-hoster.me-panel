<?php
$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/libs/lgsl/lgsl_class.php');
require($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/SSH2.php");
require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/Crypt/AES.php");

error_reporting(E_ERROR | E_WARNING | E_PARSE);
/*------------------------------------------------------------------------------------------------------+
 * AUTO RESTART
/*------------------------------------------------------------------------------------------------------*/
$hour = date('H');
$serverx = mysql_query("SELECT * FROM `serveri` WHERE `autorestart`='{$hour}' AND `startovan`='1'");

echo "CRON AUTORESTART !<br />\n";
echo "Restarting all servers scheduled for {$hour}:00 !<br />\n";

while($row = mysql_fetch_array($serverx))
{
	
	
    $ip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$row['ip_id']."'");
	$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$row['box_id']."'");
	$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$row['ip_id']."'");
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$row['id']."'");
	$serverid = $row['id'];
	$serverime = $row['name'];


    stop_server($ip['ip'], $box['sshport'], $server['username'], $server['password'], $serverid, "admin", TRUE);
	start_server($ip['ip'], $box['sshport'], $server['username'], $server['password'], $serverid, "admin", TRUE);
				
    echo $row['name'] ."\n";
	$poruka = "Autorestart za ".$hour.":00 pokrenut. ( Server : <m>".$serverime."</m> - Server ID : <m>".$serverid."</m>)";
	$poruka = mysql_real_escape_string($poruka);
	alog(0, $poruka, 'Hosting', fuckcloudflare());
	
}

echo "Finished !";

function start_server($ip, $port, $username, $password, $serverid, $klijentid, $restart)
{
	global $jezik;

	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	
	if($restart == FALSE)
	{	
		if($server['startovan'] == "1")
		{
			echo $jezik['text291'];
		}
	}

	if (!function_exists("ssh2_connect")) echo $jezik['text290'];

	if(!($con = ssh2_connect($ip, $port))) echo $jezik['text292'];
	else 
	{
		if(!ssh2_auth_password($con, $username, $password)) echo $jezik['text293'];
		else 
		{
			if($server['igra'] == "1")
			{
				$komanda = $server["komanda"];
				$komanda = str_replace('{$ip}', $ip, $komanda);
				$komanda = str_replace('{$port}', $server['port'], $komanda);
				$komanda = str_replace('{$slots}', $server['slotovi'], $komanda);
				$komanda = str_replace('{$map}', $server['map'], $komanda);
				$komanda = str_replace('{$fps}', $server['fps'], $komanda);	
			}
			else if($server['igra'] == "3")
			{
				$komanda = $server["komanda"];

				// Max Ram ( SLOT * 51.2)
				$mr = ($server['slotovi'] * 51.2);

				// Min Ram
				$minr = "512";

				$komanda = str_replace('{$maxram}', $mr, $komanda);
				$komanda = str_replace('{$minram}', $minr, $komanda);		
			}
			else
			{
				$komanda = $server["komanda"];
			}

			$stream = ssh2_shell($con, 'vt102', null, 80, 24, SSH2_TERM_UNIT_CHARS);
			fwrite( $stream, "screen -mSL $username".PHP_EOL);
			sleep(1);
			fwrite( $stream, "$komanda".PHP_EOL);
			sleep(1);
			fwrite( $stream, "rm log.log".PHP_EOL);
			sleep(1);
			
			$data = "";
			
			while($line = fgets($stream)) 
			{
				$data .= $line;
			}
			query_basic("UPDATE `serveri` SET `startovan` = '1' WHERE `id` = '".$serverid."'");
			echo 'startovan';
		}
	}	
}


function stop_server($ip, $port, $username, $password, $serverid, $klijentid, $restart)
{
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");

	if($restart == FALSE)
	{
		if($server['startovan'] == "0")
		{
			echo "Server mora biti startovan!";
		}
	}

	if (!function_exists("ssh2_connect")) echo "SSH2 PHP extenzija nije instalirana";

	if(!($con = ssh2_connect($ip, $port))) echo "Ne mogu se spojiti na server";
	else 
	{
		if(!ssh2_auth_password($con, $username, $password)) echo "Netačni podatci za prijavu";
		else 
		{
			$stream = ssh2_shell($con, 'vt102', null, 80, 24, SSH2_TERM_UNIT_CHARS);
			fwrite( $stream, 'kill -9 `screen -list | grep "'.$username.'" | awk {\'print $1\'} | cut -d . -f1`'.PHP_EOL);
			sleep(1);
			fwrite( $stream, 'screen -wipe'.PHP_EOL);
			sleep(1);
			
			$data = "";
			
			while($line = fgets($stream)) 
			{
				$data .= $line;
			}
			query_basic("UPDATE `serveri` SET `startovan` = '0' WHERE `id` = '".$serverid."'");			
			echo 'stopiran';
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