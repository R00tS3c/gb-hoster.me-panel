<?php 
	
	//Start/Restart server

	function start_server($server_ip, $ssh_port, $username, $password, $komanda, $server_igraa) {
		if (!function_exists("ssh2_connect")) {
			return "SSH2 PHP extenzija nije instalirana";
		}

		if(!($con = ssh2_connect($server_ip, $ssh_port))) {
		    return "Ne mogu se spojiti na server";
		} else {

		    if(!ssh2_auth_password($con, $username, $password)) {
		    	return "Netačni podatci za prijavu";
		    } else {
		    	if ($server_igraa == "1") {
		    		$stream = ssh2_shell($con, 'xterm');
			    	//$stream = ssh2_shell($con, 'xterm');
			    	//Stopiraj server
					fwrite( $stream, 'kill -9 `screen -list | grep "'.$username.'" | awk {\'print $1\'} | cut -d . -f1`'.PHP_EOL);
					sleep(1);
					fwrite( $stream, 'screen -wipe'.PHP_EOL);
					sleep(1);
					//Startuj server
					//fwrite( $stream, "screen -A -m -L -S $username".PHP_EOL);
					fwrite( $stream, "screen -mSL $username".PHP_EOL);
					sleep(1);
					fwrite( $stream, "$komanda".PHP_EOL);
					sleep(1);
					$data = "";
					while($line = fgets($stream)) {
						$data .= $line;
					}
					return "OK";
		    	} else if ($server_igraa == "2") {
		    		$stream = ssh2_shell($con, 'xterm');
			    	//$stream = ssh2_shell($con, 'xterm');
			    	//Stopiraj server
					fwrite( $stream, 'kill -9 `screen -list | grep "'.$username.'" | awk {\'print $1\'} | cut -d . -f1`'.PHP_EOL);
					sleep(1);
					fwrite( $stream, 'screen -wipe'.PHP_EOL);
					sleep(1);
					//Startuj server
					//fwrite( $stream, "screen -A -m -S $username".PHP_EOL);
					fwrite( $stream, "screen -mSL $username".PHP_EOL);
					sleep(1);
					fwrite( $stream, "$komanda".PHP_EOL);
					sleep(1);
					$data = "";
					while($line = fgets($stream)) {
						$data .= $line;
					}
					return "OK";
		    	}
			else if ($server_igraa == "3") {
		    		$stream = ssh2_shell($con, 'xterm');
			    	//$stream = ssh2_shell($con, 'xterm');
			    	//Stopiraj server
					fwrite( $stream, 'kill -9 `screen -list | grep "'.$username.'" | awk {\'print $1\'} | cut -d . -f1`'.PHP_EOL);
					sleep(1);
					fwrite( $stream, 'screen -wipe'.PHP_EOL);
					sleep(1);
					//Startuj server
					//fwrite( $stream, "screen -A -m -S $username".PHP_EOL);
					fwrite( $stream, "screen -mSL $username".PHP_EOL);
					sleep(1);
					fwrite( $stream, "$komanda".PHP_EOL);
					sleep(1);
					$data = "";
					while($line = fgets($stream)) {
						$data .= $line;
					}
					return "OK";
		    	}
		    	else if ($server_igraa == "9") {
		    		$stream = ssh2_shell($con, 'xterm');
			    	//$stream = ssh2_shell($con, 'xterm');
			    	//Stopiraj server
					fwrite( $stream, 'kill -9 `screen -list | grep "'.$username.'" | awk {\'print $1\'} | cut -d . -f1`'.PHP_EOL);
					sleep(1);
					fwrite( $stream, 'screen -wipe'.PHP_EOL);
					sleep(1);
					//Startuj server
					//fwrite( $stream, "screen -A -m -S $username".PHP_EOL);
					fwrite( $stream, "screen -mSL $username".PHP_EOL);
					sleep(1);
					fwrite( $stream, "$komanda".PHP_EOL);
					sleep(1);
					$data = "";
					while($line = fgets($stream)) {
						$data .= $line;
					}
					return "OK";
		    	}
			else {
		    		return "Za ovaj server jos nije optimizovan GamePanel!";
		    	}
		    	//var_dump($stream);
		    }
		}	
	}

	//Stop server

	function stop_server($server_ip, $ssh_port, $username, $password) {
		if (!function_exists("ssh2_connect")) return "SSH2 PHP extenzija nije instalirana";

		if(!($con = ssh2_connect($server_ip, $ssh_port))) {
		    return "Ne mogu se spojiti na server";
		} else {
	    	if(!ssh2_auth_password($con, $username, $password)) {
	        	return "Netačni podatci za prijavu";
	    	} else {
				$stream = ssh2_shell($con, 'xterm');
				//$stream = ssh2_shell($con, 'xterm');
				fwrite( $stream, 'kill -9 `screen -list | grep "'.$username.'" | awk {\'print $1\'} | cut -d . -f1`'.PHP_EOL);
				sleep(1);
				fwrite( $stream, 'screen -wipe'.PHP_EOL);
				sleep(1);
				$data = "";
				while($line = fgets($stream)) {
					$data .= $line;
				}
				return "OK";
		    }
		}
	}

	//Reinstall server
	
	/* ROOT VERSION
	
	function reinstall_server($server_ip, $ssh_port, $root_user, $root_pw, $ftp_username, $mod_putanja) {
		if (!function_exists("ssh2_connect")) return "Error SSH";

		if(!($con = ssh2_connect($server_ip, $ssh_port))) {
		    return "Ne mogu se spojiti na server";
		} else {
	    	if(!ssh2_auth_password($con, $root_user, $root_pw)) {
	        	return "Netačni podatci za prijavu";
			} else {
				$stream = ssh2_shell($con, 'xterm');
	    	    $cmd1 = 'screen -m -S '.$ftp_username.'_reinstall';	    
	    	    fwrite( $stream, "$cmd1\n");
	    	    sleep(1);
	    	    $cmd2 = 'nice -n 19 rm -Rf /home/'.$ftp_username.'/* && cp -Rf '.$mod_putanja.'/* /home/'.$ftp_username.' && sudo chmod -R 777 '.$ftp_username.':'.$ftp_username.' /home/'.$ftp_username.' && exit';
	    	    fwrite( $stream, "$cmd2\n");
	    	    sleep(2);

				$data = "";
				
	    	    while($line = fgets($stream)) {
	    	    	$data .= $line;
	    	    }
	    	    return "OK";
			}
		}	
	}

	*/
