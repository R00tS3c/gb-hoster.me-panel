<?php

	//SSH

	function ssh_exec($ip,$port,$user,$pass,$cmd,$output=false,$wait=true) {
		$timeout = 6;
		
		if(empty($ip))
		   $err  = 'Connection Address';
		elseif(empty($port))
		   $err  = 'Connection Port';
		elseif(empty($user))
		   $err  = 'Connection Username';
		elseif(empty($pass))
		   $err  = 'Connection Password';
		elseif(empty($cmd))
		   $err  = 'Connection Command';
		   
		if(!empty($err)) {
	        return 'Error! <b>' . $err . '</b>';
	    }
		
	    require_once('libs/SSH/Net/SSH2.php');
	
	    $ssh = new Net_SSH2($ip, $port, $timeout);
		
	    if (!$ssh->login($user, $pass)) {
			return false;
	    }
		
		if($output) {
			return trim($ssh->exec($cmd,$wait));
	    } else {
	        $ssh->exec($cmd,$wait);
	        return true;
	    }
	}

?>