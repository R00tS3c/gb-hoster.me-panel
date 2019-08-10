<?php
	$randomnr = "MH".rand(1000, 9999);
	$_SESSION['captchamd5'] = md5($randomnr);
	$_SESSION['captcha'] = $randomnr;
?>