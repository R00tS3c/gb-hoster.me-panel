<?php
include('./fnc/ostalo.php');
include('server_process.php');
require('./inc/libs/phpseclib/Crypt/AES.php');

header('Content-Type: application/json');

$token = ispravi_text($_GET['token']);
$task = ispravi_text($_GET["task"]);
//provera
$user = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `token` = '$token'"));
if(!$user)
{
    $new_row['error'] = "Pogresan token!";
    $row_set[] = $new_row;
    echo json_encode($row_set, JSON_PRETTY_PRINT); 
    die();
}

if($task=="")
{
    $new_row['error'] = "Niste uneli task!";
    $row_set[] = $new_row;
    echo json_encode($row_set, JSON_PRETTY_PRINT); 
    die();
}

$userid = $user["klijentid"];


if(isset($_GET['task']) && $_GET['task'] == "serverlist"){
                                       $gp_obv = mysql_query("SELECT * FROM `serveri` WHERE `user_id` = '$userid'");

                                        while($row = mysql_fetch_array($gp_obv)) { 

                                            $srw_id = htmlspecialchars(mysql_real_escape_string(addslashes($row['id'])));
                                            $naziv_servera = htmlspecialchars(mysql_real_escape_string(addslashes($row['name'])));
                                            $istice = htmlspecialchars(mysql_real_escape_string(addslashes($row['istice'])));
                                            $box_id = htmlspecialchars(mysql_real_escape_string(addslashes($row['box_id'])));
                                            $port = htmlspecialchars(mysql_real_escape_string(addslashes($row['port'])));
                                            $slotovi = htmlspecialchars(mysql_real_escape_string(addslashes($row['slotovi'])));
                                            $cena = htmlspecialchars(mysql_real_escape_string(addslashes($row['cena'])));
                                            $status = htmlspecialchars(mysql_real_escape_string(addslashes($row['status'])));
                                            $igra = htmlspecialchars(mysql_real_escape_string(addslashes($row['igra'])));

                                            $serverStatus = $status;  
                                            if ($serverStatus == "Aktivan") {
                                                $serverStatus = "<span style='color: green;'> Aktivan </span>";
                                            } else if($serverStatus == "Suspendovan") {
                                                $serverStatus = "<span style='color: red;'> Suspendovan </span>";
                                            } else {
                                                $serverStatus = "<span style='color: red;'> Neaktivan </span>";
                                            }

                                            if ($igra == "1") {
                                                $igra = "img/icon/gp/game/cs.ico";
                                            } else if($igra == "2") {
                                                $igra = "img/icon/gp/game/samp.jpg";
                                            } else if($igra == "3") {
                                                $igra = "img/icon/gp/game/mc.png";
                                            } else if($igra == "4") {
                                                $igra = "img/icon/gp/game/cod.png";
                                            } else if($igra == "5") {
                                                $igra = "img/icon/gp/game/csgo.jpg";
                                            } else {
                                                $igra = "img/icon/gp/game/not-fount.png";
                                            }
                                                                                        $server_ip = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$box_id'"));
                                                                                        
                                        $new_row['id'] = $srw_id;
                                        $new_row['ime'] = $naziv_servera;
                                        $new_row['istice'] = $istice;
                                        $new_row['ip'] = $server_ip["ip"];
                                        $new_row['port'] = $port;
                                        $new_row['slotovi'] = $slotovi;
                                        $new_row['port'] = $port;
                                        $new_row["cena"] = $cena;
                                        $new_row['status'] = $status;
                                        $row_set[] = $new_row;
            
        }
                                               echo json_encode($row_set, JSON_PRETTY_PRINT); 
}

