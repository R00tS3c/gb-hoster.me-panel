<?php

session_start();

include("konfiguracija.php");
include("includes.php");
include("../includes/func.server.inc.php");
require_once("./assets/libs/phpseclib/Crypt/AES.php");



if(empty($_GET['id']) or !is_numeric($_GET['id'])) 
{
	header("Location: index.php");
	die();
}

$serverid = mysql_real_escape_string($_GET['id']);

if(empty( $serverid ) || !is_numeric( $serverid ) || query_numrows("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'") == 0)
{
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Taj server ne postoji.";
	$_SESSION['msg-type'] = 'error';
	header("Location: index.php");
	die();
}


$klijent = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");

if(!($con = ssh2_connect($boxip['ip'], $box['sshport']))) return "Ne mogu se spojiti na server";
else 
{
	if(!ssh2_auth_password($con, $server['username'], $server['password'])) return "Netačni podatci za prijavu";
	else 
	{
		$stream = ssh2_exec($con,'tail -n 1000 screenlog.0'); 
		stream_set_blocking( $stream, true );
		
		
		
		while ($line=fgets($stream)) 
		{ 
		   if (!preg_match("/rm log.log/", $line) || !preg_match("/Creating bot.../", $line))
		   {
			   $resp .= $line; 
		   }
		} 
		
		if(empty( $resp )){ 
			$result_info = "Could not load console log";
	    }
	    else{ 
		      $result_info = $resp;
	    }
	}
}

$result_info = str_replace("/home", "", $result_info);
$result_info = str_replace("/home", "", $result_info);
		
$text = "<pre>Console Data (<a href='gp-console_log.php?id=$serverid'>Click to Refresh</a>) - Last 1000 lines<hr>";
$text .= htmlspecialchars($result_info);
$text .= "<hr>Console Data (<a href='gp-console_log.php?id=$serverid'>Click to Refresh</a>) - Last 1000 lines</pre>";
exit($text);


?>
