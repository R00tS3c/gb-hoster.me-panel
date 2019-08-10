<?php


if (file_exists("./admin/assets/libs/phpseclib/SSH2.php"))
{
	function ssh_provera($ip, $sshport, $login, $password)
	{
		require_once("./admin/assets/libs/phpseclib/SSH2.php");

		$ssh = new Net_SSH2($ip, $sshport);

		if (!$ssh->login($login, $password))
		{
			$socket = fsockopen($ip, $sshport, $errno, $errstr, 100);

			if ($socket == FALSE) {
				return $jezik['text287'];
			}

			return $jezik['text288'];
		}

		return $ssh;
	}
}

function server_izbrisi($id, $klijentid, $ip, $sshport, $login, $password)
{
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$id."'");
	
	if($klijentid != "admin")
	{
		if(query_numrows("SELECT * FROM `serveri` WHERE `user_id` = '".$klijentid."'") == 0) return $jezik['text289'];
	}
	//$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `boxid` = '".$server['box_id']."'");
	
	if (!function_exists("ssh2_connect")) return $jezik['text290'];

	if($server['startovan'] == "1")
	{
		return $jezik['text291'];
	}	
	
	if(!($con = ssh2_connect("$ip", "$sshport"))) return $jezik['text292'];
	else 
	{
		if(!ssh2_auth_password($con, "$login", "$password")) return $jezik['text293'];
		else 
		{	
			$stream = ssh2_shell($con, 'xterm');
			fwrite( $stream, "userdel ".$server['username']."\n");
			sleep(1);
			fwrite( $stream, "nice -n 19 rm -Rf /home/".$server['username']."\n");
			sleep(4);
			
			$data = "";
			
			while($line = fgets($stream)) 
			{
				$data .= $line;
			}
			
			query_basic("DELETE FROM `serveri` WHERE `id` = '".$id."'");
			query_basic("DELETE FROM `lgsl` WHERE `id` = '".$id."'");
			
			return "uspesno";
		}
	}	
}

function ssh_dodaj_server($ip, $port, $username, $password, $novi_user, $novi_user_pw, $mod)
{
	if (!function_exists("ssh2_connect")) return $jezik['text290'];

	if(!($con = ssh2_connect("$ip", "$port"))) return $jezik['text292'];
	else 
	{
		if(!ssh2_auth_password($con, "$username", "$password")) return $jezik['text293'];
		else 
		{
			$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$mod."'");
			
			$mod["zipname"] = str_replace("zip", "tar.gz", $mod["zipname"]);
			
			$cmd1 = "passwd $novi_user";
			$cmd2 = "$novi_user_pw";
			$cmd3 = "$novi_user_pw";        
					
			$stream = ssh2_shell($con, 'xterm');
			fwrite( $stream, "mkdir /home/$novi_user\n");
			sleep(2);
			fwrite( $stream, "useradd -s /bin/bash $novi_user\n");
			sleep(1);
			fwrite( $stream, "$cmd1\n");
			sleep(1);
			fwrite( $stream, "$cmd2\n");
			sleep(1);
			fwrite( $stream, "$cmd3\n");
			sleep(1);
			$cmd1_final = 'screen -mSL '.$novi_user.'_instalacija';	    
			fwrite( $stream, "$cmd1_final\n");
			sleep(1);
			
			$cmd_nice = 'nice -n 19 rm -Rf /home/'.$novi_user.'/*';
			$cmd_wget = 'cd /home/'.$novi_user.'/ && wget '.$mod["link"].'/'.$mod["zipname"].'';
			$cmd_chown = 'chown -Rf '.$novi_user.':'.$novi_user.' /home/'.$novi_user.'/'.$mod["zipname"].'';
			$cmd_unzip = 'tar xvfz '.$mod["zipname"].' && chown -Rf '.$novi_user.':'.$novi_user.' /home/'.$novi_user.'';
			$cmd_chmod = 'chmod -R 755 /home/'.$novi_user.'/*';
			$cmd_clean = 'rm -rf '.$mod["zipname"];
			$cmd_steamclient = 'cd /home/'.$novi_user.'/;mkdir .steam;cd /home/'.$novi_user.'/.steam/;mkdir sdk32;cd /home/'.$novi_user.'/.steam/sdk32/;wget www.gb-hoster.me/Tools/steamclient.so;rm -rf steamclient.so.1;rm -rf steamclient.so.2;rm -rf steamclient.so.3;rm -rf steamclient.so.4;chmod -R 777 *;chown -Rf '.$novi_user.':'.$novi_user.' /home/'.$novi_user.'/.steam/sdk32/steamclient.so;exit';
			
			fwrite( $stream, "$cmd_nice\n");
			fwrite( $stream, "$cmd_wget\n");
			sleep(5);
			fwrite( $stream, "$cmd_chown\n");
			fwrite( $stream, "$cmd_unzip\n");
			sleep(5);
			fwrite( $stream, "$cmd_chmod\n");
			fwrite( $stream, "$cmd_clean\n");
			fwrite( $stream, "$cmd_steamclient\n");
			sleep(2);
			
			$data = "";
			
			while($line = fgets($stream)) 
			{
				$data .= $line;
			}
			
			$vreme = time();
			query_basic("UPDATE `serveri` SET `reinstaliran` = '{$vreme}' WHERE `username` = '{$novi_user}'");
			
			return "uspesno";
		}
	}	
}

