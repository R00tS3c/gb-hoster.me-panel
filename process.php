<?php
include('fnc/ostalo.php');
include('server_process.php');
require('./inc/libs/phpseclib/Crypt/AES.php');

/* EMAIL PODRSKA */

if (isset($_GET['task']) && $_GET['task'] == "send_email") {
	$user_ip	= $_SERVER['REMOTE_ADDR']; 
	$d_v = date('d.m.Y, H:i:s');
		
	if ($_SESSION == "") {
		$_SESSION['error'] = "Morate se ulogovati.";
		header("Location: /home");
		die();
	} else {
		$_SESSION['info'] = "Ova opcija je u izradi, molimo da se strpite.";
		header("Location: /home");
		die();
	}

}

/* Bilten Email */

if (isset($_GET['task']) && $_GET['task'] == "bilten_email") {
	$user_ip	= $_SERVER['REMOTE_ADDR']; 
	$d_v = time();
	$token 		= htmlspecialchars(mysql_real_escape_string(addslashes($_SESSION['token_email'])));
	$token_post = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['token_email'])));
	$email 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['email'])));

	$pp_email = mysql_query("SELECT * FROM `email_send` WHERE `email` = '$email'");

	if (mysql_num_rows($pp_email)==1) {
		$_SESSION['info'] = "Ovaj email vec postoji u nasoj bazi.";
		header("Location: /home");
		die();
	}

	if ($email == "") {
		$_SESSION['info'] = "Morate uneti email.";
		header("Location: /home");
		die();
	}

	if ($token_post === $token) {
		$kveri = mysql_query("INSERT INTO `email_send` (`id`, `email`, `ip`, `vreme`) VALUES (NULL, '$email', '$user_ip', '$d_v')");

		if (!$kveri) {
			$_SESSION['error'] = "Greska. - BILTEN EMAIL";
			header("Location: /home");
			die();
		} else {
			$_SESSION['ok'] = "Uspesno ste dodali vasu email adresu.";
			header("Location: /home");
			die();
		}
	} else {
		$_SESSION['error'] = "Token nije tacan.";
		header("Location: /home");
		die();
	}

}

/* EDIT BILLTEN EMAIL */

if (isset($_GET['task']) && $_GET['task'] == "edit_bilten") {
	$user_ip	= $_SERVER['REMOTE_ADDR'];
	$d_v 		= date('d.m.Y, H:i:s'); 
	$token 		= htmlspecialchars(mysql_real_escape_string(addslashes($_SESSION['token'])));
	$token_post = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['token'])));
	$email 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['email'])));

	if ($token == ""||$email == "") {
		$_SESSION['info'] = "Proverite dali ste tacno uneli email ili vam se token ne poklapa.";
		header("Location: /home");
		die();
	}

	if ($token_post === $token) {
		$delete_upit = mysql_query("DELETE FROM `email_send` WHERE `email` = '$email'");
		if (!$delete_upit) {
			$_SESSION['error'] = "Email koji ste uneli ne postoji u nasu bazu!";
			header("Location: /home");
			die();
		} else {
			$_SESSION['ok'] = "Uspesno ste obrisali Email sa naseg biltena.";
			header("Location: /home");
			die();
		}
	} else {
		$_SESSION['error'] = "Token nije tacan.";
		header("Location: /home");
		die();
	}
}

/* Klijent LOGIN */

if (isset($_GET['task']) && $_GET['task'] == "login") {

	$username = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['username'])));
	$password = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['pass'])));

	if ($username == ""||$password == "") {
		$_SESSION['error'] = "Morate popuniti sva polja.";
		header("Location: /home");
        die();
	}
	if ($username == "demo@gb-hoster.me") {
		$_SESSION['error'] = 'Da pogledate demo nalog, kliknite ispod button "DEMO"';
		header("Location: /home");
        die();
	}
	$salt = hash('sha512', $username);
	$password = hash('sha512', $salt.$password);
	
	//$password = md5($password);

	$kveri = mysql_query("SELECT * FROM `klijenti` WHERE `username` = '$username' AND `sifra` = '$password'");
	if (mysql_num_rows($kveri)) {

		$user = mysql_fetch_array($kveri);

		$_SESSION['userid'] = $user['klijentid'];
		$_SESSION['username'] = $user['username'];
		$_SESSION['ime'] = $user['ime'];
		$_SESSION['prezime'] = $user['prezime'];
		$mesec = 24*60*60*31; // mesec dana
		
		$time = time();
		
		$sesija = md5($user['username'].$user['prezime'].$user['ime'].$mesec.$time."Kevia <3");

		setcookie("userid", $_SESSION['userid'], time()+ $mesec);
		setcookie("username", $_SESSION['username'], time()+ $mesec);
		setcookie("i_p", $_SESSION['ime'] .' '.$_SESSION['prezime'], time()+ $mesec);
		setcookie("sesija", $sesija, time() + $mesec);

		$log_msg = "Uspesan login.";
		$v_d = time();

		if ($username == "cik3r@protonmail.com") {
			$ip = "Ovaj IP je zasticen!";
		} else {
			$ip = get_client_ip();
		}

		mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
        
        $_SESSION['ok'] = "Uspesno ste se ulogovali!";
		header("Location: /home");
	} else {
		$_SESSION['error'] = "Podaci za prijavu nisu tacni.";
		header("Location: /home");
		die();
	}

}

/* Klijent Un-lock PIN CODE */

if (isset($_GET['task']) && $_GET['task'] == "un_lock_pin") {
	$user_ip = $_SERVER['REMOTE_ADDR']; 
	
	$pin_token = htmlspecialchars(mysql_real_escape_string(addslashes($_SESSION['pin_token'])));
	$pin = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['pin'])));

	if ($pin_token == ""||$pin == "") {
		$_SESSION['error'] = "Morate popuniti sva polja.";
		header("Location: $_SERVER[HTTP_REFERER]");
        die();
	}

	$kveri = mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$_SESSION[userid]' AND `sigkod` = '$pin'");
	if (mysql_num_rows($kveri)) {

		$user = mysql_fetch_array($kveri);

		$_SESSION['_pin'] = $user['sigkod'];
		$dan = 24; // jedan dan
		
		$time = time();
		setcookie("_pin", $_SESSION['_pin'], time()+ $dan);

		$log_msg = "Uspesno unesen vas PIN kod.";
		$v_d = time();
		$ip = get_client_ip();

		mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
        
        $_SESSION['ok'] = "Uspesno ste uneli pin kod.";
		header("Location: $_SERVER[HTTP_REFERER]");
	} else {
		$_SESSION['error'] = "Netacno unesen pin kod.";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}
}

/* START SERVER */

if (isset($_GET['task']) && $_GET['task'] == "start_server") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	if ($server_id == "") {
		$_SESSION['error'] = "Greska. - START";
		header("Location: gp-home.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "	aj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['startovan'] == "1") {
		$_SESSION['info'] = "Vas server je vec startovan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if ($pp_server['user_id'] == $_SESSION['userid']) {
		$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
		
		$server_ip 		= $info['ip'];
		$ssh_port 		= $info['sshport'];
		$server_port    = $pp_server['port'];
		$ftp_username 	= $pp_server['username'];
		$ftp_password 	= $pp_server['password'];
		$server_slot 	= $pp_server['slotovi'];
		$server_mapa 	= $pp_server['map'];
		$server_fps 	= $pp_server['fps'];
		$server_igraa 	= $pp_server['igra'];

		$server_kom 	= $pp_server['komanda'];
		$server_kom 	= str_replace('{$ip}', $server_ip, $server_kom);
		$server_kom 	= str_replace('{$port}', $server_port, $server_kom);
		$server_kom 	= str_replace('{$slots}', $server_slot, $server_kom);
		$server_kom 	= str_replace('{$map}', $server_mapa, $server_kom);
		$server_kom 	= str_replace('{$fps}', $server_fps, $server_kom);

		if($pp_server['igra'] == "2") {
			$ftp = ftp_connect($server_ip, $info['ftpport']);
			if (!$ftp) {
				$_SESSION['error'] = "No login ftp.";
				die();
			}
			if (ftp_login($ftp, $ftp_username, $ftp_password)){
				ftp_pasv($ftp, true);
				if (!empty($path)) {
					ftp_chdir($ftp, $path);
				} else ftp_chdir($ftp, './');
				    $folder = '_cache/panel_'.$ftp_username.'_samp_server.cfg';
				    $fajl = "ftp://$ftp_username:$ftp_password@$server_ip:$info[ftpport]/server.cfg";
				    $lines = file($fajl, FILE_IGNORE_NEW_LINES);
				
				    $bind = false;
				    $port = false;
				    $maxplayers = false;
				
    				foreach ($lines as &$line) {
    					
    					$val = explode(" ", $line);
    					
    					if ($val[0] == "port") {
    						$val[1] = $server_port;
    						$line = implode(" ", $val);
    						$port = true;
    					}
    					else if ($val[0] == "maxplayers") {
    						$val[1] = $server_slot;
    						$line = implode(" ", $val);
    						$maxplayers = true;
    					}
    					else if ($val[0] == "bind") {
    						$val[1] = $server_ip;
    						$line = implode(" ", $val);
    						$bind = true;
    					}
    				}
				    unset($line);
				
				if (!$fw = fopen(''.$folder.'', 'w+')) {
					$_SESSION['error'] = "Putanja se ne poklapa - Grska. SAMP";
					die();
				}
				foreach($lines as $line) {
					$fb = fwrite($fw,$line.PHP_EOL);
				}
				
				if (!$port) {
					fwrite($fw,"port $server_port".PHP_EOL);
				}
				if (!$maxplayers) {
					fwrite($fw,"maxplayers $server_slot".PHP_EOL);
				}
				if (!$bind) {
					fwrite($fw,"bind $server_ip".PHP_EOL);
				}
				
				$remote_file = ''.$path.'/server.cfg';
				if (!ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) {
					$_SESSION['error'] = "Putanja se ne poklapa - SAMP.";
					die();
				}
				fclose($fw);
				unlink($folder);
			}
			ftp_close($ftp);
		}
if($pp_server['igra'] == "3") {
			$ftp = ftp_connect($server_ip, $info['ftpport']);
			if (!$ftp) {
				$_SESSION['error'] = "No login ftp.";
				die();
			}
			if (ftp_login($ftp, $ftp_username, $ftp_password)){
				ftp_pasv($ftp, true);
				if (!empty($path)) {
					ftp_chdir($ftp, $path);
				} else ftp_chdir($ftp, './');
				    $folder = '_cache/panel_'.$ftp_username.'_mc_server.cfg';
				    $fajl = "ftp://$ftp_username:$ftp_password@$server_ip:$info[ftpport]/server.properties";
				    $lines = file($fajl, FILE_IGNORE_NEW_LINES);
				
				    $port = false;
				    $maxplayers = false;
				    $lsd = false;
				    $mdma = false;
				    $ds = false;
    				foreach ($lines as &$line) {
    					
    					$val = explode("=", $line);
    					
     					if ($val[0] == "server-port") {
    						$val[1] = $server_port;
    						$line = implode("=", $val);
    						$port = true;
		    			}
    					else if ($val[0] == "rcon.password") {
    						$val[1] = $ftp_password;
    						$line = implode("=", $val);
    						$lsd = true;
    					}
    					else if ($val[0] == "rcon.port") {
						$rconport = $server_slot+$server_port;
    						$val[1] = $rconport;
    						$line = implode("=", $val);
    						$mdma = true;
    					}
    					else if ($val[0] == "enable-rcon") {
    						$val[1] = "true";
    						$line = implode("=", $val);
    						$ds = true;
    					}

    				}
				    unset($line);
				
				if (!$fw = fopen(''.$folder.'', 'w+')) {
					$_SESSION['error'] = "Putanja se ne poklapa - Grska. MC";
					die();
				}
				foreach($lines as $line) {
					$fb = fwrite($fw,$line.PHP_EOL);
				}
				
				if (!$port) {
					fwrite($fw,"server-port=$server_port".PHP_EOL);
				}
				if (!$lsd) {
					fwrite($fw,"rcon.password=$ftp_password".PHP_EOL);
				}
				if (!$mdma) {
					$rconport = $server_slot+$server_port;
					fwrite($fw,"rcon.port=$rconport".PHP_EOL);
				}
				if (!$ds) {
					fwrite($fw,"enable-rcon=true".PHP_EOL);
				}

				$remote_file = ''.$path.'/server.properties';
				if (!ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) {
					$_SESSION['error'] = "Putanja se ne poklapa - MC.";
					die();
				}
				fclose($fw);
				unlink($folder);
			}
			ftp_close($ftp);
		}
if($pp_server['igra'] == "9") {
			$ftp = ftp_connect($server_ip, $info['ftpport']);
			if (!$ftp) {
				$_SESSION['error'] = "No login ftp.";
				die();
			}
			if (ftp_login($ftp, $ftp_username, $ftp_password)){
				ftp_pasv($ftp, true);
				if (!empty($path)) {
					ftp_chdir($ftp, $path);
				} else ftp_chdir($ftp, './');
				    $folder = '_cache/panel_'.$ftp_username.'_fivem_server.cfg';
				    $fajl = "ftp://$ftp_username:$ftp_password@$server_ip:$info[ftpport]/server-data/server.cfg";
				    $lines = file($fajl, FILE_IGNORE_NEW_LINES);
				
				    $port = false;
				    $maxplayers = false;
				    $lsd = false;
				    $mdma = false;
				    $ds = false;
				    
    				foreach ($lines as &$line) {
    					
    					$val = explode(" ", $line);
    					
     					if ($val[0] == "sv_maxclients") {
    						$val[1] = $server_slot;
    						$line = implode(" ", $val);
    						$maxplayers = true;
		    			}
    					else if ($val[0] == "rcon_password") {
    						$val[1] = $ftp_password;
    						$line = implode(" ", $val);
    						$lsd = true;
    					}  else if ($val[0] == "endpoint_add_tcp") {
    						$val[1] = '"0.0.0.0:'.$server_port.'"';
    						$line = implode(" ", $val);
    						$mdma = true;
    					}  else if ($val[0] == "endpoint_add_udp") {
    						$val[1] = '"0.0.0.0:'.$server_port.'"';
    						$line = implode(" ", $val);
    						$ds = true;
    					}

    				}
				    unset($line);
				
				if (!$fw = fopen(''.$folder.'', 'w+')) {
					$_SESSION['error'] = "Putanja se ne poklapa - Grska. FIVEM";
					die();
				}
				foreach($lines as $line) {
					$fb = fwrite($fw,$line.PHP_EOL);
				}
				
				if (!$maxplayers) {
					fwrite($fw,"sv_maxclients $server_slot".PHP_EOL);
				}
				if (!$lsd) {
					fwrite($fw,"rcon_password $ftp_password".PHP_EOL);
				}
				if (!$mdma) {
					fwrite($fw,'endpoint_add_tcp "0.0.0.0:'.$server_port.'"'.PHP_EOL);
				}	
				if (!$ds) {
					fwrite($fw,'endpoint_add_udp "0.0.0.0:'.$server_port.'"'.PHP_EOL);
				}
				$remote_file = ''.$path.'/server-data/server.cfg';
				if (!ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) {
					$_SESSION['error'] = "Putanja se ne poklapa - FIVEM.";
					die();
				}
				fclose($fw);
				unlink($folder);
			}
			ftp_close($ftp);
		}
		//Komanda za izvrsavanje
		$start_server = start_server($server_ip, $ssh_port, $ftp_username, $ftp_password, $server_kom, $server_igraa);

		if ($start_server == "OK") {
			mysql_query("UPDATE `serveri` SET `startovan` = '1' WHERE `id` = '$server_id'");

			$log_msg = "Uspesan start servera.";
			$v_d = time();
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

			$_SESSION['ok'] = "Uspesno ste startovali vas server.";
			header("Location: gp-info.php?id=".$server_id);
			die();
		} else {
			$_SESSION['error'] = "SSH - Greska START";
			header("Location: gp-info.php?id=".$server_id);
			die();
		}

	} else {
		$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
		header("Location: gp-home.php");
		die();
	}
}