/*
	//Reinstall server - USER VERSION
	function reinstall_server($server_ip, $ssh_port, $root_user, $ftp_password, $ftp_username, $mod_putanja) {
		if (!function_exists("ssh2_connect")) {
			return "Error SSH";
		}

		if(!($con = ssh2_connect($server_ip, $ssh_port))) {
		    return "Ne mogu se spojiti na server";
		} else {
	    	if(!ssh2_auth_password($con, $ftp_username, $ftp_password)) {
	        	return "Netačni podatci za prijavu";
			} else {
				$stream = ssh2_shell($con, 'xterm');
	    	    $cmd1 = 'screen -m -S '.$ftp_username.'_reinstall';	    
	    	    fwrite( $stream, "$cmd1\n");
	    	    sleep(1);
	    	    $cmd2 = 'nice -n 19 rm -Rf /home/'.$ftp_username.'/* && cp -Rf '.$mod_putanja.'/* /home/'.$ftp_username.' && sudo chmod -R 755 '.$ftp_username.':'.$ftp_username.' /home/'.$ftp_username.' && exit';
	    	    fwrite( $stream, "$cmd2\n");
	    	    sleep(1);

				$data = "";
				
	    	    while($line = fgets($stream)) {
	    	    	$data .= $line;
	    	    }
	    	    return "OK";
			}
		}	
	}
*/
	//Reinstall server - USER VERSION
	function reinstall_server($server_ip, $ssh_port, $root_user, $ftp_password, $ftp_username, $mod_id) {
		if (!function_exists("ssh2_connect")) {
			return "Error SSH";
		}
		if(!($con = ssh2_connect($server_ip, $ssh_port))) {
		    return "Ne mogu se spojiti na server";
		} else {
	    	if(!ssh2_auth_password($con, $ftp_username, $ftp_password)) {
	        	return "Netačni podatci za prijavu";
			} else {
				$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$mod_id."'");
				
				if(empty($mod["link"])) return "Ne mogu dobiti mod putanju, javite administratorima o ovome.";
				if(empty($mod["zipname"])) return "Ne mogu dobiti mod putanju, javite administratorima o ovome.";
				
				$mod["zipname"] = str_replace("zip", "tar.gz", $mod["zipname"]);
				
				$cmd_nice = 'nice -n 19 rm -Rf /home/'.$ftp_username.'/*';
				$cmd_wget = 'cd /home/'.$ftp_username.'/ && wget '.$mod["link"].'/'.$mod["zipname"].'';
				$cmd_unzip = 'tar xvfz '.$mod["zipname"].' && chown -Rf '.$ftp_username.':'.$ftp_username.' /home/'.$ftp_username.'';
				$cmd_chown = 'chown -Rf '.$ftp_username.':'.$ftp_username.' /home/'.$ftp_username.'/* && chmod -R 755 /home/'.$ftp_username.'/*';
				$cmd_clean = 'rm -rf '.$mod["zipname"].' && rm -rf wget-log';
				//$cmd_steamclient = "cd /home/$ftp_username/;mkdir .steam;cd /home/$ftp_username/.steam/;mkdir sdk32;cd /home/$ftp_username/.steam/sdk32/;wget www.gb-hoster.me/Tools/steamclient.so;rm -rf steamclient.so.1;rm -rf steamclient.so.2;rm -rf steamclient.so.3;rm -rf steamclient.so.4;chmod -R 777 *;chown -Rf $ftp_username:$ftp_username /home/$ftp_username/.steam/sdk32/steamclient.so;exit";
				
				$stream = ssh2_shell($con, 'xterm');
	    	    $cmd1 = 'screen -m -S '.$ftp_username.'_reinstall';	    
	    	    fwrite( $stream, "$cmd1\n");
	    	    sleep(1);
				
				fwrite( $stream, "$cmd_nice\n");
				fwrite( $stream, "$cmd_wget\n");
				sleep(5);
				fwrite( $stream, "$cmd_unzip\n");
				sleep(5);
				fwrite( $stream, "$cmd_chown\n");
				fwrite( $stream, "$cmd_clean\n");
				//fwrite( $stream, "$cmd_steamclient\n");
				sleep(1);
				
				$data = "";
				
	    	    while($line = fgets($stream)) {
	    	    	$data .= $line;
	    	    }
	    	    return "OK";
			}
		}
	}
	
	//KILL - Obrisi sve
	function obrisi_sve($server_ip, $ssh_port, $root_user, $root_pw, $ftp_username) {
		if (!function_exists("ssh2_connect")) return "Error SSH";

		if(!($con = ssh2_connect($server_ip, $ssh_port))) {
		    return "Ne mogu se spojiti na server";
		} else {
	    	if(!ssh2_auth_password($con, $root_user, $root_pw)) {
	        	return "Netačni podatci za prijavu";
			} else {
				$stream = ssh2_shell($con, 'xterm');
	    	    $cmd1 = 'screen -m -S '.$ftp_username.'_delAll';	    
	    	    fwrite( $stream, "$cmd1\n");
	    	    sleep(1);
	    	    $cmd2 = 'nice -n 19 rm -Rf /home/'.$ftp_username.'/* && sudo chmod -R 755 '.$ftp_username.':'.$ftp_username.' /home/'.$ftp_username.' && exit';
	    	    fwrite( $stream, "$cmd2\n");
	    	    sleep(1);

				$data = "";
				
	    	    while($line = fgets($stream)) {
	    	    	$data .= $line;
	    	    }
	    	    return "OK";
			}
		}	
	}
