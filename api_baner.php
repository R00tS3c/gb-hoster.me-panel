<?php

$ip = $_GET["ip"];
$port = $_GET["port"];

//header('Content-Type: image/jpeg');

$image = 'https://gametracker.xyz/banner_normal/'.$ip.':'.$port.'/blue';
//$imagee = imagecreatefromstring(file_get_contents($image));

//imagejpeg($imagee);
header("Location: $image");

?>