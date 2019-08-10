<?php
function fivemstatus ($ip, $port) {
$content = json_decode(file_get_contents("http://".$ip.":".$port."/info.json"), true);
if($content):
    return true;
else:
    return false;
endif;
}

function fivemplayers ($ip, $port) {
    $igraci = file_get_contents("http://".$ip.":".$port."/players.json");
	$content = json_decode($igraci, true);
	$broj = count($content);
	return $broj;
}

?>