/*
	//Change mod server
	function promeni_mod($server_ip, $ssh_port, $root_user, $root_pw, $ftp_username, $mod_putanja) {
		if (!function_exists("ssh2_connect")) return "Error SSH";

		if(!($con = ssh2_connect($server_ip, $ssh_port))) {
		    return "Ne mogu se spojiti na server";
		} else {
	    	if(!ssh2_auth_password($con, $root_user, $root_pw)) {
	        	return "Netačni podatci za prijavu";
			} else {
				$stream = ssh2_shell($con, 'xterm');
	    	    $cmd1 = 'screen -m -S '.$ftp_username.'_mod';	    
	    	    fwrite( $stream, "$cmd1\n");
	    	    sleep(1);
	    	    $cmd2 = 'nice -n 19 rm -Rf /home/'.$ftp_username.'/* && cp -Rf '.$mod_putanja.'/* /home/'.$ftp_username.' && sudo chmod -R 755 '.$ftp_username.':'.$ftp_username.' /home/'.$ftp_username.' && exit';
	    	    fwrite( $stream, "$cmd2\n");
	    	    sleep(2);

				$data = "";
				
	    	    while($line = fgets($stream)) {
	    	    	$data .= $line;
	    	    }
	    	    return "OK";
			}
		}	
	}
*/
	//Change mod server
	function promeni_mod($server_ip, $ssh_port, $root_user, $root_pw, $ftp_username, $mod_id) {
		if (!function_exists("ssh2_connect")) return "Error SSH";

		if(!($con = ssh2_connect($server_ip, $ssh_port))) {
		    return "Ne mogu se spojiti na server";
		} else {
	    	if(!ssh2_auth_password($con, $root_user, $root_pw)) {
	        	return "Netačni podatci za prijavu";
			} else {
				$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$mod_id."'");
				
				if(empty($mod["link"])) return $jezik['text298'];
				if(empty($mod["zipname"])) return $jezik['text298'];
				
				$mod["zipname"] = str_replace("zip", "tar.gz", $mod["zipname"]);
				
				$cmd_nice = 'nice -n 19 rm -Rf /home/'.$ftp_username.'/*';
				$cmd_wget = 'cd /home/'.$ftp_username.'/ && wget '.$mod["link"].'/'.$mod["zipname"].'';
				$cmd_unzip = 'tar xvfz '.$mod["zipname"].' && chown -Rf '.$ftp_username.':'.$ftp_username.' /home/'.$ftp_username.'';
				$cmd_chown = 'chown -Rf '.$ftp_username.':'.$ftp_username.' /home/'.$ftp_username.'/* && chmod -R 755 /home/'.$ftp_username.'/*';
				$cmd_clean = 'rm -rf '.$mod["zipname"].' && rm -rf wget-log';
				//$cmd_steamclient = "cd /home/$ftp_username/;mkdir .steam;cd /home/$ftp_username/.steam/;mkdir sdk32;cd /home/$ftp_username/.steam/sdk32/;wget www.gb-hoster.me/Tools/steamclient.so;rm -rf steamclient.so.1;rm -rf steamclient.so.2;rm -rf steamclient.so.3;rm -rf steamclient.so.4;chmod -R 777 *;chown -Rf $ftp_username:$ftp_username /home/$ftp_username/.steam/sdk32/steamclient.so;exit";
				
				$stream = ssh2_shell($con, 'xterm');
	    	    $cmd1 = 'screen -m -S '.$ftp_username.'_reinstall';	    
	    	    fwrite( $stream, "$cmd1\n");
	    	    sleep(1);
				
				fwrite( $stream, "$cmd_nice\n");
				fwrite( $stream, "$cmd_wget\n");
				sleep(5);
				fwrite( $stream, "$cmd_unzip\n");
				sleep(5);
				fwrite( $stream, "$cmd_chown\n");
				fwrite( $stream, "$cmd_clean\n");
				//fwrite( $stream, "$cmd_steamclient\n");
				sleep(1);
				
				$data = "";
				
	    	    while($line = fgets($stream)) {
	    	    	$data .= $line;
	    	    }
	    	    return "OK";
			}
		}	
	}

	//Create new ftp password

	function new_ftp_pw($server_ip, $ssh_port, $ftp_username, $ftp_password, $ftp_pw_kor) {
		if (!function_exists("ssh2_connect")) return "SSH2 PHP extenzija nije instalirana";

		if(!($con = ssh2_connect($server_ip, $ssh_port))) {
		    return "Ne mogu se spojiti na server";
		} else {
			if(!ssh2_auth_password($con, $ftp_username, $ftp_password)) {
	        	return "Netačni podatci za prijavu";
	    	} else {
	    		$cmd1 = 'passwd';
                $cmd2 = $ftp_password;
                $cmd3 = $ftp_pw_kor;
                $cmd4 = $ftp_pw_kor;

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
                    if($pos !== false) {
                        return "OK";
                    }
                }
            }
	   	}
	}

	//WGET komanda

	function wget_comands($server_ip, $ssh_port, $ftp_username, $ftp_password, $path, $wget_link) {
		if (!function_exists("ssh2_connect")) return "SSH2 PHP extenzija nije instalirana";

		if(!($con = ssh2_connect($server_ip, $ssh_port))) {
		    return "Ne mogu se spojiti na server";
		} else {
			if(!ssh2_auth_password($con, $ftp_username, $ftp_password)) {
	        	return "Netačni podatci za prijavu";
	    	} else {
                $cmd1 = "cd ~";
                $cmd2 = "cd ".$path;
                $cmd3 = "wget ".$wget_link;

                $stream = ssh2_shell($con, 'xterm');
                fwrite( $stream, "$cmd1\n");
                sleep(1);            
                fwrite( $stream, "$cmd2\n");
                sleep(1);
                fwrite( $stream, "$cmd3\n");
                sleep(1);
               	$data = "";
	    	    while($line = fgets($stream)) {
	    	    	$data .= $line;
	    	    }
	    	    return "OK";
            }
	   	}
	}

	//WGET komanda

	function unn_file($server_ip, $ssh_port, $ftp_username, $ftp_password, $file_ext, $file_name) {
		if (!function_exists("ssh2_connect")) return "SSH2 PHP extenzija nije instalirana";

		if(!($con = ssh2_connect($server_ip, $ssh_port))) {
		    return "Ne mogu se spojiti na server";
		} else {
			if(!ssh2_auth_password($con, $ftp_username, $ftp_password)) {
	        	return "Netačni podatci za prijavu";
	    	} else {
                $cmd1 = "cd ~";
                $cmd2 = $file_ext.' '.$file_name;

                $stream = ssh2_shell($con, 'xterm');
                fwrite( $stream, "$cmd1\n");
                sleep(1);            
                fwrite( $stream, "$cmd2\n");
                sleep(1);
               	$data = "";
	    	    while($line = fgets($stream)) {
	    	    	$data .= $line;
	    	    }
	    	    return "OK";
            }
	   	}
	}

?>