<?php

function CSDownloadInfo($Type) {
	$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
	$url = "http://cs.gb-hoster.me/api/index.php";
	
	$xml = file_get_contents($url, false, $context);
	$xml = simplexml_load_string($xml) or die("Error: Cannot create object");
	
	if(isset($xml->apiError) == 1) {
		echo $xml->errorText;
		die();
	}
	switch($Type) {
		case 'download_count':
			return $xml->download_count;
		break;
	}
}

function is_url_exist($url) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	if($code == 200) {
		$status = true;
	} else {
		$status = false;
	}
	
	curl_close($ch);
	return $status;
}

?>