function start_server($ip, $port, $username, $password, $serverid, $klijentid, $restart)
{
	if($klijentid == "admin") $server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	else $server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."' AND `user_id` = '".$_SESSION['klijentid']."'");
	if (file_exists("./admin/assets/libs/phpseclib/SSH2.php"))
	{
		if(query_numrows("SELECT * FROM `serveri` WHERE `id` = '".$serverid."' AND `user_id` = '".$_SESSION['klijentid']."'") == 0)
		{
			return $jezik['text289'];
		}
	}
	if($restart == FALSE)
	{	
		if($server['startovan'] == "1")
		{
			return $jezik['text291'];
		}
	}
	if (file_exists("./admin/assets/libs/phpseclib/SSH2.php"))
	{
		if($server['status'] == "Suspendovan")
		{
			return $jezik['text294'];
		}
	}
	if (!function_exists("ssh2_connect")) return $jezik['text290'];

	if(!($con = ssh2_connect($ip, $port))) return $jezik['text292'];
	else 
	{
		if(!ssh2_auth_password($con, $username, $password)) return $jezik['text293'];
		else 
		{
			$komanda = $server["komanda"]; 
			$komanda = str_replace('{$ip}', $ip, $komanda);
			$komanda = str_replace('{$port}', $server['port'], $komanda);
			$komanda = str_replace('{$slots}', $server['slotovi'], $komanda);
			$komanda = str_replace('{$map}', $server['map'], $komanda);
			$komanda = str_replace('{$fps}', $server['fps'], $komanda);	

			$stream = ssh2_shell($con, 'vt102', null, 80, 24, SSH2_TERM_UNIT_CHARS);
			fwrite( $stream, "screen -mSL $username".PHP_EOL);
			sleep(1);
			fwrite( $stream, "$komanda".PHP_EOL);
			sleep(1);
			fwrite( $stream, "rm log.log".PHP_EOL);
			sleep(1);
			
			$data = "";
			
			while($line = fgets($stream)) 
			{
				$data .= $line;
			}
			query_basic("UPDATE `serveri` SET `startovan` = '1' WHERE `id` = '".$serverid."'");
			return 'startovan';
		}
	}	
}

