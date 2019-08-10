<?php
//header("Location: /logout.php");
include $_SERVER['DOCUMENT_ROOT']."/ban.php";
error_reporting(0);
session_start();
ob_start();

if(!defined('DB_HOST')) define("DB_HOST", 'localhost');
if(!defined('DB_NAME')) define("DB_NAME", 'gbho_ster');
if(!defined('DB_USER')) define("DB_USER", 'gbho_ster'); 
if(!defined('DB_PASS')) define("DB_PASS", 'unikatinikadfusevi');

//master
define('MASTER_HOST', '185.164.32.15');
define('MASTER_USER', 'master');
define('MASTER_PASS', 'djVVE5zcznh5sbwA');
define('MASTER_NAME', 'master');

//game mysql
define('GAME_HOST', '185.164.32.15');
define('GAME_USER', 'master');
define('GAME_PASS', 'djVVE5zcznh5sbwA');

if (!$db=@mysql_connect(DB_HOST, DB_USER, DB_PASS)) {
	die ("<b>Doslo je do greske prilikom spajanja na MySQL...</b>");
}

if (!mysql_select_db(DB_NAME, $db)) {
	die ("<b>Greska prilikom biranja baze!</b>");
}

//MASTER CONNECT
$mdb = new mysqli(MASTER_HOST, MASTER_USER, MASTER_PASS, MASTER_NAME);

/* Jezik */
$stats_klijenti			= mysql_query("SELECT * FROM `klijenti`");

$stats_tiketi 			= mysql_query("SELECT * FROM `tiketi`");

$stats_server 			= mysql_query("SELECT * FROM `serveri`");

$stats_servera_aktivnih = mysql_query("SELECT * FROM `serveri` WHERE `startovan` = 1");


$stats_masine 			= mysql_query("SELECT * FROM `box`");

$dl_link_cs = "http://cs.gb-hoster.me/"; // Link counter-strike 1.6

function vreme($data) {
	$vreme = date("d.m.Y, H:i", $data);
	$time = explode(",", $vreme);
	
	$time[1] = "<z>" . $time[1] . "</z>";
	
	$datum = $time[0] . ',' . $time[1];
	
	return $datum;
}