if(isset($_GET['task']) && $_GET['task'] == "server"){
        $server_id = $_GET['id'];
    $proveri_server = mysql_num_rows(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$user[klijentid]'"));

    $server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$user[klijentid]'"));
    $server_ip = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$server[box_id]'"));

    if (!$proveri_server) {
        $_SESSION['error'] = "Taj server ne postoji ili nemate ovlaščenje za isti.";
        header("Location: /gp-home.php");
        die();
    }
    
//LGSL - SERVER INFO
require './inc/libs/lgsl/lgsl_class.php';

$ss_ip = $server_ip['ip'];
$ss_port = $server['port'];
$info = mysql_fetch_array(mysql_query("SELECT * FROM `lgsl` WHERE ip='$ss_ip' AND q_port='$ss_port' AND c_port='$ss_port'"));

if($server['igra'] == "1") { $igras = "halflife"; }
else if($server['igra'] == "2") { $igras = "samp"; }
else if($server['igra'] == "4") { $igras = "callofduty4"; }
else if($server['igra'] == "3") { $igras = "minecraft"; }
else if($server['igra'] == "5") { $igras = "mta"; }

if($server['igra'] == "5") {
    $serverl = lgsl_query_live($igras, $info['ip'], NULL, $server['port']+123, NULL, 's');
} else {
    $serverl = lgsl_query_live($igras, $info['ip'], NULL, $server['port'], NULL, 's');
}
if(@$serverl['b']['status'] == '1') {
    $server_onli = "<span style='color:#54ff00;'>Online</span>"; 
} else {
    if ($server['startovan'] == "1") {
        $server_onli = "<span style='color:red;'>Server je offline.</span>";
    } else {
        $server_onli = "<span style='color:red;'>Server je stopiran u panelu.</span>";
    }
}
$server_mapa = @$serverl['s']['map'];
$server_name = @$serverl['s']['name'];
$server_play = @$serverl['s']['players'].'/'.@$serverl['s']['playersmax'];

if ($server_name == "") {
    $server_name = "n/a";
}
if ($server_mapa == "") {
    $server_mapa = "n/a";
}
$serverStatus = $server['status'];  
$new_row['ime'] = $server['name'];
$new_row['istice'] = $server['istice'];
$new_row['igra'] = gp_igra($server['igra']);
$new_row['ip'] = $server_ip['ip'];
$new_row['port'] = $server['port'];
$new_row['gpstatus'] = $serverStatus;
$new_row['status'] = @$serverl['b']['status'];



$row_set[] = $new_row;
echo json_encode($row_set, JSON_PRETTY_PRINT); 

}

if(isset($_GET['task']) && $_GET['task'] == "stop"){
	$server_id = htmlspecialchars(mysql_real_escape_string(addslashes($_GET['id'])));
	if ($server_id == "") {
    $new_row["error"] = "Morate uneti id servera!";
    $row_set[] = $new_row;
    $return = json_encode(row_set, JSON_PRETTY_PRINT);
    echo $return; 
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
    $new_row['error'] = "Server ne postoji!";
    $row_set[] = $new_row;
    echo json_encode($row_set, JSON_PRETTY_PRINT); 
	}

	if ($pp_server['startovan'] == "0") {
    $new_row['error'] = "Server je vec stopiran";
    $row_set[] = $new_row;
    echo json_encode($row_set, JSON_PRETTY_PRINT); 
	}

	if ($pp_server['status'] == "Suspendovan") {
    $new_row['error'] = "Server je suspendovan!";
    $row_set[] = $new_row;
    echo json_encode($row_set, JSON_PRETTY_PRINT); 
	}
	
	if ($pp_server['user_id'] == $user['klijentid']) {
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
			$v_d = date('d.m.Y, H:i:s');
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

			  $new_row['status'] = "ok";
              $row_set[] = $new_row;
              echo json_encode($row_set, JSON_PRETTY_PRINT); 
			die();
		} else {
            $new_row['error'] = "SSH greska!";
            $row_set[] = $new_row;
            echo json_encode($row_set, JSON_PRETTY_PRINT); 
			die();
		}

	} else {
		$_SESSION['info'] = "Nemate ovlascenje za ovaj server.";
		header("Location: gp-home.php");
		die();
	}
    
}

