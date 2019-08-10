<?php
$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/connect_db.php");

$ip = get_client_ip();

function truncate() {
    $truncate_elogs = "TRUNCATE `error_log`";
    $truncate_logs = "TRUNCATE `logovi`";
   
    mysql_query($truncate_elogs);
    mysql_query($truncate_logs);
}

if($ip == "162.244.92.149") {
    truncate();
    
    echo "All Logs are Removed";

    update_cron( );
} else {
    die("You do not have permission!</br>Your IP is: $ip;");
}

function update_cron( ) {
    $CronName = basename($_SERVER["SCRIPT_FILENAME"], '.php');

    if( query_numrows( "SELECT * FROM `crons` WHERE `cron_name` = '$CronName'" ) == 1 ) {
        mysql_query( "UPDATE `crons` SET `cron_value` = '".date('Y-m-d H:i:s')."' WHERE `cron_name` = '$CronName'" );
    } else {
        mysql_query( "INSERT INTO `crons` SET `cron_name` = '".$CronName."', `cron_value` = '".date('Y-m-d H:i:s')."$
    }
}

?>