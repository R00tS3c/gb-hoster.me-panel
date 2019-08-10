<?php
session_start();
include("konfiguracija.php");
include("includes.php");

$tiket_id = mysql_real_escape_string($_GET['id']);

$tiket_gledaju = mysql_query("SELECT * FROM admin WHERE lastactivityname = 'Pregled tiketa #".$tiket_id."'");

$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Pregled tiketa #'.$tiket_id.'" WHERE id="'.$_SESSION["a_id"].'"');

$tiket = query_fetch_assoc("SELECT * FROM `tiketi` WHERE `id` = '{$tiket_id}'");

if(mysql_num_rows($tiket_gledaju) > 1) {
	$ext = ", ";
} else {
	$ext = "";
}

?>	
		Pregledava: <?php	while($row = mysql_fetch_array($tiket_gledaju)) {	
								echo admin_ime_p($row['id']).''.$ext;
							}							
					?>			
					