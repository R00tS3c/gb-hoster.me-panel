<?php
require("konfiguracija.php");

$nowtime = time();

//$isk = query_fetch_assoc("SELECT `value` FROM `config` WHERE `setting` = 'iskljucen'");
if($isk['value'] == "1") { header("Location: iskljucen.php"); die(); }

if (klijentUlogovan() == FALSE) 
{
	if (!empty($return)) 
	{
		//if ($return === TRUE) 
		//{
			$_SESSION['msg'] = "Morate biti ulogovani.";
			header( "Location: index.php" );
			die();
		//}
	}
}

if (klijentUlogovan() == TRUE)
{
	klijent_activity($_SESSION['klijentid']);
	$klijentverifikacija = mysql_query( "SELECT `username`, `ime`, `prezime`, `token`, `lastip` FROM `klijenti` WHERE `klijentid` = '".$_SESSION['klijentid']."' && `status` = 'Aktivan'" );
	###
	$klijentverifikacija = mysql_fetch_assoc($klijentverifikacija);
	if (
			($klijentverifikacija['username'] != $_SESSION['klijentusername']) ||
			($klijentverifikacija['ime'] != $_SESSION['klijentime']) ||
			($klijentverifikacija['prezime'] != $_SESSION['klijentprezime']) ||
			($klijentverifikacija['token'] != session_id()) ||
			($klijentverifikacija['lastip'] != fuckcloudflare())
		)
	{
		session_destroy();
		session_start();
		$_SESSION['msg'] = "Sesije su istekle ili ste promenili podatke svog profila. Ulogujte se ponovo.";
		header( "Location: index.php" );
		die();
	}	
}	

//if($_SERVER['HTTP_HOST'] != DOMEN)
//{
	//die("Ovaj sajt/panel moze biti samo na domen ".DOMEN."");
//}

if (!function_exists("ssh2_connect")) die("SSH2 PHP extenzija nije instalirana.");
?>