function stop_server($ip, $port, $username, $password, $serverid, $klijentid, $restart)
{
	if($klijentid == "admin") 
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	else 
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."' AND `user_id` = '".$_SESSION['klijentid']."'");

	if (file_exists("./admin/assets/libs/phpseclib/SSH2.php"))
	{	
		if(query_numrows("SELECT * FROM `serveri` WHERE `id` = '".$serverid."' AND `user_id` = '".$_SESSION['klijentid']."'") == 0)
		{
			return $jezik['text289'];
		}
	}
	if($restart == FALSE)
	{
		if($server['startovan'] == "0")
		{
			return $jezik['text295'];
		}
	}
	if (file_exists("./admin/assets/libs/phpseclib/SSH2.php"))
	{	
		if($server['status'] == "Suspendovan")
		{
			return $jezik['text294'];
		}
	}
	if (!function_exists("ssh2_connect")) return $jezik['text290'];
    
	$serverport = $server['port'];
	
	if ($server['mod'] == "51")
		$cmd = "kill -9 `screen -list | grep \"$username\" | awk {'print $1'} | cut -d . -f1`; kill -9 $(netstat -ntlp |grep -P \"$ip:$serverport\" |awk '{print $7}' |cut -d / -f 1);";
	else
		$cmd = "kill -9 `screen -list | grep \"$username\" | awk {'print $1'} | cut -d . -f1`; kill -9 $(netstat -ntlp |grep -P \"$ip:$serverport\" |awk '{print $7}' |cut -d / -f 1);";
	

	$cmd = "kill -9 $(netstat -ntlp |grep -P \"$ip:$serverport\" |awk '{print $7}' |cut -d / -f 1); 
                kill -9 `screen -list | grep \"$username\" | awk {'print $1'} | cut -d . -f1`";

	if ( !( $ssh_return = ssh_exec( $ip, $port, $username, $password, $cmd ,true,true) ) )
	{
		return false;

	}
	query_basic("UPDATE `serveri` SET `startovan` = '0' WHERE `id` = '".$serverid."'");	
	return 'stopiran';
	
	if(!($con = ssh2_connect($ip, $port))) return $jezik['text292'];
	else 
	{
		if(!ssh2_auth_password($con, $username, $password)) return $jezik['text293'];
		else 
		{
			$stream = ssh2_shell($con, 'vt102', null, 80, 24, SSH2_TERM_UNIT_CHARS);
			//fwrite( $stream, 'kill -9 `screen -list | grep "'.$username.'" | awk {\'print $1\'} | cut -d . -f1`'.PHP_EOL);
			//fwrite( $stream, 'pkill -u "'.$username.'"'.PHP_EOL);
			
			$line_stop = "kill -9 `screen -list | grep \"$username\" | awk {'print $1'} | cut -d . -f1`";
			fwrite( $stream, $line_stop);
			
			
			sleep(1);
			fwrite( $stream, 'screen -wipe'.PHP_EOL);
			sleep(1);
			
			$data = "";
			
			while($line = fgets($stream)) 
			{
				$data .= $line;
			}
			query_basic("UPDATE `serveri` SET `startovan` = '0' WHERE `id` = '".$serverid."'");			
			return 'stopiran';
		}
	}	
}

function reinstall_server($ip, $port, $username, $password, $serverid, $klijentid)
{
	if($klijentid == "admin") $server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	else $server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."' AND `user_id` = '".$_SESSION['klijentid']."'");

	if (file_exists("./admin/assets/libs/phpseclib/SSH2.php"))
	{	
		if(query_numrows("SELECT * FROM `serveri` WHERE `id` = '".$serverid."' AND `user_id` = '".$klijentid."'") == 0)
		{
			return $jezik['text289'];
		}
	}
	
	if(($server['reinstaliran'] + 5 ) > time())
	{
		return $jezik['text296'];
	}
	
	if($server['startovan'] == "1")
	{
		return $jezik['text291'];
	}
	if (file_exists("./admin/assets/libs/phpseclib/SSH2.php"))
	{	
		if($server['status'] == "Suspendovan")
		{
			return $jezik['text294'];
		}
	}	
	if (!function_exists("ssh2_connect")) return $jezik['text290'];

	if(!($con = ssh2_connect($ip, $port))) return $jezik['text292'];
	else 
	{
		if(!ssh2_auth_password($con, $username, $password)) return $jezik['text293'];
		else 
		{
			$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$server['mod']."'");
			
			if(empty($server["username"])) return $jezik['text297'];
			if(empty($mod["link"])) return $jezik['text298'];
			if(empty($mod["zipname"])) return $jezik['text298'];
			
			$mod["zipname"] = str_replace("zip", "tar.gz", $mod["zipname"]);
			
			$stream = ssh2_shell($con, 'xterm');
    	    $cmd1 = 'screen -m -S '.$server["username"].'_reinstall';	    
    	    fwrite( $stream, "$cmd1\n");
    	    sleep(1);
    	    
			$cmd_nice = 'nice -n 19 rm -Rf /home/'.$server["username"].'/*';
			$cmd_wget = 'cd /home/'.$server["username"].'/ && wget '.$mod["link"].'/'.$mod["zipname"].'';
			$cmd_chown = 'chown -Rf '.$server["username"].':'.$server["username"].' /home/'.$server["username"].'/'.$mod["zipname"].'';
			$cmd_unzip = 'tar xvfz '.$mod["zipname"].' && chown -Rf '.$server["username"].':'.$server["username"].' /home/'.$server["username"].'';
			$cmd_clean = 'rm -rf '.$mod["zipname"];
			$cmd_steamclient = 'cd /home/'.$server["username"].'/;mkdir .steam;cd /home/'.$server["username"].'/.steam/;mkdir sdk32;cd /home/'.$server["username"].'/.steam/sdk32/;wget www.gb-hoster.me/Tools/steamclient.so;rm -rf steamclient.so.1;rm -rf steamclient.so.2;rm -rf steamclient.so.3;rm -rf steamclient.so.4;chmod -R 777 *;chown -Rf '.$server["username"].':'.$server["username"].' /home/'.$server["username"].'/.steam/sdk32/steamclient.so;exit';
			
			fwrite( $stream, "$cmd_nice\n");
			fwrite( $stream, "$cmd_wget\n");
			sleep(5);
			fwrite( $stream, "$cmd_chown\n");
			fwrite( $stream, "$cmd_unzip\n");
			sleep(5);
			fwrite( $stream, "$cmd_clean\n");
			fwrite( $stream, "$cmd_steamclient\n");
			sleep(2);
			
			$data = "";
			
    	    while($line = fgets($stream)) {
    	    	    $data .= $line;
    	    }
			
			$vreme = time();
			query_basic("UPDATE `serveri` SET `reinstaliran` = '{$vreme}' WHERE `id` = '{$serverid}'");
			
			return 'reinstaliran';
		}
	}	
}

