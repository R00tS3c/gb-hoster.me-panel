<?php
$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");


$servers = mysql_query("SELECT * FROM serveri");

while($row = mysql_fetch_assoc($servers)) {
	mysql_query( "UPDATE `lgsl` SET `rank_bodovi` = '0' WHERE `id` = '".$row['id']."'" );
}

unset($servers);

echo "Server stats is Restarted";


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