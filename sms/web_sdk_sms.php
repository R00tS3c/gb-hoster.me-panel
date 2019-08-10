<?php

include $_SERVER['DOCUMENT_ROOT']."/connect_db.php";

$myfile = fopen("SendedWithGetMethod.ini", "w");

fwrite($myfile, print_r($_GET, true));

fclose($myfile);

$billing_reports_enabled = false;
/*
if( !in_array( $_SERVER[ 'REMOTE_ADDR' ], array( '1.2.3.4', '2.3.4.5', '54.72.6.23' ) ) ) {
	header( "HTTP/1.0 403 Forbidden" );
	die( "Error: Unknown IP" );
}
*/
$secret = '375db3f3a30407ef762eaecd91e5ee7c';
/*
if( empty( $secret ) || !check_signature( $_GET, $secret ) ) {
	header( "HTTP/1.0 404 Not Found" );
	die( "Error: Invalid signature" );
}
*/
$sender = $_GET[ 'sender' ];

PrintReply( );

$user_id = userIdId( $_GET[ 'cuid' ] );
$user_name = userIme( $user_id );
$username = userName( $user_id );

$novac = convertCurrency( $_GET[ "revenue" ], $_GET[ 'currency' ], "EUR" );

$novac = round($novac, 2);

$novac = str_replace(",", ".", $novac);

$link = "SMS UPLATA";
$drzava = $_GET[ "country" ];
$d_v = date("h.m.s, d-m-Y");

if( $_GET[ 'status' ] == "completed" ) {

	$in_base = mysql_query("INSERT INTO `uplate` (`id`, `klijentid`, `ime`, `novac`, `link`, `drzava`, `status`, `vreme`) VALUES (NULL, '$user_id', '$user_name', '$novac', '$link', '$drzava', '2', '$d_v');");
	$update = mysql_query("UPDATE `klijenti` SET `novac`=`novac`+'{$novac}' WHERE `username`='{$username}'");

} else {

	$in_base = mysql_query("INSERT INTO `uplate` (`id`, `klijentid`, `ime`, `novac`, `link`, `drzava`, `status`, `vreme`) VALUES (NULL, '$user_id', '$user_name', '$novac', '$link', '$drzava', '1', '$d_v');");

}

function PrintReply( ) {
	$supportEmail = "support@gb-hoster.me";
	$companyName = "GB Hoster";

	echo "You purchased " . $_GET[ 'amount' ] . " " . $_GET[ 'credit_name' ] . " for " . $_GET[ 'price' ] . " " . $_GET[ 'currency' ] . ". Thank You! Support: " . $supportEmail;
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