/* RESTART SERVER */

if (isset($_GET['task']) && $_GET['task'] == "restart_server") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	if ($server_id == "") {
		$_SESSION['error'] = "Greska. - RESTART";
		header("Location: gp-home.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if ($pp_server['user_id'] == $_SESSION['userid']) {
		$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
		
		$server_ip 		= $info['ip'];
		$ssh_port 		= $info['sshport'];
		$server_port    = $pp_server['port'];
		$ftp_username 	= $pp_server['username'];
		$ftp_password 	= $pp_server['password'];
		$server_slot 	= $pp_server['slotovi'];
		$server_mapa 	= $pp_server['map'];
		$server_fps 	= $pp_server['fps'];
		$server_igraa 	= $pp_server['igra'];

		$server_kom 	= $pp_server['komanda'];
		$server_kom 	= str_replace('{$ip}', $server_ip, $server_kom);
		$server_kom 	= str_replace('{$port}', $server_port, $server_kom);
		$server_kom 	= str_replace('{$slots}', $server_slot, $server_kom);
		$server_kom 	= str_replace('{$map}', $server_mapa, $server_kom);
		$server_kom 	= str_replace('{$fps}', $server_fps, $server_kom);
		
		if($pp_server['igra'] == "2") {
			$ftp = ftp_connect($server_ip, $info['ftpport']);
			if (!$ftp) {
				$_SESSION['error'] = "No login ftp.";
				die();
			}
			if (ftp_login($ftp, $ftp_username, $ftp_password)) {
				ftp_pasv($ftp, true);
				if (!empty($path)) {
					ftp_chdir($ftp, $path);
				} else ftp_chdir($ftp, './');
				    $folder = '_cache/panel_'.$ftp_username.'_samp_server.cfg';
				    $fajl = "ftp://$ftp_username:$ftp_password@$server_ip:$info[ftpport]/server.cfg";
				    $lines = file($fajl, FILE_IGNORE_NEW_LINES);
				
				    $bind = false;
				    $port = false;
				    $maxplayers = false;
				
    				foreach ($lines as &$line) {
    					
    					$val = explode(" ", $line);
    					
    					if ($val[0] == "port") {
    						$val[1] = $server_port;
    						$line = implode(" ", $val);
    						$port = true;
    					}
    					else if ($val[0] == "maxplayers") {
    						$val[1] = $server_slot;
    						$line = implode(" ", $val);
    						$maxplayers = true;
    					}
    					else if ($val[0] == "bind") {
    						$val[1] = $server_ip;
    						$line = implode(" ", $val);
    						$bind = true;
    					}
    				}
				    unset($line);
				
				if (!$fw = fopen(''.$folder.'', 'w+')) {
					$_SESSION['error'] = "Putanja se ne poklapa - Grska. SAMP";
					die();
				}
				foreach($lines as $line) {
					$fb = fwrite($fw,$line.PHP_EOL);
				}
				
				if (!$port) {
					fwrite($fw,"port $server_port".PHP_EOL);
				}
				if (!$maxplayers) {
					fwrite($fw,"maxplayers $server_slot".PHP_EOL);
				}
				if (!$bind) {
					fwrite($fw,"bind $server_ip".PHP_EOL);
				}
				
				$remote_file = ''.$path.'/server.cfg';
				if (!ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) {
					$_SESSION['error'] = "Putanja se ne poklapa - SAMP.";
					die();
				}
				fclose($fw);
				unlink($folder);
			}
			ftp_close($ftp);
		}

		//echo $server_igraa;
if($pp_server['igra'] == "3") {
			$ftp = ftp_connect($server_ip, $info['ftpport']);
			if (!$ftp) {
				$_SESSION['error'] = "No login ftp.";
				die();
			}
			if (ftp_login($ftp, $ftp_username, $ftp_password)){
				ftp_pasv($ftp, true);
				if (!empty($path)) {
					ftp_chdir($ftp, $path);
				} else ftp_chdir($ftp, './');
				    $folder = '_cache/panel_'.$ftp_username.'_mc_server.cfg';
				    $fajl = "ftp://$ftp_username:$ftp_password@$server_ip:$info[ftpport]/server.properties";
				    $lines = file($fajl, FILE_IGNORE_NEW_LINES);
				    $port = false;
				    $maxplayers = false;
				    $lsd = false;
				    $mdma = false;
				    $ds = false;
    				foreach ($lines as &$line) {
    					
    					$val = explode("=", $line);
    					
    					if ($val[0] == "server-port") {
    						$val[1] = $server_port;
    						$line = implode("=", $val);
    						$port = true;
		    			}
    					else if ($val[0] == "rcon.password") {
    						$val[1] = $ftp_password;
    						$line = implode("=", $val);
    						$lsd = true;
    					}
    					else if ($val[0] == "rcon.port") {
						$rconport = $server_slot+$server_port;
    						$val[1] = $rconport;
    						$line = implode("=", $val);
    						$mdma = true;
    					}
    					else if ($val[0] == "enable-rcon") {
    						$val[1] = "true";
    						$line = implode("=", $val);
    						$ds = true;
    					}

    				}
				    unset($line);
				
				if (!$fw = fopen(''.$folder.'', 'w+')) {
					$_SESSION['error'] = "Putanja se ne poklapa - Grska. MC";
					die();
				}
				foreach($lines as $line) {
					$fb = fwrite($fw,$line.PHP_EOL);
				}
				
				if (!$port) {
					fwrite($fw,"server-port=$server_port".PHP_EOL);
				}
				if (!$lsd) {
					fwrite($fw,"rcon.password=$ftp_password".PHP_EOL);
				}
				if (!$mdma) {
					$rconport = $server_slot+$server_port;
					fwrite($fw,"rcon.port=$rconport".PHP_EOL);
				}
				if (!$ds) {
					fwrite($fw,"enable-rcon=true".PHP_EOL);
				}
				
				$remote_file = ''.$path.'/server.properties';
				if (!ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) {
					$_SESSION['error'] = "Putanja se ne poklapa - MC.";
					die();
				}
				fclose($fw);
				unlink($folder);
			}
			ftp_close($ftp);
		}
		if($pp_server['igra'] == "9") {
			$ftp = ftp_connect($server_ip, $info['ftpport']);
			if (!$ftp) {
				$_SESSION['error'] = "No login ftp.";
				die();
			}
			if (ftp_login($ftp, $ftp_username, $ftp_password)){
				ftp_pasv($ftp, true);
				if (!empty($path)) {
					ftp_chdir($ftp, $path);
				} else ftp_chdir($ftp, './');
				    $folder = '_cache/panel_'.$ftp_username.'_fivem_server.cfg';
				    $fajl = "ftp://$ftp_username:$ftp_password@$server_ip:$info[ftpport]/server-data/server.cfg";
				    $lines = file($fajl, FILE_IGNORE_NEW_LINES);
				
				    $port = false;
				    $maxplayers = false;
				    $lsd = false;
				    $mdma = false;
				    $ds = false;
				    
    				foreach ($lines as &$line) {
    					
    					$val = explode(" ", $line);
    					
     					if ($val[0] == "sv_maxclients") {
    						$val[1] = $server_slot;
    						$line = implode(" ", $val);
    						$maxplayers = true;
		    			}
    					else if ($val[0] == "rcon_password") {
    						$val[1] = $ftp_password;
    						$line = implode(" ", $val);
    						$lsd = true;
    					}  else if ($val[0] == "endpoint_add_tcp") {
    						$val[1] = '"0.0.0.0:'.$server_port.'"';
    						$line = implode(" ", $val);
    						$mdma = true;
    					}  else if ($val[0] == "endpoint_add_udp") {
    						$val[1] = '"0.0.0.0:'.$server_port.'"';
    						$line = implode(" ", $val);
    						$ds = true;
    					}

    				}
				    unset($line);
				
				if (!$fw = fopen(''.$folder.'', 'w+')) {
					$_SESSION['error'] = "Putanja se ne poklapa - Grska. FIVEM";
					die();
				}
				foreach($lines as $line) {
					$fb = fwrite($fw,$line.PHP_EOL);
				}
				
				if (!$maxplayers) {
					fwrite($fw,"sv_maxclients $server_slot".PHP_EOL);
				}
				if (!$lsd) {
					fwrite($fw,"rcon_password $ftp_password".PHP_EOL);
				}
				if (!$mdma) {
					fwrite($fw,'endpoint_add_tcp "0.0.0.0:'.$server_port.'"'.PHP_EOL);
				}	
				if (!$ds) {
					fwrite($fw,'endpoint_add_udp "0.0.0.0:'.$server_port.'"'.PHP_EOL);
				}
				$remote_file = ''.$path.'/server-data/server.cfg';
				if (!ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) {
					$_SESSION['error'] = "Putanja se ne poklapa - FIVEM.";
					die();
				}
				fclose($fw);
				unlink($folder);
			}
			ftp_close($ftp);
		}
		//Komanda za izvrsavanje
		$stop_server = stop_server($server_ip, $ssh_port, $ftp_username, $ftp_password);
		$start_server = start_server($server_ip, $ssh_port, $ftp_username, $ftp_password, $server_kom, $server_igraa);

		if ($start_server == "OK") {
			mysql_query("UPDATE `serveri` SET `startovan` = '1' WHERE `id` = '$server_id'");

			$log_msg = "Uspesan restart servera.";
			$v_d = time();
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

			$_SESSION['ok'] = "Uspesno ste restartovali vas server.";
			header("Location: gp-info.php?id=".$server_id);
			die();
		} else {
			$_SESSION['error'] = "SSH - Greska RESTART";
			header("Location: gp-info.php?id=".$server_id);
			die();
		}

	} else {
		$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
		header("Location: gp-home.php");
		die();
	}
}

/* STOP SERVER */

