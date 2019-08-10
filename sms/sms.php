<?php

include $_SERVER['DOCUMENT_ROOT']."/connect_db.php";

$myfile = fopen("SendedWithGetMethod.ini", "w") or die("Unable to open file!");

fwrite($myfile, print_r($_GET, true));

fclose($myfile);

$billing_reports_enabled = false;

if( !in_array( $_SERVER[ 'REMOTE_ADDR' ], array( '1.2.3.4', '2.3.4.5', '54.72.6.23' ) ) ) {
	header( "HTTP/1.0 403 Forbidden" );
	die( "Error: Unknown IP" );
}

$secret = '6a125067ae40d8397ea0e89f8eaedce3';

if( empty( $secret ) || !check_signature( $_GET, $secret ) ) {
	header( "HTTP/1.0 404 Not Found" );
	die( "Error: Invalid signature" );
}

$sender = $_GET[ 'sender' ];

PrintReply( );

$user_id = userId( $_GET[ 'message' ] );
$user_name = userIme( $user_id );
$username = userName( $user_id );

$novac = ( $_GET[ 'price' ] * CalculatePrice( $_GET[ "country" ] ) ) / 100;
$novac = convertCurrency( $novac, $_GET[ 'currency' ], "EUR" );

$novac = round($novac, 2);

$link = "SMS UPLATA";
$drzava = $_GET[ "country" ];
$d_v = date("h.m.s, d-m-Y");

if( preg_match( "/OK/i", $_GET[ 'status' ] ) || ( preg_match( "/MO/i", $_GET[ 'billing_type' ] ) && preg_match( "/pending/i", $_GET[ 'status' ] ) ) ) {

	$in_base = mysql_query("INSERT INTO `uplate` (`id`, `klijentid`, `ime`, `novac`, `link`, `drzava`, `status`, `vreme`) VALUES (NULL, '$user_id', '$user_name', '$novac', '$link', '$drzava', '2', '$d_v');");
	$update = mysql_query("UPDATE `klijenti` SET `novac`=`novac`+'{$novac}' WHERE `username`='{$username}'");

} else if( preg_match( "/failed/i", $_GET[ 'status' ] ) ) {

	$in_base = mysql_query("INSERT INTO `uplate` (`id`, `klijentid`, `ime`, `novac`, `link`, `drzava`, `status`, `vreme`) VALUES (NULL, '$user_id', '$user_name', '$novac', '$link', '$drzava', '1', '$d_v');");

}

function PrintReply( ) {
	$productName = "GBH Point";
	$supportEmail = "support@gb-hoster.me";
	$companyName = "GB Hoster";

	echo "You purchased " . $productName . " for " . $_GET[ 'price' ] . " " . $_GET[ 'currency' ] . ". Thank You! Support: " . $supportEmail;
}

