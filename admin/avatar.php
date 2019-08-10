<?php
session_start();

$naslov = "Avatar";
$fajl = "gp";
$return = "gp.php";
$ucp = "gp-topserveri";

include("includes.php");

if(empty($_GET['mode'])) die("Morate izabrati ?mode=XX");
if(empty($_GET['id'])) die("Morate izabrati &id=XX");

if($_GET['mode'] == "a") {
	$id = mysql_real_escape_string($_GET['id']);
	$id = htmlspecialchars($id);
	if(!is_numeric($id)) die("(!) Greska");

	$query = mysql_query("SELECT avatar FROM admin WHERE id = '{$id}'");
	if(mysql_num_rows($query) != 1) die("Greska");

	$query = mysql_fetch_assoc($query);
	
	$imagepath = "http://gb-hoster.me/admin/avatari/{$query['avatar']}";
	
	$ext = explode(".", $query['avatar']);
	
	if($ext[1] == "jpeg" || $ext[1] == "jpg") {
		$image = imagecreatefromjpeg($imagepath);
		header('Content-Type: image/jpeg');
		imagejpeg($image);			
	} else if($ext[1] == "png") {
		$image = imagecreatefrompng($imagepath);
		header('Content-Type: image/png');
		imagepng($image);				
	} else if($ext[1] == "gif") {
		$image = imagecreatefromgif($imagepath);
		header('Content-Type: image/gif');
		imagegif($image);
	}
} else if($_GET['mode'] == "c") {
	$id = mysql_real_escape_string($_GET['id']);
	$id = htmlspecialchars($id);
	if(!is_numeric($id)) die("(!) Greska");

	$query = mysql_query("SELECT avatar FROM klijenti WHERE klijentid = '{$id}'");
	if(mysql_num_rows($query) != 1) die("Greska");

	$query = mysql_fetch_assoc($query);
	
	$imagepath = "http://gb-hoster.me/avatari/{$query['avatar']}";
	
	$ext = explode(".", $query['avatar']);
	
	if($ext[1] == "jpeg" || $ext[1] == "jpg") {
		$image = imagecreatefromjpeg($imagepath);
		header('Content-Type: image/jpeg');
		imagejpeg($image);			
	} else if($ext[1] == "png") {
		$image = imagecreatefrompng($imagepath);
		header('Content-Type: image/png');
		imagepng($image);				
	} else if($ext[1] == "gif") {
		$image = imagecreatefromgif($imagepath);
		header('Content-Type: image/gif');
		imagegif($image);
	}
}
?>