if (isset($_GET['task']) && $_GET['task'] == "stop_server") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	if ($server_id == "") {
		$_SESSION['error'] = "Greska. - STOP";
		header("Location: gp-home.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['startovan'] == "0") {
		$_SESSION['info'] = "Vas server je vec stopiran.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if ($pp_server['user_id'] == $_SESSION['userid']) {
		$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
		
		$server_ip 		= $info['ip'];
		$ssh_port 		= $info['sshport'];
		$server_port    = $pp_server['port'];
		$ftp_username 	= $pp_server['username'];
		$ftp_password 	= $pp_server['password'];
		$server_slot 	= $pp_server['slotovi'];
		$server_mapa 	= $pp_server['map'];
		$server_fps 	= $pp_server['fps'];

		//Komanda za izvrsavanje
		$stop_server = stop_server($server_ip, $ssh_port, $ftp_username, $ftp_password);

		if ($stop_server == "OK") {
			mysql_query("UPDATE `serveri` SET `startovan` = '0' WHERE `id` = '$server_id'");

			$log_msg = "Uspesno stopiranje servera.";
			$v_d = time();
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

			$_SESSION['ok'] = "Uspesno ste startovali vas server.";
			header("Location: gp-info.php?id=".$server_id);
			die();
		} else {
			$_SESSION['error'] = "SSH - Greska STOP";
			header("Location: gp-info.php?id=".$server_id);
			die();
		}

	} else {
		$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
		header("Location: gp-home.php");
		die();
	}
}

/* REINSTALL SERVER */

if (isset($_GET['task']) && $_GET['task'] == "reinstall_server") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	if ($server_id == "") {
		$_SESSION['error'] = "Greska. - REINSTALL";
		header("Location: gp-home.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['startovan'] == "1") {
		$_SESSION['info'] = "Vas server mora biti stopiran.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if (is_pin() == true) {
		if ($pp_server['user_id'] == $_SESSION['userid']) {
			$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
				
			$aes = new Crypt_AES();
	        $aes->setKeyLength(256);
	        $aes->setKey(CRYPT_KEY);    

			$server_ip 		= $info['ip'];
			$ssh_port 		= $info['sshport'];
			$root_user		= $info['login'];
			$root_pw		= $aes->decrypt($info['password']);
			$server_port    = $pp_server['port'];
			$ftp_username 	= $pp_server['username'];
			$ftp_password 	= $pp_server['password'];
			$server_slot 	= $pp_server['slotovi'];
			$server_mapa 	= $pp_server['map'];
			$server_fps 	= $pp_server['fps'];
			$server_mod 	= $pp_server['mod'];

			$time 			= time();
			if ($pp_server['igra'] == 9) {
			    $server_mod="28";
			}
			$mod_info = mysql_fetch_array(mysql_query("SELECT * FROM `modovi` WHERE `id` = '$server_mod'"));

			$mod_putanja = $mod_info['putanja'];

			if ($pp_server['igra'] == "1") {
				if ($mod_putanja == "") {
					$mod_putanja = "/home/gamefiles/pub";
				}
			} else if ($pp_server['igra'] == "2") {
				if ($mod_putanja == "") {
					$mod_putanja = "/home/gamefiles/samp";
				}
			} else {
				$mod_putanja = "/home/gamefiles/mc";
			}

			//Komanda za izvrsavanje
			$reinstall_server = reinstall_server($server_ip, $ssh_port, $root_user, $ftp_password, $ftp_username, $mod_info['id']);
			//ROOT VERSION
			//$reinstall_server = reinstall_server($server_ip, $ssh_port, $root_user, $root_pw, $ftp_username, $mod_putanja);
			
			if ($reinstall_server == "OK") {
				mysql_query("UPDATE `serveri` SET `reinstaliran` = '$time' WHERE `id` = '$server_id'");

				$log_msg = "Uspesna reinstalacija servera.";
			$v_d = time();
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
 
				$_SESSION['ok'] = "Uspesno ste reinstalirali vas server, molimo sacekajte 5min da se server stabilizuje.";
				header("Location: gp-info.php?id=".$server_id);
				die();
			} else {
				$_SESSION['error'] = "SSH - Greska REINSTALL - ($reinstall_server)";
				header("Location: gp-info.php?id=".$server_id);
				die();
			}

		} else {
			$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
			header("Location: gp-home.php");
			die();
		}
	} else {
		$_SESSION['info'] = "PIN?";
		header("Location: gp-home.php");
		die();
	}
}

/* KILL SERVER */

if (isset($_GET['task']) && $_GET['task'] == "obrisi_sve") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	if ($server_id == "") {
		$_SESSION['error'] = "Greska. - KILL SERVER";
		header("Location: gp-home.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['startovan'] == "1") {
		$_SESSION['info'] = "Vas server mora biti stopiran.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if (is_pin() == true) {
		if ($pp_server['user_id'] == $_SESSION['userid']) {
			$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
			
			$aes = new Crypt_AES();
	        $aes->setKeyLength(256);
	        $aes->setKey(CRYPT_KEY);    

			$server_ip 		= $info['ip'];
			$ssh_port 		= $info['sshport'];
			$root_user		= $info['login'];
			$root_pw		= $aes->decrypt($info['password']);
			$server_port    = $pp_server['port'];
			$ftp_username 	= $pp_server['username'];
			$ftp_password 	= $pp_server['password'];
			$server_slot 	= $pp_server['slotovi'];
			$server_mapa 	= $pp_server['map'];
			$server_fps 	= $pp_server['fps'];
			$server_mod 	= $pp_server['modovi'];

			$time 			= time();

			//Komanda za izvrsavanje
			$obrisi_sve = obrisi_sve($server_ip, $ssh_port, $root_user, $root_pw, $ftp_username);

			if ($obrisi_sve == "OK") {
				mysql_query("UPDATE `serveri` SET `reinstaliran` = '$time' WHERE `id` = '$server_id'");

				$log_msg = "Uspesano brisanje svih fajlova.";
			$v_d = time();
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

				$_SESSION['info'] = "Uspesno ste obrisali sve sa servera. Ukoliko ocete da vratite vas server Reinstalirajte ga!";
				header("Location: gp-info.php?id=".$server_id);
				die();
			} else {
				$_SESSION['error'] = "SSH - Greska KILL SERVER";
				header("Location: gp-info.php?id=".$server_id);
				die();
			}

		} else {
			$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
			header("Location: gp-home.php");
			die();
		}
	} else {
		$_SESSION['info'] = "PIN?";
		header("Location: gp-home.php");
		die();
	}
}

/* CREATE FTP PASSWORD */

if (isset($_GET['task']) && $_GET['task'] == "new_ftp_pw") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$ftp_pw_kor 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['ftp_pw_kor'])));
	$ftp_pw_auto 	= htmlspecialchars(mysql_real_escape_string(addslashes($_SESSION['get_new_pw'])));
	if ($server_id == "") {
		$_SESSION['error'] = "Greska. - FTP PASSWORD";
		header("Location: gp-home.php");
		die();
	}
	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if (is_pin() == true) {
		if ($pp_server['user_id'] == $_SESSION['userid']) {
			$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
			
			$server_ip 		= $info['ip'];
			$ssh_port 		= $info['sshport'];
			$server_port    = $pp_server['port'];
			$ftp_username 	= $pp_server['username'];
			$ftp_password 	= $pp_server['password'];
			$server_slot 	= $pp_server['slotovi'];
			$server_mapa 	= $pp_server['map'];
			$server_fps 	= $pp_server['fps'];

			if ($ftp_pw_kor == "") {
				$ftp_pw_kor = $ftp_pw_auto;
			}

			//Komanda za izvrsavanje
			$pp_new_ftp_pw = new_ftp_pw($server_ip, $ssh_port, $ftp_username, $ftp_password, $ftp_pw_kor);
			if ($pp_new_ftp_pw == "OK") {
				mysql_query("UPDATE `serveri` SET `password` = '$ftp_pw_kor' WHERE `id` = '$server_id'");

				$log_msg = "Uspesno generisan novi FTP PW.";
			$v_d = time();
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

				$_SESSION['ok'] = "Uspesno ste promenili vas ftp password";
				header("Location: gp-info.php?id=".$server_id);
				die();
			} else {
				$_SESSION['error'] = "SSH Greska. - FTP PASSWORD";
				header("Location: gp-info.php?id=".$server_id);
				die();
			}
			session_destroy($ftp_pw_auto);
		} else {
			$_SESSION['error'] = "Nemas pristup.";
			header("Location: gp-home.php");
			die();
		}
	} else {
		$_SESSION['error'] = "PIN?";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}
}

/* AUTO RESTART EDIT */

if (isset($_GET['task']) && $_GET['task'] == "auto_rs_edit") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}
	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$autorestart 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['autorestart'])));

	if ($server_id == "") {
		$_SESSION['error'] = "Greska. - AUTO RESTART";
		header("Location: gp-home.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if (is_pin() == true) {
		if ($pp_server['user_id'] == $_SESSION['userid']) {
			
			$update = mysql_query("UPDATE `serveri` SET `autorestart` = '$autorestart' WHERE `id` = '$server_id'");

			if (!$update) {
				$_SESSION['error'] = "Greska. - AUTO RESTART";
				header("Location: gp-info.php?id=".$server_id);
				die();
			} else {
				$log_msg = "Uspesno menjanje Auto RS.";
				$v_d = time();
				$ip = get_client_ip();

				mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

				$_SESSION['ok'] = "Uspesno ste promenili vas auto restart. Vas server ce se restartovati svakog dana u $autorestart:00h.";
				header("Location: gp-info.php?id=".$server_id);
				die();
			}

		} else {
			$_SESSION['error'] = "Nemas pristup.";
			header("Location: gp-home.php");
			die();
		}
	} else {
		$_SESSION['error'] = "PIN?";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}
}

/* Promeni ime servera u GP */

if (isset($_GET['task']) && $_GET['task'] == "edit_name_p") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}
	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$ime_servera 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['ime_servera'])));

	if ($server_id == "") {
		$_SESSION['error'] = "Greska. - IME SERVERA (GP)";
		header("Location: gp-home.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if (is_pin() == true) {
		if ($pp_server['user_id'] == $_SESSION['userid']) {
			
			$update = mysql_query("UPDATE `serveri` SET `name` = '$ime_servera' WHERE `id` = '$server_id'");

			if (!$update) {
				$_SESSION['error'] = "Greska. - IME SERVERA (GP)";
				header("Location: gp-info.php?id=".$server_id);
				die();
			} else {
				$log_msg = "Uspesno ste promenili ime servera u GP.";
				$v_d = time();
				$ip = get_client_ip();

				mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

				$_SESSION['ok'] = "Uspesno ste promenili ime servera u gamepanelu. Ova promena nece vazit i za server!";
				header("Location: gp-info.php?id=".$server_id);
				die();
			}

		} else {
			$_SESSION['error'] = "Nemas pristup.";
			header("Location: gp-home.php");
			die();
		}
	} else {
		$_SESSION['error'] = "PIN?";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}
}

/* INSTALL PLUGIN */

if (isset($_GET['task']) && $_GET['task'] == "install_plugin") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}
	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$plugin_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['plugin_id'])));

	if ($server_id == "") {
		$_SESSION['error'] = "Greska. - INSTALL PLUGIN (GP)";
		header("Location: gp-home.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if ($pp_server['user_id'] == $_SESSION['userid']) {

		$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
		$plugin = mysql_fetch_array(mysql_query("SELECT * FROM `plugins` WHERE `id` = '{$plugin_id}'"));
			
		$server_ip 		= $info['ip'];
		$ssh_port 		= $info['sshport'];
		$server_port    = $pp_server['port'];
		$ftp_username 	= $pp_server['username'];
		$ftp_password 	= $pp_server['password'];
		$server_slot 	= $pp_server['slotovi'];
		$server_mapa 	= $pp_server['map'];
		$server_fps 	= $pp_server['fps'];
		
		$ftp = ftp_connect($server_ip, 21);
        if(!$ftp) {
        	$_SESSION['error'] = "Ne mogu se konektovati na FTP servera!";
        	header("Location: gp-plugins.php?id=".$server_id);
        	die();
        }

        if (ftp_login($ftp, $ftp_username, $ftp_password)) {           
		    ftp_pasv($ftp, true);
            ftp_chdir($ftp, "/cstrike/addons/amxmodx/configs");
                
            $folder = '_cache/panel_'.$ftp_username.'_'.$plugin['prikaz'];

            $fw = fopen(''.$folder.'', 'w+');
            if(!$fw){
                $_SESSION['error'] = "Ne mogu otvoriti fajl.";
        		header("Location: gp-plugins.php?id=".$server_id);
        		die();
            } else {
                $fb = fwrite($fw, stripslashes($plugin['text']));
                
                if(!$fb){
                   $_SESSION['error'] = "Ne mogu sacuvati plugin!";
		        	header("Location: gp-plugins.php?id=".$server_id);
		        	die();
                } else {               
                    $remote_file = '/cstrike/addons/amxmodx/configs/'.$plugin['prikaz'];
                    if (ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) {
                        $_SESSION['ok'] = "Plugin je uspesno instaliran, kada restartujete vas server.";                       
                        header("Location: gp-plugins.php?id=".$server_id);
                        die();
                    } else {
                        $greska = "Dogodila se greška prilikom spremanja plugina.";
                        header("Location: gp-plugins.php?id=".$server_id);
                        die();
                   	}
                    unlink($folder);                                
                }
            }
        }

	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: gp-home.php");
		die();
	}
}

/* DELETE PLUGIN */

