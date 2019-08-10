<?php
$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/libs/lgsl/lgsl_class.php');
require($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/SSH2.php");
require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/Crypt/AES.php");

$servers = mysql_query("SELECT * FROM serveri");

while($row = mysql_fetch_assoc($servers)) {
	file_get_contents("http://gb-hoster.me/admin/srv-grafik.php?id={$row[id]}&cron=gbh312#");
	file_get_contents("http://gb-hoster.me/gp-srvgrafik.php?id={$row[id]}&cron=gbh312#");
	file_get_contents("http://gb-hoster.me/gp-banner.php?id={$row[id]}&cron=gbh312#");
}

unset($servers);

/*------------------------------------------------------------------------------------------------------+
 * BRISANJE ISTEKLIH BANOVA
/*------------------------------------------------------------------------------------------------------*/

if(BRISANJE_ISTEKLIH_BANOVA_KLIJENTA) {
	$starttime = microtime(true);
	
	$vreme = strtotime(date("m/d/Y", time()));
	$banovi = mysql_query("SELECT id, klijentid FROM banovi WHERE  trajanje < {$vreme}");
	$br = 0;
	
	while($row = mysql_fetch_array($banovi)) {	
		mysql_query("UPDATE `klijenti` SET `banovan` = '0' WHERE `klijentid` = '".$row['klijentid']."'");
		mysql_query("DELETE FROM `banovi` WHERE `id` = '{$row['id']}'");
		$br++;
	}
	
	$endtime = microtime(true);
	
	echo 'Unbannovano je : '.$br.' klijenata.<br />';
	
	unset($vreme);
	unset($banovi);
	unset($br);
}

/*------------------------------------------------------------------------------------------------------+
 * ISTEKLI SERVERI
/*------------------------------------------------------------------------------------------------------*/

if(ISTEKLI_SERVERI_STATUS) {
	$starttime = microtime(true);
	
	$server = mysql_query("SELECT istice, id FROM `serveri` WHERE `status` = 'Aktivan'");
	$br = 0;
	
	while($row = mysql_fetch_assoc($server)) {
		if(strtotime($row['istice']) < strtotime(date("Y-m-d", time()))) {
			query_basic("UPDATE `serveri` SET `status` = 'Istekao' WHERE `id` = '".$row['id']."'");	
			$br++;			
		}
	}
	
	$endtime = microtime(true);
	
	echo 'Istekli su: '.$br.' servera.<br />';
	unset($br);
	unset($server);
}

/*------------------------------------------------------------------------------------------------------+
 * SUSPENDOVANJE ISTEKLIH SERVERA
/*------------------------------------------------------------------------------------------------------*/

if(SUSPEND_ISTEKLI_SERVERI) {
	$starttime = microtime(true);
	
	$server = mysql_query("SELECT * FROM `serveri` WHERE `status` = 'Istekao'");
	
	$br = "0";
	$vreme = time() + (-SUSPEND_ISTEKLI_SERVERI_VREME * 24 * 60 * 60);  
	
	while($row = mysql_fetch_assoc($server))
	{
		if(strtotime($row['istice']) < strtotime(date("Y-m-d", $vreme)))
		{
			$br++;
			
			query_basic("UPDATE `serveri` SET `status` = 'Suspendovan' WHERE `id` = '".$row['id']."'");
		}
	}
	
	$endtime = microtime(true);
	
	echo 'Suspendovani su: '.$br.' servera.<br />';
	
	unset($br);
	unset($ip);
	unset($box);
	unset($server);
	unset($vreme);
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