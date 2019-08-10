<?php
error_reporting(0);

function ipadresabezportaap($srvid)
{
	$port = query_fetch_assoc("SELECT `port`, `ip_id`, `box_id` FROM `serveri` WHERE `id` = '".$srvid."'");
	$ip = query_fetch_assoc("SELECT `ip` FROM `boxip` WHERE `ipid` = '".$port['ip_id']."'");
	
	return $ip['ip'];
}

function convertCurrency($amount,$from_currency,$to_currency) {
	
	$from_Currency = urlencode($from_currency);
	$to_Currency = urlencode($to_currency);
	$amount = urlencode($amount);
	$query =  "{$from_Currency}_{$to_Currency}";
	
	$json = file_get_contents("https://free.currencyconverterapi.com/api/v6/convert?q={$query}&compact=ultra&apiKey=46c2492b21b5339228ec");
	$obj = json_decode($json, true);
	
	$val = floatval($obj["$query"]);
	
	$total = $val * $amount;
	return number_format($total, 2, '.', '');
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

$dostupni_jezici = array('en','sr');

if(isset($_COOKIE['jezik']))
{
	$_SESSION['jezik'] = $_COOKIE['jezik'];
}
else
{
	if(!isset($_SESSION['jezik'])) $_SESSION['jezik'] = 'sr';
}

if(isset($_GET['jezik']) && $_GET['jezik'] != '')
{ 
	if(in_array($_GET['jezik'], $dostupni_jezici))
	{       
		$_SESSION['jezik'] = $_GET['jezik'];
		setcookie('jezik', $_GET['jezik'], time() + (86400 * 7 * 2));
	}
}

include('../jezici/lang.'.$_SESSION['jezik'].'.php');

$times = array(
	'online'	=> 0,
	'idle'		=> 300,
	'offline'	=> 700,
);


function sampcfg($find, $serverid)
{
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
	$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
	
	$fajl = "ftp://$server[username]:$server[password]@$boxip[ip]:21/server.cfg";
				
	$contents = file_get_contents($fajl);
	
	$pattern = preg_quote($find, '/');

	$pattern = "/^.*$pattern.*\$/m";

	if(preg_match_all($pattern, $contents, $matches)){
		$text = implode("\n", $matches[0]);
		$g = explode(' ', $text);
		return $g[1];
	}
}

function adminUlogovan()
{
	if (!empty($_SESSION['a_id']) && is_numeric($_SESSION['a_id']))
	{
		$verifikacija = mysql_query( "SELECT `username` FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'" );
		if (mysql_num_rows($verifikacija) == 1)
		{
			return TRUE;
		}
		unset($verifikacija);
	}
	return FALSE;
}

if(empty($fajl)) $fajl = "0";
if($fajl != "login")
{
	if (AdminUlogovan() == FALSE) 
	{
		if(isset($_COOKIE['mol0g1n']))
		{
			$string = explode("-", $_COOKIE['mol0g1n']);
			$id = $string[0];
			$idpw = $string[1];
			
			if(query_numrows("SELECT `id` FROM `admin` WHERE `id` = '{$id}'") != "1") die();
			
			$row = query_fetch_assoc("SELECT `username`, `fname`, `lname`, `password` FROM `admin` WHERE `id` = '{$id}'");
			
			$cookie = "{$id}|{$row['password']}";
			$cookie = $id."-".hash('sha512', $cookie);
			
			
			if($cookie == $_COOKIE['mol0g1n'])
			{		
				$_SESSION['a_ulogovan'] = true;
				$_SESSION['a_username'] = $row['username'];
				$_SESSION['a_ime'] = $row['fname'];
				$_SESSION['a_prezime'] = $row['lname'];
				$_SESSION['a_id'] = $id;
				
				$poruka = "Uspešan login preko kolacice.";
				alog($id, $poruka, $row['fname'].' '.$row['lname'], fuckcloudflare());
		
				$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
				$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Login" WHERE id="'.$_SESSION["a_id"].'"');				
			}
		}
		else
		{
			header( "Location: login.php" );
			die();
		}
	}
}


if (AdminUlogovan() == TRUE)
{
	$adminverifikacija = query_fetch_assoc( "SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'" );
	
	if (
		($adminverifikacija['username'] == $_SESSION['a_username']) ||
		($adminverifikacija['fname'] == $_SESSION['a_ime']) ||
		($adminverifikacija['lname'] == $_SESSION['a_prezime'])
		)
	{	
	
	}
	else
	{
		session_destroy();
		session_start();
		$_SESSION['msg-type'] = "error";
		$_SESSION['msg1'] = "Greška";		
		$_SESSION['msg2'] = "Sesije su istekle ili ste promenili podatke svog profila. Ulogujte se ponovo.";
		header( "Location: login.php" );
		die();
	}	
}	

function sifra($password)
{
	$jacihash = "8aDj3#s%Qd51Wc1gH*H@#fBNE*Wash";

	$password = mysql_real_escape_string($password);
	$password = htmlspecialchars($password);	
	$password = sha1(md5($jacihash . $password));
	
	return $password;
}

function logged_in() {
	if(isset($_SESSION['a_ulogovan'])) {
		return true;
	} else {
		if(isset($_COOKIE['a_zapamtime'])) {
			return true;
		} else {
			return false;
		}
	}
}

function novac($novac, $drzava)
{
	$clientcurrency = mysql_fetch_array(mysql_query("SELECT * FROM `billing_currency` WHERE `zemlja`='{$drzava}'"));
	if($drzava=="srb"){
		$novac = $novac * $clientcurrency['multiply'];	
		$novacc = number_format(floatval($novac), 2).' din';
		return $novacc;	
	}else if($drzava=="hr"){
		$novac = $novac * $clientcurrency['multiply'];	
		$novacc = number_format(floatval($novac), 2).' kn';
		return $novacc;		
	}else if($drzava=="bih"){
		$novac = $novac * $clientcurrency['multiply'];		
		$novacc = number_format(floatval($novac), 2).' km';
		return $novacc;		
	}else if($drzava == "cg" || $drzava == "other"){	
		$novacc = number_format(floatval($novac), 2).' eur';
		return $novacc;		
	}else if($drzava=="mk"){
		$novac = $novac * $clientcurrency['multiply'];	
		$novacc = number_format(floatval($novac), 2).' den';
		return $novacc;		
	}
	return FALSE;
}

function maketime ($taim,$type=0) {
	if ($type==0) $format = '%d.%m.%Y. %H:%M';
	else if ($type==1) $format = '%d.%m.%Y.';
	return strftime($format, $taim);
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

function pristup() {
	$sql = mysql_query("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'");
	$row = mysql_fetch_array($sql);
	
	if($row['status'] == "admin")
		return true;
		
	if($row['status'] == "support")
		return false;
}

function logout() {
	query_basic('UPDATE admin SET lastactivity = "'.($_SERVER['REQUEST_TIME']-60).'" WHERE id="'.$_SESSION["a_id"].'"');
	
	setcookie('mol0g1n', htmlentities($_COOKIE['mol0g1n'], ENT_QUOTES), time() - 3600);

	unset($_SESSION['a_ulogovan']);
	unset($_SESSION['a_username']);
	unset($_SESSION['a_ime']);
	unset($_SESSION['a_prezime']);
	unset($_SESSION['a_id']);

	header("Location: login.php");
	die();
}

function admin_ime_l($id) {
	$sql = "SELECT * FROM admin WHERE id = '$id'";
	$info = mysql_query($sql) or die(mysql_error());
	
	$info = mysql_fetch_assoc($info);
	
	$ime = '<a style="color: ' . $info['boja'] . '" href="admin_pregled.php?id=' . $info['id'] . '">' . sqli($info['fname']) . ' ' . sqli($info['lname']) . '</a>';
	
	return $ime;
}

function admin_ime($id) {
	$sql = "SELECT * FROM admin WHERE id = '$id'";
	$info = mysql_query($sql) or die(mysql_error());
	
	$info = mysql_fetch_assoc($info);
	
	$ime = '<span style="color: ' . $info['boja'] . '" >' . sqli($info['fname']) . ' ' . sqli($info['lname']) . '</span>';
	
	return $ime;
}

function admin_ime_c($id) {
	$sql = "SELECT * FROM admin WHERE id = '$id'";
	$info = mysql_query($sql) or die(mysql_error());
	
	$info = mysql_fetch_assoc($info);
	
	$ime = '<span style=\'color: ' . $info['boja'] . '\' >' . sqli($info['fname']) . ' ' . sqli($info['lname']) . '</span>';
	
	return $ime;
}

function admin_ime_p($id) {
	$sql = "SELECT * FROM admin WHERE id = '$id'";
	$info = mysql_query($sql) or die(mysql_error());

	$info = mysql_fetch_assoc($info);
		
	
	$data = "<div id='info_1'>".
			"<img src='".admin_avatar($id)."' style='width: 90px; height: 90px;' />".
			"</div>".
			"<div id='info_2'>".
			"<span style='color: " . $info['boja'] . "'>" . sqli($info['fname']) . " " . sqli($info['lname']) . "</span><br />".
			"<m>".$info['email']."</m><br />".
			"Reputacija: " . reputacija($id) . "<br />".
			"<a href='admin_pregled.php?id=".$id."' class='btn btn-mini btn-primary'>Profil</a> ".
			"</div>";	
	
	$ime = '<span class="autor" data-selector="true" data-content="'.$data.'" style="color: ' . $info['boja'] . '"><a style="color: ' . $info['boja'] . '">' . sqli($info['fname']) . ' ' . sqli($info['lname']) . '</a></span>';
	
	return $ime;
}

function admin_ime_p_l($id) {
	$sql = "SELECT * FROM admin WHERE id = '$id'";
	$info = mysql_query($sql) or die(mysql_error());
	
	$info = mysql_fetch_assoc($info);
		
	
	$data = "<div id='info_1'>".
			"<img src='".admin_avatar($id)."' style='width: 90px; height: 90px;' />".
			"</div>".
			"<div id='info_2'>".
			"<span style='color: " . $info['boja'] . "'>" . sqli($info['fname']) . " " . sqli($info['lname']) . "</span><br />".
			"<m>".$info['email']."</m><br />".
			"Reputacija: " . reputacija($id) . "<br />".
			"<a href='admin_pregled.php?id=".$id."' class='btn btn-mini btn-primary'>Profil</a> ".
			"</div>";	
	
	$ime = '<span class="autor" data-selector="true" data-content="'.$data.'" style="color: ' . $info['boja'] . '"><a style="color: ' . $info['boja'] . '" href="admin_pregled.php?id=' . $info['id'] . '">' . sqli($info['fname']) . ' ' . sqli($info['lname']) . '</a></span>';
	
	return $ime;
}

function log_ime($id) {
	$sql = "SELECT * FROM admin WHERE id = '$id' LIMIT 1";
	$res  = mysql_query($sql) or die(mysql_error());
	
	$check = mysql_num_rows($res);
	
	if($check > 0) {
		$info = mysql_fetch_assoc($res);

	$data = "<div id='info_1'>".
			"<img src='".admin_avatar($id)."' style='width: 90px; height: 90px;' />".
			"</div>".
			"<div id='info_2'>".
			"<span style='color: " . $info['boja'] . "'>" . sqli($info['fname']) . " " . sqli($info['lname']) . "</span><br />".
			"<m>".$info['email']."</m><br />".
			"Reputacija: " . reputacija($id) . "<br />".
			"<a href='admin_pregled.php?id=".$id."' class='btn btn-mini btn-primary'>Profil</a> ".
			"</div>";		
		
		$ime = '<span class="autor" data-selector="true" data-content="'.$data.'" style="color: ' . $info['boja'] . '">' . sqli($info['fname']) . ' ' . sqli($info['lname']) . '</span>';
	} else {
		$ime = 'Nije ulogovan';
	}
	
	return $ime;
}

function user_ime($id) {
	$sql = "SELECT * FROM klijenti WHERE klijentid = '$id'";
	$info = mysql_query($sql) or die(mysql_error());
	
	$info = mysql_fetch_assoc($info);
		
	
	$data = "<div id='info_1'>".
			"<img src='".user_avatar($id)."' style='width: 90px; height: 90px;' />".
			"</div>".
			"<div id='info_2'>".
			"<span style='color: #95A1AB'>" . sqli($info['ime']) . " " . sqli($info['prezime']) . "</span><br />".
			"<m>".$info['email']."</m><br />".
			"<a href='klijent.php?id=".$id."' class='btn btn-mini btn-primary'>Profil</a> ".
			"</div>";	
	
	$ime = '<span class="autor" data-selector="true" data-content="'.$data.'" style="color: #95A1AB"><a href="klijent.php?id='.$info['klijentid'].'">' . sqli($info['ime']) . ' ' . sqli($info['prezime']) . '</a></span>';
	
	return $ime;
}

function user_imesl($id) {
	$sql = "SELECT * FROM klijenti WHERE klijentid = '$id'";
	$info = mysql_query($sql) or die(mysql_error());
	
	$info = mysql_fetch_assoc($info);
		
	
	$data = "<div id='info_1'>".
			"<img src='".user_avatar($id)."' style='width: 90px; height: 90px;' />".
			"</div>".
			"<div id='info_2'>".
			"<font color='silver'>" . sqli($info['ime']) . " " . sqli($info['prezime']) . "</font><br />".
			"<m>".$info['email']."</m><br />".
			"<a href='klijent.php?id=".$id."' class='btn btn-mini btn-primary'>Profil</a> ".
			"</div>";	
	
	$ime = '<span class="autor" data-selector="true" data-content="'.$data.'" style="color: silver"><a href="klijent.php?id='.$info['klijentid'].'"><font color="A3A3A3">' . sqli($info['ime']) . ' ' . sqli($info['prezime']) . '</font></a></span>';
	
	return $ime;
}

function user_imes($id) {
	$sql = "SELECT * FROM klijenti WHERE klijentid = '$id'";
	$info = mysql_query($sql) or die(mysql_error());
	
	$info = mysql_fetch_assoc($info);
	
	$prezime = (strlen($info['prezime']) > 2) ? substr($info['prezime'],0,1).'.' : $info['prezime'];
		
	
	$data = "<div id='info_1'>".
			"<img src='".user_avatar($id)."' style='width: 90px; height: 90px;' />".
			"</div>".
			"<div id='info_2'>".
			"<span style='color: #95A1AB'>" . sqli($info['ime']) . " " . sqli($info['prezime']) . "</span><br />".
			"<m>".$info['email']."</m><br />".
			"<a href='klijent.php?id=".$id."' class='btn btn-mini btn-primary'>Profil</a> ".
			"</div>";	
	
	$ime = '<span class="autor" data-selector="true" data-content="'.$data.'" style="color: #95A1AB"><a href="klijent.php?id='.$info['klijentid'].'">' . sqli($info['ime']) . ' ' . sqli($prezime) . '</a></span>';
	
	return $ime;
}

function user_imep($id) {
	$sql = "SELECT * FROM klijenti WHERE klijentid = '$id'";
	$info = mysql_query($sql) or die(mysql_error());
	
	$info = mysql_fetch_assoc($info);
		
	
	$data = "<div id='info_1'>".
			"<img src='".user_avatar($id)."' style='width: 90px; height: 90px;' />".
			"</div>".
			"<div id='info_2'>".
			"<span style='color: #95A1AB'>" . sqli($info['ime']) . " " . sqli($info['prezime']) . "</span><br />".
			"<m>".$info['email']."</m><br />".
			"<a href='klijent.php?id=".$id."' class='btn btn-mini btn-primary'>Profil</a> ".
			"</div>";	
	
	$ime = '<span class="autor" data-selector="true" data-content="'.$data.'" style="color: #95A1AB"><a style="color: #95A1AB" href="klijent.php?id=' . $info['klijentid'] . '">' . sqli($info['ime']) . ' ' . sqli($info['prezime']) . '</a></span>';
	
	return $ime;
}

function admin_imep($id) {
	$sql = "SELECT * FROM admin WHERE id = '$id'";
	$info = mysql_query($sql) or die(mysql_error());
	
	$info = mysql_fetch_assoc($info);
		
	
	$data = "<div id='info_1'>".
			"<img src='".admin_avatar($id)."' style='width: 90px; height: 90px;' />".
			"</div>".
			"<div id='info_2'>".
			"<span style='color: #95A1AB'>" . sqli($info['ime']) . " " . sqli($info['prezime']) . "</span><br />".
			"<m>".$info['email']."</m><br />".
			"<a href='admin_pregled.php?id=".$id."' class='btn btn-mini btn-primary'>Profil</a> ".
			"</div>";	
	
	$ime = '<span class="autor" data-selector="true" data-content="'.$data.'" style="color: #95A1AB"><a style="color: #95A1AB" href="admin_pregled.php?id=' . $info['id'] . '">' . sqli($info['ime']) . ' ' . sqli($info['prezime']) . '</a></span>';
	
	return $ime;
}

function srvgrafik($ip, $port) {

	$ipport = $ip.'_'.$port;
	
	$grafik = '<span class="autor" data-selector="true" data-content="<img src=\'http://gb-hoster.me/grafik/'.$ipport.'.png\' />" style="color: #95A1AB">' . $ip . ':<m>' . $port . '</m></span>';
	
	return $grafik;
}

function admin_avatar($id) {
	$query = mysql_query("SELECT avatar FROM admin WHERE id = '{$id}'");
	$query = mysql_fetch_assoc($query);
	
	return "http://gb-hoster.me/admin/avatari/{$query['avatar']}";	
}

function user_avatar($id) {
	$query = mysql_query("SELECT avatar FROM klijenti WHERE klijentid = '{$id}'");
	$query = mysql_fetch_assoc($query);
	
	//return "http://gb-hoster.me/avatari/default.png";
	return "https://gb-hoster.me/img/a/default.png";
}

function alog($id, $message, $name, $ip) {
	$vreme = time();
	query_basic("INSERT INTO `logovi` SET
		`adminid` = '".$id."',
		`message` = '".$message."',
		`name` = '".$name."',
		`ip` = '".$ip."',
		`vreme` = '".$vreme."'");
	return TRUE;
}
/*
function alog($id, $message, $name, $ip) {
	$vreme = time();
	mysql_query("INSERT INTO `logovi` (adminid, message, name, ip, vreme) VALUES (" . $id . ", '" . $message . "', '" . $name . "', '" . $ip . "', '" . $vreme . "')") or die(mysql_error());
	
	return false;
}
*/
function klijent_log($cid, $poruka, $ime, $ip, $vreme)
{
	query_basic("INSERT INTO `logovi` SET
		`clientid` = '".$cid."',
		`message` = '".$poruka."',
		`name` = '".$ime."',
		`ip` = '".$ip."',
		`vreme` = '".$vreme."'");
	return TRUE;
}

function vreme($data) {
	$vreme = date("d.m.Y, H:i", $data);
	$time = explode(",", $vreme);
	
	$time[1] = "<m>" . $time[1] . "</m>";
	
	$datum = $time[0] . ',' . $time[1];
	
	return $datum;
}

function get_status($last)
{
	global $times;

	$status = '<span style="color: green;">Online</span>';
	if ($last < (time() - $times['idle']))
	{
		$status = '<m>Zauzet</m>';
	}
	if ($last < (time() - $times['offline']))
	{
		$status = '<span style="color: red;">Offline</span>';
	}		
	return $status;
}

function admin_status($id)
{
	$info = mysql_query("SELECT * FROM `admin` WHERE id = '".$id."'");
	$info = mysql_fetch_array($info);
	
	if($info['status'] == "admin") {
		$status = '<span style="color: '.$info['boja'].'">Vlasnik</span>';
	} else {
		$status = '<span style="color: '.$info['boja'].'">Radnik</span>';
	}
	return $status;
}

function status_tiketa($id)
{
	$info = mysql_query("SELECT * FROM `tiketi` WHERE id = '".$id."'");
	$info = mysql_fetch_array($info);
	
	if($info['status'] == "1") {
		$status = "<span style='color: #14C0D8'>Novi tiket</span>";
	}
	if($info['status'] == "2") {
		$status = "<span style='color: #15A031'>Odgovoren</span>";
	}
	if($info['status'] == "3") {
		$status = "<span style='color: red'>Zatvoren</span>";
	}
	if($info['status'] == "4") {
		$status = "<span style='color: #0A93CC'>Pročitan</span>";
	}
	if($info['status'] == "5") {
		$status = "<span style='color: #0A93CC'>Odgovoren - Klijent</span>";
	}
	if($info['status'] == "8") {
		$status = "<span style='color: #14C0D8'>Novi tiket</span>";
	}
	if($info['status'] == "10") {
		$status = "<span style='color: #14C0D8'>Prosledjen</span>";
	}	
	
	return $status;
}

function makeClickableLinks($text)
{

        $text = html_entity_decode($text);
        $text = " ".$text;
        $text = preg_replace("/(((f|ht){1}tp:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/",
                '<a href="\\1" target=_blank>\\1</a>', $text);
        $text = preg_replace("/(((f|ht){1}tps:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/",
                '<a href="\\1" target=_blank>\\1</a>', $text);
        $text = preg_replace("/([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/",
        '\\1<a href="http://\\2" target=_blank>\\2</a>', $text);
        $text = preg_replace('/([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4})/',
        '<a href="mailto:\\1" target=_blank>\\1</a>', $text);
        return $text;
}

function time_elapsed_A($secs){
    $bit = array(
        'y' => $secs / 31556926 % 12,
        'w' => $secs / 604800 % 52,
        'd' => $secs / 86400 % 7,
        'h' => $secs / 3600 % 24,
        'm' => $secs / 60 % 60,
        's' => $secs % 60
        );
        
    foreach($bit as $k => $v)
	{
        if($v > 0)
		{
			$ret[] = $v . $k;
		}
	}
	
	if(($secs % 60) <= 0) {
		$ret[] = '0 s';
	}
        
    return join(' ', $ret);
}
    

function time_elapsed_B($secs){
    $bit = array(
        ' godine'        => $secs / 31556926 % 12,
        ' nedelje'        => $secs / 604800 % 52,
        ' dana'        => $secs / 86400 % 7,
        ' sata'        => $secs / 3600 % 24,
        ' minuta'    => $secs / 60 % 60,
        ' sekunde'    => $secs % 60
        );
        
    foreach($bit as $k => $v){
        if($v > 1)
		{
			$ret[] = $v . $k . 's';
		}
        if($v == 1)
		{
			$ret[] = $v . $k;
		}
    }
    array_splice($ret, count($ret)-1, 0, 'and');
    $ret[] = 'ago.';
    
    return join(' ', $ret);
}
    

    
    
$nowtime = time();

/*
function infobox($id)
{
	$info = mysql_query("SELECT * FROM `admin` WHERE id = '".$id."'");
	$info = mysql_fetch_array($info);
	
	$data = "<div id='info_1'>".
			"<img src='".admin_avatar($id)."' style='width: 64px; height: 64px;' />".
			"</div>".
			"<div id='info_2'>".
			"<span style='color: " . $info['boja'] . "'>" . $info['fname'] . " " . $info['lname'] . "</span><br />".
			"<m>".$info['email']."</m><br />".
			"<button class='btn btn-mini btn-primary' type='button'>Profil</button>".
			"".
			"</div>";
	return $data;
}*/

function randomSifra($duzina)
{
	$karakteri = "abcdefghijkmnpqrstuvwxyz1234567890ABCDEFGHJKLMNPQRSTUVWXYZ";
	$string = str_shuffle($karakteri);
	$sifra = substr($string, 0, $duzina);
	return $sifra;
}

function randomBroj($duzina)
{
	$karakteri = "1234567890";
	$string = str_shuffle($karakteri);
	$sifra = substr($string, 0, $duzina);
	return $sifra;
}

function ssh_provera($ip, $sshport, $login, $password)
{
	$ssh = new Net_SSH2($ip, $sshport);

	if (!$ssh->login($login, $password))
	{
		$socket = fsockopen($ip, $sshport, $errno, $errstr, 100);

		if ($socket == FALSE) {
			return 'Unable to connect to '.$ip.' on port '.$port.': '.$errstr.' (Errno: '.$errno.')';
		}

		return 'Podatci za konektovanje na masinu nisu tacni.';
	}

	return $ssh;
}

/**
 * Validate Ip Addresses, by iceomnia
 *
 * Return TRUE if the IP is okay, FALSE if not.
 */
function validateIP($ip)
{
	$regex = "#[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}#";
	$validate = preg_match($regex, $ip);
	if ($validate == 1)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

function CheckBoxStatus($id) {
	$server = query_fetch_assoc("SELECT `box_id`, `ip_id` FROM `serveri` WHERE `id` = '".$id."'");
	$boxip = query_fetch_assoc("SELECT `ip` FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
	$box = query_fetch_assoc("SELECT `sshport` FROM `box` WHERE `boxid` = '".$server['box_id']."'");
	
	if($socket = @fsockopen($boxip['ip'], $box['sshport'], $errno, $errstr, 1)) {
		fclose($socket);
		return 'Online';
	} else {
		if($socket = @fsockopen($boxip['ip'], $box['sshport'], $errno, $errstr, 1)) {
			fclose($socket);
			return 'Online';
		} else {
			if($socket = @fsockopen($boxip['ip'], $box['sshport'], $errno, $errstr, 1)) {
				fclose($socket);
				return 'Online';
			} else {
				if($socket = @fsockopen($boxip['ip'], $box['sshport'], $errno, $errstr, 1)) {
					fclose($socket);
					return 'Online';
				} else {
					return 'Offline';
				}
			}
		}
	}
}

/**
 * getStatus
 *
 * Test if the specified [ip-port] is Online or Offline.
 *
 * Return string 'Online' or 'Offline'
 */
function getStatus($ip, $port)
{
	if($socket = @fsockopen($ip, $port, $errno, $errstr, 1))
	{
		fclose($socket);
		return 'Online';
	}
	else
	{
		###
		//Uncomment the line above for debugging
		//echo "$errstr ($errno)<br />\n";
		###
		return 'Offline';
	}
}

/**
 * Format the status
 *
 * Online / Offline -- Active / Inactive / Suspended / Pending -- Started / Stopped
 */
function formatStatus($status)
{
	switch ($status)
	{
		case 'Active':
			return "<span class=\"label label-success\">Aktivan</span>";

		case 'Inactive':
			return "<span class=\"label\">Neaktivan</span>";

		case 'Suspended':
			return "<span class=\"label label-warning\">Suspendovan</span>";

		case 'Pending':
			return "<span class=\"label label-warning\">Na čekanju</span>";

		case 'Online':
			return "<span class=\"label label-success\">Online</span>";

		case 'Offline':
			return "<span class=\"label label-important\">Offline</span>";

		case 'Started':
			return "<span class=\"label label-success\">Startovan</span>";

		case 'Stopped':
			return "<span class=\"label label-warning\">Stopiran</span>";

		default:
			return "<span class=\"label\">Default</span>";
	}
}

function bytesToSize($bytes, $precision = 2)
{
	$kilobyte = 1024;
	$megabyte = $kilobyte * 1024;
	$gigabyte = $megabyte * 1024;
	$terabyte = $gigabyte * 1024;

	if (($bytes >= 0) && ($bytes < $kilobyte)) {
		return $bytes . ' B';

	} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
		return round($bytes / $kilobyte, $precision) . ' KB';

	} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
		return round($bytes / $megabyte, $precision) . ' MB';

	} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
		return round($bytes / $gigabyte, $precision) . ' GB';

	} elseif ($bytes >= $terabyte) {
		return round($bytes / $terabyte, $precision) . ' TB';

	} else {
		return $bytes . ' B';
	}
}

function formatDate($timestamp)
{
	if ($timestamp == '0000-00-00 00:00:00' || $timestamp == 'Never')
	{
		return 'Never';
	}
	else
	{
		$dateTable = date_parse_from_format('Y-m-d H:i:s', $timestamp);
		return date('l | F j, Y | H:i', mktime($dateTable['hour'], $dateTable['minute'], $dateTable['second'], $dateTable['month'], $dateTable['day'], $dateTable['year']));
	}
}

function br_statistika($vrsta)
{
	if($vrsta == "klijenti")
	{
		$broj = mysql_query("SELECT `klijentid` FROM `klijenti`");
		$broj = mysql_num_rows($broj);
		return $broj;
	}
	else if($vrsta == "klijenti_server")
	{
		$broj = mysql_query("SELECT `user_id` FROM `serveri`");
		$broj = mysql_num_rows($broj);
		return $broj;
	}
	else if($vrsta == "klijenti_aktivacija")
	{
		$broj = mysql_query("SELECT `klijentid` FROM `klijenti` WHERE `status` = 'Aktivacija'");
		$broj = mysql_num_rows($broj);
		return $broj;
	}
	else if($vrsta == "klijenti_aktivni")
	{
		$broj = mysql_query("SELECT `klijentid` FROM `klijenti` WHERE `status` = 'Aktivan'");
		$broj = mysql_num_rows($broj);
		return $broj;
	}
	else if($vrsta == "akomentari")
	{
		$broj = mysql_query("SELECT `id` FROM `komentari`");
		$broj = mysql_num_rows($broj);
		
		return $broj;
	}
	else if($vrsta == "ckomentari")
	{
		$broj = mysql_query("SELECT `id` FROM `klijenti_komentari`");
		$broj = mysql_num_rows($broj);		
		return $broj;
	}
	else if($vrsta == "tiketi")
	{
		$broj = mysql_query("SELECT `id` FROM `tiketi`");
		$broj = mysql_num_rows($broj);		
		return $broj;
	}
	else if($vrsta == "uplate_nacekanje")
	{
		$broj = mysql_query("SELECT `id` FROM `billing` WHERE `status` = 'Na cekanju'");
		$broj = mysql_num_rows($broj);		
		return $broj;
	}
	else if($vrsta == "uplate_validne")
	{
		$broj = mysql_query("SELECT `id` FROM `billing` WHERE `status` = 'Leglo'");
		$broj = mysql_num_rows($broj);		
		return $broj;
	}
	else if($vrsta == "uplate_odbijene")
	{
		$broj = mysql_query("SELECT `id` FROM `billing` WHERE `status` = 'Nije leglo'");
		$broj = mysql_num_rows($broj);		
		return $broj;
	}
	else if($vrsta == "zarada")
	{
		$broj = mysql_query("SELECT sum(cena) as total_iznos FROM `serveri` WHERE `status` = 'Aktivan' AND `free` = 'Ne' AND `igra` != 3");
		$broj = mysql_fetch_array($broj);		
		return $broj['total_iznos'] . "e";
	}
	else if($vrsta == "serveri")
	{
		$broj = mysql_query("SELECT `id` FROM `serveri`");
		$broj = mysql_num_rows($broj);
		return $broj;
	}
	else if($vrsta == "serveri_aktivni")
	{
		$broj = mysql_query("SELECT `id` FROM `serveri` WHERE `status` = 'Aktivan'");
		$broj = mysql_num_rows($broj);
		return $broj;
	}
	else if($vrsta == "serveri_susp")
	{
		$broj = mysql_query("SELECT `id` FROM `serveri` WHERE `status` = 'Suspendovan'");
		$broj = mysql_num_rows($broj);
		return $broj;
	}
	else if($vrsta == "serveri_istekli")
	{
		$broj = mysql_query("SELECT `id` FROM `serveri` WHERE `status` = 'Istekao'");
		$broj = mysql_num_rows($broj);
		return $broj;
	}
	else if($vrsta == "ceka_uplatu")
	{
		$broj = mysql_query("SELECT `id` FROM `tiketi` WHERE `naslov` = 'Billing: Nova uplata - Ceka proveru'");
		$broj = mysql_num_rows($broj);
		return $broj;
	}
	return FALSE;
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

/**
 * query_basic -- mysql_query + mysql_num_rows
 *
 * Return broj kolona.
 */
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

/**
 * query_fetch_assoc -- mysql_query + mysql_fetch_assoc
 */
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
function file_size($size)
{
    $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
}

function srv_free($id)
{
	$server = query_fetch_assoc("SELECT `free` FROM `serveri` WHERE `id` = '{$id}'");

	if($server['free'] == "Da") $free = "<font style='color: green'>Da</font>";
	else if($server['free'] == "Ne") $free = "<font style='color: red'>Ne</font>";
	
	return $free;
}

function srv_istekao($srvid)
{
	$da = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$srvid."'");
	$datum = $da['istice'];
	$sdatum = date("Y-m-d", time());
	
	$d = strtotime($datum);
	$s = strtotime($sdatum);
	if($s > $d)
	{
		$d = $d-$s;
		$dana = floor($d/(60*60*24));
		
		$dana = explode("-", $dana);
		
		$istekao = "<span style='color: #FF0000'>".$datum."</span> (istekao pre <m>".$dana[1]."</m> dana)";
	} 
	else
	{
		$d = $d-$s;
		$dana = floor($d/(60*60*24));
		
		if($dana == "0")
		{
			$istekao = "<m>".$datum."</m> (istekao danas)";		
		}
		else
		{
			$istekao = "<m>".$datum."</m> (istice za <m>".$dana."</m> dana)";
		}
	}
	return $istekao;
}

function srv_status($status)
{
	if($status=="Aktivan")
	{
		$st = '<font color="#4ED000">Aktivan</font>';
		return $st;
	}
	else if($status=="Suspendovan")
	{
		$st = '<font color="#FF0000">Suspendovan</font>';
		return $st;
	}
	else if($status=="Istekao")
	{
		$st = '<m>Istekao</m>';
		return $st;
	}
	return FALSE;
}

function samo_vlasnik($id)
{
	$admin = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$id."'");
	
	if($admin['status'] != "admin")
	{
		$_SESSION['msg1'] = "Greška";
		$_SESSION['msg2'] = "Nemate pristup ovome!";
		$_SESSION['msg-type'] = "error";
		header("Location: index.php");
		die();
	}
	return 1;
}

function samo_fs($id)
{
	if($id == "1" OR $id == "2") return TRUE;
	else
	{
		$_SESSION['msg1'] = "Greška";
		$_SESSION['msg2'] = "Nemate pristup ovome!";
		$_SESSION['msg-type'] = "error";
		header("Location: index.php");
		die();
	}
}

function vlasnik($id)
{
	$admin = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$id."'");
	
	if($admin['status'] != "admin")
	{
		return 0;
	}
	else
	{
		return 1;
	}
}

function reputacija($id)
{
	$rep = query_fetch_assoc("SELECT SUM(rep) as repu FROM `reputacija` WHERE `adminid` = '".$id."'");
	$rep = $rep['repu'];
	if($rep == 0)
	{
		$rep = "<span style='color: #4ED000;'>0</span>";
	}
	else if($rep > 0)
	{
		$rep = "<span style='color: #4ED000;'>+".$rep."</span>";
	}
	else if($rep < 0)
	{
		$rep = "<span style='color: #FF0000;'>".$rep."</span>";
	}
	return $rep;
}

function reputacijab($rep)
{
	if($rep == 0)
	{
		$rep = "<span style='color: #4ED000;'>0</span>";
	}
	else if($rep > 0)
	{
		$rep = "<span style='color: #4ED000;'>+".$rep."</span>";
	}
	else if($rep < 0)
	{
		$rep = "<span style='color: #FF0000;'>".$rep."</span>";
	}
	return $rep;
}

function mccfg($find, $serverid)
{
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
	$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
	
	$fajl = "ftp://$server[username]:$server[password]@$boxip[ip]:21/server.properties";
				
	$contents = file_get_contents($fajl);
	
	$pattern = preg_quote($find, '/');

	$pattern = "/^.*$pattern.*\$/m";

	if(preg_match_all($pattern, $contents, $matches)){
		$text = implode("\n", $matches[0]);
		$g = explode('=', $text);
		return $g[1];
	}
}

function cscfg($find, $serverid)
{
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
	$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
	
	$fajl = "ftp://$server[username]:$server[password]@$boxip[ip]:21/cstrike/server.cfg";
				
	$contents = file_get_contents($fajl);
	
	$pattern = preg_quote($find, '/');

	$pattern = "/^.*$pattern.*\$/m";

	if(preg_match_all($pattern, $contents, $matches)){
	   $text = implode("\n", $matches[0]);
	   $g = explode('"', $text);
	   return $g[1];
	}
}

function sqli($text)
{
	$text = htmlspecialchars($text);	
	$text = mysql_real_escape_string($text);
	
	return $text;
}

function igra($igra)
{
	if($igra == "1") return "Counter-Strike 1.6";
	if($igra == "2") return "San Andreas Multiplayer";
	if($igra == "3") return "Minecraft";	
	if($igra == "4") return "Call Of Duty 4";
	if($igra == "5") return "Multi Theft Auto";
	if($igra == "6") return "Team Speak 3";
	if($igra == "7") return "FastDL";
    if($igra == "9") return "FiveM";
}


function resize($format2, $width = 150, $height = 150){
	list($w, $h) = getimagesize($_FILES['avatar']['tmp_name']);

	$ratio = max($width/$w, $height/$h);
	$h = ceil($height / $ratio);
	$x = ($w - $width / $ratio) / 2;
	$w = ceil($width / $ratio);
	
	$format = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));

	$path = "./avatari/{$_SESSION['klijentid']}{$format2}";

	$imgString = file_get_contents($_FILES['avatar']['tmp_name']);

	$image = imagecreatefromstring($imgString);
	$tmp = imagecreatetruecolor($width, $height);
	imagecopyresampled($tmp, $image,
  	0, 0,
  	$x, 0,
  	$width, $height,
  	$w, $h);

	switch ($_FILES['avatar']['type']) {
		case 'image/jpeg':
			imagejpeg($tmp, $path, 100);
			break;
		case 'image/png':
			imagepng($tmp, $path, 0);
			break;
		case 'image/gif':
			imagegif($tmp, $path);
			break;
		default:
			exit;
			break;
	}
	return $path;

	imagedestroy($image);
	imagedestroy($tmp);
}