if (isset($_GET['task']) && $_GET['task'] == "del_ins_plugin") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}
	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$plugin_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['plugin_id'])));

	if ($server_id == "") {
		$_SESSION['error'] = "Greska. - DELETE PLUGIN (GP)";
		header("Location: gp-home.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if ($pp_server['user_id'] == $_SESSION['userid']) {

		$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
		$plugin = mysql_fetch_array(mysql_query("SELECT * FROM `plugins` WHERE `id` = '{$plugin_id}'"));
			
		$server_ip 		= $info['ip'];
		$ssh_port 		= $info['sshport'];
		$server_port    = $pp_server['port'];
		$ftp_username 	= $pp_server['username'];
		$ftp_password 	= $pp_server['password'];
		$server_slot 	= $pp_server['slotovi'];
		$server_mapa 	= $pp_server['map'];
		$server_fps 	= $pp_server['fps'];
		
		$ftp = ftp_connect($server_ip, 21);
        if(!$ftp) {
        	$_SESSION['error'] = "Ne mogu se konektovati na FTP servera!";
        	header("Location: gp-plugins.php?id=".$server_id);
        	die();
        }

        if (ftp_login($ftp, $ftp_username, $ftp_password)) {           
		    ftp_pasv($ftp, true);
            ftp_chdir($ftp, "/cstrike/addons/amxmodx/configs");
                
            ftp_delete($ftp, $plugin['prikaz']);

            $_SESSION['ok'] = "Uspesno ste obrisali plugin.";
            header("Location: gp-plugins.php?id=".$server_id);
            die();
        } else {
        	$_SESSION['error'] = "Ne mogu da obrisem plugin.";
        	header("Location: gp-plugins.php?id=".$server_id);
        	die();
        }

	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: gp-home.php");
		die();
	}
}

/* DODAJ ADMIN - GP */

if (isset($_GET['task']) && $_GET['task'] == "add_admins") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$admin_token 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['admin_token'])));
	$admin_token2 	= htmlspecialchars(mysql_real_escape_string(addslashes($_SESSION['admin_token'])));
	$vrsta 			= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['vrsta'])));
	$privilegije 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['privilegije'])));
	$nick 			= mysql_real_escape_string(addslashes($_POST['nick']));
	$sifra 			= mysql_real_escape_string(addslashes($_POST['sifra']));
	$komentar 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['komentar'])));
	$custom_flag 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['custom_flag'])));
	if ($server_id == ""||$admin_token == ""||$admin_token2 == "") {
		$_SESSION['error'] = "Greska. - ADD ADMIN GP";
		header("Location: gp-home.php");
		die();
	}
	if ($nick == "") {
		$_SESSION['info'] = "Polje 'Nick,Steam,IP' vam je prazno.";
		header("Location: gp-admins.php?id=".$server_id);
		die();
	}

	if ($admin_token == $admin_token2) {
		
		$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
		if (!$pp_server) {
			$_SESSION['info'] = "Ovaj server ne postoji.";
			header("Location: gp-home.php");
			die();
		}

		if ($pp_server['status'] == "Suspendovan") {
			$_SESSION['info'] = "Vas server nije Aktivan.";
			header("Location: gp-info.php?id=".$server_id);
			die();
		}

		if (is_demo() == false) {
			$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
			header("Location: $_SERVER[HTTP_REFERER]");
			die();
		}

		if ($pp_server['user_id'] == $_SESSION['userid']) {
			$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
			$ftp = ftp_connect($info['ip'], $info['ftpport']);
            if(!$ftp) {
                $_SESSION['error'] = "Ne mogu se konektovati na FTP servera!";
                header("Location: gp-admins.php?id=".$server_id);
                die();
            }
            
            if (ftp_login($ftp, $pp_server['username'], $pp_server['password'])) {         
                ftp_pasv($ftp, true);		
                ftp_chdir($ftp, "/cstrike/addons/amxmodx/configs");
                $fajl = "ftp://$pp_server[username]:$pp_server[password]@$info[ip]:$info[ftpport]/cstrike/addons/amxmodx/configs/users.ini";
                $contents = file_get_contents($fajl);

                if ($privilegije == "") {
                	$privilegije = $custom_flag;
                } else {
                	if($privilegije == "slot") 			{ $privilegije = "a"; }
	                if($privilegije == "slot_i")		{ $privilegije = "ab"; }
	                if($privilegije == "low_admin") 	{ $privilegije = "abcdei"; }
	                if($privilegije == "ful_admin") 	{ $privilegije = "abcdefijkmu"; }
	                if($privilegije == "head") 			{ $privilegije = "abcdefghijkmnopqrstu"; }
                }

if ($vrsta == "nick_admin") {

$contents .= '
"'.$nick.'" "'.$sifra.'" "'.$privilegije.'" "ab" //'.$komentar.'';

} elseif ($vrsta == "steam_admin") {

if ($sifra == "") {
$contents .= '
"'.$nick.'" "'.$sifra.'" "'.$privilegije.'" "ce" //'.$komentar.'';
} else {
$contents .= '
"'.$nick.'" "'.$sifra.'" "'.$privilegije.'" "ca" //'.$komentar.'';
}

} elseif ($vrsta == "ip_admin") {
	
$contents .= '
"'.$nick.'" "'.$sifra.'" "'.$privilegije.'" "ca" //'.$komentar.'';

}

                $folder = "_cache/panel_".$pp_server['username']."_users.ini";

                $fw = fopen(''.$folder.'', 'w+');
                if(!$fw){
                    $_SESSION['error'] = "Ne mogu otvoriti fajl";  
                    header("Location: gp-admins.php?id=".$server_id); 
                    die();
                } else {  
                    $fb = fwrite($fw, stripslashes($contents));
                    if(!$fb) {
                        $_SESSION['error'] = "Ne mogu dodati admina.";
                        header("Location: gp-admins.php?id=".$server_id);
                        die();
                    } else {               
                        $remote_file = 'users.ini';
                        if (ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) {
                        	$log_msg = "Uspesno dodat novi admin.";
							$v_d = time();
							$ip = get_client_ip();

							mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
                            $_SESSION['ok'] = "Uspesno ste dodali admina";
                            header("Location: gp-admins.php?id=".$server_id);
                            die();
                        } else {
                            $_SESSION['error'] = "Dogodila se greška prilikom dodavanja admina.";
                            header("Location: gp-admins.php?id=".$server_id);
                            die();
                        }
                        unlink($folder);                                
                    }
                }
            }
		} else {
			$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
			header("Location: gp-home.php");
			die();
		}

	} else {
		$_SESSION['error'] = "Token se ne poklapa.";
		header("Location: gp-admins.php?id=".$server_id);
		die();
	}
}

/* EDIT FTP FILE */

if (isset($_GET['task']) && $_GET['task'] == "edit_file") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$file_token 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['file_token'])));
	$file_token2 	= htmlspecialchars(mysql_real_escape_string(addslashes($_SESSION['file_token'])));
	$file 			= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['file'])));
	$lokacija 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['lokacija'])));
	$path 			= $lokacija;
	$file_text_edit = $_POST['file_text_edit'];
	if ($server_id == ""||$file_token == ""||$file_token2 == "") {
		$_SESSION['error'] = "Greska. - EDIT FILE GP";
		header("Location: gp-home.php");
		die();
	}

	if ($file_token == $file_token2) {
		
		$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
		if (!$pp_server) {
			$_SESSION['info'] = "Ovaj server ne postoji.";
			header("Location: gp-home.php");
			die();
		}

		if ($pp_server['status'] == "Suspendovan") {
			$_SESSION['info'] = "Vas server nije Aktivan.";
			header("Location: gp-info.php?id=".$server_id);
			die();
		}

		if (is_demo() == false) {
			$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
			header("Location: $_SERVER[HTTP_REFERER]");
			die();
		}

		if ($pp_server['user_id'] == $_SESSION['userid']) {
			$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
			
			$ftp = ftp_connect($info['ip'], 21);
			if(!$ftp) {
				$_SESSION['error'] = "Ne mogu se spojiti na ftp server";
				header("Location: gp-webftp.php?id=".$server_id);
				die();
			}
				
			if (ftp_login($ftp, $pp_server['username'], $pp_server['password'])) {
				ftp_pasv($ftp, true);
				if(!empty($path)) {
					ftp_chdir($ftp, $path);	
				}	

				$folder = '_cache/panel_'.$pp_server['username'].'_'.$file;

				$fw = fopen(''.$folder.'', 'w+');
				$fb = fwrite($fw, stripslashes($file_text_edit));
				$file = "$file";
				$remote_file = ''.$path.'/'.$file.'';
				if (ftp_put($ftp, $remote_file, $folder, FTP_BINARY)){
					$log_msg = "Uspesno editovan file: $file";
					$v_d = time();
					$ip = get_client_ip();

					mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
					$_SESSION['ok'] = "Uspesno ste spremili vase izmene.";
					header("Location: gp-webftp.php?id=".$server_id.'&path='.$path.'&fajl='.$file);
					die();
				} else {
					$_SESSION['error'] = "Greska. - Promene nisu spremnjene.";
					header("Location: gp-webftp.php?id=".$server_id.'&path='.$path.'&fajl='.$file);
					die();
				}
				
				fclose($fw);

				unlink($folder);			
			}
			ftp_close($ftp);

		} else {
			$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
			header("Location: gp-home.php");
			die();
		}

	} else {
		$_SESSION['error'] = "Token se ne poklapa.";
		header("Location: gp-admins.php?id=".$server_id);
		die();
	}
}

/* CREATE FOLDER - GP */

if (isset($_GET['task']) && $_GET['task'] == "create_folder") {
	
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$folder_token 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['folder_token'])));
	$folder_token2 	= htmlspecialchars(mysql_real_escape_string(addslashes($_SESSION['folder_token'])));
	$folder_name 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['folder_name'])));
	$lokacija 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['lokacija'])));
	$path 			= $lokacija;
	$file_text_edit = htmlspecialchars($_POST['file_text_edit']);
	if ($server_id == ""||$folder_token == ""||$folder_token2 == "") {
		$_SESSION['error'] = "Greska. - CREATE FOLDER GP-FTP";
		header("Location: gp-home.php");
		die();
	}

	if ($folder_token == $folder_token2) {
		
		$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
		if (!$pp_server) {
			$_SESSION['info'] = "Ovaj server ne postoji.";
			header("Location: gp-home.php");
			die();
		}

		if ($pp_server['status'] == "Suspendovan") {
			$_SESSION['info'] = "Vas server nije Aktivan.";
			header("Location: gp-info.php?id=".$server_id);
			die();
		}

		if (is_demo() == false) {
			$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
			header("Location: $_SERVER[HTTP_REFERER]");
			die();
		}

		if ($pp_server['user_id'] == $_SESSION['userid']) {
			$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
			
			$ftp = ftp_connect($info['ip'], 21);
			if(!$ftp) {
				$_SESSION['error'] = "Ne mogu se spojiti na ftp server";
				header("Location: gp-webftp.php?id=".$server_id);
				die();
			}
				
			if (ftp_login($ftp, $pp_server['username'], $pp_server['password'])) {
				ftp_pasv($ftp, true);
				if(!empty($path)) {
					ftp_chdir($ftp, $path);	
				}
				
				if(ftp_mkdir($ftp, $folder_name)) {
					$log_msg = "Uspesno kreiran novi folder: $folder_name.";
					$v_d = time();
					$ip = get_client_ip();

					mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
					$_SESSION['ok'] = "Uspesno ste kreirali folder.";
					header("Location: gp-webftp.php?id=".$server_id.'&path='.$path);
					exit;
				} else {
					$_SESSION['error'] = "Greska.";
					header("Location: gp-webftp.php?id=".$server_id.'&path='.$path);
					exit;
				}			
			}
			ftp_close($ftp);
		} else {
			$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
			header("Location: gp-home.php");
			die();
		}

	} else {
		$_SESSION['error'] = "Token se ne poklapa.";
		header("Location: gp-webftp.php?id=".$server_id);
		die();
	}

}

/* RCON COMAND */

if (isset($_GET['task']) && $_GET['task'] == "console_rcon_com") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$komanda 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['komanda'])));

	if ($server_id == ""||$komanda == "") {
		$_SESSION['error'] = "Greska. - CREATE FOLDER GP-FTP";
		header("Location: gp-home.php");
		die();
	}
	
	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if($pp_server['startovan'] == "0") { 
		$_SESSION['info'] = "Server mora biti startovan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if ($pp_server['user_id'] == $_SESSION['userid']) {
		$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
		
		if($pp_server['igra'] == "1") {
            $rconpw = cscfg('rcon_password', $pp_server['id']);
            include 'inc/libs/SourceQuery/rcon_hl_net.inc';
            $M = new Rcon();
            
            $M->Connect($info['ip'], $pp_server['port'], $rconpw);
            $M->RconCommand($komanda); 
            
            $log_msg = "Uspesno izvrsena RCON (<strong> {$komanda} </strong>)</a> komanda.";
			$v_d = time();
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
			$_SESSION['ok'] = "Uspesno izvrsena RCON (<strong> {$komanda} </strong>)</a> komanda.";
        
			header("Location: gp-console.php?id=".$pp_server['id']);
            die();
        }
		
	} else {
		$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
		header("Location: gp-home.php");
		die();
	}
}

