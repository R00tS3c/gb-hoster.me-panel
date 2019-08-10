<?php
require("konfiguracija.php");

if(session_id() == '') {
    session_start();
}

$dostupne_teme = array('orange','blue');

/*
if(isset($_COOKIE['theme']))
{
	if(in_array($_COOKIE['theme'], $dostupne_teme))
	{
		$_SESSION['style'] = $_COOKIE['theme'];
	}
}
else
{
	if(!isset($_SESSION['style'])) $_SESSION['style'] = 'blue';
}

if(isset($_GET['style']) && $_GET['style'] != '')
{ 
	if(in_array($_GET['style'], $dostupne_teme))
	{       
		$_SESSION['style'] = $_GET['style'];
		setcookie('theme', $_GET['style'], time() + (86400 * 7 * 2));
	}
}
*/


$nowtime = time();

if(!empty($_SESSION['a_id']))
{
	$isk = query_fetch_assoc("SELECT `value` FROM `config` WHERE `setting` = 'iskljucen'");
	if($isk['value'] == "1") { header("Location: iskljucen.php"); die(); }
}



//if($_SERVER['HTTP_HOST'] != DOMEN)
//{
	//die("Ovaj sajt/panel moze biti samo na domen ".DOMEN."");
//}

if (!function_exists("ssh2_connect")) die("SSH2 PHP extenzija nije instalirana.");

?>
