<?php
	include("connect_db.php");

	$save_last = date("h.m.s / d-m-Y");

	session_destroy();

	header("Location: /home");
	die();
?>