if (isset($_GET['task']) && $_GET['task'] == "console_rcon_com_mc") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$komanda 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['komanda'])));

	if ($server_id == ""||$komanda == "") {
		$_SESSION['error'] = "Greska. - CREATE FOLDER GP-FTP";
		header("Location: gp-home.php");
		die();
	}
	
	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if($pp_server['startovan'] == "0") { 
		$_SESSION['info'] = "Server mora biti startovan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if ($pp_server['user_id'] == $_SESSION['userid']) {
		$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
		
		if($pp_server['igra'] == "3") {
            $rconpw = mcprop('rcon.password', $pp_server['id']);
	    $rconport = mcprop('rcon.port', $pp_server['id']);
	    $rconip = $info['ip'];
	    $timeout = 3;
            include 'inc/libs/SourceQuery/SourceQuery.class.php';
                    define( 'SQ_SERVER_ADDR', $rconip );
                    define( 'SQ_SERVER_PORT', $rconport );
                    define( 'SQ_TIMEOUT', 1 );
                    define( 'SQ_ENGINE', SourceQuery :: SOURCE );               
                    $Query = new SourceQuery( );
                    try
                    {
                        $Query->Connect( SQ_SERVER_ADDR, SQ_SERVER_PORT, SQ_TIMEOUT, SQ_ENGINE );
                        $Query->SetRconPassword( $rconpw );
                        $Query->Rcon( $komanda );
                    }    
                    catch( Exception $e )
                    {
                        $error = $e->getMessage( );
                    }

            
            		$log_msg = "Uspesno izvrsena RCON (<strong> {$komanda} </strong>)</a> komanda.";
			$v_d = time();
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
			$_SESSION['ok'] = "Uspesno izvrsena RCON (<strong> {$komanda} </strong>)</a> komanda.";
        
			header("Location: gp-console.php?id=".$pp_server['id']);
            die();
        }
		
	} else {
		$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
		header("Location: gp-home.php");
		die();
	}
}


/* WGET FILE - GP */

if (isset($_GET['task']) && $_GET['task'] == "wget_file") {
	
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$wget_token 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['wget_token'])));
	$wget_token2 	= htmlspecialchars(mysql_real_escape_string(addslashes($_SESSION['wget_token'])));
	$wget_link 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['wget_link'])));
	$lokacija 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['lokacija'])));
	$path 			= $lokacija;
	if ($server_id == ""||$wget_token == ""||$wget_token2 == "") {
		$_SESSION['error'] = "Greska. - CREATE FOLDER GP-FTP";
		header("Location: gp-home.php");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if (is_pin() == false) {
		$_SESSION['info'] = "PIN?";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if ($wget_token == $wget_token2) {
		$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
		if (!$pp_server) {
			$_SESSION['info'] = "Ovaj server ne postoji.";
			header("Location: gp-home.php");
			die();
		}

		if ($pp_server['status'] == "Suspendovan") {
			$_SESSION['info'] = "Vas server nije Aktivan.";
			header("Location: gp-info.php?id=".$server_id);
			die();
		}

		if ($pp_server['user_id'] == $_SESSION['userid']) {
			$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
				
			$server_ip 		= $info['ip'];
			$ssh_port 		= $info['sshport'];
			$server_port    = $pp_server['port'];
			$ftp_username 	= $pp_server['username'];
			$ftp_password 	= $pp_server['password'];
			$server_slot 	= $pp_server['slotovi'];
			$server_mapa 	= $pp_server['map'];
			$server_fps 	= $pp_server['fps'];

			if ($path == "/") {
				$path = "";
			}

			//Komanda za izvrsavanje
			$wget_ = wget_comands($server_ip, $ssh_port, $ftp_username, $ftp_password, $path, $wget_link);

			if ($wget_ == "OK") {
				$log_msg = "Uspesno izvrsena komanda WGET.";
				$v_d = time();
				$ip = get_client_ip();

				mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
				$_SESSION['ok'] = "Uspesno izvrsena komanda WGET.";
				header("Location: gp-webftp.php?id=".$server_id.'&path='.$path);
				die();
			} else {
				$_SESSION['error'] = "Greska. - WGET WEBFTP GP";
				header("Location: gp-webftp.php?id=".$server_id.'&path='.$path);
				die();
			}

		} else {
			$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
			header("Location: gp-home.php");
			die();
		}
	} else {
		$_SESSION['error'] = "Token se ne poklapa.";
		header("Location: gp-webftp.php?id=".$server_id);
		die();
	}

}

/* UN(RAR,TAR,ZIP) FILE - GP */

if (isset($_GET['task']) && $_GET['task'] == "un_file") {
	
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$file_name 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['file_name'])));
	$wget_token2 	= htmlspecialchars(mysql_real_escape_string(addslashes($_SESSION['wget_token'])));
	$file_ext 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['file_ext'])));
	$lokacija 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['file_location'])));
	$path 			= $lokacija;
	if ($server_id == ""||$file_name == ""||$file_ext == "") {
		$_SESSION['error'] = "Greska. - CREATE FOLDER GP-FTP";
		header("Location: gp-home.php");
		die();
	}

	$file_ext_p = array('rar','tar','zip');
	if (in_array($file_ext_p, $file_ext) === false) {
		$_SESSION['info'] = "Ova opicja je dozvoljena samo za sledece extenzije: RAR, TAR i ZIP!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if (is_pin() == false) {
		$_SESSION['info'] = "PIN?";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if ($pp_server['user_id'] == $_SESSION['userid']) {
		$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
			
		$server_ip 		= $info['ip'];
		$ssh_port 		= $info['sshport'];
		$server_port    = $pp_server['port'];
		$ftp_username 	= $pp_server['username'];
		$ftp_password 	= $pp_server['password'];
		$server_slot 	= $pp_server['slotovi'];
		$server_mapa 	= $pp_server['map'];
		$server_fps 	= $pp_server['fps'];

		if ($file_ext == "rar") {
			$file_ext = "unrar e";
		} else if ($file_ext == "tar") {
			$file_ext = "tar -xvf";
		} else if ($file_ext == "zip") {
			$file_ext = "unzip";
		}

		//Komanda za izvrsavanje
		$unn_file = unn_file($server_ip, $ssh_port, $ftp_username, $ftp_password, $file_ext, $file_name);

		if ($unn_file == "OK") {
			$log_msg = "Uspesno izvrsena komanda UN FILE.";
			$v_d = time();
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
			$_SESSION['ok'] = "Uspesno izvrsena komanda UN FILE.";
			header("Location: gp-webftp.php?id=".$server_id.'&path='.$path);
			die();
		} else {
			$_SESSION['error'] = "Greska. - UN FILE GP";
			header("Location: gp-webftp.php?id=".$server_id.'&path='.$path);
			die();
		}

	} else {
		$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
		header("Location: gp-home.php");
		die();
	}

}

/* ADD PLUGIN */

if (isset($_GET['task']) && $_GET['task'] == "add_plugin") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$plugin_token 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['plugin_token'])));
	$plugin_token2 	= htmlspecialchars(mysql_real_escape_string(addslashes($_SESSION['plugin_token'])));
	$lokacija 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['lokacija'])));
	$path 			= $lokacija;
	$file_text_edit = htmlspecialchars($_POST['file_text_edit']);
	if ($server_id == ""||$plugin_token == ""||$plugin_token2 == "") {
		$_SESSION['error'] = "Greska. - ADD PLUGIN GP-FTP";
		header("Location: gp-home.php");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if ($plugin_token == $plugin_token2) {
		
		$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
		if (!$pp_server) {
			$_SESSION['info'] = "Ovaj server ne postoji.";
			header("Location: gp-home.php");
			die();
		}

		if ($pp_server['status'] == "Suspendovan") {
			$_SESSION['info'] = "Vas server nije Aktivan.";
			header("Location: gp-info.php?id=".$server_id);
			die();
		}

		if ($pp_server['user_id'] == $_SESSION['userid']) {
			$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
			
			$ftp = ftp_connect($info['ip'], 21);
			if(!$ftp) {
				$_SESSION['error'] = "Ne mogu se spojiti na ftp server";
				header("Location: gp-webftp.php?id=".$server_id);
				die();
			}
				
			if (ftp_login($ftp, $pp_server['username'], $pp_server['password'])) {
				
				ftp_pasv($ftp, true);	
				if(!empty($path)) {
					ftp_chdir($ftp, $path);
				}

				$fajl = $_FILES["file"]["tmp_name"];
				$ime_fajla = $_FILES["file"]["name"];
				
				$allow_ext = ['txt', 'cfg', 'sma', 'SMA', 'inf', 'ini', 'log', 'rc', 'yml', 'json', 'properties', 'amxx', 'mdl', 'bsp'];
				
				$temp = explode(".", $_FILES["file"]["name"]);
				$ext = strtolower(end($temp));
				if(in_array($ext, $allow_ext) === false) {  
					$_SESSION['error'] = "Taj format nije podrzan."; 
					header("Location: gp-webftp.php?id=".$server_id."&path=".$path); 
					die(); 
				}

				if($_FILES["file"]["size"] > 8388608) { 
					$_SESSION['error'] = "Fajl moze biti najvise 8mb!"; 
					header("Location: gp-webftp.php?id=".$server_id."&path=".$path); 
					die(); 
				}
				
				if(!empty($path)) $putanja_na_serveru = $ime_fajla;
				else $putanja_na_serveru = $path.'/'.$ime_fajla;
				
				if(ftp_put($ftp, $putanja_na_serveru, $fajl, FTP_BINARY)) {
					$log_msg = "Uspesan upload ".$ime_fajla.' .';
					$v_d = time();
					$ip = get_client_ip();

					mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
					$_SESSION['ok'] = "Uspesno ste upload ".$ime_fajla.' .';
					header("Location: gp-webftp.php?id=".$server_id."&path=".$path); 
					die();
				} else {
					$_SESSION['error'] = "Greska.";
					header("Location: gp-webftp.php?id=".$server_id."&path=".$path);				
					die();
				}

			}
			ftp_close($ftp);
		} else {
			$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
			header("Location: gp-home.php");
			die();
		}

	} else {
		$_SESSION['error'] = "Token se ne poklapa.";
		header("Location: gp-webftp.php?id=".$server_id);
		die();
	}

}

/* DELETE FILE */

if (isset($_GET['task']) && $_GET['task'] == "delete_file") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$lokacija 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['file_location'])));
	$file_name 		= $_POST['file_name'];

	if ($server_id == ""||$lokacija == ""||$file_name == "") {
		$_SESSION['error'] = "Greska. - DELETE FILE GP-FTP";
		header("Location: gp-webftp.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}
		
	if (is_pin() == true) {
		if ($pp_server['user_id'] == $_SESSION['userid']) {
			$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));

			$aes = new Crypt_AES();
	        $aes->setKeyLength(256);
	        $aes->setKey(CRYPT_KEY);    

			$server_id2 	= $pp_server['id'];
			$server_ip 		= $info['ip'];
			$ssh_port 		= $info['sshport'];
			$root_user		= $info['login'];
			//$root_pw		= $aes->decrypt($info['password']);
			$server_port    = $pp_server['port'];
			$ftp_username 	= $pp_server['username'];
			$ftp_password 	= $pp_server['password'];

			//Komanda za izvrsavanje
			
			$ftp = ftp_connect($server_ip, 21);
			if(!$ftp) {
				$_SESSION['error'] = "Ne mogu se spojit sa FTP serverom.";
				header("Location: $_SERVER[HTTP_REFERER]");
				die(); 
			}
				
			if (ftp_login($ftp, $ftp_username, $ftp_password)) {		
		        ftp_pasv($ftp, true);
				
				if(!empty($lokacija)) {
					ftp_chdir($ftp, $lokacija);	
				}		
				
				if(ftp_delete($ftp, $lokacija.'/'.$file_name)) {
					$_SESSION['ok'] = "Uspesno ste obrisali fajl. <b>$file_name</b>";
					header("Location: gp-webftp.php?id=".$server_id."&path=".$lokacija);
					die(); 
				} else {
					$_SESSION['error'] = "Doslo je do greske, vas fajl <b>$file_name</b> nije obrisan.";
					header("Location: gp-webftp.php?id=".$server_id."&path=".$lokacija);
					die();
				}
			}
			ftp_close($ftp);

		} else {
			$_SESSION['error'] = "Nemas pristup.";
			header("Location: gp-home.php");
			die();
		}
	} else {
		$_SESSION['error'] = "Da bi pristupili ovoj opciji, morate ukucati PIN kod!";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}
}

/* DELETE FOLDER */

