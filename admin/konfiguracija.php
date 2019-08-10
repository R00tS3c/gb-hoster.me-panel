<?php
include $_SERVER['DOCUMENT_ROOT']."/rootsec/project-security.php";
include $_SERVER['DOCUMENT_ROOT']."/ban.php";

if(!defined('HOST')) define("HOST", 'localhost');
if(!defined('DBNAME')) define("DBNAME", 'gbho_ster');
if(!defined('DBUSER')) define("DBUSER", 'gbho_ster'); 
if(!defined('DBPASS')) define("DBPASS", 'unikatinikadfusevi');
if(!defined('CHARSET')) define("CHARSET", 'utf8');
if(!defined('DOMEN')) define("DOMEN", 'www.gb-hoster.me');

if(!defined('CSDLL_HOST')) define("CSDLL_HOST", 'localhost');
if(!defined('CSDLL_DBUSER')) define("CSDLL_DBUSER", 'gbho_csdll'); 
if(!defined('CSDLL_DBPASS')) define("CSDLL_DBPASS", 'bBRggRJhLnPL');
if(!defined('CSDLL_DBNAME')) define("CSDLL_DBNAME", 'gbho_csdll');

// KONFIGURACIJA ZA CRONOVE
define("BRISANJE_ISTEKLIH_BANOVA_KLIJENTA", true);
define("ISTEKLI_SERVERI_STATUS", true); // Stavlja status 'ISTEKAO' na sve servere koji su istekli
define("SUSPEND_ISTEKLI_SERVERI", true); // Suspenduje servere koji su istekli
define("SUSPEND_ISTEKLI_SERVERI_VREME", 5); // Dani posle koliko da suspenduje istekle servere
define("AUTO_RESTART", true); // Auto restart

function tssrvadminpw($ip) {
if($ip=="185.38.151.130") {
    return "zivimzivot1337";
} else if($ip=="46.251.239.88")  {
    return "zivimzivot1337";
}
}

function fuckcloudflare()
{
	if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
        
		$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}else{
        $ip=$_SERVER['REMOTE_ADDR'];
	}   
    return $ip;
}
$config['paypal']['email'] = "brandji985@gmail.com";

// BOOST PODATKE
if(!defined('BOOST_HOST')) define("BOOST_HOST", 'localhost');
if(!defined('BOOST_DBNAME')) define("BOOST_DBNAME", 'csdownload_master');
if(!defined('BOOST_DBUSER')) define("BOOST_DBUSER", 'csdownload_ma'); 
if(!defined('BOOST_DBPASS')) define("BOOST_DBPASS", 'ety7adaru');

if(!defined('BOOST_MAX')) define("BOOST_MAX", '15');

date_default_timezone_set("Europe/Belgrade");

error_reporting(E_ERROR | E_WARNING | E_PARSE);

if(!file_exists('includes/funkcije.php'))
{
	$connect=@mysql_connect(HOST, DBUSER, DBPASS) or die('Cannot connect to database!');
	$select=mysql_select_db(DBNAME, $connect) or die('Cannot select database!');
}
else
{
	if(file_exists('./includes/funkcije.php')) include("./includes/funkcije.php");
}
?>
