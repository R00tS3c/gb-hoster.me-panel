<?php  

include('connect_db.php');

// User avatar(slika)

function user_avatar($userid) {
	$avatar = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$userid'"));
	return $avatar['avatar'];
}

// Ime i prezime

function ime_prezime($userid) {
	$i_p = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$userid'"));
	return $i_p['ime'].' '.$i_p['prezime'];
}

function cscfg($find, $server_id) {
	$server_info = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id'"));
	$box_ip = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$server_info[box_id]'"));
	
	$fajl = "ftp://$server_info[username]:$server_info[password]@$box_ip[ip]:21/cstrike/server.cfg";
				
	$contents = file_get_contents($fajl);
	
	$pattern = preg_quote($find, '/');

	$pattern = "/^.*$pattern.*\$/m";

	if(preg_match_all($pattern, $contents, $matches)){
	   $text = implode("\n", $matches[0]);
	   $g = explode('"', $text);
	   return $g[1];
	}
}

function mcprop($find, $server_id) {
	$server_info = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id'"));
	$box_ip = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$server_info[box_id]'"));
	
	$fajl = "ftp://$server_info[username]:$server_info[password]@$box_ip[ip]:21/server.properties";
	
        $contents = file_get_contents($fajl);
	
	$pattern = preg_quote($find, '/');

	$pattern = "/^.*$pattern.*\$/m";
			
	$contents = file_get_contents($fajl);
	
	if(preg_match_all($pattern, $contents, $matches)){
	   $text = implode("\n", $matches[0]);
	   $g = explode('=', $text);
	   return $g[1];
	}
}


/* 

POSTAVI SVIMA DEFAULT AVATAR

$kevia_ = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '2'"));
if ($kevia_ == true) {
    $av = mysql_query("UPDATE `klijenti` SET `avatar` = 'kevia.png'");
    if ($av = true) {
        $_SESSION['ok'] = "Avatar je uspesno izmenjen svima na default!";
    }
}

*/

?>