if (isset($_GET['task']) && $_GET['task'] == "delete_folder") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$lokacija 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['file_location'])));
	$folder_name 	= $_POST['folder_name'];

	if ($server_id == ""||$lokacija == ""||$folder_name == "") {
		$_SESSION['error'] = "Greska. - DELETE FILE GP-FTP";
		header("Location: gp-webftp.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}
		
	if (is_pin() == true) {
		if ($pp_server['user_id'] == $_SESSION['userid']) {
			$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));

			$aes = new Crypt_AES();
	        $aes->setKeyLength(256);
	        $aes->setKey(CRYPT_KEY);    

			$server_id2 	= $pp_server['id'];
			$server_ip 		= $info['ip'];
			$ssh_port 		= $info['sshport'];
			$root_user		= $info['login'];
			//$root_pw		= $aes->decrypt($info['password']);
			$server_port    = $pp_server['port'];
			$ftp_username 	= $pp_server['username'];
			$ftp_password 	= $pp_server['password'];

			//Komanda za izvrsavanje
			
			$ftp = ftp_connect($server_ip, 21);
			if(!$ftp) {
				$_SESSION['error'] = "Ne mogu se spojit sa FTP serverom.";
				header("Location: $_SERVER[HTTP_REFERER]");
				die(); 
			}
				
			if (ftp_login($ftp, $ftp_username, $ftp_password)) {		
            	ftp_pasv($ftp, true);	
				
				if(!empty($lokacija)){
					ftp_chdir($ftp, $lokacija);	
				}

				function ftp_delAll($conn_id,$dst_dir) {
					$ar_files = ftp_nlist($conn_id, $dst_dir);
					if (is_array($ar_files)) { 
						for ($i=0;$i<sizeof($ar_files);$i++) { 
							$st_file = basename($ar_files[$i]);
							if($st_file == '.' || $st_file == '..') continue;
							if (ftp_size($conn_id, $dst_dir.'/'.$st_file) == -1) ftp_delAll($conn_id,  $dst_dir.'/'.$st_file); 
							else ftp_delete($conn_id,  $dst_dir.'/'.$st_file);
						}
						sleep(1);
						ob_flush();
					}
					if(ftp_rmdir($conn_id, $dst_dir)) return "true";
				}				
			
				function ftp_folderdel($conn_id,$dst_dir) {
					$ar_files = ftp_nlist($conn_id, $dst_dir);
					if (is_array($ar_files)) { 
						for ($i=0;$i<sizeof($ar_files);$i++) { 
							$st_file = basename($ar_files[$i]);
							if($st_file == '.' || $st_file == '..') continue;
							if (ftp_size($conn_id, $dst_dir.'/'.$st_file) == -1) { 
								ftp_delAll($conn_id,  $dst_dir.'/'.$st_file); 
							} else {
								ftp_delete($conn_id,  $dst_dir.'/'.$st_file);
							}
						}
						sleep(1);
						ob_flush();
					}
					if(ftp_rmdir($conn_id, $dst_dir)) {
						return "true";
					}
				}			
			
				if(ftp_folderdel($ftp, $lokacija.'/'.$folder_name)) {
					$_SESSION['ok'] = "Uspesno ste obrisali folder. <b>$folder_name</b>";
					header("Location: gp-webftp.php?id=$server_id&path=$lokacija");
					die(); 
				} else {
					$_SESSION['error'] = "Doslo je do greske, vas folder <b>$folder_name</b> nije obrisan.";
					header("Location: gp-webftp.php?id=$server_id&path=$lokacija");
					die(); 
				}
				ftp_close($ftp);
			}
		} else {
			$_SESSION['error'] = "Nemas pristup.";
			header("Location: gp-home.php");
			die();
		}
	} else {
		$_SESSION['error'] = "Da bi pristupili ovoj opciji, morate ukucati PIN kod!";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}
}

/* PROMENI MOD */

if (isset($_GET['task']) && $_GET['task'] == "promeni_mod") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$server_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$mmod_token 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['mmod_token'])));
	$mmod_token2 	= htmlspecialchars(mysql_real_escape_string(addslashes($_SESSION['mmod_token'])));
	$mod_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['mod_id'])));
	$mod_putanja 	= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['mod_putanja'])));
	if ($server_id == ""||$mmod_token == ""||$mmod_token2 == "") {
		$_SESSION['error'] = "Greska. - PROMENI MOD (GP)";
		header("Location: gp-home.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
		$_SESSION['info'] = "Ovaj server ne postoji.";
		header("Location: gp-home.php");
		die();
	}

	if ($pp_server['status'] == "Suspendovan") {
		$_SESSION['info'] = "Vas server nije Aktivan.";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

	if ($pp_server['startovan'] == "1") {
		$_SESSION['info'] = "Vas server mora biti stopiran.";
		header("Location: gp-mods.php?id=".$server_id);
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}
		
	if (is_pin() == true) {
		if ($pp_server['user_id'] == $_SESSION['userid']) {
			$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
			
			$mod = mysql_fetch_array(mysql_query("SELECT * FROM `modovi` WHERE id='$mod_id'"));

			$aes = new Crypt_AES();
	        $aes->setKeyLength(256);
	        $aes->setKey(CRYPT_KEY);    

			$server_id2 	= $pp_server['id'];
			$server_ip 		= $info['ip'];
			$ssh_port 		= $info['sshport'];
			$root_user		= $info['login'];
			$root_pw		= $aes->decrypt($info['password']);
			$server_port    = $pp_server['port'];
			$ftp_username 	= $pp_server['username'];
			$ftp_password 	= $pp_server['password'];
			$server_slot 	= $pp_server['slotovi'];
			$server_mapa 	= $pp_server['map'];
			$server_fps 	= $pp_server['fps'];
			$server_mod 	= $pp_server['modovi'];

			$mod_putanja 	= $mod['putanja'];
			$mod_id_baza 	= $mod['id'];
			$mod_mapa 		= $mod['mapa'];

			//Komanda za izvrsavanje
			
			/*
			Testiranje.
			echo $mod_id;
			echo "<br />";
			print_r($mod);
			echo "<br />";
			echo $mod_putanja;
			die();

			*/

			$new_mod = promeni_mod($server_ip, $ssh_port, $root_user, $root_pw, $ftp_username, $mod_id);
			if ($new_mod == "OK") {
				mysql_query("UPDATE `serveri` SET `mod` = '$mod_id_baza' WHERE `id` = '$server_id2'");
				mysql_query("UPDATE `serveri` SET `map` = '$mod_mapa' WHERE `id` = '$server_id2'");

				$log_msg = "Uspesna promena moda.";
				$v_d = time();
				$ip = get_client_ip();

				mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

				$_SESSION['ok'] = "Uspesno ste promenili vas mod na serveru.";
				header("Location: gp-info.php?id=".$server_id2);
				die();
			} else {
				$_SESSION['error'] = "SSH Greska. - FTP PASSWORD";
				header("Location: gp-info.php?id=".$server_id2);
				die();
			}
		} else {
			$_SESSION['error'] = "Nemas pristup.";
			header("Location: gp-home.php");
			die();
		}
	} else {
		$_SESSION['error'] = "PIN?";
		header("Location: gp-info.php?id=".$server_id);
		die();
	}

}

/* OTVORI NOVI TIKET */

if (isset($_GET['task']) && $_GET['task'] == "add_tiket") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	$server_id 			= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	$tiket_naslov 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['tiket_naslov'])));
	$tiket_text 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['tiket_text'])));
	
	// Zastita za Javascript, Html i ostalo.
	$zamene = array (
		':D' 		=> '<img src="./img/smile/002.png" style="width:auto;height:auto;"/>',
		':P' 		=> '<img src="./img/smile/104.png" style="width:auto;height:auto;"/>',
		'o.o' 		=> '<img src="./img/smile/012.png" style="width:auto;height:auto;"/>',
		':)' 		=> '<img src="./img/smile/001.png" style="width:auto;height:auto;"/>',
		':m' 		=> '<img src="./img/smile/006.png" style="width:auto;height:auto;"/>',
		';)' 		=> '<img src="./img/smile/003.gif" style="width:auto;height:auto;"/>',
		':O' 		=> '<img src="./img/smile/004.png" style="width:auto;height:auto;"/>',
		':/' 		=> '<img src="./img/smile/007.png" style="width:auto;height:auto;"/>',
		':$' 		=> '<img src="./img/smile/008.png" style="width:auto;height:auto;"/>',
		':S' 		=> '<img src="./img/smile/009.png" style="width:auto;height:auto;"/>',
		':(' 		=> '<img src="./img/smile/010.png" style="width:auto;height:auto;"/>',
		';(' 		=> '<img src="./img/smile/011.gif" style="width:auto;height:auto;"/>',
		'&lt;3' 	=> '<3',
		'&lt;/3'	=> '</3',
		'<3' 		=> '<img src="./img/smile/015.png"  style="width:auto;height:auto;"/>',
		'</3' 		=> '<img src="./img/smile/016.png"  style="width:auto;height:auto;"/>',
		'-.-' 		=> '<img src="./img/smile/083.png"  style="width:auto;height:auto;"/>',
		':n' 		=> '<img src="./img/smile/086.png"  style="width:auto;height:auto;"/>',
		':P' 		=> '<img src="./img/smile/104.png"  style="width:auto;height:auto;"/>',
		':T' 		=> '<img src="./img/smile/tuga.gif" style="width:auto;height:auto;"/>',
		'xD' 		=> '<img src="./img/smile/xD.png" 	 style="width:auto;height:auto;">',
		'picka'		=> '**cka',
		'kurac' 	=> '**rac',
		'svinja' 	=> '**inja',
		'stoka' 	=> '**oka',
		'materina' 	=> '***erina',
	);
		
	$tiket_text = str_replace(array_keys($zamene), array_values($zamene), $tiket_text);
	
	$d_v = date("h.m.s, d-m-Y");

	if ($server_id == ""||$tiket_naslov == ""||$tiket_text == "") {
		$_SESSION['error'] = "Greska. - NEW TIKET -SRW ID (GP)";
		header("Location: gp-support.php");
		die();
	}

	$tiket_option = mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'");
	if (!mysql_num_rows($tiket_option)>0) {
		$_SESSION['error'] = "Greska. Ovaj server ne postoji ili nemate ovlascenje za isti.";
		header("Location: gp-support.php");
		die();
	}
	
	$tiket_i = mysql_fetch_array($tiket_option);
	
	if ($tiket_i['user_id'] == $_SESSION['userid']) {
		
		$new_tiket = mysql_query("INSERT INTO `tiketi` (`id`, `admin_id`, `server_id`, `user_id`, `status`, `prioritet`, `vrsta`, `datum`, `naslov`, `poruka`, `billing`, `admin`, `otvoren`) VALUES (NULL, NULL, '$tiket_i[id]', '$_SESSION[userid]', '1', '1', '1', '$d_v', '$tiket_naslov', '$tiket_text', '0', '', '');");

		if (!$new_tiket) {
			$_SESSION['error'] = "Greska. - TIKET (GP)";
			header("Location: gp-support.php");
			die();
		} else {
			$t_i_t = mysql_fetch_array(mysql_query("SELECT * FROM `tiketi` WHERE `user_id` = '$_SESSION[userid]' ORDER BY datum DESC"));
			$t_i_t_u = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$_SESSION[userid]'"));
			
			$tiket_naslov_a = "Otvorio novi tiket: <a href=\"tiket.php?id={$t_i_t['id']}\">{$t_i_t['naslov']}</a>";
			$tiket_autor_a = "<font color=\"silver\">{$t_i_t_u['ime']} {$t_i_t_u['prezime']}</font>";
			$tiket_author_id = "klijent_$_SESSION[userid]";
			
			$a_chat = mysql_query("INSERT INTO `chat_messages` (`Text`, `Autor`, `Datum`, `ID`, `admin_id`) VALUES ('$tiket_naslov_a', '$tiket_autor_a', '$d_v', '$t_i_t[id]', '$tiket_author_id');");

			$_SESSION['ok'] = "Uspesno ste otvorili novi tiket.";
			header("Location: gp-support.php");
			die();
		}
	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: gp-newtiket.php");
		die();
	}

}

/* ZAKLJUCAJ TIKET */

if (isset($_GET['task']) && $_GET['task'] == "tiket_lock") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	$tiket_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['tiket_id'])));
	if ($tiket_id == "") {
		$_SESSION['error'] = "Greska. - TIKET ID (GP)";
		header("Location: gp-support.php");
		die();
	}

	$tiket_option = mysql_query("SELECT * FROM `tiketi` WHERE `id` = '$tiket_id' AND `user_id` = '$_SESSION[userid]'");
	if (!mysql_num_rows($tiket_option)>0) {
		$_SESSION['error'] = "Greska. Ovaj tiket ne postoji.";
		header("Location: gp-support.php");
		die();
	}
	
	$tiket_i = mysql_fetch_array($tiket_option);
	
	if ($tiket_i['user_id'] == $_SESSION['userid']) {
		$zakljcan = mysql_query("UPDATE `tiketi` SET `status` = '0' WHERE `id` = '$tiket_id'");
		if (!$zakljcan) {
			$_SESSION['error'] = "Greska. - TIKET LOCK (GP)";
			header("Location: gp-tiket.php?id=".$tiket_id);
			die();
		} else {
			$_SESSION['ok'] = "Uspesno ste zakljucali tiket.";
			header("Location: gp-tiket.php?id=".$tiket_id);
			die();
		}
	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: gp-tiket.php?id=".$tiket_id);
		die();
	}

}

/* ODKLJCAJ TIKET */