function translateMCColors($text) {
    $dictionary = array(
        '[30;22m' => '</span><span style="color: #000000;">', // §0 - Black
        '[34;22m' => '</span><span style="color: #0000AA;">', // §1 - Dark_Blue
        '[32;22m' => '</span><span style="color: #00AA00;">', // §2 - Dark_Green
        '[36;22m' => '</span><span style="color: #00AAAA;">', // §3 - Dark_Aqua
        '[31;22m' => '</span><span style="color: #AA0000;">', // §4 - Dark_Red
        '[35;22m' => '</span><span style="color: #AA00AA;">', // §5 - Purple
        '[33;22m' => '</span><span style="color: #FFAA00;">', // §6 - Gold
        '[37;22m' => '</span><span style="color: #AAAAAA;">', // §7 - Gray
        '[30;1m' => '</span><span style="color: #555555;">', // §8 - Dakr_Gray
        '[34;1m' => '</span><span style="color: #5555FF;">', // §9 - Blue
        '[32;1m' => '</span><span style="color: #55FF55;">', // §a - Green
        '[36;1m' => '</span><span style="color: #55FFFF;">', // §b - Aqua
        '[31;1m' => '</span><span style="color: #FF5555;">', // §c - Red
        '[35;1m' => '</span><span style="color: #FF55FF;">', // §d - Light_Purple
        '[33;1m' => '</span><span style="color: #FFFF55;">', // §e - Yellow
        '[37;1m' => '</span><span style="color: #000;">', // §f - White
       
        '[0;30;22m' => '</span><span style="color: #000000;">', // §0 - Black
        '[0;34;22m' => '</span><span style="color: #0000AA;">', // §1 - Dark_Blue
        '[0;32;22m' => '</span><span style="color: #00AA00;">', // §2 - Dark_Green
        '[0;36;22m' => '</span><span style="color: #00AAAA;">', // §3 - Dark_Aqua
        '[0;31;22m' => '</span><span style="color: #AA0000;">', // §4 - Dark_Red
        '[0;35;22m' => '</span><span style="color: #AA00AA;">', // §5 - Purple
        '[0;33;22m' => '</span><span style="color: #FFAA00;">', // §6 - Gold
        '[0;37;22m' => '</span><span style="color: #AAAAAA;">', // §7 - Gray
        '[0;30;1m' => '</span><span style="color: #555555;">', // §8 - Dakr_Gray
        '[0;34;1m' => '</span><span style="color: #5555FF;">', // §9 - Blue
        '[0;32;1m' => '</span><span style="color: #55FF55;">', // §a - Green
        '[0;36;1m' => '</span><span style="color: #55FFFF;">', // §b - Aqua
        '[0;31;1m' => '</span><span style="color: #FF5555;">', // §c - Red
        '[0;35;1m' => '</span><span style="color: #FF55FF;">', // §d - Light_Purple
        '[0;33;1m' => '</span><span style="color: #FFFF55;">', // §e - Yellow
        '[0;37;1m' => '</span><span style="color: #000;">', // §f - White
       
        '[5m' => '', // Obfuscated
        '[21m' => '<b>', // Bold
        '[9m' => '<s>', // Strikethrough
        '[4m' => '<u>', // Underline
        '[3m' => '<i>', // Italic
       
        '[0;39m' => '</b></s></u></i></span>', // Reset
        '[0m' => '</b></s></u></i></span>', // Reset
        '[m' => '</b></s></u></i></span>', // End
    );
  
	$text = str_replace("<", htmlentities("<"), $text);
	$text = str_replace(">", htmlentities(">"), $text);
    $text = str_replace(array_keys($dictionary), $dictionary, $text);
   
    return '<span style="color: #FFFFFF;">'.$text;
}

?>