function server_mod($ip, $port, $username, $password, $serverid, $mod, $klijentid)
{
	if($klijentid == "admin") $server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	else $server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."' AND `user_id` = '".$_SESSION['klijentid']."'");
	
	if (file_exists("./admin/assets/libs/phpseclib/SSH2.php"))
	{	
		if(query_numrows("SELECT * FROM `serveri` WHERE `id` = '".$serverid."' AND `user_id` = '".$_SESSION['klijentid']."'") == 0)
		{
			return $jezik['text289'];
		}
	}
	
	if(($server['reinstaliran'] + 300 ) > time())
	{
		return $jezik['text296'];
	}	
	
	if($server['startovan'] == "1")
	{
		return $jezik['text291'];
	}
	if (file_exists("./admin/assets/libs/phpseclib/SSH2.php"))
	{	
		if($server['status'] == "Suspendovan")
		{
			return $jezik['text294'];
		}
	}	
	if (!function_exists("ssh2_connect")) return $jezik['text290'];

	if(!($con = ssh2_connect($ip, $port))) return $jezik['text292'];
	else 
	{
		if(!ssh2_auth_password($con, $username, $password)) return $jezik['text293'];
		else 
		{
			$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$mod."' AND `sakriven` = '0'");

			if(empty($server["username"])) return $jezik['text297'];
			if(empty($mod["link"])) return $jezik['text298'];			
			if(empty($mod["zipname"])) return $jezik['text298'];
			
			$mod["zipname"] = str_replace("zip", "tar.gz", $mod["zipname"]);
			
			$stream = ssh2_shell($con, 'xterm');
    	    $cmd1 = 'screen -m -S '.$server["username"].'_mod';	    
    	    fwrite( $stream, "$cmd1\n");
    	    sleep(1);
    	    
			$cmd_nice = 'nice -n 19 rm -Rf /home/'.$server["username"].'/*';
			$cmd_wget = 'cd /home/'.$server["username"].'/ && wget '.$mod["link"].'/'.$mod["zipname"].'';
			$cmd_chown = 'chown -Rf '.$server["username"].':'.$server["username"].' /home/'.$server["username"].'/'.$mod["zipname"].'';
			$cmd_unzip = 'tar xvfz '.$mod["zipname"].' && chown -Rf '.$server["username"].':'.$server["username"].' /home/'.$server["username"].'';
			$cmd_clean = 'rm -rf '.$mod["zipname"];
			$cmd_steamclient = 'cd /home/'.$server["username"].'/;mkdir .steam;cd /home/'.$server["username"].'/.steam/;mkdir sdk32;cd /home/'.$server["username"].'/.steam/sdk32/;wget www.gb-hoster.me/Tools/steamclient.so;rm -rf steamclient.so.1;rm -rf steamclient.so.2;rm -rf steamclient.so.3;rm -rf steamclient.so.4;chmod -R 777 *;chown -Rf '.$server["username"].':'.$server["username"].' /home/'.$server["username"].'/.steam/sdk32/steamclient.so;exit';
			
			fwrite( $stream, "$cmd_nice\n");
			fwrite( $stream, "$cmd_wget\n");
			sleep(5);
			fwrite( $stream, "$cmd_chown\n");
			fwrite( $stream, "$cmd_unzip\n");
			sleep(5);
			fwrite( $stream, "$cmd_clean\n");
			fwrite( $stream, "$cmd_steamclient\n");
			sleep(2);
			
			$data = "";
			
    	    while($line = fgets($stream)) {
    	    	    $data .= $line;
    	    }
			
			$vreme = time();
			query_basic("UPDATE `serveri` SET `reinstaliran` = '{$vreme}' AND `map` = '{$mod[mapa]}' WHERE `id` = '{$serverid}'");			
			
			return 'instaliran';
		}
	}	
}

?>