if(isset($_GET['task']) && $_GET['task'] == "start"){
	$server_id = htmlspecialchars(mysql_real_escape_string(addslashes($_GET['id'])));
	if ($server_id == "") {
    $new_row["error"] = "Morate uneti id servera!";
    $row_set[] = $new_row;
    $return = json_encode(row_set, JSON_PRETTY_PRINT);
    echo $return; 
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
    $new_row['error'] = "Server ne postoji!";
    $row_set[] = $new_row;
    echo json_encode($row_set, JSON_PRETTY_PRINT); 
	}

	if ($pp_server['startovan'] == "1") {
    $data = array('status' => 'true', 'msg' => 'Već je startovan');
    $new_row['error'] = "Server je vec startovan";
    $row_set[] = $new_row;
    echo json_encode($row_set, JSON_PRETTY_PRINT); 
	}

	if ($pp_server['status'] == "Suspendovan") {
    $new_row['error'] = "Server je suspendovan!";
    $row_set[] = $new_row;
    echo json_encode($row_set, JSON_PRETTY_PRINT); 
	}

	if ($pp_server['user_id'] == $user['klijentid']) {
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
                $data = array('error' => 'true', 'msg' => 'FTP Greška!');
                $return = json_encode($data, JSON_PRETTY_PRINT);
                return $return; 
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
                $data = array('error' => 'true', 'msg' => 'FTP Greška!');
                $return = json_encode($data, JSON_PRETTY_PRINT);
                return $return; 
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
                $data = array('error' => 'true', 'msg' => 'FTP Greška!');
                $return = json_encode($data, JSON_PRETTY_PRINT);
                return $return; 
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
                $data = array('error' => 'true', 'msg' => 'FTP Greška!');
                $return = json_encode($data, JSON_PRETTY_PRINT);
                return $return; 
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
			$v_d = date('d.m.Y, H:i:s');
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$user[klijentid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

                $data = array('status' => 'true', 'msg' => 'Uspešno!');
                $return = json_encode($data, JSON_PRETTY_PRINT);
                return $return; 
		} else {
                $data = array('error' => 'true', 'msg' => 'SSH Greška!');
                $return = json_encode($data, JSON_PRETTY_PRINT);
                return $return; 
		}

	} else {
                $data = array('error' => 'true', 'msg' => 'Nemate ovlašćenja za ovaj server!');
                $return = json_encode($data, JSON_PRETTY_PRINT);
                return $return; 
	}
}

if(isset($_GET['task']) && $_GET['task'] == "restart"){
	$server_id = htmlspecialchars(mysql_real_escape_string(addslashes($_GET['id'])));
	if ($server_id == "") {
    $new_row["error"] = "Morate uneti id servera!";
    $return = json_encode($data, JSON_PRETTY_PRINT);
    echo $return; 
	}

	$pp_server = mysql_fetch_array(mysql_query("SELECT * FROM serveri WHERE id='$server_id'"));
	if (!$pp_server) {
    $new_row['error'] = "Server ne postoji!";
    $row_set[] = $new_row;
    echo json_encode($row_set, JSON_PRETTY_PRINT); 
	}


	if ($pp_server['status'] == "Suspendovan") {
    $new_row['error'] = "Server je suspendovan!";
    $row_set[] = $new_row;
    echo json_encode($row_set, JSON_PRETTY_PRINT); 
	}

	if ($pp_server['user_id'] == $user['klijentid']) {
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
				                $new_row['error'] = "FTP GRESKA!";
                $row_set[] = $new_row;
                echo json_encode($row_set, JSON_PRETTY_PRINT); 
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
					                $new_row['error'] = "FTP GRESKA!";
                $row_set[] = $new_row;
                echo json_encode($row_set, JSON_PRETTY_PRINT); 
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
				$new_row['error'] = "FTP GRESKA!";
                $row_set[] = $new_row;
                echo json_encode($row_set, JSON_PRETTY_PRINT); 
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
				$new_row['error'] = "FTP GRESKA!";
                $row_set[] = $new_row;
                echo json_encode($row_set, JSON_PRETTY_PRINT); 
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
				$new_row['error'] = "FTP GRESKA!";
                $row_set[] = $new_row;
                echo json_encode($row_set, JSON_PRETTY_PRINT); 
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
                $new_row['error'] = "FTP GRESKA!";
                $row_set[] = $new_row;
                echo json_encode($row_set, JSON_PRETTY_PRINT); 
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
			$v_d = date('d.m.Y, H:i:s');
			$ip = get_client_ip();

			mysql_query("INSERT INTO `logovi` (`id`, `clientid`, `message`, `name`, `ip`, `vreme`, `adminid`) VALUES (NULL, '$_SESSION[userid]', '$log_msg', '$log_msg', '$ip', '$v_d', NULL);");

                $new_row['status'] = "ok";
                $row_set[] = $new_row;
                echo json_encode($row_set, JSON_PRETTY_PRINT); 
			die();
		} else {
                $new_row['error'] = "SSH GRESKA!";
                $row_set[] = $new_row;
                echo json_encode($row_set, JSON_PRETTY_PRINT); 
			die();
		}

	} else {
                $new_row['error'] = "Nemate pristup ovom serveru!";
                $row_set[] = $new_row;
                echo json_encode($row_set, JSON_PRETTY_PRINT); 
		die();
	}
}
?>