if(isset($srw_file)) {
	function ipadresa($srvid) {
		$port = query_fetch_assoc("SELECT `port`, `ip_id`, `box_id` FROM `serveri` WHERE `id` = '".$srvid."'");
		$ip = query_fetch_assoc("SELECT `ip` FROM `boxip` WHERE `ipid` = '".$port['ip_id']."'");
		$row = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$srvid."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$row['box_id']."'");
		
		$ip = $ip['ip'].":".$port['port'];
		
		if($row['igra'] == "7") {
			$ip = $box['fdl_link']."/".$row['username']."/cstrike/";
		}
		
		return $ip;
	}
	
	function query_numrows($query) {
		$result = mysql_query($query);
		if ($result == FALSE) {
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

	function query_fetch_assoc($query) {
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
}

$dostupni_jezici = array('en','sr','de');

if(isset($_COOKIE['jezik']))
{
	if(in_array($_COOKIE['jezik'], $dostupni_jezici))
	{
		$_SESSION['jezik'] = $_COOKIE['jezik'];
		if($_SESSION['jezik'] == "sr") include('lang/lang.sr.php');
		else if($_SESSION['jezik'] == "en") include('lang/lang.en.php');
		else if($_SESSION['jezik'] == "de") include('lang/lang.de.php');
		else include('lang/lang.sr.php');
	}
}
else
{
	if(!isset($_SESSION['jezik'])) 
	{
		$_SESSION['jezik'] = 'sr';
		include('lang/lang.sr.php');
	}
	else
	{
		$_SESSION['jezik'] = 'sr';
		include('lang/lang.sr.php');		
	}
}

if(isset($_GET['jezik']) && $_GET['jezik'] != '')
{ 
	if(in_array($_GET['jezik'], $dostupni_jezici))
	{       
		$_SESSION['jezik'] = $_GET['jezik'];
		setcookie('jezik', $_GET['jezik'], time() + (86400 * 7 * 2));

		if($_SESSION['jezik'] == "sr") include('lang/lang.sr.php');
		else if($_SESSION['jezik'] == "en") include('lang/lang.en.php');
		else if($_SESSION['jezik'] == "de") include('lang/lang.de.php');
		else include('lang/lang.sr.php');	
	}
}

function is_login(){
	if(isset($_SESSION['userid'])){
		return true;
	} else {
		return false;
	}
}

function is_pin(){
	if(isset($_SESSION['_pin'])){
		return true;
	} else {
		return false;
	}
}

function is_demo(){
	if($_SESSION[userid]==1) {
		return false;
	} else {
		return true;
	}
}

if (isset($_SESSION['userid'])) {
$time_online = time();
mysql_query("UPDATE `klijenti` SET `lastactivity` = '$time_online' WHERE `klijentid` = '$_SESSION[userid]'");
}
/*
if((get_client_ip() == "178.79.32.9") || (isset($_SESSION['userid']) && $_SESSION['userid'] == 308)) {
	die("Banovan si dok ti podrska ne startuje server!");
}
*/

function get_client_ip() {
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


function clientIpHost($client_ip) {
	$cl_ip_host = json_decode(file_get_contents("https://ipinfo.io/$client_ip/json/"));
	if ($cl_ip_host == true) {
		return $cl_ip_host->hostname;
	} else {
		return "HostName nije pronadjen.";
	}
}


function randomSifra($duzina){
	$karakteri = "abcdefghijkmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWXYZ";
	$string = str_shuffle($karakteri);
	$sifra = substr($string, 0, $duzina);
	return $sifra;
}

function randomNumber($duzina){
	$karakteri = "1234567890";
	$string = str_shuffle($karakteri);
	$sifra = substr($string, 0, $duzina);
	return $sifra;
}

/* Provera SQL */

function sqli($text) {
	$text = mysql_real_escape_string($text);
	$text = htmlspecialchars($text);
	return $text;
}

/* GP - IGRA */

function gp_igra($game_id) {
	if ($game_id == "1") {
		return "Counter-Strike 1.6";
	} else if ($game_id == "1") {
		return 'Counter-Strike 1.6.';
	} else if ($game_id == "2") {
		return 'SAMP';
	} else if ($game_id == "3") {
		return 'Minecraft';
	} else if ($game_id == "4") {
		return 'Call of duty 4: Modern warfare';
	} else if ($game_id == "5") {
		return 'Counter-Strike: GO';
	} else if ($game_id == "7") {
		return 'FastDL';
	} else if ($game_id == "6") {
		return 'TeamSpeak 3';
	} else if ($game_id == "9") {
		return 'FiveM';
	}
	else {
		return "Game?";
	}
}

/* LOKACIJA SERVERA */

function gp_lokacija($server_ip) {
	$location_ip = json_decode(file_get_contents("https://freegeoip.net/json/".$server_ip));
	if ($location_ip === true) {
		return $location_ip->country_code;
	} else {
		return "Lokacija nije pronadjena.";
	}
}

/* IME MODA */

function mod_ime($mod_id) {
	$mod_name = mysql_fetch_assoc(mysql_query("SELECT * FROM `modovi` WHERE `id` = '$mod_id'"));
	if ($mod_name == true) {
		return $mod_name['ime'];
	} else {
		return "Ne mogu pronaci mod.";
	}
}

/* IME KLIJENTA */

function userId($user_ime) {
	$id_usera = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `username` = '$user_ime'"));
	if ($id_usera == true) {
		return $id_usera['klijentid'];
	} else {
		return 1;
	}
}

function userIdId($user_id) {
	$id_usera = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$user_id'"));
	if ($id_usera == true) {
		return $id_usera['klijentid'];
	} else {
		return 1;
	}
}

function userIme($user_id) {
	$ime_usera = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$user_id'"));
	if ($ime_usera == true) {
		return $ime_usera['ime'].' '.$ime_usera['prezime'];
	} else {
		return "Ne mogu pronaci ime.";
	}
}

function userName($user_id) {
	$ime_usera = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$user_id'"));
	if ($ime_usera == true) {
		return $ime_usera['username'];
	} else {
		return "Ne mogu pronaci ime.";
	}
}

function adminIme($user_id) {
	$ime_usera = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE `id` = '$user_id'"));
	if ($ime_usera == true) {
		return $ime_usera['fname'].' '.$ime_usera['lname'];
	} else {
		return "Ne mogu pronaci ime.";
	}
}


function userEmail($user_id) {
	$email_usera = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$user_id'"));
	if ($email_usera == true) {
		return $email_usera['email'];
	} else {
		return "Ne mogu pronaci email.";
	}
}


/* KLIJENTOV NOVAC */

function userMoney($user_id) {
	$email_usera = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$user_id'"));
	if ($email_usera == true) {
		return $email_usera['novac'];
	} else {
		return "Ne mogu pronaci klijentov novac.";
	}
}

/* KLIJENTOV NOVAC UPDATE*/

function userMoneyUpdate($user_id, $value, $sign) {
	if($sign == true) {
		$novac = (userMoney($user_id)+$value);
	} else {
		$novac = (userMoney($user_id)-$value);
	}
	
	$update = mysql_query("UPDATE `klijenti` SET `novac` = '$novac' WHERE `klijentid` = '$user_id'");
	
	return $update;
}


/* KLIJENTOVA VALUTA */

function userCurrency($user_id) {
	$email_usera = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$user_id'"));
	if ($email_usera == true) {
		return $email_usera['currency'];
	} else {
		return "Ne mogu pronaci klijentovu valutu.";
	}
}

/* KLIJENTOVA DRZAVA */

function userCountry($user_id) {
	$email_usera = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$user_id'"));
	if ($email_usera == true) {
		return $email_usera['zemlja'];
	} else {
		return "Ne mogu pronaci klijentovu zemlju.";
	}
}

/* KLIJENTOV NOVAC BEZ VALUTE */

function userMoneyWithoutValute($novac, $drzava) {
	$clientcurrency = mysql_fetch_array(mysql_query("SELECT * FROM `billing_currency` WHERE `zemlja`='{$drzava}'"));
	if($drzava=="srb"){
		$novac = $novac * $clientcurrency['multiply'];	
		$novacc = $novac;
		return $novacc;	
	} else if($drzava=="hr"){
		$novac = $novac * $clientcurrency['multiply'];		
		$novacc = $novac;
		return $novacc;		
	} else if($drzava=="bih"){
		$novac = $novac * $clientcurrency['multiply'];		
		$novacc = $novac;
		return $novacc;		
	} else if($drzava=="cg" || $drzava == "other"){
		$novacc = $novac;
		return $novacc;		
	} else if($drzava=="mk"){
		$novac = $novac * $clientcurrency['multiply'];		
		$novacc = $novac;
		return $novacc;		
	}
	return FALSE;
}

/* KLIJENTOV NOVAC + VALUTA */

function userMoneyWithValute($novac, $drzava) {
	$clientcurrency = mysql_fetch_array(mysql_query("SELECT * FROM `billing_currency` WHERE `zemlja`='{$drzava}'"));
	if($drzava == "srb"){
		$novac = $novac*$clientcurrency['multiply'];	
		$novacc = number_format(floatval($novac), 2).' RSD';
		return $novacc;	
	}else if($drzava == "hr"){
		$novac = $novac*$clientcurrency['multiply'];	
		$novacc = number_format(floatval($novac), 2).' HRK';
		return $novacc;		
	}else if($drzava == "bih"){
		$novac = $novac*$clientcurrency['multiply'];		
		$novacc = number_format(floatval($novac), 2).' BAM';
		return $novacc;		
	}else if($drzava == "cg" || $drzava == "other"){	
		$novacc = number_format(floatval($novac), 2).' EUR';
		return $novacc;		
	}else if($drzava == "mk"){
		$novac = $novac*$clientcurrency['multiply'];	
		$novacc = number_format(floatval($novac), 2).' MKD';
		return $novacc;		
	}
	return FALSE;
}

function lastLogin($user_id) {
	$lastlogin = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$user_id'"));
	if ($lastlogin == true) {
		return $lastlogin['lastlogin'];
	} else {
		return "Ne mogu pronaci datum.";
	}
}


function userAvatar($user_id) {
	$userAvatar = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$user_id'"));
	if ($userAvatar["avatar"] == "default.png") {
		return "/img/a/default.png";
	} else {
		return $userAvatar['avatar'];
	}
}
function rootlogin($ip) {
        require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/phpseclib/Crypt/AES.php");

        $box = mysql_fetch_assoc(mysql_query("SELECT * FROM `box` WHERE `ip` = '$ip'"));
        
        $aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);
		$sifra = $aes->decrypt($box['password']);

        return $sifra;
}

function ispravi_text($poruka) {
	return htmlspecialchars(mysql_real_escape_string(addslashes($poruka)));
}
function ispravi_text_sql($poruka) {
	return htmlspecialchars(addslashes($poruka));
}
function ispravi_text_html($poruka) {
	return htmlspecialchars($poruka);
}

function convertCurrency($amount,$from_currency,$to_currency) {
	
	$from_Currency = urlencode($from_currency);
	$to_Currency = urlencode($to_currency);
	$amount = urlencode($amount);
	$query =  "{$from_Currency}_{$to_Currency}";
	
	$json = file_get_contents("https://free.currencyconverterapi.com/api/v6/convert?q={$query}&compact=ultra&apiKey=cca74d7dc618d06046df");
	$obj = json_decode($json, true);
	
	$val = floatval($obj["$query"]);
	
	$total = $val * $amount;
	return number_format($total, 2, '.', '');
}

?>
