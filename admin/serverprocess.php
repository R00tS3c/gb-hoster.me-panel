<?php
$return = "test";
session_start();

include("konfiguracija.php");
include("includes.php");

include("../includes/func.server.inc.php");

require_once("./assets/libs/phpseclib/Crypt/AES.php");

error_reporting(E_ALL);

if (isset($_POST['task']))
{
	$task = mysql_real_escape_string($_POST['task']);
} 

else if(!empty($_POST['task']))
{
	header("Location: index.php");
}

else if (isset($_GET['task']))
{
	$task = mysql_real_escape_string($_GET['task']);
}

else if (!empty($_GET['task']))
{
	header("Location: index.php");
}

switch (@$task)
{
	case 'server-start':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		if(!is_numeric($serverid))
		{
			$error = "Server id je nepravilan!";
		}
		if(empty($serverid))
		{
			$error = "Server id je nepravilan!";
		}
		
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);		
		
		$klijent = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		//$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$server['mod']."'");
		$client = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '{$server[user_id]}'");
		
		if($server['igra'] == "2")
		{
			$ftp = ftp_connect($boxip['ip'], $boxip['ftpport']);
			if (!$ftp) {
				echo $jezik['text121'];
				die();
			}
			if (ftp_login($ftp, $server["username"], $server["password"])){
			
				ftp_pasv($ftp, true);
				
				if (!empty($path)) {
					ftp_chdir($ftp, $path);
				} else ftp_chdir($ftp, './');
				
				
				$folder = 'cache_folder/panel_'.$server["username"].'_samp_server.cfg';
				$fajl = "ftp://$server[username]:$server[password]@$boxip[ip]:$boxip[ftpport]/server.cfg";
				$lines = file($fajl, FILE_IGNORE_NEW_LINES);
				
				$bind = false;
				$port = false;
				$maxplayers = false;
				$rcon_pw = true;
				
				foreach ($lines as &$line) {
					
					$val = explode(" ", $line);
					
					if ($val[0] == "port") {
						$val[1] = $server['port'];
						$line = implode(" ", $val);
						$port = true;
					}
					else if ($val[0] == "maxplayers") {
						$val[1] = $server['slotovi'];
						$line = implode(" ", $val);
						$maxplayers = true;
					}
					else if ($val[0] == "bind") {
						$val[1] = $boxip['ip'];
						$line = implode(" ", $val);
						$bind = true;
					}
					else if ($val[0] == "rcon_password") {
						$val[1] = "changeme";
						$line = implode(" ", $val);
						$rcon_pw = false;
					}
				}
				unset($line);
				
				
				if (!$fw = fopen(''.$folder.'', 'w+')) 
				{
					echo $jezik['text131'];
				}
				foreach($lines as $line) {
					$fb = fwrite($fw,$line.PHP_EOL);
				}
				
				if (!$port) 
				{
					fwrite($fw,"port $server[port]".PHP_EOL);
				}
				if (!$maxplayers) 
				{
					fwrite($fw,"maxplayers $server[slotovi]".PHP_EOL);
				}
				if (!$bind) 
				{
					fwrite($fw,"bind $boxip[ip]".PHP_EOL);
				}
				
				if (!$rcon_pw) 
				{
					fwrite($fw,"rcon_password promenime".PHP_EOL);
				}
				
				$remote_file = ''.$path.'/server.cfg';
				if (!ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) 
				{
					echo $jezik['text131'];
				}
				fclose($fw);
				unlink($folder);
			}
			ftp_close($ftp);
		}		
		else if($server['igra'] == "3")
		{
			$ftp = ftp_connect($boxip['ip'], 21);
			if(!$ftp)
			{
				echo $jezik['text121'];
				die();
			}
				
			if (ftp_login($ftp, $server["username"], $server["password"]))
			{
				if(!empty($path))
				{
					ftp_chdir($ftp, $path);	
				} else ftp_chdir($ftp, './');	

				$folder = 'cache_folder/panel_'.$server["username"].'_server.properties';

				$fajl = "ftp://$server[username]:$server[password]@$boxip[ip]:21/server.properties";
				$lines = file($fajl, FILE_IGNORE_NEW_LINES);

				foreach($lines as &$line) {
				   $val = explode("=",$line);
				   if ($val[0]=="server-port") {
				      $val[1] = $server['port'];
				      $line = implode("=",$val);
				   }
				   else if ($val[0]=="query.port") {
				      $val[1] = $server['port'];
				      $line = implode("=",$val);
				   }
				   else if ($val[0]=="max-players") {
				      $val[1] = $server['slotovi'];
				      $line = implode("=",$val);
				   }
				   else if ($val[0]=="server-ip") {
				      $val[1] = $boxip['ip'];
				      $line = implode("=",$val);
				   }
				}
				unset($line);

				$fw = fopen(''.$folder.'', 'w+');
				foreach($lines as $line) {
				   $fb = fwrite($fw,$line.PHP_EOL);
				}				
				$file = "$fajl";
				$remote_file = ''.$path.'/server.properties';
				if (!ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) 
				{
					echo $jezik['text131'];
					die();
				}
				
				fclose($fw);

				unlink($folder);			
			}
			ftp_close($ftp);						
		}
		
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}
		
		$start = start_server($boxip['ip'], $box['sshport'], $server['username'], $server['password'], $serverid, "admin", FALSE);		
		
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}		
		
		if($start == "startovan")
		{	
			$poruka = "Startovao <z>".$server['name']."</z> server";
			alog($klijent['id'], $poruka, $klijent['fname'].' '.$klijent['lname'], fuckcloudflare(), time());

			$poruka = "<b>( Admin #{$klijent[fname]} )</b> Startovao <z>".$server['name']."</z> server";
			klijent_log($client['klijentid'], $poruka, $client['ime'].' '.$client['prezime'], fuckcloudflare(), time());

			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Server je startovan";
			$_SESSION['msg-type'] = 'success';
			
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}
		else
		{
			echo $start;
		}
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}		
	break;

	case 'server-stop':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		if(!is_numeric($serverid))
		{
			$error = "Server id je nepravilan!";
		}
		if(empty($serverid))
		{
			$error = "Server id je nepravilan!";
		}
		
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);		

		$klijent = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$client = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '{$server[user_id]}'");
		//$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$server['mod']."'");

		$stop = stop_server($boxip['ip'], $box['sshport'], $server['username'], $server['password'], $serverid, "admin", FALSE);

		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}		
		
		if($stop == "stopiran")
		{
			$poruka = "Stopirao <z>".$server['name']."</z> server";
			alog($klijent['id'], $poruka, $klijent['fname'].' '.$klijent['lname'], fuckcloudflare(), time());			

			$poruka = "<b>( Admin #{$klijent[fname]} )</b> Stopirao <z>".$server['name']."</z> server";
			klijent_log($client['klijentid'], $poruka, $client['ime'].' '.$client['prezime'], fuckcloudflare(), time());

			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Server je stopiran";
			$_SESSION['msg-type'] = 'success';
			
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}
		else
		{
			$error = $stop;
		}
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}
	break;
	case 'server-reinstall':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		if(!is_numeric($serverid))
		{
			$error = "Server id je nepravilan!";
		}
		if(empty($serverid))
		{
			$error = "Server id je nepravilan!";
		}
		
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);		
		
		$klijent = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$client = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '{$server[user_id]}'");
		//$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$server['mod']."'");
		
		$siframs = $aes->decrypt($box['password']);
		
		if($server['startovan'] == "1")
		{
			$error = "Servera mora biti stopiran.";
		}
		
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}		

		$stop = reinstall_server($boxip['ip'], $box['sshport'], $box['login'], $siframs, $serverid, "admin");
		
		if($stop == "reinstaliran")
		{
			$poruka = "Reinstalirao <z>".$server['name']."</z> server";
			alog($klijent['id'], $poruka, $klijent['fname'].' '.$klijent['lname'], fuckcloudflare(), time());			

			$poruka = "<b>( Admin #{$klijent[fname]} )</b> Reinstalirao <z>".$server['name']."</z> server";
			klijent_log($client['klijentid'], $poruka, $client['ime'].' '.$client['prezime'], fuckcloudflare(), time());

			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Server je reinstaliran";
			$_SESSION['msg-type'] = 'success';
			
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}
		else
		{
			$error = $stop;
		}
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}
	break;
	case 'server-restart':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		if(!is_numeric($serverid))
		{
			$error = "Server id je nepravilan!";
		}
		if(empty($serverid))
		{
			$error = "Server id je nepravilan!";
		}

		
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);		
		
		$klijent = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$client = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '{$server[user_id]}'");
		//$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$server['mod']."'");
		
		
		if($server['igra'] == "2")
		{
			$ftp = ftp_connect($boxip['ip'], $boxip['ftpport']);
			if (!$ftp) {
				echo $jezik['text121'];
				die();
			}
			if (ftp_login($ftp, $server["username"], $server["password"])){
			
				ftp_pasv($ftp, true);
				
				if (!empty($path)) {
					ftp_chdir($ftp, $path);
				} else ftp_chdir($ftp, './');
				
				
				$folder = 'cache_folder/panel_'.$server["username"].'_samp_server.cfg';
				$fajl = "ftp://$server[username]:$server[password]@$boxip[ip]:$boxip[ftpport]/server.cfg";
				$lines = file($fajl, FILE_IGNORE_NEW_LINES);
				
				$bind = false;
				$port = false;
				$maxplayers = false;
				
				foreach ($lines as &$line) {
					
					$val = explode(" ", $line);
					
					if ($val[0] == "port") {
						$val[1] = $server['port'];
						$line = implode(" ", $val);
						$port = true;
					}
					else if ($val[0] == "maxplayers") {
						$val[1] = $server['slotovi'];
						$line = implode(" ", $val);
						$maxplayers = true;
					}
					else if ($val[0] == "bind") {
						$val[1] = $boxip['ip'];
						$line = implode(" ", $val);
						$bind = true;
					}
				}
				unset($line);
				
				
				if (!$fw = fopen(''.$folder.'', 'w+')) 
				{
					echo $jezik['text131'];
				}
				foreach($lines as $line) {
					$fb = fwrite($fw,$line.PHP_EOL);
				}
				
				if (!$port) 
				{
					fwrite($fw,"port $server[port]".PHP_EOL);
				}
				if (!$maxplayers) 
				{
					fwrite($fw,"maxplayers $server[slotovi]".PHP_EOL);
				}
				if (!$bind) 
				{
					fwrite($fw,"bind $boxip[ip]".PHP_EOL);
				}
				
				$remote_file = ''.$path.'/server.cfg';
				if (!ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) 
				{
					echo $jezik['text131'];
				}
				fclose($fw);
				unlink($folder);
			}
			ftp_close($ftp);
		}		
		else if($server['igra'] == "3")
		{
			$ftp = ftp_connect($boxip['ip'], 21);
			if(!$ftp)
			{
				echo $jezik['text121'];
				die();
			}
				
			if (ftp_login($ftp, $server["username"], $server["password"]))
			{
				if(!empty($path))
				{
					ftp_chdir($ftp, $path);	
				} else ftp_chdir($ftp, './');	

				$folder = 'cache_folder/panel_'.$server["username"].'_server.properties';

				$fajl = "ftp://$server[username]:$server[password]@$boxip[ip]:21/server.properties";
				$lines = file($fajl, FILE_IGNORE_NEW_LINES);

				foreach($lines as &$line) {
				   $val = explode("=",$line);
				   if ($val[0]=="server-port") {
				      $val[1] = $server['port'];
				      $line = implode("=",$val);
				   }
				   else if ($val[0]=="query.port") {
				      $val[1] = $server['port'];
				      $line = implode("=",$val);
				   }
				   else if ($val[0]=="max-players") {
				      $val[1] = $server['slotovi'];
				      $line = implode("=",$val);
				   }
				   else if ($val[0]=="server-ip") {
				      $val[1] = $boxip['ip'];
				      $line = implode("=",$val);
				   }
				}
				unset($line);

				$fw = fopen(''.$folder.'', 'w+');
				foreach($lines as $line) {
				   $fb = fwrite($fw,$line.PHP_EOL);
				}				
				$file = "$fajl";
				$remote_file = ''.$path.'/server.properties';
				if (!ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) 
				{
					echo $jezik['text131'];
					die();
				}
				
				fclose($fw);

				unlink($folder);			
			}
			ftp_close($ftp);						
		}
		
		$stop = stop_server($boxip['ip'], $box['sshport'], $server['username'], $server['password'], $serverid, "admin", TRUE);
		$start = start_server($boxip['ip'], $box['sshport'], $server['username'], $server['password'], $serverid, "admin", TRUE);

		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}		
		
		if($stop == "stopiran" AND $start == "startovan")
		{	
			$poruka = "Restartovao <z>".$server['name']."</z> server";
			alog($klijent['id'], $poruka, $klijent['fname'].' '.$klijent['lname'], fuckcloudflare(), time());	

			$poruka = "<b>( Admin #{$klijent[fname]} )</b> Restartovao <z>".$server['name']."</z> server";
			klijent_log($client['klijentid'], $poruka, $client['ime'].' '.$client['prezime'], fuckcloudflare(), time());

			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Server je restartovan";
			$_SESSION['msg-type'] = 'success';
			
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}
		else
		{
			$error = $start;
		}
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}
	break;	

	case 'server-boost':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		$klijentid = $_SESSION['a_id'];
		
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$boxip = query_fetch_assoc("SELECT `ip` FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		
		$vremes = date("Y-m-d", time());
		$vremes = strtotime($vremes);	
		
		if($server['boost'] > $vremes)
		{
			$vreme = date("m.d, H:i", $server['boost']);
			$error = "Sledeći boost možete obaviti tek ".$vreme."";
		}
		if(query_numrows("SELECT `id` FROM `serveri` WHERE `id` = '".$serverid."' AND `user_id` = '".$klijentid."'") == 0)
		{
			$error = "Nemas pristup tom serveru!";
		}
		
		if($server['slotovi'] < 26)
		{
			$error = "Morate imati najmanje 26 slota da bi boostovali server!";
		}
		
		$kon = mysql_connect("193.183.98.164", "root", "sasavps") or die(mysql_error());
		$sel = mysql_select_db("t2") or die(mysql_error());
		
		if(mysql_num_rows(mysql_query("SELECT * FROM `t2`", $kon)) == 6) 
		{
			$zadnji = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `t2` ORDER BY `id` ASC LIMIT 1", $kon));	
			mysql_query("DELETE FROM `t2` WHERE `id` = '".$zadnji['id']."'", $kon);
		}

		mysql_query("INSERT INTO `t2`(`ipport`,`type`,`time`,`country`,`weekly`) VALUES('".$boxip['ip'].":".$server['port']."','cs', '".time()."', 'de', '0')", $kon);
		
		mysql_close($kon);
		unset($sel);
		unset($kon);
		
		$vreme = date("Y-m-d", time()+345600);
		$vreme = strtotime($vreme);
		
		query_basic("UPDATE `serveri` SET `boost` = '".$vreme."' WHERE `id` = '".$serverid."'");
		
		echo'uspesno';
	break;
		
	case'server-ftppw':
		$serverid = mysql_real_escape_string($_POST['serverid']);
			
		$sifra = randomSifra(8);
				
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		
		if (!function_exists("ssh2_connect")) { echo "SSH2 PHP extenzija nije instalirana."; die(); }

		if(!($con = ssh2_connect($boxip['ip'], $box['sshport']))) { echo "Ne mogu se spojiti na server."; die(); }
		else 
		{
			if(!ssh2_auth_password($con, $server['username'], $server['password'])) { echo "Netačni podatci za prijavu"; die(); }
			else 
			{			
			$cmd1 = 'passwd '.$server['username'];
			$cmd2 = $server['password'];
			$cmd3 = $sifra;
			$cmd4 = $sifra;

			$stream = ssh2_shell($con, 'xterm');
			fwrite( $stream, "$cmd1\n");
			sleep(1);
			fwrite( $stream, "$cmd2\n");
			sleep(1);
			fwrite( $stream, "$cmd3\n");
			sleep(1);
			fwrite( $stream, "$cmd4\n");
			sleep(1);
			
			$data = "";
			
			while($line = fgets($stream)) {
				$data .= $line;
				$pos = strpos($line, "successfully");	
				if($pos !== false){
					$promenjeno = "da";	
				}
			}
			
			if($promenjeno == "da")
			{
				query_basic("UPDATE `serveri` SET `password` = '".$sifra."' WHERE `id` = '".$server['id']."'");
				echo 'uspesno';
				die();
			}
			
			echo 'Dogodila se greska';
			die();
			}
		}
	break;
		
      case'rcon':
            $serverid = mysql_real_escape_string($_POST['serverid']);
            
            $rcon = mysql_real_escape_string($_POST['rcon']);
            $rcon = htmlspecialchars($rcon);
            
            if(empty($rcon)) { $error = 'Morate uneti rcon komandu.'; }
            
            if(empty($serverid)) { $error = 'Mora imati serverid upisan.'; }
            
            $server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
            $boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
            $box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
            $klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$_SESSION['klijentid']."'");
            
            if($server['startovan'] == "0") { $error = 'Server mora biti startovan.'; }
            
            if($server['igra'] == "1")
            {
                $rconpw = cscfg('rcon_password', $serverid);
                include '../includes/rcon_hl_net.inc';
                $M = new Rcon();
                
                $M->Connect($boxip['ip'], $server['port'], $rconpw);
                $M->RconCommand($rcon); 
                
                $poruka = mysql_real_escape_string("Poslao rcon komandu ( <z>{$rcon}</z> ) na <a href='gp-server.php?id={$server['id']}'><z>".$server['name']."</z></a> server");
                klijent_log($klijent['klijentid'], $poruka, $klijent['ime'].' '.$klijent['prezime'], fuckcloudflare(), time());     
                
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = 'Poslali ste komandu';
				$_SESSION['msg-type'] = "success";
				header("Location: srv-konzola.php?id=".$serverid);
                die();
            }
            else if($server['igra'] == "3")
            {
                //error_reporting(E_ALL);
                require '../inc/libs/SourceQuery/SourceQuery.class.php';

                $rcona = array(
                    "status"    => mccfg('enable-rcon', $serverid),
                    "password"  => mccfg('rcon.password', $serverid),
                    "port"      => mccfg('rcon.port', $serverid),
                );

                if($rcona["status"] == "true")
                {
                    define( 'SQ_SERVER_ADDR', $boxip['ip'] );
                    define( 'SQ_SERVER_PORT', $rcona['port'] );
                    define( 'SQ_TIMEOUT', 1 );
                    define( 'SQ_ENGINE', SourceQuery :: SOURCE );               

                    $Query = new SourceQuery( );
                    try
                    {
                        $Query->Connect( SQ_SERVER_ADDR, SQ_SERVER_PORT, SQ_TIMEOUT, SQ_ENGINE );
                        $Query->SetRconPassword( $rcona['password'] );
                        $Query->Rcon( $rcon );
						$_SESSION['msg1'] = "Uspešno";
						$_SESSION['msg2'] = 'Poslali ste komandu';
						$_SESSION['msg-type'] = "success";
						header("Location: srv-konzola.php?id=".$serverid);
                    }
                    catch( Exception $e )
                    {
                        $error = $e->getMessage( );
                    }
                    $Query->Disconnect( );
                } else $error = 'greska';
            } 
            else if ($server['igra'] == "8")
            {
            
            }
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = "error";
			header("Location: srv-konzola.php?id=".$serverid);
			die();
        break;
		
	case 'server-suspend':
		samo_vlasnik($_SESSION['a_id']);
		$serverid = mysql_real_escape_string($_POST['serverid']);

		if(!is_numeric($serverid))
		{
			$error = "Server id je nepravilan!";
		}
		if(empty($serverid))
		{
			$error = "Server id je nepravilan!";
		}
			
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);		

		$klijent = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$client = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '{$server[user_id]}'");
		//$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$server['mod']."'");
			
		if($server['startovan'] == "1")
		{
			$stop = stop_server($boxip['ip'], $box['sshport'], $server['username'], $server['password'], $serverid, "admin", FALSE);

			if($stop == "stopiran")
			{
				query_basic("UPDATE `serveri` SET `status` = 'Suspendovan' WHERE `id` = '".$serverid."'");
				$poruka = "Suspendovao <a href='srv-pocetna.php?id=".$server['id']."'><m>".$server['name']."</m></a> server";
				alog($klijent['id'], mysql_real_escape_string($poruka), $klijent['fname'].' '.$klijent['lname'], fuckcloudflare(), time());

				$poruka = "<b>( Admin #{$klijent[fname]} )</b> Suspendovao <a href='srv-pocetna.php?id=".$server['id']."'><m>".$server['name']."</m></a> server";
				klijent_log($client['klijentid'], $poruka, $client['ime'].' '.$client['prezime'], fuckcloudflare(), time());

				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = "Server je suspendovan";
				$_SESSION['msg-type'] = 'success';
						
				header("Location: srv-pocetna.php?id=".$serverid);
				die();				
			}
			else
			{
				$error = $stop;
			}
		}
		else
		{
			query_basic("UPDATE `serveri` SET `status` = 'Suspendovan' WHERE `id` = '".$serverid."'");

			$poruka = "<b>( Admin #{$klijent[fname]} )</b> Suspendovao <a href='srv-pocetna.php?id=".$server['id']."'><m>".$server['name']."</m></a> server";
			klijent_log($client['klijentid'], $poruka, $client['ime'].' '.$client['prezime'], fuckcloudflare(), time());

			$poruka = "Suspendovao <a href='srv-pocetna.php?id=".$server['id']."'><m>".$server['name']."</m></a> server";
			alog($klijent['id'], mysql_real_escape_string($poruka), $klijent['fname'].' '.$klijent['lname'], fuckcloudflare(), time());				
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Server je suspendovan";
			$_SESSION['msg-type'] = 'success';
					
			header("Location: srv-pocetna.php?id=".$serverid);
			die();	
		}
			
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}			
	break;
		
	case 'server-unsuspend':
		samo_vlasnik($_SESSION['a_id']);
		$serverid = mysql_real_escape_string($_POST['serverid']);

		if(!is_numeric($serverid))
		{
			$error = "Server id je nepravilan!";
		}
		if(empty($serverid))
		{
			$error = "Server id je nepravilan!";
		}
			
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}		

		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$klijent = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
		$client = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '{$server[user_id]}'");
			
		query_basic("UPDATE `serveri` SET `status` = 'Aktivan' WHERE `id` = '".$serverid."'");

		$poruka = "<b>( Admin #{$klijent[fname]} )</b> Aktivirao <a href=\"srv-pocetna.php?id=$server[id]\"><m>".$server['name']."</m></a> server";
		klijent_log($client['klijentid'], $poruka, $client['ime'].' '.$client['prezime'], fuckcloudflare(), time());

		$poruka = "Aktivirao <a href=\"srv-pocetna.php?id=$server[id]\"><m>".$server['name']."</m></a> server";
		alog($klijent['id'], mysql_real_escape_string($poruka), $klijent['fname'].' '.$klijent['lname'], fuckcloudflare(), time());				
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Server je sada aktivan";
		$_SESSION['msg-type'] = 'success';
					
		header("Location: srv-pocetna.php?id=".$serverid);
		die();			
	break;
		
	case 'server-delete':
		samo_vlasnik($_SESSION['a_id']);
		$serverid = mysql_real_escape_string($_POST['serverid']);

		if(!is_numeric($serverid))
		{
			$error = "Server id je nepravilan!";
		}
		if(empty($serverid))
		{
			$error = "Server id je nepravilan!";
		}
		
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);		
		
		$klijent = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$client = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '{$server[user_id]}'");
		//$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$server['mod']."'");
		
		if($server['startovan'] == "1") $error = "Server mora biti stopiran da bi ga izbrisali.";
		else $delete = server_izbrisi($serverid, "admin", $boxip['ip'], $box['sshport'], $box['login'], $aes->decrypt($box['password']));

		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}		
		
		if($delete == "uspesno")
		{	
			$poruka = "Izbrisao <a href=\"srv-pocetna.php?id=$server[id]\"><m>".$server['name']."</m></a> server";
			alog($klijent['id'], $poruka, $klijent['fname'].' '.$klijent['lname'], fuckcloudflare(), time());
			
			$poruka = "<b>( Admin #{$klijent[fname]} )</b> Izbrisao <a href=\"gp-serveri.php\"><m>".$server['name']."</m></a> server";
			klijent_log($client['klijentid'], $poruka, $client['ime'].' '.$client['prezime'], fuckcloudflare(), time());

			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Server je izbrisan";
			$_SESSION['msg-type'] = 'success';

			header("Location: index.php");
			die();
		}
		else
		{
			$error = $delete;
		}
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}	
	break;
	
	case 'srv-podesavanja':
		samo_vlasnik($_SESSION['a_id']);
		$serverid = mysql_real_escape_string($_POST['id']);
		$masina = mysql_real_escape_string($_POST['masina']);
		$ipid = mysql_real_escape_string($_POST['ip']);
		$mod = mysql_real_escape_string($_POST['mod']);
		$slotovi = mysql_real_escape_string($_POST['slotovi']);
		$ime = sqli($_POST['ime']);
		$map = sqli($_POST['map']);
		$port = sqli($_POST['port']);
		$password = sqli($_POST['password']);
		$istice = sqli($_POST['istice']);
		$fps = sqli($_POST['fps']);
		$komanda = sqli($_POST['komanda']);
		$free = sqli($_POST['free']);
		
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		
		if(!is_numeric($port) && $server['igra'] != "7"){ $error .= "Port mora biti u brojnom formatu"; }
		if(!is_numeric($slotovi) && $server['igra'] != "7"){ $error .= "Slotovi moraju biti u brojnom formatu"; }
		if(!is_numeric($ipid)){ $error .= "Ip id mora biti u brojnom formatu"; header("Location: naruci-instaliraj.php"); die(); }
		if(!is_numeric($masina)){ $error .= "Box id mora biti u brojnom formatu"; header("Location: naruci-instaliraj.php"); die(); }
		if(!is_numeric($serverid)){ $error .= "Server id mora biti u brojnom formatu"; }
		
		$istice = explode("/", $istice);
		$istice = $istice['2'].'-'.$istice['0'].'-'.$istice['1'];
	
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '{$serverid}'");
		
		if(strlen($ime) > 24) $error .= "Ime servera mora biti manje od 24 karaktera.";
		if(strlen($ime) < 4) $error .= "Ime servera mora biti vece od 4 karaktera.";
		
		if($server['igra'] == "1") if(strlen($port) != 5) $error .= "Port za cs 1.6 servere mora sadrzati 5 broja.";
		else if($server['igra'] == "2") if(strlen($port) != 4) $error .= "Port za samp servere mora sadrzati 4 broja.";
		else if($server['igra'] == "3") if(strlen($port) != 5) $error .= "Port za mc servere mora sadrzati 5 broja.";
		
		if($fps > 333) $error .= "Max fps moze biti 333";
			
		if($port != $server['port'])
		{
			if(query_numrows("SELECT `port` FROM `serveri` WHERE `port` = '".$port."'") == 1)
			{ 
				$error .= "Taj port je vec u upotrebi, javite administratorima za ovu gresku!"; 
			}
		}
		
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		
		if (!function_exists("ssh2_connect")) { $error .= "SSH2 PHP extenzija nije instalirana."; }
		
		if(!($con = ssh2_connect($boxip['ip'], $box['sshport']))) { $error .= "Ne mogu se spojiti na server."; }
		
		query_basic("UPDATE `serveri` SET 
			`password` = '{$password}',
			`box_id` = '{$masina}',
			`ip_id` = '{$ipid}',
			`name` = '{$ime}',
			`mod` = '{$mod}',
			`map` = '{$map}',
			`port` = '{$port}',
			`fps` = '{$fps}',
			`slotovi` = '{$slotovi}',
			`istice` = '{$istice}',
			`free` = '{$free}',
			`komanda` = '{$komanda}' WHERE `id` = '{$server['id']}'");
		
		query_basic( "UPDATE `lgsl` SET
			`ip` = '".$boxip['ip']."',
			`c_port` = '".$port."',
			`q_port` = '".$port."',
			`comment` = '".$ime."' WHERE `id` = '{$server['id']}'" );						
		
		$_SESSION['msg-type'] = "success";
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Promenili ste podatke servera";
		header("Location: srv-pocetna.php?id={$serverid}");
		die();
		
		if(!empty($error))
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			header("Location: srv-podesavanja.php?id={$serverid}&masina={$server['box_id']}&ip={$server['ip_id']}");
			die();
		}		
		
	break;
	
	case 'server-add':
		samo_vlasnik($_SESSION['a_id']);
		$igra = mysql_real_escape_string($_POST['igra']);
		$igra = htmlspecialchars($igra);
		
		$istice = mysql_real_escape_string($_POST['istice']);
		$istice = htmlspecialchars($istice);
		
		$klijentid = mysql_real_escape_string($_POST['klijentid']);
		$klijentid = htmlspecialchars($klijentid);
		
		$imeservera = mysql_real_escape_string($_POST['ime']);
		$imeservera = htmlspecialchars($imeservera);
		
		$mod = mysql_real_escape_string($_POST['mod']);
		$mod = htmlspecialchars($mod);
		
		$ipid = mysql_real_escape_string($_POST['ipid']);
		$ipid = htmlspecialchars($ipid);
		
		$boxid = mysql_real_escape_string($_POST['boxid']);
		$boxid = htmlspecialchars($boxid);
		
		$username = mysql_real_escape_string($_POST['username']);
		$username = htmlspecialchars($username);
		
		if(empty($username))
		{
			$provera_username = query_numrows("SELECT `id` FROM `serveri` WHERE `user_id` = '".$klijentid."'");  
			$server_br = $provera_username+1;
			$username = 'server_'.$klijentid.'_'.$server_br.'';			
		}
		
		$sifra = mysql_real_escape_string($_POST['password']);
		$sifra = htmlspecialchars($sifra);
		
		$slotovi = mysql_real_escape_string($_POST['slotovi']);
		$slotovi = htmlspecialchars($slotovi);
		
		if($igra == "1") {
			$port = mysql_real_escape_string($_POST['portcs']);
			$port = htmlspecialchars($port);
		} else if ($igra == "2") {
			$port = mysql_real_escape_string($_POST['portsamp']);
			$port = htmlspecialchars($port);
		} else if ($igra == "3") {
			$port = mysql_real_escape_string($_POST['portmc']);
			$port = htmlspecialchars($port);
		} else if ($igra == "7") {
			$port = "0000";
			$mod = "6";
			$slotovi = "1";
		} else if ($igra == "9") {
			$port = mysql_real_escape_string($_POST['portfivem']);
			$mod = "28";
			$port = htmlspecialchars($port);
		} else if ($igra == "6") {
			$port = mysql_real_escape_string($_POST['portts']);
			$port = htmlspecialchars($port);
		} else {
			$port = mysql_real_escape_string($_POST['port']);
			$port = htmlspecialchars($port);
		}
		
		if(empty($imeservera)) $error = "Morate uneti ime servera.";
		if(($mod == 0)) $error = "Morate izabrati mod servera.";
		if(empty($istice)) $error = "Morate odrediti dokle da traje server.";
		if(empty($port)) $error = "Morate uneti port servera.";
		if($slotovi == 0) $error = "Morate izabrati slotove.";
		if($igra == 0) $error = "Morate izabrati igru.";
		
		if((!is_numeric($port))) $error = "Port mora biti u brojnom formatu";
		if(!is_numeric($slotovi)) $error = "Slotovi moraju biti u brojnom formatu";
		if(!is_numeric($igra)) $error = "Igra mora biti u brojnom formatu";
		if(!is_numeric($klijentid)) $error = "ID klijenta mora biti u brojnom formatu";
		if(!is_numeric($ipid)) $error = "Ip id mora biti u brojnom formatu";
		if(!is_numeric($boxid)) $error = "Box id mora biti u brojnom formatu";
		
		if((query_numrows("SELECT `port` FROM `serveri` WHERE `port` = '".$port."' AND `ip_id` = '{$ipid}'") == 1) && $igra != 7) $error = "Taj port je vec u upotrebi, javite administratorima za ovu gresku!"; 
		
		// Default mapa ---------------------------------------------------------------------------------------------  
		$mapa = query_fetch_assoc("SELECT `mapa` FROM `modovi` WHERE `id` = '".$mod."'");
		$mapa = $mapa['mapa'];
		
		// Datum isteka ---------------------------------------------------------------------------------------------  
		$datum = explode("/", $istice);
		
		$dan = $datum[1];
		$mesec = $datum[0];
		$godina = $datum[2];
		
		$istice = $godina."-".$mesec."-".$dan;
		
		// Default komanda ---------------------------------------------------------------------------------------------  
		$komanda = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$mod."'");
		$komandaa = $komanda['komanda'];

		// Query --------------------------------------------------------------------------------------------- 
		$ipi = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$ipid."'");
		$boxi = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$boxid."'");
		$modi = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$mod."'");
		
		if($igra == "7" && ($boxi['fdl']== "0")) {
			$error = "Ovaj BOX nije napravljen za FDL!!!";
		}
		
		require_once("./assets/libs/phpseclib/Crypt/AES.php");
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);		
		
		$masina_pw = $aes->decrypt($boxi['password']);

		$ipetx = $ipi['ip'];
		//$ipsa = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ip` = '{$boxi[ip]}'");
		//if($igra == "2") { $ipid = $ipsa['ipid']; $ipetx = $boxi['ip']; }
		
		if(!empty($error))
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;		
			header("Location: serveradd.php");
			die();
		}
		else
		{
		    if($igra != 6) {
			$ssh_dodavanje = ssh_dodaj_server($ipi['ip'], $boxi['sshport'], $boxi['login'], $masina_pw, $username, $sifra, $mod);
		    }  else {
        require_once($_SERVER['DOCUMENT_ROOT'].'/admin/libraries/TeamSpeak3/TeamSpeak3.php');
        $connect = "serverquery://serveradmin:".tssrvadminpw($ipi['ip'])."@".$ipi['ip'].":10011";
    		$ts3 = TeamSpeak3::factory($connect);
        $array = [
            "virtualserver_name" => $imeservera,
            "virtualserver_maxclients" => $slot,
            "virtualserver_port" => $port,
            "virtualserver_autostart" => "1"
        ];
        if($ts3->serverCreate($array))
        {
              $ts_dodavanje=true;
        }
        }
			if($ssh_dodavanje == "uspesno")
			{
				$s = explode("|", $modi['cena']);
				$slotcena = $s[0];
				$cena = ($s[1]*$slotovi)."";	
				$aid = $_SESSION['a_id'];
				query_basic("INSERT INTO `serveri` SET
					`user_id` = '".$klijentid."',
					`box_id` = '".$boxid."',
					`ip_id` = '".$ipid."',
					`name` = '".$imeservera."',
					`mod` = '".$mod."',
					`map` = '".$mapa."',
					`port` = '".$port."',
					`fps` = '333',
					`slotovi` = '".$slotovi."',
					`username` = '".$username."',
					`password` = '".$sifra."',
					`istice` = '".$istice."',
					`status` = 'Aktivan',
					`startovan` = '0',
					`free` = 'Ne',
					`cena` = '".$cena."',
					`komanda` = '".$komandaa."',
					`igra` = '".$igra."',
					`aid` = '".$aid."'");
				
				$serverid = mysql_insert_id();
				
				query_basic("DELETE FROM `lgsl` WHERE `id` = '".$serverid."'");
				
				if($igra == "1") $querytype = "halflife";
				else if($igra == "2") $querytype = "samp";
				else if($igra == "3") $querytype = "minecraft";
				else if($igra=="6") $querytype = "ts3";

				if ($igra != "7") {
					query_basic( "INSERT INTO `lgsl` SET
						`id` = '".$serverid."',
						`type` = '".$querytype."',
						`ip` = '".$ipetx."',
						`c_port` = '".$port."',
						`q_port` = '".$port."',
						`s_port` = '0',
						`zone` = '0',
						`disabled` = '0',
						`comment` = '".$imeservera."',
						`status` = '0',
						`cache` = '',
						`cache_time` = ''"
					);			
				}
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = "Uspešno ste instalirali server.";
				header("Location: srv-pocetna.php?id={$serverid}");
				die();		
			} else if($ts_dodavanje ==true) {
			    				$s = explode("|", $modi['cena']);
				$slotcena = $s[0];
				$cena = ($s[1]*$slotovi)."";	
				$aid = $_SESSION['a_id'];
				query_basic("INSERT INTO `serveri` SET
					`user_id` = '".$klijentid."',
					`box_id` = '".$boxid."',
					`ip_id` = '".$ipid."',
					`name` = '".$imeservera."',
					`mod` = '".$mod."',
					`map` = 'TeamSpeak3',
					`port` = '".$port."',
					`fps` = '333',
					`slotovi` = '".$slotovi."',
					`username` = 'serveradmin',
					`password` = '".tssrvadminpw($ipi['ip'])."',
					`istice` = '".$istice."',
					`status` = 'Aktivan',
					`startovan` = '0',
					`free` = 'Ne',
					`cena` = '".$cena."',
					`komanda` = '".$komandaa."',
					`igra` = '6',
					`aid` = '".$aid."'");
				
				$serverid = mysql_insert_id();
				
				query_basic("DELETE FROM `lgsl` WHERE `id` = '".$serverid."'");
				
				if($igra == "1") $querytype = "halflife";
				else if($igra == "2") $querytype = "samp";
				else if($igra == "3") $querytype = "minecraft";
				else if($igra=="6") $querytype = "ts3";

				if ($igra != "7") {
					query_basic( "INSERT INTO `lgsl` SET
						`id` = '".$serverid."',
						`type` = '".$querytype."',
						`ip` = '".$ipetx."',
						`c_port` = '".$port."',
						`q_port` = '10011',
						`s_port` = '0',
						`zone` = '0',
						`disabled` = '0',
						`comment` = '".$imeservera."',
						`status` = '0',
						`cache` = '',
						`cache_time` = ''"
					);			
				}
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = "Uspešno ste instalirali server.";
				header("Location: srv-pocetna.php?id={$serverid}");
				die();
			}
			else
			{
				$error = $ssh_dodavanje;	
			}
		}
		
		if(!empty($error))
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;		
			header("Location: serveradd.php");
			die();
		}
	break;
	
	case 'server-add-backup':
		samo_vlasnik($_SESSION['a_id']);
		$igra = mysql_real_escape_string($_POST['igra']);
		$igra = htmlspecialchars($igra);
		
		if($igra == "7")

		$slotovi = mysql_real_escape_string($_POST['slotovi']);
		$slotovi = htmlspecialchars($slotovi);
	
		if($igra == "1") {
			$port = mysql_real_escape_string($_POST['portcs']);
			$port = htmlspecialchars($port);
		} else if ($igra == "2") {
			$port = mysql_real_escape_string($_POST['portsamp']);
			$port = htmlspecialchars($port);
		} else if ($igra == "3") {
			$port = mysql_real_escape_string($_POST['portmc']);
			$port = htmlspecialchars($port);
		} else {
			$port = mysql_real_escape_string($_POST['port']);
			$port = htmlspecialchars($port);
		}
			
		$istice = mysql_real_escape_string($_POST['istice']);
		$istice = htmlspecialchars($istice);
		
		$klijentid = mysql_real_escape_string($_POST['klijentid']);
		$klijentid = htmlspecialchars($klijentid);
		
		$imeservera = mysql_real_escape_string($_POST['ime']);
		$imeservera = htmlspecialchars($imeservera);

		$mod = mysql_real_escape_string($_POST['mod']);
		$mod = htmlspecialchars($mod);

		$ipid = mysql_real_escape_string($_POST['ipid']);
		$ipid = htmlspecialchars($ipid);

		$boxid = mysql_real_escape_string($_POST['boxid']);
		$boxid = htmlspecialchars($boxid);

		$username = mysql_real_escape_string($_POST['username']);
		$username = htmlspecialchars($username);
		
		if(empty($username))
		{
			$provera_username = query_numrows("SELECT `id` FROM `serveri` WHERE `user_id` = '".$klijentid."'");  
			$server_br = $provera_username+1;
			$username = 'server_'.$klijentid.'_'.$server_br.'';			
		}
		
		$sifra = mysql_real_escape_string($_POST['password']);
		$sifra = htmlspecialchars($sifra);
		
		if(empty($imeservera)) $error = "Morate uneti ime servera.";
		if(($mod == 0) && $igra != 7) $error = "Morate izabrati mod servera.";
		if(empty($istice)) $error = "Morate odrediti dokle da traje server.";
		if(empty($port) && $igra != 7) $error = "Morate uneti port servera.";
		if($slotovi == 0) $error = "Morate izabrati slotove.";
		if($igra == 0) $error = "Morate izabrati igru.";
		
		if((!is_numeric($port)) && $igra != 7) $error = "Port mora biti u brojnom formatu";
		if(!is_numeric($slotovi)) $error = "Slotovi moraju biti u brojnom formatu";
		if(!is_numeric($igra)) $error = "Igra mora biti u brojnom formatu";
		if(!is_numeric($klijentid)) $error = "ID klijenta mora biti u brojnom formatu";
		if(!is_numeric($ipid)) $error = "Ip id mora biti u brojnom formatu";
		if(!is_numeric($boxid)) $error = "Box id mora biti u brojnom formatu";
		
		if($igra == 7) {
			$port = "0000";
			$mod = "6";
		}
		if((query_numrows("SELECT `port` FROM `serveri` WHERE `port` = '".$port."' AND `ip_id` = '{$ipid}'") == 1) && $igra != 7) $error = "Taj port je vec u upotrebi, javite administratorima za ovu gresku!"; 
		
		// Default mapa ---------------------------------------------------------------------------------------------  
		$mapa = query_fetch_assoc("SELECT `mapa` FROM `modovi` WHERE `id` = '".$mod."'");
		$mapa = $mapa['mapa'];
		
		
		// Datum isteka ---------------------------------------------------------------------------------------------  
		$datum = explode("/", $istice);
		
		$dan = $datum[1];
		$mesec = $datum[0];
		$godina = $datum[2];
		
		$istice = $godina."-".$mesec."-".$dan;
		
		// Default komanda ---------------------------------------------------------------------------------------------  
		$komanda = query_fetch_assoc("SELECT `komanda`, `putanja` FROM `modovi` WHERE `id` = '".$mod."'");
		$komandaa = $komanda['komanda'];

		// Query --------------------------------------------------------------------------------------------- 
		$ipi = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$ipid."'");
		$boxi = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$boxid."'");
		$modi = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$mod."'");
		
		require_once("./assets/libs/phpseclib/Crypt/AES.php");
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);		
		
		$masina_pw = $aes->decrypt($boxi['password']);

		$ipetx = $ipi['ip'];
		$ipsa = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ip` = '{$boxi[ip]}'");
		if($igra == "2") { $ipid = $ipsa['ipid']; $ipetx = $boxi['ip']; }
		
		if(!empty($error))
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;		
			header("Location: serveradd.php");
			die();
		}
		else
		{
		
			$ssh_dodavanje = ssh_dodaj_server($ipi['ip'], $boxi['sshport'], $boxi['login'], $masina_pw, $username, $sifra, $mod);
			
			if($ssh_dodavanje == "uspesno")
			{
				$s = explode("|", $modi['cena']);
				$slotcena = $s[0];
				$cena = ($s[1]*$slotovi)."";	
				$aid = $_SESSION['a_id'];
				query_basic("INSERT INTO `serveri` SET
					`user_id` = '".$klijentid."',
					`box_id` = '".$boxid."',
					`ip_id` = '".$ipid."',
					`name` = '".$imeservera."',
					`mod` = '".$mod."',
					`map` = '".$mapa."',
					`port` = '".$port."',
					`fps` = '333',
					`slotovi` = '".$slotovi."',
					`username` = '".$username."',
					`password` = '".$sifra."',
					`istice` = '".$istice."',
					`status` = 'Aktivan',
					`startovan` = '0',
					`free` = 'Ne',
					`cena` = '".$cena."',
					`komanda` = '".$komandaa."',
					`igra` = '".$igra."',
					`aid` = '".$aid."'");
				
				$serverid = mysql_insert_id();
				
				query_basic("DELETE FROM `lgsl` WHERE `id` = '".$serverid."'");
				
				if($igra == "1") $querytype = "halflife";
				else if($igra == "2") $querytype = "samp";
				else if($igra == "3") $querytype = "minecraft";
				else if($igra == "9") $querytype = "fivem";

				
				query_basic( "INSERT INTO `lgsl` SET
					`id` = '".$serverid."',
					`type` = '".$querytype."',
					`ip` = '".$ipetx."',
					`c_port` = '".$port."',
					`q_port` = '".$port."',
					`s_port` = '0',
					`zone` = '0',
					`disabled` = '0',
					`comment` = '".$imeservera."',
					`status` = '0',
					`cache` = '',
					`cache_time` = ''" );			
				
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = "Uspešno ste instalirali server.";
				header("Location: srv-pocetna.php?id={$serverid}");
				die();		
			}
			else
			{
				$error = $ssh_dodavanje;	
			}
		}
		
		if(!empty($error))
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;		
			header("Location: serveradd.php");
			die();
		}
	break;	

	case 'prebacisrv':
		samo_vlasnik($_SESSION['a_id']);
		$serverid = mysql_real_escape_string($_POST['serverid']);
		$serverid = htmlspecialchars($serverid);

		$email = mysql_real_escape_string($_POST['email']);
		$email = htmlspecialchars($email);

		if(query_numrows("SELECT * FROM `klijenti` WHERE `email` = '{$email}'") == 1)
		{
			$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `email` = '{$email}'");
			query_basic("UPDATE `serveri` SET `user_id` = '$klijent[klijentid]' WHERE `id` = '$serverid'");

			$_SESSION['msg-type'] = "success";
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Prebacili ste server korisniku <z>$klijent[ime] $klijent[prezime]</z>";		
			header("Location: srv-pocetna.php?id=".$serverid);
			die();			
		}
		else
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Ne postoji klijent sa emailom: <z>$email</z>";		
			header("Location: srv-pocetna.php?id=".$serverid);
			die();				
		}
	break;
	
	case 'add_update_user':
		samo_vlasnik($_SESSION['a_id']);

		$serverid = mysql_real_escape_string($_POST['serverid']);
		$serverid = htmlspecialchars($serverid);

		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '{$serverid}'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '{$server[box_id]}'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `boxid` = '{$server[box_id]}'");

		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);
		
		$boxpw = $aes->decrypt($box['password']);

		if (!function_exists("ssh2_connect")) $error = $jezik['text290'];
		
		if(!($con = ssh2_connect("$boxip[ip]", "$box[sshport]"))) $error = $jezik['text292'];
		else 
		{
			if(!ssh2_auth_password($con, "$box[login]", "$boxpw")) $error = $jezik['text293'];
			else 
			{
				$cmd1 = "useradd -s /bin/bash $server[username]";
				$cmd2 = "passwd $server[username]";
				$cmd3 = $server['password'];
				$cmd4 = $server['password'];
				
				$stream = ssh2_shell($con, 'xterm');
				fwrite( $stream, "$cmd1\n");
				sleep(1);
				fwrite( $stream, "$cmd2\n");
				sleep(1);
				fwrite( $stream, "$cmd3\n");
				sleep(1);
				fwrite( $stream, "$cmd4\n");
				sleep(1);

				$data = "";
				
				while($line = fgets($stream)) 
				{
					$data .= $line;
				}	
				
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = "Update ste FTP Usera <z>$server[username]</z>";		
				header("Location: srv-pocetna.php?id=".$serverid);
				die();	
			}
		}

		$_SESSION['msg-type'] = "error";
		$_SESSION['msg1'] = "Greška";
		$_SESSION['msg2'] = $error;		
		header("Location: srv-pocetna.php?id=".$serverid);
		die();	

	break;
	
	case 'updateuser':
		samo_vlasnik($_SESSION['a_id']);

		$serverid = mysql_real_escape_string($_POST['serverid']);
		$serverid = htmlspecialchars($serverid);

		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '{$serverid}'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '{$server[box_id]}'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `boxid` = '{$server[box_id]}'");
		
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);
		
		$boxpw = $aes->decrypt($box['password']);

		if (!function_exists("ssh2_connect")) $error = $jezik['text290'];
		
		if(!($con = ssh2_connect("$boxip[ip]", "$box[sshport]"))) $error = $jezik['text292'];
		else 
		{
			if(!ssh2_auth_password($con, "$box[login]", "$boxpw")) $error = $jezik['text293'];
			else 
			{
				$cmd1 = "useradd -s /bin/bash $server[username]";
				$cmd2 = "passwd $server[username]";
				$cmd3 = $server['password'];
				$cmd4 = $server['password'];
				
				$stream = ssh2_shell($con, 'xterm');
				fwrite( $stream, "$cmd1\n");
				sleep(1);
				fwrite( $stream, "$cmd2\n");
				sleep(1);
				fwrite( $stream, "$cmd3\n");
				sleep(1);
				fwrite( $stream, "$cmd4\n");
				sleep(1);

				$data = "";
				
				while($line = fgets($stream)) 
				{
					$data .= $line;
				}	
				
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = "Update ste FTP Usera <z>$server[username]</z>";		
				header("Location: srv-pocetna.php?id=".$serverid);
				die();	
			}
		}

		$_SESSION['msg-type'] = "error";
		$_SESSION['msg1'] = "Greška";
		$_SESSION['msg2'] = $error;		
		header("Location: srv-pocetna.php?id=".$serverid);
		die();	

	break;
	
	case 'chown':
		samo_vlasnik($_SESSION['a_id']);
		
		$serverid = mysql_real_escape_string($_POST['serverid']);
		$serverid = htmlspecialchars($serverid);
		
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '{$serverid}'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '{$server[box_id]}'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `boxid` = '{$server[box_id]}'");
		
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);
		
		$boxpw = $aes->decrypt($box['password']);
		
		if (!function_exists("ssh2_connect")) {
			$error = $jezik['text290'];
		}
		
		if(!($con = ssh2_connect("$boxip[ip]", "$box[sshport]"))) {
			$error = $jezik['text292'];
		} else {
			if(!ssh2_auth_password($con, "$box[login]", "$boxpw")) {
				$error = $jezik['text293'];
			} else {
				$stream = ssh2_shell($con, 'xterm');
				fwrite( $stream, "chown -Rf $server[username]:$server[username] /home/$server[username]\n");
				sleep(1);
				fwrite( $stream, "chmod -R 755 /home/$server[username]/*\n");
				sleep(1);
				
				$data = "";
				
				while($line = fgets($stream)) {
					$data .= $line;
				}
				
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = "Chown ste FTP Usera <z>$server[username]</z>";
				header("Location: srv-pocetna.php?id=".$serverid);
				die();
			}
		}
		
		$_SESSION['msg-type'] = "error";
		$_SESSION['msg1'] = "Greška";
		$_SESSION['msg2'] = $error;
		header("Location: srv-pocetna.php?id=".$serverid);
		die();
		
	break;

	case'promena-moda':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		$mod = mysql_real_escape_string($_POST['modid']);
		
		if(!is_numeric($mod)) { $_SESSION['msg1'] = "Greška"; $_SESSION['msg-type'] = "error"; $_SESSION['msg2'] = "Mod ID mora biti u brojevnom formatu."; header("Location: srv-modovi.php?id=".$serverid); die(); }
		
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		
		if($server['mod'] == $mod)
		{
			$_SESSION['msg1'] = "Greška"; $_SESSION['msg-type'] = "error"; $_SESSION['msg2'] = "Taj mod vec imate na serveru.";
			header("Location: srv-modovi.php?id=".$serverid);
			die();
		}
		
		if($server['startovan'] == "1")
		{
			$_SESSION['msg1'] = "Greška"; $_SESSION['msg-type'] = "error"; $_SESSION['msg2'] = "Server mora biti stopiran.";
			header("Location: srv-modovi.php?id=".$serverid);
			die();
		}
		
		if($server['status'] == "Suspendovan")
		{
			$_SESSION['msg1'] = "Greška"; $_SESSION['msg-type'] = "error"; $_SESSION['msg2'] = "Server vam je suspendovan i nemate pristup ovoj komandi.";
			header("Location: srv-modovi.php?id=".$serverid);	
			die();
		}

		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);		
		
		$klijent = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$modrow = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$mod."' AND `sakriven` = '0'");
		$siframs = $aes->decrypt($box['password']);

		$stop = server_mod($boxip['ip'], $box['sshport'], $box['login'], $siframs, $serverid, $mod, 'admin');
		
		if($stop == "instaliran")
		{
			query_basic("UPDATE `serveri` SET `map` = '{$modrow['mapa']}' WHERE `id` = '{$serverid}'");			
			$poruka = mysql_real_escape_string("Instalirao novi mod <a href='srv-pocetna.php?id={$server['id']}'><z>".$server['name']."</z></a> server");
			alog($klijent['id'], $poruka, $klijent['fname'].' '.$klijent['lname'], fuckcloudflare(), time());	
			query_basic("UPDATE `serveri` SET `mod` = '".$mod."' WHERE `id` = '".$serverid."'");
			
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Uspešno ste promenili vaš mod na serveru.";
			$_SESSION['msg-type'] = "success";
			header("Location: srv-modovi.php?id=".$serverid);
			die();
		}
		else
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $stop;
			$_SESSION['msg-type'] = "error";
			header("Location: srv-modovi.php?id=".$serverid);
			die();
		}			
		
		
	break;

	case 'plugin-add':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		$id = mysql_real_escape_string($_POST['id']);
			
		if(empty($serverid)) $greska = "SERVER ID mora biti unet!";
		if(empty($id)) $greska = "PLUGIN ID mora biti unet!";

		$plugin = query_fetch_assoc("SELECT * FROM `plugins` WHERE `id` = '{$id}'");
			
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		$klijent = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
			
		$ftp = ftp_connect($boxip['ip'], 21);
		if(!$ftp)
		{
			$greska = "Ne mogu se konektovati na FTP servera!";
		}
			
		if (ftp_login($ftp, $server["username"], $server["password"]))
		{
			ftp_pasv($ftp, true);
			ftp_chdir($ftp, "/cstrike/addons/amxmodx/configs");
				
			$folder = 'cache_folder/panel_'.$server["username"].'_'.$plugin['prikaz'];

			$fw = fopen(''.$folder.'', 'w+');
			if(!$fw){
				echo "Ne mogu otvoriti fajl";	
				die();
			} 
			else 
			{
								
				$fb = fwrite($fw, stripslashes($plugin['text']));
				if(!$fb)
				{
					$greska = "Ne mogu dodati plugin.";
				} 
				else 
				{				
					$remote_file = '/cstrike/addons/amxmodx/configs/'.$plugin['prikaz'];
					if (ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) 
					{
						$_SESSION['msg2'] = "Uspešno";
						$_SESSION['msg1'] = "Plugin je instaliran.";
						$_SESSION['msg-type'] = "success";
							
						$poruka = ("Instalirao plugin ( <z>{$plugin['ime']}</z> ) na <a href='srv-pocetna.php?id={$server['id']}'><z>".$server['name']."</z></a> server");
						alog($klijent['id'], $poruka, $klijent['fname'].' '.$klijent['lname'], fuckcloudflare(), time());					
											
						header("Location: srv-plugini.php?id=".$serverid);
						die();
					} 
					else 
					{
						$greska = "Dogodila se greška prilikom dodavanja plugina.";
					}
					unlink($folder);								
				}
			}
		}
		else
		{
			$greska = 'Podaci za konektovanje na FTP nisu tacni.';
		}
		ftp_close($ftp);	
			
		if(!empty($greska))
		{
			$_SESSION['msg2'] = "Greška";
			$_SESSION['msg1'] = $greska;
			$_SESSION['msg-type'] = "error";
			header("Location: srv-plugini.php?id=".$serverid);
			die();
		}	
	break;
		
	case 'plugin-remove':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		$id = mysql_real_escape_string($_POST['id']);
		
		if(empty($serverid)) $greska = "SERVER ID mora biti unet!";
		if(empty($id)) $greska = "PLUGIN ID mora biti unet!";

		$plugin = query_fetch_assoc("SELECT * FROM `plugins` WHERE `id` = '{$id}'");
			
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		$klijent = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
		
		$ftp = ftp_connect($boxip['ip'], 21);
		if(!$ftp)
		{
			$greska = "Ne mogu se konektovati na FTP servera!";
		}
			
		if (ftp_login($ftp, $server["username"], $server["password"]))
		{
			ftp_pasv($ftp, true);
			ftp_chdir($ftp, "/cstrike/addons/amxmodx/configs");
			
			ftp_delete ($ftp, $plugin['prikaz']);

			$poruka = ("Izbrisao plugin ( <z>{$plugin['ime']}</z> ) na <a href='srv-pocetna.php?id={$server['id']}'><z>".$server['name']."</z></a> server");
			alog($klijent['id'], $poruka, $klijent['fname'].' '.$klijent['lname'], fuckcloudflare(), time());		

			$_SESSION['msg2'] = "Uspešno";
			$_SESSION['msg1'] = "Plugin je izbrisan.";
			$_SESSION['msg-type'] = "success";			
			header("Location: srv-plugini.php?id=".$serverid);
			die();
		}
		else
		{
			$greska = 'Podaci za konektovanje na FTP nisu tacni.';
		}
		ftp_close($ftp);	
		
		if(!empty($greska))
		{
			$_SESSION['msg2'] = "Greška";
			$_SESSION['msg1'] = $greska;
			$_SESSION['msg-type'] = "error";
			header("Location: srv-plugini.php?id=".$serverid);
			die();
		}	
	break;		

	case 'napomena':
		samo_vlasnik($_SESSION['a_id']);
		$serverid = mysql_real_escape_string($_POST['serverid']);
		$napomena = mysql_real_escape_string(htmlspecialchars($_POST['napomena']));

		if(!is_numeric($serverid)) $error = "Greška.";

		if(!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = "error";
			header("Location: srv-pocetna.php?id=".$serverid);
			die();
		}

		query_basic("UPDATE `serveri` SET `napomena` = '{$napomena}' WHERE `id` = '{$serverid}'");

		$poruka = "Promenio napomenu servera #{$serverid} <a href=\'srv-pocetna.php?id={$serverid}\'>Pogledaj server</a>";
		alog($_SESSION['a_id'], $poruka, $_SESSION['fname'], $_SESSION['lname'], fuckcloudflare(), time());

		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Promenili ste napomenu servera";
		$_SESSION['msg-type'] = "success";
		header("Location: srv-pocetna.php?id=".$serverid);
		die();
	break;
}
?>
