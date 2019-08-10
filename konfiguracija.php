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
if(!defined('CSDLL_DBUSER')) define("CSDLL_DBUSER", 'gbhoster_csdll'); 
if(!defined('CSDLL_DBPASS')) define("CSDLL_DBPASS", 'bB;@R,?ggRJhLn-P_L');
if(!defined('CSDLL_DBNAME')) define("CSDLL_DBNAME", 'gbhoster_csdll');


// KONFIGURACIJA ZA CRONOVE
define("BRISANJE_ISTEKLIH_BANOVA_KLIJENTA", true);
define("ISTEKLI_SERVERI_STATUS", true); // Stavlja status 'ISTEKAO' na sve servere koji su istekli
define("SUSPEND_ISTEKLI_SERVERI", true); // Suspenduje servere koji su istekli
define("SUSPEND_ISTEKLI_SERVERI_VREME", 5); // Dani posle koliko da suspenduje istekle servere
define("AUTO_RESTART", true); // Auto restart


function fuckcloudflare()
{
	if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
        
		$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}else{
        $ip=$_SERVER['REMOTE_ADDR'];
	}   
    return $ip;
}

// BOOST PODATKE
//if(!defined('BOOST_HOST')) define("BOOST_HOST", '94.23.76.27');
//if(!defined('BOOST_DBNAME')) define("BOOST_DBNAME", 'zadmin_emax');
//if(!defined('BOOST_DBUSER')) define("BOOST_DBUSER", 'zadmin_emax'); 
//if(!defined('BOOST_DBPASS')) define("BOOST_DBPASS", 'yzupu7a6u');
if(!defined('BOOST_HOST')) define("BOOST_HOST", 'localhost');
if(!defined('BOOST_DBNAME')) define("BOOST_DBNAME", 'zadmin_emax');
if(!defined('BOOST_DBUSER')) define("BOOST_DBUSER", 'zadmin_emax'); 
if(!defined('BOOST_DBPASS')) define("BOOST_DBPASS", 'yzupu7a6u');

if(!defined('BOOST_MAX')) define("BOOST_MAX", '15');

date_default_timezone_set("Europe/Belgrade");

error_reporting(E_ERROR | E_WARNING | E_PARSE);

if(!file_exists('includes/funkcije.php'))
{
	$connect = @mysql_connect(HOST, DBUSER, DBPASS) or die('Cannot connect to database!');
	$select = mysql_select_db(DBNAME) or die('Cannot select database!');
	mysql_query('SET  NAMES \''.CHARSET.'\'',$connect);
}
else
{
	if(file_exists('./includes/funkcije.php')) include("./includes/funkcije.php");
	//if(file_exists('../../funkcije.php')) include("../../funkcije.php");
}
?>
