<?php
	function output_errors($errors){
		return '<div id="error_box"><ol style="color:white;" class="err_ul"><li>'.implode('</li><li>',$errors).'</li></ol></div>';
	}

	function redirect($url){
		header('Location:'.$url);
	}

	function get_client_ip_env() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	        $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	 
	    return $ipaddress;
	}

	function get_client_host(){
		$hostname = $_SERVER['REMOTE_ADDR'];

		if (strstr($hostname, ', ')) {
		    $ips = explode(', ', $hostname);
		    $ip = $ips[0];

		    return gethostbyaddr($ip);
		}

		return gethostbyaddr($hostname);				
	}

	function get_ip_contry($ip){
		$loc_ip = json_decode(file_get_contents("http://ipinfo.io/$ip/json/"));
		echo $loc_ip->country;
	}

	function rdr_msg($msg, $url){
		echo "<p style='color:red;'>$msg</p>";
		header('Location:'.$url);
		die();
	}

	function start_server($ip, $ssh_port, $username, $password, $komanda){

		if (!function_exists("ssh2_connect")) return "SSH2 PHP extenzija nije instalirana";

		if(!($con = ssh2_connect("$ip", "$ssh_port"))){
		    return "Ne mogu se spojiti na server";
		} else {

		    if(!ssh2_auth_password($con, "$username", "$password")) {
		        return "Netačni podatci za prijavu";
		    } else {
				/* START FUNKIJE */    	 

				$stream = ssh2_shell($con, 'vt102', null, 80, 24, SSH2_TERM_UNIT_CHARS);
				fwrite( $stream, "screen -A -m -S $username".PHP_EOL);
				sleep(1);
				fwrite( $stream, "$komanda".PHP_EOL);
				sleep(1);
				$data = "";
				while($line = fgets($stream)) {
				$data .= $line;
				}

				return TRUE;
				    
				/* KRAJ FUNKCIJE */    
		    }
		}	

	}

	function stop_server($server, $port, $username, $password){
		if (!function_exists("ssh2_connect")) return "SSH2 PHP extenzija nije instalirana";

		if(!($con = ssh2_connect("$server", "$port"))){
		    return "Ne mogu se spojiti na server";
		} else {
	    	if(!ssh2_auth_password($con, "$username", "$password")) {
	        	return "Netačni podatci za prijavu";
	    	} else {
				/* START FUNKIJE */    	    

				$stream = ssh2_shell($con, 'vt102', null, 80, 24, SSH2_TERM_UNIT_CHARS);
				/*fwrite( $stream, "screen -S $username -X quit".PHP_EOL);*/
				fwrite( $stream, 'kill -9 `screen -list | grep "'.$username.'" | awk {\'print $1\'} | cut -d . -f1`'.PHP_EOL);
				sleep(1);
				fwrite( $stream, 'screen -wipe'.PHP_EOL);
				sleep(1);
				while($line = fgets($stream)) {
				$data .= $line;
				}

				return TRUE;
				    
				/* KRAJ FUNKCIJE */    
		    }
		}
	}

	function reinstall_server($server, $port, $username, $password, $mod_putanja) {
		if (!function_exists("ssh2_connect")) return "Error SSH";

		if(!($con = ssh2_connect($server, $port))){
		    return "Ne mogu se spojiti na server";
		} else {
	    	if(!ssh2_auth_password($con, $username, $password)) {
	        	return "Netačni podatci za prijavu";
	    	} else {
				
				$stream = ssh2_shell($con, 'xterm');
	    	    $cmd1 = 'screen -m -S '.$server["username"].'_reinstall';	    
	    	    fwrite( $stream, "$cmd1\n");
	    	    sleep(1);
	    	    $cmd2 = 'nice -n 19 rm -Rf /home/'.$username.'/* && cp -Rf '.$mod_putanja.'/* /home/'.$username.' && chown -Rf '.$username.':'.$username.' /home/'.$username.' && exit';
	    	    fwrite( $stream, "$cmd2\n");
	    	    sleep(2);

				$data = "";
				
	    	    while($line = fgets($stream)) {
	    	    	$data .= $line;
	    	    }

	    	    return 'reinstaliran';
				
			}
		}	
	}

	function promeni_mod($server, $port, $username, $password, $mod_putanja) {
		if (!function_exists("ssh2_connect")) return "Error SSH";

		if(!($con = ssh2_connect($server, $port))){
		    return "Ne mogu se spojiti na server";
		} else {
	    	if(!ssh2_auth_password($con, $username, $password)) {
	        	return "Netačni podatci za prijavu";
	    	} else {
				
				$stream = ssh2_shell($con, 'xterm');
	    	    $cmd1 = 'screen -m -S '.$server["username"].'_reinstall';	    
	    	    fwrite( $stream, "$cmd1\n");
	    	    sleep(1);
	    	    $cmd2 = 'nice -n 19 rm -Rf /home/'.$username.'/* && cp -Rf '.$mod_putanja.'/* /home/'.$username.' && chown -Rf '.$username.':'.$username.' /home/'.$username.' && exit';
	    	    fwrite( $stream, "$cmd2\n");
	    	    sleep(2);

				$data = "";
				
	    	    while($line = fgets($stream)) {
	    	    	$data .= $line;
	    	    }

	    	    //return 'reinstaliran';
				
			}
		}	
	}

	function ssh_provera($ip, $sshport, $login, $password){
		if (!function_exists("ssh2_connect")) {
			return 'Error SSH - SSH2 PHP extenzija nije instalirana.';
		} else {

			require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/SSH2.php");

			$ssh = new Net_SSH2($ip, $sshport);

			if (!$ssh->login($login, $password))
			{
				$socket = fsockopen($ip, $sshport, $errno, $errstr, 100);

				if ($socket == FALSE) {
					return 'Error SSH';
				}

				return 'Error SSH'; 
			}

			return $ssh;

		}	
	}