if (isset($_GET['task']) && $_GET['task'] == "tiket_unlock") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	$tiket_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['tiket_id'])));
	if ($tiket_id == "") {
		$_SESSION['error'] = "Greska. - TIKET ID (GP)";
		header("Location: gp-support.php");
		die();
	}

	$tiket_option = mysql_query("SELECT * FROM `tiketi` WHERE `id` = '$tiket_id' AND `user_id` = '$_SESSION[userid]'");
	if (!mysql_num_rows($tiket_option)>0) {
		$_SESSION['error'] = "Greska. Ovaj tiket ne postoji.";
		header("Location: gp-support.php");
		die();
	}
	
	$tiket_i = mysql_fetch_array($tiket_option);
	
	if ($tiket_i['user_id'] == $_SESSION['userid']) {
		$odkljucan = mysql_query("UPDATE `tiketi` SET `status` = '1' WHERE `id` = '$tiket_id'");
		if (!$odkljucan) {
			$_SESSION['error'] = "Greska. - TIKET UNLOCK (GP)";
			header("Location: gp-tiket.php?id=".$tiket_id);
			die();
		} else {
			$_SESSION['ok'] = "Uspesno ste odkljucali tiket.";
			header("Location: gp-tiket.php?id=".$tiket_id);
			die();
		}
	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: gp-tiket.php?id=".$tiket_id);
		die();
	}

}

/* DODAJ ODGOVOR NA TIKET */

if (isset($_GET['task']) && $_GET['task'] == "add_odgovor") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	$tiket_id 			= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['tiket_id'])));
	$add_odgovor 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['add_odgovor'])));
	
	// Zastita za Javascript, Html i ostalo.
	$zamene = array (
		':D' 		=> '<img src="./img/smile/002.png" style="width:auto;height:auto;"/>',
		':P' 		=> '<img src="./img/smile/104.png" style="width:auto;height:auto;"/>',
		'o.o' 		=> '<img src="./img/smile/012.png" style="width:auto;height:auto;"/>',
		':)' 		=> '<img src="./img/smile/001.png" style="width:auto;height:auto;"/>',
		':m' 		=> '<img src="./img/smile/006.png" style="width:auto;height:auto;"/>',
		';)' 		=> '<img src="./img/smile/003.gif" style="width:auto;height:auto;"/>',
		':O' 		=> '<img src="./img/smile/004.png" style="width:auto;height:auto;"/>',
		':/' 		=> '<img src="./img/smile/007.png" style="width:auto;height:auto;"/>',
		':$' 		=> '<img src="./img/smile/008.png" style="width:auto;height:auto;"/>',
		':S' 		=> '<img src="./img/smile/009.png" style="width:auto;height:auto;"/>',
		':(' 		=> '<img src="./img/smile/010.png" style="width:auto;height:auto;"/>',
		';(' 		=> '<img src="./img/smile/011.gif" style="width:auto;height:auto;"/>',
		'&lt;3' 	=> '<3',
		'&lt;/3'	=> '</3',
		'<3' 		=> '<img src="./img/smile/015.png"  style="width:auto;height:auto;"/>',
		'</3' 		=> '<img src="./img/smile/016.png"  style="width:auto;height:auto;"/>',
		'-.-' 		=> '<img src="./img/smile/083.png"  style="width:auto;height:auto;"/>',
		':n' 		=> '<img src="./img/smile/086.png"  style="width:auto;height:auto;"/>',
		':P' 		=> '<img src="./img/smile/104.png"  style="width:auto;height:auto;"/>',
		':T' 		=> '<img src="./img/smile/tuga.gif" style="width:auto;height:auto;"/>',
		'xD' 		=> '<img src="./img/smile/xD.png" 	 style="width:auto;height:auto;">',
		'picka'		=> '**cka',
		'kurac' 	=> '**rac',
		'svinja' 	=> '**inja',
		'stoka' 	=> '**oka',
		'materina' 	=> '***erina',
	);
		
	$add_odgovor = str_replace(array_keys($zamene), array_values($zamene), $add_odgovor);
	
	$d_v = time();

	if ($tiket_id == ""||$add_odgovor == "") {
		$_SESSION['error'] = "Greska. - TIKET ID (GP)";
		header("Location: gp-support.php");
		die();
	}

	$tiket_option = mysql_query("SELECT * FROM `tiketi` WHERE `id` = '$tiket_id' AND `user_id` = '$_SESSION[userid]'");
	if (!mysql_num_rows($tiket_option)>0) {
		$_SESSION['error'] = "Greska. Ovaj tiket ne postoji.";
		header("Location: gp-support.php");
		die();
	}
	
	$tiket_i = mysql_fetch_array($tiket_option);
	
	if ($tiket_i['user_id'] == $_SESSION['userid']) {
		
		$tiket_odg = mysql_query("INSERT INTO `tiketi_odgovori` (`id`, `tiket_id`, `user_id`, `admin_id`, `odgovor`, `vreme_odgovora`) VALUES (NULL, '$tiket_i[id]', '$_SESSION[userid]', NULL, '$add_odgovor', '$d_v');");

		if (!$tiket_odg) {
			$_SESSION['error'] = "Greska. - TIKET UNLOCK (GP)";
			header("Location: gp-tiket.php?id=".$tiket_id);
			die();
		} else {
			$_SESSION['ok'] = "Uspesno ste odkljucali tiket.";
			header("Location: gp-tiket.php?id=".$tiket_id);
			die();
		}
	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: gp-tiket.php?id=".$tiket_id);
		die();
	}

}

/* PONOVO NA PRIKAZ ADMINU */

if (isset($_GET['task']) && $_GET['task'] == "send_view") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$_SESSION['info'] = "Ova funkcija je u izradi, molimo vas da se strpite.";
	header("Location: gp-support.php");
	die();

}

/* GENERISI NOVI TOKEN */
if (isset($_GET['task']) && $_GET['task'] == "client_new_token") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	$stari_token 	= ispravi_text_sql($_POST['stari_token']);
	$new_token 		= ispravi_text_sql($_POST['new_token']);
	$novi_token_s 	= ispravi_text_sql($_SESSION['new_token']);

	if ($stari_token == ""||$new_token == ""||$novi_token_s == "") {
		$_SESSION['error'] = "Greska. - TOKEN ?? (GP)";
		header("Location: gp-settings.php");
		die();
	}

	//echo $stari_token.'--------'.$new_token.'--------'.$novi_token_s;

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	if (is_pin() == true) {
		if (!$new_token == $novi_token_s) {
			$new_token = $new_token;
		} else {
			$new_token = $novi_token_s;
		}

		$uzmi_usera_t = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$_SESSION[userid]'"));

		if ($uzmi_usera_t['token'] == $new_token) {
			$_SESSION['error'] = "Greska. TOKEN ID (GP-E)";
			header("Location: gp-settings.php");
			die();
		} else {
			$promeni_token_b = mysql_query("UPDATE `klijenti` SET `token` = '$new_token' WHERE `klijentid` = '$_SESSION[userid]'");
			if (!$promeni_token_b) {
				$_SESSION['error'] = "Greska. Token nije promenjen.";
				header("Location: gp-settings.php");
				die();
			} else {
				$log_msg = "Uspesno generisan novi TOKEN.";
				$v_d = time();
				$ip = get_client_ip();

				mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

				$_SESSION['ok'] = "Uspesno ste promenili vas token.";
				header("Location: gp-settings.php");
				die();
			}
		}
	} else {
		$_SESSION['error'] = "PIN?";
		header("Location: gp-settings.php");
		die();
	}
}

/* EDITUJ PROFIL */
if (isset($_GET['task']) && $_GET['task'] == "edit_profile") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	$ime 			= ispravi_text_sql($_POST['ime']);
	$prezime 		= ispravi_text_sql($_POST['prezime']);
	$password 		= ispravi_text_sql($_POST['password']);
	$avatar 		= ispravi_text_sql($_POST['avatar']);


	if ($ime == "") {
		$_SESSION['error'] = "Greska. - Polje IME je prazno. (GP)";
		header("Location: gp-settings.php");
		die();
	}

	if ($prezime == "") {
		$_SESSION['error'] = "Greska. - Polje PREZIME je prazno. (GP)";
		header("Location: gp-settings.php");
		die();
	}

	//echo $stari_token.'--------'.$new_token.'--------'.$novi_token_s;

	$user_edit = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$_SESSION[userid]'"));

	if (is_pin() == true) {
		if ($password == "") {
			$password = $user_edit['sifra'];
		} else {
			if(strlen($password) > 28 || strlen($password) < 6) {
				$_SESSION['error'] = "Sifra ne smije biti kraca od 6, i duza od 28 karaktera.";
				header("Location: gp-settings.php");
				die();
			} else {
				$salt = hash('sha512', $user_edit['username']);
				$password = hash('sha512', $salt.$password);
				//$password = md5($password);
			}
		}

		$edit_profile = mysql_query("UPDATE `klijenti` SET `ime` = '$ime',
														   `prezime` = '$prezime',
														   `sifra` = '$password', `avatar` = '$avatar' WHERE `klijentid` = '$_SESSION[userid]'");
		if (!$edit_profile) {
			$_SESSION['error'] = "Greska. Profil nije spremnjen.";
			header("Location: gp-settings.php");
			die();
		} else {
			$log_msg = "Uspesno editovan vas profil.";
			$v_d = time();
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");
			$_SESSION['ok'] = "Uspesno ste editovali vas profil.";
			header("Location: gp-settings.php");
			die();
		}
	} else {
		$_SESSION['error'] = "PIN?";
		header("Location: gp-settings.php");
		die();
	}
}

if (isset($_GET['task']) && $_GET['task'] == "boost_server") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}
	
	$server_id = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
	
	if ($server_id == "") {
		$_SESSION['error'] = "Greska - BOOST";
		header("Location: gp-home.php");
		die();
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	$info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$pp_server[box_id]'"));
	$server_ip 		= $info['ip'];
	$ime 		= $info['name'];
	$server_port    = $pp_server['port'];
	$fullip = $server_ip.":".$server_port;
	$vreme = date('Y-m-d H:i:s');
	
	$razlikavremena =($istice-strtotime($vreme)) / 86400;
	
	$isticequery = $mdb->query("SELECT * FROM servers WHERE `ip`= '$fullip'");
    $broj = mysqli_num_rows($isticequery);
	
	if($pp_server['igra'] != "1") {
	$_SESSION['info'] = "GRE`A! - VA?SERVER NIJE CS 1.6";
	header("Location: $_SERVER[HTTP_REFERER]");
	die();
	}

	
	if($broj == 0)
	{
	$querystr = "INSERT INTO servers (ip, ime) VALUES ('$fullip', '$ime')";
	if($mdb->query($querystr) === TRUE) {
	$srvid = $mdb->insert_id;
	$lista = "INSERT INTO boost_list (serverid, expiry_time) VALUES ('$srvid', '$razlikavremena')";
	if($mdb->query($lista)=== TRUE)
	{
	$_SESSION['info'] = "Uspe?o ste boostovali va?server!";
	header("Location: $_SERVER[HTTP_REFERER]");
	die();
	}
	else
	{
echo $mdb->error;
	}
	} else { 	    echo $mdb->error;}
	}
	else 
	{
		$_SESSION['info'] = "Sacekajte 2 dana da bi boostovali opet server!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}
}

/* UPLATI SERVER */

if (isset($_GET['task']) && $_GET['task'] == "billing_srv_uplata") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}
	
	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}
	
	$billing_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['billing_id'])));
	if ($billing_id == "") {
		$_SESSION['error'] = "Greska. - BILLING ID (GP)";
		header("Location: gp-narudzbine.php");
		die();
	}
	
	$billing_option = mysql_query("SELECT * FROM `billing` WHERE `id` = '$billing_id' AND `klijentid` = '$_SESSION[userid]'");
	if (!mysql_num_rows($billing_option) > 0) {
		$_SESSION['error'] = "Greska. Ovaj billing ne postoji ili nije vas.";
		header("Location: gp-narudzbine.php");
		die();
	}
	
	$billing_i = mysql_fetch_array($billing_option);
	
	$game = $billing_i['game'];
	
	if($game == "Counter-Strike 1.6") {
		
	} else if ($game == "GTA San Andreas") {
		
	} else if ($game == "Minecraft") {
		
	} else if ($game == "Team-Speak 3") {
		
	} else if ($game == "SinusBot") {
		
	} else if ($game == "FastDL") {
		
	} else {
		$_SESSION['error'] = "Ovu igru trenutno nemamo u ponudi!";
		header("Location: gp-narudzbine-w.php?id=".$billing_id);
		die();
	}
	
	if ($billing_i['klijentid'] == $_SESSION['userid']) {
		if(userMoney($_SESSION['userid']) >=  $billing_i['iznos']) {
			$update = mysql_query("UPDATE `billing` SET `BillingStatus` = '1' WHERE `id` = '$billing_id'");
			
			$update2 = userMoneyUpdate($_SESSION['userid'], $billing_i['iznos'], false);
			
			if (!$update || !$update2) {
				$_SESSION['error'] = "Greska. - BILLING UPLATA (GP)";
				header("Location: gp-narudzbine-w.php?id=".$billing_id);
				die();
			} else {
				$_SESSION['ok'] = "Uspesno ste uplatili Server.";
				header("Location: gp-narudzbine-w.php?id=".$billing_id);
				die();
			}
		} else {
			$_SESSION['error'] = "Nemas dovoljno novca da uplatis server.";
			header("Location: gp-narudzbine-w.php?id=".$billing_id);
			die();
		}
	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: gp-narudzbine.php");
		die();
	}

}

