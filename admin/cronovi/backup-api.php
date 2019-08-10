<?php
$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/libs/lgsl/lgsl_class.php');
require($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/SSH2.php");
require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/Crypt/AES.php");


$task = mysql_real_escape_string($_GET['task']);
$serverid = mysql_real_escape_string($_GET['serverid']);
$status = mysql_real_escape_string($_GET['status']);
$name = mysql_real_escape_string($_GET['name']);
$size = mysql_real_escape_string($_GET['size']);

if( $task == "backupapi" && is_numeric($serverid) ) {

    $size = get_size($size);
	
    mysql_query("UPDATE `server_backup` SET `size` = '".$size."', `status` = '".$status."' WHERE `name` = '".$name."'");
	
	exit("$task $serverid $status $name");
	
}
else if( $task == "restore" && is_numeric($serverid) ) 
{
	mysql_query("UPDATE `server_backup` SET `status` = '".$status."' WHERE `name` = '".$name."'");
	
}
else if( $task == "newbase" ) 
{
	$name = mysql_real_escape_string($_GET['name']);
    $size = mysql_real_escape_string($_GET['size']);

	$result_owns = mysql_query( "SELECT COUNT(id) AS thecount FROM server_backup WHERE name = '{$name}'"  );
	while ( $row_owns = mysql_fetch_array( $result_owns ) )
    {
        $user_owns2 = $row_owns['thecount'];
    }
	if (is_numeric($user_owns2) && $user_owns2 >= 1) {
  
        exit("vec postoji \n");
    }
	
	$size = get_size($size);
	
	$name2 = explode(".", $name);
	
	$name3 = query_fetch_assoc("SELECT * FROM `serveri` WHERE `username` = '".$name2[0]."'");
    
	$dasdas = $name3['id'];
	
	if( $dasdas ) 
	   mysql_query("INSERT INTO `server_backup` (srvid, name, time, status,size) VALUES('".$dasdas."', '".$name."', '".$name2[1]."', 'ok', '".$size."')");
	else
	   exit($name." ----- nema servera\n");
   
	exit($name." ---".$dasdas."--ok\n");
	
}
else if( $task == "cleanbackup" ) 
{
	$name = mysql_real_escape_string($_GET['name']);
        $size = mysql_real_escape_string($_GET['size']);
   
        $size = get_size($size);
	
	$name2 = explode(".", $name);
	
	$name3 = query_fetch_assoc("SELECT * FROM `serveri` WHERE `username` = '".$name2[0]."'");
    
	$dasdas = $name3['id'];

        if( !$dasdas ){
            //exit($name2[0]." ----- nema servera\n");
            mysql_query("DELETE FROM `server_backup` WHERE `name` = '".$name."'");
            exit("false");

        }else{
            //exit($name2[0]." ----- ima servera u bazi\n");
            exit("true");
	}
}
else if( $task == "cleanservers" ) 
{
	$name = mysql_real_escape_string($_GET['name']);

	
	$name3 = query_fetch_assoc("SELECT * FROM `serveri` WHERE `username` = '".$name."'");
    
	$dasdas = $name3['id'];

        if( !$dasdas ){
            exit("false");

        }else{
            //exit($name2[0]." ----- ima servera u bazi\n");
            exit("true");
	}
}
else{die;}

function get_size( $size )
{
	if ( $size < 0 - 1 )
	{
		return "Nepoznato";
	}
    if ( $size < 1024 )
	{
		return round( $size, 2 )." Byte";
	}
	if ( $size < 1024 * 1024 )
	{
		return round( $size / 1024, 2 )." Kb";
	}
	if ( $size < 1024 * 1024 * 1024 )
	{
		return round( $size / 1024 / 1024, 2 )." Mb";
	}
	if ( $size < 1024 * 1024 * 1024 * 1024 )
	{
		return round( $size / 1024 / 1024 / 1024, 2 )." Gb";
	}
	if ( $size < 1024 * 1024 * 1024 * 1024 * 1024 )
	{
		return round( $size / 1024 / 1024 / 1024 / 1024, 2 )." Tb";
	}
}


?>