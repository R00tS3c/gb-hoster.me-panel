<?php

	require_once( "./lib/cryptobox.class.php" );

	$userID 		= "1";				// place your registered userID or md5(userID) here (user1, user7, uo43DC, etc).
										// you don't need to use userID for unregistered website visitors
										// if userID is empty, system will autogenerate userID and save in cookies
	$userFormat		= "COOKIE";			// save userID in cookies (or you can use IPADDRESS, SESSION)
	$orderID 		= "invoice000383";	// invoice number - 000383
	$amountUSD		= 2.21;				// invoice amount - 2.21 USD
	$period			= "NOEXPIRY";		// one time payment, not expiry
	$def_language	= "en";				// default Payment Box Language
	$public_key		= "41573AA8wITtBitcoin77BTCPUBDhmXPs7QqiPS1Pjh16GQKag"; // from gourl.io
	$private_key	= "41573AA8wITtBitcoin77BTCPRVE3VhidXDSDRfMi7E6F0qsn0";// from gourl.io

	// *** For convert Euro/GBP/etc. to USD/Bitcoin, use function convert_currency_live() with Google Finance
	// *** examples: convert_currency_live("EUR", "BTC", 22.37) - convert 22.37 Euro to Bitcoin
	// *** convert_currency_live("EUR", "USD", 22.37) - convert 22.37 Euro to USD

	$options = array(
			"public_key"  => $public_key, 	// your public key from gourl.io
			"private_key" => $private_key, 	// your private key from gourl.io
			"webdev_key"  => "", 		// optional, gourl affiliate key
			"orderID"     => $orderID, 		// order id or product name
			"userID"      => $userID, 		// unique identifier for every user
			"userFormat"  => $userFormat, 	// save userID in COOKIE, IPADDRESS or SESSION
			"amount"   	  => 0,				// product price in coins OR in USD below
			"amountUSD"   => $amountUSD,	// we use product price in USD
			"period"      => $period, 		// payment valid period
			"language"	  => $def_language  // text on EN - english, FR - french, etc
	);

	$box = new Cryptobox ($options);

	$coinName = $box->coin_name(); 

	if ($box->is_paid())  {
		if (!$box->is_confirmed()) {
			$message =  "Thank you for order (order #".$orderID.", payment #".$box->payment_id()."). Awaiting transaction/payment confirmation";
		} else {

			if (!$box->is_processed()) {
				// One time action after payment has been made/confirmed
				// !!For update db records, please use function cryptobox_new_payment()!!

				$message = "Thank you for order (order #".$orderID.", payment #".$box->payment_id()."). Payment Confirmed. <br>(User will see this message one time after payment has been made)"; 

				// Set Payment Status to Processed
				$box->set_status_processed();  
			} else $message = "Thank you for order (order #".$orderID.", payment #".$box->payment_id()."). Payment Confirmed. <br>(User will see this message during ".$period." period after payment has been made)"; // General message
		}
	} else $message = "This invoice has not been paid yet";

	$languages_list = display_language_box($def_language);

	// ...
	// Also you need to use IPN function cryptobox_new_payment($paymentID = 0, $payment_details = array(), $box_status = "") 
	// for send confirmation email, update database, update user membership, etc.
	// You need to modify file - cryptobox.newpayment.php, read more - https://gourl.io/api-php.html#ipn
	// ...
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<title><?php echo $coinName; ?> Pay-Per-Product Cryptocoin Payment Example</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='Expires' content='-1'>
<meta name='robots' content='all'>
<script src='../js/cryptobox.min.js' type='text/javascript'></script>
</head>
<body style='font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#666;margin:0'>
<div align='center'>

<h1>Customer Invoice</h1>
<br>
<img style='position:absolute;margin-left:auto;margin-right:auto;left:0;right:0;' alt='status' src='https://gourl.io/images/<?php echo ($box->is_paid()?"paid":"unpaid"); ?>.png'>
<img alt='Invoice' border='0' height='500' src='https://gourl.io/images/invoice.png'>

<br><br>
<?php if (!$box->is_paid()) echo "<h2>Pay Invoice Now - </h2>"; else echo "<br><br>";  ?>
<div style='margin:30px 0 5px 300px'>Language: &#160; <?php echo $languages_list; ?></div>
<?php echo $box->display_cryptobox(true, 580, 230); ?>
<br><br><br>
<h3>Message :</h3>
<h2 style='color:#999'><?php echo $message; ?></h2>


</div><br><br><br><br><br><br>
<div style='position:absolute;left:0;'><a target="_blank" href="http://validator.w3.org/check?uri=<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>"><img src="https://gourl.io/images/w3c.png" alt="Valid HTML 4.01 Transitional"></a></div>
</body>
</html>