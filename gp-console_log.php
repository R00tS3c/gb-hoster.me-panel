<?php
include('fnc/ostalo.php');

if (is_login() == false) {
    $_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
    $serverid = ispravi_text($_GET['id']);
    $server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$serverid' AND `user_id` = '$_SESSION[userid]'"));
    
    if (!$server) {
        $_SESSION['error'] = "Taj server ne postoji ili nemas ovlascenje za isti.";
        header("Location: /gp-home.php");
        die();
    }

    $info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$server[box_id]'"));
}

?>
<pre id="scroll" style="width: 89%;
    height: 465px;
    background: none;
    color: #fff;
    overflow-y: scroll;
    color: #ba0000;
    border: 1px solid #fff;
    margin: 1px 1px 1px 32px;
    padding: 20px;overflow-x: hidden;">
<?php
if(!($con = ssh2_connect($info['ip'], $info['sshport']))) return "Ne mogu se spojiti na server";
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
        if($server['igra'] == "2") {
			$filename = "ftp://$server[username]:$server[password]@$info[ip]:21/server_log.txt";
			$text .= file_get_contents($filename);
			echo $text;
        } 
	else

	 {
			$text .= htmlspecialchars($result_info);
			echo $text;
        }		
exit($text);


?>
</pre>