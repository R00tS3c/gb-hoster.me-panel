<?php
$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/libs/lgsl/lgsl_class.php');
require($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/SSH2.php");
require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/Crypt/AES.php");


/*------------------------------------------------------------------------------------------------------+
 * DOBIJANJE PODATAKA ZA SERVERE ( LGSL )
/*------------------------------------------------------------------------------------------------------*/
lgsl_database();

// SETTINGS:

$lgsl_config['cache_time'] = 30; // HOW OLD CACHE MUST BE BEFORE IT NEEDS REFRESHING
$request = "sep";                // WHAT TO PRE-CACHE: [s] = BASIC INFO [e] = SETTINGS [p] = PLAYERS

//------------------------------------------------------------------------------------------------------------+

$mysql_query  = "SELECT `type`,`ip`,`c_port`,`q_port`,`s_port` FROM `lgsl` WHERE `disabled`=0 ORDER BY `cache_time` ASC";
$mysql_result = mysql_query($mysql_query) or die(mysql_error());

while($mysql_row = mysql_fetch_array($mysql_result, MYSQL_ASSOC))
{
	lgsl_query_cached($mysql_row['type'], $mysql_row['ip'], $mysql_row['c_port'], $mysql_row['q_port'], $mysql_row['s_port'], $request);
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
