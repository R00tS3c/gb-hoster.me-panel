<?php
include("func.mysql.inc.php");
include("func.razno.inc.php");
include("func.server.inc.php");

include_once("libs/csrf_guard.php");


include("../configs.php");

function random_str( $length = 32, $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890" )
{
    $chars_length = strlen( $chars ) - 1;
    $string = $chars[rand( 0, $chars_length )];
    $i = 1;
    while ( $i < $length )
    {
        $r = $chars[rand( 0, $chars_length )];
        if ( $r != $string[$i - 1] )
        {
            $string .= $r;
        }
        $i = strlen( $string );
    }
    return $string;
}

function get_size( $size )
{
	if ( $size < 0 - 1 )
	{
		return "Nepoznato";
	}
    if ( $size < 1024 )
	{
		return round( $size, 2 )." Byte";
	}
	if ( $size < 1024 * 1024 )
	{
		return round( $size / 1024, 2 )." Kb";
	}
	if ( $size < 1024 * 1024 * 1024 )
	{
		return round( $size / 1024 / 1024, 2 )." Mb";
	}
	if ( $size < 1024 * 1024 * 1024 * 1024 )
	{
		return round( $size / 1024 / 1024 / 1024, 2 )." Gb";
	}
	if ( $size < 1024 * 1024 * 1024 * 1024 * 1024 )
	{
		return round( $size / 1024 / 1024 / 1024 / 1024, 2 )." Tb";
	}
}

function user_isowner( $serverid, $clientid )
{
    $serverid = mysql_real_escape_string( $serverid );
    $clientid = mysql_real_escape_string( $clientid );
    $result_owns = mysql_query( "SELECT COUNT(id) AS thecount FROM serveri WHERE id = '{$serverid}' AND user_id = '{$clientid}' ");
    while ( $row_owns = mysql_fetch_array( $result_owns ) )
    {
        $user_owns = $row_owns['thecount'];
    }
    if ( $user_owns == 0 )
    {
        return false;
    }
    return true;
}

function kill_server($ip, $port, $username, $password, $serverid, $clientid, $restart=false)
{
	
	$cmd = "kill -9 `screen -list | grep \"$username\" | awk {'print $1'} | cut -d . -f1`; pkill -u $username";
	
    if ( !( $ssh_return = ssh_exec( $ip, $port, $username, $password, $cmd ,true) ) )
	{
		return $ssh_return;
	}
	return 'stopiran';
}

function get_backup_data($clientid,$serverid,$count=false)
{
	$serverid = mysql_real_escape_string( $serverid );
        $clientid = mysql_real_escape_string( $clientid );
	
	$user_owns = user_isowner( $serverid, $clientid );
	if ( !$user_owns )
	{
		$_SESSION['msg'] = "You are not the owner of this server!";
		header("Location: index.php");
		exit();
	}
	
	$query = "SELECT
	s.id,
	s.box_id,
	s.username,
	s.name,
	s.ip_id,
	
	b1.boxid,
	b1.sshport,
	b1.login,
	b1.password,
	b1.ip
	
	FROM serveri AS s
	LEFT JOIN box AS b1 ON s.box_id = b1.boxid WHERE `id`='$serverid'";
	
	if (!($result = mysql_query($query))) exit("Failed!! " . mysql_error());
	$row    = mysql_fetch_row($result);
	
	$sshport = $row[6];
	$root = $row[7];
	
	require_once("includes/libs/phpseclib/Crypt/AES.php");
	$aes = new Crypt_AES();
	$aes->setKeyLength(256);
	$aes->setKey(CRYPT_KEY);
	$password = $aes->decrypt($row[8]);
	$ip = $row[9];
	$serverftpname = $row[2];
	
	$cmd = '
	cd /home/backups;
	if [ "$?" -ne "0" ]; then
	mkdir -p /home/backups;
	fi;
	
	DATA="`ls -lRt | grep -i -e \''.$serverftpname.'.*\.tar.gz$\' -e \''.$serverftpname.'.*\.pack$\' -e \''.$serverftpname.'.*\.res$\' -e \''.$serverftpname.'.*\.pack1$\'`";
	
	if [ "$?" -eq 0 ]; then
	printf \'%s\n\' "${DATA}"
	else
	echo "empty";
	fi
	';
	
	if ( !( $ssh_return = ssh_exec( $ip, $sshport, $root, $password, $cmd ,true) ) )
	{
		return false;
	}
	else if ( trim( $ssh_return ) == "empty" )
    {
        return false;
    }
	
	
	$info = trim( $ssh_return ); 
	$info = preg_split('/[\r\n]+/', $info, -1, PREG_SPLIT_NO_EMPTY);
	
	$arr = array();
	$i=0;
	foreach ( $info as $key => $single_file )
	{
		$arr_items = preg_split("/[\\s]+/", $single_file, 9);
		
		$data = explode(".", $arr_items[8]);
		$date = date('d.m.Y - H:i:s', $data[1]);
                if ($count)
		{
			if ( $data[2] == "pack" or $data[2] == "res" or $data[2] == "pack1"){
			     return -1;
		    }
		}
		$i++;
		
		$arr[$key]['date'] = $date;
		$arr[$key]['size'] = get_size($arr_items[4]);
		
		$c = new Cipher();
		$encrypted = $c->encrypt($arr_items[8]);
		$arr[$key]['name'] = $encrypted;
		
		//$arr[$key]['type'] = $data[2];	
		
		if ( $data[2] == "pack" )
		$arr[$key]['status'] = 0;
		else if ( $data[2] == "pack1" )
		$arr[$key]['status'] = 4;
	    else if ( $data[2] == "tar" )
		$arr[$key]['status'] = 1;
		else if ( $data[2] == "res" )
		$arr[$key]['status'] = 3;
	    else
		$arr[$key]['status'] = 2;
		
	}
	
	if ($count)
	{
		if ($i >= _MAX_USERBACKUPS ) {
			
			return $i;
		}
	}
	
	return $arr;
}

function get_backup_data2($clientid,$serverid,$count=false)
{
	$serverid = mysql_real_escape_string( $serverid );
	$clientid = mysql_real_escape_string( $clientid );
	
	$user_owns = user_isowner( $serverid, $clientid );
	if ( !$user_owns )
	{
		$_SESSION['msg'] = "You are not the owner of this server!";
		header("Location: index.php");
		exit();
	}
	
	$query = "SELECT
	s.id,
	s.box_id,
	s.username,
	s.name,
	s.ip_id,
	
	b1.boxid,
	b1.sshport,
	b1.login,
	b1.password,
	b1.ip
	
	FROM serveri AS s
	LEFT JOIN box AS b1 ON s.box_id = b1.boxid WHERE `id`='$serverid'";
	
	if (!($result = mysql_query($query))) exit("Failed!! " . mysql_error());
	$row    = mysql_fetch_row($result);
	
	$sshport = $row[6];
	$root = $row[7];
	
	require_once("includes/libs/phpseclib/Crypt/AES.php");
	$aes = new Crypt_AES();
	$aes->setKeyLength(256);
	$aes->setKey(CRYPT_KEY);
	$password = $aes->decrypt($row[8]);
	$ip = $row[9];
	$serverftpname = $row[2];
	
	$cmd = '
	cd /home/backupnew;
	
	if [ "$?" -ne "0" ]; then
	mkdir -p /home/backupnew;
	fi;
	
	DATA="`ls -lRt | grep -i -e \''.$serverftpname.'.*\.tar.gz\' -e \''.$serverftpname.'.*\.pack\' `";
	
	if [ "$?" -eq 0 ]; then
	printf \'%s\n\' "${DATA}"
	else
	echo "empty";
	fi
	';
	
	if ( !( $ssh_return = ssh_exec( "51.254.203.85", 2223, "backupnew", "backupnew", $cmd ,true) ) )
	{
		return false;
	}
	else if ( trim( $ssh_return ) == "empty" )
    {
        return false;
    }
	
	
	$info = trim( $ssh_return ); 
	$info = preg_split('/[\r\n]+/', $info, -1, PREG_SPLIT_NO_EMPTY);
	
	$arr = array();
	$i=0;
	foreach ( $info as $key => $single_file )
	{
		$arr_items = preg_split("/[\\s]+/", $single_file, 9);
		
		$data = explode(".", $arr_items[8]);
		$date = date('d.m.Y - H:i:s', $data[1]);
		
		if ($count)
		{
			if ( $data[2] == "pack" or $data[2] == "res" or $data[2] == "pack1"){
			     return -1;
		    }
		}
		$i++;
		
		$arr[$key]['date'] = $date;
		$arr[$key]['size'] = get_size($arr_items[4]);
		
		$c = new Cipher();
		$encrypted = $c->encrypt($arr_items[8]);
		$arr[$key]['name'] = $encrypted;
		
		//$arr[$key]['type'] = $data[2];	
		
		if ( $data[2] == "pack" )
		$arr[$key]['status'] = 0;
		else if ( $data[2] == "pack1" )
		$arr[$key]['status'] = 4;
	    else if ( $data[2] == "tar" )
		$arr[$key]['status'] = 1;
		else if ( $data[2] == "res" )
		$arr[$key]['status'] = 3;
	    else
		$arr[$key]['status'] = 2;
		
	}
	
	if ($count)
	{
		if ($i >= _MAX_USERBACKUPS ) {
			
			return $i;
		}
	}
	
	return $arr;
}

function ssh_exec($ip,$port,$user,$pass,$cmd,$output=false,$wait=true)
{
	
	
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
	   
	if(!empty($err))
    {
        return 'Error! <b>' . $err . '</b>';
    }
	
    require_once('libs/SSH/Net/SSH2.php');
	

    $ssh = new Net_SSH2($ip, $port, $timeout);
	
    if (!$ssh->login($user, $pass))
    {
		return false;
    }
	
	if($output)
    {
		
		return trim($ssh->exec($cmd,$wait));
		
    }
    else
    {
        $ssh->exec($cmd,$wait);
        return true;
    }
}

function price_by_slot($clientid, $igra, $srvid) {
	
	$serverslot = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '{$srvid}'");
	
	$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '{$clientid}'");

	$cenaslota = query_fetch_assoc("SELECT `cena` FROM `modovi` WHERE `igra` = '{$igra}'");
	$cenaslota = explode("|", $cenaslota['cena']);

	if($klijent['zemlja'] == "srb") $cena = $cenaslota[0];
	else if($klijent['zemlja'] == "hr") $cena = $cenaslota[3];
	else if($klijent['zemlja'] == "bih") $cena = $cenaslota[4];
	else if($klijent['zemlja'] == "mk") $cena = $cenaslota[2];
	else if($klijent['zemlja'] == "cg") $cena = $cenaslota[1];
	else if($klijent['zemlja'] == "other") $cena = $cenaslota[1];
	


	$out = round($cena * $serverslot['slotovi'],2);
	$out = number_format($out, 2);
	
	$out = $out." ".drzava_valuta($klijent['zemlja']); 
	return $out;
}

function getMoney($clientid, $currency=false, $def_iznos=false) {
	
	$query = "SELECT 
	
	klijenti.currency,
	klijenti.novac,
	klijenti.zemlja,

	billing_currency.cid,
	billing_currency.sign,
	billing_currency.multiply,
	billing_currency.zemlja
	FROM klijenti 
	LEFT JOIN billing_currency ON klijenti.zemlja = billing_currency.zemlja WHERE `klijentid`='{$clientid}'";
	
	if ( !( $result = mysql_query( $query ) ) )
	{
		exit( "Failed!!!". mysql_error() );
	}
	while ( $line = mysql_fetch_assoc( $result ) )
	{
		$value[] = $line;
	}
	
	if ( empty($value[0]['cid']) ) return false;

        if (!$def_iznos)
        {
           $def_iznos = $value[0]['novac'];    
        }

    $out = round($def_iznos*$value[0]['multiply'],2);
	$out = number_format($out, 2);
	
	if ($currency) $out .= " ".$value[0]['sign'];
	return $out;
}
?>