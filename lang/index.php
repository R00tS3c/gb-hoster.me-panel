<?php
header('Cache-control: private'); // IE 6 FIX

if (isset($_GET['jezik'])) {
	$jezik = $_GET['jezik'];

	$_SESSION['jezik'] = $jezik;

	setcookie("jezik", $jezik, time() + (3600 * 24 * 30));
} else if(isset($_SESSION['jezik'])) {
	$jezik = $_SESSION['jezik'];
} else if(isset($_COOKIE['jezik'])) {
	$jezik = $_COOKIE['jezik'];
} else {
	$jezik = 'en';
}

switch ($jezik) {
	case 'en':
	$lang_file = 'jezik.en.php';
	break;

	case 'rs':
	$lang_file = 'jezik.rs.php';
	break;

	default:
	$lang_file = 'jezik.en.php';

}

include_once 'jezik/'.$lang_file;

?>