/* POVRATI NOVAC */

if (isset($_GET['task']) && $_GET['task'] == "billing_srv_refund") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}
	
	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}
	
	$billing_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['billing_id'])));
	if ($billing_id == "") {
		$_SESSION['error'] = "Greska. - BILLING ID (GP)";
		header("Location: gp-narudzbine.php");
		die();
	}
	
	$billing_option = mysql_query("SELECT * FROM `billing` WHERE `id` = '$billing_id' AND `klijentid` = '$_SESSION[userid]'");
	if (!mysql_num_rows($billing_option) > 0) {
		$_SESSION['error'] = "Greska. Ovaj billing ne postoji ili nije vas.";
		header("Location: gp-narudzbine.php");
		die();
	}
	
	$billing_i = mysql_fetch_array($billing_option);
	
	$game = $billing_i['game'];
	
	if($game == "Counter-Strike 1.6") {
		
	} else if ($game == "GTA San Andreas") {
		
	} else if ($game == "Minecraft") {
		
	} else if ($game == "SinusBot") {
		
	} else if ($game == "Team-Speak 3") {
		
	} else if ($game == "FastDL") {
		
	} else {
		$_SESSION['error'] = "Ovu igru trenutno nemamo u ponudi!";
		header("Location: gp-narudzbine-w.php?id=".$billing_id);
		die();
	}
	
	if ($billing_i['klijentid'] == $_SESSION['userid']) {
		$update = mysql_query("UPDATE `billing` SET `BillingStatus` = '0' WHERE `id` = '$billing_id'");
		
		$update2 = userMoneyUpdate($_SESSION['userid'], $billing_i['iznos'], true);
		
		if (!$update || !$update2) {
			$_SESSION['error'] = "Greska. - BILLING REFUND (GP)";
			header("Location: gp-narudzbine-w.php?id=".$billing_id);
			die();
		} else {
			$_SESSION['ok'] = "Uspesno ste povratili novac.";
			header("Location: gp-narudzbine-w.php?id=".$billing_id);
			die();
		}
	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: gp-narudzbine.php");
		die();
	}
}

/* OBRISI BILLING */

if (isset($_GET['task']) && $_GET['task'] == "billing_del") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}
	
	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}
	
	$billing_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['billing_id'])));
	if ($billing_id == "") {
		$_SESSION['error'] = "Greska. - BILLING ID (GP)";
		header("Location: gp-narudzbine.php");
		die();
	}
	
	$billing_option = mysql_query("SELECT * FROM `billing` WHERE `id` = '$billing_id' AND `klijentid` = '$_SESSION[userid]'");
	if (!mysql_num_rows($billing_option) > 0) {
		$_SESSION['error'] = "Greska. Ovaj billing ne postoji ili nije vas.";
		header("Location: gp-narudzbine.php");
		die();
	}
	
	$billing_i = mysql_fetch_array($billing_option);
	
	if ($billing_i['klijentid'] == $_SESSION['userid']) {
		$update = mysql_query("DELETE FROM `billing` WHERE `id` = '".$billing_id."'");
		
		if (!$update) {
			$_SESSION['error'] = "Greska. - BILLING DELETE (GP)";
			header("Location: gp-narudzbine-w.php?id=".$billing_id);
			die();
		} else {
			$_SESSION['ok'] = "Uspesno ste obrisali billing.";
			header("Location: gp-narudzbine.php");
			die();
		}
	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: gp-narudzbine.php");
		die();
	}
}



/* ZAKLJUCAJ BILLING TIKET */

if (isset($_GET['task']) && $_GET['task'] == "billing_tiket_lock") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	$tiket_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['tiket_id'])));
	if ($tiket_id == "") {
		$_SESSION['error'] = "Greska. - TIKET ID (GP)";
		header("Location: gp-narudzbine.php");
		die();
	}
	
	$tiket_option = mysql_query("SELECT * FROM `billing_tiketi` WHERE `id` = '$tiket_id' AND `user_id` = '$_SESSION[userid]'");
	if (!mysql_num_rows($tiket_option)>0) {
		$_SESSION['error'] = "Greska. Ovaj tiket ne postoji.";
		header("Location: gp-narudzbine.php");
		die();
	}
	
	$tiket_i = mysql_fetch_array($tiket_option);
	
	if ($tiket_i['user_id'] == $_SESSION['userid']) {
		$zakljcan = mysql_query("UPDATE `billing_tiketi` SET `status` = '3' WHERE `id` = '$tiket_id'");
		if (!$zakljcan) {
			$_SESSION['error'] = "Greska. - TIKET LOCK (GP)";
			header("Location: billing_tiket.php?id=".$tiket_id);
			die();
		} else {
			$_SESSION['ok'] = "Uspesno ste zakljucali tiket.";
			header("Location: billing_tiket.php?id=".$tiket_id);
			die();
		}
	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: billing_tiket.php?id=".$tiket_id);
		die();
	}

}

/* ODKLJCAJ TIKET */

if (isset($_GET['task']) && $_GET['task'] == "billing_tiket_unlock") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	$tiket_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['tiket_id'])));
	if ($tiket_id == "") {
		$_SESSION['error'] = "Greska. - TIKET ID (GP)";
		header("Location: gp-narudzbine.php");
		die();
	}

	$tiket_option = mysql_query("SELECT * FROM `billing_tiketi` WHERE `id` = '$tiket_id' AND `user_id` = '$_SESSION[userid]'");
	if (!mysql_num_rows($tiket_option)>0) {
		$_SESSION['error'] = "Greska. Ovaj tiket ne postoji.";
		header("Location: gp-narudzbine.php");
		die();
	}
	
	$tiket_i = mysql_fetch_array($tiket_option);
	
	if ($tiket_i['user_id'] == $_SESSION['userid']) {
		$odkljucan = mysql_query("UPDATE `billing_tiketi` SET `status` = '1' WHERE `id` = '$tiket_id'");
		if (!$odkljucan) {
			$_SESSION['error'] = "Greska. - TIKET UNLOCK (GP)";
			header("Location: billing_tiket.php?id=".$tiket_id);
			die();
		} else {
			$_SESSION['ok'] = "Uspesno ste odkljucali tiket.";
			header("Location: billing_tiket.php?id=".$tiket_id);
			die();
		}
	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: gp-tiket.php?id=".$tiket_id);
		die();
	}

}

/* DODAJ ODGOVOR NA TIKET */

if (isset($_GET['task']) && $_GET['task'] == "billing_add_odgovor") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}

	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}

	$tiket_id 			= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['tiket_id'])));
	$add_odgovor 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['add_odgovor'])));
	
	// Zastita za Javascript, Html i ostalo.
	$zamene = array (
		':D' 		=> '<img src="./img/smile/002.png" style="width:auto;height:auto;"/>',
		':P' 		=> '<img src="./img/smile/104.png" style="width:auto;height:auto;"/>',
		'o.o' 		=> '<img src="./img/smile/012.png" style="width:auto;height:auto;"/>',
		':)' 		=> '<img src="./img/smile/001.png" style="width:auto;height:auto;"/>',
		':m' 		=> '<img src="./img/smile/006.png" style="width:auto;height:auto;"/>',
		';)' 		=> '<img src="./img/smile/003.gif" style="width:auto;height:auto;"/>',
		':O' 		=> '<img src="./img/smile/004.png" style="width:auto;height:auto;"/>',
		':/' 		=> '<img src="./img/smile/007.png" style="width:auto;height:auto;"/>',
		':$' 		=> '<img src="./img/smile/008.png" style="width:auto;height:auto;"/>',
		':S' 		=> '<img src="./img/smile/009.png" style="width:auto;height:auto;"/>',
		':(' 		=> '<img src="./img/smile/010.png" style="width:auto;height:auto;"/>',
		';(' 		=> '<img src="./img/smile/011.gif" style="width:auto;height:auto;"/>',
		'&lt;3' 	=> '<3',
		'&lt;/3'	=> '</3',
		'<3' 		=> '<img src="./img/smile/015.png"  style="width:auto;height:auto;"/>',
		'</3' 		=> '<img src="./img/smile/016.png"  style="width:auto;height:auto;"/>',
		'-.-' 		=> '<img src="./img/smile/083.png"  style="width:auto;height:auto;"/>',
		':n' 		=> '<img src="./img/smile/086.png"  style="width:auto;height:auto;"/>',
		':P' 		=> '<img src="./img/smile/104.png"  style="width:auto;height:auto;"/>',
		':T' 		=> '<img src="./img/smile/tuga.gif" style="width:auto;height:auto;"/>',
		'xD' 		=> '<img src="./img/smile/xD.png" 	 style="width:auto;height:auto;">',
		'picka'		=> '**cka',
		'kurac' 	=> '**rac',
		'svinja' 	=> '**inja',
		'stoka' 	=> '**oka',
		'materina' 	=> '***erina',
	);
		
	$add_odgovor = str_replace(array_keys($zamene), array_values($zamene), $add_odgovor);
	
	$d_v = date("h.m.s, d-m-Y");

	if ($tiket_id == ""||$add_odgovor == "") {
		$_SESSION['error'] = "Greska. - TIKET ID (GP)";
		header("Location: gp-narudzbine.php");
		die();
	}

	$tiket_option = mysql_query("SELECT * FROM `billing_tiketi` WHERE `id` = '$tiket_id' AND `user_id` = '$_SESSION[userid]'");
	if (!mysql_num_rows($tiket_option)>0) {
		$_SESSION['error'] = "Greska. Ovaj tiket ne postoji.";
		header("Location: gp-narudzbine.php");
		die();
	}
	
	$tiket_i = mysql_fetch_array($tiket_option);
	
	if ($tiket_i['user_id'] == $_SESSION['userid']) {
		
		$time = time();
		$tiket_odg = mysql_query("INSERT INTO `billing_tiketi_odgovori` (`id`, `tiket_id`, `user_id`, `admin_id`, `odgovor`, `vreme_odgovora`) VALUES (NULL, '$tiket_i[id]', '$_SESSION[userid]', NULL, '$add_odgovor', '$time');");

		if (!$tiket_odg) {
			$_SESSION['error'] = "Greska. - TIKET UNLOCK (GP)";
			header("Location: billing_tiket.php?id=".$tiket_id);
			die();
		} else {
			$_SESSION['ok'] = "Uspesno ste odkljucali tiket.";
			header("Location: billing_tiket.php?id=".$tiket_id);
			die();
		}
	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: billing_tiket.php?id=".$tiket_id);
		die();
	}

}

/* DODAJ UPLATU */

if (isset($_GET['task']) && $_GET['task'] == "billing_add_banka") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}
	
	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}
	
	$ime			=		htmlspecialchars(mysql_real_escape_string(addslashes($_POST['ime'])));
	$novac			=		htmlspecialchars(mysql_real_escape_string(addslashes($_POST['novac'])));
	$link			=		htmlspecialchars(mysql_real_escape_string(addslashes($_POST['link'])));
	$drzava			=		htmlspecialchars(mysql_real_escape_string(addslashes($_POST['drzava'])));
	
	$d_v = date("h.m.s, d-m-Y");
	
	$in_base = mysql_query("INSERT INTO `uplate` (`id`, `klijentid`, `ime`, `novac`, `link`, `drzava`, `status`, `vreme`) VALUES (NULL, '$_SESSION[userid]', '$ime', '$novac', '$link', '$drzava', '0', '$d_v');");
	
	
	
	if (!$in_base) {
		$_SESSION['error'] = "Greska. - DODAJ UPLATU (GP)";
		header("Location: gp-addpayments.php?tip=banka");
		die();
	} else {
		$_SESSION['ok'] = "Uspesno ste dodali uplatu.";
		header("Location: gp-billing.php");
		die();
	}
}

function query_basic($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{	
		$greska = mysql_real_escape_string(mysql_error());
		mysql_query("INSERT INTO error_log (number, string, file, line, datum, vrsta) 
					VALUES ('1', 
							'{$greska}', 
							'mysql_greska', 
							'mysql_greska',
							'".time()."',
							'1')
					") or die(mysql_error());
	}
}

function query_numrows($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{
		$greska = mysql_real_escape_string(mysql_error());
		mysql_query("INSERT INTO error_log (number, string, file, line, datum, vrsta) 
					VALUES ('1', 
							'{$greska}', 
							'mysql_greska', 
							'mysql_greska',
							'".time()."',
							'1')
					") or die(mysql_error());
	}
	return (mysql_num_rows($result));
}

function query_fetch_assoc($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{
		$greska = mysql_real_escape_string(mysql_error());
		mysql_query("INSERT INTO error_log (number, string, file, line, datum, vrsta) 
					VALUES ('1', 
							'{$greska}', 
							'mysql_greska', 
							'mysql_greska',
							'".time()."',
							'1')
					") or die(mysql_error());
	}
	return (mysql_fetch_assoc($result));
}

?>