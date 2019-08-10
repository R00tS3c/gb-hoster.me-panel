<?php  
require($_SERVER['DOCUMENT_ROOT'].'/PHPMailer-master/class.phpmailer.php');

$mail = new PHPMailer;

$mail->IsSMTP();                                    // Set mailer to use SMTP
$mail->Host = 'cp.gamehoster.biz';  				// Specify main and backup server
$mail->SMTPAuth = true;                             // Enable SMTP authentication
$mail->Username = 'info@gamehoster.biz';            // SMTP username
$mail->Password = 'AbXTtJlN4E';                    	// SMTP password
$mail->SMTPSecure = 'tls';                          // Enable encryption, 'ssl' also accepted

$mail->From = 'info@gamehoster.biz';
$mail->FromName = 'gamehoster.biz';


$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->IsHTML(true);  
?>