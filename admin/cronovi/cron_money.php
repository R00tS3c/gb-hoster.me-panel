<?php
$fajl = "login";

include($_SERVER['DOCUMENT_ROOT']."/konfiguracija.php");
include($_SERVER['DOCUMENT_ROOT']."/admin/includes.php");


error_reporting(E_ERROR | E_WARNING | E_PARSE);

echo "Starting Money Update !<br />\n<br />\n";

mysql_query("UPDATE billing_currency SET multiply = '1' WHERE zemlja = 'cg'");
echo "1 EUR = 1 EUR<br />\n";

$srb = convertCurrency("1", "EUR", "RSD");

if($srb == "0.00") {
	$srb = "118";
}

mysql_query("UPDATE billing_currency SET multiply = '".$srb."' WHERE zemlja = 'srb'");
echo "1 EUR = {$srb} RSD<br />\n";

$hr = convertCurrency("1", "EUR", "HRK");

if($hr == "0.00") {
	$hr = "7.404";
}

mysql_query("UPDATE billing_currency SET multiply = '".$hr."' WHERE zemlja = 'hr'");
echo "1 EUR = {$hr} HRK<br />\n";

$bih = convertCurrency("1", "EUR", "BAM");

if($bih == "0.00") {
	$bih = "1.96";
}

mysql_query("UPDATE billing_currency SET multiply = '".$bih."' WHERE zemlja = 'bih'");
echo "1 EUR = {$bih} BAM<br />\n";

$mk = convertCurrency("1", "EUR", "MKD");

if($mk == "0.00") {
	$mk = "61.859";
}

mysql_query("UPDATE billing_currency SET multiply = '".$mk."' WHERE zemlja = 'mk'");
echo "1 EUR = {$mk} MKD<br />\n<br />\n";

mysql_query("UPDATE billing_currency SET multiply = '1' WHERE zemlja = 'cg'");
echo "1 EUR = 1 EUR<br />\n";
/*
$srb_2 = convertCurrency("1", "RSD", "EUR");
mysql_query("UPDATE billing_currency SET multiply_2 = '".$srb_2."' WHERE zemlja = 'srb'");
echo "1 RSD = {$srb_2} EUR<br />\n";

$hr_2 = convertCurrency("1", "HRK", "EUR");
mysql_query("UPDATE billing_currency SET multiply_2 = '".$hr_2."' WHERE zemlja = 'hr'");
echo "1 HRK = {$hr_2} EUR<br />\n";

$bih_2 = convertCurrency("1", "BAM", "EUR");
mysql_query("UPDATE billing_currency SET multiply_2 = '".$bih_2."' WHERE zemlja = 'bih'");
echo "1 EUR = {$bih_2} BAM<br />\n";

$mk_2 = convertCurrency("1", "MKD", "EUR");
mysql_query("UPDATE billing_currency SET multiply_2 = '".$mk_2."' WHERE zemlja = 'mk'");
echo "1 MKD = {$mk_2} EUR<br />\n<br />\n";
*/
echo "Updated !<br />\n <br />\n<br />\n";

echo "Starting Price of mods Update !<br />\n<br />\n";

$mods = mysql_query("SELECT * FROM `modovi`");

