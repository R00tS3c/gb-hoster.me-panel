<?php
$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/libs/lgsl/lgsl_class.php');
require($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/SSH2.php");
require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/Crypt/AES.php");

$pocetak = time();

set_time_limit(0);

$lgsl_config['cache_time'] = 300;


/*------------------------------------------------------------------------------------------------------+
 * DOBIJANJE PODATAKA ZA SERVERE ( LGSL )
/*------------------------------------------------------------------------------------------------------*/
lgsl_database();

// SETTINGS:

$request = "sep";                // WHAT TO PRE-CACHE: [s] = BASIC INFO [e] = SETTINGS [p] = PLAYERS

echo "<pre>ROK: [ ".ini_get("max_execution_time")." ] [ CACHE VREME: {$lgsl_config['cache_time']} ]\r\n\r\n";

//------------------------------------------------------------------------------------------------------------+
$sql  = "SELECT `type`,`ip`,`c_port`,`q_port`,`s_port` FROM `lgsl` WHERE `disabled` = '0' ORDER BY `cache_time` ASC";
$mysql_result = mysql_query($sql);

while($row = mysql_fetch_array($mysql_result))
{
	echo	str_pad(lgsl_timer("taken"),  8,  " ").":".
			str_pad($row['type'],   15, " ").":".
			str_pad($row['ip'],     30, " ").":".
			str_pad($row['c_port'], 6,  " ").":".
			str_pad($row['q_port'], 6,  " ").":".
			str_pad($row['s_port'], 12, " ")."\r\n";

	lgsl_query_cached($row['type'], $row['ip'], $row['c_port'], $row['q_port'], $row['s_port'], "sep");

	flush();
}
//------------------------------------------------------------------------------------------------------------+

echo "\r\nKraj</pre>";

//------------------------------------------------------------------------------------------------------------+

$kraj = time();
$vreme = $kraj - $pocetak;
echo "Potrebno vreme za skeniranje: {$vreme}";

$q = mysql_query("SELECT * FROM `lgsl` ORDER BY id ASC");

echo "<pre>\r\n\r\n";

echo	str_pad("Type",   15, " ").":".
		str_pad("IP Adresa",  30, " ").":".
		str_pad("C Port", 10,  " ").":".
		str_pad("Q Port", 10,  " ").":".
		str_pad("Igraci", 15, " ")."\r\n\n";

while ($kolona = mysql_fetch_array($q)) 
{
	$podaci = unserialize($kolona['cache']);
	$players = $podaci['s']['players'];

	// RANK SISTEM - START -
	$points = $kolona['rank_bodovi'];
	$points += ($players) / 10;
	query_basic("UPDATE `lgsl` SET `rank_bodovi` = '{$points}' WHERE `id` = '{$kolona['id']}' LIMIT 1");
	// RANK SISTEM - END -

	if(empty($kolona["igraci_5min"])) $kolona5min = "0";
	else $kolona5min = $kolona["igraci_5min"];
	$igrachi = explode(":",$kolona5min);
	
	for ($i=48; $i > 0; $i--) $igrachi[$i] = $igrachi[$i-1];
	
	$igrachi[0] = $players;
	$igraci = implode(":",$igrachi);
	query_basic("UPDATE `lgsl` SET `igraci_5min` = '{$igraci}' WHERE `id` = '{$kolona['id']}' LIMIT 1");
	
	echo	str_pad($kolona['type'],   15, " ").":".
			str_pad($kolona['ip'],  30, " ").":".
			str_pad($kolona['c_port'], 10,  " ").":".
			str_pad($kolona['q_port'], 10,  " ").":".
			str_pad($players, 15, " ")."\r\n";
			
}

/* BOX LOAD GRAFIK */
$boxdata = mysql_query("SELECT * FROM `box` ORDER BY boxid ASC");

while ($column = mysql_fetch_array($boxdata)) 
{
	$data = unserialize(gzuncompress($column['cache']));
	$load = $data["{$column['boxid']}"]['loadavg']['loadavg'];
	if(empty($column["box_load_5min"])) $column5min = "0";
	else $column5min = $column["box_load_5min"];
	$load_data = explode(":",$column5min);
	
	for ($i=48; $i > 0; $i--) $load_data[$i] = $load_data[$i-1];

	$load = str_replace("Unknown HZ value! (28) Assume 100.
Warning: /boot/System.map-3.10.9-xxxx-grs-ipv6-64 has an incorrect kernel version.
 ", "", $load);
	$load = str_replace("Unknown HZ value! (776) Assume 100.
			Warning: /boot/System.map-3.10.9-xxxx-grs-ipv6-64 has an incorrect kernel version.", "", $load);
	$load = str_replace("Unknown HZ value! (28) Assume 100.
Warning: /boot/System.map-3.10.9-xxxx-grs-ipv6-64 has an incorrect kernel version.
 ", "", $load);		

	$load_data[0] = $load;
	$loadavg = implode(":",$load_data);
	query_basic("UPDATE `box` SET `box_load_5min` = '{$loadavg}' WHERE `boxid` = '{$column['boxid']}' LIMIT 1");	
}

echo "\r\nKraj</pre>";

update_cron( );

function update_cron( ) {
	$CronName = basename($_SERVER["SCRIPT_FILENAME"], '.php');
	
	if( query_numrows( "SELECT * FROM `crons` WHERE `cron_name` = '$CronName'" ) == 1 ) {
		mysql_query( "UPDATE `crons` SET `cron_value` = '".date('Y-m-d H:i:s')."' WHERE `cron_name` = '$CronName'" );
	} else {
		mysql_query( "INSERT INTO `crons` SET `cron_name` = '".$CronName."', `cron_value` = '".date('Y-m-d H:i:s')."'" );
	}
}