function CalculatePrice( $country ) {
	
	switch( $country ) {
		// Albania
		case "AL": 
			return 31;
		break;

		// Argentina
		case "AR": 
			return 10;
		break;

		// Armenia
		case "AM": 
			return 32;
		break;

		// Austria
		case "AT": 
			return 40;
		break;

		// Azerbaijan
		case "AZ": 
			return 26;
		break;

		// Bahrain
		case "BH": 
			return 37;
		break;

		// Belarus
		case "BY": 
			return 28;
		break;

		// Belgium
		case "BE": 
			return 50;
		break;

		// Bosnia and Herzegovina
		case "BA": 
			return 33;
		break;

		// Brazil
		case "BR": 
			return 15;
		break;

		// Bulgaria
		case "BG": 
			return 37;
		break;

		// Cambodia
		case "KH": 
			return 21;
		break;

		// Chile
		case "CL": 
			return 27;
		break;

		// Colombia
		case "CO": 
			return 13;
		break;

		// Costa Rica
		case "CR": 
			return 26;
		break;

		// Cote d'Ivoire
		case "CI": 
			return 26;
		break;

		// Croatia
		case "HR": 
			return 45;
		break;

		// Cyprus
		case "CY": 
			return 35;
		break;

		// Czech Republic
		case "CZ": 
			return 40;
		break;

		// Denmark
		case "DK": 
			return 65;
		break;

		// Ecuador
		case "EC": 
			return 10;
		break;

		// Egypt
		case "EG": 
			return 30;
		break;

		// Estonia
		case "EE": 
			return 50;
		break;

		// Ethiopia
		case "ET": 
			return 50;
		break;

		// Finland
		case "FI": 
			return 51;
		break;

		// France
		case "FR": 
			return 50;
		break;

		// Georgia
		case "GE": 
			return 27;
		break;

		// Germany
		case "DE": 
			return 51;
		break;

		// Greece
		case "GR": 
			return 49;
		break;

		// Guatemala
		case "GT": 
			return 13;
		break;

		// Hungary
		case "HU": 
			return 41;
		break;

		// India
		case "IN": 
			return 42;
		break;

		// Indonesia
		case "ID": 
			return 40;
		break;

		// Iraq
		case "IQ": 
			return 12;
		break;

		// Ireland
		case "IE": 
			return 56;
		break;

		// Jordan
		case "JO": 
			return 18;
		break;

		// Kazakhstan
		case "KZ": 
			return 30;
		break;

		// Kenya
		case "KE": 
			return 30;
		break;

		// Kosovo (Serbia)
		case "KV": 
			return 40;
		break;

		// Kuwait
		case "KW": 
			return 35;
		break;

		// Latvia
		case "LV": 
			return 35;
		break;

		// Lebanon
		case "LB": 
			return 28;
		break;

		// Lithuania
		case "LT": 
			return 48;
		break;

		// Luxembourg
		case "LU": 
			return 47;
		break;

		// Macedonia
		case "MK": 
			return 38;
		break;

		// Malaysia
		case "MY": 
			return 36;
		break;

		// Mexico
		case "MX": 
			return 25;
		break;

		// Moldova
		case "MD": 
			return 22;
		break;

		// Montenegro
		case "ME": 
			return 45;
		break;

		// Morocco
		case "MA": 
			return 32;
		break;

		// Myanmar
		case "MM": 
			return 40;
		break;

		// Netherlands
		case "NL": 
			return 55;
		break;

		// Norway
		case "NO": 
			return 41;
		break;

		// Oman
		case "OM": 
			return 36;
		break;

		// Pakistan
		case "PK": 
			return 50;
		break;

		// Palestine
		case "PS": 
			return 11;
		break;

		// Peru
		case "PE": 
			return 12;
		break;

		// Philippines
		case "PH": 
			return 30;
		break;

		// Poland
		case "PL": 
			return 42;
		break;

		// Portugal
		case "PT": 
			return 30;
		break;

		// Qatar
		case "QA": 
			return 33;
		break;

		// Romania
		case "RO": 
			return 31;
		break;

		// Russia
		case "RU": 
			return 37;
		break;

		// Saudi Arabia
		case "SA": 
			return 25;
		break;

		// Senegal
		case "SN": 
			return 26;
		break;

		// Serbia
		case "RS": 
			return 40;
		break;

		// Slovakia
		case "SK": 
			return 33;
		break;

		// Slovenia
		case "SI": 
			return 35;
		break;

		// Spain
		case "ES": 
			return 54;
		break;

		// Sweden
		case "SE": 
			return 58;
		break;

		// Switzerland
		case "CH": 
			return 54;
		break;

		// Taiwan
		case "TW": 
			return 50;
		break;

		// Tajikistan
		case "TJ": 
			return 16;
		break;

		// Thailand
		case "TH": 
			return 35;
		break;

		// Tunisia
		case "TN": 
			return 34;
		break;

		// Turkey
		case "TR": 
			return 43;
		break;

		// Ukraine
		case "UA": 
			return 26;
		break;

		// United Arab Emirates
		case "AE": 
			return 28;
		break;

		// Uruguay
		case "UY": 
			return 17;
		break;

		// Vietnam
		case "VN": 
			return 25;
		break;
	}
}

function check_signature( $params_array, $secret ) {
	ksort( $params_array );

	$str = '';
	foreach( $params_array as $k => $v ) {
		if( $k != 'sig' )
			$str .= "$k=$v";
	}

	$str .= $secret;
	$signature = md5( $str );

	return ( $params_array[ 'sig' ] == $signature );
}

?>