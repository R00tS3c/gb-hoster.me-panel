<?php
//if(!isset($_GET['sifra'])) die("Morate uneti sifru!");
error_reporting(E_ERROR | ERROR_WARNING);
$sifra = $_GET['sifra'];

//if($sifra != "krontab123xasd") die("Sifra nije tacna!");

$fajl = "login";

//include("../../../includes.php");

include("includess.php");

$pocetak = time();

set_time_limit(0);
 
require "lgsl_class.php"; global $lgsl_config;
lgsl_database();
$lgsl_config['cache_time'] = 300;    


//------------------------------------------------------------------------------------------------------------+

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
	mysql_query("UPDATE `lgsl` SET `rank_bodovi` = '{$points}' WHERE `id` = '{$kolona['id']}' LIMIT 1");
	// RANK SISTEM - END -

	if(empty($kolona["igraci_5min"])) $kolona5min = "0";
	else $kolona5min = $kolona["igraci_5min"];
	$igrachi = explode(":",$kolona5min);
	
	for ($i=48; $i > 0; $i--) $igrachi[$i] = $igrachi[$i-1];
	
	$igrachi[0] = $players;
	$igraci = implode(":",$igrachi);
	mysql_query("UPDATE `lgsl` SET `igraci_5min` = '{$igraci}' WHERE `id` = '{$kolona['id']}' LIMIT 1");
	
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
	mysql_query("UPDATE `box` SET `box_load_5min` = '{$loadavg}' WHERE `boxid` = '{$column['boxid']}' LIMIT 1");	
}

echo "\r\nKraj</pre>";




mysql_query( "UPDATE `config` SET `value` = '".date('Y-m-d H:i:s')."' WHERE `setting` = 'lgsl_cron'" );

?>
