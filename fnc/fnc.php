<?php
	
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

	function get_client_ip(){
		$loc_ip = json_decode(file_get_contents("http://ipinfo.io/$ip/json/"));
		echo $loc_ip->ip;
	}

	function get_ip_contry($ip){
		$loc_ip = json_decode(file_get_contents("http://ipinfo.io/$ip/json/"));
		echo $loc_ip->country;
	}