while($row = mysql_fetch_array($mods)) {
	$igra = $row['igra'];
	$mod_ime = $row['ime'];
	
	$cenaslota = query_fetch_assoc("SELECT `cena` FROM `modovi` WHERE `igra` = '{$igra}'");
	$cenaslota = explode("|", $cenaslota['cena']);
	
	$currency_srb = query_fetch_assoc("SELECT `multiply` FROM `billing_currency` WHERE `zemlja` = 'srb'");
	$currency_mkd = query_fetch_assoc("SELECT `multiply` FROM `billing_currency` WHERE `zemlja` = 'mk'");
	$currency_hrk = query_fetch_assoc("SELECT `multiply` FROM `billing_currency` WHERE `zemlja` = 'hr'");
	$currency_bih = query_fetch_assoc("SELECT `multiply` FROM `billing_currency` WHERE `zemlja` = 'bih'");
	
	$cena = $cenaslota[1];
	
	$srb = $cena * $currency_srb['multiply'];
	$mk = $cena * $currency_mkd['multiply'];
	$hr = $cena * $currency_hrk['multiply'];
	$bih = $cena * $currency_bih['multiply'];
	
	$cenaslota_premium = query_fetch_assoc("SELECT `cena_premium` FROM `modovi` WHERE `igra` = '{$igra}'");
	$cenaslota_premium = explode("|", $cenaslota_premium['cena_premium']);
	
	$cena_premium = $cenaslota_premium[1];
	
	$srb_premium = $cena_premium * $currency_srb['multiply'];
	$mk_premium = $cena_premium * $currency_mkd['multiply'];
	$hr_premium = $cena_premium * $currency_hrk['multiply'];
	$bih_premium = $cena_premium * $currency_bih['multiply'];
	
	mysql_query("UPDATE modovi SET cena = '$srb|$cena|$mk|$hr|$bih' WHERE igra = $igra");
	mysql_query("UPDATE modovi SET cena_premium = '$srb_premium|$cena_premium|$mk_premium|$hr_premium|$bih_premium' WHERE igra = $igra");
	
	echo "Mod : $mod_ime <br />\n";
	echo "Lite lot = $srb [RSD] | $cena [EUR] | $mk [MKD] | $hr [HRK] | $bih [BAM]<br />\n<br />\n";
	echo "Premium slot = $srb_premium [RSD] | $cena_premium [EUR] | $mk_premium [MKD] | $hr_premium [HRK] | $bih_premium [BAM]<br />\n<br />\n";
}
/*
while($row = mysql_fetch_array($mods)) {
	$igra = $row['igra'];
	$mod_ime = $row['ime'];
	
	$cenaslota = query_fetch_assoc("SELECT `cena` FROM `modovi` WHERE `igra` = '{$igra}'");
	$cenaslota = explode("|", $cenaslota['cena']);
	
	$cena = $cenaslota[1];
	
	$srb = convertCurrency("$cena", "EUR", "RSD");
	$mk = convertCurrency("$cena", "EUR", "MKD");
	$hr = convertCurrency("$cena", "EUR", "HRK");
	$bih = convertCurrency("$cena", "EUR", "BAM");
	
	$cenaslota_premium = query_fetch_assoc("SELECT `cena_premium` FROM `modovi` WHERE `igra` = '{$igra}'");
	$cenaslota_premium = explode("|", $cenaslota_premium['cena_premium']);
	
	$cena_premium = $cenaslota_premium[1];
	
	$srb_premium = convertCurrency("$cena_premium", "EUR", "RSD");
	$mk_premium = convertCurrency("$cena_premium", "EUR", "MKD");
	$hr_premium = convertCurrency("$cena_premium", "EUR", "HRK");
	$bih_premium = convertCurrency("$cena_premium", "EUR", "BAM");
	
	mysql_query("UPDATE modovi SET cena = '$srb|$cena|$mk|$hr|$bih' WHERE igra = $igra");
	mysql_query("UPDATE modovi SET cena_premium = '$srb_premium|$cena_premium|$mk_premium|$hr_premium|$bih_premium' WHERE igra = $igra");
	
	echo "Mod : $mod_ime <br />\n";
	echo "Lite lot = $srb [RSD] | $cena [EUR] | $mk [MKD] | $hr [HRK] | $bih [BAM]<br />\n<br />\n";
	echo "Premium slot = $srb_premium [RSD] | $cena_premium [EUR] | $mk_premium [MKD] | $hr_premium [HRK] | $bih_premium [BAM]<br />\n<br />\n";
}
*/
echo "Updated !";

update_cron( );

function update_cron( ) {
	$CronName = basename($_SERVER["SCRIPT_FILENAME"], '.php');
	
	if( query_numrows( "SELECT * FROM `crons` WHERE `cron_name` = '$CronName'" ) == 1 ) {
		mysql_query( "UPDATE `crons` SET `cron_value` = '".date('Y-m-d H:i:s')."' WHERE `cron_name` = '$CronName'" );
	} else {
		mysql_query( "INSERT INTO `crons` SET `cron_name` = '".$CronName."', `cron_value` = '".date('Y-m-d H:i:s')."'" );
	}
}
